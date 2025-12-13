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

class PopulateFactTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fact:populate {--fresh : Clear existing data first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate fact tables (FactJadwal, FactUtilisasiRuangan, FactKecocokanJadwal) from operational data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to populate fact tables...');

        if ($this->option('fresh')) {
            $this->warn('Clearing existing fact tables...');
            FactJadwal::truncate();
            FactUtilisasiRuangan::truncate();
            FactKecocokanJadwal::truncate();
        }

        // 1. Populate FactJadwal
        $this->info('Populating FactJadwal...');
        $this->populateFactJadwal();

        // 2. Populate FactUtilisasiRuangan
        $this->info('Populating FactUtilisasiRuangan...');
        $this->populateFactUtilisasiRuangan();

        // 3. Populate FactKecocokanJadwal
        $this->info('Populating FactKecocokanJadwal...');
        $this->populateFactKecocokanJadwal();

        $this->info('Fact tables populated successfully!');
    }

    /**
     * Populate FactJadwal from jadwals table
     */
    private function populateFactJadwal()
    {
        $jadwals = Jadwal::with(['mataKuliah.dosen', 'ruangan'])
            ->where('status', true)
            ->get();

        $bar = $this->output->createProgressBar($jadwals->count());
        $bar->start();

        foreach ($jadwals as $jadwal) {
            try {
                // Get dimension keys
                $dosen = $jadwal->mataKuliah->dosen ?? null;
                if (!$dosen) {
                    $bar->advance();
                    continue;
                }

                $dosenKey = DimDosen::where('nip', $dosen->nip ?? $dosen->id)->first()?->dosen_key;
                $mkKey = DimMataKuliah::where('kode_mk', $jadwal->mataKuliah->kode_mk)->first()?->mata_kuliah_key;
                $ruanganKey = DimRuangan::where('kode_ruangan', $jadwal->ruangan->kode_ruangan)->first()?->ruangan_key;
                
                // Find waktu_key
                $waktuKey = DimWaktu::where('hari', $jadwal->hari)
                    ->where('jam_mulai', $jadwal->jam_mulai->format('H:i'))
                    ->where('jam_selesai', $jadwal->jam_selesai->format('H:i'))
                    ->first()?->waktu_key;

                // Find prodi_key
                $prodiKey = DimProdi::where('kode_prodi', $this->getProdiCode($jadwal->prodi))
                    ->orWhere('nama_prodi', 'like', '%' . $jadwal->prodi . '%')
                    ->first()?->prodi_key;

                if (!$dosenKey || !$mkKey || !$ruanganKey || !$waktuKey || !$prodiKey) {
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

                // Calculate measures
                $jumlahSks = $jadwal->mataKuliah->sks ?? 0;
                $durasiMenit = $this->calculateDuration($jadwal->jam_mulai, $jadwal->jam_selesai);
                $kapasitasKelas = $jadwal->mataKuliah->kapasitas ?? 0;
                $jumlahMahasiswa = $kapasitasKelas; // Assuming full capacity
                $utilisasiRuangan = $kapasitasKelas > 0 ? ($jumlahMahasiswa / $kapasitasKelas) * 100 : 0;
                $prioritasPreferensi = $preferensi ? $preferensi->prioritas : null;
                
                // Check for conflicts
                $konflik = $this->checkConflict($jadwal);
                $tingkatKonflik = $konflik ? $this->calculateConflictLevel($jadwal) : 0;

                // Create or update FactJadwal
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
            } catch (\Exception $e) {
                $this->error("Error processing jadwal ID {$jadwal->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Populate FactUtilisasiRuangan from FactJadwal aggregation
     */
    private function populateFactUtilisasiRuangan()
    {
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
                // Get waktu dimension for semester and tahun_akademik
                $waktu = DimWaktu::where('waktu_key', $data->waktu_key)->first();
                
                // Calculate total hours available (assuming 8 hours per day = 480 minutes)
                $totalJamTersedia = 480; // 8 hours * 60 minutes
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
                $this->error("Error processing utilisasi data: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Populate FactKecocokanJadwal from jadwal and preferensi
     */
    private function populateFactKecocokanJadwal()
    {
        $jadwals = Jadwal::with(['mataKuliah.dosen', 'ruangan'])
            ->where('status', true)
            ->get();

        $bar = $this->output->createProgressBar($jadwals->count());
        $bar->start();

        foreach ($jadwals as $jadwal) {
            try {
                $dosen = $jadwal->mataKuliah->dosen ?? null;
                if (!$dosen) {
                    $bar->advance();
                    continue;
                }

                // Get dimension keys
                $dosenKey = DimDosen::where('nip', $dosen->nip ?? $dosen->id)->first()?->dosen_key;
                
                $waktuKey = DimWaktu::where('hari', $jadwal->hari)
                    ->where('jam_mulai', $jadwal->jam_mulai->format('H:i'))
                    ->where('jam_selesai', $jadwal->jam_selesai->format('H:i'))
                    ->first()?->waktu_key;

                if (!$dosenKey || !$waktuKey) {
                    $bar->advance();
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
                    $bar->advance();
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
                    $bar->advance();
                    continue;
                }

                // Check if preferences are met
                $preferensiHari = is_array($preferensi->preferensi_hari) 
                    ? $preferensi->preferensi_hari 
                    : json_decode($preferensi->preferensi_hari, true) ?? [];
                
                $preferensiJam = is_array($preferensi->preferensi_jam) 
                    ? $preferensi->preferensi_jam 
                    : json_decode($preferensi->preferensi_jam, true) ?? [];

                $hariTerpenuhi = in_array($jadwal->hari, $preferensiHari);
                
                $jamTerpenuhi = false;
                $jamMulaiStr = $jadwal->jam_mulai->format('H:i');
                $jamSelesaiStr = $jadwal->jam_selesai->format('H:i');
                foreach ($preferensiJam as $jam) {
                    if (strpos($jam, $jamMulaiStr) !== false || strpos($jam, $jamSelesaiStr) !== false) {
                        $jamTerpenuhi = true;
                        break;
                    }
                }

                // Calculate scores - LOGIKA BARU: Jika jadwal sesuai preferensi (hari DAN jam) = 100%, jika tidak = 0%
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

                // Get waktu for semester and tahun_akademik
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
            } catch (\Exception $e) {
                $this->error("Error processing kecocokan jadwal ID {$jadwal->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    /**
     * Calculate duration in minutes
     */
    private function calculateDuration($jamMulai, $jamSelesai)
    {
        $start = is_string($jamMulai) ? strtotime($jamMulai) : $jamMulai->getTimestamp();
        $end = is_string($jamSelesai) ? strtotime($jamSelesai) : $jamSelesai->getTimestamp();
        return (int) (($end - $start) / 60);
    }

    /**
     * Check if jadwal has conflict
     */
    private function checkConflict($jadwal)
    {
        // Check if same dosen has overlapping schedule
        $conflicts = Jadwal::where('id', '!=', $jadwal->id)
            ->whereHas('mataKuliah', function($q) use ($jadwal) {
                $q->where('dosen_id', $jadwal->mataKuliah->dosen_id);
            })
            ->where('hari', $jadwal->hari)
            ->where('status', true)
            ->where(function($q) use ($jadwal) {
                $q->whereBetween('jam_mulai', [$jadwal->jam_mulai, $jadwal->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$jadwal->jam_mulai, $jadwal->jam_selesai])
                  ->orWhere(function($q2) use ($jadwal) {
                      $q2->where('jam_mulai', '<=', $jadwal->jam_mulai)
                         ->where('jam_selesai', '>=', $jadwal->jam_selesai);
                  });
            })
            ->exists();

        // Check if same ruangan has overlapping schedule
        $ruanganConflict = Jadwal::where('id', '!=', $jadwal->id)
            ->where('ruangan_id', $jadwal->ruangan_id)
            ->where('hari', $jadwal->hari)
            ->where('status', true)
            ->where(function($q) use ($jadwal) {
                $q->whereBetween('jam_mulai', [$jadwal->jam_mulai, $jadwal->jam_selesai])
                  ->orWhereBetween('jam_selesai', [$jadwal->jam_mulai, $jadwal->jam_selesai])
                  ->orWhere(function($q2) use ($jadwal) {
                      $q2->where('jam_mulai', '<=', $jadwal->jam_mulai)
                         ->where('jam_selesai', '>=', $jadwal->jam_selesai);
                  });
            })
            ->exists();

        return $conflicts || $ruanganConflict;
    }

    /**
     * Calculate conflict level (0-5)
     */
    private function calculateConflictLevel($jadwal)
    {
        if (!$this->checkConflict($jadwal)) {
            return 0;
        }

        $conflictCount = 0;
        
        // Count dosen conflicts
        $dosenConflicts = Jadwal::where('id', '!=', $jadwal->id)
            ->whereHas('mataKuliah', function($q) use ($jadwal) {
                $q->where('dosen_id', $jadwal->mataKuliah->dosen_id);
            })
            ->where('hari', $jadwal->hari)
            ->where('status', true)
            ->count();
        
        $conflictCount += $dosenConflicts;

        // Count ruangan conflicts
        $ruanganConflicts = Jadwal::where('id', '!=', $jadwal->id)
            ->where('ruangan_id', $jadwal->ruangan_id)
            ->where('hari', $jadwal->hari)
            ->where('status', true)
            ->count();
        
        $conflictCount += $ruanganConflicts;

        // Return level based on conflict count (max 5)
        return min(5, $conflictCount);
    }

    /**
     * Get prodi code from prodi name
     */
    private function getProdiCode($prodiName)
    {
        $mapping = [
            'Teknik Informatika' => 'IF',
            'Sistem Informasi' => 'SI',
            'Informatika' => 'IF',
        ];

        foreach ($mapping as $name => $code) {
            if (stripos($prodiName, $name) !== false) {
                return $code;
            }
        }

        return 'IF'; // Default
    }
}
