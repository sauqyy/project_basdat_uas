<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;

class SimpleRuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Teknik Industri
        $this->createRuangan('TINK1', 'Kelas Teknik Industri 1', 'Teknik Industri', 'kelas');
        $this->createRuangan('TINK2', 'Kelas Teknik Industri 2', 'Teknik Industri', 'kelas');
        $this->createRuangan('TINK3', 'Kelas Teknik Industri 3', 'Teknik Industri', 'kelas');
        $this->createRuangan('TINL1', 'Lab Teknik Industri', 'Teknik Industri', 'lab');
        
        // Teknik Elektro
        $this->createRuangan('TELK1', 'Kelas Teknik Elektro 1', 'Teknik Elektro', 'kelas');
        $this->createRuangan('TELK2', 'Kelas Teknik Elektro 2', 'Teknik Elektro', 'kelas');
        $this->createRuangan('TELK3', 'Kelas Teknik Elektro 3', 'Teknik Elektro', 'kelas');
        $this->createRuangan('TELL1', 'Lab Teknik Elektro', 'Teknik Elektro', 'lab');
        
        // Teknik Robotika dan Kecerdasan Buatan
        $this->createRuangan('TRKK1', 'Kelas Teknik Robotika 1', 'Teknik Robotika dan Kecerdasan Buatan', 'kelas');
        $this->createRuangan('TRKK2', 'Kelas Teknik Robotika 2', 'Teknik Robotika dan Kecerdasan Buatan', 'kelas');
        $this->createRuangan('TRKK3', 'Kelas Teknik Robotika 3', 'Teknik Robotika dan Kecerdasan Buatan', 'kelas');
        $this->createRuangan('TRKL1', 'Lab Teknik Robotika', 'Teknik Robotika dan Kecerdasan Buatan', 'lab');
        
        $this->command->info('Simple ruangan seeder completed!');
    }
    
    private function createRuangan($kode, $nama, $prodi, $tipe)
    {
        $existing = Ruangan::where('kode_ruangan', $kode)->first();
        if (!$existing) {
            Ruangan::create([
                'kode_ruangan' => $kode,
                'nama_ruangan' => $nama,
                'kapasitas' => $tipe === 'lab' ? rand(20, 30) : rand(30, 50),
                'tipe_ruangan' => $tipe,
                'fasilitas' => $tipe === 'lab' ? 'AC, Komputer, Proyektor, Whiteboard' : 'AC, Proyektor, Whiteboard',
                'status' => true,
                'prodi' => $prodi
            ]);
            $this->command->info("Created: {$kode}");
        } else {
            $this->command->info("Exists: {$kode}");
        }
    }
}
