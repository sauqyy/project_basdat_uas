<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class JadwalProdiLainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil beberapa ruangan dan mata kuliah untuk testing
        $ruangans = Ruangan::take(3)->get();
        $mataKuliahs = \App\Models\MataKuliah::take(5)->get();
        
        if ($ruangans->count() >= 3 && $mataKuliahs->count() >= 5) {
            // Jadwal prodi lain yang akan bentrok
            $jadwalProdiLain = [
                [
                    'mata_kuliah_id' => $mataKuliahs[0]->id,
                    'ruangan_id' => $ruangans[0]->id,
                    'hari' => 'Senin',
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '09:50', // 2 SKS = 100 menit
                    'semester' => 'Ganjil',
                    'tahun_akademik' => date('Y'),
                    'prodi' => 'Teknik Sipil',
                    'status' => true
                ],
                [
                    'mata_kuliah_id' => $mataKuliahs[1]->id,
                    'ruangan_id' => $ruangans[1]->id,
                    'hari' => 'Selasa',
                    'jam_mulai' => '10:00',
                    'jam_selesai' => '11:50', // 2 SKS = 100 menit
                    'semester' => 'Ganjil',
                    'tahun_akademik' => date('Y'),
                    'prodi' => 'Teknik Mesin',
                    'status' => true
                ],
                [
                    'mata_kuliah_id' => $mataKuliahs[2]->id,
                    'ruangan_id' => $ruangans[2]->id,
                    'hari' => 'Rabu',
                    'jam_mulai' => '13:00',
                    'jam_selesai' => '14:50', // 2 SKS = 100 menit
                    'semester' => 'Ganjil',
                    'tahun_akademik' => date('Y'),
                    'prodi' => 'Teknik Elektro',
                    'status' => true
                ],
                [
                    'mata_kuliah_id' => $mataKuliahs[3]->id,
                    'ruangan_id' => $ruangans[0]->id,
                    'hari' => 'Kamis',
                    'jam_mulai' => '15:00',
                    'jam_selesai' => '16:50', // 2 SKS = 100 menit
                    'semester' => 'Ganjil',
                    'tahun_akademik' => date('Y'),
                    'prodi' => 'Teknik Kimia',
                    'status' => true
                ],
                [
                    'mata_kuliah_id' => $mataKuliahs[4]->id,
                    'ruangan_id' => $ruangans[1]->id,
                    'hari' => 'Jumat',
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '09:50', // 2 SKS = 100 menit
                    'semester' => 'Ganjil',
                    'tahun_akademik' => date('Y'),
                    'prodi' => 'Teknik Industri',
                    'status' => true
                ]
            ];

            foreach ($jadwalProdiLain as $jadwal) {
                Jadwal::create($jadwal);
            }

            $this->command->info('Jadwal prodi lain berhasil ditambahkan untuk testing konflik!');
            $this->command->info('Prodi yang ditambahkan: Teknik Sipil, Teknik Mesin, Teknik Elektro, Teknik Kimia, Teknik Industri');
        } else {
            $this->command->error('Tidak ada ruangan yang tersedia untuk testing!');
        }
    }
}