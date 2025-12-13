<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\User;
use App\Models\Jadwal;
use App\Models\Ruangan;
use App\Models\FactJadwal;
use App\Models\FactUtilisasiRuangan;
use App\Models\FactKecocokanJadwal;
use App\Models\DimProdi;
use App\Models\DimDosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class AdminProdiController extends Controller
{
    // Dashboard Admin Prodi
    public function dashboard()
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        
        // Data dari tabel operasional
        $totalMataKuliah = MataKuliah::where('prodi', $prodi)->count();
        $totalDosen = User::where('role', 'dosen')
            ->where('prodi', $prodi)
            ->count();
        $mataKuliahAktif = MataKuliah::where('prodi', $prodi)->count();

        // Ambil prodi_key dari dim_prodi berdasarkan nama prodi
        $dimProdi = DimProdi::where('nama_prodi', $prodi)->where('is_active', true)->first();
        
        if ($dimProdi) {
            $prodiKey = $dimProdi->prodi_key;
            
            // Data dari Fact Tables untuk prodi ini - LANGSUNG DARI DATABASE, TIDAK DIMANIPULASI
            // FactJadwal - Statistik jadwal prodi
            $totalJadwalFact = FactJadwal::where('prodi_key', $prodiKey)
                ->where('status_aktif', true)
                ->count();
            
            $totalKonflik = FactJadwal::where('prodi_key', $prodiKey)
                ->where('status_aktif', true)
                ->where('konflik_jadwal', true)
                ->count();
            
            // Utilisasi ruangan dari FactUtilisasiRuangan (bukan dari FactJadwal)
            $avgUtilisasiGlobal = FactUtilisasiRuangan::where('prodi_key', $prodiKey)
                ->avg('persentase_utilisasi');
            
            // Jika null, set ke 0
            $avgUtilisasiRuangan = $avgUtilisasiGlobal ?? 0;
            
            // Total mahasiswa dari FactJadwal
            $totalMahasiswa = FactJadwal::where('prodi_key', $prodiKey)
                ->where('status_aktif', true)
                ->sum('jumlah_mahasiswa') ?? 0;
            
            // FactUtilisasiRuangan - Top 5 ruangan paling sering digunakan
            $topRuanganUtilisasi = FactUtilisasiRuangan::with('dimRuangan')
                ->where('prodi_key', $prodiKey)
                ->orderBy('persentase_utilisasi', 'desc')
                ->limit(5)
                ->get();
            
            // FactKecocokanJadwal - Analisis kepuasan preferensi dosen di prodi ini
            // Ambil dosen_key dari dosen di prodi ini
            $dosenProdi = User::where('role', 'dosen')->where('prodi', $prodi)->pluck('nip')->filter()->toArray();
            
            if (empty($dosenProdi)) {
                $avgKecocokan = 0;
                $totalPreferensiTerpenuhi = 0;
            } else {
                $dimDosenProdi = DimDosen::whereIn('nip', $dosenProdi)
                    ->where('is_active', true)
                    ->pluck('dosen_key')
                    ->filter()
                    ->toArray();
                
                if (empty($dimDosenProdi)) {
                    $avgKecocokan = 0;
                    $totalPreferensiTerpenuhi = 0;
                } else {
                    // Rata-rata kecocokan preferensi - LOGIKA BARU: Rata-rata persentase semua jadwal dosen
                    // Jika seluruh jadwal sesuai preferensi → 100%, jika ada yang di luar → berkurang
                    $totalJadwalDosen = FactKecocokanJadwal::whereIn('dosen_key', $dimDosenProdi)->count();
                    $jadwalSesuai = FactKecocokanJadwal::whereIn('dosen_key', $dimDosenProdi)
                        ->where('preferensi_hari_terpenuhi', true)
                        ->where('preferensi_jam_terpenuhi', true)
                        ->count();
                    
                    // Persentase = (jadwal yang sesuai / total jadwal) * 100
                    $avgKecocokan = $totalJadwalDosen > 0 
                        ? ($jadwalSesuai / $totalJadwalDosen) * 100 
                        : 0;
                    
                    // Total preferensi yang terpenuhi - LANGSUNG DARI DATABASE
                    $totalPreferensiTerpenuhi = FactKecocokanJadwal::whereIn('dosen_key', $dimDosenProdi)
                        ->where('preferensi_hari_terpenuhi', true)
                        ->where('preferensi_jam_terpenuhi', true)
                        ->count();
                }
            }
            
            
            // Top 5 dosen dengan beban mengajar tertinggi di prodi ini - LANGSUNG DARI DATABASE
            // Hanya ambil dosen yang masih ada di tabel users (belum dihapus)
            $dosenAktifNIPs = User::where('role', 'dosen')
                ->where('prodi', $prodi)
                ->pluck('nip')
                ->filter()
                ->toArray();
            
            $dosenAktifEmails = User::where('role', 'dosen')
                ->where('prodi', $prodi)
                ->pluck('email')
                ->filter()
                ->toArray();
            
            // Get dosen_key dari dim_dosen yang masih aktif
            $dosenKeysAktif = DimDosen::where('is_active', true)
                ->where(function($query) use ($dosenAktifNIPs, $dosenAktifEmails) {
                    $query->whereIn('nip', $dosenAktifNIPs)
                          ->orWhereIn('email', $dosenAktifEmails);
                })
                ->pluck('dosen_key')
                ->toArray();
            
            $topDosenBeban = FactJadwal::select(
                'dosen_key',
                DB::raw('COUNT(*) as total_jadwal'),
                DB::raw('SUM(jumlah_sks) as total_sks'),
                DB::raw('SUM(jumlah_mahasiswa) as total_mahasiswa')
            )
            ->where('prodi_key', $prodiKey)
            ->where('status_aktif', true)
            ->whereIn('dosen_key', $dosenKeysAktif) // Hanya dosen yang masih aktif
            ->with('dimDosen')
            ->groupBy('dosen_key')
            ->orderBy('total_jadwal', 'desc')
            ->limit(5)
            ->get();
        } else {
            // Fallback jika dimensi belum ada
            $totalJadwalFact = 0;
            $totalKonflik = 0;
            $avgUtilisasiRuangan = 0;
            $totalMahasiswa = 0;
            $topRuanganUtilisasi = collect();
            $avgUtilisasiGlobal = 0;
            $avgKecocokan = 0;
            $totalPreferensiTerpenuhi = 0;
            $topDosenBeban = collect();
        }

        return view('admin-prodi.dashboard', compact(
            'totalMataKuliah', 
            'totalDosen', 
            'mataKuliahAktif', 
            'prodi',
            'totalJadwalFact',
            'totalKonflik',
            'avgUtilisasiRuangan',
            'totalMahasiswa',
            'topRuanganUtilisasi',
            'avgUtilisasiGlobal',
            'avgKecocokan',
            'totalPreferensiTerpenuhi',
            'topDosenBeban'
        ));
    }

    // Manajemen Mata Kuliah untuk Prodi
    public function mataKuliah()
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        $mataKuliahs = MataKuliah::where('prodi', $prodi)->with('dosen')->get();
        // Hanya tampilkan dosen dari prodi yang sama
        $dosens = User::where('role', 'dosen')
            ->where('prodi', $prodi)
            ->get();
        
        return view('admin-prodi.mata-kuliah', compact('mataKuliahs', 'dosens', 'prodi'));
    }

    public function storeMataKuliah(Request $request)
    {
        try {
            $admin = Auth::user();
            $prodi = $admin->prodi;
            
        $request->validate([
            'kode_mk' => 'required|unique:mata_kuliahs',
            'nama_mk' => 'required',
            'sks' => 'required|integer|min:1|max:12', // Maksimal 12 untuk praktikum (6+6)
            'semester' => 'required',
            'dosen_id' => 'required|exists:users,id',
            'kapasitas' => 'required|integer|min:1',
            'tipe_kelas' => 'required|in:teori,praktikum',
            'menit_per_sks' => 'required|integer|min:30|max:120',
            'deskripsi' => 'nullable',
            'sks_materi' => 'nullable|integer|min:0|max:6',
            'sks_praktikum' => 'nullable|integer|min:0|max:6'
        ]);

            $data = $request->all();
            $data['prodi'] = $prodi;
            
            // Set praktikum fields
            if ($request->tipe_kelas === 'praktikum') {
                $data['ada_praktikum'] = true;
                $data['sks_materi'] = $request->sks_materi ?? 2;
                $data['sks_praktikum'] = $request->sks_praktikum ?? 1;
            } else {
                $data['ada_praktikum'] = false;
                $data['sks_materi'] = 0;
                $data['sks_praktikum'] = 0;
            }

            // Cek kapasitas ruangan sebelum menyimpan
            $kapasitasNeeded = $request->kapasitas;
            $ruangans = Ruangan::where('status', true)->where('prodi', $prodi)->get();
            
            $warnings = [];
            if ($request->tipe_kelas === 'praktikum') {
                // Cek ruangan lab untuk praktikum
                $ruanganLab = $ruangans->where('kapasitas', '>=', $kapasitasNeeded)
                    ->where('tipe_ruangan', 'lab')
                    ->first();
                
                if (!$ruanganLab) {
                    $maxLabCapacity = $ruangans->where('tipe_ruangan', 'lab')->max('kapasitas') ?? 0;
                    $warnings[] = "Tidak ada ruangan lab dengan kapasitas {$kapasitasNeeded} mahasiswa. Kapasitas lab terbesar: {$maxLabCapacity} mahasiswa";
                }
                
                // Cek ruangan kelas untuk materi
                $ruanganKelas = $ruangans->where('kapasitas', '>=', $kapasitasNeeded)
                    ->where('tipe_ruangan', 'kelas')
                    ->first();
                
                if (!$ruanganKelas) {
                    $maxKelasCapacity = $ruangans->where('tipe_ruangan', 'kelas')->max('kapasitas') ?? 0;
                    $warnings[] = "Tidak ada ruangan kelas dengan kapasitas {$kapasitasNeeded} mahasiswa. Kapasitas kelas terbesar: {$maxKelasCapacity} mahasiswa";
                }
            } else {
                // Cek ruangan untuk mata kuliah biasa
                $ruanganSesuai = $ruangans->where('kapasitas', '>=', $kapasitasNeeded)->first();
                
                if (!$ruanganSesuai) {
                    $maxCapacity = $ruangans->max('kapasitas') ?? 0;
                    $warnings[] = "Tidak ada ruangan dengan kapasitas {$kapasitasNeeded} mahasiswa. Kapasitas ruangan terbesar: {$maxCapacity} mahasiswa";
                }
            }

            \Log::info('Creating mata kuliah with data:', $data);
            \Log::info('Request data:', $request->all());

            MataKuliah::create($data);

            $successMessage = 'Mata kuliah berhasil ditambahkan';
            
            if (!empty($warnings)) {
                $warningMessage = ' Namun, terdapat peringatan: ' . implode('; ', $warnings);
                $successMessage .= $warningMessage;
                
                return redirect()->route('admin-prodi.mata-kuliah')
                    ->with('success', $successMessage)
                    ->with('warnings', $warnings);
            }

            return redirect()->route('admin-prodi.mata-kuliah')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Error creating mata kuliah: ' . $e->getMessage());
            return redirect()->route('admin-prodi.mata-kuliah')->with('error', 'Gagal menambahkan mata kuliah: ' . $e->getMessage());
        }
    }

    public function updateMataKuliah(Request $request, $id)
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        
        $request->validate([
            'kode_mk' => 'required|unique:mata_kuliahs,kode_mk,' . $id,
            'nama_mk' => 'required',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required',
            'dosen_id' => 'required|exists:users,id',
            'kapasitas' => 'required|integer|min:1',
            'tipe_kelas' => 'required|in:teori,praktikum',
            'menit_per_sks' => 'required|integer|min:30|max:120',
            'deskripsi' => 'nullable',
            'sks_materi' => 'nullable|integer|min:0|max:6',
            'sks_praktikum' => 'nullable|integer|min:0|max:6'
        ]);

        $mataKuliah = MataKuliah::where('id', $id)->where('prodi', $prodi)->firstOrFail();
        
        $data = $request->all();
        
        // Set praktikum fields
        if ($request->tipe_kelas === 'praktikum') {
            $data['ada_praktikum'] = true;
            $data['sks_materi'] = $request->sks_materi ?? 2;
            $data['sks_praktikum'] = $request->sks_praktikum ?? 1;
        } else {
            $data['ada_praktikum'] = false;
            $data['sks_materi'] = 0;
            $data['sks_praktikum'] = 0;
        }
        
        $mataKuliah->update($data);

        return redirect()->route('admin-prodi.mata-kuliah')->with('success', 'Mata kuliah berhasil diperbarui');
    }

    public function destroyMataKuliah($id)
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        
        $mataKuliah = MataKuliah::where('id', $id)->where('prodi', $prodi)->firstOrFail();
        $mataKuliah->delete();

        return redirect()->route('admin-prodi.mata-kuliah')->with('success', 'Mata kuliah berhasil dihapus');
    }

    public function getMataKuliah($id)
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        
        $mataKuliah = MataKuliah::where('id', $id)->where('prodi', $prodi)->firstOrFail();
        
        return response()->json([
            'id' => $mataKuliah->id,
            'kode_mk' => $mataKuliah->kode_mk,
            'nama_mk' => $mataKuliah->nama_mk,
            'sks' => $mataKuliah->sks,
            'semester' => $mataKuliah->semester,
            'dosen_id' => $mataKuliah->dosen_id,
            'kapasitas' => $mataKuliah->kapasitas,
            'tipe_kelas' => $mataKuliah->tipe_kelas,
            'menit_per_sks' => $mataKuliah->menit_per_sks,
            'deskripsi' => $mataKuliah->deskripsi,
            'ada_praktikum' => $mataKuliah->ada_praktikum,
            'sks_materi' => $mataKuliah->sks_materi,
            'sks_praktikum' => $mataKuliah->sks_praktikum
        ]);
    }

    // Manajemen Dosen untuk Prodi
    public function dosen()
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        $dosens = User::where('role', 'dosen')->where('prodi', $prodi)->get();
        
        return view('admin-prodi.dosen', compact('dosens', 'prodi'));
    }

    public function storeDosen(Request $request)
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nip' => 'required|string|unique:users,nip',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['role'] = 'dosen';
        $data['prodi'] = $prodi;

        User::create($data);

        return redirect()->route('admin-prodi.dosen')->with('success', 'Akun dosen berhasil ditambahkan');
    }

    public function updateDosen(Request $request, $id)
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nip' => 'required|string|unique:users,nip,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $dosen = User::where('id', $id)->where('role', 'dosen')->where('prodi', $prodi)->firstOrFail();
        
        $data = $request->except('password');
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $dosen->update($data);

        return redirect()->route('admin-prodi.dosen')->with('success', 'Data dosen berhasil diperbarui');
    }

    public function destroyDosen($id)
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        
        $dosen = User::where('id', $id)->where('role', 'dosen')->where('prodi', $prodi)->firstOrFail();
        $dosen->delete();

        return redirect()->route('admin-prodi.dosen')->with('success', 'Akun dosen berhasil dihapus');
    }

    public function getDosen($id)
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        
        $dosen = User::where('id', $id)->where('role', 'dosen')->where('prodi', $prodi)->firstOrFail();
        return response()->json($dosen);
    }

    // Generate Jadwal AI untuk Admin Prodi
    public function generateJadwal()
    {
        try {
            $admin = Auth::user();
            $prodi = $admin->prodi;
            
            \Log::info('Admin Prodi Generate jadwal started', [
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'prodi' => $prodi
            ]);
            
            // Algoritma AI untuk plotting jadwal - hanya untuk prodi admin yang login
            $mataKuliahs = MataKuliah::where('prodi', $prodi)
                ->with('dosen', 'preferensiDosen')
                ->get();
            $ruangans = Ruangan::where('status', true)
                ->where('prodi', $prodi)
                ->get();
            
            \Log::info('Generate jadwal data loaded', [
                'mata_kuliah_count' => $mataKuliahs->count(),
                'ruangan_count' => $ruangans->count(),
                'prodi' => $prodi
            ]);
            
            // Cek kapasitas ruangan sebelum generate jadwal
            $warnings = $this->checkRuanganCapacity($mataKuliahs, $ruangans);
            
            // Hapus jadwal lama untuk prodi ini saja
            Jadwal::where('prodi', $prodi)->delete();
            \Log::info('Old jadwal for prodi ' . $prodi . ' deleted');
            
            $jadwalGenerated = $this->aiPlottingAlgorithm($mataKuliahs, $ruangans, $prodi);
            
            \Log::info('Generate jadwal completed', [
                'jadwal_generated_count' => count($jadwalGenerated),
                'prodi' => $prodi
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
            $successMessage = 'Jadwal berhasil di-generate menggunakan AI untuk prodi ' . $prodi . '. Total jadwal: ' . count($jadwalGenerated);
            
            if (!empty($warnings)) {
                $warningMessage = ' Namun, terdapat peringatan: ' . implode('; ', $warnings);
                $successMessage .= $warningMessage;
                
                return redirect()->route('admin-prodi.jadwal')
                    ->with('success', $successMessage)
                    ->with('warnings', $warnings);
            }
            
            return redirect()->route('admin-prodi.jadwal')->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Error generating jadwal for admin prodi: ' . $e->getMessage(), [
                'admin_id' => Auth::id(),
                'prodi' => Auth::user()->prodi ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('admin-prodi.jadwal')->with('error', 'Gagal generate jadwal: ' . $e->getMessage());
        }
    }

    private function aiPlottingAlgorithm($mataKuliahs, $ruangans, $prodi)
    {
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        // Generate jam slots berdasarkan custom SKS settings
        $jamSlots = $this->generateJamSlots();
        
        $jadwalGenerated = [];
        
        \Log::info('Starting AI plotting algorithm for prodi ' . $prodi . ' with ' . $mataKuliahs->count() . ' mata kuliah and ' . $ruangans->count() . ' ruangan');
        
        // Urutkan mata kuliah berdasarkan prioritas preferensi global
        $mataKuliahs = $mataKuliahs->sortBy(function($mk) {
            $preferensiGlobal = \App\Models\PreferensiDosen::where('dosen_id', $mk->dosen_id)
                ->whereNull('mata_kuliah_id')
                ->first();
            return $preferensiGlobal ? $preferensiGlobal->prioritas : 3;
        });
        
        foreach ($mataKuliahs as $mk) {
            \Log::info('Processing mata kuliah: ' . $mk->nama_mk . ' (ID: ' . $mk->id . ', Dosen ID: ' . $mk->dosen_id . ', Prodi: ' . $mk->prodi . ')');
            
            if ($mk->ada_praktikum) {
                // Mata kuliah dengan praktikum - buat 2 jadwal terpisah
                \Log::info('Creating praktikum jadwal for: ' . $mk->nama_mk);
                $this->createJadwalPraktikum($mk, $ruangans, $hari, $jamSlots, $jadwalGenerated, $prodi);
            } else {
                // Mata kuliah biasa
                \Log::info('Creating regular jadwal for: ' . $mk->nama_mk);
                $this->createJadwalBiasa($mk, $ruangans, $hari, $jamSlots, $jadwalGenerated, $prodi);
            }
        }
        
        return $jadwalGenerated;
    }
    
    private function createJadwalBiasa($mk, $ruangans, $hari, $jamSlots, &$jadwalGenerated, $prodi)
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
            
            \Log::info('Using global preferensi for dosen ' . $mk->dosen_id . ' (' . $mk->dosen->name . '): ' . json_encode($preferensiHari) . ' | Jam: ' . json_encode($preferensiJam));
        } else {
            $preferensiHari = $hari;
            $preferensiJam = $jamSlots;
            \Log::info('No global preferensi found for dosen ' . $mk->dosen_id . ' (' . $mk->dosen->name . '), using default: ' . json_encode($preferensiHari));
        }
        
        // Filter jam slots berdasarkan custom SKS mata kuliah
        $jamSesuaiSKS = $this->filterJamByCustomSKS($preferensiJam, $mk->sks, $mk->menit_per_sks);
        
        // Cari ruangan yang sesuai dengan prodi
        $ruanganSesuai = $ruangans->where('kapasitas', '>=', $mk->kapasitas)->first();
        
        \Log::info('Ruangan search for ' . $mk->nama_mk . ' (Prodi: ' . $prodi . ', Kapasitas needed: ' . $mk->kapasitas . '): ' . ($ruanganSesuai ? 'Found: ' . $ruanganSesuai->nama_ruangan : 'Not found'));
        
        \Log::info('Jam slots after filtering: ' . json_encode($jamSesuaiSKS));
        
        if ($ruanganSesuai && !empty($jamSesuaiSKS)) {
            \Log::info('Creating jadwal for ' . $mk->nama_mk . ' in ' . $ruanganSesuai->nama_ruangan);
            $this->createSingleJadwal($mk, $ruanganSesuai, $preferensiHari, $jamSesuaiSKS, $jadwalGenerated, '', $prodi);
        } else {
            $reason = [];
            if (!$ruanganSesuai) {
                $reason[] = 'Tidak ada ruangan yang sesuai (Prodi: ' . $prodi . ', Kapasitas: ' . $mk->kapasitas . ')';
            }
            if (empty($jamSesuaiSKS)) {
                $reason[] = 'Tidak ada jam slot yang sesuai dengan SKS ' . $mk->sks . ' (menit per SKS: ' . $mk->menit_per_sks . ')';
            }
            
            \Log::warning('❌ MATA KULIAH TIDAK BISA DIBUAT JADWAL: ' . $mk->nama_mk . ' (Dosen: ' . $mk->dosen->name . ', Prodi: ' . $prodi . ') - Alasan: ' . implode(', ', $reason));
        }
    }
    
    private function createJadwalPraktikum($mk, $ruangans, $hari, $jamSlots, &$jadwalGenerated, $prodi)
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
        
        // 1. Buat jadwal MATERI (harus di awal minggu)
        $jamMateri = $this->filterJamByCustomSKS($preferensiJam, $mk->sks_materi, $mk->menit_per_sks);
        $ruanganMateri = $ruangans->where('kapasitas', '>=', $mk->kapasitas)
            ->where('tipe_ruangan', 'kelas')
            ->first();
        
        if (!$ruanganMateri) {
            $ruanganMateri = $ruangans->where('kapasitas', '>=', $mk->kapasitas)->first();
        }
        
        if ($ruanganMateri && !empty($jamMateri)) {
            \Log::info('Creating materi jadwal for: ' . $mk->nama_mk);
            $this->createSingleJadwalWithSpecificDays($mk, $ruanganMateri, $hariMateri, $jamMateri, $jadwalGenerated, 'Materi', $prodi);
        } else {
            \Log::warning('Cannot create materi jadwal for: ' . $mk->nama_mk, [
                'ruangan_available' => $ruanganMateri ? 'yes' : 'no',
                'jam_available' => !empty($jamMateri) ? 'yes' : 'no'
            ]);
        }
        
        // 2. Buat jadwal PRAKTIKUM (harus di akhir minggu)
        $jamPraktikum = $this->filterJamByCustomSKS($preferensiJam, $mk->sks_praktikum, $mk->menit_per_sks);
        $ruanganPraktikum = $ruangans->where('kapasitas', '>=', $mk->kapasitas)
            ->where('tipe_ruangan', 'lab')
            ->first();
        
        if (!$ruanganPraktikum) {
            $ruanganPraktikum = $ruangans->where('kapasitas', '>=', $mk->kapasitas)->first();
        }
        
        if ($ruanganPraktikum && !empty($jamPraktikum)) {
            \Log::info('Creating praktikum jadwal for: ' . $mk->nama_mk);
            $this->createSingleJadwalWithSpecificDays($mk, $ruanganPraktikum, $hariPraktikum, $jamPraktikum, $jadwalGenerated, 'Praktikum', $prodi);
        } else {
            \Log::warning('Cannot create praktikum jadwal for: ' . $mk->nama_mk, [
                'ruangan_available' => $ruanganPraktikum ? 'yes' : 'no',
                'jam_available' => !empty($jamPraktikum) ? 'yes' : 'no'
            ]);
        }
    }
    
    private function createSingleJadwal($mk, $ruangan, $preferensiHari, $jamSesuaiSKS, &$jadwalGenerated, $tipeKelas = '', $prodi)
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
        \Log::info('=== ADMIN PRODI DETAILED PREFERENSI LOGGING ===');
        \Log::info('Dosen: ' . $mk->dosen->name . ' (ID: ' . $mk->dosen_id . ')');
        \Log::info('Mata Kuliah: ' . $mk->nama_mk . ' (ID: ' . $mk->id . ')');
        \Log::info('Preferensi Hari yang Diterima: ' . json_encode($preferensiHari));
        \Log::info('Jam Slots yang Tersedia: ' . json_encode($jamSesuaiSKS));
        \Log::info('Ruangan: ' . $ruangan->nama_ruangan . ' (ID: ' . $ruangan->id . ')');
        \Log::info('Tipe Kelas: ' . $tipeKelas);
        \Log::info('Prodi: ' . $prodi);
        
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
                    'prodi' => $prodi
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
                    'prodi' => $prodi
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
    
    private function createSingleJadwalWithSpecificDays($mk, $ruangan, $hariTersedia, $jamSesuaiSKS, &$jadwalGenerated, $tipeKelas = '', $prodi)
    {
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
                    'prodi' => $prodi
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
                    'prodi' => $prodi
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
            if (count($preferensiHari) > 0 && count($jamSesuaiSKS) > 0) {
                $reason .= ' - Semua kombinasi hari (' . implode(', ', $preferensiHari) . ') dan jam (' . implode(', ', $jamSesuaiSKS) . ') sudah terisi atau konflik';
            }
            
            \Log::warning('❌ JADWAL TIDAK BISA DIBUAT: ' . $mk->nama_mk . ' (' . $tipeKelas . ') - Dosen: ' . $mk->dosen->name . ' - Prodi: ' . $prodi . ' - Alasan: ' . $reason);
        }
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
    
    private function filterJamByCustomSKS($jamSlots, $sks, $menitPerSKS)
    {
        $totalMenit = $sks * $menitPerSKS;
        $filteredSlots = [];
        
        // Decode JSON string if needed
        if (is_string($jamSlots)) {
            $jamSlots = json_decode($jamSlots, true);
        }
        
        // Handle both old format (array of jam) and new format (object with jam per hari)
        $jamArray = [];
        if (is_array($jamSlots) && isset($jamSlots[0]) && is_string($jamSlots[0])) {
            // Old format: array of jam strings
            $jamArray = $jamSlots;
        } elseif (is_array($jamSlots) || is_object($jamSlots)) {
            // New format: object with jam per hari, collect all unique jam
            foreach ($jamSlots as $hari => $jamHari) {
                if (is_array($jamHari)) {
                    foreach ($jamHari as $jam) {
                        if (!in_array($jam, $jamArray)) {
                            $jamArray[] = $jam;
                        }
                    }
                }
            }
        }
        
        \Log::info('Filtering jam slots for SKS: ' . $sks . ', Menit per SKS: ' . $menitPerSKS . ', Total menit needed: ' . $totalMenit);
        \Log::info('Jam slots to filter: ' . json_encode($jamArray));
        
        // Cek slot individual (1 jam = 60 menit)
        foreach ($jamArray as $slot) {
            $duration = 60; // 1 jam = 60 menit
            if ($duration >= $totalMenit) {
                $filteredSlots[] = $slot;
                \Log::info('✅ Individual jam slot ' . $slot . ' passed filter (Duration: ' . $duration . ' min, Required: ' . $totalMenit . ' min)');
            } else {
                \Log::info('❌ Individual jam slot ' . $slot . ' failed filter (Duration: ' . $duration . ' min, Required: ' . $totalMenit . ' min)');
            }
        }
        
        // If no individual slots work, try combining consecutive 1-hour slots
        if (empty($filteredSlots)) {
            \Log::info('No individual slots work, trying to combine consecutive 1-hour slots...');
            
            // Sort slots by start time
            $sortedSlots = array_filter($jamArray, function($slot) {
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
                        
                        if ($totalDuration >= $totalMenit) {
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
                            
                            if ($totalDuration >= $totalMenit) {
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
    
    private function generateJamSlots()
    {
        $jamSlots = [];
        $startHour = 8;
        $endHour = 17;
        
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $jamMulai = sprintf('%02d:00', $hour);
            $jamSelesai = sprintf('%02d:00', $hour + 1);
            $jamSlots[] = $jamMulai . '-' . $jamSelesai;
        }
        
        return $jamSlots;
    }

    // Jadwal untuk Admin Prodi
    public function jadwal(Request $request)
    {
        $admin = Auth::user();
        $prodi = $admin->prodi;
        
        \Log::info('Admin Prodi Jadwal filter request:', [
            'admin_id' => $admin->id,
            'admin_name' => $admin->name,
            'prodi' => $prodi,
            'all_params' => $request->all()
        ]);
        
        $query = Jadwal::with('mataKuliah.dosen', 'ruangan')
            ->where('prodi', $prodi);
        
        $jadwals = $query->get();
        
        \Log::info('Admin Prodi Jadwal query result:', [
            'total_jadwals' => $jadwals->count(),
            'prodi' => $prodi,
            'jadwals_prodi' => $jadwals->pluck('prodi')->unique()->toArray()
        ]);
        
        // Get data for edit form - hanya untuk prodi ini
        $mataKuliahs = MataKuliah::where('prodi', $prodi)->with('dosen')->get();
        $ruangans = Ruangan::where('status', true)->where('prodi', $prodi)->get();
        $hariOptions = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        return view('admin-prodi.jadwal', compact('jadwals', 'prodi', 'mataKuliahs', 'ruangans', 'hariOptions'));
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