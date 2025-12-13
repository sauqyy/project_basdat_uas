<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MataKuliah;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodiList = [
            'Teknologi Sains Data',
            'Rekayasa Nanoteknologi',
            'Teknik Industri',
            'Teknik Elektro',
            'Teknik Robotika dan Kecerdasan Buatan'
        ];

        // Create dosen untuk setiap prodi
        $dosenPerProdi = [];
        foreach ($prodiList as $prodi) {
            $dosenPerProdi[$prodi] = [];
            
            // Buat 3-4 dosen per prodi
            for ($i = 1; $i <= 4; $i++) {
                $email = strtolower(str_replace(' ', '', $prodi)) . "dosen{$i}@example.com";
                $nip = 'NIP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                
                // Cek apakah dosen sudah ada
                $existingDosen = User::where('email', $email)->first();
                if ($existingDosen) {
                    $dosenPerProdi[$prodi][] = $existingDosen;
                    continue;
                }
                
                $dosen = User::create([
                    'name' => "Dosen {$prodi} {$i}",
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'dosen',
                    'nip' => $nip,
                    'prodi' => $prodi
                ]);
                
                $dosenPerProdi[$prodi][] = $dosen;
            }
        }

        // Create ruangan per prodi dan ruangan umum
        $ruanganPerProdi = [];
        
        // Ruangan per prodi
        foreach ($prodiList as $prodi) {
            $ruanganPerProdi[$prodi] = [];
            
            // 3 ruangan kelas per prodi
            for ($i = 1; $i <= 3; $i++) {
                $kodeRuangan = strtoupper(substr(str_replace(' ', '', $prodi), 0, 3)) . "K{$i}";
                
                // Cek apakah ruangan sudah ada
                $existingRuangan = Ruangan::where('kode_ruangan', $kodeRuangan)->first();
                if ($existingRuangan) {
                    $ruanganPerProdi[$prodi][] = $existingRuangan;
                    continue;
                }
                
                $ruangan = Ruangan::create([
                    'kode_ruangan' => $kodeRuangan,
                    'nama_ruangan' => "Kelas {$prodi} {$i}",
                    'kapasitas' => rand(30, 50),
                    'tipe_ruangan' => 'kelas',
                    'fasilitas' => 'AC, Proyektor, Whiteboard',
                    'status' => true,
                    'prodi' => $prodi
                ]);
                
                $ruanganPerProdi[$prodi][] = $ruangan;
            }
            
            // 1 lab per prodi
            $kodeLab = strtoupper(substr(str_replace(' ', '', $prodi), 0, 3)) . "L1";
            $existingLab = Ruangan::where('kode_ruangan', $kodeLab)->first();
            if (!$existingLab) {
                $lab = Ruangan::create([
                    'kode_ruangan' => $kodeLab,
                    'nama_ruangan' => "Lab {$prodi}",
                    'kapasitas' => rand(20, 30),
                    'tipe_ruangan' => 'lab',
                    'fasilitas' => 'AC, Komputer, Proyektor, Whiteboard',
                    'status' => true,
                    'prodi' => $prodi
                ]);
                
                $ruanganPerProdi[$prodi][] = $lab;
            } else {
                $ruanganPerProdi[$prodi][] = $existingLab;
            }
        }

        // Ruangan umum (untuk semua prodi)
        $ruanganUmum = [];
        
        // 2 auditorium
        for ($i = 1; $i <= 2; $i++) {
            $kodeAuditorium = "AUD{$i}";
            $existingAuditorium = Ruangan::where('kode_ruangan', $kodeAuditorium)->first();
            if (!$existingAuditorium) {
                $auditorium = Ruangan::create([
                    'kode_ruangan' => $kodeAuditorium,
                    'nama_ruangan' => "Auditorium {$i}",
                    'kapasitas' => rand(100, 200),
                    'tipe_ruangan' => 'auditorium',
                    'fasilitas' => 'AC, Proyektor, Sound System, Podium',
                    'status' => true,
                    'prodi' => null // Umum
                ]);
                
                $ruanganUmum[] = $auditorium;
            } else {
                $ruanganUmum[] = $existingAuditorium;
            }
        }
        
        // 3 ruangan kelas umum
        for ($i = 1; $i <= 3; $i++) {
            $kodeKelasUmum = "UMUM{$i}";
            $existingKelasUmum = Ruangan::where('kode_ruangan', $kodeKelasUmum)->first();
            if (!$existingKelasUmum) {
                $kelasUmum = Ruangan::create([
                    'kode_ruangan' => $kodeKelasUmum,
                    'nama_ruangan' => "Kelas Umum {$i}",
                    'kapasitas' => rand(40, 60),
                    'tipe_ruangan' => 'kelas',
                    'fasilitas' => 'AC, Proyektor, Whiteboard',
                    'status' => true,
                    'prodi' => null // Umum
                ]);
                
                $ruanganUmum[] = $kelasUmum;
            } else {
                $ruanganUmum[] = $existingKelasUmum;
            }
        }

        // Create mata kuliah untuk setiap prodi
        $mataKuliahData = [
            'Teknologi Sains Data' => [
                ['kode' => 'TSD101', 'nama' => 'Matematika Dasar', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TSD102', 'nama' => 'Statistika Dasar', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TSD201', 'nama' => 'Pemrograman Python', 'sks' => 4, 'semester' => '2', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TSD202', 'nama' => 'Data Mining', 'sks' => 3, 'semester' => '2', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TSD301', 'nama' => 'Machine Learning', 'sks' => 4, 'semester' => '3', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TSD302', 'nama' => 'Big Data Analytics', 'sks' => 3, 'semester' => '3', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TSD401', 'nama' => 'Deep Learning', 'sks' => 4, 'semester' => '4', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TSD402', 'nama' => 'Visualisasi Data', 'sks' => 3, 'semester' => '4', 'tipe' => 'praktikum', 'menit' => 60]
            ],
            'Rekayasa Nanoteknologi' => [
                ['kode' => 'RNT101', 'nama' => 'Fisika Dasar', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'RNT102', 'nama' => 'Kimia Dasar', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'RNT201', 'nama' => 'Nanomaterial', 'sks' => 4, 'semester' => '2', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'RNT202', 'nama' => 'Karakterisasi Material', 'sks' => 3, 'semester' => '2', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'RNT301', 'nama' => 'Sintesis Nanopartikel', 'sks' => 4, 'semester' => '3', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'RNT302', 'nama' => 'Nanobiologi', 'sks' => 3, 'semester' => '3', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'RNT401', 'nama' => 'Aplikasi Nanoteknologi', 'sks' => 4, 'semester' => '4', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'RNT402', 'nama' => 'Nanomedicine', 'sks' => 3, 'semester' => '4', 'tipe' => 'teori', 'menit' => 50]
            ],
            'Teknik Industri' => [
                ['kode' => 'TIN101', 'nama' => 'Matematika Teknik', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TIN102', 'nama' => 'Fisika Teknik', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TIN201', 'nama' => 'Ergonomi', 'sks' => 3, 'semester' => '2', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TIN202', 'nama' => 'Manajemen Operasi', 'sks' => 4, 'semester' => '2', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TIN301', 'nama' => 'Sistem Produksi', 'sks' => 4, 'semester' => '3', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TIN302', 'nama' => 'Kontrol Kualitas', 'sks' => 3, 'semester' => '3', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TIN401', 'nama' => 'Optimasi Sistem', 'sks' => 4, 'semester' => '4', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TIN402', 'nama' => 'Manajemen Proyek', 'sks' => 3, 'semester' => '4', 'tipe' => 'teori', 'menit' => 50]
            ],
            'Teknik Elektro' => [
                ['kode' => 'TEL101', 'nama' => 'Matematika Teknik', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TEL102', 'nama' => 'Fisika Dasar', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TEL201', 'nama' => 'Rangkaian Listrik', 'sks' => 4, 'semester' => '2', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TEL202', 'nama' => 'Elektronika Dasar', 'sks' => 3, 'semester' => '2', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TEL301', 'nama' => 'Sistem Kontrol', 'sks' => 4, 'semester' => '3', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TEL302', 'nama' => 'Telekomunikasi', 'sks' => 3, 'semester' => '3', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TEL401', 'nama' => 'Sistem Tenaga', 'sks' => 4, 'semester' => '4', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TEL402', 'nama' => 'Instrumentasi', 'sks' => 3, 'semester' => '4', 'tipe' => 'praktikum', 'menit' => 60]
            ],
            'Teknik Robotika dan Kecerdasan Buatan' => [
                ['kode' => 'TRK101', 'nama' => 'Matematika Dasar', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TRK102', 'nama' => 'Fisika Dasar', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TRK201', 'nama' => 'Pemrograman Robot', 'sks' => 4, 'semester' => '2', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TRK202', 'nama' => 'Kecerdasan Buatan', 'sks' => 3, 'semester' => '2', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TRK301', 'nama' => 'Computer Vision', 'sks' => 4, 'semester' => '3', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TRK302', 'nama' => 'Machine Learning', 'sks' => 3, 'semester' => '3', 'tipe' => 'teori', 'menit' => 50],
                ['kode' => 'TRK401', 'nama' => 'Robotika Lanjut', 'sks' => 4, 'semester' => '4', 'tipe' => 'praktikum', 'menit' => 60],
                ['kode' => 'TRK402', 'nama' => 'Sistem Otonom', 'sks' => 3, 'semester' => '4', 'tipe' => 'praktikum', 'menit' => 60]
            ]
        ];

        foreach ($prodiList as $prodi) {
            $dosenList = $dosenPerProdi[$prodi];
            $mataKuliahList = $mataKuliahData[$prodi];
            
            foreach ($mataKuliahList as $mkData) {
                // Cek apakah mata kuliah sudah ada
                $existingMk = MataKuliah::where('kode_mk', $mkData['kode'])->first();
                if ($existingMk) {
                    continue;
                }
                
                // Pilih dosen secara random dari prodi yang sama
                $dosen = $dosenList[array_rand($dosenList)];
                
                MataKuliah::create([
                    'kode_mk' => $mkData['kode'],
                    'nama_mk' => $mkData['nama'],
                    'sks' => $mkData['sks'],
                    'semester' => $mkData['semester'],
                    'dosen_id' => $dosen->id,
                    'kapasitas' => rand(25, 40),
                    'deskripsi' => "Mata kuliah {$mkData['nama']} untuk program studi {$prodi}",
                    'tipe_kelas' => $mkData['tipe'],
                    'menit_per_sks' => $mkData['menit'],
                    'prodi' => $prodi
                ]);
            }
        }

        $this->command->info('Dummy data created successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . User::where('role', 'dosen')->count() . ' dosen');
        $this->command->info('- ' . MataKuliah::count() . ' mata kuliah');
        $this->command->info('- ' . Ruangan::count() . ' ruangan');
        
        foreach ($prodiList as $prodi) {
            $mkCount = MataKuliah::where('prodi', $prodi)->count();
            $ruanganCount = Ruangan::where('prodi', $prodi)->count();
            $this->command->info("  {$prodi}: {$mkCount} mata kuliah, {$ruanganCount} ruangan");
        }
        
        $ruanganUmumCount = Ruangan::whereNull('prodi')->count();
        $this->command->info("  Ruangan Umum: {$ruanganUmumCount} ruangan");
    }
}
