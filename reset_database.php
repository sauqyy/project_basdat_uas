<?php

/**
 * Script untuk reset database dan seed ulang dengan data baru
 * 
 * Usage: php reset_database.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "  RESET DATABASE & SEED DATA BARU\n";
echo "========================================\n\n";

echo "⚠️  PERINGATAN: Semua data akan dihapus!\n";
echo "Tekan Enter untuk melanjutkan atau Ctrl+C untuk membatalkan...\n";
readline();

try {
    echo "\n🗑️  Menghapus semua tabel...\n";
    Artisan::call('migrate:fresh', ['--force' => true]);
    echo "✅ Migrasi fresh selesai\n\n";
    
    echo "🌱 Menjalankan seeder...\n";
    Artisan::call('db:seed', [
        '--class' => 'FreshDataSeeder',
        '--force' => true
    ]);
    echo "✅ Seeding selesai\n\n";
    
    echo "========================================\n";
    echo "  ✅ RESET DATABASE BERHASIL!\n";
    echo "========================================\n\n";
    
    echo "📊 RINGKASAN DATA:\n";
    echo "- Super Admin: 1 akun\n";
    echo "- Admin Prodi: 5 akun\n";
    echo "- Dosen: 20 akun (4 per prodi)\n";
    echo "- Ruangan: ~27 ruangan (kelas + lab per prodi + auditorium)\n";
    echo "- Mata Kuliah: ~26 mata kuliah\n\n";
    
    echo "🔑 AKUN LOGIN:\n";
    echo "Super Admin:\n";
    echo "  Email: superadmin@kampusmerdeka.ac.id\n";
    echo "  Password: password\n\n";
    
    echo "Admin Prodi:\n";
    echo "  Email: admin.tsd@kampusmerdeka.ac.id (atau admin.rn, admin.ti, dll)\n";
    echo "  Password: password\n\n";
    
    echo "Dosen:\n";
    echo "  Email: tsd.dosen1@kampusmerdeka.ac.id (atau sesuai prodi)\n";
    echo "  Password: password\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

