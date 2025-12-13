<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Sistem Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 12px;
            width: 380px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        .login-box h3 {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h3>Login</h3>
        <form method="POST" action="{{ url('/login') }}">
    @csrf
    <div class="mb-3">
        <label>Email / NIM / NIP</label>
        <input type="text" class="form-control" name="email" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" class="form-control" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Login</button>
</form>

    </div>
</body>
</html>