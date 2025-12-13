@extends('layouts.dosen')

@section('title', 'Admin Prodi Dashboard - Data Warehouse')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3 class="mb-0"><i class="fas fa-chart-line me-2"></i>Dashboard Data Warehouse - {{ $prodi }}</h3>
                    <p class="mb-0">Analisis sistem jadwal untuk Program Studi {{ $prodi }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <p class="text-truncate font-size-14 mb-2">Total Mata Kuliah</p>
                            <h4 class="mb-2">{{ $totalMataKuliah }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="fas fa-book font-size-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <p class="text-truncate font-size-14 mb-2">Total Dosen</p>
                            <h4 class="mb-2">{{ $totalDosen }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="fas fa-user font-size-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <p class="text-truncate font-size-14 mb-2">Jadwal Aktif</p>
                            <h4 class="mb-2 text-info">{{ number_format($totalJadwalFact) }}</h4>
                            <small class="text-muted">Dari FactJadwal</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="fas fa-calendar-check font-size-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <p class="text-truncate font-size-14 mb-2">Total Konflik</p>
                            <h4 class="mb-2 text-danger">{{ number_format($totalKonflik) }}</h4>
                            <small class="text-muted">{{ $totalJadwalFact > 0 ? number_format(($totalKonflik / $totalJadwalFact) * 100, 2) : 0 }}%</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-danger rounded-3">
                                <i class="fas fa-exclamation-triangle font-size-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analisis Data Warehouse -->
    <div class="row mt-4">
        <div class="col-xl-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Utilisasi Ruangan</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="text-info mb-0">{{ number_format($avgUtilisasiRuangan, 2) }}%</h2>
                        <p class="text-muted mb-3">Rata-rata Utilisasi Ruangan</p>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-info progress-bar-striped" role="progressbar" 
                                 style="width: {{ min($avgUtilisasiRuangan, 100) }}%" 
                                 aria-valuenow="{{ $avgUtilisasiRuangan }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ number_format($avgUtilisasiRuangan, 1) }}%
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <h4 class="text-success">{{ number_format($totalMahasiswa) }}</h4>
                        <p class="text-muted mb-0">Total Mahasiswa Terdaftar</p>
                    </div>
                    <small class="text-muted d-block mt-3">Data dari FactJadwal</small>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-heart me-2"></i>Kepuasan Preferensi Dosen</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="text-primary mb-0">{{ number_format($avgKecocokan, 2) }}%</h2>
                        <p class="text-muted mb-3">Rata-rata Kecocokan Preferensi</p>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ min($avgKecocokan, 100) }}%" 
                                 aria-valuenow="{{ $avgKecocokan }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ number_format($avgKecocokan, 1) }}%
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <h4 class="text-success">{{ number_format($totalPreferensiTerpenuhi) }}</h4>
                        <p class="text-muted mb-0">Preferensi yang Terpenuhi</p>
                    </div>
                    <small class="text-muted d-block mt-3">Data dari FactKecocokanJadwal</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Ruangan dan Dosen -->
    <div class="row mt-4">
        <div class="col-xl-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Top 5 Ruangan Paling Sering Digunakan</h5>
                </div>
                <div class="card-body">
                    @if($topRuanganUtilisasi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ruangan</th>
                                        <th class="text-end">Utilisasi</th>
                                        <th class="text-end">Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topRuanganUtilisasi as $ruangan)
                                    <tr>
                                        <td>
                                            <strong>{{ $ruangan->dimRuangan->nama_ruangan ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $ruangan->dimRuangan->tipe_ruangan ?? '' }}</small>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-info">{{ number_format($ruangan->persentase_utilisasi ?? 0, 1) }}%</span>
                                        </td>
                                        <td class="text-end">{{ $ruangan->jumlah_kelas ?? 0 }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Belum ada data utilisasi ruangan</p>
                    @endif
                    <small class="text-muted">Data dari FactUtilisasiRuangan</small>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Top 5 Dosen dengan Beban Mengajar Tertinggi</h5>
                </div>
                <div class="card-body">
                    @if($topDosenBeban->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Dosen</th>
                                        <th class="text-end">Jadwal</th>
                                        <th class="text-end">Total SKS</th>
                                        <th class="text-end">Mahasiswa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topDosenBeban as $dosen)
                                    <tr>
                                        <td>
                                            <strong>{{ $dosen->dimDosen->nama_dosen ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $dosen->dimDosen->nip ?? '' }}</small>
                                        </td>
                                        <td class="text-end">{{ $dosen->total_jadwal ?? 0 }}</td>
                                        <td class="text-end">{{ $dosen->total_sks ?? 0 }}</td>
                                        <td class="text-end">{{ number_format($dosen->total_mahasiswa ?? 0) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Belum ada data beban mengajar dosen</p>
                    @endif
                    <small class="text-muted">Data dari FactJadwal</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Quick Actions</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('admin-prodi.mata-kuliah') }}" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-book me-2"></i>Kelola Mata Kuliah
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin-prodi.dosen') }}" class="btn btn-info btn-lg w-100 mb-3">
                                <i class="fas fa-users me-2"></i>Kelola Dosen
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('admin-prodi.jadwal') }}" class="btn btn-success btn-lg w-100 mb-3">
                                <i class="fas fa-calendar me-2"></i>Lihat Jadwal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
