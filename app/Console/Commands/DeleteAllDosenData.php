<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\MataKuliah;
use App\Models\Jadwal;
use App\Models\PreferensiDosen;

class DeleteAllDosenData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dosen:delete-all {--force : Force delete without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all dosen accounts and related data (mata kuliah, jadwal, preferensi)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== SCRIPT PENGHAPUSAN SEMUA DATA DOSEN DAN MATA KULIAH ===');
        $this->newLine();

        // Konfirmasi penghapusan
        if (!$this->option('force')) {
            if (!$this->confirm('Apakah Anda yakin ingin menghapus SEMUA data dosen dan mata kuliah? Tindakan ini TIDAK DAPAT DIBATALKAN!')) {
                $this->info('Penghapusan dibatalkan.');
                return 0;
            }
        }

        try {
            // Mulai transaksi database
            DB::beginTransaction();
            
            // Disable foreign key checks sementara
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            $this->info('1. Menghapus semua jadwal...');
            $jadwalCount = Jadwal::count();
            Jadwal::truncate();
            $this->info("   ✓ Berhasil menghapus {$jadwalCount} jadwal");
            $this->newLine();
            
            $this->info('2. Menghapus semua preferensi dosen...');
            $preferensiCount = PreferensiDosen::count();
            PreferensiDosen::truncate();
            $this->info("   ✓ Berhasil menghapus {$preferensiCount} preferensi dosen");
            $this->newLine();
            
            $this->info('3. Menghapus semua mata kuliah...');
            $mataKuliahCount = MataKuliah::count();
            MataKuliah::truncate();
            $this->info("   ✓ Berhasil menghapus {$mataKuliahCount} mata kuliah");
            $this->newLine();
            
            $this->info('4. Menghapus semua akun dosen...');
            $dosenCount = User::where('role', 'dosen')->count();
            User::where('role', 'dosen')->delete();
            $this->info("   ✓ Berhasil menghapus {$dosenCount} akun dosen");
            $this->newLine();
            
            // Enable foreign key checks kembali
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            // Commit transaksi
            DB::commit();
            
            $this->info('=== PENGHAPUSAN BERHASIL ===');
            $this->info('Data yang dihapus:');
            $this->info("- {$jadwalCount} jadwal");
            $this->info("- {$preferensiCount} preferensi dosen");
            $this->info("- {$mataKuliahCount} mata kuliah");
            $this->info("- {$dosenCount} akun dosen");
            $this->newLine();
            
            $this->info('Database telah dibersihkan dari semua data dosen dan mata kuliah.');
            $this->info('Data admin dan mahasiswa tetap aman.');
            
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            try {
                DB::rollback();
            } catch (\Exception $rollbackException) {
                // Ignore rollback error
            }
            
            $this->error('=== ERROR TERJADI ===');
            $this->error('Error: ' . $e->getMessage());
            $this->error('Transaksi dibatalkan. Data tidak berubah.');
            return 1;
        }

        $this->newLine();
        $this->info('Script selesai dijalankan.');
        return 0;
    }
}
