@extends('layouts.dosen')

@section('title', 'Mata Kuliah')
@section('page-title', 'Mata Kuliah')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Daftar Mata Kuliah</h5>
                    <p class="text-muted mb-0">Mata kuliah yang Anda ajar</p>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode MK</th>
                                <th>Nama Mata Kuliah</th>
                                <th>SKS</th>
                                <th>Semester</th>
                                <th>Kapasitas</th>
                                <th>Tipe Kelas</th>
                                <th>Menit/SKS</th>
                                <th>Prodi</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mataKuliahs as $mk)
                                <tr>
                                    <td><strong>{{ $mk->kode_mk }}</strong></td>
                                    <td>{{ $mk->nama_mk }}</td>
                                    <td>{{ $mk->sks }} SKS</td>
                                    <td>{{ $mk->semester }}</td>
                                    <td>{{ $mk->kapasitas }} orang</td>
                                    <td>
                                        <span class="badge bg-{{ $mk->tipe_kelas == 'teori' ? 'primary' : 'success' }}">
                                            {{ ucfirst($mk->tipe_kelas) }}
                                        </span>
                                    </td>
                                    <td>{{ $mk->menit_per_sks }} menit</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $mk->prodi ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ Str::limit($mk->deskripsi, 50) ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Belum ada mata kuliah yang terdaftar</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
