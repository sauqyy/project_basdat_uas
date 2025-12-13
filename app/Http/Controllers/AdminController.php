<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\MataKuliah;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Dashboard Admin
    public function dashboard()
    {
        $totalRuangan = Ruangan::count();
        $totalMataKuliah = MataKuliah::count();
        $totalJadwal = Jadwal::count();
        $ruanganTersedia = Ruangan::where('status', true)->count();

        return view('admin.dashboard', compact('totalRuangan', 'totalMataKuliah', 'totalJadwal', 'ruanganTersedia'));
    }

    // Manajemen Ruangan
    public function ruangan()
    {
        $ruangans = Ruangan::all();
        return view('admin.ruangan', compact('ruangans'));
    }

    public function storeRuangan(Request $request)
    {
        $request->validate([
            'kode_ruangan' => 'required|unique:ruangans',
            'nama_ruangan' => 'required',
            'kapasitas' => 'required|integer|min:1',
            'tipe_ruangan' => 'required|in:kelas,lab,auditorium',
            'fasilitas' => 'nullable'
        ]);

        Ruangan::create($request->all());

        return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil ditambahkan');
    }

    public function updateRuangan(Request $request, $id)
    {
        $request->validate([
            'kode_ruangan' => 'required|unique:ruangans,kode_ruangan,' . $id,
            'nama_ruangan' => 'required',
            'kapasitas' => 'required|integer|min:1',
            'tipe_ruangan' => 'required|in:kelas,lab,auditorium',
            'fasilitas' => 'nullable'
        ]);

        $ruangan = Ruangan::findOrFail($id);
        $ruangan->update($request->all());

        return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil diperbarui');
    }

    public function destroyRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return redirect()->route('admin.ruangan')->with('success', 'Ruangan berhasil dihapus');
    }

    public function getRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return response()->json($ruangan);
    }

    // Generate Jadwal AI
    public function generateJadwal()
    {
        try {
            // Algoritma AI untuk plotting jadwal
            $mataKuliahs = MataKuliah::with('dosen', 'preferensiDosen')->get();
            $ruangans = Ruangan::where('status', true)->get();
            
            // Hapus jadwal lama (hanya jadwal prodi Teknik Informatika)
            Jadwal::where('prodi', 'Teknik Informatika')->delete();
            
            $jadwalGenerated = $this->aiPlottingAlgorithm($mataKuliahs, $ruangans);
            
            return redirect()->route('admin.jadwal')->with('success', 'Jadwal berhasil di-generate menggunakan AI. Total jadwal: ' . count($jadwalGenerated));
        } catch (\Exception $e) {
            \Log::error('Error generating jadwal: ' . $e->getMessage());
            return redirect()->route('admin.jadwal')->with('error', 'Gagal generate jadwal: ' . $e->getMessage());
        }
    }

    private function aiPlottingAlgorithm($mataKuliahs, $ruangans)
    {
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        // Generate jam slots berdasarkan SKS (1 SKS = 50 menit)
        $jamSlots = $this->generateJamSlots();
        
        $jadwalGenerated = [];
        $prodi = 'Teknik Informatika'; // Default prodi
        
        // Urutkan mata kuliah berdasarkan prioritas preferensi
        $mataKuliahs = $mataKuliahs->sortBy(function($mk) {
            $preferensi = $mk->preferensiDosen->first();
            return $preferensi ? $preferensi->prioritas : 3;
        });
        
        foreach ($mataKuliahs as $mk) {
            $preferensi = $mk->preferensiDosen->first();
            
            if ($preferensi) {
                $preferensiHari = $preferensi->preferensi_hari ?? $hari;
                $preferensiJam = $preferensi->preferensi_jam ?? $jamSlots;
                
                // Filter jam slots berdasarkan SKS mata kuliah
                $jamSesuaiSKS = $this->filterJamBySKS($preferensiJam, $mk->sks);
                
                // Cari ruangan yang sesuai
                $ruanganSesuai = $ruangans->where('kapasitas', '>=', $mk->kapasitas)->first();
                
                if ($ruanganSesuai && !empty($jamSesuaiSKS)) {
                    $jadwalBerhasil = false;
                    $maxAttempts = 10; // Maksimal percobaan untuk menghindari infinite loop
                    $attempt = 0;
                    
                    while (!$jadwalBerhasil && $attempt < $maxAttempts) {
                        // Pilih hari dan jam dari preferensi
                        $hariTerpilih = $preferensiHari[array_rand($preferensiHari)];
                        $jamTerpilih = $jamSesuaiSKS[array_rand($jamSesuaiSKS)];
                        
                        [$jamMulai, $jamSelesai] = explode('-', $jamTerpilih);
                        
                        // Cek konflik dengan prodi lain dan prodi yang sama
                        $konflik = $this->checkKonflikJadwal($hariTerpilih, $ruanganSesuai->id, $jamMulai, $jamSelesai, $prodi, $mk->dosen_id);
                        
                        if (!$konflik) {
                            Jadwal::create([
                                'mata_kuliah_id' => $mk->id,
                                'ruangan_id' => $ruanganSesuai->id,
                                'hari' => $hariTerpilih,
                                'jam_mulai' => $jamMulai,
                                'jam_selesai' => $jamSelesai,
                                'semester' => 'Ganjil',
                                'tahun_akademik' => date('Y'),
                                'prodi' => $prodi
                            ]);
                            
                            $jadwalGenerated[] = [
                                'mata_kuliah' => $mk->nama_mk,
                                'dosen' => $mk->dosen->name,
                                'ruangan' => $ruanganSesuai->nama_ruangan,
                                'hari' => $hariTerpilih,
                                'jam' => $jamTerpilih,
                                'sks' => $mk->sks,
                                'prodi' => $prodi
                            ];
                            
                            $jadwalBerhasil = true;
                        }
                        
                        $attempt++;
                    }
                    
                    if (!$jadwalBerhasil) {
                        // Log mata kuliah yang gagal dijadwalkan
                        \Log::warning("Gagal menjadwalkan mata kuliah: {$mk->nama_mk} - Konflik dengan prodi lain atau tidak ada slot tersedia");
                    }
                }
            }
        }
        
        return $jadwalGenerated;
    }
    
    /**
     * Generate jam slots berdasarkan SKS (1 SKS = 50 menit)
     */
    private function generateJamSlots()
    {
        $jamSlots = [];
        $startHour = 8; // Mulai jam 08:00
        $endHour = 17; // Sampai jam 17:00
        
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            for ($sks = 1; $sks <= 4; $sks++) { // Maksimal 4 SKS per slot
                $duration = $sks * 50; // 50 menit per SKS
                $startTime = sprintf('%02d:00', $hour);
                $endTime = date('H:i', strtotime($startTime . ' +' . $duration . ' minutes'));
                
                // Pastikan tidak melebihi jam 17:00
                if (strtotime($endTime) <= strtotime('17:00')) {
                    $jamSlots[] = $startTime . '-' . $endTime;
                }
            }
        }
        
        return array_unique($jamSlots);
    }
    
    /**
     * Filter jam slots berdasarkan SKS mata kuliah
     */
    private function filterJamBySKS($preferensiJam, $sks)
    {
        $filteredSlots = [];
        
        foreach ($preferensiJam as $jam) {
            [$start, $end] = explode('-', $jam);
            $duration = (strtotime($end) - strtotime($start)) / 60; // Durasi dalam menit
            $sksSlot = $duration / 50; // Konversi ke SKS
            
            // Cek apakah durasi slot sesuai dengan SKS mata kuliah
            if ($sksSlot >= $sks) {
                $filteredSlots[] = $jam;
            }
        }
        
        return $filteredSlots;
    }
    
    /**
     * Cek konflik jadwal dengan prodi lain dan prodi yang sama
     */
    private function checkKonflikJadwal($hari, $ruanganId, $jamMulai, $jamSelesai, $prodi, $dosenId = null)
    {
        // Check for conflicts in the same room
        $konflikRuangan = Jadwal::where('hari', $hari)
            ->where('ruangan_id', $ruanganId)
            ->where(function($query) use ($jamMulai, $jamSelesai) {
                $query->where(function($q) use ($jamMulai, $jamSelesai) {
                    // Cek overlap waktu
                    $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                      ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                      ->orWhere(function($q2) use ($jamMulai, $jamSelesai) {
                          // Cek jika jadwal baru berada di dalam jadwal yang sudah ada
                          $q2->where('jam_mulai', '<=', $jamMulai)
                             ->where('jam_selesai', '>=', $jamSelesai);
                      });
                });
            })->exists();
        
        // Check for conflicts with the same dosen (if dosenId provided)
        $konflikDosen = false;
        if ($dosenId) {
            $konflikDosen = Jadwal::where('hari', $hari)
                ->whereHas('mataKuliah', function($query) use ($dosenId) {
                    $query->where('dosen_id', $dosenId);
                })
                ->where(function($query) use ($jamMulai, $jamSelesai) {
                    $query->where(function($q) use ($jamMulai, $jamSelesai) {
                        $q->whereBetween('jam_mulai', [$jamMulai, $jamSelesai])
                          ->orWhereBetween('jam_selesai', [$jamMulai, $jamSelesai])
                          ->orWhere(function($q2) use ($jamMulai, $jamSelesai) {
                              $q2->where('jam_mulai', '<=', $jamMulai)
                                 ->where('jam_selesai', '>=', $jamSelesai);
                          });
                    });
                })->exists();
        }
        
        // Corrected logic: 
        // - Dosen boleh mengajar di jam yang sama dengan dosen lain asalkan kelasnya berbeda
        // - Dosen tidak boleh mengajar lebih dari 1 matkul di jam yang sama
        // - Ruangan tidak boleh digunakan oleh 2 kelas pada waktu yang sama
        $konflik = $konflikRuangan || $konflikDosen;
        
        \Log::info('Konflik check for ' . $hari . ' at ' . $jamMulai . '-' . $jamSelesai . ' in room ' . $ruanganId . ' for dosen ' . $dosenId . ': ' . ($konflik ? 'CONFLICT' : 'NO CONFLICT') . ' (Room: ' . ($konflikRuangan ? 'YES' : 'NO') . ', Dosen: ' . ($konflikDosen ? 'YES' : 'NO') . ') - Logic: Conflict if room OR dosen conflict');
        
        return $konflik;
    }

    // Lihat Jadwal
    public function jadwal()
    {
        $jadwals = Jadwal::with('mataKuliah.dosen', 'ruangan')->get();
        return view('admin.jadwal', compact('jadwals'));
    }
}
