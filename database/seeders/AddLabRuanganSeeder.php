<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class AddLabRuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔍 Checking current ruangan...');
        
        $labCount = Ruangan::where('tipe_ruangan', 'lab')->count();
        $kelasCount = Ruangan::where('tipe_ruangan', 'kelas')->count();
        $totalCount = Ruangan::count();
        
        $this->command->info("Current ruangan: {$labCount} lab, {$kelasCount} kelas, {$totalCount} total");
        
        // Add lab ruangan if not enough
        if ($labCount < 3) {
            $labRuangans = [
                [
                    'kode_ruangan' => 'LAB001',
                    'nama_ruangan' => 'Lab Komputer 1',
                    'kapasitas' => 30,
                    'tipe_ruangan' => 'lab',
                    'fasilitas' => 'AC, Proyektor, 30 PC, Internet',
                    'status' => true,
                    'prodi' => null // General lab
                ],
                [
                    'kode_ruangan' => 'LAB002',
                    'nama_ruangan' => 'Lab Komputer 2',
                    'kapasitas' => 25,
                    'tipe_ruangan' => 'lab',
                    'fasilitas' => 'AC, Proyektor, 25 PC, Internet',
                    'status' => true,
                    'prodi' => null // General lab
                ],
                [
                    'kode_ruangan' => 'LAB003',
                    'nama_ruangan' => 'Lab Komputer 3',
                    'kapasitas' => 20,
                    'tipe_ruangan' => 'lab',
                    'fasilitas' => 'AC, Proyektor, 20 PC, Internet',
                    'status' => true,
                    'prodi' => null // General lab
                ]
            ];
            
            foreach ($labRuangans as $lab) {
                // Check if ruangan already exists
                $existing = Ruangan::where('kode_ruangan', $lab['kode_ruangan'])->first();
                if (!$existing) {
                    Ruangan::create($lab);
                    $this->command->info("✅ Created lab: {$lab['nama_ruangan']} ({$lab['kode_ruangan']})");
                } else {
                    $this->command->info("ℹ️  Lab already exists: {$lab['nama_ruangan']} ({$lab['kode_ruangan']})");
                }
            }
        }
        
        // Add kelas ruangan if not enough
        if ($kelasCount < 3) {
            $kelasRuangans = [
                [
                    'kode_ruangan' => 'KLS001',
                    'nama_ruangan' => 'Kelas A101',
                    'kapasitas' => 40,
                    'tipe_ruangan' => 'kelas',
                    'fasilitas' => 'AC, Proyektor, Whiteboard, Sound System',
                    'status' => true,
                    'prodi' => null // General kelas
                ],
                [
                    'kode_ruangan' => 'KLS002',
                    'nama_ruangan' => 'Kelas A102',
                    'kapasitas' => 35,
                    'tipe_ruangan' => 'kelas',
                    'fasilitas' => 'AC, Proyektor, Whiteboard, Sound System',
                    'status' => true,
                    'prodi' => null // General kelas
                ],
                [
                    'kode_ruangan' => 'KLS003',
                    'nama_ruangan' => 'Kelas A103',
                    'kapasitas' => 30,
                    'tipe_ruangan' => 'kelas',
                    'fasilitas' => 'AC, Proyektor, Whiteboard, Sound System',
                    'status' => true,
                    'prodi' => null // General kelas
                ]
            ];
            
            foreach ($kelasRuangans as $kelas) {
                // Check if ruangan already exists
                $existing = Ruangan::where('kode_ruangan', $kelas['kode_ruangan'])->first();
                if (!$existing) {
                    Ruangan::create($kelas);
                    $this->command->info("✅ Created kelas: {$kelas['nama_ruangan']} ({$kelas['kode_ruangan']})");
                } else {
                    $this->command->info("ℹ️  Kelas already exists: {$kelas['nama_ruangan']} ({$kelas['kode_ruangan']})");
                }
            }
        }
        
        $finalLabCount = Ruangan::where('tipe_ruangan', 'lab')->count();
        $finalKelasCount = Ruangan::where('tipe_ruangan', 'kelas')->count();
        $finalTotalCount = Ruangan::count();
        
        $this->command->info('');
        $this->command->info('📊 Final ruangan summary:');
        $this->command->info("- Lab: {$finalLabCount}");
        $this->command->info("- Kelas: {$finalKelasCount}");
        $this->command->info("- Total: {$finalTotalCount}");
        $this->command->info('');
        $this->command->info('✅ Ruangan setup completed!');
    }
}