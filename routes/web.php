<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminProdiController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LandingController;

// Landing Page Routes
Route::get('/', [LandingController::class, 'index'])->name('landing.index');
Route::get('/learn-more', [LandingController::class, 'learnMore'])->name('landing.learn-more');
Route::get('/about', [LandingController::class, 'about'])->name('landing.about');
Route::get('/contact', [LandingController::class, 'contact'])->name('landing.contact');

// Login Routes
Route::get('/login', function () {
    return view('auth.login-dosen');
})->name('login');

Route::get('/admin/login', function () {
    return view('auth.login-admin');
})->name('admin.login');

Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/admin/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Super Admin Routes
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    
    // Manajemen Kelas (Ruangan) dengan Prodi Tags
    Route::get('/kelas', [SuperAdminController::class, 'kelas'])->name('kelas');
    Route::post('/kelas', [SuperAdminController::class, 'storeKelas'])->name('kelas.store');
    Route::get('/kelas/{id}', [SuperAdminController::class, 'getKelas'])->name('kelas.get');
    Route::put('/kelas/{id}', [SuperAdminController::class, 'updateKelas'])->name('kelas.update');
    Route::delete('/kelas/{id}', [SuperAdminController::class, 'destroyKelas'])->name('kelas.destroy');
    
    // Generate Jadwal AI
    Route::post('/generate-jadwal', [SuperAdminController::class, 'generateJadwal'])->name('generate-jadwal');
    Route::get('/jadwal', [SuperAdminController::class, 'jadwal'])->name('jadwal');
    
});

// Admin Prodi Routes
Route::prefix('admin-prodi')->name('admin-prodi.')->middleware(['auth', 'admin_prodi'])->group(function () {
    Route::get('/dashboard', [AdminProdiController::class, 'dashboard'])->name('dashboard');
    
    // Manajemen Mata Kuliah untuk Prodi
    Route::get('/mata-kuliah', [AdminProdiController::class, 'mataKuliah'])->name('mata-kuliah');
    Route::post('/mata-kuliah', [AdminProdiController::class, 'storeMataKuliah'])->name('mata-kuliah.store');
    Route::get('/mata-kuliah/{id}', [AdminProdiController::class, 'getMataKuliah'])->name('mata-kuliah.get');
    Route::put('/mata-kuliah/{id}', [AdminProdiController::class, 'updateMataKuliah'])->name('mata-kuliah.update');
    Route::delete('/mata-kuliah/{id}', [AdminProdiController::class, 'destroyMataKuliah'])->name('mata-kuliah.destroy');
    
    // Manajemen Dosen untuk Prodi
    Route::get('/dosen', [AdminProdiController::class, 'dosen'])->name('dosen');
    Route::post('/dosen', [AdminProdiController::class, 'storeDosen'])->name('dosen.store');
    Route::get('/dosen/{id}', [AdminProdiController::class, 'getDosen'])->name('dosen.get');
    Route::put('/dosen/{id}', [AdminProdiController::class, 'updateDosen'])->name('dosen.update');
    Route::delete('/dosen/{id}', [AdminProdiController::class, 'destroyDosen'])->name('dosen.destroy');
    
    // Manajemen Jadwal untuk Prodi
    Route::get('/jadwal', [AdminProdiController::class, 'jadwal'])->name('jadwal');
    Route::post('/generate-jadwal', [AdminProdiController::class, 'generateJadwal'])->name('generate-jadwal');
});

// Dosen Routes
Route::prefix('dosen')->name('dosen.')->middleware(['auth', 'dosen'])->group(function () {
    Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dashboard');
    
    // Lihat Mata Kuliah yang diampu (read-only)
    Route::get('/mata-kuliah', [DosenController::class, 'mataKuliah'])->name('mata-kuliah');
    
    // Manajemen Preferensi Jadwal Global
    Route::get('/preferensi', [DosenController::class, 'preferensi'])->name('preferensi');
    Route::post('/preferensi', [DosenController::class, 'storePreferensi'])->name('preferensi.store');
    Route::get('/preferensi/{id}', [DosenController::class, 'getPreferensi'])->name('preferensi.get');
    Route::put('/preferensi/{id}', [DosenController::class, 'updatePreferensi'])->name('preferensi.update');
    
    // Lihat Jadwal (Calendar View)
    Route::get('/jadwal', [DosenController::class, 'jadwal'])->name('jadwal');
});

// Mahasiswa Routes - REMOVED (tidak lagi dibutuhkan)

