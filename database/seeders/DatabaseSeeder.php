<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ruangan;
use App\Models\MataKuliah;
use App\Models\PreferensiDosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nip' => 'ADM001'
        ]);

        // Create Dosen Users
        $dosen1 = User::create([
            'name' => 'Dr. Ahmad Wijaya, S.Kom., M.T.',
            'email' => 'ahmad@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'DSN001'
        ]);

        $dosen2 = User::create([
            'name' => 'Dr. Siti Nurhaliza, S.Kom., M.Kom.',
            'email' => 'siti@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'DSN002'
        ]);

        $dosen3 = User::create([
            'name' => 'Prof. Dr. Budi Santoso, S.Kom., M.T.',
            'email' => 'budi@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'DSN003'
        ]);

        // Create Ruangan
        $ruangans = [
            ['kode_ruangan' => 'R001', 'nama_ruangan' => 'Lab Komputer 1', 'kapasitas' => 30, 'tipe_ruangan' => 'lab', 'fasilitas' => 'AC, Proyektor, 30 PC'],
            ['kode_ruangan' => 'R002', 'nama_ruangan' => 'Lab Komputer 2', 'kapasitas' => 25, 'tipe_ruangan' => 'lab', 'fasilitas' => 'AC, Proyektor, 25 PC'],
            ['kode_ruangan' => 'R003', 'nama_ruangan' => 'Kelas A101', 'kapasitas' => 40, 'tipe_ruangan' => 'kelas', 'fasilitas' => 'AC, Proyektor, Whiteboard'],
            ['kode_ruangan' => 'R004', 'nama_ruangan' => 'Kelas A102', 'kapasitas' => 35, 'tipe_ruangan' => 'kelas', 'fasilitas' => 'AC, Proyektor, Whiteboard'],
            ['kode_ruangan' => 'R005', 'nama_ruangan' => 'Auditorium', 'kapasitas' => 100, 'tipe_ruangan' => 'auditorium', 'fasilitas' => 'AC, Proyektor, Sound System'],
        ];

        foreach ($ruangans as $ruangan) {
            Ruangan::create($ruangan);
        }

        // Create Mata Kuliah
        $mataKuliahs = [
            ['kode_mk' => 'MK001', 'nama_mk' => 'Algoritma dan Struktur Data', 'sks' => 3, 'semester' => '3', 'dosen_id' => $dosen1->id, 'kapasitas' => 30, 'deskripsi' => 'Mata kuliah dasar pemrograman'],
            ['kode_mk' => 'MK002', 'nama_mk' => 'Basis Data', 'sks' => 3, 'semester' => '4', 'dosen_id' => $dosen2->id, 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah manajemen data'],
            ['kode_mk' => 'MK003', 'nama_mk' => 'Pemrograman Web', 'sks' => 4, 'semester' => '5', 'dosen_id' => $dosen1->id, 'kapasitas' => 30, 'deskripsi' => 'Mata kuliah pengembangan web'],
            ['kode_mk' => 'MK004', 'nama_mk' => 'Jaringan Komputer', 'sks' => 3, 'semester' => '6', 'dosen_id' => $dosen3->id, 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah jaringan dan komunikasi'],
            ['kode_mk' => 'MK005', 'nama_mk' => 'Kecerdasan Buatan', 'sks' => 3, 'semester' => '7', 'dosen_id' => $dosen2->id, 'kapasitas' => 20, 'deskripsi' => 'Mata kuliah AI dan machine learning'],
        ];

        foreach ($mataKuliahs as $mk) {
            MataKuliah::create($mk);
        }

        // Create Preferensi Dosen
        $preferensi = [
            ['dosen_id' => $dosen1->id, 'mata_kuliah_id' => 1, 'preferensi_hari' => ['Senin', 'Rabu'], 'preferensi_jam' => ['08:00-10:00', '10:00-12:00'], 'prioritas' => 1, 'catatan' => 'Prefer pagi hari'],
            ['dosen_id' => $dosen1->id, 'mata_kuliah_id' => 3, 'preferensi_hari' => ['Selasa', 'Kamis'], 'preferensi_jam' => ['13:00-15:00', '15:00-17:00'], 'prioritas' => 2, 'catatan' => 'Prefer siang hari'],
            ['dosen_id' => $dosen2->id, 'mata_kuliah_id' => 2, 'preferensi_hari' => ['Senin', 'Jumat'], 'preferensi_jam' => ['08:00-10:00', '13:00-15:00'], 'prioritas' => 1, 'catatan' => 'Fleksibel'],
            ['dosen_id' => $dosen2->id, 'mata_kuliah_id' => 5, 'preferensi_hari' => ['Rabu', 'Kamis'], 'preferensi_jam' => ['10:00-12:00', '15:00-17:00'], 'prioritas' => 1, 'catatan' => 'Prefer ruang lab'],
            ['dosen_id' => $dosen3->id, 'mata_kuliah_id' => 4, 'preferensi_hari' => ['Selasa', 'Rabu'], 'preferensi_jam' => ['08:00-10:00', '10:00-12:00'], 'prioritas' => 1, 'catatan' => 'Prefer pagi hari'],
        ];

        foreach ($preferensi as $p) {
            PreferensiDosen::create($p);
        }
    }
}
