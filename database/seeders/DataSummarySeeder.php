<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MataKuliah;
use App\Models\Ruangan;

class DataSummarySeeder extends Seeder
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

        $this->command->info('=== RINGKASAN DATA DUMMY ===');
        $this->command->info('');
        
        $this->command->info('📊 TOTAL DATA:');
        $this->command->info('- Dosen: ' . User::where('role', 'dosen')->count());
        $this->command->info('- Mata Kuliah: ' . MataKuliah::count());
        $this->command->info('- Ruangan: ' . Ruangan::count());
        $this->command->info('');
        
        $this->command->info('📚 MATA KULIAH PER PRODI:');
        foreach ($prodiList as $prodi) {
            $mkCount = MataKuliah::where('prodi', $prodi)->count();
            $this->command->info("- {$prodi}: {$mkCount} mata kuliah");
        }
        $this->command->info('');
        
        $this->command->info('🏢 RUANGAN PER PRODI:');
        foreach ($prodiList as $prodi) {
            $ruanganCount = Ruangan::where('prodi', $prodi)->count();
            $this->command->info("- {$prodi}: {$ruanganCount} ruangan");
        }
        
        $ruanganUmumCount = Ruangan::whereNull('prodi')->count();
        $this->command->info("- Ruangan Umum: {$ruanganUmumCount} ruangan");
        $this->command->info('');
        
        $this->command->info('👨‍🏫 DOSEN PER PRODI:');
        foreach ($prodiList as $prodi) {
            $dosenCount = User::where('role', 'dosen')->where('prodi', $prodi)->count();
            $this->command->info("- {$prodi}: {$dosenCount} dosen");
        }
        $this->command->info('');
        
        $this->command->info('✅ Data dummy berhasil dibuat!');
        $this->command->info('Sekarang Anda bisa:');
        $this->command->info('1. Login sebagai admin prodi untuk mengelola mata kuliah');
        $this->command->info('2. Login sebagai super admin untuk mengelola ruangan dan generate jadwal');
        $this->command->info('3. Login sebagai dosen untuk melihat jadwal dan set preferensi');
    }
}
