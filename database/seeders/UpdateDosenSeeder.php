<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MataKuliah;
use App\Models\PreferensiDosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UpdateDosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua mata kuliah dan preferensi yang ada
        PreferensiDosen::query()->delete();
        MataKuliah::query()->delete();

        // Ambil semua dosen
        $dosens = User::where('role', 'dosen')->get();

        // Mata kuliah untuk setiap dosen (3 mata kuliah per dosen)
        $mataKuliahData = [
            // Dosen 1 - Dr. Ahmad Wijaya
            [
                ['kode_mk' => 'MK001', 'nama_mk' => 'Algoritma dan Struktur Data', 'sks' => 3, 'semester' => '3', 'kapasitas' => 30, 'deskripsi' => 'Mata kuliah dasar pemrograman dan struktur data'],
                ['kode_mk' => 'MK002', 'nama_mk' => 'Pemrograman Web', 'sks' => 4, 'semester' => '5', 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah pengembangan aplikasi web'],
                ['kode_mk' => 'MK003', 'nama_mk' => 'Pemrograman Berorientasi Objek', 'sks' => 3, 'semester' => '4', 'kapasitas' => 30, 'deskripsi' => 'Mata kuliah konsep OOP dan implementasinya'],
            ],
            // Dosen 2 - Dr. Siti Nurhaliza
            [
                ['kode_mk' => 'MK004', 'nama_mk' => 'Basis Data', 'sks' => 3, 'semester' => '4', 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah manajemen dan desain basis data'],
                ['kode_mk' => 'MK005', 'nama_mk' => 'Kecerdasan Buatan', 'sks' => 3, 'semester' => '7', 'kapasitas' => 20, 'deskripsi' => 'Mata kuliah AI dan machine learning'],
                ['kode_mk' => 'MK006', 'nama_mk' => 'Data Mining', 'sks' => 3, 'semester' => '8', 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah penambangan dan analisis data'],
            ],
            // Dosen 3 - Prof. Dr. Budi Santoso
            [
                ['kode_mk' => 'MK007', 'nama_mk' => 'Jaringan Komputer', 'sks' => 3, 'semester' => '6', 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah jaringan dan komunikasi data'],
                ['kode_mk' => 'MK008', 'nama_mk' => 'Keamanan Sistem', 'sks' => 3, 'semester' => '7', 'kapasitas' => 20, 'deskripsi' => 'Mata kuliah keamanan sistem informasi'],
                ['kode_mk' => 'MK009', 'nama_mk' => 'Administrasi Jaringan', 'sks' => 3, 'semester' => '8', 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah administrasi dan manajemen jaringan'],
            ],
            // Dosen 4 - Dr. Rina Sari
            [
                ['kode_mk' => 'MK010', 'nama_mk' => 'Sistem Operasi', 'sks' => 3, 'semester' => '4', 'kapasitas' => 30, 'deskripsi' => 'Mata kuliah sistem operasi komputer'],
                ['kode_mk' => 'MK011', 'nama_mk' => 'Pemrograman Mobile', 'sks' => 4, 'semester' => '6', 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah pengembangan aplikasi mobile'],
                ['kode_mk' => 'MK012', 'nama_mk' => 'Arsitektur Komputer', 'sks' => 3, 'semester' => '3', 'kapasitas' => 30, 'deskripsi' => 'Mata kuliah arsitektur dan organisasi komputer'],
            ],
            // Dosen 5 - Dr. Agus Prasetyo
            [
                ['kode_mk' => 'MK013', 'nama_mk' => 'Rekayasa Perangkat Lunak', 'sks' => 4, 'semester' => '6', 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah pengembangan perangkat lunak'],
                ['kode_mk' => 'MK014', 'nama_mk' => 'Testing dan Quality Assurance', 'sks' => 3, 'semester' => '7', 'kapasitas' => 20, 'deskripsi' => 'Mata kuliah pengujian dan jaminan kualitas software'],
                ['kode_mk' => 'MK015', 'nama_mk' => 'Manajemen Proyek TI', 'sks' => 3, 'semester' => '8', 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah manajemen proyek teknologi informasi'],
            ],
            // Dosen 6 - Dr. Maya Indira
            [
                ['kode_mk' => 'MK016', 'nama_mk' => 'Interaksi Manusia Komputer', 'sks' => 3, 'semester' => '5', 'kapasitas' => 30, 'deskripsi' => 'Mata kuliah desain antarmuka pengguna'],
                ['kode_mk' => 'MK017', 'nama_mk' => 'Desain Grafis', 'sks' => 3, 'semester' => '4', 'kapasitas' => 25, 'deskripsi' => 'Mata kuliah desain grafis dan multimedia'],
                ['kode_mk' => 'MK018', 'nama_mk' => 'Animasi dan Game Development', 'sks' => 4, 'semester' => '7', 'kapasitas' => 20, 'deskripsi' => 'Mata kuliah pengembangan animasi dan game'],
            ],
        ];

        // Preferensi yang berbeda untuk setiap dosen (jam disesuaikan dengan SKS)
        $preferensiData = [
            // Dosen 1 - Dr. Ahmad Wijaya (Prefer pagi hari)
            [
                ['preferensi_hari' => ['Senin', 'Rabu'], 'preferensi_jam' => ['08:00-11:20', '10:00-13:20'], 'prioritas' => 1, 'catatan' => 'Prefer pagi hari untuk mata kuliah dasar (6 SKS = 300 menit)'],
                ['preferensi_hari' => ['Selasa', 'Kamis'], 'preferensi_jam' => ['08:00-10:50', '10:00-12:50'], 'prioritas' => 1, 'catatan' => 'Prefer pagi hari untuk web programming (3 SKS = 150 menit)'],
                ['preferensi_hari' => ['Senin', 'Jumat'], 'preferensi_jam' => ['10:00-12:50', '13:00-15:50'], 'prioritas' => 2, 'catatan' => 'Fleksibel untuk OOP (3 SKS = 150 menit)'],
            ],
            // Dosen 2 - Dr. Siti Nurhaliza (Prefer siang hari)
            [
                ['preferensi_hari' => ['Selasa', 'Kamis'], 'preferensi_jam' => ['13:00-15:50', '15:00-17:50'], 'prioritas' => 1, 'catatan' => 'Prefer siang hari untuk basis data (3 SKS = 150 menit)'],
                ['preferensi_hari' => ['Rabu', 'Jumat'], 'preferensi_jam' => ['13:00-16:20', '15:00-18:20'], 'prioritas' => 1, 'catatan' => 'Prefer siang hari untuk AI (4 SKS = 200 menit)'],
                ['preferensi_hari' => ['Senin', 'Rabu'], 'preferensi_jam' => ['10:00-12:50', '13:00-15:50'], 'prioritas' => 2, 'catatan' => 'Fleksibel untuk data mining (3 SKS = 150 menit)'],
            ],
            // Dosen 3 - Prof. Dr. Budi Santoso (Prefer campuran)
            [
                ['preferensi_hari' => ['Senin', 'Rabu'], 'preferensi_jam' => ['08:00-10:00', '15:00-17:00'], 'prioritas' => 1, 'catatan' => 'Prefer pagi dan sore untuk jaringan'],
                ['preferensi_hari' => ['Selasa', 'Kamis'], 'preferensi_jam' => ['10:00-12:00', '13:00-15:00'], 'prioritas' => 1, 'catatan' => 'Prefer tengah hari untuk keamanan'],
                ['preferensi_hari' => ['Rabu', 'Jumat'], 'preferensi_jam' => ['08:00-10:00', '13:00-15:00'], 'prioritas' => 2, 'catatan' => 'Fleksibel untuk administrasi jaringan'],
            ],
            // Dosen 4 - Dr. Rina Sari (Prefer pagi dan siang)
            [
                ['preferensi_hari' => ['Senin', 'Rabu'], 'preferensi_jam' => ['08:00-10:00', '10:00-12:00'], 'prioritas' => 1, 'catatan' => 'Prefer pagi untuk sistem operasi'],
                ['preferensi_hari' => ['Selasa', 'Kamis'], 'preferensi_jam' => ['13:00-15:00', '15:00-17:00'], 'prioritas' => 1, 'catatan' => 'Prefer siang untuk mobile programming'],
                ['preferensi_hari' => ['Senin', 'Jumat'], 'preferensi_jam' => ['10:00-12:00', '13:00-15:00'], 'prioritas' => 2, 'catatan' => 'Fleksibel untuk arsitektur komputer'],
            ],
            // Dosen 5 - Dr. Agus Prasetyo (Prefer siang dan sore)
            [
                ['preferensi_hari' => ['Selasa', 'Kamis'], 'preferensi_jam' => ['13:00-15:00', '15:00-17:00'], 'prioritas' => 1, 'catatan' => 'Prefer siang untuk RPL'],
                ['preferensi_hari' => ['Rabu', 'Jumat'], 'preferensi_jam' => ['10:00-12:00', '15:00-17:00'], 'prioritas' => 1, 'catatan' => 'Prefer campuran untuk testing'],
                ['preferensi_hari' => ['Senin', 'Rabu'], 'preferensi_jam' => ['13:00-15:00', '15:00-17:00'], 'prioritas' => 2, 'catatan' => 'Fleksibel untuk manajemen proyek'],
            ],
            // Dosen 6 - Dr. Maya Indira (Prefer pagi dan sore)
            [
                ['preferensi_hari' => ['Senin', 'Rabu'], 'preferensi_jam' => ['08:00-10:00', '15:00-17:00'], 'prioritas' => 1, 'catatan' => 'Prefer pagi dan sore untuk HCI'],
                ['preferensi_hari' => ['Selasa', 'Kamis'], 'preferensi_jam' => ['10:00-12:00', '13:00-15:00'], 'prioritas' => 1, 'catatan' => 'Prefer tengah hari untuk desain grafis'],
                ['preferensi_hari' => ['Rabu', 'Jumat'], 'preferensi_jam' => ['08:00-10:00', '13:00-15:00'], 'prioritas' => 2, 'catatan' => 'Fleksibel untuk animasi dan game'],
            ],
        ];

        // Buat mata kuliah dan preferensi untuk setiap dosen
        foreach ($dosens as $index => $dosen) {
            if (isset($mataKuliahData[$index])) {
                $mataKuliahs = $mataKuliahData[$index];
                $preferensi = $preferensiData[$index];

                foreach ($mataKuliahs as $mkIndex => $mkData) {
                    $mkData['dosen_id'] = $dosen->id;
                    $mataKuliah = MataKuliah::create($mkData);

                    // Buat preferensi untuk mata kuliah ini
                    $prefData = $preferensi[$mkIndex];
                    $prefData['dosen_id'] = $dosen->id;
                    $prefData['mata_kuliah_id'] = $mataKuliah->id;
                    PreferensiDosen::create($prefData);
                }
            }
        }

        $this->command->info('Berhasil update semua dosen dengan 3 mata kuliah dan preferensi yang berbeda!');
        $this->command->info('Total mata kuliah: ' . MataKuliah::count());
        $this->command->info('Total preferensi: ' . PreferensiDosen::count());
    }
}