@php
    $currentUser = auth()->user();
    $userName = $currentUser->name ?? 'Administrator';
    $userInitial = strtoupper(substr($userName, 0, 1));
    
    // Deteksi Role User
    $userRole = 'GUEST';
    $userRoleShort = 'GUEST';
    if ($currentUser) {
        if (method_exists($currentUser, 'getRoleNames') && $currentUser->getRoleNames()->isNotEmpty()) {
            $userRole = strtoupper($currentUser->getRoleNames()->first());
        } elseif (isset($currentUser->role) && !empty($currentUser->role)) {
            $userRole = strtoupper($currentUser->role);
        }
    }

    if ($userRole === 'ADMIN_PUSAT') {
        $userRoleShort = 'ADMIN';
    } elseif ($userRole === 'PETUGAS_PROVINSI') {
        $userRoleShort = 'PP';
    }

    // Deteksi Wilayah Provinsi
    $namaProvinsi = null;
    $namaProvinsiShort = null;
    if ($currentUser) {
        if (isset($currentUser->provinsi->nama_provinsi)) {
            $namaProvinsi = $currentUser->provinsi->nama_provinsi;
        } elseif (isset($currentUser->nama_provinsi)) {
            $namaProvinsi = $currentUser->nama_provinsi;
        }
    }

    if ($namaProvinsi) {
        $words = explode(' ', trim($namaProvinsi));
        if (count($words) >= 2) {
            $namaProvinsiShort = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            $namaProvinsiShort = strtoupper(substr($namaProvinsi, 0, 4));
        }
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPANEN - Sistem Informasi Pangan & Stok Benih')</title>

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-green: #106742;
            --primary-green-hover: #0c5234;
            --bg-light: #f4f7f4;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 72px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: #2c3e50;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #106742;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            display: flex;
            flex-direction: column;
        }

        #sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            height: 70px;
            flex-shrink: 0;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
            color: #ffffff;
            overflow: hidden;
        }

        .sidebar-brand-img {
            height: 40px;
            width: auto;
            max-width: 45px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            white-space: nowrap;
        }

        #sidebar.collapsed .sidebar-brand-text,
        #sidebar.collapsed .sidebar-category,
        #sidebar.collapsed .nav-text,
        #sidebar.collapsed .user-info-text,
        #sidebar.collapsed .scope-badge {
            display: none !important;
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }

        .sidebar-category {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.5);
            letter-spacing: 0.5px;
            margin: 1.25rem 0 0.5rem 0.75rem;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            padding: 0.65rem 0.85rem;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            transition: all 0.2s ease;
        }

        .nav-item-link i {
            font-size: 1.15rem;
            min-width: 24px;
            text-align: center;
            margin-right: 0.75rem;
            color: rgba(255, 255, 255, 0.75);
        }

        #sidebar.collapsed .nav-item-link {
            justify-content: center;
            padding: 0.75rem 0;
        }

        #sidebar.collapsed .nav-item-link i {
            margin-right: 0;
        }

        .nav-item-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        .nav-item-link.active {
            background-color: #ffffff;
            color: #106742;
        }

        .nav-item-link.active i {
            color: #106742;
        }

        .sidebar-footer {
            padding: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            flex-shrink: 0;
        }

        .user-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 0.75rem;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
        }

        #sidebar.collapsed .user-card {
            justify-content: center;
            padding: 0.5rem 0;
            background: transparent;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background-color: #ffffff;
            color: #106742;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .scope-badge {
            font-size: 0.65rem;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 1px 6px;
            border-radius: 4px;
            margin-top: 2px;
            display: inline-block;
        }

        #main-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }

        #sidebar.collapsed ~ #main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            padding: 0.5rem 0.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 60px;
        }

        .topbar-badge {
            background-color: #f1f5f3;
            border-radius: 50px;
            padding: 0.25rem 0.6rem;
            font-size: 0.7rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .badge-full { display: inline-block; }
        .badge-short { display: none; }

        @media (max-width: 400px) {
            .badge-full { display: none !important; }
            .badge-short { display: inline-block !important; }
            .topbar-subtitle { display: none !important; }
            .topbar { padding: 0.4rem 0.5rem; }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1040;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                left: -260px;
                width: var(--sidebar-width) !important;
            }

            #sidebar.mobile-show {
                left: 0;
            }

            #sidebar.mobile-show + .sidebar-overlay {
                display: block;
            }

            #main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }

            .sidebar-brand-text,
            .sidebar-category,
            .nav-text,
            .user-info-text,
            .scope-badge {
                display: flex !important;
            }
            #sidebar.mobile-show .sidebar-category { display: block !important; }
            #sidebar.mobile-show .nav-item-link { justify-content: flex-start !important; }
            #sidebar.mobile-show .nav-item-link i { margin-right: 0.75rem !important; }
        }
    </style>
</head>
<body>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar">
        <div class="sidebar-header">
            <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" class="sidebar-brand">
                <img src="{{ asset('images/logo-removebg-preview.png') }}" 
                     alt="CTC Logo" 
                     class="sidebar-brand-img"
                     onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
                
                <div class="sidebar-brand-text">
                    <span class="fw-bold fs-6">SIPANEN</span>
                    <small class="text-white-50" style="font-size: 0.65rem;">PT CIPTA TEKNOLOGI CENDEKIA</small>
                </div>
            </a>
            <button id="sidebarToggle" class="btn btn-sm btn-outline-light border-0">
                <i class="bi bi-list fs-5"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span class="nav-text">Dashboard</span>
            </a>

            <div class="sidebar-category">MASTER DATA</div>
            
            @hasrole('admin_pusat')
            <a href="{{ Route::has('users.index') ? route('users.index') : '#' }}" class="nav-item-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span class="nav-text">Manajemen User</span>
            </a>
            @endhasrole

            @hasanyrole('admin_pusat|petugas_provinsi')
            <a href="{{ Route::has('benih.index') ? route('benih.index') : '#' }}" class="nav-item-link {{ request()->routeIs('benih.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>
                <span class="nav-text">Varietas & Komoditas</span>
            </a>
            <a href="{{ Route::has('wilayah.index') ? route('wilayah.index') : '#' }}" class="nav-item-link {{ request()->routeIs('wilayah.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i>
                <span class="nav-text">Wilayah & Gudang BPSIP</span>
            </a>
            @endhasanyrole

            <div class="sidebar-category">DATA & TRANSAKSI</div>
            <a href="{{ Route::has('panen.index') ? route('panen.index') : '#' }}" class="nav-item-link {{ request()->routeIs('panen.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span class="nav-text">Data Panen</span>
            </a>
            <a href="{{ Route::has('stok.index') ? route('stok.index') : '#' }}" class="nav-item-link {{ request()->routeIs('stok.index') ? 'active' : '' }}">
                <i class="bi bi-boxes"></i>
                <span class="nav-text">Stok Benih Wilayah</span>
            </a>
            <a href="{{ Route::has('stok.riwayat') ? route('stok.riwayat') : '#' }}" class="nav-item-link {{ request()->routeIs('stok.riwayat') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span class="nav-text">Riwayat Mutasi</span>
            </a>

            <div class="sidebar-category">LAPORAN</div>

            @hasrole('admin_pusat')
            <a href="{{ Route::has('admin.report.rekap') ? route('admin.report.rekap') : '#' }}" class="nav-item-link {{ request()->routeIs('admin.report.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span class="nav-text">Rekap BBPSIP Nasional</span>
            </a>
            @endhasrole

            @hasrole('petugas_provinsi')
            <a href="{{ Route::has('petugas.report.rekap') ? route('petugas.report.rekap') : '#' }}" class="nav-item-link {{ request()->routeIs('petugas.report.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span class="nav-text">Rekap Stok Wilayah</span>
            </a>
            @endhasrole

            @hasrole('admin_pusat')
            <div class="sidebar-category">SISTEM</div>
            <a href="{{ Route::has('logs.index') ? route('logs.index') : '#' }}" class="nav-item-link {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i>
                <span class="nav-text">Log Aktivitas</span>
            </a>
            <a href="{{ Route::has('settings.index') ? route('settings.index') : '#' }}" class="nav-item-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span class="nav-text">Pengaturan Sistem</span>
            </a>
            @endhasrole
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="user-avatar">
                        {{ $userInitial }}
                    </div>
                    <div class="user-info-text overflow-hidden">
                        <div class="fw-bold text-truncate" style="font-size: 0.85rem;">{{ $userName }}</div>
                        <small class="d-block text-truncate text-white-50" style="font-size: 0.7rem;">{{ $userRole }}</small>
                        @if($namaProvinsi)
                            <span class="scope-badge"><i class="bi bi-geo-alt-fill me-1"></i>{{ $namaProvinsi }}</span>
                        @endif
                    </div>
                </div>
                <form action="{{ Route::has('logout') ? route('logout') : '#' }}" method="POST" class="d-inline m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm text-white-50 border-0 p-1" title="Logout">
                        <i class="bi bi-box-arrow-right fs-6"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div id="main-wrapper">
        <!-- TOPBAR HEADER -->
        <header class="topbar flex-nowrap gap-2">
            <div class="d-flex align-items-center gap-2 overflow-hidden" style="min-width: 0;">
                <button id="mobileSidebarToggle" class="btn btn-sm btn-outline-success d-lg-none rounded-3 flex-shrink-0 px-2 py-1">
                    <i class="bi bi-list fs-6"></i>
                </button>
                <div class="overflow-hidden" style="min-width: 0;">
                    <h6 class="fw-bold mb-0 text-truncate" style="font-size: clamp(0.78rem, 2.8vw, 0.9rem); line-height: 1.2;">
                        Sistem Informasi Pertanian & Stok Benih
                    </h6>
                    <small class="text-muted text-truncate topbar-subtitle" style="font-size: 0.65rem;">
                        SIPANEN Nasional
                    </small>
                </div>
            </div>

            <!-- BADGE USER -->
            <div class="topbar-badge ms-auto flex-shrink-0">
                <span class="d-none d-md-inline me-1">Halo, {{ $userName }}</span>
                @if($namaProvinsi)
                    <span class="badge bg-secondary text-uppercase" style="font-size: 0.6rem;">
                        <span class="badge-full">{{ $namaProvinsi }}</span>
                        <span class="badge-short">{{ $namaProvinsiShort }}</span>
                    </span>
                @endif
                <span class="badge bg-success text-uppercase" style="background-color: var(--primary-green) !important; font-size: 0.6rem;">
                    <span class="badge-full">{{ $userRole }}</span>
                    <span class="badge-short">{{ $userRoleShort }}</span>
                </span>
            </div>
        </header>

        <main class="p-2 p-md-4 flex-grow-1">
            @yield('content')
        </main>

        <!-- FOOTER GLOBAL SISTEM -->
        <footer class="mt-auto py-3 bg-white border-top text-center" style="box-shadow: 0 -2px 10px rgba(0,0,0,0.02);">
            <div class="container-fluid px-4">
                <p class="mb-0 text-muted small fw-semibold" style="font-size: 12px; color: #6c757d;">
                    Sistem Informasi Pertanian & Stok Benih Padi dan Jagung (SIPANEN) &copy; {{ date('Y') }} PT Cipta Teknologi Cendekia. All Rights Reserved.
                </p>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const sidebarToggle = document.getElementById("sidebarToggle");
            const mobileSidebarToggle = document.getElementById("mobileSidebarToggle");
            const sidebarOverlay = document.getElementById("sidebarOverlay");

            if (sidebarToggle) {
                sidebarToggle.addEventListener("click", function() {
                    if (window.innerWidth >= 992) {
                        sidebar.classList.toggle("collapsed");
                        localStorage.setItem("sipanen-sidebar-state", sidebar.classList.contains("collapsed") ? "collapsed" : "expanded");
                    } else {
                        sidebar.classList.remove("mobile-show");
                    }
                });
            }

            if (mobileSidebarToggle) {
                mobileSidebarToggle.addEventListener("click", function() {
                    sidebar.classList.add("mobile-show");
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener("click", function() {
                    sidebar.classList.remove("mobile-show");
                });
            }

            if (window.innerWidth >= 992 && localStorage.getItem("sipanen-sidebar-state") === "collapsed") {
                sidebar.classList.add("collapsed");
            }
        });
    </script>
    @stack('scripts')
</body>
</html>