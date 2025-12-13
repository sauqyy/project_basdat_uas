@extends('layouts.dosen')

@section('title', 'Dashboard Dosen - Data Warehouse')
@section('page-title', 'Overview')

@section('content')
<div class="row g-4 mb-4">
    <!-- Header -->
    <div class="col-12">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h3 class="mb-0"><i class="fas fa-chart-line me-2"></i>Dashboard Data Warehouse</h3>
                <p class="mb-0">Analisis jadwal dan preferensi Anda menggunakan Data Warehouse</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-primary-light text-primary">
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
            <div class="icon-wrapper bg-success-light text-success">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div>
                <p class="stat-label">Jadwal Aktif</p>
                <h5 class="stat-value">{{ number_format($totalJadwal) }}</h5>
                <small class="text-muted">Jadwal aktif</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-danger-light text-danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <p class="stat-label">Konflik Jadwal</p>
                <h5 class="stat-value">{{ number_format($totalKonflik) }}</h5>
                <small class="text-muted">{{ $totalJadwal > 0 ? number_format(($totalKonflik / $totalJadwal) * 100, 2) : 0 }}%</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="icon-wrapper bg-info-light text-info">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <p class="stat-label">Total SKS</p>
                <h5 class="stat-value">{{ number_format($totalSKS) }}</h5>
                <small class="text-muted">Total beban mengajar</small>
            </div>
        </div>
    </div>
</div>

<!-- Analisis Kecocokan Preferensi -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1"><i class="fas fa-heart me-2"></i>Analisis Kepuasan Preferensi</h5>
                <p class="text-muted mb-0">Tingkat pemenuhan preferensi jadwal Anda</p>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <h3 class="text-primary mb-0">{{ number_format($avgKecocokan, 2) }}%</h3>
                        <p class="text-muted mb-0">Rata-rata Kecocokan</p>
                    </div>
                    <div class="col-6">
                        <h3 class="text-success mb-0">{{ number_format($avgSkor) }}</h3>
                        <p class="text-muted mb-0">Skor Kecocokan</p>
                    </div>
                </div>
                <div class="progress mb-3" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $avgKecocokan }}%" 
                         aria-valuenow="{{ $avgKecocokan }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        {{ number_format($avgKecocokan, 1) }}%
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-success">{{ number_format($preferensiTerpenuhi) }}</h4>
                        <p class="text-muted mb-0">Preferensi Terpenuhi</p>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info">{{ number_format($totalPreferensi) }}</h4>
                        <p class="text-muted mb-0">Total Preferensi</p>
                    </div>
                </div>
                <small class="text-muted d-block mt-3">Berdasarkan preferensi yang telah Anda set</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1"><i class="fas fa-chart-bar me-2"></i>Statistik Mengajar</h5>
                <p class="text-muted mb-0">Ringkasan aktivitas mengajar Anda</p>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <h3 class="text-info mb-0">{{ number_format($totalMahasiswa) }}</h3>
                        <p class="text-muted mb-0">Total Mahasiswa</p>
                    </div>
                    <div class="col-6 mb-3">
                        <h3 class="text-warning mb-0">{{ number_format($totalSKS) }}</h3>
                        <p class="text-muted mb-0">Total SKS</p>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-success">{{ number_format($totalJadwal) }}</h4>
                        <p class="text-muted mb-0">Jadwal Aktif</p>
                    </div>
                    <div class="col-6">
                        <h4 class="text-danger">{{ number_format($totalKonflik) }}</h4>
                        <p class="text-muted mb-0">Konflik</p>
                    </div>
                </div>
                <small class="text-muted d-block mt-3">Berdasarkan jadwal aktif Anda</small>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal Per Hari -->
@if($jadwalPerHari->count() > 0)
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1"><i class="fas fa-calendar-alt me-2"></i>Distribusi Jadwal Per Hari</h5>
                <p class="text-muted mb-0">Jadwal mengajar Anda berdasarkan hari</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Hari</th>
                                <th class="text-end">Total Jadwal</th>
                                <th class="text-end">Total SKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalPerHari as $jadwal)
                            <tr>
                                <td>
                                    <strong>{{ $jadwal['hari'] ?? 'N/A' }}</strong>
                                </td>
                                <td class="text-end">{{ $jadwal['total_jadwal'] ?? 0 }}</td>
                                <td class="text-end">{{ $jadwal['total_sks'] ?? 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Berdasarkan jadwal aktif Anda</small>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Jadwal dengan Konflik -->
@if($jadwalKonflik->count() > 0)
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-1"><i class="fas fa-exclamation-triangle me-2"></i>Jadwal dengan Konflik</h5>
                <p class="mb-0">Jadwal yang memiliki konflik dan perlu diperhatikan</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>Ruangan</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th class="text-end">Tingkat Konflik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalKonflik as $konflik)
                            <tr>
                                <td>
                                    <strong>{{ $konflik->mataKuliah->nama_mk ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $konflik->mataKuliah->kode_mk ?? '' }}</small>
                                </td>
                                <td>
                                    {{ $konflik->ruangan->nama_ruangan ?? 'N/A' }}<br>
                                    <small class="text-muted">{{ $konflik->ruangan->kode_ruangan ?? '' }}</small>
                                </td>
                                <td>{{ $konflik->hari ?? 'N/A' }}</td>
                                <td>
                                    {{ is_object($konflik->jam_mulai) ? $konflik->jam_mulai->format('H:i') : $konflik->jam_mulai }} - 
                                    {{ is_object($konflik->jam_selesai) ? $konflik->jam_selesai->format('H:i') : $konflik->jam_selesai }}
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-danger">Konflik</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Berdasarkan jadwal aktif Anda</small>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1">Quick Actions</h5>
                <p class="text-muted mb-0">Aksi cepat untuk manajemen jadwal</p>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('dosen.mata-kuliah') }}" class="btn btn-outline-primary">
                        <i class="fas fa-book me-2"></i>Kelola Mata Kuliah
                    </a>
                    <a href="{{ route('dosen.preferensi') }}" class="btn btn-outline-success">
                        <i class="fas fa-clock me-2"></i>Set Preferensi Jadwal
                    </a>
                    <a href="{{ route('dosen.jadwal') }}" class="btn btn-outline-info">
                        <i class="fas fa-calendar-alt me-2"></i>Lihat Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1">Informasi Data Warehouse</h5>
                <p class="text-muted mb-0">Tentang dashboard ini</p>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Dashboard Data Warehouse</h6>
                    <p class="mb-0">Dashboard ini menggunakan data dari fact tables untuk memberikan analisis yang lebih mendalam tentang jadwal dan preferensi Anda.</p>
                </div>
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check text-success me-2"></i>Data dari FactJadwal</li>
                    <li><i class="fas fa-check text-success me-2"></i>Data dari FactKecocokanJadwal</li>
                    <li><i class="fas fa-check text-success me-2"></i>Analisis real-time</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
