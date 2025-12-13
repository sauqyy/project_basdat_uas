<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\PreferensiDosen;
use App\Models\Jadwal;
use App\Models\FactJadwal;
use App\Models\FactKecocokanJadwal;
use App\Models\DimDosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    // Dashboard Dosen
    public function dashboard()
    {
        $dosen = Auth::user();
        
        // Data dari tabel operasional
        $totalMataKuliah = MataKuliah::where('dosen_id', $dosen->id)->count();
        $totalJadwal = Jadwal::whereHas('mataKuliah', function($query) use ($dosen) {
            $query->where('dosen_id', $dosen->id);
        })->where('status', true)->count();
        $mataKuliahAktif = MataKuliah::where('dosen_id', $dosen->id)->count();

        // Hitung data dari tabel operasional (tidak bergantung pada fact tables)
        $jadwals = Jadwal::with('mataKuliah', 'ruangan')
            ->whereHas('mataKuliah', function($query) use ($dosen) {
                $query->where('dosen_id', $dosen->id);
            })
            ->where('status', true)
            ->get();
        
        // Hitung total SKS
        $totalSKS = $jadwals->sum(function($jadwal) {
            return $jadwal->mataKuliah->sks ?? 0;
        });
        
        // Hitung total mahasiswa
        $totalMahasiswa = $jadwals->sum(function($jadwal) {
            return $jadwal->mataKuliah->kapasitas ?? 0;
        });
        
        // Hitung konflik jadwal
        $totalKonflik = $this->hitungKonflikDosen($jadwals);
        $jadwalKonflik = $this->getJadwalKonflikDosen($jadwals);
        
        // Hitung utilisasi ruangan rata-rata
        $avgUtilisasi = $this->hitungUtilisasiDosen($jadwals);
        
        // Jadwal per hari
        $jadwalPerHari = $this->getJadwalPerHariDosen($jadwals);
        
        // Analisis preferensi dari tabel operasional
        $preferensiData = $this->hitungKecocokanPreferensi($dosen->id, $jadwals);
        $avgKecocokan = $preferensiData['avg_kecocokan'] ?? 0;
        $avgSkor = $preferensiData['avg_skor'] ?? 0;
        $preferensiTerpenuhi = $preferensiData['preferensi_terpenuhi'] ?? 0;
        $totalPreferensi = $preferensiData['total_preferensi'] ?? 0;

        return view('dosen.dashboard', compact(
            'totalMataKuliah', 
            'totalJadwal', 
            'mataKuliahAktif',
            'totalSKS',
            'totalMahasiswa',
            'totalKonflik',
            'avgUtilisasi',
            'avgKecocokan',
            'avgSkor',
            'preferensiTerpenuhi',
            'totalPreferensi',
            'jadwalPerHari',
            'jadwalKonflik'
        ));
    }
    
    /**
     * Hitung konflik jadwal untuk dosen
     */
    private function hitungKonflikDosen($jadwals)
    {
        $konflikCount = 0;
        foreach ($jadwals as $jadwal) {
            // Check ruangan conflict
            $ruanganConflict = Jadwal::where('id', '!=', $jadwal->id)
                ->where('ruangan_id', $jadwal->ruangan_id)
                ->where('hari', $jadwal->hari)
                ->where('status', true)
                ->where(function($query) use ($jadwal) {
                    $jamMulai = is_object($jadwal->jam_mulai) ? $jadwal->jam_mulai->format('H:i:s') : $jadwal->jam_mulai;
                    $jamSelesai = is_object($jadwal->jam_selesai) ? $jadwal->jam_selesai->format('H:i:s') : $jadwal->jam_selesai;
                    $query->where('jam_mulai', '<', $jamSelesai)
                          ->where('jam_selesai', '>', $jamMulai);
                })
                ->exists();
            
            // Check dosen conflict
            $dosenConflict = false;
            if ($jadwal->mataKuliah && $jadwal->mataKuliah->dosen_id) {
                $jamMulai = is_object($jadwal->jam_mulai) ? $jadwal->jam_mulai->format('H:i:s') : $jadwal->jam_mulai;
                $jamSelesai = is_object($jadwal->jam_selesai) ? $jadwal->jam_selesai->format('H:i:s') : $jadwal->jam_selesai;
                
                $dosenConflict = Jadwal::where('id', '!=', $jadwal->id)
                    ->whereHas('mataKuliah', function($q) use ($jadwal) {
                        $q->where('dosen_id', $jadwal->mataKuliah->dosen_id);
                    })
                    ->where('hari', $jadwal->hari)
                    ->where('status', true)
                    ->where(function($query) use ($jamMulai, $jamSelesai) {
                        $query->where('jam_mulai', '<', $jamSelesai)
                              ->where('jam_selesai', '>', $jamMulai);
                    })
                    ->exists();
            }
            
            if ($ruanganConflict || $dosenConflict) {
                $konflikCount++;
            }
        }
        
        return $konflikCount;
    }
    
    /**
     * Get jadwal dengan konflik untuk dosen
     */
    private function getJadwalKonflikDosen($jadwals)
    {
        $konflikList = [];
        foreach ($jadwals as $jadwal) {
            // Check ruangan conflict
            $ruanganConflict = Jadwal::where('id', '!=', $jadwal->id)
                ->where('ruangan_id', $jadwal->ruangan_id)
                ->where('hari', $jadwal->hari)
                ->where('status', true)
                ->where(function($query) use ($jadwal) {
                    $jamMulai = is_object($jadwal->jam_mulai) ? $jadwal->jam_mulai->format('H:i:s') : $jadwal->jam_mulai;
                    $jamSelesai = is_object($jadwal->jam_selesai) ? $jadwal->jam_selesai->format('H:i:s') : $jadwal->jam_selesai;
                    $query->where('jam_mulai', '<', $jamSelesai)
                          ->where('jam_selesai', '>', $jamMulai);
                })
                ->exists();
            
            // Check dosen conflict
            $dosenConflict = false;
            if ($jadwal->mataKuliah && $jadwal->mataKuliah->dosen_id) {
                $jamMulai = is_object($jadwal->jam_mulai) ? $jadwal->jam_mulai->format('H:i:s') : $jadwal->jam_mulai;
                $jamSelesai = is_object($jadwal->jam_selesai) ? $jadwal->jam_selesai->format('H:i:s') : $jadwal->jam_selesai;
                
                $dosenConflict = Jadwal::where('id', '!=', $jadwal->id)
                    ->whereHas('mataKuliah', function($q) use ($jadwal) {
                        $q->where('dosen_id', $jadwal->mataKuliah->dosen_id);
                    })
                    ->where('hari', $jadwal->hari)
                    ->where('status', true)
                    ->where(function($query) use ($jamMulai, $jamSelesai) {
                        $query->where('jam_mulai', '<', $jamSelesai)
                              ->where('jam_selesai', '>', $jamMulai);
                    })
                    ->exists();
            }
            
            if ($ruanganConflict || $dosenConflict) {
                $konflikList[] = $jadwal;
            }
        }
        
        return collect($konflikList)->take(5);
    }
    
    /**
     * Hitung utilisasi ruangan untuk dosen
     */
    private function hitungUtilisasiDosen($jadwals)
    {
        $totalUtilisasi = 0;
        $count = 0;
        
        foreach ($jadwals as $jadwal) {
            $kapasitas = $jadwal->ruangan->kapasitas ?? 0;
            $mahasiswa = $jadwal->mataKuliah->kapasitas ?? 0;
            
            if ($kapasitas > 0) {
                $utilisasi = ($mahasiswa / $kapasitas) * 100;
                $totalUtilisasi += $utilisasi;
                $count++;
            }
        }
        
        return $count > 0 ? ($totalUtilisasi / $count) : 0;
    }
    
    /**
     * Get jadwal per hari untuk dosen
     */
    private function getJadwalPerHariDosen($jadwals)
    {
        $jadwalPerHari = [];
        
        foreach ($jadwals as $jadwal) {
            $hari = $jadwal->hari;
            if (!isset($jadwalPerHari[$hari])) {
                $jadwalPerHari[$hari] = [
                    'hari' => $hari,
                    'total_jadwal' => 0,
                    'total_sks' => 0
                ];
            }
            
            $jadwalPerHari[$hari]['total_jadwal']++;
            $jadwalPerHari[$hari]['total_sks'] += $jadwal->mataKuliah->sks ?? 0;
        }
        
        return collect($jadwalPerHari)->values();
    }
    
    /**
     * Hitung kecocokan preferensi dari tabel operasional
     */
    private function hitungKecocokanPreferensi($dosenId, $jadwals)
    {
        // Get preferensi global dosen
        $preferensiGlobal = PreferensiDosen::where('dosen_id', $dosenId)
            ->whereNull('mata_kuliah_id')
            ->first();
        
        if (!$preferensiGlobal || $jadwals->count() == 0) {
            return [
                'avg_kecocokan' => 0,
                'avg_skor' => 0,
                'preferensi_terpenuhi' => 0,
                'total_preferensi' => 0
            ];
        }
        
        $preferensiHari = is_array($preferensiGlobal->preferensi_hari) 
            ? $preferensiGlobal->preferensi_hari 
            : json_decode($preferensiGlobal->preferensi_hari, true) ?? [];
        
        $preferensiJam = is_array($preferensiGlobal->preferensi_jam) 
            ? $preferensiGlobal->preferensi_jam 
            : json_decode($preferensiGlobal->preferensi_jam, true) ?? [];
        
        // Flatten preferensi jam jika dalam format object
        if (is_array($preferensiJam) && !isset($preferensiJam[0])) {
            $allJam = [];
            foreach ($preferensiJam as $hari => $jamHari) {
                if (is_array($jamHari)) {
                    foreach ($jamHari as $jam) {
                        if (!in_array($jam, $allJam)) {
                            $allJam[] = $jam;
                        }
                    }
                }
            }
            $preferensiJam = $allJam;
        }
        
        $totalKecocokan = 0;
        $totalSkor = 0;
        $preferensiTerpenuhi = 0;
        $totalPreferensi = count($preferensiHari) + count($preferensiJam);
        
        foreach ($jadwals as $jadwal) {
            $hariTerpenuhi = in_array($jadwal->hari, $preferensiHari);
            
            $jamTerpenuhi = false;
            $jamMulaiStr = is_object($jadwal->jam_mulai) 
                ? $jadwal->jam_mulai->format('H:i') 
                : $jadwal->jam_mulai;
            $jamSelesaiStr = is_object($jadwal->jam_selesai) 
                ? $jadwal->jam_selesai->format('H:i') 
                : $jadwal->jam_selesai;
            
            foreach ($preferensiJam as $jam) {
                if (strpos($jam, $jamMulaiStr) !== false || strpos($jam, $jamSelesaiStr) !== false) {
                    $jamTerpenuhi = true;
                    break;
                }
            }
            
            $jumlahTerpenuhi = ($hariTerpenuhi ? 1 : 0) + ($jamTerpenuhi ? 1 : 0);
            $persentase = $totalPreferensi > 0 ? ($jumlahTerpenuhi / $totalPreferensi) * 100 : 0;
            $skor = (int) $persentase;
            
            $totalKecocokan += $persentase;
            $totalSkor += $skor;
            
            if ($hariTerpenuhi && $jamTerpenuhi) {
                $preferensiTerpenuhi++;
            }
        }
        
        $count = $jadwals->count();
        
        return [
            'avg_kecocokan' => $count > 0 ? ($totalKecocokan / $count) : 0,
            'avg_skor' => $count > 0 ? ($totalSkor / $count) : 0,
            'preferensi_terpenuhi' => $preferensiTerpenuhi,
            'total_preferensi' => $totalPreferensi
        ];
    }

    // Lihat Mata Kuliah yang diampu (read-only)
    public function mataKuliah()
    {
        $dosen = Auth::user();
        $mataKuliahs = MataKuliah::where('dosen_id', $dosen->id)->get();
        return view('dosen.mata-kuliah', compact('mataKuliahs'));
    }

    // Manajemen Preferensi Jadwal Global (untuk seluruh mata kuliah)
    public function preferensi()
    {
        $dosen = Auth::user();
        $preferensi = PreferensiDosen::where('dosen_id', $dosen->id)->whereNull('mata_kuliah_id')->first();
        
        return view('dosen.preferensi', compact('preferensi'));
    }

    public function storePreferensi(Request $request)
    {
        $request->validate([
            'preferensi_hari' => 'required|string',
            'preferensi_jam' => 'required|string',
            'prioritas' => 'required|integer|min:1|max:3',
            'catatan' => 'nullable'
        ]);

        // Parse JSON data
        $preferensiHari = json_decode($request->preferensi_hari, true);
        $preferensiJam = json_decode($request->preferensi_jam, true);

        // Validasi minimal
        if (empty($preferensiHari) || count($preferensiHari) === 0) {
            return back()->withErrors(['preferensi_hari' => 'Pilih minimal 1 hari untuk mengajar!']);
        }

        // Validasi jam per hari
        if (is_array($preferensiJam) && isset($preferensiJam[0]) && is_string($preferensiJam[0])) {
            // Format lama: array jam global, konversi ke format baru
            $jamPerHari = [];
            foreach ($preferensiHari as $hari) {
                $jamPerHari[$hari] = $preferensiJam;
            }
            $preferensiJam = $jamPerHari;
        }
        // Jika sudah dalam format object (jam per hari), biarkan saja

        // Hapus preferensi lama untuk dosen ini
        PreferensiDosen::where('dosen_id', Auth::id())
            ->whereNull('mata_kuliah_id')
            ->delete();

        $data = [
            'dosen_id' => Auth::id(),
            'mata_kuliah_id' => null, // Global preferensi
            'preferensi_hari' => $preferensiHari,
            'preferensi_jam' => $preferensiJam,
            'prioritas' => $request->prioritas,
            'catatan' => $request->catatan
        ];

        PreferensiDosen::create($data);
        
        \Log::info('Created new preferensi for dosen ' . Auth::id() . ': ' . json_encode($preferensiHari) . ' | Jam: ' . json_encode($preferensiJam));

        return redirect()->route('dosen.preferensi')->with('success', 'Preferensi jadwal global berhasil disimpan. Hari yang tidak dipilih berarti dosen tidak bisa mengajar pada hari tersebut.');
    }

    public function updatePreferensi(Request $request, $id)
    {
        $request->validate([
            'preferensi_hari' => 'required|string',
            'preferensi_jam' => 'required|string',
            'prioritas' => 'required|integer|min:1|max:3',
            'catatan' => 'nullable'
        ]);

        // Parse JSON data
        $preferensiHari = json_decode($request->preferensi_hari, true);
        $preferensiJam = json_decode($request->preferensi_jam, true);

        // Validasi jam per hari
        if (is_array($preferensiJam) && isset($preferensiJam[0]) && is_string($preferensiJam[0])) {
            // Format lama: array jam global, konversi ke format baru
            $jamPerHari = [];
            foreach ($preferensiHari as $hari) {
                $jamPerHari[$hari] = $preferensiJam;
            }
            $preferensiJam = $jamPerHari;
        }
        // Jika sudah dalam format object (jam per hari), biarkan saja

        $preferensi = PreferensiDosen::where('id', $id)->where('dosen_id', Auth::id())->firstOrFail();
        
        $data = [
            'preferensi_hari' => $preferensiHari,
            'preferensi_jam' => $preferensiJam,
            'prioritas' => $request->prioritas,
            'catatan' => $request->catatan
        ];

        $preferensi->update($data);
        
        \Log::info('Updated preferensi for dosen ' . Auth::id() . ': ' . json_encode($preferensiHari) . ' | Jam: ' . json_encode($preferensiJam));

        return redirect()->route('dosen.preferensi')->with('success', 'Preferensi jadwal global berhasil diperbarui');
    }

    public function getPreferensi($id)
    {
        $preferensi = PreferensiDosen::where('id', $id)->where('dosen_id', Auth::id())->firstOrFail();
        return response()->json($preferensi);
    }

    // Lihat Jadwal
    public function jadwal()
    {
        $dosen = Auth::user();
        
        // Debug: Cek mata kuliah dosen
        $mataKuliahDosen = \App\Models\MataKuliah::where('dosen_id', $dosen->id)->get();
        \Log::info('Dosen ' . $dosen->id . ' (' . $dosen->name . ') has ' . $mataKuliahDosen->count() . ' mata kuliah');
        
        $jadwals = Jadwal::whereHas('mataKuliah', function($query) use ($dosen) {
            $query->where('dosen_id', $dosen->id);
        })->with('mataKuliah', 'ruangan')->get();
        
        \Log::info('Dosen ' . $dosen->id . ' has ' . $jadwals->count() . ' jadwals');
        
        return view('dosen.jadwal', compact('jadwals'));
    }
}
