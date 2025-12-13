@extends('layouts.dosen')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3 class="mb-0"><i class="fas fa-chart-line me-2"></i>Dashboard Super Admin</h3>
                    <p class="mb-0">Overview sistem jadwal perkuliahan</p>
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
                            <p class="text-truncate font-size-14 mb-2">Total Ruangan</p>
                            <h4 class="mb-2">{{ number_format($totalRuangan) }}</h4>
                            <small class="text-muted">{{ $ruanganTersedia }} tersedia</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="fas fa-home font-size-18"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Total Jadwal Aktif</p>
                            <h4 class="mb-2 text-info">{{ number_format($totalJadwal) }}</h4>
                            <small class="text-muted">Jadwal aktif</small>
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
                            <small class="text-muted">
                                {{ $totalJadwal > 0 ? number_format(($totalKonflik / $totalJadwal) * 100, 2) : 0 }}% dari total
                            </small>
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
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-1">
                            <p class="text-truncate font-size-14 mb-2">Rata-rata Utilisasi Ruangan</p>
                            <h4 class="mb-2 text-success">{{ number_format($avgUtilisasiRuangan, 2) }}%</h4>
                            <small class="text-muted">{{ number_format($totalMahasiswa) }} mahasiswa</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="fas fa-chart-pie font-size-18"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Utilisasi Ruangan -->
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
                                        <th>Tipe</th>
                                        <th class="text-end">Penggunaan</th>
                                        <th class="text-end">Utilisasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topRuanganUtilisasi as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->ruangan->nama_ruangan ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $item->ruangan->kode_ruangan ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($item->ruangan->tipe_ruangan ?? '') }}</span>
                                        </td>
                                        <td class="text-end">{{ $item->jumlah_penggunaan ?? 0 }}x</td>
                                        <td class="text-end">
                                            <span class="badge bg-success">{{ number_format($item->utilisasi ?? 0, 1) }}%</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Belum ada data utilisasi ruangan</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Rata-rata Utilisasi Ruangan</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="text-success mb-0">{{ number_format($avgUtilisasiRuangan, 2) }}%</h2>
                        <p class="text-muted mb-3">Rata-rata Utilisasi Ruangan</p>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-success progress-bar-striped" role="progressbar" 
                                 style="width: {{ min($avgUtilisasiRuangan, 100) }}%" 
                                 aria-valuenow="{{ $avgUtilisasiRuangan }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ number_format($avgUtilisasiRuangan, 1) }}%
                            </div>
                        </div>
                    </div>
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <h4 class="text-primary">{{ number_format($totalJadwal) }}</h4>
                            <p class="text-muted mb-0">Total Jadwal</p>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info">{{ number_format($totalMahasiswa) }}</h4>
                            <p class="text-muted mb-0">Total Mahasiswa</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Konflik Jadwal -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Jadwal dengan Konflik</h5>
                </div>
                <div class="card-body">
                    @if($jadwalKonflik->count() > 0)
                        <div class="alert alert-warning">
                            <strong>Peringatan!</strong> Terdapat {{ $totalKonflik }} jadwal yang memiliki konflik. 
                            Konflik terjadi ketika ruangan atau dosen digunakan pada waktu yang sama.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Ruangan</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Prodi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalKonflik as $index => $jadwal)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $jadwal->mataKuliah->nama_mk ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">{{ $jadwal->mataKuliah->kode_mk ?? '' }}</small>
                                        </td>
                                        <td>{{ $jadwal->mataKuliah->dosen->name ?? 'N/A' }}</td>
                                        <td>
                                            {{ $jadwal->ruangan->nama_ruangan ?? 'N/A' }}<br>
                                            <small class="text-muted">{{ $jadwal->ruangan->kode_ruangan ?? '' }}</small>
                                        </td>
                                        <td>{{ $jadwal->hari }}</td>
                                        <td>
                                            {{ $jadwal->jam_mulai->format('H:i') }} - {{ $jadwal->jam_selesai->format('H:i') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $jadwal->prodi ?? 'N/A' }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Bagus!</strong> Tidak ada konflik jadwal. Semua jadwal berjalan dengan baik.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Per Prodi -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-university me-2"></i>Statistik Per Program Studi</h5>
                </div>
                <div class="card-body">
                    @if($statistikProdi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Program Studi</th>
                                        <th class="text-end">Total Jadwal</th>
                                        <th class="text-end">Konflik</th>
                                        <th class="text-end">Rata-rata Utilisasi</th>
                                        <th class="text-end">Total Mahasiswa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statistikProdi as $stat)
                                    <tr>
                                        <td>
                                            <strong>{{ $stat->prodi }}</strong>
                                        </td>
                                        <td class="text-end">{{ number_format($stat->total_jadwal ?? 0) }}</td>
                                        <td class="text-end">
                                            <span class="badge {{ ($stat->total_konflik ?? 0) > 0 ? 'bg-danger' : 'bg-success' }}">
                                                {{ number_format($stat->total_konflik ?? 0) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-info">{{ number_format($stat->avg_utilisasi ?? 0, 2) }}%</span>
                                        </td>
                                        <td class="text-end">{{ number_format($stat->total_mahasiswa ?? 0) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Belum ada data statistik prodi</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top Dosen -->
    <div class="row mt-4">
        <div class="col-12">
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
                                        <th>NIP</th>
                                        <th class="text-end">Total Jadwal</th>
                                        <th class="text-end">Total SKS</th>
                                        <th class="text-end">Total Mahasiswa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topDosenBeban as $dosen)
                                    <tr>
                                        <td>
                                            <strong>{{ $dosen->dosen->name ?? 'N/A' }}</strong>
                                        </td>
                                        <td>{{ $dosen->dosen->nip ?? 'N/A' }}</td>
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
                        <div class="col-md-3">
                            <a href="{{ route('super-admin.kelas') }}" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-home me-2"></i>Kelola Kelas
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('super-admin.jadwal') }}" class="btn btn-success btn-lg w-100 mb-3">
                                <i class="fas fa-calendar me-2"></i>Lihat Jadwal
                            </a>
                        </div>
                        <div class="col-md-3">
                            <form method="POST" action="{{ route('super-admin.generate-jadwal') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-info btn-lg w-100 mb-3" onclick="return confirm('Apakah Anda yakin ingin generate jadwal?')">
                                    <i class="fas fa-robot me-2"></i>Generate Jadwal AI
                                </button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('super-admin.kelas') }}" class="btn btn-warning btn-lg w-100 mb-3">
                                <i class="fas fa-plus me-2"></i>Tambah Kelas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
