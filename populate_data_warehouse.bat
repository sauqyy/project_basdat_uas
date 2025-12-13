@echo off
echo ========================================
echo POPULATE DATA WAREHOUSE
echo ========================================
echo.

echo Step 1: Populating Dimension Tables...
php artisan dw:populate-all --fresh

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERROR: Command failed. Trying alternative method...
    echo.
    echo Step 1a: Populating Dim Tables manually...
    php artisan tinker --execute="
    use App\Models\User; use App\Models\DimDosen;
    User::whereIn('role', ['dosen', 'admin_prodi'])->get()->each(function(\$u) {
        DimDosen::updateOrCreate(['nip' => \$u->nip ?? \$u->id], [
            'dosen_key' => 'DOSEN_' . (\$u->nip ?? \$u->id),
            'nama_dosen' => \$u->name,
            'email' => \$u->email,
            'prodi' => \$u->prodi,
            'role' => \$u->role,
            'is_active' => true,
            'valid_from' => \$u->created_at ?? now(),
        ]);
    });
    echo 'DimDosen populated: ' . DimDosen::count();
    "
    
    php artisan fact:populate --fresh
)

echo.
echo ========================================
echo DONE! Please refresh your dashboard.
echo ========================================
pause

