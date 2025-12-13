<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\MataKuliah;
use App\Models\Ruangan;

class PraktikumJadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari mata kuliah yang ada praktikum
        $mataKuliahPraktikum = MataKuliah::where('ada_praktikum', true)->get();
        
        if ($mataKuliahPraktikum->count() == 0) {
            echo "No mata kuliah with praktikum found.\n";
            return;
        }
        
        // Cari ruangan lab
        $ruanganLab = Ruangan::where('tipe_ruangan', 'lab')->get();
        
        if ($ruanganLab->count() == 0) {
            echo "No lab ruangan found.\n";
            return;
        }
        
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        foreach ($mataKuliahPraktikum as $mk) {
            // Hapus jadwal praktikum yang sudah ada untuk mata kuliah ini
            Jadwal::where('mata_kuliah_id', $mk->id)
                  ->whereHas('ruangan', function($q) {
                      $q->where('tipe_ruangan', 'lab');
                  })
                  ->delete();
            
            // Buat jadwal praktikum baru
            $hari = $hariList[array_rand($hariList)];
            $ruangan = $ruanganLab->random();
            
            Jadwal::create([
                'mata_kuliah_id' => $mk->id,
                'ruangan_id' => $ruangan->id,
                'hari' => $hari,
                'jam_mulai' => '14:00:00',
                'jam_selesai' => '17:00:00',
                'semester' => 'Ganjil',
                'tahun_akademik' => 2024,
                'prodi' => $mk->prodi,
                'status' => true
            ]);
            
            echo "Created praktikum jadwal for: " . $mk->nama_mk . " on " . $hari . " in " . $ruangan->nama_ruangan . "\n";
        }
        
        echo "Total praktikum jadwals created: " . $mataKuliahPraktikum->count() . "\n";
    }
}
