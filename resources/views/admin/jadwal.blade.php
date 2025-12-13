@extends('layouts.admin')

@section('title', 'Jadwal Plotting')
@section('page-title', 'Jadwal Plotting')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Jadwal Plotting AI</h5>
                    <p class="text-muted mb-0">Jadwal yang dihasilkan oleh algoritma AI</p>
                </div>
                <form action="{{ route('admin.generate-jadwal') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-robot me-2"></i>Generate Ulang Jadwal
                    </button>
                </form>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($jadwals->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hari</th>
                                    <th>Jam</th>
                                    <th>Mata Kuliah</th>
                                    <th>Dosen</th>
                                    <th>Ruangan</th>
                                    <th>SKS</th>
                                    <th>Prodi</th>
                                    <th>Semester</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwals as $jadwal)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $jadwal->hari }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $jadwal->mataKuliah->kode_mk }}</strong><br>
                                                <small class="text-muted">{{ $jadwal->mataKuliah->nama_mk }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $jadwal->mataKuliah->dosen->name }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $jadwal->ruangan->kode_ruangan }}</strong><br>
                                                <small class="text-muted">{{ $jadwal->ruangan->nama_ruangan }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $jadwal->mataKuliah->sks }} SKS</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $jadwal->prodi }}</span>
                                        </td>
                                        <td>{{ $jadwal->semester }} {{ $jadwal->tahun_akademik }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada jadwal yang di-generate</h5>
                        <p class="text-muted">Klik tombol "Generate Jadwal AI" untuk membuat jadwal otomatis</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($jadwals->count() > 0)
<div class="row g-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-1">Statistik Jadwal</h5>
                <p class="text-muted mb-0">Ringkasan distribusi jadwal</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary">{{ $jadwals->where('hari', 'Senin')->count() }}</h4>
                            <p class="text-muted mb-0">Senin</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success">{{ $jadwals->where('hari', 'Selasa')->count() }}</h4>
                            <p class="text-muted mb-0">Selasa</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-warning">{{ $jadwals->where('hari', 'Rabu')->count() }}</h4>
                            <p class="text-muted mb-0">Rabu</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-info">{{ $jadwals->where('hari', 'Kamis')->count() }}</h4>
                            <p class="text-muted mb-0">Kamis</p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-danger">{{ $jadwals->where('hari', 'Jumat')->count() }}</h4>
                            <p class="text-muted mb-0">Jumat</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-primary">{{ $jadwals->unique('ruangan_id')->count() }}</h4>
                            <p class="text-muted mb-0">Ruangan Digunakan</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-success">{{ $jadwals->unique('mata_kuliah_id')->count() }}</h4>
                            <p class="text-muted mb-0">Mata Kuliah Terjadwal</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <h4 class="text-warning">{{ $jadwals->unique(function($item) { return $item->mataKuliah->dosen_id; })->count() }}</h4>
                            <p class="text-muted mb-0">Dosen Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
