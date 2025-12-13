<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DosenAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodiList = [
            'Teknologi Sains Data',
            'Rekayasa Nanoteknologi',
            'Teknik Industri',
            'Teknik Elektro',
            'Teknik Robotika dan Kecerdasan Buatan'
        ];

        $this->command->info('=== DAFTAR AKUN DOSEN ===');
        $this->command->info('');
        $this->command->info('🔑 Password untuk semua dosen: password');
        $this->command->info('');

        foreach ($prodiList as $prodi) {
            $this->command->info("📚 {$prodi}:");
            $this->command->info('─' . str_repeat('─', strlen($prodi) + 3));
            
            $dosens = User::where('role', 'dosen')
                ->where('prodi', $prodi)
                ->orderBy('name')
                ->get();
            
            foreach ($dosens as $index => $dosen) {
                $this->command->info(($index + 1) . ". Nama: {$dosen->name}");
                $this->command->info("   Email: {$dosen->email}");
                $this->command->info("   NIP: {$dosen->nip}");
                $this->command->info("   Password: password");
                $this->command->info("");
            }
        }

        $this->command->info('=== DAFTAR AKUN ADMIN ===');
        $this->command->info('');
        
        // Admin Prodi
        $this->command->info('👨‍💼 ADMIN PRODI:');
        $this->command->info('─' . str_repeat('─', 15));
        
        foreach ($prodiList as $prodi) {
            $admin = User::where('role', 'admin_prodi')
                ->where('prodi', $prodi)
                ->first();
            
            if ($admin) {
                $this->command->info("📚 {$prodi}:");
                $this->command->info("   Nama: {$admin->name}");
                $this->command->info("   Email: {$admin->email}");
                $this->command->info("   Password: password");
                $this->command->info("");
            }
        }
        
        // Super Admin
        $this->command->info('👑 SUPER ADMIN:');
        $this->command->info('─' . str_repeat('─', 12));
        
        $superAdmin = User::where('role', 'admin_super')->first();
        if ($superAdmin) {
            $this->command->info("   Nama: {$superAdmin->name}");
            $this->command->info("   Email: {$superAdmin->email}");
            $this->command->info("   Password: password");
        }
        
        $this->command->info('');
        $this->command->info('🌐 URL Login:');
        $this->command->info('   Dosen: http://localhost:8000/login');
        $this->command->info('   Admin: http://localhost:8000/admin/login');
        $this->command->info('');
        $this->command->info('✅ Semua akun siap digunakan!');
    }
}
