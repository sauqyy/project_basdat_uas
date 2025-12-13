@echo off
echo ========================================
echo   RESET DATABASE & SEED DATA BARU
echo ========================================
echo.
echo PERINGATAN: Semua data akan dihapus!
echo Tekan Enter untuk melanjutkan atau Ctrl+C untuk membatalkan...
pause

echo.
echo Menghapus semua tabel dan membuat ulang...
php artisan migrate:fresh --force

echo.
echo Menjalankan seeder data baru...
php artisan db:seed --class=FreshDataSeeder --force

echo.
echo ========================================
echo   RESET DATABASE BERHASIL!
echo ========================================
echo.
echo AKUN LOGIN:
echo.
echo Super Admin:
echo   Email: superadmin@kampusmerdeka.ac.id
echo   Password: password
echo.
echo Admin Prodi:
echo   Email: admin.tsd@kampusmerdeka.ac.id
echo   Password: password
echo.
echo Dosen:
echo   Email: tsd.dosen1@kampusmerdeka.ac.id
echo   Password: password
echo.
pause

