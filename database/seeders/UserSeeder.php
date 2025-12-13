<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Mahasiswa',
            'email' => 'mhs@mail.com',
            'password' => Hash::make('mhs123'),
            'role' => 'mahasiswa',
            'nim' => '164231107',
        ]);

        User::create([
            'name' => 'Dosen',
            'email' => 'dosen@mail.com',
            'password' => Hash::make('dosen123'),
            'role' => 'dosen',
            'nip' => '19890101',
        ]);
    }
}
