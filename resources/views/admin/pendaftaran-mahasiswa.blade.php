@extends('layouts.admin')

@section('title', 'Pendaftaran Baru - Mahasiswa')
@section('page-title', 'Pendaftaran Baru')

@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <div class="me-3">
                <div style="width: 50px; height: 50px; background: linear-gradient(45deg, #1976d2, #42a5f5); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div>
                <h4 class="mb-1">Pendaftaran Baru</h4>
                <p class="text-muted mb-0">Lengkapi form berikut untuk membuat akun Mahasiswa</p>
            </div>
        </div>
        
        <form>
            <!-- Data Mahasiswa -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <div style="width: 40px; height: 40px; background: #e3f2fd; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #1976d2;">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    <h5 class="mb-0">Data Mahasiswa</h5>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">NIM <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Masukkan NIM" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Masukkan Nama Lengkap" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" placeholder="Masukkan Email Mahasiswa" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                        <select class="form-select" required>
                            <option value="">Pilih Jurusan</option>
                            <option value="tsd">Teknologi Sains Data</option>
                            <option value="ti">Teknik Informatika</option>
                            <option value="si">Sistem Informasi</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Data Dosen -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <div style="width: 40px; height: 40px; background: #e8f5e8; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #4caf50;">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                    <h5 class="mb-0">Data Dosen</h5>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Pilih Dosen Pembimbing</label>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button" id="dosenDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="text-align: left; padding: 12px 15px; border-radius: 8px; outline: none; box-shadow: none;">
                                <span id="selectedDosen">Pilih Dosen Pembimbing</span>
                            </button>
                            <ul class="dropdown-menu w-100" aria-labelledby="dosenDropdown">
                                <li><a class="dropdown-item" href="#" onclick="selectDosen('Dr. Ahmad Wijaya, S.Kom., M.T.', '001')">Dr. Ahmad Wijaya, S.Kom., M.T.</a></li>
                                <li><a class="dropdown-item" href="#" onclick="selectDosen('Prof. Dr. Siti Nurhaliza, S.Si., M.Sc.', '002')">Prof. Dr. Siti Nurhaliza, S.Si., M.Sc.</a></li>
                                <li><a class="dropdown-item" href="#" onclick="selectDosen('Dr. Budi Santoso, S.T., M.Eng.', '003')">Dr. Budi Santoso, S.T., M.Eng.</a></li>
                                <li><a class="dropdown-item" href="#" onclick="selectDosen('Dr. Rina Melati, S.Kom., M.T.', '004')">Dr. Rina Melati, S.Kom., M.T.</a></li>
                                <li><a class="dropdown-item" href="#" onclick="selectDosen('Dr. Eko Prasetyo, S.Si., M.Sc.', '005')">Dr. Eko Prasetyo, S.Si., M.Sc.</a></li>
                            </ul>
                        </div>
                        <input type="hidden" id="selectedDosenId" name="dosen_id">
                    </div>
                </div>
            </div>
            
            <!-- Password -->
            <div class="mb-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" placeholder="Masukkan Password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" placeholder="Konfirmasi Password" required>
                    </div>
                </div>
            </div>
            
            <!-- Submit -->
            <div class="d-flex flex-column align-items-center gap-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmAccount" style="width: 18px; height: 18px; border: 2px solid #000; border-radius: 3px;">
                    <label class="form-check-label" for="confirmAccount" style="font-weight: bold; font-size: 16px; margin-left: 8px;">
                        Konfirmasi pembuatan akun
                    </label>
                </div>
               <button type="submit" 
    class="btn btn-primary px-4"
    style="
        background: #1976d2; 
        border: none; 
        border-radius: 6px; 
        font-weight: bold; 
        font-size: 16px; 
        padding: 12px 24px; 
        display: flex; 
        align-items: center; 
        justify-content: center;
    ">
    Create
</button>
            </div>
        </form>
    </div>
</div>
@endsection

<style>
/* Remove all outlines and shadows from form elements */
.form-control, .form-select, #dosenDropdown {
    outline: none !important;
    box-shadow: none !important;
    border: 1px solid #dee2e6 !important;
}

.form-control:focus, .form-select:focus, #dosenDropdown:focus {
    outline: none !important;
    box-shadow: none !important;
    border-color: #dee2e6 !important;
}

.form-control:focus-visible, .form-select:focus-visible, #dosenDropdown:focus-visible {
    outline: none !important;
    box-shadow: none !important;
}

/* Remove Bootstrap's default focus ring */
.form-control:focus, .form-select:focus {
    border-color: #dee2e6 !important;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
}

/* Override Bootstrap's focus styles completely */
.form-control:focus, .form-select:focus, #dosenDropdown:focus {
    border-color: #dee2e6 !important;
    box-shadow: none !important;
    outline: none !important;
}
</style>

<script>
function selectDosen(nama, id) {
    document.getElementById('selectedDosen').textContent = nama;
    document.getElementById('selectedDosenId').value = id;
}
</script>
