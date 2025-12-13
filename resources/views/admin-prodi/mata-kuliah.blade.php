@extends('layouts.dosen')

@section('title', 'Kelola Mata Kuliah')
@section('page-title', 'Kelola Mata Kuliah')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Daftar Mata Kuliah {{ $prodi }}</h5>
                    <p class="text-muted mb-0">Kelola mata kuliah untuk program studi {{ $prodi }}</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMataKuliahModal">
                    <i class="fas fa-plus me-2"></i>Tambah Mata Kuliah
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
                                    <th>Kode MK</th>
                                    <th>Nama MK</th>
                                    <th>SKS</th>
                                    <th>Semester</th>
                                    <th>Dosen</th>
                                    <th>Kapasitas</th>
                                    <th>Tipe Kelas</th>
                                    <th>Praktikum</th>
                                    <th>Menit/SKS</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mataKuliahs as $mk)
                                <tr>
                                    <td>{{ $mk->kode_mk }}</td>
                                    <td>{{ $mk->nama_mk }}</td>
                                    <td>{{ $mk->sks }}</td>
                                    <td>{{ $mk->semester }}</td>
                                    <td>{{ $mk->dosen->name ?? 'Belum ditentukan' }}</td>
                                    <td>{{ $mk->kapasitas }}</td>
                                    <td>
                                        <span class="badge bg-{{ $mk->tipe_kelas == 'teori' ? 'primary' : 'success' }}">
                                            {{ ucfirst($mk->tipe_kelas) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($mk->ada_praktikum)
                                            <span class="badge bg-info">
                                                <i class="fas fa-flask me-1"></i>
                                                {{ $mk->sks_materi }}SKS Materi + {{ $mk->sks_praktikum }}SKS Praktikum
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Tidak Ada</span>
                                        @endif
                                    </td>
                                    <td>{{ $mk->menit_per_sks }} menit</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning me-1 edit-mk-btn" data-id="{{ $mk->id }}" title="Edit Mata Kuliah">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-mk-btn" data-id="{{ $mk->id }}" data-name="{{ $mk->nama_mk }}" title="Hapus Mata Kuliah">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Tidak ada data mata kuliah</td>
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

<!-- Add Mata Kuliah Modal -->
<div class="modal fade" id="addMataKuliahModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Mata Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin-prodi.mata-kuliah.store') }}" id="addMataKuliahForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Mata Kuliah</label>
                                <input type="text" class="form-control" name="kode_mk" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Mata Kuliah</label>
                                <input type="text" class="form-control" name="nama_mk" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4" id="sks_normal_field">
                            <div class="mb-3">
                                <label class="form-label">SKS</label>
                                <input type="number" class="form-control" name="sks" id="sks_input" min="1" max="12" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Semester</label>
                                <input type="text" class="form-control" name="semester" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Kapasitas</label>
                                <input type="number" class="form-control" name="kapasitas" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Dosen</label>
                                <select class="form-control" name="dosen_id" required>
                                    <option value="">Pilih Dosen</option>
                                    @foreach($dosens as $dosen)
                                        <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Kelas</label>
                                <select class="form-control" name="tipe_kelas" id="tipe_kelas" required onchange="togglePraktikumFields()">
                                    <option value="teori">Teori</option>
                                    <option value="praktikum">Praktikum</option>
                                </select>
                                <script>
                                // Inline script untuk memastikan fungsi berjalan
                                function togglePraktikumFields() {
                                    console.log('Inline togglePraktikumFields called');
                                    const tipeKelas = document.getElementById('tipe_kelas').value;
                                    const praktikumOptions = document.getElementById('praktikum_options');
                                    const sksNormalField = document.getElementById('sks_normal_field');
                                    
                                    console.log('Tipe Kelas:', tipeKelas);
                                    console.log('Praktikum Options:', praktikumOptions);
                                    console.log('SKS Normal Field:', sksNormalField);
                                    
                                    if (tipeKelas === 'praktikum') {
                                        if (praktikumOptions) {
                                            praktikumOptions.style.display = 'block';
                                            console.log('Showing praktikum options');
                                        }
                                        if (sksNormalField) {
                                            sksNormalField.style.display = 'none';
                                            console.log('Hiding normal SKS field');
                                        }
                                        // Update total SKS dan pastikan field SKS terisi
                                        updateTotalSKS();
                                    } else {
                                        if (praktikumOptions) {
                                            praktikumOptions.style.display = 'none';
                                            console.log('Hiding praktikum options');
                                        }
                                        if (sksNormalField) {
                                            sksNormalField.style.display = 'block';
                                            console.log('Showing normal SKS field');
                                        }
                                        // Clear total SKS
                                        const totalSKSDisplay = document.getElementById('total_sks_display');
                                        if (totalSKSDisplay) {
                                            totalSKSDisplay.value = '';
                                        }
                                    }
                                }
                                
                                function updateTotalSKS() {
                                    const sksMateri = parseInt(document.getElementById('sks_materi').value) || 0;
                                    const sksPraktikum = parseInt(document.getElementById('sks_praktikum').value) || 0;
                                    const totalSKS = sksMateri + sksPraktikum;
                                    
                                    console.log('SKS Materi:', sksMateri, 'SKS Praktikum:', sksPraktikum, 'Total:', totalSKS);
                                    
                                    const totalSKSDisplay = document.getElementById('total_sks_display');
                                    const sksInput = document.getElementById('sks_input');
                                    
                                    if (totalSKSDisplay) {
                                        totalSKSDisplay.value = totalSKS;
                                        console.log('Updated total SKS display to:', totalSKS);
                                    }
                                    if (sksInput) {
                                        sksInput.value = totalSKS;
                                        console.log('Updated SKS input to:', totalSKS);
                                    }
                                }
                                
                                // Tambahkan event listener setelah modal dibuka
                                document.addEventListener('DOMContentLoaded', function() {
                                    const select = document.getElementById('tipe_kelas');
                                    if (select) {
                                        select.addEventListener('change', function() {
                                            console.log('Change event triggered');
                                            togglePraktikumFields();
                                        });
                                    }
                                    
                                    // Event listener untuk SKS Materi dan Praktikum
                                    const sksMateri = document.getElementById('sks_materi');
                                    const sksPraktikum = document.getElementById('sks_praktikum');
                                    
                                    if (sksMateri) {
                                        sksMateri.addEventListener('input', updateTotalSKS);
                                        console.log('Added event listener to SKS Materi');
                                    }
                                    if (sksPraktikum) {
                                        sksPraktikum.addEventListener('input', updateTotalSKS);
                                        console.log('Added event listener to SKS Praktikum');
                                    }
                                    
                                    // Debug form submission
                                    const form = document.getElementById('addMataKuliahForm');
                                    if (form) {
                                        form.addEventListener('submit', function(e) {
                                            console.log('Form submit event triggered');
                                            console.log('Form data:', new FormData(form));
                                            
                                            // Check if all required fields are filled
                                            const requiredFields = form.querySelectorAll('[required]');
                                            let allValid = true;
                                            
                                            requiredFields.forEach(field => {
                                                if (!field.value.trim()) {
                                                    console.log('Required field empty:', field.name);
                                                    allValid = false;
                                                }
                                            });
                                            
                                            if (!allValid) {
                                                console.log('Form validation failed');
                                                e.preventDefault();
                                                alert('Mohon isi semua field yang wajib diisi');
                                                return false;
                                            }
                                            
                                            console.log('Form validation passed, submitting...');
                                        });
                                    }
                                });
                                </script>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Praktikum Options -->
                    <div class="row" id="praktikum_options" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Mata Kuliah dengan Praktikum:</strong> Akan dibuat 2 kelas terpisah (Materi + Praktikum)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SKS Materi</label>
                                <input type="number" class="form-control" name="sks_materi" id="sks_materi" min="1" max="6" value="2">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SKS Praktikum</label>
                                <input type="number" class="form-control" name="sks_praktikum" id="sks_praktikum" min="1" max="6" value="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Total SKS</label>
                                <input type="number" class="form-control" id="total_sks_display" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Menit per SKS</label>
                                <input type="number" class="form-control" name="menit_per_sks" min="30" max="120" value="50" required>
                                <small class="text-muted">Default: 50 menit per SKS</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                            </div>
                        </div>
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

<!-- Edit Mata Kuliah Modal -->
<div class="modal fade" id="editMataKuliahModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Mata Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editMataKuliahForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kode Mata Kuliah</label>
                                <input type="text" class="form-control" name="kode_mk" id="edit_kode_mk" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Mata Kuliah</label>
                                <input type="text" class="form-control" name="nama_mk" id="edit_nama_mk" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SKS</label>
                                <input type="number" class="form-control" name="sks" id="edit_sks" min="1" max="6" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Semester</label>
                                <input type="text" class="form-control" name="semester" id="edit_semester" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Kapasitas</label>
                                <input type="number" class="form-control" name="kapasitas" id="edit_kapasitas" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Dosen</label>
                                <select class="form-control" name="dosen_id" id="edit_dosen_id" required>
                                    <option value="">Pilih Dosen</option>
                                    @foreach($dosens as $dosen)
                                        <option value="{{ $dosen->id }}">{{ $dosen->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tipe Kelas</label>
                                <select class="form-control" name="tipe_kelas" id="edit_tipe_kelas" required onchange="toggleEditPraktikumFields()">
                                    <option value="teori">Teori</option>
                                    <option value="praktikum">Praktikum</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Edit Praktikum Options -->
                    <div class="row" id="edit_praktikum_options" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Mata Kuliah dengan Praktikum:</strong> Akan dibuat 2 kelas terpisah (Materi + Praktikum)
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SKS Materi</label>
                                <input type="number" class="form-control" name="sks_materi" id="edit_sks_materi" min="1" max="6" value="2">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">SKS Praktikum</label>
                                <input type="number" class="form-control" name="sks_praktikum" id="edit_sks_praktikum" min="1" max="6" value="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Total SKS</label>
                                <input type="number" class="form-control" id="edit_total_sks_display" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Menit per SKS</label>
                                <input type="number" class="form-control" name="menit_per_sks" id="edit_menit_per_sks" min="30" max="120" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3"></textarea>
                            </div>
                        </div>
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
<div class="modal fade" id="deleteMataKuliahModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Mata Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan!</strong> Tindakan ini tidak dapat dibatalkan.
                </div>
                <p>Apakah Anda yakin ingin menghapus mata kuliah <strong id="deleteMataKuliahName"></strong>?</p>
                <p class="text-muted">Semua data terkait mata kuliah ini akan dihapus secara permanen.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmDeleteMataKuliah()">
                    <i class="fas fa-trash me-1"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form Delete Hidden -->
<form id="deleteMataKuliahForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
function togglePraktikumFields() {
    console.log('togglePraktikumFields called');
    const tipeKelas = document.getElementById('tipe_kelas').value;
    const praktikumOptions = document.getElementById('praktikum_options');
    const sksInput = document.querySelector('input[name="sks"]');
    
    console.log('Tipe Kelas:', tipeKelas);
    console.log('Praktikum Options element:', praktikumOptions);
    
    if (tipeKelas === 'praktikum') {
        if (praktikumOptions) {
            praktikumOptions.style.display = 'block';
            console.log('Showing praktikum options');
        }
        if (sksInput) {
            sksInput.value = 3; // Default total SKS untuk praktikum
        }
        updateTotalSKS();
    } else {
        if (praktikumOptions) {
            praktikumOptions.style.display = 'none';
            console.log('Hiding praktikum options');
        }
        if (sksInput) {
            sksInput.value = '';
        }
    }
}

function updateTotalSKS() {
    const sksMateri = parseInt(document.getElementById('sks_materi').value) || 0;
    const sksPraktikum = parseInt(document.getElementById('sks_praktikum').value) || 0;
    const totalSKS = sksMateri + sksPraktikum;
    
    document.getElementById('total_sks_display').value = totalSKS;
    document.querySelector('input[name="sks"]').value = totalSKS;
}

// Edit Mata Kuliah Function
function editMataKuliah(id) {
    console.log('editMataKuliah called with id:', id);
    
    fetch(`/admin-prodi/mata-kuliah/${id}`)
        .then(response => {
            console.log('Response received:', response);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received:', data);
            document.getElementById('edit_kode_mk').value = data.kode_mk;
            document.getElementById('edit_nama_mk').value = data.nama_mk;
            document.getElementById('edit_sks').value = data.sks;
            document.getElementById('edit_semester').value = data.semester;
            document.getElementById('edit_kapasitas').value = data.kapasitas;
            document.getElementById('edit_dosen_id').value = data.dosen_id;
            document.getElementById('edit_tipe_kelas').value = data.tipe_kelas;
            document.getElementById('edit_menit_per_sks').value = data.menit_per_sks;
            document.getElementById('edit_deskripsi').value = data.deskripsi || '';
            document.getElementById('editMataKuliahForm').action = `/admin-prodi/mata-kuliah/${id}`;
            
            // Handle praktikum fields
            if (data.ada_praktikum) {
                document.getElementById('edit_praktikum_options').style.display = 'block';
                document.getElementById('edit_sks_materi').value = data.sks_materi || 0;
                document.getElementById('edit_sks_praktikum').value = data.sks_praktikum || 0;
                updateEditTotalSKS();
            } else {
                document.getElementById('edit_praktikum_options').style.display = 'none';
            }
            
            // Use Bootstrap Modal API
            const modal = new bootstrap.Modal(document.getElementById('editMataKuliahModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data mata kuliah: ' + error.message);
        });
}

function toggleEditPraktikumFields() {
    const tipeKelas = document.getElementById('edit_tipe_kelas').value;
    const praktikumOptions = document.getElementById('edit_praktikum_options');
    const sksInput = document.getElementById('edit_sks');
    
    if (tipeKelas === 'praktikum') {
        praktikumOptions.style.display = 'block';
        sksInput.value = 3; // Default total SKS untuk praktikum
        updateEditTotalSKS();
    } else {
        praktikumOptions.style.display = 'none';
        sksInput.value = '';
    }
}

function updateEditTotalSKS() {
    const sksMateri = parseInt(document.getElementById('edit_sks_materi').value) || 0;
    const sksPraktikum = parseInt(document.getElementById('edit_sks_praktikum').value) || 0;
    const totalSKS = sksMateri + sksPraktikum;
    
    document.getElementById('edit_total_sks_display').value = totalSKS;
    document.getElementById('edit_sks').value = totalSKS;
}

// Delete Mata Kuliah Functions
let deleteMataKuliahId = null;

// Delete Mata Kuliah Function
function deleteMataKuliah(id, name) {
    console.log('deleteMataKuliah called with id:', id, 'name:', name);
    
    deleteMataKuliahId = id;
    document.getElementById('deleteMataKuliahName').textContent = name;
    
    // Use Bootstrap Modal API
    const modal = new bootstrap.Modal(document.getElementById('deleteMataKuliahModal'));
    modal.show();
}

// Confirm Delete Mata Kuliah Function
function confirmDeleteMataKuliah() {
    if (deleteMataKuliahId) {
        const form = document.getElementById('deleteMataKuliahForm');
        form.action = `/admin-prodi/mata-kuliah/${deleteMataKuliahId}`;
        form.submit();
    }
}

// Event Listeners for Mata Kuliah
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up mata kuliah event listeners...');
    
    // Edit button event listeners
    const editButtons = document.querySelectorAll('.edit-mk-btn');
    console.log('Found edit buttons:', editButtons.length);
    
    editButtons.forEach((btn, index) => {
        console.log(`Setting up edit button ${index}:`, btn);
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            console.log('Edit button clicked, id:', id);
            editMataKuliah(id);
        });
    });
    
    // Delete button event listeners
    const deleteButtons = document.querySelectorAll('.delete-mk-btn');
    console.log('Found delete buttons:', deleteButtons.length);
    
    deleteButtons.forEach((btn, index) => {
        console.log(`Setting up delete button ${index}:`, btn);
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            console.log('Delete button clicked, id:', id, 'name:', name);
            deleteMataKuliah(id, name);
        });
    });
    
    // Test if Bootstrap is available
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    console.log('Bootstrap Modal available:', typeof bootstrap?.Modal !== 'undefined');
});

// Debug form submission
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Check if elements exist
    const tipeKelasSelect = document.getElementById('tipe_kelas');
    const praktikumOptions = document.getElementById('praktikum_options');
    const sksMateri = document.getElementById('sks_materi');
    const sksPraktikum = document.getElementById('sks_praktikum');
    
    console.log('tipe_kelas element:', tipeKelasSelect);
    console.log('praktikum_options element:', praktikumOptions);
    console.log('sks_materi element:', sksMateri);
    console.log('sks_praktikum element:', sksPraktikum);
    
    const form = document.querySelector('form[action*="mata-kuliah.store"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitted');
            console.log('Form data:', new FormData(form));
        });
    }
    
    // Add event listeners for SKS calculation
    if (sksMateri) {
        sksMateri.addEventListener('input', updateTotalSKS);
    }
    if (sksPraktikum) {
        sksPraktikum.addEventListener('input', updateTotalSKS);
    }
    
    // Add event listeners for edit SKS calculation
    const editSksMateri = document.getElementById('edit_sks_materi');
    const editSksPraktikum = document.getElementById('edit_sks_praktikum');
    
    if (editSksMateri) {
        editSksMateri.addEventListener('input', updateEditTotalSKS);
    }
    if (editSksPraktikum) {
        editSksPraktikum.addEventListener('input', updateEditTotalSKS);
    }
});
</script>
@endsection
