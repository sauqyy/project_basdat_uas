<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kampus Merdeka')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="{{ asset('css/custom-styles.css') }}" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .navbar-brand {
            font-weight: bold;
            color: #2c3e50 !important;
        }
        .page-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 0;
        }
        .card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            border-radius: 12px 12px 0 0 !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-3">
                        <h4 class="text-white mb-4">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Kampus Merdeka
                        </h4>
                        
                        @if(auth()->user()->isSuperAdmin())
                            <!-- Super Admin Menu -->
                            <nav class="nav flex-column">
                                <a class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}" href="{{ route('super-admin.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                                <a class="nav-link {{ request()->routeIs('super-admin.kelas*') ? 'active' : '' }}" href="{{ route('super-admin.kelas') }}">
                                    <i class="fas fa-home me-2"></i>Kelola Kelas
                                </a>
                                <a class="nav-link {{ request()->routeIs('super-admin.jadwal*') ? 'active' : '' }}" href="{{ route('super-admin.jadwal') }}">
                                    <i class="fas fa-calendar me-2"></i>Jadwal
                                </a>
                            </nav>
                        @elseif(auth()->user()->isAdminProdi())
                            <!-- Admin Prodi Menu -->
                            <nav class="nav flex-column">
                                <a class="nav-link {{ request()->routeIs('admin-prodi.dashboard') ? 'active' : '' }}" href="{{ route('admin-prodi.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                                <a class="nav-link {{ request()->routeIs('admin-prodi.mata-kuliah*') ? 'active' : '' }}" href="{{ route('admin-prodi.mata-kuliah') }}">
                                    <i class="fas fa-book me-2"></i>Mata Kuliah
                                </a>
                            </nav>
                        @elseif(auth()->user()->isDosen())
                            <!-- Dosen Menu -->
                            <nav class="nav flex-column">
                                <a class="nav-link {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}" href="{{ route('dosen.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                                <a class="nav-link {{ request()->routeIs('dosen.mata-kuliah*') ? 'active' : '' }}" href="{{ route('dosen.mata-kuliah') }}">
                                    <i class="fas fa-book me-2"></i>Mata Kuliah
                                </a>
                                <a class="nav-link {{ request()->routeIs('dosen.preferensi*') ? 'active' : '' }}" href="{{ route('dosen.preferensi') }}">
                                    <i class="fas fa-clock me-2"></i>Preferensi
                                </a>
                                <a class="nav-link {{ request()->routeIs('dosen.jadwal*') ? 'active' : '' }}" href="{{ route('dosen.jadwal') }}">
                                    <i class="fas fa-calendar me-2"></i>Jadwal
                                </a>
                            </nav>
                        @endif
                        
                        <hr class="text-white-50">
                        <div class="mt-3">
                            <div class="text-white-50 small">
                                <i class="fas fa-user me-2"></i>{{ auth()->user()->name }}
                            </div>
                            <div class="text-white-50 small">
                                <i class="fas fa-tag me-2"></i>{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                            </div>
                            @if(auth()->user()->prodi)
                                <div class="text-white-50 small">
                                    <i class="fas fa-building me-2"></i>{{ auth()->user()->prodi }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <!-- Top Navbar -->
                    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
                        <div class="container-fluid">
                            <span class="navbar-brand mb-0 h1">@yield('page-title', 'Dashboard')</span>
                            
                            <div class="navbar-nav ms-auto">
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle me-2"></i>{{ auth()->user()->name }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </nav>
                    
                    <!-- Page Content -->
                    <div class="container-fluid p-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>
