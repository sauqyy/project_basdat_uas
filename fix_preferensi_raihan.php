<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PreferensiDosen;
use App\Models\User;

echo "=== FIX PREFERENSI DOSEN RAIHAN ===\n";

// Cari dosen Raihan
$dosenRaihan = User::where('name', 'LIKE', '%Raihan%')->first();

if (!$dosenRaihan) {
    echo "Dosen Raihan tidak ditemukan!\n";
    exit(1);
}

echo "Dosen ditemukan: " . $dosenRaihan->name . " (ID: " . $dosenRaihan->id . ")\n";

// Cari preferensi global dosen Raihan
$preferensiGlobal = PreferensiDosen::where('dosen_id', $dosenRaihan->id)
    ->whereNull('mata_kuliah_id')
    ->first();

if ($preferensiGlobal) {
    echo "Preferensi global saat ini:\n";
    echo "- Hari: " . json_encode($preferensiGlobal->preferensi_hari) . "\n";
    echo "- Jam: " . json_encode($preferensiGlobal->preferensi_jam) . "\n";
    echo "- Prioritas: " . $preferensiGlobal->prioritas . "\n";
    echo "- Catatan: " . $preferensiGlobal->catatan . "\n";
    
    // Update preferensi sesuai permintaan: Senin, Rabu, Jumat
    $preferensiGlobal->preferensi_hari = ['Senin', 'Rabu', 'Jumat'];
    $preferensiGlobal->preferensi_jam = ['08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00', '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00'];
    $preferensiGlobal->prioritas = 1;
    $preferensiGlobal->catatan = 'Preferensi global untuk Raihan - Senin, Rabu, Jumat';
    $preferensiGlobal->save();
    
    echo "\nPreferensi berhasil diupdate!\n";
    echo "Preferensi baru:\n";
    echo "- Hari: " . json_encode($preferensiGlobal->preferensi_hari) . "\n";
    echo "- Jam: " . json_encode($preferensiGlobal->preferensi_jam) . "\n";
    echo "- Prioritas: " . $preferensiGlobal->prioritas . "\n";
    echo "- Catatan: " . $preferensiGlobal->catatan . "\n";
} else {
    echo "Tidak ada preferensi global untuk dosen Raihan. Membuat preferensi baru...\n";
    
    $preferensiBaru = PreferensiDosen::create([
        'dosen_id' => $dosenRaihan->id,
        'mata_kuliah_id' => null,
        'preferensi_hari' => ['Senin', 'Rabu', 'Jumat'],
        'preferensi_jam' => ['08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00', '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00'],
        'prioritas' => 1,
        'catatan' => 'Preferensi global untuk Raihan - Senin, Rabu, Jumat'
    ]);
    
    echo "Preferensi baru berhasil dibuat!\n";
    echo "Preferensi:\n";
    echo "- Hari: " . json_encode($preferensiBaru->preferensi_hari) . "\n";
    echo "- Jam: " . json_encode($preferensiBaru->preferensi_jam) . "\n";
    echo "- Prioritas: " . $preferensiBaru->prioritas . "\n";
    echo "- Catatan: " . $preferensiBaru->catatan . "\n";
}

echo "\n=== SELESAI ===\n";
echo "Sekarang coba generate jadwal lagi untuk melihat apakah preferensi diikuti dengan benar.\n";

