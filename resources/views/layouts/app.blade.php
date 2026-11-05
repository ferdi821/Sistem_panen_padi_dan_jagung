@php
    $currentUser = auth()->user();
    $userName = $currentUser->name ?? 'Administrator';
    $userInitial = strtoupper(substr($userName, 0, 1));
    
    // Deteksi Role User
    $userRole = 'GUEST';
    if ($currentUser) {
        if (method_exists($currentUser, 'getRoleNames') && $currentUser->getRoleNames()->isNotEmpty()) {
            $userRole = strtoupper($currentUser->getRoleNames()->first());
        } elseif (isset($currentUser->role) && !empty($currentUser->role)) {
            $userRole = strtoupper($currentUser->role);
        }
    }

    // Deteksi Wilayah Provinsi (Aman untuk Admin Pusat & Pimpinan karena akan null)
    $namaProvinsi = null;
    if ($currentUser) {
        if (isset($currentUser->provinsi->nama_provinsi)) {
            $namaProvinsi = $currentUser->provinsi->nama_provinsi;
        } elseif (isset($currentUser->nama_provinsi)) {
            $namaProvinsi = $currentUser->nama_provinsi;
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

        /* Sidebar Styling (Hijau Gelap Khas SIPANEN) */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #106742;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        #sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        /* Header Logo Sidebar */
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
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            transition: opacity 0.2s ease;
            white-space: nowrap;
        }

        #sidebar.collapsed .sidebar-brand-text,
        #sidebar.collapsed .sidebar-category,
        #sidebar.collapsed .nav-text,
        #sidebar.collapsed .user-info-text,
        #sidebar.collapsed .scope-badge {
            display: none !important;
        }

        /* Navigation Container Scrollable */
        .sidebar-nav {
            padding: 1rem 0.75rem;
            flex: 1;
            overflow-y: auto;
            min-height: 0;
        }

        /* Styling Scrollbar Tipis */
        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
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
            transition: color 0.2s ease;
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

        /* User Profile Footer */
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

        /* Content Body Wrapper */
        #main-wrapper {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #sidebar.collapsed ~ #main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            padding: 0.85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        .topbar-badge {
            background-color: #f1f5f3;
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body>

    <!-- SIDEBAR -->
    <aside id="sidebar">
        <!-- Logo & Brand Header -->
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

        <!-- Navigation Links -->
        <nav class="sidebar-nav">
            <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span class="nav-text">Dashboard</span>
            </a>

            @hasrole('admin_pusat')
            <div class="sidebar-category">MASTER DATA</div>
            <a href="{{ Route::has('users.index') ? route('users.index') : '#' }}" class="nav-item-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span class="nav-text">Manajemen User</span>
            </a>
            <a href="{{ Route::has('benih.index') ? route('benih.index') : '#' }}" class="nav-item-link {{ request()->routeIs('benih.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i>
                <span class="nav-text">Varietas & Komoditas</span>
            </a>
            <a href="{{ Route::has('wilayah.index') ? route('wilayah.index') : '#' }}" class="nav-item-link {{ request()->routeIs('wilayah.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt"></i>
                <span class="nav-text">Wilayah & Gudang BPSIP</span>
            </a>
            @endhasrole

            <div class="sidebar-category">DATA & TRANSAKSI</div>
            <a href="{{ Route::has('panen.index') ? route('panen.index') : '#' }}" class="nav-item-link {{ request()->routeIs('panen.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span class="nav-text">Data Panen</span>
            </a>
            <a href="{{ Route::has('pengujian.index') ? route('pengujian.index') : '#' }}" class="nav-item-link {{ request()->routeIs('pengujian.*') ? 'active' : '' }}">
                <i class="bi bi-patch-check"></i>
                <span class="nav-text">Uji Mutu Benih</span>
            </a>
            <a href="{{ Route::has('stok.index') ? route('stok.index') : '#' }}" class="nav-item-link {{ request()->routeIs('stok.index') ? 'active' : '' }}">
                <i class="bi bi-boxes"></i>
                <span class="nav-text">Stok Benih Wilayah</span>
            </a>
            @hasanyrole('admin_pusat|petugas_provinsi')
            <a href="{{ Route::has('stok.keluar.create') ? route('stok.keluar.create') : '#' }}" class="nav-item-link {{ request()->routeIs('stok.keluar.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-up-right"></i>
                <span class="nav-text">Stok Keluar / Distribusi</span>
            </a>
            @endhasanyrole
            <a href="{{ Route::has('stok.riwayat') ? route('stok.riwayat') : '#' }}" class="nav-item-link {{ request()->routeIs('stok.riwayat') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span class="nav-text">Riwayat Mutasi</span>
            </a>
            @hasrole('admin_pusat')
            <a href="{{ Route::has('approval.index') ? route('approval.index') : '#' }}" class="nav-item-link {{ request()->routeIs('approval.*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i>
                <span class="nav-text">Persetujuan Koreksi</span>
            </a>
            @endhasrole

            <!-- MENU TAMBAHAN KHUSUS PETUGAS PROVINSI -->
            @hasrole('petugas_provinsi')
            <div class="sidebar-category">KONTROL & AUDIT</div>
            <a href="{{ Route::has('approval.index') ? route('approval.index') : '#' }}" class="nav-item-link {{ request()->routeIs('approval.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-diff"></i>
                <span class="nav-text">Pengajuan Koreksi</span>
            </a>
            <a href="{{ Route::has('logs.index') ? route('logs.index') : '#' }}" class="nav-item-link {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i>
                <span class="nav-text">Log Aktivitas Saya</span>
            </a>
            @endhasrole

            <div class="sidebar-category">LAPORAN</div>
            <a href="{{ Route::has('report.rekap') ? route('report.rekap') : '#' }}" class="nav-item-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span class="nav-text">Rekap BBPSIP</span>
            </a>

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

        <!-- User Profile Footer -->
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
        <header class="topbar">
            <div>
                <h6 class="fw-bold mb-0">Sistem Informasi Pertanian & Stok Benih</h6>
                <small class="text-muted" style="font-size: 0.75rem;">SIPANEN Nasional</small>
            </div>
            <div class="topbar-badge">
                <span>Halo, {{ $userName }}</span>
                @if($namaProvinsi)
                    <span class="badge bg-secondary text-uppercase ms-1">
                        {{ $namaProvinsi }}
                    </span>
                @endif
                <span class="badge bg-success text-uppercase" style="background-color: var(--primary-green) !important;">
                    {{ $userRole }}
                </span>
            </div>
        </header>

        <main class="p-4 flex-grow-1">
            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Collapsible Toggle Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const toggleBtn = document.getElementById("sidebarToggle");

            toggleBtn.addEventListener("click", function() {
                sidebar.classList.toggle("collapsed");
                if (sidebar.classList.contains("collapsed")) {
                    localStorage.setItem("sipanen-sidebar-state", "collapsed");
                } else {
                    localStorage.setItem("sipanen-sidebar-state", "expanded");
                }
            });

            if (localStorage.getItem("sipanen-sidebar-state") === "collapsed") {
                sidebar.classList.add("collapsed");
            }
        });
    </script>
</body>
</html>