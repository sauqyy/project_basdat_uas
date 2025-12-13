@extends('layouts.mahasiswa')

@section('title', 'Dashboard Mahasiswa')
@section('page-title', 'Dashboard Mahasiswa')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-primary-light text-primary">
                <i class="fas fa-book-open"></i>
            </div>
            <div>
                <p class="stat-label">Bab Skripsi</p>
                <h5 class="stat-value">3/5</h5>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-success-light text-success">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="stat-label">Konsultasi Bulan Ini</p>
                <h5 class="stat-value">8</h5>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-warning-light text-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="stat-label">Menunggu Review</p>
                <h5 class="stat-value">2</h5>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-info-light text-info">
                <i class="fas fa-user-tie"></i>
            </div>
            <div>
                <p class="stat-label">Pembimbing</p>
                <h5 class="stat-value">Dr. Ahmad Wijaya</h5>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-1">Konsultasi Mendatang</h5>
        <p class="text-muted mb-0">Jadwal konsultasi yang sudah dijadwalkan</p>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Review Bab 3</h6>
                        <small class="text-muted">Metodologi Penelitian</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary">Senin, 15 Jan</span>
                        <br>
                        <small class="text-muted">10:00 - 11:00</small>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Diskusi Bab 4</h6>
                        <small class="text-muted">Analisis dan Pembahasan</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success">Rabu, 17 Jan</span>
                        <br>
                        <small class="text-muted">14:00 - 15:00</small>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1">Final Review</h6>
                        <small class="text-muted">Bab 5 - Kesimpulan</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-warning">Jumat, 19 Jan</span>
                        <br>
                        <small class="text-muted">09:00 - 10:00</small>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
@endsection