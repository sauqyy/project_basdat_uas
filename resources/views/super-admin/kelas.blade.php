@extends('layouts.dosen')

@section('title', 'Kelola Kelas')
@section('page-title', 'Kelola Kelas')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Daftar Ruangan Kelas</h5>
                    <p class="text-muted mb-0">Kelola ruangan kelas untuk semua program studi</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKelasModal">
                    <i class="fas fa-plus me-2"></i>Tambah Kelas
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
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Kode Ruangan</th>
                                    <th>Nama Ruangan</th>
                                    <th>Kapasitas</th>
                                    <th>Tipe Ruangan</th>
                                    <th>Prodi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ruangans as $ruangan)
                                <tr>
                                    <td>{{ $ruangan->kode_ruangan }}</td>
                                    <td>{{ $ruangan->nama_ruangan }}</td>
                                    <td>{{ $ruangan->kapasitas }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ruangan->tipe_ruangan == 'kelas' ? 'primary' : ($ruangan->tipe_ruangan == 'lab' ? 'success' : 'info') }}">
                                            {{ ucfirst($ruangan->tipe_ruangan) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $ruangan->prodi ?? 'Belum ditentukan' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $ruangan->status ? 'success' : 'danger' }}">
                                            {{ $ruangan->status ? 'Tersedia' : 'Tidak Tersedia' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning me-1 edit-kelas-btn" data-id="{{ $ruangan->id }}" title="Edit Kelas">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-kelas-btn" data-id="{{ $ruangan->id }}" data-name="{{ $ruangan->nama_ruangan }}" title="Hapus Kelas">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data kelas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Kelas Modal -->
<div class="modal fade" id="addKelasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('super-admin.kelas.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Ruangan</label>
                        <input type="text" class="form-control" name="kode_ruangan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Ruangan</label>
                        <input type="text" class="form-control" name="nama_ruangan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" class="form-control" name="kapasitas" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Ruangan</label>
                        <select class="form-control" name="tipe_ruangan" required>
                            <option value="kelas">Kelas</option>
                            <option value="lab">Lab</option>
                            <option value="auditorium">Auditorium</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prodi</label>
                        <select class="form-control" name="prodi" required>
                            <option value="">Pilih Prodi</option>
                            @foreach($prodiList as $prodi)
                                <option value="{{ $prodi }}">{{ $prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" required>
                            <option value="1">Tersedia</option>
                            <option value="0">Tidak Tersedia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <textarea class="form-control" name="fasilitas" rows="3"></textarea>
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

<!-- Edit Kelas Modal -->
<div class="modal fade" id="editKelasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editKelasForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Ruangan</label>
                        <input type="text" class="form-control" name="kode_ruangan" id="edit_kode_ruangan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Ruangan</label>
                        <input type="text" class="form-control" name="nama_ruangan" id="edit_nama_ruangan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas</label>
                        <input type="number" class="form-control" name="kapasitas" id="edit_kapasitas" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe Ruangan</label>
                        <select class="form-control" name="tipe_ruangan" id="edit_tipe_ruangan" required>
                            <option value="kelas">Kelas</option>
                            <option value="lab">Lab</option>
                            <option value="auditorium">Auditorium</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prodi</label>
                        <select class="form-control" name="prodi" id="edit_prodi" required>
                            <option value="">Pilih Prodi</option>
                            @foreach($prodiList as $prodi)
                                <option value="{{ $prodi }}">{{ $prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" id="edit_status" required>
                            <option value="1">Tersedia</option>
                            <option value="0">Tidak Tersedia</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <textarea class="form-control" name="fasilitas" id="edit_fasilitas" rows="3"></textarea>
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteKelasModal" tabindex="-1" aria-labelledby="deleteKelasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteKelasModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kelas <strong id="deleteKelasName"></strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteKelasForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
// Test script to ensure JavaScript is working
console.log('Super Admin Kelas page script loaded!');

// Global variables for kelas
let deleteKelasId = null;

// Edit Kelas Function
function editKelas(id) {
    console.log('editKelas called with id:', id);
    fetch(`/super-admin/kelas/${id}`)
        .then(response => {
            console.log('Response received:', response);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            document.getElementById('edit_kode_ruangan').value = data.kode_ruangan;
            document.getElementById('edit_nama_ruangan').value = data.nama_ruangan;
            document.getElementById('edit_kapasitas').value = data.kapasitas;
            document.getElementById('edit_tipe_ruangan').value = data.tipe_ruangan;
            document.getElementById('edit_prodi').value = data.prodi || '';
            document.getElementById('edit_status').value = data.status ? '1' : '0';
            document.getElementById('edit_fasilitas').value = data.fasilitas || '';
            document.getElementById('editKelasForm').action = `/super-admin/kelas/${id}`;
            
            const modal = new bootstrap.Modal(document.getElementById('editKelasModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data kelas: ' + error.message);
        });
}

// Delete Kelas Function
function deleteKelas(id, name) {
    console.log('deleteKelas called with id:', id, 'name:', name);
    deleteKelasId = id;
    document.getElementById('deleteKelasName').textContent = name;
    const modal = new bootstrap.Modal(document.getElementById('deleteKelasModal'));
    modal.show();
}

// Event Listeners for Kelas
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up kelas event listeners...');
    
    const editButtons = document.querySelectorAll('.edit-kelas-btn');
    console.log('Found edit buttons:', editButtons.length);
    editButtons.forEach((btn, index) => {
        console.log(`Setting up edit button ${index}:`, btn);
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            console.log('Edit button clicked, id:', id);
            editKelas(id);
        });
    });
    
    const deleteButtons = document.querySelectorAll('.delete-kelas-btn');
    console.log('Found delete buttons:', deleteButtons.length);
    deleteButtons.forEach((btn, index) => {
        console.log(`Setting up delete button ${index}:`, btn);
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            console.log('Delete button clicked, id:', id, 'name:', name);
            deleteKelas(id, name);
        });
    });
    
    // Handle delete form submission
    const deleteForm = document.getElementById('deleteKelasForm');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            if (deleteKelasId) {
                this.action = `/super-admin/kelas/${deleteKelasId}`;
            }
        });
    }
    
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    console.log('Bootstrap Modal available:', typeof bootstrap?.Modal !== 'undefined');
});
</script>
