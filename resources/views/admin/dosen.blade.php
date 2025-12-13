@extends('layouts.admin')

@section('title', 'Daftar Dosen')
@section('page-title', 'Daftar User')

@section('content')
<!-- Segmented Button Tabs -->
<div class="user-segmented-control mb-4">
    <button class="user-segmented-btn" id="mahasiswa-tab" type="button">
        Mahasiswa
    </button>
    <button class="user-segmented-btn active" id="dosen-tab" type="button">
        Dosen
    </button>
</div>

<!-- Tab Content -->
<div class="tab-content" id="userTabsContent">
    <!-- Mahasiswa Tab -->
    <div class="tab-pane fade" id="mahasiswa" role="tabpanel">
        <!-- Search/Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-1">Daftar Mahasiswa</h5>
                <p class="text-muted mb-3">List dari akun dosen yang terdaftar</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Masukkan nama mahasiswa">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option selected>Jurusan</option>
                            <option>Teknologi Sains Data</option>
                            <option>Teknik Informatika</option>
                            <option>Sistem Informasi</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Table Card -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Judul</th>
                                <th>Pembimbing</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-bold">Raihan Naufal Saudi</div>
                                        <small class="text-muted">164231107 • Teknologi Sains Data</small><br>
                                        <small class="text-muted">rai.archv@gmail.com</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Unknown</span>
                                </td>
                                <td>
                                    <div>Dr. aiwdaih adwiawdia</div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dosen Tab -->
    <div class="tab-pane fade show active" id="dosen" role="tabpanel">
        <!-- Search/Filter Card -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-1">Daftar Dosen</h5>
                <p class="text-muted mb-3">List dari dosen awidwrawdirandboard wadfahrwdididwfdn awoidawod</p>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Masukkan nama dosen">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select">
                            <option selected>Departemen</option>
                            <option>Teknologi Sains Data</option>
                            <option>Teknik Informatika</option>
                            <option>Sistem Informasi</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Table Card -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Dosen</th>
                                <th>Departemen</th>
                                <th>Mahasiswa Bimbingan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-bold">Dr. Ahmad Wijaya</div>
                                        <small class="text-muted">123456789</small><br>
                                        <small class="text-muted">ahmad.wijaya@kampusmerdeka.ac.id</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">Teknologi Sains Data</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">15 Mahasiswa</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-bold">Prof. Dr. Siti Nurhaliza</div>
                                        <small class="text-muted">987654321</small><br>
                                        <small class="text-muted">siti.nurhaliza@kampusmerdeka.ac.id</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">Teknik Informatika</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">12 Mahasiswa</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div>
                                        <div class="fw-bold">Dr. Budi Santoso</div>
                                        <small class="text-muted">456789123</small><br>
                                        <small class="text-muted">budi.santoso@kampusmerdeka.ac.id</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning">Sistem Informasi</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">8 Mahasiswa</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        <span class="text-muted">Halaman 1 dari 5</span>
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">&lt; Previous</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next &gt;</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    document.getElementById('mahasiswa-tab').addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.user-segmented-btn').forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');
        
        // Hide all tab content
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        // Show mahasiswa content
        document.getElementById('mahasiswa').classList.add('show', 'active');
    });
    
    document.getElementById('dosen-tab').addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.user-segmented-btn').forEach(btn => btn.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');
        
        // Hide all tab content
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });
        
        // Show dosen content
        document.getElementById('dosen').classList.add('show', 'active');
    });
});
</script>
@endsection