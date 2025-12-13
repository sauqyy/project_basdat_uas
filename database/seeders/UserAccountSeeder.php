<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin_super',
            'nip' => 'SA001',
        ]);

        // Admin Prodi - Teknologi Sains Data
        User::create([
            'name' => 'Admin TSD',
            'email' => 'admin.tsd@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin_prodi',
            'nip' => 'AP001',
            'prodi' => 'Teknologi Sains Data',
        ]);

        // Admin Prodi - Rekayasa Nanoteknologi
        User::create([
            'name' => 'Admin RN',
            'email' => 'admin.rn@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin_prodi',
            'nip' => 'AP002',
            'prodi' => 'Rekayasa Nanoteknologi',
        ]);

        // Admin Prodi - Teknik Industri
        User::create([
            'name' => 'Admin TI',
            'email' => 'admin.ti@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin_prodi',
            'nip' => 'AP003',
            'prodi' => 'Teknik Industri',
        ]);

        // Admin Prodi - Teknik Elektro
        User::create([
            'name' => 'Admin TE',
            'email' => 'admin.te@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin_prodi',
            'nip' => 'AP004',
            'prodi' => 'Teknik Elektro',
        ]);

        // Admin Prodi - Teknik Robotika dan Kecerdasan Buatan
        User::create([
            'name' => 'Admin TRKB',
            'email' => 'admin.trkb@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin_prodi',
            'nip' => 'AP005',
            'prodi' => 'Teknik Robotika dan Kecerdasan Buatan',
        ]);

        // Dosen - Teknologi Sains Data
        User::create([
            'name' => 'Dr. Ahmad Data',
            'email' => 'ahmad.data@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'D001',
        ]);

        User::create([
            'name' => 'Dr. Siti Analytics',
            'email' => 'siti.analytics@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'D002',
        ]);

        // Dosen - Rekayasa Nanoteknologi
        User::create([
            'name' => 'Prof. Budi Nano',
            'email' => 'budi.nano@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'D003',
        ]);

        User::create([
            'name' => 'Dr. Citra Material',
            'email' => 'citra.material@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'D004',
        ]);

        // Dosen - Teknik Industri
        User::create([
            'name' => 'Dr. Dedi Sistem',
            'email' => 'dedi.sistem@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'D005',
        ]);

        User::create([
            'name' => 'Dr. Eva Optimasi',
            'email' => 'eva.optimasi@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'D006',
        ]);

        // Dosen - Teknik Elektro
        User::create([
            'name' => 'Prof. Fajar Circuit',
            'email' => 'fajar.circuit@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'D007',
        ]);

        User::create([
            'name' => 'Dr. Gita Power',
            'email' => 'gita.power@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'D008',
        ]);

        // Dosen - Teknik Robotika dan Kecerdasan Buatan
        User::create([
            'name' => 'Dr. Hadi Robot',
            'email' => 'hadi.robot@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'D009',
        ]);

        User::create([
            'name' => 'Dr. Indah AI',
            'email' => 'indah.ai@kampusmerdeka.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nip' => 'D010',
        ]);
    }
}