<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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

class PopulateAllDataWarehouse extends Command
{
    protected $signature = 'dw:populate-all {--fresh : Clear existing data first}';
    protected $description = 'Populate all dimension and fact tables from operational data';

    public function handle()
    {
        $this->info('🚀 Starting Data Warehouse Population...');
        $this->newLine();

        if ($this->option('fresh')) {
            $this->warn('🗑️  Clearing existing data warehouse tables...');
            $this->clearAllTables();
        }

        // Step 1: Populate Dimension Tables
        $this->info('📊 Step 1: Populating Dimension Tables...');
        $this->populateDimDosen();
        $this->populateDimMataKuliah();
        $this->populateDimRuangan();
        $this->populateDimWaktu();
        $this->populateDimProdi();
        $this->populateDimPreferensi();
        $this->newLine();

        // Step 2: Populate Fact Tables
        $this->info('📈 Step 2: Populating Fact Tables...');
        $this->populateFactJadwal();
        $this->populateFactUtilisasiRuangan();
        $this->populateFactKecocokanJadwal();
        $this->newLine();

        $this->info('✅ Data Warehouse populated successfully!');
        $this->displaySummary();
    }

    private function clearAllTables()
    {
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
    }

    // ========== DIMENSION TABLES ==========

    private function populateDimDosen()
    {
        $this->info('  → Populating DimDosen...');
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
        $this->info('    ✅ DimDosen: ' . DimDosen::count() . ' records');
    }

    private function populateDimMataKuliah()
    {
        $this->info('  → Populating DimMataKuliah...');
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
        $this->info('    ✅ DimMataKuliah: ' . DimMataKuliah::count() . ' records');
    }

    private function populateDimRuangan()
    {
        $this->info('  → Populating DimRuangan...');
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
                    'status' => $ruangan->status ?? true,
                    'is_active' => true,
                    'valid_from' => $ruangan->created_at ?? now(),
                    'valid_to' => null,
                ]
            );
        }
        $this->info('    ✅ DimRuangan: ' . DimRuangan::count() . ' records');
    }

    private function populateDimWaktu()
    {
        $this->info('  → Populating DimWaktu...');
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
            
            $hariKe = $this->getHariKe($jadwal->hari);
            $slotWaktu = $this->getSlotWaktu($jamMulai);
            $durasiMenit = $this->calculateDuration($jamMulai, $jamSelesai);
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
        $this->info('    ✅ DimWaktu: ' . DimWaktu::count() . ' records');
    }

    private function populateDimProdi()
    {
        $this->info('  → Populating DimProdi...');
        
        // Get unique prodi from multiple sources
        $prodiList = collect();
        $prodiList = $prodiList->merge(User::whereNotNull('prodi')->distinct()->pluck('prodi'));
        $prodiList = $prodiList->merge(MataKuliah::whereNotNull('prodi')->distinct()->pluck('prodi'));
        $prodiList = $prodiList->merge(Jadwal::whereNotNull('prodi')->distinct()->pluck('prodi'));
        $prodiList = $prodiList->unique()->filter();
        
        foreach ($prodiList as $prodi) {
            $kodeProdi = strtoupper(substr(str_replace(' ', '', $prodi), 0, 10));
            $fakultas = $this->getFakultas($prodi);
            
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
        $this->info('    ✅ DimProdi: ' . DimProdi::count() . ' records');
    }

    private function populateDimPreferensi()
    {
        $this->info('  → Populating DimPreferensi...');
        $preferensis = PreferensiDosen::whereNotNull('mata_kuliah_id')->get();
        
        foreach ($preferensis as $pref) {
            DimPreferensi::updateOrCreate(
                [
                    'dosen_id' => $pref->dosen_id,
                    'mata_kuliah_id' => $pref->mata_kuliah_id,
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
        $this->info('    ✅ DimPreferensi: ' . DimPreferensi::count() . ' records');
    }

    // ========== FACT TABLES ==========

    private function populateFactJadwal()
    {
        $this->info('  → Populating FactJadwal...');
        $jadwals = Jadwal::with(['mataKuliah.dosen', 'ruangan'])
            ->where('status', true)
            ->get();

        $bar = $this->output->createProgressBar($jadwals->count());
        $bar->start();

        $success = 0;
        $failed = 0;

        foreach ($jadwals as $jadwal) {
            try {
                $dosen = $jadwal->mataKuliah->dosen ?? null;
                if (!$dosen) {
                    $failed++;
                    $bar->advance();
                    continue;
                }

                // Get dimension keys
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
                    $bar->advance();
                    continue;
                }

                // Find preferensi_key if exists
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
                $durasiMenit = $this->calculateDuration($jamMulai, $jamSelesai);
                $kapasitasKelas = $jadwal->mataKuliah->kapasitas ?? 0;
                $jumlahMahasiswa = $kapasitasKelas;
                
                // Utilisasi ruangan: (jumlah_mahasiswa / kapasitas_ruangan) * 100
                $kapasitasRuangan = $jadwal->ruangan->kapasitas ?? 0;
                $utilisasiRuangan = $kapasitasRuangan > 0 
                    ? ($jumlahMahasiswa / $kapasitasRuangan) * 100 
                    : 0;
                
                $prioritasPreferensi = $preferensi ? $preferensi->prioritas : null;
                
                // Check for conflicts
                $konflik = $this->checkConflict($jadwal);
                $tingkatKonflik = $konflik ? $this->calculateConflictLevel($jadwal) : 0;

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

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('    ✅ FactJadwal: ' . FactJadwal::count() . ' records (Success: ' . $success . ', Failed: ' . $failed . ')');
    }

    private function populateFactUtilisasiRuangan()
    {
        $this->info('  → Populating FactUtilisasiRuangan...');
        
        // Aggregate data from FactJadwal
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

        $bar = $this->output->createProgressBar($utilisasiData->count());
        $bar->start();

        foreach ($utilisasiData as $data) {
            try {
                $waktu = DimWaktu::where('waktu_key', $data->waktu_key)->first();
                
                // Total jam tersedia: 8 jam = 480 menit per hari
                $totalJamTersedia = 480;
                
                // Persentase utilisasi: (total_jam_penggunaan / total_jam_tersedia) * 100
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
                // Skip error
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('    ✅ FactUtilisasiRuangan: ' . FactUtilisasiRuangan::count() . ' records');
    }

    private function populateFactKecocokanJadwal()
    {
        $this->info('  → Populating FactKecocokanJadwal...');
        $jadwals = Jadwal::with(['mataKuliah.dosen', 'ruangan'])
            ->where('status', true)
            ->get();

        $bar = $this->output->createProgressBar($jadwals->count());
        $bar->start();

        $success = 0;
        $skipped = 0;

        foreach ($jadwals as $jadwal) {
            try {
                $dosen = $jadwal->mataKuliah->dosen ?? null;
                if (!$dosen) {
                    $skipped++;
                    $bar->advance();
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
                    $bar->advance();
                    continue;
                }

                // Find preferensi
                $preferensi = PreferensiDosen::where('dosen_id', $dosen->id)
                    ->where('mata_kuliah_id', $jadwal->mata_kuliah_id)
                    ->first();

                if (!$preferensi) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $preferensiKey = DimPreferensi::where('dosen_id', $dosen->id)
                    ->where('mata_kuliah_id', $jadwal->mata_kuliah_id)
                    ->first()?->preferensi_key;

                if (!$preferensiKey) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Check if preferences are met - LANGSUNG DARI DATA
                $preferensiHari = is_array($preferensi->preferensi_hari) 
                    ? $preferensi->preferensi_hari 
                    : json_decode($preferensi->preferensi_hari, true) ?? [];
                
                $preferensiJam = is_array($preferensi->preferensi_jam) 
                    ? $preferensi->preferensi_jam 
                    : json_decode($preferensi->preferensi_jam, true) ?? [];

                // Handle object format
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

                // Calculate scores - LANGSUNG DARI DATA
                $jumlahPreferensiTotal = count($preferensiHari) + count($preferensiJam);
                $jumlahPreferensiTerpenuhi = ($hariTerpenuhi ? 1 : 0) + ($jamTerpenuhi ? 1 : 0);
                $persentaseKecocokan = $jumlahPreferensiTotal > 0 
                    ? ($jumlahPreferensiTerpenuhi / $jumlahPreferensiTotal) * 100 
                    : 0;
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

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('    ✅ FactKecocokanJadwal: ' . FactKecocokanJadwal::count() . ' records (Success: ' . $success . ', Skipped: ' . $skipped . ')');
    }

    // ========== HELPER METHODS ==========

    private function calculateDuration($jamMulai, $jamSelesai)
    {
        if (is_string($jamMulai)) {
            $start = strtotime($jamMulai);
        } else {
            $start = $jamMulai->getTimestamp();
        }
        
        if (is_string($jamSelesai)) {
            $end = strtotime($jamSelesai);
        } else {
            $end = $jamSelesai->getTimestamp();
        }
        
        return (int) (($end - $start) / 60);
    }

    private function checkConflict($jadwal)
    {
        $jamMulai = is_object($jadwal->jam_mulai) 
            ? $jadwal->jam_mulai->format('H:i:s') 
            : $jadwal->jam_mulai;
        $jamSelesai = is_object($jadwal->jam_selesai) 
            ? $jadwal->jam_selesai->format('H:i:s') 
            : $jadwal->jam_selesai;

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

        return $ruanganConflict || $dosenConflict;
    }

    private function calculateConflictLevel($jadwal)
    {
        if (!$this->checkConflict($jadwal)) {
            return 0;
        }

        $jamMulai = is_object($jadwal->jam_mulai) 
            ? $jadwal->jam_mulai->format('H:i:s') 
            : $jadwal->jam_mulai;
        $jamSelesai = is_object($jadwal->jam_selesai) 
            ? $jadwal->jam_selesai->format('H:i:s') 
            : $jadwal->jam_selesai;

        $conflictCount = 0;
        
        $dosenConflicts = Jadwal::where('id', '!=', $jadwal->id)
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

        $ruanganConflicts = Jadwal::where('id', '!=', $jadwal->id)
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

    private function getHariKe($hari)
    {
        $map = [
            'Senin' => 1,
            'Selasa' => 2,
            'Rabu' => 3,
            'Kamis' => 4,
            'Jumat' => 5,
        ];
        return $map[$hari] ?? 0;
    }

    private function getSlotWaktu($jamMulai)
    {
        $hour = (int) explode(':', $jamMulai)[0];
        if ($hour < 12) return 'Pagi';
        if ($hour < 15) return 'Siang';
        return 'Sore';
    }

    private function getFakultas($prodi)
    {
        if (stripos($prodi, 'Teknik') !== false) return 'Fakultas Teknik';
        if (stripos($prodi, 'Ekonomi') !== false) return 'Fakultas Ekonomi';
        if (stripos($prodi, 'Hukum') !== false) return 'Fakultas Hukum';
        return 'Fakultas Lainnya';
    }

    private function displaySummary()
    {
        $this->newLine();
        $this->info('📊 Data Warehouse Summary:');
        $this->table(
            ['Table', 'Count'],
            [
                ['DimDosen', DimDosen::count()],
                ['DimMataKuliah', DimMataKuliah::count()],
                ['DimRuangan', DimRuangan::count()],
                ['DimWaktu', DimWaktu::count()],
                ['DimProdi', DimProdi::count()],
                ['DimPreferensi', DimPreferensi::count()],
                ['FactJadwal', FactJadwal::count()],
                ['FactUtilisasiRuangan', FactUtilisasiRuangan::count()],
                ['FactKecocokanJadwal', FactKecocokanJadwal::count()],
            ]
        );
    }
}

