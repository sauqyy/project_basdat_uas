@extends('layouts.dosen')

@section('title', 'Manajemen Dosen')
@section('page-title', 'Manajemen Dosen')

<script>
// Test script to ensure JavaScript is working
console.log('Dosen page script loaded!');
</script>

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Daftar Dosen {{ $prodi }}</h5>
                    <p class="text-muted mb-0">Kelola akun dosen untuk program studi {{ $prodi }}</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDosenModal">
                    <i class="fas fa-plus me-2"></i>Tambah Dosen
                </button>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
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
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>NIP</th>
                                <th>Prodi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dosens as $index => $dosen)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $dosen->name }}</td>
                                    <td>{{ $dosen->email }}</td>
                                    <td>{{ $dosen->nip }}</td>
                                    <td>{{ $dosen->prodi }}</td>
                                    <td>
                                        <span class="badge bg-success">Aktif</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning me-1 edit-dosen-btn" data-id="{{ $dosen->id }}" title="Edit Dosen">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-dosen-btn" data-id="{{ $dosen->id }}" data-name="{{ $dosen->name }}" title="Hapus Dosen">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada dosen untuk prodi {{ $prodi }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Dosen -->
<div class="modal fade" id="tambahDosenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Dosen Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin-prodi.dosen.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" class="form-control" id="nip" name="nip" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="8">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required minlength="8">
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Info:</strong> Dosen akan otomatis terdaftar untuk prodi <strong>{{ $prodi }}</strong>
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

<!-- Modal Edit Dosen -->
<div class="modal fade" id="editDosenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Dosen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDosenForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_nip" class="form-label">NIP</label>
                        <input type="text" class="form-control" id="edit_nip" name="nip" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password Baru (Opsional)</label>
                        <input type="password" class="form-control" id="edit_password" name="password" minlength="8">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation" minlength="8">
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteDosenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Dosen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                </div>
                <p>Apakah Anda yakin ingin menghapus dosen <strong id="deleteDosenName"></strong>?</p>
                <p class="text-muted">Semua data terkait dosen ini akan dihapus secara permanen, termasuk mata kuliah yang diampu.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteDosen()">
                    <i class="fas fa-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form Delete Hidden -->
<form id="deleteDosenForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

<script>
// Global variables
let deleteDosenId = null;

// Edit Dosen Function
function editDosen(id) {
    console.log('editDosen called with id:', id);
    
    fetch(`/admin-prodi/dosen/${id}`)
        .then(response => {
            console.log('Response received:', response);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_nip').value = data.nip;
            document.getElementById('editDosenForm').action = `/admin-prodi/dosen/${id}`;
            
            // Use Bootstrap Modal API
            const modal = new bootstrap.Modal(document.getElementById('editDosenModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data dosen: ' + error.message);
        });
}

// Delete Dosen Function
function deleteDosen(id, name) {
    console.log('deleteDosen called with id:', id, 'name:', name);
    
    deleteDosenId = id;
    document.getElementById('deleteDosenName').textContent = name;
    
    // Use Bootstrap Modal API
    const modal = new bootstrap.Modal(document.getElementById('deleteDosenModal'));
    modal.show();
}

// Confirm Delete Function
function confirmDeleteDosen() {
    if (deleteDosenId) {
        const form = document.getElementById('deleteDosenForm');
        form.action = `/admin-prodi/dosen/${deleteDosenId}`;
        form.submit();
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners...');
    
    // Edit button event listeners
    const editButtons = document.querySelectorAll('.edit-dosen-btn');
    console.log('Found edit buttons:', editButtons.length);
    
    editButtons.forEach((btn, index) => {
        console.log(`Setting up edit button ${index}:`, btn);
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            console.log('Edit button clicked, id:', id);
            editDosen(id);
        });
    });
    
    // Delete button event listeners
    const deleteButtons = document.querySelectorAll('.delete-dosen-btn');
    console.log('Found delete buttons:', deleteButtons.length);
    
    deleteButtons.forEach((btn, index) => {
        console.log(`Setting up delete button ${index}:`, btn);
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            console.log('Delete button clicked, id:', id, 'name:', name);
            deleteDosen(id, name);
        });
    });
    
    // Test if Bootstrap is available
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    console.log('Bootstrap Modal available:', typeof bootstrap?.Modal !== 'undefined');
});
</script>
