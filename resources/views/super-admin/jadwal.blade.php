@extends('layouts.dosen')

@section('title', 'Jadwal')
@section('page-title', 'Pembuatan Jadwal')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Daftar Jadwal Perkuliahan</h5>
                    <p class="text-muted mb-0">Kelola dan generate jadwal untuk semua program studi</p>
                </div>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#generateJadwalModal">
                    <i class="fas fa-robot me-2"></i>Generate Jadwal AI
                </button>
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

                    <!-- Segmented Button untuk Filter Prodi -->
                    <div class="mb-4">
                        <label class="form-label mb-3">Filter Berdasarkan Prodi:</label>
                        <form method="GET" action="{{ route('super-admin.jadwal') }}" id="prodiFilterForm">
                            <div class="btn-group w-100" role="group" aria-label="Filter Prodi">
                                @foreach($prodiList as $key => $prodi)
                                    @php
                                        $safeKey = str_replace(' ', '_', $key);
                                    @endphp
                                <input type="radio" class="btn-check" name="prodi" id="prodi_{{ $safeKey }}" value="{{ $key }}" 
                                       {{ $prodiFilter == $key ? 'checked' : '' }} onchange="document.getElementById('prodiFilterForm').submit();">
                                    <label class="btn btn-outline-primary" for="prodi_{{ $safeKey }}">
                                        {{ $prodi }}
                                    </label>
                                @endforeach
                            </div>
                        </form>
                    </div>

                    <!-- Segmented Button untuk Tampilan -->
                    <div class="mb-4">
                        <label class="form-label mb-3">Tampilan Jadwal:</label>
                        <div class="btn-group" role="group" aria-label="Tampilan Jadwal">
                            <input type="radio" class="btn-check" name="view_type" id="view_table" value="table" checked>
                            <label class="btn btn-outline-secondary" for="view_table">
                                <i class="fas fa-table me-1"></i>Tabel
                            </label>
                            
                            <input type="radio" class="btn-check" name="view_type" id="view_calendar" value="calendar">
                            <label class="btn btn-outline-secondary" for="view_calendar">
                                <i class="fas fa-calendar-alt me-1"></i>Kalender
                            </label>
                        </div>
                    </div>

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

                    @if(session('warnings'))
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
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('failed_mata_kuliah'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-times-circle me-2"></i>Mata Kuliah Tidak Ter-generate ({{ count(session('failed_mata_kuliah')) }} mata kuliah)
                            </h5>
                            <p class="mb-3">Berikut adalah mata kuliah yang tidak berhasil di-generate beserta alasannya:</p>
                            
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered bg-white">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%;">No</th>
                                            <th style="width: 15%;">Kode MK</th>
                                            <th style="width: 25%;">Nama Mata Kuliah</th>
                                            <th style="width: 20%;">Dosen</th>
                                            <th style="width: 10%;">SKS / Kapasitas</th>
                                            <th style="width: 25%;">Alasan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(session('failed_mata_kuliah') as $index => $failed)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td><strong>{{ $failed['kode_mk'] }}</strong></td>
                                                <td>{{ $failed['nama_mk'] }}</td>
                                                <td>{{ $failed['dosen'] }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">{{ $failed['sks'] }} SKS</span><br>
                                                    <small class="text-muted">{{ $failed['kapasitas'] }} mhs</small>
                                                </td>
                                                <td>
                                                    <small class="text-danger">
                                                        <i class="fas fa-info-circle me-1"></i>
                                                        {{ $failed['reason'] }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <hr>
                            <div class="alert alert-light mb-0">
                                <h6 class="fw-bold"><i class="fas fa-lightbulb me-2 text-warning"></i>Solusi yang Disarankan:</h6>
                                <ul class="mb-0 small">
                                    <li><strong>Jika tidak ada ruangan yang sesuai:</strong> Tambahkan ruangan dengan kapasitas yang cukup untuk prodi terkait</li>
                                    <li><strong>Jika tidak ada jam slot yang sesuai:</strong> Periksa pengaturan menit per SKS atau preferensi dosen</li>
                                    <li><strong>Jika semua slot terisi:</strong> Tambahkan lebih banyak ruangan atau kurangi jumlah mata kuliah per semester</li>
                                </ul>
                            </div>
                            
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Table View -->
                    <div id="table-view" class="view-container">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Kuliah</th>
                                        <th>Dosen</th>
                                        <th>Ruangan</th>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Prodi</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwals as $index => $jadwal)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $jadwal->mataKuliah->kode_mk ?? 'N/A' }}</div>
                                            <div class="text-muted small">{{ $jadwal->mataKuliah->nama_mk ?? 'N/A' }}</div>
                                        </td>
                                        <td>{{ $jadwal->mataKuliah->dosen->name ?? 'N/A' }}</td>
                                        <td>
                                            {{ $jadwal->ruangan->nama_ruangan ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">
                                                <span class="badge bg-{{ $jadwal->ruangan->tipe_ruangan == 'lab' ? 'success' : 'info' }}">
                                                    {{ ucfirst($jadwal->ruangan->tipe_ruangan ?? 'kelas') }}
                                                </span>
                                            </small>
                                        </td>
                                        <td>{{ $jadwal->hari }}</td>
                                        <td>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $jadwal->prodi ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Aktif</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada jadwal. Klik "Generate Jadwal AI" untuk membuat jadwal.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Calendar View -->
                    <div id="calendar-view" class="view-container" style="display: none;">
                        <div class="schedule-container">
                            <div class="schedule-header">
                                <h5 class="schedule-title">Jadwal Mengajar</h5>
                                <p class="schedule-subtitle">Jadwal mata kuliah yang telah di-plot oleh sistem AI</p>
                            </div>

                            <!-- Simple Calendar Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 120px;">Jam</th>
                                            <th class="text-center">Senin</th>
                                            <th class="text-center">Selasa</th>
                                            <th class="text-center">Rabu</th>
                                            <th class="text-center">Kamis</th>
                                            <th class="text-center">Jumat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $timeSlots = [
                                                '08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00',
                                                '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00'
                                            ];
                                            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                                            
                                            // Create a mapping array that supports multiple jadwal per slot
                                            $scheduleMap = [];
                                            foreach($jadwals as $jadwal) {
                                                $jamMulai = date('H:i', strtotime($jadwal->jam_mulai));
                                                $jamSelesai = date('H:i', strtotime($jadwal->jam_selesai));
                                                
                                                // Find all time slots that this jadwal overlaps with
                                                foreach($timeSlots as $timeSlot) {
                                                    $slotStart = substr($timeSlot, 0, 5);
                                                    $slotEnd = substr($timeSlot, 6, 5);
                                                    
                                                    // Check if jadwal overlaps with this time slot
                                                    if ($jamMulai <= $slotStart && $jamSelesai > $slotStart) {
                                                        $key = $jadwal->hari . '_' . $slotStart;
                                                        if (!isset($scheduleMap[$key])) {
                                                            $scheduleMap[$key] = [];
                                                        }
                                                        $scheduleMap[$key][] = $jadwal;
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        @foreach($timeSlots as $timeSlot)
                                        <tr>
                                            <td class="text-center fw-bold bg-light">{{ $timeSlot }}</td>
                                            @foreach($days as $day)
                                                @php
                                                    $slotStart = substr($timeSlot, 0, 5);
                                                    $key = $day . '_' . $slotStart;
                                                    $jadwalsInSlot = $scheduleMap[$key] ?? [];
                                                @endphp
                                                <td class="text-center" style="height: 80px; vertical-align: top; padding: 2px;">
                                                    @if(count($jadwalsInSlot) > 0)
                                                        @foreach($jadwalsInSlot as $jadwal)
                                                            @php
                                                                $jamMulai = date('H:i', strtotime($jadwal->jam_mulai));
                                                                $jamSelesai = date('H:i', strtotime($jadwal->jam_selesai));
                                                            @endphp
                                                            <div class="course-item p-1 rounded mb-1" 
                                                                 style="background-color: {{ $jadwal->mataKuliah->ada_praktikum ? '#e3f2fd' : '#f3e5f5' }}; border-left: 3px solid {{ $jadwal->mataKuliah->ada_praktikum ? '#2196f3' : '#9c27b0' }}; font-size: 10px; cursor: pointer;"
                                                                 data-bs-toggle="modal" 
                                                                 data-bs-target="#courseModal"
                                                                 data-course-code="{{ $jadwal->mataKuliah->kode_mk ?? 'N/A' }}"
                                                                 data-course-name="{{ $jadwal->mataKuliah->nama_mk ?? 'N/A' }}"
                                                                 data-course-room="{{ $jadwal->ruangan->nama_ruangan ?? 'N/A' }}"
                                                                 data-course-time="{{ $jamMulai }}-{{ $jamSelesai }}"
                                                                 data-course-day="{{ $jadwal->hari }}"
                                                                 data-course-praktikum="{{ $jadwal->mataKuliah->ada_praktikum ? 'Ya' : 'Tidak' }}"
                                                                 data-course-dosen="{{ $jadwal->mataKuliah->dosen->name ?? 'N/A' }}"
                                                                 data-course-sks="{{ $jadwal->mataKuliah->sks ?? 'N/A' }}"
                                                                 data-course-prodi="{{ $jadwal->prodi ?? 'N/A' }}">
                                                                <div class="fw-bold" style="font-size: 9px;">{{ $jadwal->mataKuliah->kode_mk ?? 'N/A' }}</div>
                                                                <div class="text-muted" style="font-size: 8px;">{{ Str::limit($jadwal->mataKuliah->nama_mk ?? 'N/A', 12) }}</div>
                                                                <div class="text-muted" style="font-size: 7px;">{{ $jadwal->ruangan->nama_ruangan ?? 'N/A' }}</div>
                                                                <div class="text-muted" style="font-size: 7px;">{{ $jamMulai }}-{{ $jamSelesai }}</div>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted" style="font-size: 12px;">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Debug Information -->
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Course Detail Modal -->
<div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="courseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">Detail Mata Kuliah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">Informasi Mata Kuliah</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Kode MK:</strong></td>
                                <td id="modal-course-code">-</td>
                            </tr>
                            <tr>
                                <td><strong>Nama MK:</strong></td>
                                <td id="modal-course-name">-</td>
                            </tr>
                            <tr>
                                <td><strong>SKS:</strong></td>
                                <td id="modal-course-sks">-</td>
                            </tr>
                            <tr>
                                <td><strong>Praktikum:</strong></td>
                                <td id="modal-course-praktikum">-</td>
                            </tr>
                            <tr>
                                <td><strong>Prodi:</strong></td>
                                <td id="modal-course-prodi">-</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Informasi Jadwal</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Hari:</strong></td>
                                <td id="modal-course-day">-</td>
                            </tr>
                            <tr>
                                <td><strong>Waktu:</strong></td>
                                <td id="modal-course-time">-</td>
                            </tr>
                            <tr>
                                <td><strong>Ruangan:</strong></td>
                                <td id="modal-course-room">-</td>
                            </tr>
                            <tr>
                                <td><strong>Dosen:</strong></td>
                                <td id="modal-course-dosen">-</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
/* Segmented Button Styles */
.btn-group .btn-check:checked + .btn {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.btn-group .btn-check:checked + .btn:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

/* Calendar Styles */
.schedule-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.schedule-header {
    margin-bottom: 20px;
    text-align: center;
}

.schedule-title {
    color: #333;
    margin-bottom: 5px;
}

.schedule-subtitle {
    color: #666;
    font-size: 14px;
    margin-bottom: 0;
}

.course-item {
    transition: all 0.2s ease;
    cursor: pointer;
}

.course-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(255, 0, 0, 0.1);
}

.debug-info {
    font-size: 12px;
    line-height: 1.4;
    color: #666;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing calendar toggle...');
    
    // View Toggle Functionality
    const viewTable = document.getElementById('view_table');
    const viewCalendar = document.getElementById('view_calendar');
    const tableView = document.getElementById('table-view');
    const calendarView = document.getElementById('calendar-view');
    
    console.log('Elements found:', {
        viewTable: viewTable,
        viewCalendar: viewCalendar,
        tableView: tableView,
        calendarView: calendarView
    });
    
    function toggleView() {
        console.log('Toggle view called');
        console.log('viewTable.checked:', viewTable?.checked);
        console.log('viewCalendar.checked:', viewCalendar?.checked);
        
        if (viewTable && viewTable.checked) {
            console.log('Showing table view');
            if (tableView) tableView.style.display = 'block';
            if (calendarView) calendarView.style.display = 'none';
        } else if (viewCalendar && viewCalendar.checked) {
            console.log('Showing calendar view');
            if (tableView) tableView.style.display = 'none';
            if (calendarView) calendarView.style.display = 'block';
        }
    }
    
    if (viewTable) {
        viewTable.addEventListener('change', toggleView);
        console.log('Added event listener to viewTable');
    }
    
    if (viewCalendar) {
        viewCalendar.addEventListener('change', toggleView);
        console.log('Added event listener to viewCalendar');
    }
    
    // Course Modal Functionality
    const courseModal = document.getElementById('courseModal');
    if (courseModal) {
        courseModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            // Extract data from the clicked course item
            const courseCode = button.getAttribute('data-course-code');
            const courseName = button.getAttribute('data-course-name');
            const courseRoom = button.getAttribute('data-course-room');
            const courseTime = button.getAttribute('data-course-time');
            const courseDay = button.getAttribute('data-course-day');
            const coursePraktikum = button.getAttribute('data-course-praktikum');
            const courseDosen = button.getAttribute('data-course-dosen');
            const courseSks = button.getAttribute('data-course-sks');
            const courseProdi = button.getAttribute('data-course-prodi');
            
            // Update modal content
            document.getElementById('modal-course-code').textContent = courseCode;
            document.getElementById('modal-course-name').textContent = courseName;
            document.getElementById('modal-course-room').textContent = courseRoom;
            document.getElementById('modal-course-time').textContent = courseTime;
            document.getElementById('modal-course-day').textContent = courseDay;
            document.getElementById('modal-course-praktikum').textContent = coursePraktikum;
            document.getElementById('modal-course-dosen').textContent = courseDosen;
            document.getElementById('modal-course-sks').textContent = courseSks;
            document.getElementById('modal-course-prodi').textContent = courseProdi;
        });
    }
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

<!-- Modal Konfirmasi Generate Jadwal -->
<div class="modal fade" id="generateJadwalModal" tabindex="-1" aria-labelledby="generateJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pb-4">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10" style="width: 80px; height: 80px;">
                        <i class="fas fa-robot text-success" style="font-size: 40px;"></i>
                    </div>
                </div>
                <h4 class="mb-3">Generate Jadwal AI?</h4>
                <p class="text-muted mb-4">
                    Sistem akan membuat jadwal otomatis untuk semua program studi menggunakan algoritma AI. 
                    Jadwal yang sudah ada akan dihapus dan diganti dengan jadwal baru.
                </p>
                <div class="alert alert-warning text-start" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Peringatan:</strong> Proses ini akan menghapus semua jadwal yang sudah ada dan tidak dapat dibatalkan.
                </div>
                <form method="POST" action="{{ route('super-admin.generate-jadwal') }}" id="generateJadwalForm">
                    @csrf
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check me-2"></i>Ya, Generate Jadwal
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
#generateJadwalModal .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

#generateJadwalModal .btn-close {
    background-color: rgba(0, 0, 0, 0.05);
    border-radius: 50%;
    width: 32px;
    height: 32px;
    opacity: 0.5;
}

#generateJadwalModal .btn-close:hover {
    opacity: 1;
    background-color: rgba(175, 2, 2, 0.1);
}

#generateJadwalModal .btn-lg {
    padding: 12px 24px;
    font-weight: 600;
}
</style>

@endsection
