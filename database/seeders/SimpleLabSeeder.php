<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ruangan;

class SimpleLabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat beberapa ruangan lab sederhana
        $labs = [
            [
                'kode_ruangan' => 'LAB-001',
                'nama_ruangan' => 'Lab Komputer 1',
                'kapasitas' => 50,
                'tipe_ruangan' => 'lab',
                'fasilitas' => 'Komputer, Projector',
                'status' => 1,
                'prodi' => 'Teknologi Sains Data'
            ],
            [
                'kode_ruangan' => 'LAB-002',
                'nama_ruangan' => 'Lab Komputer 2',
                'kapasitas' => 50,
                'tipe_ruangan' => 'lab',
                'fasilitas' => 'Komputer, Projector',
                'status' => 1,
                'prodi' => 'Teknologi Sains Data'
            ],
            [
                'kode_ruangan' => 'LAB-003',
                'nama_ruangan' => 'Lab Umum',
                'kapasitas' => 50,
                'tipe_ruangan' => 'lab',
                'fasilitas' => 'Komputer, Projector',
                'status' => 1,
                'prodi' => null
            ]
        ];

        foreach ($labs as $lab) {
            // Cek apakah sudah ada
            $existing = Ruangan::where('kode_ruangan', $lab['kode_ruangan'])->first();
            
            if (!$existing) {
                Ruangan::create($lab);
                echo "Created lab: " . $lab['nama_ruangan'] . "\n";
            } else {
                echo "Lab already exists: " . $lab['nama_ruangan'] . "\n";
            }
        }

        echo "Lab seeding completed!\n";
    }
}
