<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;

class FixRuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodiList = [
            'Teknik Industri',
            'Teknik Elektro',
            'Teknik Robotika dan Kecerdasan Buatan'
        ];

        foreach ($prodiList as $prodi) {
            $this->command->info("Processing {$prodi}...");
            
            // 3 ruangan kelas per prodi
            for ($i = 1; $i <= 3; $i++) {
                $kodeRuangan = strtoupper(substr(str_replace(' ', '', $prodi), 0, 3)) . "K{$i}";
                
                // Cek apakah ruangan sudah ada
                $existingRuangan = Ruangan::where('kode_ruangan', $kodeRuangan)->first();
                if (!$existingRuangan) {
                    $ruangan = Ruangan::create([
                        'kode_ruangan' => $kodeRuangan,
                        'nama_ruangan' => "Kelas {$prodi} {$i}",
                        'kapasitas' => rand(30, 50),
                        'tipe_ruangan' => 'kelas',
                        'fasilitas' => 'AC, Proyektor, Whiteboard',
                        'status' => true,
                        'prodi' => $prodi
                    ]);
                    
                    $this->command->info("✅ Created ruangan: {$kodeRuangan}");
                } else {
                    $this->command->info("⚠️  Ruangan already exists: {$kodeRuangan}");
                }
            }
            
            // 1 lab per prodi
            $kodeLab = strtoupper(substr(str_replace(' ', '', $prodi), 0, 3)) . "L1";
            $existingLab = Ruangan::where('kode_ruangan', $kodeLab)->first();
            if (!$existingLab) {
                $lab = Ruangan::create([
                    'kode_ruangan' => $kodeLab,
                    'nama_ruangan' => "Lab {$prodi}",
                    'kapasitas' => rand(20, 30),
                    'tipe_ruangan' => 'lab',
                    'fasilitas' => 'AC, Komputer, Proyektor, Whiteboard',
                    'status' => true,
                    'prodi' => $prodi
                ]);
                
                $this->command->info("✅ Created lab: {$kodeLab}");
            } else {
                $this->command->info("⚠️  Lab already exists: {$kodeLab}");
            }
        }

        $this->command->info('');
        $this->command->info('🎉 Fix ruangan completed!');
    }
}
