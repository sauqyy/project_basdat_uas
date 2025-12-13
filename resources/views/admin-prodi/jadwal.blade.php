@extends('layouts.dosen')

@section('title', 'Jadwal')
@section('page-title', 'Kelola Jadwal')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div>
                    <h5 class="mb-1">Daftar Jadwal Prodi {{ $prodi }}</h5>
                    <p class="text-muted mb-0">Lihat jadwal perkuliahan untuk program studi {{ $prodi }}</p>
                </div>
            </div>
            <div class="card-body">
                <!-- Warning Messages -->
                @if(session('warnings'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Peringatan</h6>
                        <ul class="mb-0">
                            @foreach(session('warnings') as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Failed Mata Kuliah -->
                @if(session('failed_mata_kuliah'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-times-circle me-2"></i>Jadwal Tidak Bisa Dibuat</h6>
                        <p class="mb-2">Berikut adalah mata kuliah yang tidak bisa dibuat jadwalnya:</p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Alasan</th>
                                        <th>Preferensi</th>
                                        <th>Hari Tersedia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(session('failed_mata_kuliah') as $failed)
                                        <tr>
                                            <td>
                                                <strong>{{ $failed['nama_mk'] }}</strong><br>
                                                <small class="text-muted">{{ $failed['kode_mk'] }}</small>
                                            </td>
                                            <td>{{ $failed['dosen'] }}</td>
                                            <td>
                                                <span class="badge bg-danger">{{ $failed['reason'] }}</span>
                                            </td>
                                            <td>
                                                <small>{{ $failed['preferensi_hari'] ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <small>{{ $failed['hari_tersedia'] ?? 'N/A' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('warnings') && !session('failed_mata_kuliah'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading">
                                <i class="fas fa-exclamation-triangle me-2"></i>Peringatan Kapasitas Ruangan
                            </h6>
                            <ul class="mb-0">
                                @foreach(session('warnings') as $warning)
                                    <li>{{ $warning }}</li>
                                @endforeach
                            </ul>
                            <hr>
                            <p class="mb-0">
                                <strong>Solusi:</strong> Silakan tambahkan ruangan dengan kapasitas yang sesuai atau kurangi kapasitas mata kuliah.
                            </p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($jadwals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Ruangan</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>SKS</th>
                                        <th>Prodi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwals as $index => $jadwal)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $jadwal->mataKuliah->nama_mk ?? 'N/A' }}</td>
                                            <td>{{ $jadwal->mataKuliah->dosen->name ?? 'N/A' }}</td>
                                            <td>{{ $jadwal->ruangan->nama_ruangan ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $jadwal->hari }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $jadwal->mataKuliah->sks ?? 'N/A' }} SKS</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $jadwal->prodi }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-calendar-times fa-3x text-muted"></i>
                            </div>
                            <h5 class="text-muted">Belum ada jadwal untuk prodi {{ $prodi }}</h5>
                            <p class="text-muted">Klik tombol "Generate Jadwal AI" untuk membuat jadwal otomatis</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
