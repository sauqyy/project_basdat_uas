<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DimProdi;
use App\Models\User;
use App\Models\MataKuliah;
use App\Models\Jadwal;
use App\Models\Ruangan;

echo "=== Data Prodi di Database ===\n\n";

echo "1. DimProdi (Data Warehouse):\n";
$dimProdi = DimProdi::where('is_active', true)->get();
echo "   Total: " . $dimProdi->count() . " records\n";
foreach ($dimProdi as $p) {
    echo "   - {$p->nama_prodi} ({$p->kode_prodi}) - Key: {$p->prodi_key}\n";
}

echo "\n2. Unique Prodi di Tabel Operasional:\n";
echo "   Users: " . User::whereNotNull('prodi')->distinct('prodi')->count('prodi') . "\n";
echo "   MataKuliah: " . MataKuliah::whereNotNull('prodi')->distinct('prodi')->count('prodi') . "\n";
echo "   Jadwal: " . Jadwal::whereNotNull('prodi')->distinct('prodi')->count('prodi') . "\n";
echo "   Ruangan: " . Ruangan::whereNotNull('prodi')->distinct('prodi')->count('prodi') . "\n";

echo "\n3. List Prodi dari Users:\n";
$prodiUsers = User::whereNotNull('prodi')->distinct('prodi')->pluck('prodi');
foreach ($prodiUsers as $prodi) {
    echo "   - {$prodi}\n";
}

echo "\n4. List Prodi dari MataKuliah:\n";
$prodiMK = MataKuliah::whereNotNull('prodi')->distinct('prodi')->pluck('prodi');
foreach ($prodiMK as $prodi) {
    echo "   - {$prodi}\n";
}

echo "\n=== Selesai ===\n";

