<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MataKuliah;
use App\Models\PreferensiDosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 3 additional Dosen Users
        $dosen4 = User::create([
            'name' => 'Dr. Rina Sari, S.Kom., M.T.',
            'email' => 'rina@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'DSN004'
        ]);

        $dosen5 = User::create([
            'name' => 'Dr. Agus Prasetyo, S.Kom., M.Kom.',
            'email' => 'agus@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'DSN005'
        ]);

        $dosen6 = User::create([
            'name' => 'Dr. Maya Indira, S.Kom., M.T.',
            'email' => 'maya@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'DSN006'
        ]);

        // Create additional Mata Kuliah for new dosen
        $mataKuliahs = [
            ['kode_mk' => 'MK006', 'nama_mk' => 'Sistem Operasi', 'sks' => 3, 'semester' => '4', 'dosen_id' => $dosen4->id, 'kapasitas' => 30, 'deskripsi' => 'Mata kuliah sistem operasi komputer'],
            ['kode_mk' => 'MK007', 'nama_mk' => 'Pemrograman Mobile', 'sks' => 4, 'semester' => '6', 'dosen_id' => $dosen4->id, 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah pengembangan aplikasi mobile'],
            ['kode_mk' => 'MK008', 'nama_mk' => 'Keamanan Sistem', 'sks' => 3, 'semester' => '7', 'dosen_id' => $dosen5->id, 'kapasitas' => 20, 'deskripsi' => 'Mata kuliah keamanan sistem informasi'],
            ['kode_mk' => 'MK009', 'nama_mk' => 'Data Mining', 'sks' => 3, 'semester' => '8', 'dosen_id' => $dosen5->id, 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah penambangan data'],
            ['kode_mk' => 'MK010', 'nama_mk' => 'Interaksi Manusia Komputer', 'sks' => 3, 'semester' => '5', 'dosen_id' => $dosen6->id, 'kapasitas' => 30, 'deskripsi' => 'Mata kuliah desain antarmuka pengguna'],
            ['kode_mk' => 'MK011', 'nama_mk' => 'Rekayasa Perangkat Lunak', 'sks' => 4, 'semester' => '6', 'dosen_id' => $dosen6->id, 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah pengembangan perangkat lunak'],
        ];

        foreach ($mataKuliahs as $mk) {
            MataKuliah::create($mk);
        }

        // Create Preferensi for new dosen
        $preferensi = [
            // Dosen 4 - Dr. Rina Sari
            ['dosen_id' => $dosen4->id, 'mata_kuliah_id' => 6, 'preferensi_hari' => ['Senin', 'Rabu'], 'preferensi_jam' => ['08:00-10:00', '10:00-12:00'], 'prioritas' => 1, 'catatan' => 'Prefer pagi hari untuk sistem operasi'],
            ['dosen_id' => $dosen4->id, 'mata_kuliah_id' => 7, 'preferensi_hari' => ['Selasa', 'Kamis'], 'preferensi_jam' => ['13:00-15:00', '15:00-17:00'], 'prioritas' => 1, 'catatan' => 'Prefer siang hari untuk mobile programming'],
            
            // Dosen 5 - Dr. Agus Prasetyo
            ['dosen_id' => $dosen5->id, 'mata_kuliah_id' => 8, 'preferensi_hari' => ['Senin', 'Jumat'], 'preferensi_jam' => ['08:00-10:00', '13:00-15:00'], 'prioritas' => 1, 'catatan' => 'Prefer ruang lab untuk keamanan sistem'],
            ['dosen_id' => $dosen5->id, 'mata_kuliah_id' => 9, 'preferensi_hari' => ['Rabu', 'Kamis'], 'preferensi_jam' => ['10:00-12:00', '15:00-17:00'], 'prioritas' => 2, 'catatan' => 'Fleksibel untuk data mining'],
            
            // Dosen 6 - Dr. Maya Indira
            ['dosen_id' => $dosen6->id, 'mata_kuliah_id' => 10, 'preferensi_hari' => ['Selasa', 'Rabu'], 'preferensi_jam' => ['08:00-10:00', '10:00-12:00'], 'prioritas' => 1, 'catatan' => 'Prefer pagi hari untuk HCI'],
            ['dosen_id' => $dosen6->id, 'mata_kuliah_id' => 11, 'preferensi_hari' => ['Kamis', 'Jumat'], 'preferensi_jam' => ['13:00-15:00', '15:00-17:00'], 'prioritas' => 1, 'catatan' => 'Prefer siang hari untuk RPL'],
        ];

        foreach ($preferensi as $p) {
            PreferensiDosen::create($p);
        }

        $this->command->info('3 akun dosen baru berhasil dibuat!');
        $this->command->info('Email: rina@kampusmerdeka.ac.id, agus@kampusmerdeka.ac.id, maya@kampusmerdeka.ac.id');
        $this->command->info('Password: password');
    }
}