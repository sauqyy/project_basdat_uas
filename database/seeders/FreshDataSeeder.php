<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Ruangan;
use App\Models\MataKuliah;
use App\Models\PreferensiDosen;
use App\Models\Jadwal;

class FreshDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🗑️  Menghapus semua data lama...');
        
        // Nonaktifkan foreign key check untuk SQLite
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        }
        
        // Hapus semua data (dalam urutan yang benar untuk foreign key)
        Jadwal::query()->delete();
        PreferensiDosen::query()->delete();
        MataKuliah::query()->delete();
        Ruangan::query()->delete();
        User::query()->delete();
        
        // Reset auto increment untuk SQLite
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('DELETE FROM sqlite_sequence WHERE name IN ("users", "ruangans", "mata_kuliahs", "preferensi_dosens", "jadwals")');
            DB::statement('PRAGMA foreign_keys = ON');
        }
        
        $this->command->info('✅ Data lama berhasil dihapus');
        $this->command->info('');
        
        $this->command->info('👤 Membuat akun Super Admin dan Admin Prodi...');
        $this->seedAdminAccounts();
        
        $this->command->info('👨‍🏫 Membuat akun Dosen...');
        $dosens = $this->seedDosenAccounts();
        
        $this->command->info('🏢 Membuat Ruangan...');
        $this->seedRuangan();
        
        $this->command->info('📚 Membuat Mata Kuliah...');
        $mataKuliahs = $this->seedMataKuliah($dosens);
        
        $this->command->info('⭐ Membuat Preferensi Dosen...');
        $this->seedPreferensiDosen($dosens, $mataKuliahs);
        
        $this->command->info('✅ Seeding selesai!');
    }
    
    private function seedAdminAccounts()
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin_super',
            'nip' => 'SA001',
        ]);
        
        // Admin Prodi untuk setiap prodi
        $adminProdi = [
            ['name' => 'Admin TSD', 'email' => 'admin.tsd@kampusmerdeka.ac.id', 'nip' => 'AP001', 'prodi' => 'Teknologi Sains Data'],
            ['name' => 'Admin RN', 'email' => 'admin.rn@kampusmerdeka.ac.id', 'nip' => 'AP002', 'prodi' => 'Rekayasa Nanoteknologi'],
            ['name' => 'Admin TI', 'email' => 'admin.ti@kampusmerdeka.ac.id', 'nip' => 'AP003', 'prodi' => 'Teknik Industri'],
            ['name' => 'Admin TE', 'email' => 'admin.te@kampusmerdeka.ac.id', 'nip' => 'AP004', 'prodi' => 'Teknik Elektro'],
            ['name' => 'Admin TRKB', 'email' => 'admin.trkb@kampusmerdeka.ac.id', 'nip' => 'AP005', 'prodi' => 'Teknik Robotika dan Kecerdasan Buatan'],
        ];
        
        foreach ($adminProdi as $admin) {
            User::create([
                'name' => $admin['name'],
                'email' => $admin['email'],
                'password' => Hash::make('password'),
                'role' => 'admin_prodi',
                'nip' => $admin['nip'],
                'prodi' => $admin['prodi'],
            ]);
        }
    }
    
    private function seedDosenAccounts()
    {
        $prodiList = [
            'Teknologi Sains Data',
            'Rekayasa Nanoteknologi',
            'Teknik Industri',
            'Teknik Elektro',
            'Teknik Robotika dan Kecerdasan Buatan'
        ];
        
        $dosens = [];
        $dosenCounter = 1;
        
        foreach ($prodiList as $prodi) {
            $prodiShort = $this->getProdiShort($prodi);
            
            // Buat 4 dosen per prodi
            for ($i = 1; $i <= 4; $i++) {
                $dosen = User::create([
                    'name' => "Dr. {$prodiShort} Dosen {$i}, S.Kom., M.T.",
                    'email' => strtolower(str_replace(' ', '', $prodiShort)) . ".dosen{$i}@kampusmerdeka.ac.id",
                    'password' => Hash::make('password'),
                    'role' => 'dosen',
                    'nip' => 'DSN' . str_pad($dosenCounter, 3, '0', STR_PAD_LEFT),
                    'prodi' => $prodi,
                ]);
                
                $dosens[$prodi][] = $dosen;
                $dosenCounter++;
            }
        }
        
        return $dosens;
    }
    
    private function getProdiShort($prodi)
    {
        $map = [
            'Teknologi Sains Data' => 'TSD',
            'Rekayasa Nanoteknologi' => 'RN',
            'Teknik Industri' => 'TI',
            'Teknik Elektro' => 'TE',
            'Teknik Robotika dan Kecerdasan Buatan' => 'TRKB',
        ];
        
        return $map[$prodi] ?? 'UNK';
    }
    
    private function seedRuangan()
    {
        $prodiList = [
            'Teknologi Sains Data',
            'Rekayasa Nanoteknologi',
            'Teknik Industri',
            'Teknik Elektro',
            'Teknik Robotika dan Kecerdasan Buatan'
        ];
        
        $ruanganCounter = 1;
        
        foreach ($prodiList as $prodi) {
            $prodiShort = $this->getProdiShort($prodi);
            
            // Buat 3 kelas per prodi
            for ($i = 1; $i <= 3; $i++) {
                Ruangan::create([
                    'kode_ruangan' => $prodiShort . '-K' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'nama_ruangan' => "Kelas {$prodiShort} {$i}",
                    'kapasitas' => rand(30, 50),
                    'tipe_ruangan' => 'kelas',
                    'fasilitas' => 'AC, Proyektor, Whiteboard, Sound System',
                    'status' => true,
                    'prodi' => $prodi,
                ]);
                $ruanganCounter++;
            }
            
            // Buat 2 lab per prodi
            for ($i = 1; $i <= 2; $i++) {
                Ruangan::create([
                    'kode_ruangan' => $prodiShort . '-LAB' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'nama_ruangan' => "Lab {$prodiShort} {$i}",
                    'kapasitas' => rand(25, 40),
                    'tipe_ruangan' => 'lab',
                    'fasilitas' => 'AC, Proyektor, Komputer, Internet',
                    'status' => true,
                    'prodi' => $prodi,
                ]);
                $ruanganCounter++;
            }
        }
        
        // Buat 2 auditorium umum
        Ruangan::create([
            'kode_ruangan' => 'AUD-001',
            'nama_ruangan' => 'Auditorium Utama',
            'kapasitas' => 150,
            'tipe_ruangan' => 'auditorium',
            'fasilitas' => 'AC, Proyektor, Sound System, Panggung',
            'status' => true,
            'prodi' => null,
        ]);
        
        Ruangan::create([
            'kode_ruangan' => 'AUD-002',
            'nama_ruangan' => 'Auditorium Kecil',
            'kapasitas' => 80,
            'tipe_ruangan' => 'auditorium',
            'fasilitas' => 'AC, Proyektor, Sound System',
            'status' => true,
            'prodi' => null,
        ]);
    }
    
    private function seedMataKuliah($dosens)
    {
        $mataKuliahs = [];
        $mataKuliahData = [
            'Teknologi Sains Data' => [
                ['kode' => 'TSD001', 'nama' => 'Pemrograman Dasar', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori'],
                ['kode' => 'TSD002', 'nama' => 'Struktur Data', 'sks' => 3, 'semester' => '2', 'tipe' => 'teori'],
                ['kode' => 'TSD003', 'nama' => 'Basis Data', 'sks' => 4, 'semester' => '3', 'tipe' => 'praktikum', 'sks_materi' => 2, 'sks_praktikum' => 2],
                ['kode' => 'TSD004', 'nama' => 'Data Mining', 'sks' => 3, 'semester' => '4', 'tipe' => 'teori'],
                ['kode' => 'TSD005', 'nama' => 'Machine Learning', 'sks' => 4, 'semester' => '5', 'tipe' => 'praktikum', 'sks_materi' => 2, 'sks_praktikum' => 2],
                ['kode' => 'TSD006', 'nama' => 'Big Data Analytics', 'sks' => 3, 'semester' => '6', 'tipe' => 'teori'],
            ],
            'Rekayasa Nanoteknologi' => [
                ['kode' => 'RN001', 'nama' => 'Kimia Dasar', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori'],
                ['kode' => 'RN002', 'nama' => 'Fisika Material', 'sks' => 3, 'semester' => '2', 'tipe' => 'teori'],
                ['kode' => 'RN003', 'nama' => 'Sintesis Nanomaterial', 'sks' => 4, 'semester' => '3', 'tipe' => 'praktikum', 'sks_materi' => 2, 'sks_praktikum' => 2],
                ['kode' => 'RN004', 'nama' => 'Karakterisasi Material', 'sks' => 3, 'semester' => '4', 'tipe' => 'teori'],
                ['kode' => 'RN005', 'nama' => 'Nanoteknologi Terapan', 'sks' => 4, 'semester' => '5', 'tipe' => 'praktikum', 'sks_materi' => 2, 'sks_praktikum' => 2],
            ],
            'Teknik Industri' => [
                ['kode' => 'TI001', 'nama' => 'Pengantar Teknik Industri', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori'],
                ['kode' => 'TI002', 'nama' => 'Statistika Industri', 'sks' => 3, 'semester' => '2', 'tipe' => 'teori'],
                ['kode' => 'TI003', 'nama' => 'Sistem Produksi', 'sks' => 4, 'semester' => '3', 'tipe' => 'praktikum', 'sks_materi' => 2, 'sks_praktikum' => 2],
                ['kode' => 'TI004', 'nama' => 'Manajemen Operasi', 'sks' => 3, 'semester' => '4', 'tipe' => 'teori'],
                ['kode' => 'TI005', 'nama' => 'Optimasi Sistem', 'sks' => 3, 'semester' => '5', 'tipe' => 'teori'],
            ],
            'Teknik Elektro' => [
                ['kode' => 'TE001', 'nama' => 'Rangkaian Listrik', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori'],
                ['kode' => 'TE002', 'nama' => 'Elektronika Dasar', 'sks' => 3, 'semester' => '2', 'tipe' => 'teori'],
                ['kode' => 'TE003', 'nama' => 'Praktikum Elektronika', 'sks' => 4, 'semester' => '3', 'tipe' => 'praktikum', 'sks_materi' => 2, 'sks_praktikum' => 2],
                ['kode' => 'TE004', 'nama' => 'Sistem Tenaga Listrik', 'sks' => 3, 'semester' => '4', 'tipe' => 'teori'],
                ['kode' => 'TE005', 'nama' => 'Kontrol Sistem', 'sks' => 3, 'semester' => '5', 'tipe' => 'teori'],
            ],
            'Teknik Robotika dan Kecerdasan Buatan' => [
                ['kode' => 'TRKB001', 'nama' => 'Pengantar Robotika', 'sks' => 3, 'semester' => '1', 'tipe' => 'teori'],
                ['kode' => 'TRKB002', 'nama' => 'Pemrograman Robot', 'sks' => 3, 'semester' => '2', 'tipe' => 'teori'],
                ['kode' => 'TRKB003', 'nama' => 'Praktikum Robotika', 'sks' => 4, 'semester' => '3', 'tipe' => 'praktikum', 'sks_materi' => 2, 'sks_praktikum' => 2],
                ['kode' => 'TRKB004', 'nama' => 'Kecerdasan Buatan', 'sks' => 3, 'semester' => '4', 'tipe' => 'teori'],
                ['kode' => 'TRKB005', 'nama' => 'Sistem Kendali Robot', 'sks' => 4, 'semester' => '5', 'tipe' => 'praktikum', 'sks_materi' => 2, 'sks_praktikum' => 2],
            ],
        ];
        
        foreach ($mataKuliahData as $prodi => $mataKuliahs) {
            foreach ($mataKuliahs as $index => $mk) {
                // Assign dosen secara round-robin
                $dosenIndex = $index % count($dosens[$prodi]);
                $dosen = $dosens[$prodi][$dosenIndex];
                
                $data = [
                    'kode_mk' => $mk['kode'],
                    'nama_mk' => $mk['nama'],
                    'sks' => $mk['sks'],
                    'semester' => $mk['semester'],
                    'dosen_id' => $dosen->id,
                    'kapasitas' => rand(25, 40),
                    'tipe_kelas' => $mk['tipe'],
                    'menit_per_sks' => 50,
                    'deskripsi' => "Mata kuliah {$mk['nama']} untuk program studi {$prodi}",
                    'prodi' => $prodi,
                ];
                
                if ($mk['tipe'] === 'praktikum') {
                    $data['ada_praktikum'] = true;
                    $data['sks_materi'] = $mk['sks_materi'] ?? 2;
                    $data['sks_praktikum'] = $mk['sks_praktikum'] ?? 2;
                } else {
                    $data['ada_praktikum'] = false;
                    $data['sks_materi'] = 0;
                    $data['sks_praktikum'] = 0;
                }
                
                $mataKuliah = MataKuliah::create($data);
                $mataKuliahs[$prodi][] = $mataKuliah;
            }
        }
        
        return $mataKuliahs;
    }
    
    private function seedPreferensiDosen($dosens, $mataKuliahs)
    {
        // Opsi hari (minimal 3 hari)
        $hariOptions = [
            ['Senin', 'Selasa', 'Rabu'],
            ['Selasa', 'Rabu', 'Kamis'],
            ['Rabu', 'Kamis', 'Jumat'],
            ['Senin', 'Rabu', 'Jumat'],
            ['Senin', 'Selasa', 'Kamis'],
            ['Selasa', 'Kamis', 'Jumat'],
            ['Senin', 'Rabu', 'Kamis'],
            ['Selasa', 'Rabu', 'Jumat'],
        ];
        
        // Opsi jam (minimal 3 pilihan jam)
        $jamOptions = [
            ['08:00-09:00', '09:00-10:00', '10:00-11:00', '13:00-14:00', '14:00-15:00'],
            ['08:00-09:00', '10:00-11:00', '13:00-14:00', '14:00-15:00', '15:00-16:00'],
            ['09:00-10:00', '10:00-11:00', '11:00-12:00', '13:00-14:00', '15:00-16:00'],
            ['08:00-09:00', '09:00-10:00', '13:00-14:00', '14:00-15:00', '16:00-17:00'],
            ['08:00-09:00', '10:00-11:00', '11:00-12:00', '14:00-15:00', '15:00-16:00'],
        ];
        
        $hariIndex = 0;
        $jamIndex = 0;
        
        foreach ($dosens as $prodi => $dosenList) {
            foreach ($dosenList as $dosenIndex => $dosen) {
                // Pilih 3 hari untuk preferensi global
                $hariTerpilih = $hariOptions[$hariIndex % count($hariOptions)];
                $hariIndex++;
                
                // Pilih minimal 3 jam untuk preferensi global
                $jamTerpilih = $jamOptions[$jamIndex % count($jamOptions)];
                $jamIndex++;
                
                // Buat preferensi global untuk dosen
                PreferensiDosen::create([
                    'dosen_id' => $dosen->id,
                    'mata_kuliah_id' => null, // Global preferensi
                    'preferensi_hari' => $hariTerpilih,
                    'preferensi_jam' => $jamTerpilih,
                    'prioritas' => 1,
                    'catatan' => "Preferensi global untuk {$dosen->name} - {$prodi}"
                ]);
                
                // Buat preferensi spesifik untuk setiap mata kuliah dosen ini
                if (isset($mataKuliahs[$prodi])) {
                    $mataKuliahDosen = array_filter($mataKuliahs[$prodi], function($mk) use ($dosen) {
                        return $mk->dosen_id == $dosen->id;
                    });
                    
                    foreach ($mataKuliahDosen as $mkIndex => $mk) {
                        // Variasi preferensi untuk setiap mata kuliah
                        $hariMK = $hariOptions[($hariIndex + $mkIndex) % count($hariOptions)];
                        $jamMK = $jamOptions[($jamIndex + $mkIndex) % count($jamOptions)];
                        
                        PreferensiDosen::create([
                            'dosen_id' => $dosen->id,
                            'mata_kuliah_id' => $mk->id,
                            'preferensi_hari' => $hariMK,
                            'preferensi_jam' => $jamMK,
                            'prioritas' => 1,
                            'catatan' => "Preferensi untuk {$mk->nama_mk}"
                        ]);
                    }
                }
            }
        }
        
        $totalPreferensi = PreferensiDosen::count();
        $this->command->info("✅ Total preferensi dibuat: {$totalPreferensi}");
    }
}

