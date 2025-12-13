<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\MataKuliah;
use App\Models\Jadwal;
use App\Models\User;
use App\Models\FactJadwal;
use App\Models\FactUtilisasiRuangan;
use App\Models\FactKecocokanJadwal;
use App\Models\DimProdi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SuperAdminController extends Controller
{
    // Dashboard Super Admin
    public function dashboard()
    {
        // Data dasar dari tabel operasional
        $totalRuangan = Ruangan::count();
        $totalMataKuliah = MataKuliah::count();
        $totalJadwal = Jadwal::where('status', true)->count();
        $totalDosen = User::where('role', 'dosen')->count();
        $totalAdminProdi = User::where('role', 'admin_prodi')->count();
        $ruanganTersedia = Ruangan::where('status', true)->count();

        // Hitung konflik jadwal dari tabel operasional
        $totalKonflik = $this->hitungTotalKonflik();
        
        // Hitung utilisasi ruangan dari tabel operasional
        $utilisasiData = $this->hitungUtilisasiRuangan();
        $avgUtilisasiRuangan = $utilisasiData['avg_utilisasi'] ?? 0;
        $totalMahasiswa = $utilisasiData['total_mahasiswa'] ?? 0;
        
        // Top 5 ruangan paling sering digunakan
        $topRuanganUtilisasi = $this->getTopRuanganUtilisasi();
        
        // Jadwal dengan konflik (untuk ditampilkan)
        $jadwalKonflik = $this->getJadwalKonflik();
        
        // Statistik per prodi
        $statistikProdi = $this->getStatistikProdi();
        
        // Top 5 dosen dengan beban mengajar tertinggi
        $topDosenBeban = $this->getTopDosenBeban();

        return view('super-admin.dashboard', compact(
            'totalRuangan', 
            'totalMataKuliah', 
            'totalJadwal', 
            'totalDosen',
            'totalAdminProdi',
            'ruanganTersedia',
            'totalKonflik',
            'avgUtilisasiRuangan',
            'totalMahasiswa',
            'topRuanganUtilisasi',
            'jadwalKonflik',
            'statistikProdi',
            'topDosenBeban'
        ));
    }
    
    /**
     * Hitung total konflik jadwal dari tabel operasional
     */
    private function hitungTotalKonflik()
    {
        $jadwals = Jadwal::with('mataKuliah', 'ruangan')
            ->where('status', true)
            ->get();
        
        $konflikIds = [];
        foreach ($jadwals as $jadwal) {
            // Check ruangan conflict
            $ruanganConflict = Jadwal::where('id', '!=', $jadwal->id)
                ->where('ruangan_id', $jadwal->ruangan_id)
                ->where('hari', $jadwal->hari)
                ->where('status', true)
                ->where(function($query) use ($jadwal) {
                    // Convert to time string for comparison
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
                $konflikIds[] = $jadwal->id;
            }
        }
        
        return count(array_unique($konflikIds));
    }
    
    /**
     * Hitung utilisasi ruangan dari tabel operasional
     */
    private function hitungUtilisasiRuangan()
    {
        $jadwals = Jadwal::with('mataKuliah', 'ruangan')
            ->where('status', true)
            ->get();
        
        $totalUtilisasi = 0;
        $totalMahasiswa = 0;
        $count = 0;
        
        foreach ($jadwals as $jadwal) {
            $kapasitas = $jadwal->ruangan->kapasitas ?? 0;
            $mahasiswa = $jadwal->mataKuliah->kapasitas ?? 0;
            
            if ($kapasitas > 0) {
                $utilisasi = ($mahasiswa / $kapasitas) * 100;
                $totalUtilisasi += $utilisasi;
                $totalMahasiswa += $mahasiswa;
                $count++;
            }
        }
        
        return [
            'avg_utilisasi' => $count > 0 ? ($totalUtilisasi / $count) : 0,
            'total_mahasiswa' => $totalMahasiswa
        ];
    }
    
    /**
     * Get top 5 ruangan paling sering digunakan
     */
    private function getTopRuanganUtilisasi()
    {
        $ruanganUsage = Jadwal::select(
            'ruangan_id',
            DB::raw('COUNT(*) as jumlah_penggunaan'),
            DB::raw('SUM(CASE WHEN mata_kuliahs.kapasitas IS NOT NULL THEN mata_kuliahs.kapasitas ELSE 0 END) as total_mahasiswa')
        )
        ->join('mata_kuliahs', 'jadwals.mata_kuliah_id', '=', 'mata_kuliahs.id')
        ->where('jadwals.status', true)
        ->groupBy('ruangan_id')
        ->orderBy('jumlah_penggunaan', 'desc')
        ->limit(5)
        ->get();
        
        $result = [];
        foreach ($ruanganUsage as $usage) {
            $ruangan = Ruangan::find($usage->ruangan_id);
            if ($ruangan) {
                $kapasitas = $ruangan->kapasitas ?? 1;
                $utilisasi = $kapasitas > 0 ? ($usage->total_mahasiswa / ($kapasitas * $usage->jumlah_penggunaan)) * 100 : 0;
                
                $result[] = (object)[
                    'ruangan' => $ruangan,
                    'jumlah_penggunaan' => $usage->jumlah_penggunaan,
                    'utilisasi' => round($utilisasi, 2),
                    'total_mahasiswa' => $usage->total_mahasiswa
                ];
            }
        }
        
        return collect($result);
    }
    
    /**
     * Get jadwal dengan konflik
     */
    private function getJadwalKonflik()
    {
        $jadwals = Jadwal::with('mataKuliah.dosen', 'ruangan')
            ->where('status', true)
            ->get();
        
        $konflikList = [];
        foreach ($jadwals as $jadwal) {
            // Convert to time string for comparison
            $jamMulai = is_object($jadwal->jam_mulai) ? $jadwal->jam_mulai->format('H:i:s') : $jadwal->jam_mulai;
            $jamSelesai = is_object($jadwal->jam_selesai) ? $jadwal->jam_selesai->format('H:i:s') : $jadwal->jam_selesai;
            
            // Check ruangan conflict
            $ruanganConflict = Jadwal::where('id', '!=', $jadwal->id)
                ->where('ruangan_id', $jadwal->ruangan_id)
                ->where('hari', $jadwal->hari)
                ->where('status', true)
                ->where(function($query) use ($jamMulai, $jamSelesai) {
                    $query->where('jam_mulai', '<', $jamSelesai)
                          ->where('jam_selesai', '>', $jamMulai);
                })
                ->exists();
            
            // Check dosen conflict
            $dosenConflict = false;
            if ($jadwal->mataKuliah && $jadwal->mataKuliah->dosen_id) {
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
        
        return collect($konflikList)->take(10);
    }
    
    /**
     * Get statistik per prodi
     */
    private function getStatistikProdi()
    {
        $prodiList = [
            'Teknologi Sains Data',
            'Rekayasa Nanoteknologi',
            'Teknik Industri',
            'Teknik Elektro',
            'Teknik Robotika dan Kecerdasan Buatan'
        ];
        
        $statistik = [];
        foreach ($prodiList as $prodi) {
            $jadwals = Jadwal::with('mataKuliah', 'ruangan')
                ->where('prodi', $prodi)
                ->where('status', true)
                ->get();
            
            $totalJadwal = $jadwals->count();
            $totalKonflik = 0;
            $totalUtilisasi = 0;
            $totalMahasiswa = 0;
            $count = 0;
            
            foreach ($jadwals as $jadwal) {
                // Convert to time string for comparison
                $jamMulai = is_object($jadwal->jam_mulai) ? $jadwal->jam_mulai->format('H:i:s') : $jadwal->jam_mulai;
                $jamSelesai = is_object($jadwal->jam_selesai) ? $jadwal->jam_selesai->format('H:i:s') : $jadwal->jam_selesai;
                
                // Check ruangan conflict
                $ruanganConflict = Jadwal::where('id', '!=', $jadwal->id)
                    ->where('ruangan_id', $jadwal->ruangan_id)
                    ->where('hari', $jadwal->hari)
                    ->where('status', true)
                    ->where(function($query) use ($jamMulai, $jamSelesai) {
                        $query->where('jam_mulai', '<', $jamSelesai)
                              ->where('jam_selesai', '>', $jamMulai);
                    })
                    ->exists();
                
                // Check dosen conflict
                $dosenConflict = false;
                if ($jadwal->mataKuliah && $jadwal->mataKuliah->dosen_id) {
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
                    $totalKonflik++;
                }
                
                $kapasitas = $jadwal->ruangan->kapasitas ?? 0;
                $mahasiswa = $jadwal->mataKuliah->kapasitas ?? 0;
                
                if ($kapasitas > 0) {
                    $utilisasi = ($mahasiswa / $kapasitas) * 100;
                    $totalUtilisasi += $utilisasi;
                    $totalMahasiswa += $mahasiswa;
                    $count++;
                }
            }
            
            $statistik[] = (object)[
                'prodi' => $prodi,
                'total_jadwal' => $totalJadwal,
                'total_konflik' => $totalKonflik,
                'avg_utilisasi' => $count > 0 ? ($totalUtilisasi / $count) : 0,
                'total_mahasiswa' => $totalMahasiswa
            ];
        }
        
        return collect($statistik);
    }
    
    /**
     * Get top 5 dosen dengan beban mengajar tertinggi
     */
    private function getTopDosenBeban()
    {
        // Hanya ambil dosen yang masih ada di tabel users (belum dihapus)
        $dosenBeban = Jadwal::select(
            'mata_kuliahs.dosen_id',
            DB::raw('COUNT(*) as total_jadwal'),
            DB::raw('SUM(mata_kuliahs.sks) as total_sks'),
            DB::raw('SUM(mata_kuliahs.kapasitas) as total_mahasiswa')
        )
        ->join('mata_kuliahs', 'jadwals.mata_kuliah_id', '=', 'mata_kuliahs.id')
        ->join('users', 'mata_kuliahs.dosen_id', '=', 'users.id')
        ->where('jadwals.status', true)
        ->where('users.role', 'dosen')
        ->groupBy('mata_kuliahs.dosen_id')
        ->orderBy('total_jadwal', 'desc')
        ->limit(5)
        ->get();
        
        $result = [];
        foreach ($dosenBeban as $beban) {
            $dosen = User::find($beban->dosen_id);
            if ($dosen) {
                $result[] = (object)[
                    'dosen' => $dosen,
                    'total_jadwal' => $beban->total_jadwal,
                    'total_sks' => $beban->total_sks ?? 0,
                    'total_mahasiswa' => $beban->total_mahasiswa ?? 0
                ];
            }
        }
        
        return collect($result);
    }

    // Manajemen Kelas (Ruangan) dengan Prodi Tags
    public function kelas()
    {
        $ruangans = Ruangan::all();
        $prodiList = [
            'Teknologi Sains Data',
            'Rekayasa Nanoteknologi',
            'Teknik Industri',
            'Teknik Elektro',
            'Teknik Robotika dan Kecerdasan Buatan'
        ];
        return view('super-admin.kelas', compact('ruangans', 'prodiList'));
    }

    public function storeKelas(Request $request)
    {
        $request->validate([
            'kode_ruangan' => 'required|unique:ruangans',
            'nama_ruangan' => 'required',
            'kapasitas' => 'required|integer|min:1',
            'tipe_ruangan' => 'required|in:kelas,lab,auditorium',
            'prodi' => 'required|in:Teknologi Sains Data,Rekayasa Nanoteknologi,Teknik Industri,Teknik Elektro,Teknik Robotika dan Kecerdasan Buatan',
            'status' => 'required|boolean',
            'fasilitas' => 'nullable'
        ]);

        Ruangan::create($request->all());

        return redirect()->route('super-admin.kelas')->with('success', 'Kelas berhasil ditambahkan');
    }

    public function updateKelas(Request $request, $id)
    {
        $request->validate([
            'nama_ruangan' => 'required',
            'kapasitas' => 'required|integer|min:1',
            'tipe_ruangan' => 'required|in:kelas,lab,auditorium',
            'prodi' => 'required|in:Teknologi Sains Data,Rekayasa Nanoteknologi,Teknik Industri,Teknik Elektro,Teknik Robotika dan Kecerdasan Buatan',
            'status' => 'required|boolean',
            'fasilitas' => 'nullable'
        ]);

        $ruangan = Ruangan::findOrFail($id);
        $data = $request->except('kode_ruangan'); // Exclude kode_ruangan from update
        $ruangan->update($data);

        return redirect()->route('super-admin.kelas')->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroyKelas($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return redirect()->route('super-admin.kelas')->with('success', 'Kelas berhasil dihapus');
    }

    public function getKelas($id)
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
            
            \Log::info('Generate jadwal started', [
                'mata_kuliah_count' => $mataKuliahs->count(),
                'ruangan_count' => $ruangans->count()
            ]);
            
            // Hapus jadwal lama
            Jadwal::truncate();
            \Log::info('Old jadwal truncated');
            
            // Cek kapasitas ruangan sebelum generate jadwal
            $warnings = $this->checkRuanganCapacity($mataKuliahs, $ruangans);
            
            // Buat jadwal untuk dosen ID 2 secara manual untuk memastikan semua mata kuliah memiliki jadwal
            $this->createJadwalForDosen2($mataKuliahs, $ruangans);
            
            $failedMataKuliah = [];
            $jadwalGenerated = $this->aiPlottingAlgorithm($mataKuliahs, $ruangans, $failedMataKuliah);
            
            \Log::info('Generate jadwal completed', [
                'jadwal_generated_count' => count($jadwalGenerated),
                'failed_mata_kuliah_count' => count($failedMataKuliah)
            ]);
            
            // Auto-populate fact tables setelah generate jadwal
            try {
                \Log::info('Auto-populating fact tables after generate jadwal...');
                
                // Cek apakah dimension tables sudah ter-populate
                $dimDosenCount = \App\Models\DimDosen::count();
                $dimMataKuliahCount = \App\Models\DimMataKuliah::count();
                $dimRuanganCount = \App\Models\DimRuangan::count();
                $dimWaktuCount = \App\Models\DimWaktu::count();
                $dimProdiCount = \App\Models\DimProdi::count();
                
                // Jika dimension tables kosong, populate dulu menggunakan populate_dw.php
                if ($dimDosenCount == 0 || $dimMataKuliahCount == 0 || $dimRuanganCount == 0 || $dimWaktuCount == 0 || $dimProdiCount == 0) {
                    \Log::info('Dimension tables empty, populating all data warehouse...');
                    // Jalankan populate_dw.php via shell
                    $output = shell_exec('php ' . base_path('populate_dw.php') . ' 2>&1');
                    \Log::info('Populate output: ' . $output);
                } else {
                    // Hanya populate fact tables jika dimension sudah ada
                    \Artisan::call('fact:populate', ['--fresh' => false]);
                }
                
                \Log::info('Fact tables populated successfully');
            } catch (\Exception $e) {
                \Log::warning('Failed to auto-populate fact tables: ' . $e->getMessage());
                // Tidak throw error, hanya log warning
            }
            
            // Siapkan pesan sukses dengan warning jika ada
            $successMessage = 'Jadwal berhasil di-generate menggunakan AI. Total jadwal: ' . count($jadwalGenerated);
            
            if (!empty($warnings) || !empty($failedMataKuliah)) {
                $redirect = redirect()->route('super-admin.jadwal')
                    ->with('success', $successMessage);
                
                if (!empty($warnings)) {
                    $redirect->with('warnings', $warnings);
                }
                
                if (!empty($failedMataKuliah)) {
                    $redirect->with('failed_mata_kuliah', $failedMataKuliah);
                }
                
                return $redirect;
            }
            
            return redirect()->route('super-admin.jadwal')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Error generating jadwal: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('super-admin.jadwal')->with('error', 'Gagal generate jadwal: ' . $e->getMessage());
        }
    }
    

    private function createJadwalForDosen2($mataKuliahs, $ruangans)
    {
        // Ambil mata kuliah dosen ID 2
        $mataKuliahsDosen2 = $mataKuliahs->where('dosen_id', 2);
        
        if ($mataKuliahsDosen2->count() == 0) {
            \Log::info('No mata kuliah found for dosen ID 2');
            return;
        }
        
        \Log::info('Creating jadwal for dosen ID 2: ' . $mataKuliahsDosen2->count() . ' mata kuliah');
        
        // Ambil preferensi dosen ID 2 dari database
        $preferensiGlobal = \App\Models\PreferensiDosen::where('dosen_id', 2)
            ->whereNull('mata_kuliah_id')
            ->first();
        
        if ($preferensiGlobal) {
            $hariOptions = $preferensiGlobal->preferensi_hari;
            $jamOptions = $preferensiGlobal->preferensi_jam;
            
            // Decode JSON if needed
            if (is_string($hariOptions)) {
                $hariOptions = json_decode($hariOptions, true);
            }
            if (is_string($jamOptions)) {
                $jamOptions = json_decode($jamOptions, true);
            }
            
            \Log::info('Using preferensi from database: ' . json_encode($hariOptions) . ' | Jam: ' . json_encode($jamOptions));
        } else {
            // Fallback jika tidak ada preferensi
            $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            $jamOptions = [
                '08:00-09:00', '09:00-10:00', '10:00-11:00', 
                '13:00-14:00', '14:00-15:00', '15:00-16:00'
            ];
            \Log::info('No preferensi found, using default');
        }
        
        foreach ($mataKuliahsDosen2 as $index => $mk) {
            if ($mk->ada_praktikum) {
                // Mata kuliah dengan praktikum - buat 2 jadwal terpisah
                \Log::info('Creating praktikum jadwal for dosen ID 2: ' . $mk->nama_mk);
                $this->createJadwalPraktikumForDosen2($mk, $ruangans, $hariOptions, $jamOptions);
            } else {
                // Mata kuliah biasa - Harus sesuai prodi
                $ruangan = $ruangans->where('kapasitas', '>=', $mk->kapasitas)
                    ->where('prodi', $mk->prodi ?? 'Teknologi Sains Data')
                    ->first();
                
                \Log::info('Ruangan search for dosen 2 ' . $mk->nama_mk . ' (Prodi: ' . ($mk->prodi ?? 'Teknologi Sains Data') . '): ' . ($ruangan ? 'Found: ' . $ruangan->nama_ruangan . ' (Prodi: ' . $ruangan->prodi . ')' : 'Not found'));
                
                if ($ruangan) {
                    // Pilih hari secara bergantian
                    $hariTerpilih = $hariOptions[$index % count($hariOptions)];
                    
                    // Pilih jam berdasarkan SKS
                    $jamTerpilih = $jamOptions[$index % count($jamOptions)];
                    if ($mk->sks >= 4) {
                        // Untuk SKS 4+, gunakan jam 2 jam
                        $jamTerpilih = '08:00-10:00';
                    }
                    
                    // Validasi format jam sebelum explode
                    if (empty($jamTerpilih) || strpos($jamTerpilih, '-') === false) {
                        \Log::warning('❌ Format jam tidak valid untuk dosen ID 2: ' . $jamTerpilih);
                        continue;
                    }
                    
                    $jamParts = explode('-', $jamTerpilih);
                    if (count($jamParts) < 2) {
                        \Log::warning('❌ Format jam tidak lengkap untuk dosen ID 2: ' . $jamTerpilih);
                        continue;
                    }
                    
                    // Buat jadwal
                    Jadwal::create([
                        'mata_kuliah_id' => $mk->id,
                        'ruangan_id' => $ruangan->id,
                        'hari' => $hariTerpilih,
                        'jam_mulai' => $jamParts[0],
                        'jam_selesai' => $jamParts[1],
                        'semester' => 'Ganjil',
                        'tahun_akademik' => date('Y'),
                        'prodi' => $mk->prodi ?? 'Teknologi Sains Data'
                    ]);
                    
                    \Log::info('Created jadwal for dosen ID 2: ' . $mk->nama_mk . ' - ' . $hariTerpilih . ' ' . $jamTerpilih);
                }
            }
        }
    }

    private function createJadwalPraktikumForDosen2($mk, $ruangans, $hariOptions, $jamOptions)
    {
        // Hari untuk materi (awal minggu: Senin, Selasa)
        $hariMateri = ['Senin', 'Selasa'];
        // Hari untuk praktikum (setelah materi: Rabu, Kamis, Jumat)
        $hariPraktikum = ['Rabu', 'Kamis', 'Jumat'];
        
        // Filter hari berdasarkan preferensi dosen
        $hariMateriTersedia = array_intersect($hariMateri, $hariOptions);
        $hariPraktikumTersedia = array_intersect($hariPraktikum, $hariOptions);
        
        if (empty($hariMateriTersedia) || empty($hariPraktikumTersedia)) {
            \Log::warning('No suitable days for praktikum: materi=' . json_encode($hariMateriTersedia) . ', praktikum=' . json_encode($hariPraktikumTersedia));
            return;
        }
        
        // 1. Buat jadwal MATERI (harus di awal minggu) - Harus sesuai prodi
        $ruanganMateri = $ruangans->where('kapasitas', '>=', $mk->kapasitas)
            ->where('prodi', $mk->prodi ?? 'Teknologi Sains Data')
            ->where('tipe_ruangan', 'kelas')
            ->first();
        
        \Log::info('Ruangan kelas search for dosen 2 materi ' . $mk->nama_mk . ' (Prodi: ' . ($mk->prodi ?? 'Teknologi Sains Data') . '): ' . ($ruanganMateri ? 'Found: ' . $ruanganMateri->nama_ruangan . ' (Prodi: ' . $ruanganMateri->prodi . ')' : 'Not found'));
        
        if ($ruanganMateri) {
            // Validasi array tidak kosong sebelum array_rand
            if (empty($hariMateriTersedia) || empty($jamOptions)) {
                \Log::warning('❌ Array kosong untuk materi: hariMateriTersedia=' . count($hariMateriTersedia) . ', jamOptions=' . count($jamOptions));
                return;
            }
            
            $hariMateriTerpilih = $hariMateriTersedia[array_rand($hariMateriTersedia)];
            $jamMateri = $jamOptions[array_rand($jamOptions)];
            
            // Sesuaikan jam berdasarkan SKS materi
            if ($mk->sks_materi >= 2) {
                $jamMateri = '08:00-10:00';
            }
            
            Jadwal::create([
                'mata_kuliah_id' => $mk->id,
                'ruangan_id' => $ruanganMateri->id,
                'hari' => $hariMateriTerpilih,
                'jam_mulai' => $this->extractJamMulai($jamMateri, '08:00'),
                'jam_selesai' => $this->extractJamSelesai($jamMateri, '10:00'),
                'semester' => 'Ganjil',
                'tahun_akademik' => date('Y'),
                'prodi' => $mk->prodi ?? 'Teknologi Sains Data'
            ]);
            
            \Log::info('Created MATERI jadwal for dosen ID 2: ' . $mk->nama_mk . ' - ' . $hariMateriTerpilih . ' ' . $jamMateri);
        }
        
        // 2. Buat jadwal PRAKTIKUM (setelah materi, harus di lab) - Harus sesuai prodi
        $ruanganPraktikum = $ruangans->where('kapasitas', '>=', $mk->kapasitas)
            ->where('prodi', $mk->prodi ?? 'Teknologi Sains Data')
            ->where('tipe_ruangan', 'lab')
            ->first();
        
        \Log::info('Ruangan lab search for dosen 2 praktikum ' . $mk->nama_mk . ' (Prodi: ' . ($mk->prodi ?? 'Teknologi Sains Data') . '): ' . ($ruanganPraktikum ? 'Found: ' . $ruanganPraktikum->nama_ruangan . ' (Prodi: ' . $ruanganPraktikum->prodi . ')' : 'Not found'));
        
        if ($ruanganPraktikum) {
            // Validasi array tidak kosong sebelum array_rand
            if (empty($hariPraktikumTersedia) || empty($jamOptions)) {
                \Log::warning('❌ Array kosong untuk praktikum: hariPraktikumTersedia=' . count($hariPraktikumTersedia) . ', jamOptions=' . count($jamOptions));
                return;
            }
            
            $hariPraktikumTerpilih = $hariPraktikumTersedia[array_rand($hariPraktikumTersedia)];
            $jamPraktikum = $jamOptions[array_rand($jamOptions)];
            
            // Sesuaikan jam berdasarkan SKS praktikum
            if ($mk->sks_praktikum >= 2) {
                $jamPraktikum = '08:00-10:00';
            }
            
            Jadwal::create([
                'mata_kuliah_id' => $mk->id,
                'ruangan_id' => $ruanganPraktikum->id,
                'hari' => $hariPraktikumTerpilih,
                'jam_mulai' => $this->extractJamMulai($jamPraktikum, '08:00'),
                'jam_selesai' => $this->extractJamSelesai($jamPraktikum, '10:00'),
                'semester' => 'Ganjil',
                'tahun_akademik' => date('Y'),
                'prodi' => $mk->prodi ?? 'Teknologi Sains Data'
            ]);
            
            \Log::info('Created PRAKTIKUM jadwal for dosen ID 2: ' . $mk->nama_mk . ' - ' . $hariPraktikumTerpilih . ' ' . $jamPraktikum);
        }
    }

    private function aiPlottingAlgorithm($mataKuliahs, $ruangans, &$failedMataKuliah = [])
    {
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        // Generate jam slots berdasarkan custom SKS settings
        $jamSlots = $this->generateJamSlots();
        
        $jadwalGenerated = [];
        
        \Log::info('Starting AI plotting algorithm with ' . $mataKuliahs->count() . ' mata kuliah and ' . $ruangans->count() . ' ruangan');
        
        // Urutkan mata kuliah berdasarkan prioritas preferensi global
        $mataKuliahs = $mataKuliahs->sortBy(function($mk) {
            $preferensiGlobal = \App\Models\PreferensiDosen::where('dosen_id', $mk->dosen_id)
                ->whereNull('mata_kuliah_id')
                ->first();
            return $preferensiGlobal ? $preferensiGlobal->prioritas : 3;
        });
        
        foreach ($mataKuliahs as $mk) {
            \Log::info('Processing mata kuliah: ' . $mk->nama_mk . ' (ID: ' . $mk->id . ', Dosen ID: ' . $mk->dosen_id . ')');
            
            // Skip mata kuliah dosen ID 2 karena sudah dibuat jadwal secara manual
            if ($mk->dosen_id == 2) {
                \Log::info('Skipping mata kuliah dosen ID 2: ' . $mk->nama_mk . ' (already has jadwal)');
                continue;
            }
            
            if ($mk->ada_praktikum) {
                // Mata kuliah dengan praktikum - buat 2 jadwal terpisah
                \Log::info('Creating praktikum jadwal for: ' . $mk->nama_mk);
                $this->createJadwalPraktikum($mk, $ruangans, $hari, $jamSlots, $jadwalGenerated, $failedMataKuliah);
            } else {
                // Mata kuliah biasa
                \Log::info('Creating regular jadwal for: ' . $mk->nama_mk);
                $this->createJadwalBiasa($mk, $ruangans, $hari, $jamSlots, $jadwalGenerated, $failedMataKuliah);
            }
        }
        
        return $jadwalGenerated;
    }
    
    private function createJadwalBiasa($mk, $ruangans, $hari, $jamSlots, &$jadwalGenerated, &$failedMataKuliah = [])
    {
        // Cari preferensi global dosen (mata_kuliah_id = null)
        $preferensiGlobal = \App\Models\PreferensiDosen::where('dosen_id', $mk->dosen_id)
            ->whereNull('mata_kuliah_id')
            ->first();
        
        // Gunakan preferensi global jika ada, jika tidak gunakan default
        if ($preferensiGlobal) {
            $preferensiHari = $preferensiGlobal->preferensi_hari ?? $hari;
            $preferensiJam = $preferensiGlobal->preferensi_jam ?? $jamSlots;
            
            // Decode JSON if needed
            if (is_string($preferensiHari)) {
                $preferensiHari = json_decode($preferensiHari, true);
            }
            if (is_string($preferensiJam)) {
                $preferensiJam = json_decode($preferensiJam, true);
            }
            
            // Handle new format: if preferensi_jam is object with jam per hari, convert to flat array
            if (is_array($preferensiJam) && !isset($preferensiJam[0])) {
                // New format: object with jam per hari
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
            
            \Log::info('Using global preferensi for dosen ' . $mk->dosen_id . ' (' . $mk->dosen->name . '): ' . json_encode($preferensiHari) . ' | Jam: ' . json_encode($preferensiJam));
        } else {
            $preferensiHari = $hari;
            $preferensiJam = $jamSlots;
            \Log::info('No global preferensi found for dosen ' . $mk->dosen_id . ' (' . $mk->dosen->name . '), using default: ' . json_encode($preferensiHari));
        }
        
        // Filter jam slots berdasarkan custom SKS mata kuliah
        $jamSesuaiSKS = $this->filterJamByCustomSKS($preferensiJam, $mk->sks, $mk->menit_per_sks);
        
        // Cari ruangan yang sesuai dengan prodi - TIDAK ADA FALLBACK ke prodi lain
        $ruanganSesuai = $ruangans->where('kapasitas', '>=', $mk->kapasitas)
            ->where('prodi', $mk->prodi ?? 'Teknologi Sains Data')
            ->first();
        
        \Log::info('Ruangan search for ' . $mk->nama_mk . ' (Prodi: ' . ($mk->prodi ?? 'Teknologi Sains Data') . ', Kapasitas needed: ' . $mk->kapasitas . '): ' . ($ruanganSesuai ? 'Found: ' . $ruanganSesuai->nama_ruangan . ' (Prodi: ' . $ruanganSesuai->prodi . ')' : 'Not found'));
        
        // TIDAK MENGGUNAKAN FALLBACK - Harus sesuai prodi
        
        \Log::info('Jam slots after filtering: ' . json_encode($jamSesuaiSKS));
        
        if ($ruanganSesuai && !empty($jamSesuaiSKS)) {
            \Log::info('Creating jadwal for ' . $mk->nama_mk . ' in ' . $ruanganSesuai->nama_ruangan);
            $this->createSingleJadwal($mk, $ruanganSesuai, $preferensiHari, $jamSesuaiSKS, $jadwalGenerated);
        } else {
            $reason = [];
            if (!$ruanganSesuai) {
                $reason[] = 'Tidak ada ruangan yang sesuai (Prodi: ' . ($mk->prodi ?? 'Teknologi Sains Data') . ', Kapasitas: ' . $mk->kapasitas . ')';
            }
            if (empty($jamSesuaiSKS)) {
                $reason[] = 'Tidak ada jam slot yang sesuai dengan SKS ' . $mk->sks . ' (menit per SKS: ' . $mk->menit_per_sks . ')';
            }
            
            \Log::warning('❌ MATA KULIAH TIDAK BISA DIBUAT JADWAL: ' . $mk->nama_mk . ' (Dosen: ' . $mk->dosen->name . ') - Alasan: ' . implode(', ', $reason));
            
            // Simpan ke failed mata kuliah
            $failedMataKuliah[] = [
                'nama_mk' => $mk->nama_mk,
                'kode_mk' => $mk->kode_mk,
                'dosen' => $mk->dosen->name,
                'sks' => $mk->sks,
                'kapasitas' => $mk->kapasitas,
                'prodi' => $mk->prodi ?? 'Teknologi Sains Data',
                'reason' => implode(', ', $reason),
                'type' => 'not_generated'
            ];
        }
    }
    
    private function createJadwalPraktikum($mk, $ruangans, $hari, $jamSlots, &$jadwalGenerated, &$failedMataKuliah = [])
    {
        // Cari preferensi global dosen (mata_kuliah_id = null)
        $preferensiGlobal = \App\Models\PreferensiDosen::where('dosen_id', $mk->dosen_id)
            ->whereNull('mata_kuliah_id')
            ->first();
        
        if ($preferensiGlobal) {
            $preferensiHari = $preferensiGlobal->preferensi_hari ?? $hari;
            $preferensiJam = $preferensiGlobal->preferensi_jam ?? $jamSlots;
            
            // Decode JSON if needed
            if (is_string($preferensiHari)) {
                $preferensiHari = json_decode($preferensiHari, true);
            }
            if (is_string($preferensiJam)) {
                $preferensiJam = json_decode($preferensiJam, true);
            }
            
            // Handle new format: if preferensi_jam is object with jam per hari, convert to flat array
            if (is_array($preferensiJam) && !isset($preferensiJam[0])) {
                // New format: object with jam per hari
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
            
            \Log::info('Using global preferensi for praktikum dosen ' . $mk->dosen_id . ' (' . $mk->dosen->name . '): ' . json_encode($preferensiHari) . ' | Jam: ' . json_encode($preferensiJam));
        } else {
            $preferensiHari = $hari;
            $preferensiJam = $jamSlots;
            \Log::info('No global preferensi found for praktikum dosen ' . $mk->dosen_id . ' (' . $mk->dosen->name . '), using default: ' . json_encode($preferensiHari));
        }
        
        // Hari untuk materi (awal minggu: Senin, Selasa)
        $hariMateri = ['Senin', 'Selasa'];
        // Hari untuk praktikum - gunakan preferensi dosen jika ada, jika tidak gunakan default
        $hariPraktikumDefault = ['Rabu', 'Kamis', 'Jumat'];
        $hariPraktikum = $hariPraktikumDefault;
        
        // Cek apakah preferensi dosen memiliki hari yang cocok untuk praktikum
        if ($preferensiGlobal && isset($preferensiGlobal->preferensi_hari)) {
            $preferensiHariDosen = is_string($preferensiGlobal->preferensi_hari) 
                ? json_decode($preferensiGlobal->preferensi_hari, true) 
                : $preferensiGlobal->preferensi_hari;
            
            // Cari hari yang ada di preferensi dosen dan juga cocok untuk praktikum
            $hariPraktikumSesuaiPreferensi = array_intersect($preferensiHariDosen, $hariPraktikumDefault);
            
            if (!empty($hariPraktikumSesuaiPreferensi)) {
                // Prioritaskan preferensi dosen, tapi tetap sertakan hari default sebagai fallback
                $hariPraktikum = array_values($hariPraktikumSesuaiPreferensi);
                // Tambahkan hari default yang tidak ada di preferensi sebagai fallback
                $hariFallback = array_diff($hariPraktikumDefault, $hariPraktikum);
                $hariPraktikum = array_merge($hariPraktikum, $hariFallback);
                \Log::info('Menggunakan preferensi dosen untuk praktikum dengan fallback: ' . json_encode($hariPraktikum));
            } else {
                \Log::info('Preferensi dosen tidak cocok dengan hari praktikum, menggunakan default: ' . json_encode($hariPraktikum));
            }
        }
        
        // 1. Buat jadwal MATERI (harus di awal minggu) - Harus sesuai prodi
        $jamMateri = $this->filterJamByCustomSKS($preferensiJam, $mk->sks_materi, $mk->menit_per_sks);
        $ruanganMateri = $ruangans->where('kapasitas', '>=', $mk->kapasitas)
            ->where('prodi', $mk->prodi ?? 'Teknologi Sains Data')
            ->where('tipe_ruangan', 'kelas')
            ->first();
        
        \Log::info('Ruangan kelas search for materi ' . $mk->nama_mk . ' (Prodi: ' . ($mk->prodi ?? 'Teknologi Sains Data') . '): ' . ($ruanganMateri ? 'Found: ' . $ruanganMateri->nama_ruangan . ' (Prodi: ' . $ruanganMateri->prodi . ')' : 'Not found'));
        
        // TIDAK MENGGUNAKAN FALLBACK - Harus sesuai prodi
        
        if ($ruanganMateri && !empty($jamMateri)) {
            \Log::info('Creating materi jadwal for: ' . $mk->nama_mk);
            $this->createSingleJadwalWithSpecificDays($mk, $ruanganMateri, $hariMateri, $jamMateri, $jadwalGenerated, 'Materi');
        } else {
            $reason = [];
            if (!$ruanganMateri) {
                $reason[] = 'Tidak ada ruangan kelas yang sesuai (Kapasitas: ' . $mk->kapasitas . ')';
            }
            if (empty($jamMateri)) {
                $reason[] = 'Tidak ada jam slot yang sesuai untuk materi SKS ' . $mk->sks_materi;
            }
            
            \Log::warning('❌ MATERI TIDAK BISA DIBUAT JADWAL: ' . $mk->nama_mk . ' (Dosen: ' . $mk->dosen->name . ') - Alasan: ' . implode(', ', $reason));
        }
        
        // 2. Buat jadwal PRAKTIKUM (setelah hari materi, di lab) - Harus sesuai prodi
        $jamPraktikum = $this->filterJamByCustomSKS($preferensiJam, $mk->sks_praktikum, $mk->menit_per_sks);
        $ruanganPraktikum = $ruangans->where('kapasitas', '>=', $mk->kapasitas)
            ->where('prodi', $mk->prodi ?? 'Teknologi Sains Data')
            ->where('tipe_ruangan', 'lab')
            ->first();
        
        \Log::info('Ruangan lab search for praktikum ' . $mk->nama_mk . ' (Prodi: ' . ($mk->prodi ?? 'Teknologi Sains Data') . ', Kapasitas: ' . $mk->kapasitas . '): ' . ($ruanganPraktikum ? 'Found: ' . $ruanganPraktikum->nama_ruangan . ' (Prodi: ' . $ruanganPraktikum->prodi . ')' : 'Not found'));
        
        // TIDAK MENGGUNAKAN FALLBACK - Harus sesuai prodi
        
        if ($ruanganPraktikum && !empty($jamPraktikum)) {
            \Log::info('Creating praktikum jadwal for: ' . $mk->nama_mk);
            $this->createSingleJadwalWithSpecificDays($mk, $ruanganPraktikum, $hariPraktikum, $jamPraktikum, $jadwalGenerated, 'Praktikum', $failedMataKuliah);
        } else {
            $reason = [];
            if (!$ruanganPraktikum) {
                $reason[] = 'Tidak ada ruangan lab yang sesuai (Prodi: ' . ($mk->prodi ?? 'Teknologi Sains Data') . ', Kapasitas: ' . $mk->kapasitas . ')';
            }
            if (empty($jamPraktikum)) {
                $reason[] = 'Tidak ada jam slot yang sesuai untuk praktikum SKS ' . $mk->sks_praktikum;
            }
            
            \Log::warning('❌ PRAKTIKUM TIDAK BISA DIBUAT JADWAL: ' . $mk->nama_mk . ' (Dosen: ' . $mk->dosen->name . ') - Alasan: ' . implode(', ', $reason));
            
            // Simpan ke failed mata kuliah
            $failedMataKuliah[] = [
                'nama_mk' => $mk->nama_mk,
                'kode_mk' => $mk->kode_mk,
                'dosen' => $mk->dosen->name,
                'sks' => $mk->sks_praktikum,
                'kapasitas' => $mk->kapasitas,
                'prodi' => $mk->prodi ?? 'Teknologi Sains Data',
                'reason' => implode(', ', $reason),
                'type' => 'praktikum_not_generated',
                'preferensi_hari' => $preferensiGlobal ? json_encode($preferensiGlobal->preferensi_hari) : 'Tidak ada preferensi',
                'hari_tersedia' => json_encode($hariPraktikum)
            ];
        }
    }
    
    private function createSingleJadwal($mk, $ruangan, $preferensiHari, $jamSesuaiSKS, &$jadwalGenerated, $tipeKelas = '')
    {
        // STRICT PREFERENSI VALIDATION - Pastikan preferensiHari sesuai dengan preferensi dosen
        $preferensiGlobal = \App\Models\PreferensiDosen::where('dosen_id', $mk->dosen_id)
            ->whereNull('mata_kuliah_id')
            ->first();
        
        if ($preferensiGlobal && isset($preferensiGlobal->preferensi_hari)) {
            $hariPreferensiDosen = is_string($preferensiGlobal->preferensi_hari) 
                ? json_decode($preferensiGlobal->preferensi_hari, true) 
                : $preferensiGlobal->preferensi_hari;
            
            // Filter preferensiHari agar hanya berisi hari yang ada di preferensi dosen
            $preferensiHari = array_intersect($preferensiHari, $hariPreferensiDosen);
            
            if (empty($preferensiHari)) {
                \Log::warning('❌ TIDAK ADA HARI YANG SESUAI PREFERENSI: Dosen ' . $mk->dosen->name . ' tidak memiliki hari yang tersedia sesuai preferensi (' . json_encode($hariPreferensiDosen) . ')');
                return false;
            }
            
            \Log::info('✅ MENGGUNAKAN HARI SESUAI PREFERENSI: ' . json_encode($preferensiHari) . ' untuk dosen ' . $mk->dosen->name);
        }
        
        $jadwalBerhasil = false;
        $maxAttempts = 10;
        $attempt = 0;
        
        // Log detail preferensi dosen
        \Log::info('=== DETAILED PREFERENSI LOGGING ===');
        \Log::info('Dosen: ' . $mk->dosen->name . ' (ID: ' . $mk->dosen_id . ')');
        \Log::info('Mata Kuliah: ' . $mk->nama_mk . ' (ID: ' . $mk->id . ')');
        \Log::info('Preferensi Hari yang Diterima: ' . json_encode($preferensiHari));
        \Log::info('Jam Slots yang Tersedia: ' . json_encode($jamSesuaiSKS));
        \Log::info('Ruangan: ' . $ruangan->nama_ruangan . ' (ID: ' . $ruangan->id . ')');
        \Log::info('Tipe Kelas: ' . $tipeKelas);
        
        // Cek preferensi global dosen dari database
        $preferensiGlobal = \App\Models\PreferensiDosen::where('dosen_id', $mk->dosen_id)
            ->whereNull('mata_kuliah_id')
            ->first();
        
        if ($preferensiGlobal) {
            \Log::info('Preferensi Global dari Database:');
            \Log::info('- Hari: ' . json_encode($preferensiGlobal->preferensi_hari));
            \Log::info('- Jam: ' . json_encode($preferensiGlobal->preferensi_jam));
            \Log::info('- Prioritas: ' . $preferensiGlobal->prioritas);
            \Log::info('- Catatan: ' . $preferensiGlobal->catatan);
        } else {
            \Log::info('TIDAK ADA PREFERENSI GLOBAL untuk dosen ' . $mk->dosen_id);
        }
        
        // Validasi array tidak kosong sebelum loop
        if (empty($preferensiHari) || empty($jamSesuaiSKS)) {
            \Log::warning('❌ Array kosong: preferensiHari=' . count($preferensiHari) . ', jamSesuaiSKS=' . count($jamSesuaiSKS));
            return false;
        }
        
        while (!$jadwalBerhasil && $attempt < $maxAttempts) {
            \Log::info('Available preferensi hari for ' . $mk->nama_mk . ': ' . json_encode($preferensiHari));
            
            // Gunakan algoritma yang mengikuti urutan preferensi, bukan random
            $hariCount = count($preferensiHari);
            $jamCount = count($jamSesuaiSKS);
            
            if ($hariCount == 0 || $jamCount == 0) {
                \Log::warning('❌ Array kosong saat loop: preferensiHari=' . $hariCount . ', jamSesuaiSKS=' . $jamCount);
                break;
            }
            
            $hariIndex = $attempt % $hariCount;
            $jamIndex = $attempt % $jamCount;
            
            $hariTerpilih = $preferensiHari[$hariIndex];
            $jamTerpilih = $jamSesuaiSKS[$jamIndex];
            
            \Log::info('Attempt ' . ($attempt + 1) . ' for ' . $mk->nama_mk . ' (' . $tipeKelas . '): Trying hari ' . $hariTerpilih . ' at ' . $jamTerpilih . ' (hari index: ' . $hariIndex . ', jam index: ' . $jamIndex . ')');
            
            // Validasi format jam sebelum explode
            if (empty($jamTerpilih) || strpos($jamTerpilih, '-') === false) {
                \Log::warning('❌ Format jam tidak valid: ' . $jamTerpilih);
                $attempt++;
                continue;
            }
            
            $jamParts = explode('-', $jamTerpilih);
            if (count($jamParts) < 2) {
                \Log::warning('❌ Format jam tidak lengkap: ' . $jamTerpilih);
                $attempt++;
                continue;
            }
            
            [$jamMulai, $jamSelesai] = $jamParts;
            
            // Cek konflik
            $konflik = $this->checkKonflikJadwal($hariTerpilih, $ruangan->id, $jamMulai, $jamSelesai, $mk->dosen_id);
            
            if (!$konflik) {
                Jadwal::create([
                    'mata_kuliah_id' => $mk->id,
                    'ruangan_id' => $ruangan->id,
                    'hari' => $hariTerpilih,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'semester' => 'Ganjil',
                    'tahun_akademik' => date('Y'),
                    'prodi' => $mk->prodi ?? 'Teknologi Sains Data'
                ]);
                
                $namaKelas = $tipeKelas ? $mk->nama_mk . ' (' . $tipeKelas . ')' : $mk->nama_mk;
                $sksKelas = $tipeKelas === 'Materi' ? $mk->sks_materi : ($tipeKelas === 'Praktikum' ? $mk->sks_praktikum : $mk->sks);
                
                $jadwalGenerated[] = [
                    'mata_kuliah' => $namaKelas,
                    'dosen' => $mk->dosen->name,
                    'ruangan' => $ruangan->nama_ruangan,
                    'hari' => $hariTerpilih,
                    'jam' => $jamTerpilih,
                    'sks' => $sksKelas,
                    'prodi' => $mk->prodi ?? 'Teknologi Sains Data'
                ];
                
                \Log::info('Successfully created jadwal for: ' . $namaKelas . ' on ' . $hariTerpilih . ' at ' . $jamTerpilih);
                $jadwalBerhasil = true;
            }
            
            $attempt++;
        }
        
        if (!$jadwalBerhasil) {
            \Log::warning('Failed to create jadwal for: ' . $mk->nama_mk . ' (' . $tipeKelas . ') after ' . $maxAttempts . ' attempts');
        }
    }
    
    private function createSingleJadwalWithSpecificDays($mk, $ruangan, $hariTersedia, $jamSesuaiSKS, &$jadwalGenerated, $tipeKelas = '', &$failedMataKuliah = [])
    {
        // STRICT PREFERENSI VALIDATION - Pastikan hariTersedia sesuai dengan preferensi dosen
        $preferensiGlobal = \App\Models\PreferensiDosen::where('dosen_id', $mk->dosen_id)
            ->whereNull('mata_kuliah_id')
            ->first();
        
        if ($preferensiGlobal && isset($preferensiGlobal->preferensi_hari)) {
            $hariPreferensiDosen = is_string($preferensiGlobal->preferensi_hari) 
                ? json_decode($preferensiGlobal->preferensi_hari, true) 
                : $preferensiGlobal->preferensi_hari;
            
            // Filter hariTersedia agar hanya berisi hari yang ada di preferensi dosen
            $hariTersedia = array_intersect($hariTersedia, $hariPreferensiDosen);
            
            if (empty($hariTersedia)) {
                \Log::warning('❌ TIDAK ADA HARI YANG SESUAI PREFERENSI: Dosen ' . $mk->dosen->name . ' tidak memiliki hari yang tersedia sesuai preferensi (' . json_encode($hariPreferensiDosen) . ')');
                
                // Simpan ke failed mata kuliah
                $failedMataKuliah[] = [
                    'nama_mk' => $mk->nama_mk,
                    'kode_mk' => $mk->kode_mk,
                    'dosen' => $mk->dosen->name,
                    'sks' => $tipeKelas === 'Materi' ? $mk->sks_materi : ($tipeKelas === 'Praktikum' ? $mk->sks_praktikum : $mk->sks),
                    'kapasitas' => $mk->kapasitas,
                    'prodi' => $mk->prodi ?? 'Teknologi Sains Data',
                    'reason' => 'Tidak ada hari yang tersedia sesuai preferensi dosen',
                    'type' => $tipeKelas . '_no_preferensi_match',
                    'preferensi_hari' => json_encode($hariPreferensiDosen),
                    'hari_tersedia' => json_encode($hariTersedia)
                ];
                return false;
            }
            
            \Log::info('✅ MENGGUNAKAN HARI SESUAI PREFERENSI: ' . json_encode($hariTersedia) . ' untuk dosen ' . $mk->dosen->name);
        }
        
        // Log detail untuk praktikum
        \Log::info('=== PRAKTIKUM DETAILED LOGGING ===');
        \Log::info('Dosen: ' . $mk->dosen->name . ' (ID: ' . $mk->dosen_id . ')');
        \Log::info('Mata Kuliah: ' . $mk->nama_mk . ' (ID: ' . $mk->id . ')');
        \Log::info('Hari Tersedia: ' . json_encode($hariTersedia));
        \Log::info('Jam Slots: ' . json_encode($jamSesuaiSKS));
        \Log::info('Ruangan: ' . $ruangan->nama_ruangan . ' (ID: ' . $ruangan->id . ')');
        \Log::info('Tipe Kelas: ' . $tipeKelas);
        
        // Cek preferensi global dosen dari database
        $preferensiGlobal = \App\Models\PreferensiDosen::where('dosen_id', $mk->dosen_id)
            ->whereNull('mata_kuliah_id')
            ->first();
        
        if ($preferensiGlobal) {
            \Log::info('Preferensi Global untuk Praktikum:');
            \Log::info('- Hari: ' . json_encode($preferensiGlobal->preferensi_hari));
            \Log::info('- Jam: ' . json_encode($preferensiGlobal->preferensi_jam));
            \Log::info('- Prioritas: ' . $preferensiGlobal->prioritas);
        } else {
            \Log::info('TIDAK ADA PREFERENSI GLOBAL untuk praktikum dosen ' . $mk->dosen_id);
        }
        
        $jadwalBerhasil = false;
        $maxAttempts = count($hariTersedia) * count($jamSesuaiSKS); // Coba semua kombinasi yang mungkin
        $attempt = 0;
        
        \Log::info('Total possible combinations: ' . $maxAttempts . ' (hari: ' . count($hariTersedia) . ' x jam: ' . count($jamSesuaiSKS) . ')');
        
        // Validasi array tidak kosong sebelum loop
        if (empty($hariTersedia) || empty($jamSesuaiSKS)) {
            \Log::warning('❌ Array kosong: hariTersedia=' . count($hariTersedia) . ', jamSesuaiSKS=' . count($jamSesuaiSKS));
            return false;
        }
        
        while (!$jadwalBerhasil && $attempt < $maxAttempts) {
            // Gunakan algoritma yang mengikuti urutan preferensi, bukan random
            $hariCount = count($hariTersedia);
            $jamCount = count($jamSesuaiSKS);
            
            if ($hariCount == 0 || $jamCount == 0) {
                \Log::warning('❌ Array kosong saat loop: hariTersedia=' . $hariCount . ', jamSesuaiSKS=' . $jamCount);
                break;
            }
            
            $hariIndex = $attempt % $hariCount;
            $jamIndex = $attempt % $jamCount;
            
            $hariTerpilih = $hariTersedia[$hariIndex];
            $jamTerpilih = $jamSesuaiSKS[$jamIndex];
            
            \Log::info('Praktikum Attempt ' . ($attempt + 1) . ' for ' . $mk->nama_mk . ' (' . $tipeKelas . '): Trying hari ' . $hariTerpilih . ' at ' . $jamTerpilih . ' (hari index: ' . $hariIndex . ', jam index: ' . $jamIndex . ')');
            
            // Validasi format jam sebelum explode
            if (empty($jamTerpilih) || strpos($jamTerpilih, '-') === false) {
                \Log::warning('❌ Format jam tidak valid: ' . $jamTerpilih);
                $attempt++;
                continue;
            }
            
            $jamParts = explode('-', $jamTerpilih);
            if (count($jamParts) < 2) {
                \Log::warning('❌ Format jam tidak lengkap: ' . $jamTerpilih);
                $attempt++;
                continue;
            }
            
            [$jamMulai, $jamSelesai] = $jamParts;
            
            // Cek konflik
            $konflik = $this->checkKonflikJadwal($hariTerpilih, $ruangan->id, $jamMulai, $jamSelesai, $mk->dosen_id);
            
            if (!$konflik) {
                Jadwal::create([
                    'mata_kuliah_id' => $mk->id,
                    'ruangan_id' => $ruangan->id,
                    'hari' => $hariTerpilih,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'semester' => 'Ganjil',
                    'tahun_akademik' => date('Y'),
                    'prodi' => $mk->prodi ?? 'Teknologi Sains Data'
                ]);
                
                $namaKelas = $tipeKelas ? $mk->nama_mk . ' (' . $tipeKelas . ')' : $mk->nama_mk;
                $sksKelas = $tipeKelas === 'Materi' ? $mk->sks_materi : ($tipeKelas === 'Praktikum' ? $mk->sks_praktikum : $mk->sks);
                
                $jadwalGenerated[] = [
                    'mata_kuliah' => $namaKelas,
                    'dosen' => $mk->dosen->name,
                    'ruangan' => $ruangan->nama_ruangan,
                    'hari' => $hariTerpilih,
                    'jam' => $jamTerpilih,
                    'sks' => $sksKelas,
                    'prodi' => $mk->prodi ?? 'Teknologi Sains Data'
                ];
                
                \Log::info('Successfully created ' . $tipeKelas . ' jadwal for: ' . $namaKelas . ' on ' . $hariTerpilih . ' at ' . $jamTerpilih);
                $jadwalBerhasil = true;
            } else {
                \Log::info('Konflik check for ' . $hariTerpilih . ' at ' . $jamTerpilih . ' in room ' . $ruangan->id . ' for dosen ' . $mk->dosen_id . ': CONFLICT (Room: YES, Dosen: NO) - Logic: Conflict if room OR dosen conflict');
            }
            
            $attempt++;
        }
        
        if (!$jadwalBerhasil) {
            $reason = 'Setelah ' . $maxAttempts . ' percobaan, tidak ada slot yang tersedia';
            if (count($hariTersedia) > 0 && count($jamSesuaiSKS) > 0) {
                $reason .= ' - Semua kombinasi hari (' . implode(', ', $hariTersedia) . ') dan jam (' . implode(', ', $jamSesuaiSKS) . ') sudah terisi atau konflik';
            }
            
            \Log::warning('❌ JADWAL TIDAK BISA DIBUAT: ' . $mk->nama_mk . ' (' . $tipeKelas . ') - Dosen: ' . $mk->dosen->name . ' - Alasan: ' . $reason);
            
            // Simpan ke failed mata kuliah
            $failedMataKuliah[] = [
                'nama_mk' => $mk->nama_mk,
                'kode_mk' => $mk->kode_mk,
                'dosen' => $mk->dosen->name,
                'sks' => $tipeKelas === 'Materi' ? $mk->sks_materi : ($tipeKelas === 'Praktikum' ? $mk->sks_praktikum : $mk->sks),
                'kapasitas' => $mk->kapasitas,
                'prodi' => $mk->prodi ?? 'Teknologi Sains Data',
                'reason' => $reason,
                'type' => $tipeKelas . '_conflict',
                'hari_tersedia' => json_encode($hariTersedia),
                'jam_tersedia' => json_encode($jamSesuaiSKS)
            ];
        }
    }
    
    private function generateJamSlots()
    {
        $jamSlots = [];
        $startHour = 8;
        $endHour = 17;
        
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            for ($sks = 1; $sks <= 4; $sks++) {
                $duration = $sks * 50; // Default 50 menit per SKS
                $startTime = sprintf('%02d:00', $hour);
                $endTime = date('H:i', strtotime($startTime . ' +' . $duration . ' minutes'));
                
                if (strtotime($endTime) <= strtotime('17:00')) {
                    $jamSlots[] = $startTime . '-' . $endTime;
                }
            }
        }
        
        return array_unique($jamSlots);
    }
    
    private function filterJamByCustomSKS($preferensiJam, $sks, $menitPerSks)
    {
        $filteredSlots = [];
        
        // Decode JSON string if needed
        if (is_string($preferensiJam)) {
            $preferensiJam = json_decode($preferensiJam, true);
        }
        
        // Handle both old format (array of jam) and new format (object with jam per hari)
        $jamSlots = [];
        if (is_array($preferensiJam) && isset($preferensiJam[0]) && is_string($preferensiJam[0])) {
            // Old format: array of jam strings
            $jamSlots = $preferensiJam;
        } elseif (is_array($preferensiJam) || is_object($preferensiJam)) {
            // New format: object with jam per hari, collect all unique jam
            foreach ($preferensiJam as $hari => $jamHari) {
                if (is_array($jamHari)) {
                    foreach ($jamHari as $jam) {
                        if (!in_array($jam, $jamSlots)) {
                            $jamSlots[] = $jam;
                        }
                    }
                }
            }
        }
        
        if (empty($jamSlots)) {
            \Log::error('No jam slots found in preferensi: ' . json_encode($preferensiJam));
            return [];
        }
        
        \Log::info('Filtering jam slots for SKS: ' . $sks . ', Menit per SKS: ' . $menitPerSks);
        \Log::info('Jam slots to filter: ' . json_encode($jamSlots));
        
        $requiredDuration = $sks * $menitPerSks;
        
        // First, try individual slots
        foreach ($jamSlots as $jam) {
            // Validasi format jam sebelum explode
            if (empty($jam) || strpos($jam, '-') === false) {
                \Log::warning('❌ Format jam tidak valid di filterJamByCustomSKS: ' . $jam);
                continue;
            }
            
            $jamParts = explode('-', $jam);
            if (count($jamParts) < 2) {
                \Log::warning('❌ Format jam tidak lengkap di filterJamByCustomSKS: ' . $jam);
                continue;
            }
            
            [$start, $end] = $jamParts;
            $duration = (strtotime($end) - strtotime($start)) / 60;
            
            \Log::info("Individual jam slot: {$jam}, Duration: {$duration} min, Required: {$requiredDuration} min");
            
            if ($duration >= $requiredDuration) {
                $filteredSlots[] = $jam;
                \Log::info("✅ Individual jam slot {$jam} passed filter");
            } else {
                \Log::info("❌ Individual jam slot {$jam} failed filter (duration too short)");
            }
        }
        
        // If no individual slots work, try combining consecutive 1-hour slots
        if (empty($filteredSlots)) {
            \Log::info('No individual slots work, trying to combine consecutive 1-hour slots...');
            
            // Sort slots by start time
            $sortedSlots = array_filter($jamSlots, function($slot) {
                return !empty($slot) && strpos($slot, '-') !== false;
            });
            
            usort($sortedSlots, function($a, $b) {
                $partsA = explode('-', $a);
                $partsB = explode('-', $b);
                if (count($partsA) < 1 || count($partsB) < 1) {
                    return 0;
                }
                $startA = $partsA[0];
                $startB = $partsB[0];
                return strtotime($startA) - strtotime($startB);
            });
            
            \Log::info('Sorted slots: ' . json_encode($sortedSlots));
            
            // Try to combine consecutive 1-hour slots - IMPROVED: Try ALL possible combinations
            for ($i = 0; $i < count($sortedSlots); $i++) {
                $slotParts = explode('-', $sortedSlots[$i]);
                if (count($slotParts) < 1) {
                    continue;
                }
                $startTime = $slotParts[0];
                $currentEnd = $startTime;
                $totalDuration = 0;
                $combinedSlots = [];
                
                \Log::info("Trying combination starting from slot {$i}: {$sortedSlots[$i]}");
                
                for ($j = $i; $j < count($sortedSlots); $j++) {
                    $slotPartsJ = explode('-', $sortedSlots[$j]);
                    if (count($slotPartsJ) < 2) {
                        continue;
                    }
                    [$slotStart, $slotEnd] = $slotPartsJ;
                    $slotDuration = (strtotime($slotEnd) - strtotime($slotStart)) / 60;
                    
                    // Check if this slot is consecutive to the previous one OR if we can bridge the gap
                    if ($j == $i || strtotime($slotStart) == strtotime($currentEnd)) {
                        $combinedSlots[] = $sortedSlots[$j];
                        $totalDuration += $slotDuration;
                        $currentEnd = $slotEnd;
                        
                        \Log::info("Combining 1-hour slot {$j}: {$sortedSlots[$j]} (Duration: {$slotDuration} min, Total: {$totalDuration} min)");
                        
                        if ($totalDuration >= $requiredDuration) {
                            // Create combined slot from first start to last end
                            if (empty($combinedSlots)) {
                                continue;
                            }
                            $firstSlotParts = explode('-', $combinedSlots[0]);
                            if (count($firstSlotParts) < 1) {
                                continue;
                            }
                            $firstStart = $firstSlotParts[0];
                            $lastSlot = $combinedSlots[count($combinedSlots)-1];
                            $lastSlotParts = explode('-', $lastSlot);
                            if (count($lastSlotParts) < 2) {
                                continue;
                            }
                            [$lastStart, $lastEnd] = $lastSlotParts;
                            $combinedSlot = $firstStart . '-' . $lastEnd;
                            
                            $filteredSlots[] = $combinedSlot;
                            \Log::info("✅ Combined 1-hour slots created: {$combinedSlot} (Total duration: {$totalDuration} min)");
                            \Log::info("   Combined from: " . implode(' + ', $combinedSlots));
                            
                            // Continue trying other combinations instead of breaking
                            \Log::info("Continuing to try other combinations...");
                        }
                    } else {
                        // Check if we can bridge the gap (allow up to 1 hour gap)
                        $gapMinutes = (strtotime($slotStart) - strtotime($currentEnd)) / 60;
                        if ($gapMinutes <= 60) { // Allow 1 hour gap
                            $combinedSlots[] = $sortedSlots[$j];
                            $totalDuration += $slotDuration;
                            $currentEnd = $slotEnd;
                            
                            \Log::info("Bridging gap: {$gapMinutes} min, Combining slot {$j}: {$sortedSlots[$j]} (Duration: {$slotDuration} min, Total: {$totalDuration} min)");
                            
                            if ($totalDuration >= $requiredDuration) {
                                // Create combined slot from first start to last end
                                if (empty($combinedSlots)) {
                                    continue;
                                }
                                $firstSlotParts = explode('-', $combinedSlots[0]);
                                if (count($firstSlotParts) < 1) {
                                    continue;
                                }
                                $firstStart = $firstSlotParts[0];
                                $lastSlot = $combinedSlots[count($combinedSlots)-1];
                                $lastSlotParts = explode('-', $lastSlot);
                                if (count($lastSlotParts) < 2) {
                                    continue;
                                }
                                [$lastStart, $lastEnd] = $lastSlotParts;
                                $combinedSlot = $firstStart . '-' . $lastEnd;
                                
                                $filteredSlots[] = $combinedSlot;
                                \Log::info("✅ Combined slots with gap bridging: {$combinedSlot} (Total duration: {$totalDuration} min)");
                                \Log::info("   Combined from: " . implode(' + ', $combinedSlots));
                                
                                // Continue trying other combinations instead of breaking
                                \Log::info("Continuing to try other combinations...");
                            }
                        } else {
                            break; // Gap too large, stop combining
                        }
                    }
                }
            }
        }
        
        \Log::info('Filtered slots result: ' . json_encode($filteredSlots));
        return $filteredSlots;
    }
    
    /**
     * Extract jam mulai dari string jam dengan validasi
     */
    private function extractJamMulai($jamString, $default = '08:00')
    {
        if (empty($jamString) || strpos($jamString, '-') === false) {
            return $default;
        }
        $parts = explode('-', $jamString);
        return count($parts) >= 1 ? trim($parts[0]) : $default;
    }
    
    /**
     * Extract jam selesai dari string jam dengan validasi
     */
    private function extractJamSelesai($jamString, $default = '10:00')
    {
        if (empty($jamString) || strpos($jamString, '-') === false) {
            return $default;
        }
        $parts = explode('-', $jamString);
        return count($parts) >= 2 ? trim($parts[1]) : $default;
    }
    
    private function checkKonflikJadwal($hari, $ruanganId, $jamMulai, $jamSelesai, $dosenId = null)
    {
        // Check for conflicts in the same room - FIXED LOGIC
        $konflikRuangan = Jadwal::where('hari', $hari)
            ->where('ruangan_id', $ruanganId)
            ->where(function($query) use ($jamMulai, $jamSelesai) {
                // Correct overlap logic: (start1 < end2) AND (end1 > start2)
                $query->where('jam_mulai', '<', $jamSelesai)
                      ->where('jam_selesai', '>', $jamMulai);
            })->exists();
        
        // Check for conflicts with the same dosen (if dosenId provided) - FIXED LOGIC
        $konflikDosen = false;
        if ($dosenId) {
            $konflikDosen = Jadwal::where('hari', $hari)
                ->whereHas('mataKuliah', function($query) use ($dosenId) {
                    $query->where('dosen_id', $dosenId);
                })
                ->where(function($query) use ($jamMulai, $jamSelesai) {
                    // Correct overlap logic: (start1 < end2) AND (end1 > start2)
                    $query->where('jam_mulai', '<', $jamSelesai)
                          ->where('jam_selesai', '>', $jamMulai);
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
    public function jadwal(Request $request)
    {
        $prodiFilter = $request->get('prodi', 'all');
        
        \Log::info('Jadwal filter request:', [
            'prodi_filter' => $prodiFilter,
            'all_params' => $request->all()
        ]);
        
        $query = Jadwal::with('mataKuliah.dosen', 'ruangan');
        
        if ($prodiFilter !== 'all') {
            $query->where('prodi', $prodiFilter);
        }
        
        $jadwals = $query->get();
        
        \Log::info('Jadwal query result:', [
            'total_jadwals' => $jadwals->count(),
            'prodi_filter' => $prodiFilter,
            'jadwals_prodi' => $jadwals->pluck('prodi')->unique()->toArray()
        ]);
        
        // Get list of prodi for filter
        $prodiList = [
            'all' => 'Semua Prodi',
            'Teknologi Sains Data' => 'Teknologi Sains Data',
            'Rekayasa Nanoteknologi' => 'Rekayasa Nanoteknologi',
            'Teknik Industri' => 'Teknik Industri',
            'Teknik Elektro' => 'Teknik Elektro',
            'Teknik Robotika dan Kecerdasan Buatan' => 'Teknik Robotika dan Kecerdasan Buatan'
        ];
        
        // Get data for edit form
        $mataKuliahs = \App\Models\MataKuliah::with('dosen')->get();
        $ruangans = \App\Models\Ruangan::where('status', true)->get();
        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        return view('super-admin.jadwal', compact('jadwals', 'prodiList', 'prodiFilter', 'mataKuliahs', 'ruangans', 'hariOptions'));
    }

    // Fungsi untuk mengecek kapasitas ruangan
    private function checkRuanganCapacity($mataKuliahs, $ruangans)
    {
        $warnings = [];
        $ruanganCapacity = $ruangans->pluck('kapasitas', 'nama_ruangan')->toArray();
        
        \Log::info('Checking ruangan capacity', [
            'ruangan_capacity' => $ruanganCapacity,
            'mata_kuliah_count' => $mataKuliahs->count()
        ]);
        
        foreach ($mataKuliahs as $mk) {
            $kapasitasNeeded = $mk->kapasitas;
            $namaMK = $mk->nama_mk;
            
            // Cek ruangan untuk mata kuliah biasa
            if (!$mk->ada_praktikum) {
                $ruanganSesuai = $ruangans->where('kapasitas', '>=', $kapasitasNeeded)->first();
                
                if (!$ruanganSesuai) {
                    $maxCapacity = $ruangans->max('kapasitas') ?? 0;
                    $warning = "Mata kuliah '{$namaMK}' membutuhkan ruangan dengan kapasitas {$kapasitasNeeded} mahasiswa, tetapi ruangan terbesar yang tersedia hanya {$maxCapacity} mahasiswa";
                    $warnings[] = $warning;
                    
                    \Log::warning('Ruangan capacity warning for regular class', [
                        'mata_kuliah' => $namaMK,
                        'kapasitas_needed' => $kapasitasNeeded,
                        'max_available_capacity' => $maxCapacity
                    ]);
                }
            } else {
                // Cek ruangan untuk mata kuliah dengan praktikum
                // 1. Cek ruangan untuk materi (kelas)
                $ruanganMateri = $ruangans->where('kapasitas', '>=', $kapasitasNeeded)
                    ->where('tipe_ruangan', 'kelas')
                    ->first();
                
                if (!$ruanganMateri) {
                    $maxKelasCapacity = $ruangans->where('tipe_ruangan', 'kelas')->max('kapasitas') ?? 0;
                    $warning = "Mata kuliah '{$namaMK}' (Materi) membutuhkan ruangan kelas dengan kapasitas {$kapasitasNeeded} mahasiswa, tetapi ruangan kelas terbesar yang tersedia hanya {$maxKelasCapacity} mahasiswa";
                    $warnings[] = $warning;
                    
                    \Log::warning('Ruangan capacity warning for materi class', [
                        'mata_kuliah' => $namaMK,
                        'kapasitas_needed' => $kapasitasNeeded,
                        'max_kelas_capacity' => $maxKelasCapacity
                    ]);
                }
                
                // 2. Cek ruangan untuk praktikum (lab)
                $ruanganPraktikum = $ruangans->where('kapasitas', '>=', $kapasitasNeeded)
                    ->where('tipe_ruangan', 'lab')
                    ->first();
                
                if (!$ruanganPraktikum) {
                    $maxLabCapacity = $ruangans->where('tipe_ruangan', 'lab')->max('kapasitas') ?? 0;
                    $warning = "Mata kuliah '{$namaMK}' (Praktikum) membutuhkan ruangan lab dengan kapasitas {$kapasitasNeeded} mahasiswa, tetapi ruangan lab terbesar yang tersedia hanya {$maxLabCapacity} mahasiswa";
                    $warnings[] = $warning;
                    
                    \Log::warning('Ruangan capacity warning for praktikum lab', [
                        'mata_kuliah' => $namaMK,
                        'kapasitas_needed' => $kapasitasNeeded,
                        'max_lab_capacity' => $maxLabCapacity
                    ]);
                }
            }
        }
        
        \Log::info('Ruangan capacity check completed', [
            'warnings_count' => count($warnings),
            'warnings' => $warnings
        ]);
        
        return $warnings;
    }

}