<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Kampus Merdeka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .admin-container {
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

        .nav-menu {
            padding: 0 20px;
            margin-top: 20px;
        }

        .menu-title {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
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

        .sidebar .nav-link {
    white-space: nowrap;       /* jangan pindah baris */
    overflow: hidden;          /* kalau kepanjangan, sembunyikan */
    text-overflow: ellipsis;   /* kasih "..." kalau teks terpotong */
    display: flex;
    align-items: center;
}

/* Supaya icon tidak dorong teks */
.sidebar .nav-link i {
    flex-shrink: 0;
    margin-right: 10px;
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

        .submenu {
            margin-left: 20px;
            margin-top: 5px;
        }

        .submenu .nav-link {
            padding: 8px 15px;
            font-size: 13px;
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

        /* Card Styling for Registration Pages */
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

        .card-body {
            padding: 30px;
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
            margin: 0;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        /* User Segmented Control */
        .user-segmented-control {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f3f4f6;
            border-radius: 25px;
            padding: 4px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 100%;
        }

        .user-segmented-btn {
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

        .user-segmented-btn.active {
            background: #ffffff;
            color: #1f2937;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            font-weight: 600;
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

        /* Button styling to match dosen layout */
        .btn {
            height: 38px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-sm {
            height: 32px;
            padding: 6px 12px;
            font-size: 12px;
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

        /* Submenu styling */
        .submenu {
            padding-left: 20px;
            margin-top: 5px;
        }

        .submenu .nav-link {
            padding: 8px 15px;
            font-size: 13px;
        }

        /* Chevron animation */
        #pendaftaran-chevron {
            transition: transform 0.3s ease;
        }

        /* Ensure collapse show class works */
        .collapse.show {
            display: block !important;
        }

        .user-segmented-btn:hover:not(.active) {
            color: #374151;
            background: rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="admin-container">
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
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="{{ route('admin.ruangan') }}" class="nav-link {{ request()->routeIs('admin.ruangan') ? 'active' : '' }}">
                        <i class="fas fa-door-open"></i>
                        Manajemen Ruangan
                    </a>
                </div>
                
                <div class="nav-item">
                    <a href="{{ route('admin.jadwal') }}" class="nav-link {{ request()->routeIs('admin.jadwal') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        Jadwal Plotting
                    </a>
                </div>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
                <button class="logout-btn" onclick="logout()">
                    <img src="{{ asset('images/exit-icon.svg') }}" alt="Logout" style="width: 20px; height: 20px; color: #dc3545; filter: brightness(0) saturate(100%) invert(27%) sepia(51%) saturate(2878%) hue-rotate(346deg) brightness(104%) contrast(97%);">
                </button>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
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
        
        function togglePendaftaranMenu() {
            const userMenu = document.getElementById('userMenu');
            const chevron = document.getElementById('pendaftaran-chevron');
            
            if (userMenu.classList.contains('show')) {
                userMenu.classList.remove('show');
                chevron.style.transform = 'rotate(0deg)';
            } else {
                userMenu.classList.add('show');
                chevron.style.transform = 'rotate(180deg)';
            }
        }
        
        // Keep pendaftaran menu open if on pendaftaran pages
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            if (currentPath.includes('pendaftaran-mahasiswa') || currentPath.includes('pendaftaran-dosen')) {
                const userMenu = document.getElementById('userMenu');
                const chevron = document.getElementById('pendaftaran-chevron');
                userMenu.classList.add('show');
                chevron.style.transform = 'rotate(180deg)';
            }
        });
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
</body>
</html>
