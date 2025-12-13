<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\DimDosen;
use App\Models\FactKecocokanJadwal;

$prodi = 'Teknologi Sains Data';

echo "=== Verifikasi Kepuasan Dosen untuk Prodi: {$prodi} ===\n\n";

// 1. Cek dosen di prodi
$dosenProdi = User::where('role', 'dosen')->where('prodi', $prodi)->pluck('nip')->toArray();
echo "1. Dosen NIPs untuk {$prodi}: " . count($dosenProdi) . "\n";
echo "   NIPs: " . implode(', ', $dosenProdi) . "\n\n";

// 2. Cek dim_dosen
$dimDosenProdi = DimDosen::whereIn('nip', $dosenProdi)
    ->where('is_active', true)
    ->pluck('dosen_key')
    ->toArray();
echo "2. DimDosen Keys untuk {$prodi}: " . count($dimDosenProdi) . "\n";
echo "   Keys: " . implode(', ', array_slice($dimDosenProdi, 0, 5)) . "...\n\n";

// 3. Cek FactKecocokanJadwal
$factKecocokan = FactKecocokanJadwal::whereIn('dosen_key', $dimDosenProdi)->get();
echo "3. FactKecocokanJadwal untuk {$prodi}: " . $factKecocokan->count() . "\n";

if ($factKecocokan->count() > 0) {
    $avgKecocokan = $factKecocokan->avg('persentase_kecocokan');
    echo "   Avg Persentase: " . number_format($avgKecocokan, 2) . "%\n";
    echo "   Preferensi Terpenuhi: " . $factKecocokan->where('preferensi_hari_terpenuhi', true)->where('preferensi_jam_terpenuhi', true)->count() . "\n";
    
    // Sample data
    echo "\n   Sample Data (3 pertama):\n";
    foreach ($factKecocokan->take(3) as $item) {
        echo "   - Persentase: " . $item->persentase_kecocokan . "%, Hari: " . ($item->preferensi_hari_terpenuhi ? 'Ya' : 'Tidak') . ", Jam: " . ($item->preferensi_jam_terpenuhi ? 'Ya' : 'Tidak') . "\n";
    }
} else {
    echo "   ⚠️ TIDAK ADA DATA!\n";
    echo "   Kemungkinan masalah:\n";
    echo "   - DimDosen tidak match dengan dosen di prodi\n";
    echo "   - FactKecocokanJadwal belum ter-populate untuk dosen ini\n";
}

echo "\n=== Selesai ===\n";

