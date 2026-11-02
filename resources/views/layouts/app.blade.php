<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'SIPANEN & STOK BENIH') }}</title>
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        /* Sidebar Navigation Left */
        #sidebar {
            min-width: 285px;
            max-width: 285px;
            background-color: #154d19;
            color: #fff;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        #sidebar .sidebar-header {
            padding: 24px 22px;
            background-color: #0d3810;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }
        #sidebar .sidebar-header h5 {
            font-size: 1.35rem;
            letter-spacing: 0.5px;
        }
        #sidebar .sidebar-header small {
            font-size: 0.85rem;
        }
        #sidebar ul.components {
            padding: 15px 0;
        }
        #sidebar ul li a {
            padding: 14px 22px;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.88);
            text-decoration: none;
            transition: 0.2s ease-in-out;
            border-left: 5px solid transparent;
        }
        #sidebar ul li a i {
            font-size: 1.25rem;
            margin-right: 12px !important;
        }
        #sidebar ul li a:hover, 
        #sidebar ul li a.active {
            color: #ffffff;
            background-color: #236c28;
            font-weight: 600;
            border-left: 5px solid #81c784;
        }
        #sidebar .nav-section {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #a5d6a7;
            padding: 20px 22px 8px 22px;
            font-weight: 700;
        }
        /* Main Content Area */
        #content {
            width: 100%;
            padding: 25px 30px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .top-navbar {
            background-color: #fff;
            border-bottom: 1px solid #e0e0e0;
            padding: 12px 30px;
            margin: -25px -30px 25px -30px;
        }
        .user-badge {
            background-color: #e8f5e9;
            color: #1b5e20;
            font-weight: 600;
            border-radius: 20px;
            padding: 5px 15px;
        }
        footer {
            margin-top: auto;
            padding-top: 20px;
            color: #777;
            font-size: 0.85rem;
        }
        /* Wrapper khusus Auth/Guest agar kartu login centered & presisi */
        .auth-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            width: 100%;
            padding: 30px 0;
        }
    </style>
</head>
<body>

<div class="wrapper">
    @auth
    <!-- SIDEBAR KIRI (Hanya muncul ketika sudah Login) -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h5 class="mb-0 fw-bold"><i class="bi bi-tree-fill me-2"></i>SIPANEN</h5>
            <small class="text-white-50">& Stok Benih Nasional</small>
        </div>

        <ul class="list-unstyled components mb-auto">
            <div class="nav-section">Utama</div>
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('report.rekap') }}" class="{{ request()->routeIs('report.rekap') ? 'active' : '' }}">
                    <i class="bi bi-table me-2"></i> Rekap BBPSIP
                </a>
            </li>

            <div class="nav-section">Data Transaksi</div>
            <li>
                <a href="{{ route('panen.index') }}" class="{{ request()->routeIs('panen.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text me-2"></i> Data Panen
                </a>
            </li>
            <li>
                <a href="{{ route('pengujian.index') }}" class="{{ request()->routeIs('pengujian.*') ? 'active' : '' }}">
                    <i class="bi bi-patch-check me-2"></i> Uji Mutu Benih
                </a>
            </li>
            <li>
                <a href="{{ route('stok.index') }}" class="{{ request()->routeIs('stok.index') ? 'active' : '' }}">
                    <i class="bi bi-boxes me-2"></i> Stok Benih Wilayah
                </a>
            </li>
            
            @hasanyrole('admin_pusat|petugas_provinsi')
            <li>
                <a href="{{ route('stok.keluar.create') }}" class="{{ request()->routeIs('stok.keluar.*') ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-up-right me-2"></i> Stok Keluar / Distribusi
                </a>
            </li>
            @endhasanyrole

            <li>
                <a href="{{ route('stok.riwayat') }}" class="{{ request()->routeIs('stok.riwayat') ? 'active' : '' }}">
                    <i class="bi bi-clock-history me-2"></i> Riwayat Mutasi
                </a>
            </li>

            @role('admin_pusat')
            <div class="nav-section">Pengaturan Sistem</div>
            <li>
                <a href="{{ route('benih.index') }}" class="{{ request()->routeIs('benih.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam me-2"></i> Master Benih
                </a>
            </li>
            <li>
                <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Manajemen User
                </a>
            </li>
            @endrole
        </ul>
    </nav>
    @endauth

    <!-- KONTEN UTAMA -->
    <div id="content">
        @auth
        <!-- Topbar Header (Hanya muncul ketika sudah Login) -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div class="fw-semibold text-secondary">
                <i class="bi bi-geo-alt me-1"></i> Sistem Informasi Pertanian & Stok Benih
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="user-badge">
                    <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }} 
                    <span class="badge bg-success ms-1">{{ strtoupper(auth()->user()->getRoleNames()->first() ?? 'USER') }}</span>
                </span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        @endauth

        <!-- Flash Alert Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show w-100" role="alert">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show w-100" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Dynamic Main Content -->
        @auth
            @yield('content')
        @else
            <div class="auth-container">
                <div class="w-100" style="max-width: 420px;">
                    @yield('content')
                </div>
            </div>
        @endauth

        <!-- Footer -->
        <footer class="text-center w-100">
            &copy; 2026 Kementerian Pertanian Republik Indonesia. All rights reserved.
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/bootstrap.bundle.min.js"></script>
</body>
</html>