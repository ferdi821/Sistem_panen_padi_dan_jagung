<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPANEN & Stok Benih</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            background: #ffffff;
        }
        .login-header {
            background-color: #154d19;
            color: #ffffff;
            padding: 25px 20px;
            text-align: center;
        }
        .btn-success-custom {
            background-color: #154d19;
            border-color: #154d19;
            color: #fff;
            padding: 10px;
            font-weight: 600;
        }
        .btn-success-custom:hover {
            background-color: #0d3810;
            border-color: #0d3810;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center">
    <div class="card login-card">
        <div class="login-header">
            <h4 class="mb-1 fw-bold"><i class="bi bi-tree-fill me-2"></i>SIPANEN</h4>
            <small class="text-white-50">Sistem Informasi Panen & Stok Benih</small>
        </div>
        <div class="card-body p-4">
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <small>{{ session('error') }}</small>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label text-secondary fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@pertanian.go.id" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label text-secondary fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-success-custom w-100 mb-3 rounded-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </button>
            </form>

            <div class="text-center mt-2">
                <small class="text-muted">
                    Belum memiliki akun? <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Daftar Akun Petugas</a>
                </small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.bundle.min.js"></script>
</body>
</html>