@extends('layouts.mahasiswa')

@section('title', 'Jadwal Bimbingan')
@section('page-title', 'Jadwal Bimbingan')

@section('content')
<!-- Main Navigation Tabs -->
<div class="segmented-control">
    <button class="segmented-btn" onclick="switchTab('book')">Book Bimbingan</button>
    <button class="segmented-btn active" onclick="switchTab('history')">Riwayat Bimbingan</button>
</div>

<!-- Book Bimbingan Tab Content -->
<div id="book-tab" class="tab-content" style="display: none;">
    <!-- Pilih Jadwal Bimbingan Section (No Background) -->
    <div class="mb-2">
        <h5 class="mb-1">Pilih Jadwal Bimbingan</h5>
        <p class="text-muted mb-2">Pilih waktu yang tersedia dari jadwal dosen pembimbing</p>
        
        <!-- Day Selection -->
        <div class="day-segmented-control">
            <button class="day-segmented-btn active" onclick="switchDay('senin')">Senin</button>
            <button class="day-segmented-btn" onclick="switchDay('selasa')">Selasa</button>
            <button class="day-segmented-btn" onclick="switchDay('rabu')">Rabu</button>
            <button class="day-segmented-btn" onclick="switchDay('kamis')">Kamis</button>
            <button class="day-segmented-btn" onclick="switchDay('jumat')">Jumaat</button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <!-- Schedule Information -->
            <div class="schedule-info">
                <div class="schedule-title">Jadwal Konsultasi - <span id="selected-day">Senin</span></div>
                <div class="schedule-detail">Dosen pembimbing: Dr. Dwi Rantini S.Si</div>
                <div class="schedule-detail">
                    <span class="time-box">Waktu Tersedia 13.00 - 16.00</span>
                </div>
            </div>

            <!-- Time Slots -->
            <div class="mb-4">
                <h6 class="mb-3">Pilih Slot Waktu (30 menit per sesi)</h6>
                <p class="text-muted mb-3">Klik slot yang tersedia untuk melakukan booking</p>
                
                <div class="slots-grid">
                    <div class="slot-card slot-available" onclick="selectSlot(this, '13:00')">
                        <div class="slot-time">13:00</div>
                        <div class="slot-status">Tersedia</div>
                    </div>
                    <div class="slot-card slot-available" onclick="selectSlot(this, '13:30')">
                        <div class="slot-time">13:30</div>
                        <div class="slot-status">Tersedia</div>
                    </div>
                    <div class="slot-card slot-booked">
                        <div class="slot-time">14:00</div>
                        <div class="slot-status">Terbooking</div>
                    </div>
                    <div class="slot-card slot-available" onclick="selectSlot(this, '14:30')">
                        <div class="slot-time">14:30</div>
                        <div class="slot-status">Tersedia</div>
                    </div>
                    <div class="slot-card slot-booked">
                        <div class="slot-time">15:00</div>
                        <div class="slot-status">Terbooking</div>
                    </div>
                    <div class="slot-card slot-available" onclick="selectSlot(this, '15:30')">
                        <div class="slot-time">15:30</div>
                        <div class="slot-status">Tersedia</div>
                    </div>
                </div>
            </div>

            <!-- Booking Details Form -->
            <div class="mb-4">
                <h6 class="mb-3">Detail Bimbingan</h6>
                
                <div class="form-group">
                    <label class="form-label">Topik Konsultasi</label>
                    <select class="form-control" id="topic">
                        <option value="">Pilih topik konsultasi</option>
                        <option value="proposal">Pembahasan Proposal</option>
                        <option value="skripsi">Pembahasan Skripsi</option>
                        <option value="revisi">Revisi Dokumen</option>
                        <option value="konsultasi">Konsultasi Umum</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea class="form-control" id="notes" rows="4" placeholder="Jelaskan hal yang ingin didiskusikan"></textarea>
                </div>
            </div>

            <!-- Book Button -->
            <div class="d-grid">
                <button class="btn btn-primary btn-lg" id="book-button" onclick="bookBimbingan()">
                    <i class="fas fa-calendar-plus" id="book-icon"></i>
                    <span id="book-text">Book Bimbingan</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Bimbingan Terjadwal Section -->
    <div class="card" id="scheduled-guidance-card" style="display: none;">
        <div class="card-body">
            <h5 class="mb-3">Bimbingan Terjadwal</h5>
            
            <div class="scheduled-guidance">
                <div class="guidance-info">
                    <div class="guidance-topic">
                        <strong>Topik Konsultasi:</strong> <span id="guidance-topic-text">Bab 2 - Landasan Teori</span>
                    </div>
                    <div class="guidance-time">
                        <strong>Waktu:</strong> <span id="guidance-time-text">Selasa, 02-03-2025 • 10.00 - 10.30</span>
                    </div>
                </div>
                
                <div class="guidance-action">
                    <button class="btn btn-danger btn-lg" onclick="showCancelModal()">Batalkan Booking</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- History Tab Content -->
<div id="history-tab" class="tab-content" style="display: none;">
    <div class="mb-4">
        <h5 class="mb-2">Riwayat Bimbingan</h5>
        <p class="text-muted mb-4">Lihat semua konsultasi yang sudah dan akan dilakukan</p>
    </div>
    
    <div class="card">
        <div class="card-body">
            <!-- History Entries -->
            <div class="history-entry">
                <div class="entry-icon">
                    <div class="status-dot status-completed"></div>
                </div>
                <div class="entry-content">
                    <div class="entry-title">Bimbingan Bab 1</div>
                    <div class="entry-details">
                        <div class="entry-date">
                            <i class="fas fa-calendar"></i>
                            <span>02-04-2025</span>
                        </div>
                        <div class="entry-time">
                            <div class="time-dot"></div>
                            <span>10.00</span>
                        </div>
                    </div>
                </div>
                <div class="entry-action">
                    <button class="btn btn-primary btn-sm">Selesai</button>
                </div>
            </div>

            <div class="history-entry">
                <div class="entry-icon">
                    <div class="status-dot status-scheduled"></div>
                </div>
                <div class="entry-content">
                    <div class="entry-title">Bimbingan Bab 2</div>
                    <div class="entry-details">
                        <div class="entry-date">
                            <i class="fas fa-calendar"></i>
                            <span>02-05-2025</span>
                        </div>
                        <div class="entry-time">
                            <div class="time-dot"></div>
                            <span>13.00</span>
                        </div>
                    </div>
                </div>
                <div class="entry-action">
                    <button class="btn btn-primary btn-sm">Terjadwal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedSlot = null;
let selectedDay = 'senin';

function switchTab(tab) {
    console.log('Switching to tab:', tab); // Debug log
    
    // Hide all tab contents
    document.getElementById('book-tab').style.display = 'none';
    document.getElementById('history-tab').style.display = 'none';
    
    // Remove active class from all buttons
    document.querySelectorAll('.segmented-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab and activate button
    if (tab === 'book') {
        document.getElementById('book-tab').style.display = 'block';
        document.querySelector('.segmented-btn:first-child').classList.add('active');
    } else {
        document.getElementById('history-tab').style.display = 'block';
        document.querySelector('.segmented-btn:last-child').classList.add('active');
    }
}

// Initialize page with history tab active
document.addEventListener('DOMContentLoaded', function() {
    switchTab('history');
});

function switchDay(day) {
    selectedDay = day;
    
    // Remove active class from all day buttons
    document.querySelectorAll('.day-segmented-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to clicked button
    event.target.classList.add('active');
    
    // Update selected day text
    const dayNames = {
        'senin': 'Senin',
        'selasa': 'Selasa', 
        'rabu': 'Rabu',
        'kamis': 'Kamis',
        'jumat': 'Jumaat'
    };
    document.getElementById('selected-day').textContent = dayNames[day];
    
    // Reset selected slot
    selectedSlot = null;
    document.querySelectorAll('.slot-card').forEach(card => {
        card.classList.remove('selected');
    });
}

function selectSlot(element, time) {
    // Only allow selection of available slots
    if (element.classList.contains('slot-booked')) {
        return;
    }
    
    // Remove selected class from all slots
    document.querySelectorAll('.slot-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked slot
    element.classList.add('selected');
    selectedSlot = time;
}

function bookBimbingan() {
    if (!selectedSlot) {
        alert('Pilih slot waktu terlebih dahulu!');
        return;
    }
    
    const topic = document.getElementById('topic').value;
    if (!topic) {
        alert('Pilih topik konsultasi terlebih dahulu!');
        return;
    }
    
    // Show the scheduled guidance section
    const scheduledCard = document.getElementById('scheduled-guidance-card');
    scheduledCard.style.display = 'block';
    
    // Update the guidance info with selected data
    const topicText = document.getElementById('topic').selectedOptions[0].text;
    const guidanceTopicText = document.getElementById('guidance-topic-text');
    const guidanceTimeText = document.getElementById('guidance-time-text');
    
    if (guidanceTopicText) {
        guidanceTopicText.textContent = topicText;
    }
    
    if (guidanceTimeText) {
        const dayNames = {
            'senin': 'Senin',
            'selasa': 'Selasa', 
            'rabu': 'Rabu',
            'kamis': 'Kamis',
            'jumat': 'Jumat'
        };
        guidanceTimeText.textContent = `${dayNames[selectedDay]}, ${selectedSlot}`;
    }
    
    // Disable the book button
    const bookButton = document.getElementById('book-button');
    const bookIcon = document.getElementById('book-icon');
    const bookText = document.getElementById('book-text');
    
    bookButton.disabled = true;
    bookButton.classList.remove('btn-primary');
    bookButton.classList.add('btn-secondary');
    bookIcon.className = 'fas fa-lock';
    bookText.textContent = 'Sudah Terbooking';
    
    // Change selected slot to booked (red)
    const selectedSlotCard = document.querySelector('.slot-card.selected');
    if (selectedSlotCard) {
        selectedSlotCard.classList.remove('slot-available', 'selected');
        selectedSlotCard.classList.add('slot-booked');
        
        // Update the status text
        const statusElement = selectedSlotCard.querySelector('.slot-status');
        if (statusElement) {
            statusElement.textContent = 'Terbooking';
        }
    }
    
    // Scroll to the scheduled section
    scheduledCard.scrollIntoView({ behavior: 'smooth' });
    
    // Reset form
    selectedSlot = null;
    document.getElementById('topic').value = '';
    document.getElementById('notes').value = '';
    document.querySelectorAll('.slot-card').forEach(card => {
        card.classList.remove('selected');
    });
}

function cancelBooking() {
    if (confirm('Apakah Anda yakin ingin membatalkan booking ini?')) {
        const scheduledCard = document.getElementById('scheduled-guidance-card');
        scheduledCard.style.display = 'none';
        
        // Re-enable the book button
        const bookButton = document.getElementById('book-button');
        const bookIcon = document.getElementById('book-icon');
        const bookText = document.getElementById('book-text');
        
        bookButton.disabled = false;
        bookButton.classList.remove('btn-secondary');
        bookButton.classList.add('btn-primary');
        bookIcon.className = 'fas fa-calendar-plus';
        bookText.textContent = 'Book Bimbingan';
        
        alert('Booking berhasil dibatalkan!');
    }
}

function showCancelModal() {
    document.getElementById('cancelBookingModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeCancelModal() {
    document.getElementById('cancelBookingModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function confirmCancelBooking() {
    const scheduledCard = document.getElementById('scheduled-guidance-card');
    scheduledCard.style.display = 'none';
    
    // Re-enable the book button
    const bookButton = document.getElementById('book-button');
    const bookIcon = document.getElementById('book-icon');
    const bookText = document.getElementById('book-text');
    
    bookButton.disabled = false;
    bookButton.classList.remove('btn-secondary');
    bookButton.classList.add('btn-primary');
    bookIcon.className = 'fas fa-calendar-plus';
    bookText.textContent = 'Book Bimbingan';
    
    // Change booked slot back to available (green)
    const bookedSlotCard = document.querySelector('.slot-card.slot-booked');
    if (bookedSlotCard) {
        bookedSlotCard.classList.remove('slot-booked');
        bookedSlotCard.classList.add('slot-available');
        
        // Update the status text
        const statusElement = bookedSlotCard.querySelector('.slot-status');
        if (statusElement) {
            statusElement.textContent = 'Tersedia';
        }
    }
    
    // Close modal
    closeCancelModal();
    
    alert('Booking berhasil dibatalkan!');
}
</script>

<style>
.tab-content {
    display: block;
    width: 100%;
    min-height: 200px;
}

#book-tab {
    display: none;
}

#history-tab {
    display: block;
}

.slot-card.selected {
    background: #dbeafe !important;
    border-color: #3b82f6 !important;
    color: #1e40af !important;
    transform: scale(1.05);
}

.slot-card.selected .slot-time {
    font-weight: 700;
}

.slot-card.selected .slot-status {
    font-weight: 600;
}

/* Ensure segmented control is visible */
.segmented-control {
    display: flex;
            justify-content: center;
            align-items: center;
            background: #f3f4f6;
            border-radius: 25px;
            padding: 4px;
            margin-bottom: 20px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            width: 100%;
}

.segmented-btn {
    display: block !important;
    visibility: visible !important;
    flex: 1 !important;
    text-align: center !important;
    padding: 8px 12px !important;
    border: none !important;
    background: transparent !important;
    color: #6b7280 !important;
    border-radius: 20px !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    position: relative;
    z-index: 11;
}

.segmented-btn.active {
    background: #ffffff !important;
    color: #1f2937 !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    font-weight: 600 !important;
}

.segmented-btn:hover:not(.active) {
    color: #374151 !important;
    background: rgba(255, 255, 255, 0.5) !important;
}
</style>
@endsection
