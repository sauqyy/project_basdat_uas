@extends('layouts.dosen')

@section('title', 'Preferensi Jadwal Global')
@section('page-title', 'Preferensi Jadwal Global')

@section('styles')
<style>
/* Segmented Button Styles */
.btn-group .btn-check:checked + .btn {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.btn-group .btn {
    border-radius: 0;
    border-right: 1px solid #dee2e6;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
    border-right: none;
}

.btn-group .btn:not(:first-child):not(:last-child) {
    border-radius: 0;
}

/* Time slot styling */
.time-slot, .time-slot-edit {
    margin-right: 0.5rem;
}

.form-check-label {
    font-size: 0.9rem;
    padding-left: 0.25rem;
}

/* Modal styling */
.modal-xl {
    max-width: 90%;
}

@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
    }
    
    .btn-group .btn {
        font-size: 0.8rem;
        padding: 0.375rem 0.5rem;
    }
}
</style>
@endsection

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Preferensi Jadwal Global</h5>
                    <p class="text-muted mb-0">Set preferensi hari dan jam untuk seluruh mata kuliah Anda</p>
                    <small class="text-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Catatan:</strong> Hari yang tidak dipilih jam berarti Anda tidak bisa mengajar pada hari tersebut.
                    </small>
                </div>
                @if(!$preferensi)
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPreferensiModal">
                    <i class="fas fa-plus me-2"></i>Tambah Preferensi
                </button>
                @else
                    <button type="button" class="btn btn-warning" onclick="editPreferensi({{ $preferensi->id }})">
                        <i class="fas fa-edit me-2"></i>Edit Preferensi
                    </button>
                @endif
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
                

                @if($preferensi)
                    <div class="row">
                        <div class="col-12">
                            <h6>Preferensi Jadwal per Hari:</h6>
                            <div class="row">
                                @php
                                    $preferensiPerHari = [];
                                    if (is_array($preferensi->preferensi_hari)) {
                                        // Cek apakah preferensi_jam adalah array dengan index numerik (format lama) atau object/associative array (format baru)
                                        if (is_array($preferensi->preferensi_jam) && isset($preferensi->preferensi_jam[0]) && is_string($preferensi->preferensi_jam[0])) {
                                            // Format lama: jam global untuk semua hari
                                            foreach ($preferensi->preferensi_hari as $hari) {
                                                $preferensiPerHari[$hari] = $preferensi->preferensi_jam;
                                            }
                                        } else {
                                            // Format baru: jam per hari (object atau associative array)
                                            $preferensiPerHari = [];
                                            foreach ($preferensi->preferensi_jam as $hari => $jamData) {
                                                if (is_array($jamData)) {
                                                    // Format normal: {"Rabu": ["09:00-10:00", ...]}
                                                    $preferensiPerHari[$hari] = $jamData;
                                                } elseif (is_object($jamData) && isset($jamData->$hari)) {
                                                    // Format corrupted: {"Rabu": {"Rabu": ["09:00-10:00", ...]}}
                                                    $preferensiPerHari[$hari] = $jamData->$hari;
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari)
                                    @if(isset($preferensiPerHari[$hari]) && count($preferensiPerHari[$hari]) > 0)
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-header bg-primary text-white py-2">
                                                    <h6 class="mb-0">{{ $hari }}</h6>
                                                </div>
                                                <div class="card-body py-2">
                                                    @foreach($preferensiPerHari[$hari] as $jam)
                                                        @if(is_string($jam))
                                                            <span class="badge bg-success me-1 mb-1">{{ $jam }}</span>
                                                        @elseif(is_array($jam))
                                                            @foreach($jam as $jamItem)
                                                                @if(is_string($jamItem))
                                                                    <span class="badge bg-success me-1 mb-1">{{ $jamItem }}</span>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Prioritas:</h6>
                            <span class="badge bg-{{ $preferensi->prioritas == 1 ? 'danger' : ($preferensi->prioritas == 2 ? 'warning' : 'info') }}">
                                {{ $preferensi->prioritas == 1 ? 'Tinggi' : ($preferensi->prioritas == 2 ? 'Sedang' : 'Rendah') }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6>Catatan:</h6>
                            <p class="text-muted">{{ $preferensi->catatan ?? 'Tidak ada catatan' }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada preferensi jadwal global</h5>
                        <p class="text-muted">Tambahkan preferensi jadwal untuk seluruh mata kuliah Anda</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Preferensi -->
<div class="modal fade" id="tambahPreferensiModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Preferensi Jadwal Global</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('dosen.preferensi.store') }}" method="POST" id="preferensiForm">
                @csrf
                <div class="modal-body">
                    <!-- Segmented Button untuk Hari -->
                    <div class="mb-4">
                        <label class="form-label mb-3">Pilih Hari</label>
                        <div class="btn-group w-100" role="group" aria-label="Pilih Hari">
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $index => $hari)
                                <input type="radio" class="btn-check" name="selected_day" id="day_{{ $hari }}" value="{{ $hari }}" {{ $index == 0 ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary day-selector" for="day_{{ $hari }}">
                                    {{ $hari }}
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Tips:</strong> Jika tidak memilih jam untuk hari tertentu, berarti dosen tidak bisa mengajar pada hari tersebut.
                            </small>
                        </div>
                    </div>
                    
                    <!-- Input Jam per Hari -->
                    <div class="mb-4">
                        <label class="form-label mb-3">Pilih Jam untuk <span id="selected-day-name">Senin</span></label>
                        <div class="row" id="time-slots-container">
                            @foreach(['08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00', '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00'] as $jam)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input time-slot" type="checkbox" name="preferensi_jam_per_hari[Senin][]" value="{{ $jam }}" id="jam_senin_{{ str_replace(':', '_', str_replace('-', '_', $jam)) }}">
                                        <label class="form-check-label" for="jam_senin_{{ str_replace(':', '_', str_replace('-', '_', $jam)) }}">
                                            {{ $jam }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Hidden inputs untuk menyimpan data -->
                    <input type="hidden" name="preferensi_hari" id="preferensi_hari_input">
                    <input type="hidden" name="preferensi_jam" id="preferensi_jam_input">
                    
                    <div class="mb-3">
                        <label for="prioritas" class="form-label">Prioritas</label>
                        <select class="form-select" id="prioritas" name="prioritas" required>
                            <option value="">Pilih Prioritas</option>
                            <option value="1">Tinggi</option>
                            <option value="2">Sedang</option>
                            <option value="3">Rendah</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Catatan tambahan untuk preferensi jadwal"></textarea>
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

<!-- Modal Edit Preferensi -->
<div class="modal fade" id="editPreferensiModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Preferensi Jadwal Global</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPreferensiForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Segmented Button untuk Hari -->
                    <div class="mb-4">
                        <label class="form-label mb-3">Pilih Hari</label>
                        <div class="btn-group w-100" role="group" aria-label="Pilih Hari">
                            @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $index => $hari)
                                <input type="radio" class="btn-check" name="selected_day_edit" id="edit_day_{{ $hari }}" value="{{ $hari }}" {{ $index == 0 ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary day-selector-edit" for="edit_day_{{ $hari }}">
                                            {{ $hari }}
                                        </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Input Jam per Hari dengan Kategori Waktu -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label mb-0">Pilih Jam untuk <span id="edit-selected-day-name" class="fw-bold text-primary">Senin</span></label>
                            <button type="button" class="btn btn-sm btn-success" onclick="selectAllTimeSlotsEdit()">
                                <i class="fas fa-check-double me-1"></i>Select All
                            </button>
                        </div>
                        
                        <div id="edit-time-slots-container">
                            <!-- Pagi (08:00 - 12:00) -->
                            <div class="card mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-sun text-warning me-2"></i>
                                        <strong>Pagi (08:00 - 12:00)</strong>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectCategoryEdit('pagi')">
                                        <i class="fas fa-check me-1"></i>Select Pagi
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="edit-pagi-slots">
                                        @foreach(['08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00'] as $jam)
                                            <div class="col-md-6 col-lg-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input time-slot-edit pagi-slot" type="checkbox" name="preferensi_jam_per_hari_edit[Senin][]" value="{{ $jam }}" id="edit_jam_senin_{{ str_replace(':', '_', str_replace('-', '_', $jam)) }}">
                                                    <label class="form-check-label" for="edit_jam_senin_{{ str_replace(':', '_', str_replace('-', '_', $jam)) }}">
                                                        {{ $jam }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Siang (13:00 - 15:00) -->
                            <div class="card mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-cloud-sun text-info me-2"></i>
                                        <strong>Siang (13:00 - 15:00)</strong>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectCategoryEdit('siang')">
                                        <i class="fas fa-check me-1"></i>Select Siang
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="edit-siang-slots">
                                        @foreach(['13:00-14:00', '14:00-15:00'] as $jam)
                                            <div class="col-md-6 col-lg-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input time-slot-edit siang-slot" type="checkbox" name="preferensi_jam_per_hari_edit[Senin][]" value="{{ $jam }}" id="edit_jam_senin_{{ str_replace(':', '_', str_replace('-', '_', $jam)) }}">
                                                    <label class="form-check-label" for="edit_jam_senin_{{ str_replace(':', '_', str_replace('-', '_', $jam)) }}">
                                                        {{ $jam }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sore (15:00 - 17:00) -->
                            <div class="card mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-cloud text-secondary me-2"></i>
                                        <strong>Sore (15:00 - 17:00)</strong>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectCategoryEdit('sore')">
                                        <i class="fas fa-check me-1"></i>Select Sore
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="edit-sore-slots">
                                        @foreach(['15:00-16:00', '16:00-17:00'] as $jam)
                                            <div class="col-md-6 col-lg-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input time-slot-edit sore-slot" type="checkbox" name="preferensi_jam_per_hari_edit[Senin][]" value="{{ $jam }}" id="edit_jam_senin_{{ str_replace(':', '_', str_replace('-', '_', $jam)) }}">
                                                    <label class="form-check-label" for="edit_jam_senin_{{ str_replace(':', '_', str_replace('-', '_', $jam)) }}">
                                                        {{ $jam }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden inputs untuk menyimpan data -->
                    <input type="hidden" name="preferensi_hari" id="edit_preferensi_hari_input">
                    <input type="hidden" name="preferensi_jam" id="edit_preferensi_jam_input">
                    
                    <div class="mb-3">
                        <label for="edit_prioritas" class="form-label">Prioritas</label>
                        <select class="form-select" id="edit_prioritas" name="prioritas" required>
                            <option value="">Pilih Prioritas</option>
                            <option value="1">Tinggi</option>
                            <option value="2">Sedang</option>
                            <option value="3">Rendah</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="edit_catatan" name="catatan" rows="3"></textarea>
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
// Data untuk menyimpan preferensi per hari
let preferensiPerHari = {
    'Senin': [],
    'Selasa': [],
    'Rabu': [],
    'Kamis': [],
    'Jumat': []
};

let editPreferensiPerHari = {
    'Senin': [],
    'Selasa': [],
    'Rabu': [],
    'Kamis': [],
    'Jumat': []
};

// Generate time slots untuk semua hari
function generateTimeSlots() {
    const timeSlots = ['08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00', '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00'];
    const hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    
    hariList.forEach(hari => {
        preferensiPerHari[hari] = [];
        editPreferensiPerHari[hari] = [];
    });
}

// Update time slots display berdasarkan hari yang dipilih
function updateTimeSlots(day, isEdit = false) {
    const containerId = isEdit ? 'edit-time-slots-container' : 'time-slots-container';
    const dayNameId = isEdit ? 'edit-selected-day-name' : 'selected-day-name';
    const container = document.getElementById(containerId);
    const dayName = document.getElementById(dayNameId);
    
    dayName.textContent = day;
    
    const pagiSlots = ['08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00'];
    const siangSlots = ['13:00-14:00', '14:00-15:00'];
    const soreSlots = ['15:00-16:00', '16:00-17:00'];
    
    const createSlotHTML = (slots, category) => {
        return slots.map(jam => {
            const inputId = isEdit ? `edit_jam_${day.toLowerCase()}_${jam.replace(/:/g, '_').replace(/-/g, '_')}` : `jam_${day.toLowerCase()}_${jam.replace(/:/g, '_').replace(/-/g, '_')}`;
            const inputName = isEdit ? `preferensi_jam_per_hari_edit[${day}][]` : `preferensi_jam_per_hari[${day}][]`;
            const isChecked = isEdit ? editPreferensiPerHari[day].includes(jam) : preferensiPerHari[day].includes(jam);
            const slotClass = isEdit ? 'time-slot-edit' : 'time-slot';
            
            return `
                <div class="col-md-6 col-lg-3 mb-2">
                    <div class="form-check">
                        <input class="form-check-input ${slotClass} ${category}-slot" type="checkbox" name="${inputName}" value="${jam}" id="${inputId}" ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label" for="${inputId}">
                            ${jam}
                        </label>
                    </div>
                </div>
            `;
        }).join('');
    };
    
    const html = `
        <!-- Pagi (08:00 - 12:00) -->
        <div class="card mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-sun text-warning me-2"></i>
                    <strong>Pagi (08:00 - 12:00)</strong>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectCategoryEdit('pagi')">
                    <i class="fas fa-check me-1"></i>Select Pagi
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    ${createSlotHTML(pagiSlots, 'pagi')}
                </div>
            </div>
        </div>
        
        <!-- Siang (13:00 - 15:00) -->
        <div class="card mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-cloud-sun text-info me-2"></i>
                    <strong>Siang (13:00 - 15:00)</strong>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectCategoryEdit('siang')">
                    <i class="fas fa-check me-1"></i>Select Siang
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    ${createSlotHTML(siangSlots, 'siang')}
                </div>
            </div>
        </div>
        
        <!-- Sore (15:00 - 17:00) -->
        <div class="card mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-cloud text-secondary me-2"></i>
                    <strong>Sore (15:00 - 17:00)</strong>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectCategoryEdit('sore')">
                    <i class="fas fa-check me-1"></i>Select Sore
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    ${createSlotHTML(soreSlots, 'sore')}
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
}

// Event listeners untuk segmented buttons
document.addEventListener('DOMContentLoaded', function() {
    generateTimeSlots();
    
    // Event listeners untuk modal tambah
    document.querySelectorAll('input[name="selected_day"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                updateTimeSlots(this.value, false);
            }
        });
    });
    
    // Event listeners untuk modal edit
    document.querySelectorAll('input[name="selected_day_edit"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.checked) {
                updateTimeSlots(this.value, true);
            }
        });
    });
    
    // Event listeners untuk time slots
    document.addEventListener('change', function(e) {
        console.log('Change event triggered:', e.target);
        
        if (e.target.classList.contains('time-slot')) {
            const day = e.target.name.match(/\[(.*?)\]/)[1];
            console.log('Time slot changed for day:', day, 'value:', e.target.value, 'checked:', e.target.checked);
            
            if (e.target.checked) {
                if (!preferensiPerHari[day].includes(e.target.value)) {
                    preferensiPerHari[day].push(e.target.value);
                }
            } else {
                preferensiPerHari[day] = preferensiPerHari[day].filter(jam => jam !== e.target.value);
            }
            console.log('Updated preferensiPerHari:', preferensiPerHari);
            updateHiddenInputs();
        }
        
        if (e.target.classList.contains('time-slot-edit')) {
            const day = e.target.name.match(/\[(.*?)\]/)[1];
            console.log('Edit time slot changed for day:', day, 'value:', e.target.value, 'checked:', e.target.checked);
            
            if (e.target.checked) {
                if (!editPreferensiPerHari[day].includes(e.target.value)) {
                    editPreferensiPerHari[day].push(e.target.value);
                }
            } else {
                editPreferensiPerHari[day] = editPreferensiPerHari[day].filter(jam => jam !== e.target.value);
            }
            console.log('Updated editPreferensiPerHari:', editPreferensiPerHari);
            updateEditHiddenInputs();
        }
    });
});

// Update hidden inputs untuk form tambah
function updateHiddenInputs() {
    const hari = [];
    const jamPerHari = {};
    
    Object.keys(preferensiPerHari).forEach(day => {
        if (preferensiPerHari[day].length > 0) {
            hari.push(day);
            jamPerHari[day] = preferensiPerHari[day];
        }
    });
    
    console.log('Updating hidden inputs:', { hari, jamPerHari });
    
    const hariInput = document.getElementById('preferensi_hari_input');
    const jamInput = document.getElementById('preferensi_jam_input');
    
    if (hariInput) hariInput.value = JSON.stringify(hari);
    if (jamInput) jamInput.value = JSON.stringify(jamPerHari);
    
    // Validasi minimal
    if (hari.length === 0) {
        alert('Pilih minimal 1 hari untuk mengajar!');
        return false;
    }
    
    return true;
}

// Update hidden inputs untuk form edit
function updateEditHiddenInputs() {
    const hari = [];
    const jamPerHari = {};
    
    Object.keys(editPreferensiPerHari).forEach(day => {
        if (editPreferensiPerHari[day].length > 0) {
            hari.push(day);
            // Pastikan format yang benar: hanya array jam untuk hari tersebut
            jamPerHari[day] = [...editPreferensiPerHari[day]];
        }
    });
    
    console.log('updateEditHiddenInputs - hari:', hari);
    console.log('updateEditHiddenInputs - jamPerHari:', jamPerHari);
    
    document.getElementById('edit_preferensi_hari_input').value = JSON.stringify(hari);
    document.getElementById('edit_preferensi_jam_input').value = JSON.stringify(jamPerHari);
}

function editPreferensi(id) {
    console.log('Edit preferensi called with ID:', id);
    
    // Reset data - pastikan semua hari dikosongkan
    ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'].forEach(day => {
        editPreferensiPerHari[day] = [];
    });
    
    // Set form action
    document.getElementById('editPreferensiForm').action = `/dosen/preferensi/${id}`;
    
    // Show modal dengan form kosong
    const modal = new bootstrap.Modal(document.getElementById('editPreferensiModal'));
    modal.show();
}

// Fungsi untuk select all time slots edit
function selectAllTimeSlotsEdit() {
    const checkboxes = document.querySelectorAll('#edit-time-slots-container .time-slot-edit');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
        // Trigger change event
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

// Fungsi untuk select category (pagi, siang, sore) edit
function selectCategoryEdit(category) {
    const checkboxes = document.querySelectorAll(`#edit-time-slots-container .${category}-slot`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
        // Trigger change event
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

// Add form submission handler
document.addEventListener('DOMContentLoaded', function() {
    const preferensiForm = document.getElementById('preferensiForm');
    if (preferensiForm) {
        preferensiForm.addEventListener('submit', function(e) {
            console.log('Form submission triggered');
            
            // Update hidden inputs before submit
            if (!updateHiddenInputs()) {
                e.preventDefault();
                return false;
            }
            
            console.log('Form submitted successfully');
        });
    }
    
    // Add form submission handler for edit form
    const editPreferensiForm = document.getElementById('editPreferensiForm');
    if (editPreferensiForm) {
        editPreferensiForm.addEventListener('submit', function(e) {
            console.log('Edit form submission triggered');
            
            // Update hidden inputs before submit
            updateEditHiddenInputs();
            
            // Log form data before submission
            const formData = new FormData(this);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            console.log('Edit form submitted successfully');
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
            }
            
            // Don't prevent default form submission
            // Let the form submit naturally and redirect
        });
    }
});
</script>
@endsection