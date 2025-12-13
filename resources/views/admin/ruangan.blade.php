@extends('layouts.admin')

@section('title', 'Manajemen Ruangan')
@section('page-title', 'Manajemen Ruangan')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Daftar Ruangan</h5>
                    <p class="text-muted mb-0">Kelola ruangan yang tersedia untuk plotting jadwal</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahRuanganModal">
                    <i class="fas fa-plus me-2"></i>Tambah Ruangan
                </button>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode Ruangan</th>
                                <th>Nama Ruangan</th>
                                <th>Kapasitas</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th>Fasilitas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ruangans as $ruangan)
                                <tr>
                                    <td><strong>{{ $ruangan->kode_ruangan }}</strong></td>
                                    <td>{{ $ruangan->nama_ruangan }}</td>
                                    <td>{{ $ruangan->kapasitas }} orang</td>
                                    <td>
                                        <span class="badge bg-{{ $ruangan->tipe_ruangan == 'lab' ? 'info' : ($ruangan->tipe_ruangan == 'auditorium' ? 'warning' : 'primary') }}">
                                            {{ ucfirst($ruangan->tipe_ruangan) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $ruangan->status ? 'success' : 'danger' }}">
                                            {{ $ruangan->status ? 'Tersedia' : 'Tidak Tersedia' }}
                                        </span>
                                    </td>
                                    <td>{{ $ruangan->fasilitas ?? '-' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editRuangan({{ $ruangan->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.ruangan.destroy', $ruangan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus ruangan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada ruangan yang terdaftar</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Ruangan -->
<div class="modal fade" id="tambahRuanganModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Ruangan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.ruangan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="kode_ruangan" class="form-label">Kode Ruangan</label>
                        <input type="text" class="form-control" id="kode_ruangan" name="kode_ruangan" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
                        <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan" required>
                    </div>
                    <div class="mb-3">
                        <label for="kapasitas" class="form-label">Kapasitas</label>
                        <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipe_ruangan" class="form-label">Tipe Ruangan</label>
                        <select class="form-select" id="tipe_ruangan" name="tipe_ruangan" required>
                            <option value="">Pilih Tipe</option>
                            <option value="kelas">Kelas</option>
                            <option value="lab">Laboratorium</option>
                            <option value="auditorium">Auditorium</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fasilitas" class="form-label">Fasilitas</label>
                        <textarea class="form-control" id="fasilitas" name="fasilitas" rows="3" placeholder="Contoh: AC, Proyektor, Whiteboard"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Ruangan -->
<div class="modal fade" id="editRuanganModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Ruangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRuanganForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_kode_ruangan" class="form-label">Kode Ruangan</label>
                        <input type="text" class="form-control" id="edit_kode_ruangan" name="kode_ruangan" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nama_ruangan" class="form-label">Nama Ruangan</label>
                        <input type="text" class="form-control" id="edit_nama_ruangan" name="nama_ruangan" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_kapasitas" class="form-label">Kapasitas</label>
                        <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tipe_ruangan" class="form-label">Tipe Ruangan</label>
                        <select class="form-select" id="edit_tipe_ruangan" name="tipe_ruangan" required>
                            <option value="">Pilih Tipe</option>
                            <option value="kelas">Kelas</option>
                            <option value="lab">Laboratorium</option>
                            <option value="auditorium">Auditorium</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_fasilitas" class="form-label">Fasilitas</label>
                        <textarea class="form-control" id="edit_fasilitas" name="fasilitas" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRuangan(id) {
    // Ambil data ruangan dari server
    fetch(`/admin/ruangan/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_kode_ruangan').value = data.kode_ruangan;
            document.getElementById('edit_nama_ruangan').value = data.nama_ruangan;
            document.getElementById('edit_kapasitas').value = data.kapasitas;
            document.getElementById('edit_tipe_ruangan').value = data.tipe_ruangan;
            document.getElementById('edit_fasilitas').value = data.fasilitas || '';
            
            document.getElementById('editRuanganForm').action = `/admin/ruangan/${id}`;
            
            new bootstrap.Modal(document.getElementById('editRuanganModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data ruangan');
        });
}
</script>
@endsection
