<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PreferensiDosen;
use App\Models\User;

class FixPreferensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua preferensi lama
        PreferensiDosen::whereNull('mata_kuliah_id')->delete();
        
        // Ambil beberapa dosen
        $dosens = User::where('role', 'dosen')->take(5)->get();
        
        if ($dosens->count() == 0) {
            echo "No dosen found!\n";
            return;
        }
        
        // Buat preferensi untuk setiap dosen
        foreach ($dosens as $index => $dosen) {
            $hariOptions = [
                ['Senin', 'Rabu'], // Dosen 1: Senin & Rabu
                ['Selasa', 'Kamis'], // Dosen 2: Selasa & Kamis  
                ['Senin', 'Selasa', 'Rabu'], // Dosen 3: Senin, Selasa, Rabu
                ['Rabu', 'Kamis', 'Jumat'], // Dosen 4: Rabu, Kamis, Jumat
                ['Senin', 'Jumat'] // Dosen 5: Senin & Jumat
            ];
            
            $hari = $hariOptions[$index % count($hariOptions)];
            
            PreferensiDosen::create([
                'dosen_id' => $dosen->id,
                'mata_kuliah_id' => null, // Global preferensi
                'preferensi_hari' => $hari,
                'preferensi_jam' => ['08:00-09:00', '09:00-10:00', '10:00-11:00', '13:00-14:00', '14:00-15:00'],
                'prioritas' => 1,
                'catatan' => 'Test preferensi untuk dosen ' . $dosen->name
            ]);
            
            echo "Created preferensi for dosen " . $dosen->name . " (ID: " . $dosen->id . ") - Days: " . implode(', ', $hari) . "\n";
        }
        
        echo "Total preferensi created: " . $dosens->count() . "\n";
    }
}