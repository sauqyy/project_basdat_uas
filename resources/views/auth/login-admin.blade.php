<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Kampus Merdeka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #dc3545 120%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        
    .slide-in-blurred-left {
        -webkit-animation: slide-in-blurred-left 0.6s cubic-bezier(0.230, 1.000, 0.320, 1.000) both;
                animation: slide-in-blurred-left 0.6s cubic-bezier(0.230, 1.000, 0.320, 1.000) both;
    }

    @-webkit-keyframes slide-in-blurred-left {
        0% {
            -webkit-transform: translateX(-1000px) scaleX(2.5) scaleY(0.2);
                    transform: translateX(-1000px) scaleX(2.5) scaleY(0.2);
            -webkit-transform-origin: 100% 50%;
                    transform-origin: 100% 50%;
            -webkit-filter: blur(40px);
                    filter: blur(40px);
            opacity: 0;
        }
        100% {
            -webkit-transform: translateX(0) scaleY(1) scaleX(1);
                    transform: translateX(0) scaleY(1) scaleX(1);
            -webkit-transform-origin: 50% 50%;
                    transform-origin: 50% 50%;
            -webkit-filter: blur(0);
                    filter: blur(0);
            opacity: 1;
        }
    }

    @keyframes slide-in-blurred-left {
        0% {
            -webkit-transform: translateX(-1000px) scaleX(2.5) scaleY(0.2);
                    transform: translateX(-1000px) scaleX(2.5) scaleY(0.2);
            -webkit-transform-origin: 100% 50%;
                    transform-origin: 100% 50%;
            -webkit-filter: blur(40px);
                    filter: blur(40px);
            opacity: 0;
        }
        100% {
            -webkit-transform: translateX(0) scaleY(1) scaleX(1);
                    transform: translateX(0) scaleY(1) scaleX(1);
            -webkit-transform-origin: 50% 50%;
                    transform-origin: 50% 50%;
            -webkit-filter: blur(0);
                    filter: blur(0);
            opacity: 1;
        }
    }



        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 1200px;
            gap: 60px;
        }
        
        .logo-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .logo-container {
            width: 300px;
            height: 300px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .logo-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .login-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-card {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            background: #2c3e50;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .user-avatar i {
            color: white;
            font-size: 30px;
        }
        
        .login-title {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        .admin-badge {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .admin-badge span {
            background: #dc3545;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group i {
            position: absolute;
            left: 15px;
            top: 65%;
            transform: translateY(-50%);
            color: #6b7280;
            z-index: 2;
        }
        
        .form-control {
            padding: 12px 15px 12px 45px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            font-size: 14px;
            background: white;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .form-label {
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 5px;
            display: block;
        }
        
        .forgot-password {
            text-align: right;
            margin-bottom: 25px;
        }
        
        .forgot-password a {
            color: #6b7280;
            text-decoration: none;
            font-size: 12px;
            transition: color 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: #3b82f6;
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .login-btn:hover {
            background: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .login-btn:active {
            transform: translateY(0);
        }
        
        .login-btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .login-btn:disabled:hover {
            background: #9ca3af;
            transform: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .loading-spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid #f5c6cb;
            display: none;
        }
        
        .error-message.show {
            display: block;
        }
        
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                gap: 30px;
            }
            
            .logo-container {
                width: 200px;
                height: 200px;
            }
            
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-wrapper">
            <!-- Logo Section -->
           <div class="logo-section">
    <div class="logo-container">
    <img src="{{ asset('images/logo.png') }}" 
     alt="Kampus Merdeka" 
     class="slide-in-blurred-left">

    </div>
</div>
            
            <!-- Login Section -->
            <div class="login-section">
                <div class="login-card">
                    <!-- User Avatar -->
                    <div class="user-avatar">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    
                    <!-- Login Title -->
                    <h2 class="login-title">Login Admin</h2>
                    
                    <!-- Admin Badge -->
                    <div class="admin-badge">
                        <span>Super Admin & Admin Prodi</span>
                    </div>
                    
                    <!-- Login Form -->
                    <form method="POST" action="{{ url('/admin/login') }}">
                        @csrf
                        
                        <!-- Username Field -->
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <i class="fas fa-user"></i>
                            <input type="text" class="form-control" name="email" placeholder="Username" required>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <i class="fas fa-lock"></i>
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                        
                        <!-- Forgot Password -->
                        <div class="forgot-password">
                            <a href="#">Forgot password?</a>
                        </div>
                        
                        <!-- Error Message -->
                        <div class="error-message" id="errorMessage" @if($errors->any() || session('error')) style="display: block;" @endif>
                            <i class="fas fa-exclamation-triangle"></i>
                            <span id="errorText">
                                @if($errors->any())
                                    {{ $errors->first('email') }}
                                @elseif(session('error'))
                                    {{ session('error') }}
                                @else
                                    Password/Username salah
                                @endif
                            </span>
                        </div>
                        
                        <!-- Login Button -->
                        <button type="submit" class="login-btn" id="loginBtn">
                            <span class="loading-spinner" id="loadingSpinner" style="display: none;"></span>
                            <span id="loginText">Login</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const errorMessage = document.getElementById('errorMessage');
            const errorText = document.getElementById('errorText');
            const loginForm = document.querySelector('form');
            const loginBtn = document.getElementById('loginBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const loginText = document.getElementById('loginText');
            
            // Form submit handler
            loginForm.addEventListener('submit', function(e) {
                // Get form values
                const username = document.querySelector('input[name="email"]').value;
                const password = document.querySelector('input[name="password"]').value;
                
                // Simple validation - show error only if fields are empty
                if (!username || !password) {
                    e.preventDefault();
                    errorText.textContent = 'Username dan Password harus diisi';
                    errorMessage.classList.add('show');
                    
                    // Hide error message after 5 seconds
                    setTimeout(function() {
                        errorMessage.classList.remove('show');
                    }, 5000);
                } else {
                    // Show loading state
                    loginBtn.disabled = true;
                    loadingSpinner.style.display = 'inline-block';
                    loginText.textContent = 'Logging in...';
                    
                    // If form is valid, let it submit normally
                    // The server will handle authentication and redirect accordingly
                    // If authentication fails, the server will redirect back with error
                }
            });
            
            // Check if there are server-side errors on page load
            @if($errors->any() || session('error'))
                errorMessage.classList.add('show');
                // Hide error message after 5 seconds
                setTimeout(function() {
                    errorMessage.classList.remove('show');
                }, 5000);
            @endif
        });
    </script>
</body>
</html>
