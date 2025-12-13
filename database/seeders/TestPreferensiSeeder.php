<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PreferensiDosen;
use App\Models\User;

class TestPreferensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari dosen yang ada
        $dosens = User::where('role', 'dosen')->take(3)->get();
        
        if ($dosens->count() == 0) {
            echo "No dosen found. Please run other seeders first.\n";
            return;
        }
        
        // Hapus preferensi lama
        PreferensiDosen::whereNull('mata_kuliah_id')->delete();
        
        // Buat preferensi test untuk beberapa dosen
        $preferensiData = [
            [
                'dosen_id' => $dosens[0]->id,
                'preferensi_hari' => ['Senin', 'Rabu'], // Hanya Senin dan Rabu
                'preferensi_jam' => ['08:00-09:00', '09:00-10:00', '10:00-11:00', '13:00-14:00'],
                'prioritas' => 1,
                'catatan' => 'Test preferensi - hanya bisa Senin dan Rabu'
            ],
            [
                'dosen_id' => $dosens[1]->id ?? $dosens[0]->id,
                'preferensi_hari' => ['Selasa', 'Kamis'], // Hanya Selasa dan Kamis
                'preferensi_jam' => ['08:00-09:00', '09:00-10:00', '14:00-15:00', '15:00-16:00'],
                'prioritas' => 2,
                'catatan' => 'Test preferensi - hanya bisa Selasa dan Kamis'
            ]
        ];
        
        foreach ($preferensiData as $data) {
            if ($data['dosen_id']) {
                PreferensiDosen::create($data);
                echo "Created preferensi for dosen ID: " . $data['dosen_id'] . " - Days: " . implode(', ', $data['preferensi_hari']) . "\n";
            }
        }
        
        echo "Total preferensi created: " . count($preferensiData) . "\n";
    }
}
