<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Mahasiswa') - Kampus Merdeka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .mahasiswa-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 280px;
            background: #ffffff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 30px 20px;
            border-bottom: 1px solid #e9ecef;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 120px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin: 0;
        }
        
        .logo img {
            width: 60px;
            height: auto;
        }
        
        .logo-text {
            font-family: 'Poppins', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: #0d6efd;
            letter-spacing: 2px;
            margin: 0;
        }
        
        .menu-title {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        
        .nav-menu {
            padding: 0 20px;
            margin-top: 20px;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #6b7280;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-link:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .nav-link.active {
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: 600;
        }
        
        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        
        .user-info {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #e5e7eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }
        
        .user-name {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        
        .logout-btn {
            width: 32px;
            height: 32px;
            background: #f3f4f6;
            border: none;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #e5e7eb;
        }
        
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }
        
        .header {
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }
        
        .content-area {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .bg-primary-light {
            background: #dbeafe;
        }

        .bg-success-light {
            background: #dcfce7;
        }

        .bg-warning-light {
            background: #fef3c7;
        }

        .bg-info-light {
            background: #dbeafe;
        }

        .text-primary {
            color: #1d4ed8;
        }

        .text-success {
            color: #16a34a;
        }

        .text-warning {
            color: #d97706;
        }

        .text-info {
            color: #0891b2;
        }

        .stat-label {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 4px 0;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        /* Scheduled Guidance Section */
        .scheduled-guidance {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .guidance-info {
            flex: 1;
        }

        .guidance-topic {
            font-size: 16px;
            color: #374151;
            margin-bottom: 8px;
        }

        .guidance-time {
            font-size: 14px;
            color: #6b7280;
        }

        .guidance-action {
            margin-left: 20px;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 16px;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
        }

        /* Disabled button styling */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover:not(:disabled) {
            background-color: #5a6268;
            border-color: #545b62;
        }
        
        .card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 100%;
        }

        .card:last-child {
            margin-bottom: 0;
        }
        
        .card-header {
            background: none;
            border-bottom: 1px solid #e5e7eb;
            padding: 20px 25px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Segmented Control */
        .segmented-control {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f3f4f6;
            border-radius: 25px;
            padding: 4px;
            margin: 0 auto 5px auto;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            width: 100%;
        }

.segmented-btn {
    flex: 1;
    text-align: center;
    padding: 8px 12px;
    border: none;
    background: transparent;
    color: #6b7280;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.segmented-btn.active {
    background: #ffffff;
    color: #1f2937;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-weight: 600;
}

        
        .segmented-btn:hover:not(.active) {
            color: #374151;
            background: rgba(255, 255, 255, 0.5);
        }
        
        .segmented-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        /* Day Segmented Control */
        .day-segmented-control {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f3f4f6;
            border-radius: 25px;
            padding: 4px;
            margin-bottom: 0;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            width: 100%;
        }

        .day-segmented-btn {
            flex: 1;
            text-align: center;
            padding: 8px 12px;
            border: none;
            background: transparent;
            color: #6b7280;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .day-segmented-btn.active {
            background: #ffffff;
            color: #1f2937;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-weight: 600;
        }

        .day-segmented-btn:hover:not(.active) {
            color: #374151;
            background: rgba(255, 255, 255, 0.5);
        }

        .day-segmented-btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Logout Modal Styles */
        .logout-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        .logout-modal-content {
            background: white;
            border-radius: 16px;
            padding: 0;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            animation: slideIn 0.3s ease;
        }

        .logout-modal-header {
            padding: 24px 24px 16px 24px;
            text-align: center;
            border-bottom: 1px solid #f3f4f6;
        }

        .logout-icon {
            margin-bottom: 12px;
        }

        .logout-modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .logout-modal-body {
            padding: 16px 24px;
            text-align: center;
        }

        .logout-modal-body p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
        }

        .logout-modal-footer {
            padding: 16px 24px 24px 24px;
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .btn-cancel, .btn-logout {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            min-width: 80px;
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #6b7280;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-logout {
            background: #dc3545;
            color: white;
        }

        .btn-logout:hover {
            background: #c82333;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { 
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to { 
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .schedule-info {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .schedule-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .schedule-detail {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        
        .time-box {
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px 16px;
            display: inline-block;
            font-weight: 500;
            color: #1f2937;
        }
        
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        
        .slot-card {
            padding: 8px 6px;
            border-radius: 6px;
            text-align: center;
            font-weight: 500;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid;
            width: 100%;
            aspect-ratio: 1.3;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .slot-available {
            background: #f0fdf4;
            color: #166534;
            border-color: #C9F4C7;
        }
        
        .slot-booked {
            background: #fef2f2;
            color: #dc2626;
            border-color: #FFD0D0;
        }
        
        .slot-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .slot-time {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .slot-status {
            font-size: 12px;
            font-weight: 500;
            opacity: 0.9;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-primary {
            background-color: #1976d2;
            border-color: #1976d2;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
            font-size: 16px;
        }
        
        .btn-primary:hover {
            background-color: #1565c0;
            border-color: #1565c0;
        }
        
        .btn-primary:disabled {
            background-color: #9ca3af;
            border-color: #9ca3af;
            cursor: not-allowed;
        }
        
        /* History Entry Styles */
        .history-entry {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            margin-bottom: 15px;
            background: #fff;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .history-entry:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .entry-icon {
            margin-right: 15px;
        }
        
        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-dot.status-completed {
            background-color: #22c55e;
        }
        
        .status-dot.status-scheduled {
            background-color: #3b82f6;
        }
        
        .entry-content {
            flex: 1;
        }
        
        .entry-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 8px 0;
        }
        
        .entry-details {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .entry-date {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .entry-time {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: #6b7280;
        }
        
        .entry-date i {
            font-size: 12px;
            color: #9ca3af;
        }
        
        .time-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #9ca3af;
            display: inline-block;
        }
        
        .entry-action {
            margin-left: 15px;
        }
        
        .btn-sm {
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 6px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="mahasiswa-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <img src="{{ asset('images/logo1.png') }}" alt="Logo">
                    <h1 class="logo-text">SCHEDIFY</h1>
                </div>
            </div>
            
            <div class="nav-menu">
                <div class="menu-title">Menu</div>
                
                <div class="nav-item">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        Progress
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="{{ route('mahasiswa.jadwal') }}" class="nav-link {{ request()->routeIs('mahasiswa.jadwal') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        Jadwal Bimbingan
                    </a>
                </div>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-name">{{ Auth::user()->name ?? 'Mahasiswa' }}</div>
                <button class="logout-btn" onclick="logout()">
                    <img src="{{ asset('images/exit-icon.svg') }}" alt="Logout" style="width: 20px; height: 20px; color: #dc3545; filter: brightness(0) saturate(100%) invert(27%) sepia(51%) saturate(2878%) hue-rotate(346deg) brightness(104%) contrast(97%);">
                </button>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1 class="page-title">@yield('page-title', 'Dashboard Mahasiswa')</h1>
            </div>
            
            <div class="content-area">
                @yield('content')
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function logout() {
            document.getElementById('logoutModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function closeLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        function confirmLogout() {
            document.getElementById('logout-form').submit();
        }
    </script>
    
    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="logout-modal" style="display: none;">
        <div class="logout-modal-content">
            <div class="logout-modal-header">
                <div class="logout-icon">
                    <img src="{{ asset('images/exit-icon.svg') }}" alt="Logout" style="width: 24px; height: 24px; filter: brightness(0) saturate(100%) invert(27%) sepia(51%) saturate(2878%) hue-rotate(346deg) brightness(104%) contrast(97%);">
                </div>
                <h3>Konfirmasi Logout</h3>
            </div>
            <div class="logout-modal-body">
                <p>Apakah Anda yakin ingin keluar dari sistem?</p>
            </div>
            <div class="logout-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeLogoutModal()">Batal</button>
                <button type="button" class="btn-logout" onclick="confirmLogout()">Logout</button>
            </div>
        </div>
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <!-- Cancel Booking Confirmation Modal -->
    <div id="cancelBookingModal" class="logout-modal" style="display: none;">
        <div class="logout-modal-content">
            <div class="logout-modal-header">
                <div class="logout-icon">
                    <i class="fas fa-exclamation-triangle" style="width: 24px; height: 24px; color: #dc3545;"></i>
                </div>
                <h3>Konfirmasi Pembatalan</h3>
            </div>
            <div class="logout-modal-body">
                <p>Apakah Anda yakin ingin membatalkan booking bimbingan ini?</p>
            </div>
            <div class="logout-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeCancelModal()">Batal</button>
                <button type="button" class="btn-logout" onclick="confirmCancelBooking()">Batalkan Booking</button>
            </div>
        </div>
    </div>
</body>
</html>
