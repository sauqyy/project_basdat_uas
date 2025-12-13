<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\MataKuliah;
use App\Models\Ruangan;

class TestJadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some mata kuliah and ruangan
        $mataKuliahs = MataKuliah::take(5)->get();
        $ruangans = Ruangan::take(5)->get();
        
        if ($mataKuliahs->count() == 0 || $ruangans->count() == 0) {
            echo "No mata kuliah or ruangan found. Please run other seeders first.\n";
            return;
        }
        
        // Create test jadwals for different prodi
        $prodiList = [
            'Teknologi Sains Data',
            'Rekayasa Nanoteknologi', 
            'Teknik Industri',
            'Teknik Elektro',
            'Teknik Robotika dan Kecerdasan Buatan'
        ];
        
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamList = [
            ['08:00:00', '10:00:00'],
            ['10:00:00', '12:00:00'],
            ['13:00:00', '15:00:00'],
            ['15:00:00', '17:00:00']
        ];
        
        foreach ($mataKuliahs as $index => $mk) {
            $prodi = $prodiList[$index % count($prodiList)];
            $hari = $hariList[$index % count($hariList)];
            $jam = $jamList[$index % count($jamList)];
            $ruangan = $ruangans[$index % $ruangans->count()];
            
            // Update mata kuliah prodi
            $mk->update(['prodi' => $prodi]);
            
            // Create jadwal
            Jadwal::create([
                'mata_kuliah_id' => $mk->id,
                'ruangan_id' => $ruangan->id,
                'hari' => $hari,
                'jam_mulai' => $jam[0],
                'jam_selesai' => $jam[1],
                'semester' => 'Ganjil',
                'tahun_akademik' => 2024,
                'prodi' => $prodi,
                'status' => true
            ]);
        }
        
        echo "Created " . $mataKuliahs->count() . " test jadwals for different prodi.\n";
    }
}
