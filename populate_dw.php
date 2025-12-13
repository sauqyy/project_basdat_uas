<?php

/**
 * Script untuk Populate Data Warehouse
 * Jalankan: php populate_dw.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Jadwal;
use App\Models\MataKuliah;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\PreferensiDosen;
use App\Models\DimDosen;
use App\Models\DimMataKuliah;
use App\Models\DimRuangan;
use App\Models\DimWaktu;
use App\Models\DimProdi;
use App\Models\DimPreferensi;
use App\Models\FactJadwal;
use App\Models\FactUtilisasiRuangan;
use App\Models\FactKecocokanJadwal;

// Helper functions
function checkConflict($jadwal) {
    $jamMulai = is_object($jadwal->jam_mulai) 
        ? $jadwal->jam_mulai->format('H:i:s') 
        : $jadwal->jam_mulai;
    $jamSelesai = is_object($jadwal->jam_selesai) 
        ? $jadwal->jam_selesai->format('H:i:s') 
        : $jadwal->jam_selesai;

    $ruanganConflict = \App\Models\Jadwal::where('id', '!=', $jadwal->id)
        ->where('ruangan_id', $jadwal->ruangan_id)
        ->where('hari', $jadwal->hari)
        ->where('status', true)
        ->where(function($query) use ($jamMulai, $jamSelesai) {
            $query->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai);
        })
        ->exists();

    $dosenConflict = false;
    if ($jadwal->mataKuliah && $jadwal->mataKuliah->dosen_id) {
        $dosenConflict = \App\Models\Jadwal::where('id', '!=', $jadwal->id)
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

    return $ruanganConflict || $dosenConflict;
}

function calculateConflictLevel($jadwal) {
    if (!checkConflict($jadwal)) {
        return 0;
    }

    $jamMulai = is_object($jadwal->jam_mulai) 
        ? $jadwal->jam_mulai->format('H:i:s') 
        : $jadwal->jam_mulai;
    $jamSelesai = is_object($jadwal->jam_selesai) 
        ? $jadwal->jam_selesai->format('H:i:s') 
        : $jadwal->jam_selesai;

    $conflictCount = 0;
    
    $dosenConflicts = \App\Models\Jadwal::where('id', '!=', $jadwal->id)
        ->whereHas('mataKuliah', function($q) use ($jadwal) {
            $q->where('dosen_id', $jadwal->mataKuliah->dosen_id);
        })
        ->where('hari', $jadwal->hari)
        ->where('status', true)
        ->where(function($query) use ($jamMulai, $jamSelesai) {
            $query->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai);
        })
        ->count();
    
    $conflictCount += $dosenConflicts;

    $ruanganConflicts = \App\Models\Jadwal::where('id', '!=', $jadwal->id)
        ->where('ruangan_id', $jadwal->ruangan_id)
        ->where('hari', $jadwal->hari)
        ->where('status', true)
        ->where(function($query) use ($jamMulai, $jamSelesai) {
            $query->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai);
        })
        ->count();
    
    $conflictCount += $ruanganConflicts;

    return min(5, $conflictCount);
}

echo "🚀 Starting Data Warehouse Population...\n\n";

// Clear existing data
echo "🗑️  Clearing existing data warehouse tables...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0');
FactKecocokanJadwal::truncate();
FactUtilisasiRuangan::truncate();
FactJadwal::truncate();
DimPreferensi::truncate();
DimProdi::truncate();
DimWaktu::truncate();
DimRuangan::truncate();
DimMataKuliah::truncate();
DimDosen::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1');
echo "✅ Cleared\n\n";

// ========== DIMENSION TABLES ==========

echo "📊 Step 1: Populating Dimension Tables...\n";

// DimDosen
echo "  → DimDosen...\n";
$users = User::whereIn('role', ['dosen', 'admin_prodi'])->get();
foreach ($users as $user) {
    DimDosen::updateOrCreate(
        ['nip' => $user->nip ?? $user->id],
        [
            'dosen_key' => 'DOSEN_' . ($user->nip ?? $user->id),
            'nama_dosen' => $user->name,
            'email' => $user->email,
            'prodi' => $user->prodi,
            'role' => $user->role,
            'profile_picture' => $user->profile_picture,
            'judul_skripsi' => $user->judul_skripsi,
            'is_active' => true,
            'valid_from' => $user->created_at ?? now(),
            'valid_to' => null,
        ]
    );
}
echo "    ✅ DimDosen: " . DimDosen::count() . " records\n";

// DimMataKuliah
echo "  → DimMataKuliah...\n";
$mataKuliahs = MataKuliah::all();
foreach ($mataKuliahs as $mk) {
    DimMataKuliah::updateOrCreate(
        ['kode_mk' => $mk->kode_mk],
        [
            'mata_kuliah_key' => 'MK_' . $mk->kode_mk,
            'nama_mk' => $mk->nama_mk,
            'sks' => $mk->sks,
            'semester' => $mk->semester,
            'prodi' => $mk->prodi,
            'kapasitas' => $mk->kapasitas,
            'deskripsi' => $mk->deskripsi,
            'tipe_kelas' => $mk->tipe_kelas,
            'menit_per_sks' => $mk->menit_per_sks,
            'ada_praktikum' => $mk->ada_praktikum ?? false,
            'sks_praktikum' => $mk->sks_praktikum ?? 0,
            'sks_materi' => $mk->sks_materi ?? 0,
            'is_active' => true,
            'valid_from' => $mk->created_at ?? now(),
            'valid_to' => null,
        ]
    );
}
echo "    ✅ DimMataKuliah: " . DimMataKuliah::count() . " records\n";

// DimRuangan
echo "  → DimRuangan...\n";
$ruangans = Ruangan::all();
foreach ($ruangans as $ruangan) {
    DimRuangan::updateOrCreate(
        ['kode_ruangan' => $ruangan->kode_ruangan],
        [
            'ruangan_key' => 'R_' . $ruangan->kode_ruangan,
            'nama_ruangan' => $ruangan->nama_ruangan,
            'kapasitas' => $ruangan->kapasitas,
            'tipe_ruangan' => $ruangan->tipe_ruangan,
            'fasilitas' => $ruangan->fasilitas,
            'prodi' => $ruangan->prodi ?? 'Umum',
            'status' => $ruangan->status ?? true, // Note: adjust based on your schema
            'is_active' => true,
            'valid_from' => $ruangan->created_at ?? now(),
            'valid_to' => null,
        ]
    );
}
echo "    ✅ DimRuangan: " . DimRuangan::count() . " records\n";

// DimWaktu
echo "  → DimWaktu...\n";
$jadwals = Jadwal::where('status', true)
    ->select('hari', 'jam_mulai', 'jam_selesai', 'semester', 'tahun_akademik')
    ->distinct()
    ->get();

foreach ($jadwals as $jadwal) {
    $jamMulai = is_object($jadwal->jam_mulai) 
        ? $jadwal->jam_mulai->format('H:i') 
        : $jadwal->jam_mulai;
    $jamSelesai = is_object($jadwal->jam_selesai) 
        ? $jadwal->jam_selesai->format('H:i') 
        : $jadwal->jam_selesai;
    
    $waktuKey = $jadwal->hari . '_' . str_replace(':', '', $jamMulai) . '_' . str_replace(':', '', $jamSelesai) . '_' . $jadwal->semester . '_' . $jadwal->tahun_akademik;
    
    $hariKe = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5][$jadwal->hari] ?? 0;
    $hour = (int) explode(':', $jamMulai)[0];
    $slotWaktu = $hour < 12 ? 'Pagi' : ($hour < 15 ? 'Siang' : 'Sore');
    
    $start = strtotime($jamMulai);
    $end = strtotime($jamSelesai);
    $durasiMenit = (int) (($end - $start) / 60);
    $periode = (intval($jadwal->semester) % 2 == 1) ? 'Ganjil' : 'Genap';
    
    DimWaktu::updateOrCreate(
        [
            'hari' => $jadwal->hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'semester' => $jadwal->semester,
            'tahun_akademik' => $jadwal->tahun_akademik,
        ],
        [
            'waktu_key' => $waktuKey,
            'hari_ke' => $hariKe,
            'slot_waktu' => $slotWaktu,
            'durasi_menit' => $durasiMenit,
            'periode' => $periode,
            'is_active' => true,
        ]
    );
}
echo "    ✅ DimWaktu: " . DimWaktu::count() . " records\n";

// DimProdi
echo "  → DimProdi...\n";
$prodiList = collect();
$prodiList = $prodiList->merge(User::whereNotNull('prodi')->distinct()->pluck('prodi'));
$prodiList = $prodiList->merge(MataKuliah::whereNotNull('prodi')->distinct()->pluck('prodi'));
$prodiList = $prodiList->merge(Jadwal::whereNotNull('prodi')->distinct()->pluck('prodi'));
$prodiList = $prodiList->unique()->filter();

foreach ($prodiList as $prodi) {
    $kodeProdi = strtoupper(substr(str_replace(' ', '', $prodi), 0, 10));
    $fakultas = stripos($prodi, 'Teknik') !== false ? 'Fakultas Teknik' : 'Fakultas Lainnya';
    
    DimProdi::updateOrCreate(
        ['nama_prodi' => $prodi],
        [
            'prodi_key' => 'PRODI_' . $kodeProdi,
            'kode_prodi' => $kodeProdi,
            'fakultas' => $fakultas,
            'is_active' => true,
            'valid_from' => now(),
            'valid_to' => null,
        ]
    );
}
echo "    ✅ DimProdi: " . DimProdi::count() . " records\n";

// DimPreferensi
echo "  → DimPreferensi...\n";
$preferensis = PreferensiDosen::all();
foreach ($preferensis as $pref) {
    // Support preferensi global (mata_kuliah_id null) dan preferensi spesifik
    DimPreferensi::updateOrCreate(
        [
            'dosen_id' => $pref->dosen_id,
            'mata_kuliah_id' => $pref->mata_kuliah_id, // Bisa null untuk preferensi global
        ],
        [
            'preferensi_key' => 'PREF_' . $pref->id,
            'preferensi_hari' => is_string($pref->preferensi_hari) 
                ? $pref->preferensi_hari 
                : json_encode($pref->preferensi_hari),
            'preferensi_jam' => is_string($pref->preferensi_jam) 
                ? $pref->preferensi_jam 
                : json_encode($pref->preferensi_jam),
            'prioritas' => $pref->prioritas,
            'catatan' => $pref->catatan,
            'is_active' => true,
            'valid_from' => $pref->created_at ?? now(),
            'valid_to' => null,
        ]
    );
}
echo "    ✅ DimPreferensi: " . DimPreferensi::count() . " records\n";

echo "\n";

// ========== FACT TABLES ==========

echo "📈 Step 2: Populating Fact Tables...\n";

// FactJadwal
echo "  → FactJadwal...\n";
$jadwals = Jadwal::with(['mataKuliah.dosen', 'ruangan'])
    ->where('status', true)
    ->get();

$success = 0;
$failed = 0;

foreach ($jadwals as $jadwal) {
    try {
        $dosen = $jadwal->mataKuliah->dosen ?? null;
        if (!$dosen) {
            $failed++;
            continue;
        }

        $dosenKey = DimDosen::where('nip', $dosen->nip ?? $dosen->id)->first()?->dosen_key;
        $mkKey = DimMataKuliah::where('kode_mk', $jadwal->mataKuliah->kode_mk)->first()?->mata_kuliah_key;
        $ruanganKey = DimRuangan::where('kode_ruangan', $jadwal->ruangan->kode_ruangan)->first()?->ruangan_key;
        
        $jamMulai = is_object($jadwal->jam_mulai) 
            ? $jadwal->jam_mulai->format('H:i') 
            : $jadwal->jam_mulai;
        $jamSelesai = is_object($jadwal->jam_selesai) 
            ? $jadwal->jam_selesai->format('H:i') 
            : $jadwal->jam_selesai;
        
        $waktuKey = DimWaktu::where('hari', $jadwal->hari)
            ->where('jam_mulai', $jamMulai)
            ->where('jam_selesai', $jamSelesai)
            ->first()?->waktu_key;

        $prodiKey = DimProdi::where('nama_prodi', $jadwal->prodi)
            ->orWhere('nama_prodi', 'like', '%' . $jadwal->prodi . '%')
            ->first()?->prodi_key;

        if (!$dosenKey || !$mkKey || !$ruanganKey || !$waktuKey || !$prodiKey) {
            $failed++;
            continue;
        }

        $preferensi = PreferensiDosen::where('dosen_id', $dosen->id)
            ->where('mata_kuliah_id', $jadwal->mata_kuliah_id)
            ->first();
        
        $preferensiKey = null;
        if ($preferensi) {
            $preferensiKey = DimPreferensi::where('dosen_id', $dosen->id)
                ->where('mata_kuliah_id', $jadwal->mata_kuliah_id)
                ->first()?->preferensi_key;
        }

        // Calculate measures - LANGSUNG DARI DATA OPERASIONAL
        $jumlahSks = $jadwal->mataKuliah->sks ?? 0;
        $start = strtotime($jamMulai);
        $end = strtotime($jamSelesai);
        $durasiMenit = (int) (($end - $start) / 60);
        $kapasitasKelas = $jadwal->mataKuliah->kapasitas ?? 0;
        $jumlahMahasiswa = $kapasitasKelas;
        
        $kapasitasRuangan = $jadwal->ruangan->kapasitas ?? 0;
        $utilisasiRuangan = $kapasitasRuangan > 0 
            ? ($jumlahMahasiswa / $kapasitasRuangan) * 100 
            : 0;
        
        $prioritasPreferensi = $preferensi ? $preferensi->prioritas : null;
        
        // Check conflicts
        $konflik = checkConflict($jadwal);
        $tingkatKonflik = $konflik ? calculateConflictLevel($jadwal) : 0;

        FactJadwal::updateOrCreate(
            [
                'dosen_key' => $dosenKey,
                'mata_kuliah_key' => $mkKey,
                'ruangan_key' => $ruanganKey,
                'waktu_key' => $waktuKey,
                'prodi_key' => $prodiKey,
            ],
            [
                'preferensi_key' => $preferensiKey,
                'jumlah_sks' => $jumlahSks,
                'durasi_menit' => $durasiMenit,
                'kapasitas_kelas' => $kapasitasKelas,
                'jumlah_mahasiswa' => $jumlahMahasiswa,
                'utilisasi_ruangan' => round($utilisasiRuangan, 2),
                'prioritas_preferensi' => $prioritasPreferensi,
                'konflik_jadwal' => $konflik,
                'tingkat_konflik' => $tingkatKonflik,
                'status_aktif' => true,
                'created_at_jadwal' => $jadwal->created_at ?? now(),
                'updated_at_jadwal' => $jadwal->updated_at ?? now(),
            ]
        );
        $success++;
    } catch (\Exception $e) {
        $failed++;
    }
}

echo "    ✅ FactJadwal: " . FactJadwal::count() . " records (Success: $success, Failed: $failed)\n";

// FactUtilisasiRuangan
echo "  → FactUtilisasiRuangan...\n";
$utilisasiData = FactJadwal::select(
    'ruangan_key',
    'waktu_key',
    'prodi_key',
    DB::raw('SUM(durasi_menit) as total_menit'),
    DB::raw('COUNT(*) as jumlah_kelas'),
    DB::raw('SUM(jumlah_mahasiswa) as total_mahasiswa'),
    DB::raw('AVG(utilisasi_ruangan) as avg_utilisasi'),
    DB::raw('MAX(utilisasi_ruangan) as peak_utilisasi')
)
->where('status_aktif', true)
->groupBy('ruangan_key', 'waktu_key', 'prodi_key')
->get();

foreach ($utilisasiData as $data) {
    try {
        $waktu = DimWaktu::where('waktu_key', $data->waktu_key)->first();
        $totalJamTersedia = 480; // 8 jam
        $persentaseUtilisasi = $totalJamTersedia > 0 
            ? ($data->total_menit / $totalJamTersedia) * 100 
            : 0;

        FactUtilisasiRuangan::updateOrCreate(
            [
                'ruangan_key' => $data->ruangan_key,
                'waktu_key' => $data->waktu_key,
                'prodi_key' => $data->prodi_key,
            ],
            [
                'total_jam_penggunaan' => $data->total_menit,
                'total_jam_tersedia' => $totalJamTersedia,
                'persentase_utilisasi' => round($persentaseUtilisasi, 2),
                'jumlah_kelas' => $data->jumlah_kelas,
                'jumlah_mahasiswa_total' => $data->total_mahasiswa,
                'rata_rata_kapasitas' => round($data->avg_utilisasi ?? 0, 2),
                'peak_hour_utilisasi' => round($data->peak_utilisasi ?? 0, 2),
                'periode_semester' => $waktu->semester ?? null,
                'tahun_akademik' => $waktu->tahun_akademik ?? null,
            ]
        );
    } catch (\Exception $e) {
        // Skip
    }
}
echo "    ✅ FactUtilisasiRuangan: " . FactUtilisasiRuangan::count() . " records\n";

// FactKecocokanJadwal
echo "  → FactKecocokanJadwal...\n";
$jadwals = Jadwal::with(['mataKuliah.dosen', 'ruangan'])
    ->where('status', true)
    ->get();

$success = 0;
$skipped = 0;

foreach ($jadwals as $jadwal) {
    try {
        $dosen = $jadwal->mataKuliah->dosen ?? null;
        if (!$dosen) {
            $skipped++;
            continue;
        }

        $dosenKey = DimDosen::where('nip', $dosen->nip ?? $dosen->id)->first()?->dosen_key;
        
        $jamMulai = is_object($jadwal->jam_mulai) 
            ? $jadwal->jam_mulai->format('H:i') 
            : $jadwal->jam_mulai;
        $jamSelesai = is_object($jadwal->jam_selesai) 
            ? $jadwal->jam_selesai->format('H:i') 
            : $jadwal->jam_selesai;
        
        $waktuKey = DimWaktu::where('hari', $jadwal->hari)
            ->where('jam_mulai', $jamMulai)
            ->where('jam_selesai', $jamSelesai)
            ->first()?->waktu_key;

        if (!$dosenKey || !$waktuKey) {
            $skipped++;
            continue;
        }

        // Cari preferensi spesifik untuk mata kuliah ini, jika tidak ada gunakan preferensi global
        $preferensi = PreferensiDosen::where('dosen_id', $dosen->id)
            ->where('mata_kuliah_id', $jadwal->mata_kuliah_id)
            ->first();
        
        // Jika tidak ada preferensi spesifik, gunakan preferensi global
        if (!$preferensi) {
            $preferensi = PreferensiDosen::where('dosen_id', $dosen->id)
                ->whereNull('mata_kuliah_id')
                ->first();
        }

        if (!$preferensi) {
            $skipped++;
            continue;
        }

        // Cari preferensi_key - untuk preferensi spesifik atau global
        $preferensiKey = null;
        if ($preferensi->mata_kuliah_id) {
            // Preferensi spesifik
            $preferensiKey = DimPreferensi::where('dosen_id', $dosen->id)
                ->where('mata_kuliah_id', $jadwal->mata_kuliah_id)
                ->first()?->preferensi_key;
        } else {
            // Preferensi global - cari atau buat preferensi_key untuk global
            $dimPreferensi = DimPreferensi::where('dosen_id', $dosen->id)
                ->whereNull('mata_kuliah_id')
                ->first();
            
            if (!$dimPreferensi) {
                // Buat dim_preferensi untuk preferensi global jika belum ada
                $dimPreferensi = DimPreferensi::create([
                    'preferensi_key' => 'PREF_GLOBAL_' . $dosen->id,
                    'dosen_id' => $dosen->id,
                    'mata_kuliah_id' => null,
                    'preferensi_hari' => is_string($preferensi->preferensi_hari) 
                        ? $preferensi->preferensi_hari 
                        : json_encode($preferensi->preferensi_hari),
                    'preferensi_jam' => is_string($preferensi->preferensi_jam) 
                        ? $preferensi->preferensi_jam 
                        : json_encode($preferensi->preferensi_jam),
                    'prioritas' => $preferensi->prioritas,
                    'catatan' => $preferensi->catatan ?? 'Preferensi global',
                    'is_active' => true,
                    'valid_from' => $preferensi->created_at ?? now(),
                    'valid_to' => null,
                ]);
            }
            $preferensiKey = $dimPreferensi->preferensi_key;
        }

        if (!$preferensiKey) {
            $skipped++;
            continue;
        }

        // Check preferences - LANGSUNG DARI DATA
        $preferensiHari = is_array($preferensi->preferensi_hari) 
            ? $preferensi->preferensi_hari 
            : json_decode($preferensi->preferensi_hari, true) ?? [];
        
        $preferensiJam = is_array($preferensi->preferensi_jam) 
            ? $preferensi->preferensi_jam 
            : json_decode($preferensi->preferensi_jam, true) ?? [];

        if (is_array($preferensiJam) && !isset($preferensiJam[0]) && !empty($preferensiJam)) {
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

        $hariTerpenuhi = in_array($jadwal->hari, $preferensiHari);
        
        $jamTerpenuhi = false;
        foreach ($preferensiJam as $jam) {
            if (is_string($jam) && (strpos($jam, $jamMulai) !== false || strpos($jam, $jamSelesai) !== false)) {
                $jamTerpenuhi = true;
                break;
            }
        }

        // Calculate - LOGIKA BARU: Jika jadwal sesuai preferensi (hari DAN jam) = 100%, jika tidak = 0%
        // Jika seluruh jadwal sesuai preferensi → 100%, jika ada yang di luar → berkurang
        if ($hariTerpenuhi && $jamTerpenuhi) {
            // Jadwal ini 100% sesuai preferensi
            $persentaseKecocokan = 100;
        } else {
            // Jadwal ini tidak sesuai preferensi
            $persentaseKecocokan = 0;
        }
        
        $jumlahPreferensiTotal = count($preferensiHari) + count($preferensiJam);
        $jumlahPreferensiTerpenuhi = ($hariTerpenuhi ? 1 : 0) + ($jamTerpenuhi ? 1 : 0);
        $skorKecocokan = (int) $persentaseKecocokan;

        $waktu = DimWaktu::where('waktu_key', $waktuKey)->first();

        FactKecocokanJadwal::updateOrCreate(
            [
                'dosen_key' => $dosenKey,
                'preferensi_key' => $preferensiKey,
                'waktu_key' => $waktuKey,
            ],
            [
                'preferensi_hari_terpenuhi' => $hariTerpenuhi,
                'preferensi_jam_terpenuhi' => $jamTerpenuhi,
                'skor_kecocokan' => $skorKecocokan,
                'prioritas_preferensi' => $preferensi->prioritas,
                'jumlah_preferensi_total' => $jumlahPreferensiTotal,
                'jumlah_preferensi_terpenuhi' => $jumlahPreferensiTerpenuhi,
                'persentase_kecocokan' => round($persentaseKecocokan, 2),
                'catatan_kecocokan' => $hariTerpenuhi && $jamTerpenuhi 
                    ? 'Preferensi terpenuhi' 
                    : 'Preferensi tidak sepenuhnya terpenuhi',
                'semester' => $waktu->semester ?? null,
                'tahun_akademik' => $waktu->tahun_akademik ?? null,
            ]
        );
        $success++;
    } catch (\Exception $e) {
        $skipped++;
    }
}

echo "    ✅ FactKecocokanJadwal: " . FactKecocokanJadwal::count() . " records (Success: $success, Skipped: $skipped)\n";

echo "\n";
echo "✅ Data Warehouse populated successfully!\n\n";

// Summary
echo "📊 Summary:\n";
echo "  DimDosen: " . DimDosen::count() . "\n";
echo "  DimMataKuliah: " . DimMataKuliah::count() . "\n";
echo "  DimRuangan: " . DimRuangan::count() . "\n";
echo "  DimWaktu: " . DimWaktu::count() . "\n";
echo "  DimProdi: " . DimProdi::count() . "\n";
echo "  DimPreferensi: " . DimPreferensi::count() . "\n";
echo "  FactJadwal: " . FactJadwal::count() . "\n";
echo "  FactUtilisasiRuangan: " . FactUtilisasiRuangan::count() . "\n";
echo "  FactKecocokanJadwal: " . FactKecocokanJadwal::count() . "\n";


