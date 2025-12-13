@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Overview')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-primary-light text-primary">
                <i class="fas fa-door-open"></i>
            </div>
            <div>
                <p class="stat-label">Total Ruangan</p>
                <h5 class="stat-value">{{ $totalRuangan }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-success-light text-success">
                <i class="fas fa-book"></i>
            </div>
            <div>
                <p class="stat-label">Total Mata Kuliah</p>
                <h5 class="stat-value">{{ $totalMataKuliah }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-warning-light text-warning">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <p class="stat-label">Jadwal Terjadwal</p>
                <h5 class="stat-value">{{ $totalJadwal }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-info-light text-info">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <p class="stat-label">Ruangan Tersedia</p>
                <h5 class="stat-value">{{ $ruanganTersedia }}</h5>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">AI Plotting Jadwal</h5>
                    <p class="text-muted mb-0">Generate jadwal otomatis menggunakan algoritma AI</p>
                </div>
                <form action="{{ route('admin.generate-jadwal') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-robot me-2"></i>Generate Jadwal AI
                    </button>
                </form>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Fitur AI Plotting:</strong> Sistem akan secara otomatis mengatur jadwal berdasarkan preferensi dosen, ketersediaan ruangan, dan algoritma optimasi untuk menghindari konflik jadwal.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-1">Aktivitas Terbaru</h5>
        <p class="text-muted mb-0">Ringkasan aktivitas sistem plotting jadwal</p>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <i class="fas fa-circle text-success me-2"></i> Sistem berhasil generate jadwal untuk 15 mata kuliah.
                <span class="float-end text-muted">2 jam lalu</span>
            </li>
            <li class="list-group-item">
                <i class="fas fa-circle text-info me-2"></i> Dosen Dr. Ahmad memperbarui preferensi jadwal mata kuliah Algoritma.
                <span class="float-end text-muted">5 jam lalu</span>
            </li>
            <li class="list-group-item">
                <i class="fas fa-circle text-warning me-2"></i> Admin menambahkan ruangan laboratorium baru.
                <span class="float-end text-muted">1 hari lalu</span>
            </li>
        </ul>
    </div>
</div>
@endsection