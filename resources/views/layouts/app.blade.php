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
    } elseif ($userRole === 'PIMPINAN') {
        $userRoleShort = 'PIMPINAN';
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
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPANEN - Sistem Informasi Pangan & Stok Benih')</title>

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Anti-Flicker: Terapkan tema dari localStorage sebelum render -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('sipanen-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <style>
        :root {
            --primary-green: #106742;
            --primary-green-hover: #0c5234;
            --bg-light: #f4f7f4;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 82px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-light);
            color: #1e293b;
            overflow-x: hidden;
            transition: background-color 0.25s ease, color 0.25s ease;
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
            padding: 0.75rem 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            height: 70px;
            flex-shrink: 0;
            overflow: hidden;
            gap: 0.5rem;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            color: #ffffff;
            overflow: hidden;
            min-width: 0;
        }

        .sidebar-brand-img {
            height: 38px;
            width: 38px;
            min-width: 38px;
            object-fit: contain;
            flex-shrink: 0;
            background: #ffffff;
            border-radius: 8px;
            padding: 2px;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            white-space: nowrap;
            transition: opacity 0.2s ease;
            min-width: 0;
        }

        /* Tombol Garis Tiga */
        #sidebarToggle {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #sidebarToggle:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* KONDISI SAAT SIDEBAR COLLAPSED */
        #sidebar.collapsed .sidebar-header {
            justify-content: space-between;
            padding: 0.75rem 0.45rem;
            gap: 4px;
        }

        #sidebar.collapsed .sidebar-brand {
            gap: 0;
        }

        #sidebar.collapsed .sidebar-brand-text,
        #sidebar.collapsed .sidebar-category,
        #sidebar.collapsed .nav-text,
        #sidebar.collapsed .user-info-text,
        #sidebar.collapsed .scope-badge {
            display: none !important;
        }

        #sidebar.collapsed .sidebar-brand-img {
            height: 34px;
            width: 34px;
            min-width: 34px;
            padding: 1px;
        }

        #sidebar.collapsed #sidebarToggle {
            width: 32px;
            height: 34px;
            padding: 0;
        }

        #sidebar.collapsed .sidebar-footer {
            padding: 0.75rem 0.25rem;
        }

        #sidebar.collapsed .user-card {
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0;
            background: transparent;
            gap: 0.5rem;
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

        #sidebar .nav-item-link {
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

        #sidebar .nav-item-link i {
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

        #sidebar .nav-item-link:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }

        #sidebar .nav-item-link.active {
            background-color: #ffffff !important;
            color: #106742 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        #sidebar .nav-item-link.active i {
            color: #106742 !important;
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
            color: #ffffff;
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
            flex-shrink: 0;
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
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }

        #sidebar.collapsed ~ #main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
            width: calc(100% - var(--sidebar-collapsed-width));
        }

        @media (max-width: 991.98px) {
            #main-wrapper {
                margin-left: var(--sidebar-collapsed-width) !important;
                width: calc(100% - var(--sidebar-collapsed-width)) !important;
            }
            #sidebar:not(.collapsed) {
                box-shadow: 6px 0 24px rgba(0, 0, 0, 0.25);
            }
        }

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.5rem 0.85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 70px;
            transition: background-color 0.25s ease, border-color 0.25s ease;
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
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Tombol Switch Mode Gelap / Terang */
        #themeToggle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        #themeToggle:hover {
            background-color: #e2e8f0;
            transform: rotate(15deg);
        }

        .global-footer {
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.02);
            transition: background-color 0.25s ease, border-color 0.25s ease;
        }

        .badge-full { display: inline-block; }
        .badge-short { display: none; }

        @media (max-width: 576px) {
            .badge-full { display: none !important; }
            .badge-short { display: inline-block !important; }
            .topbar-subtitle { display: none !important; }
            .topbar { padding: 0.4rem 0.6rem; }
        }

        /* =========================================================
           1. MODE TERANG (LIGHT): TAMPILAN NORMAL & FONT HITAM
           ========================================================= */
        html[data-bs-theme="light"] .card {
            border: 1px solid #d5e0da !important;
            box-shadow: 0 2px 10px rgba(16, 103, 66, 0.05);
        }

        html[data-bs-theme="light"] table,
        html[data-bs-theme="light"] table thead th:not([style*="color"]),
        html[data-bs-theme="light"] table tbody td:not([style*="color"]) {
            color: #1e293b !important;
        }

        html[data-bs-theme="light"] table tbody td .text-white:not(.badge),
        html[data-bs-theme="light"] table tbody td .text-light:not(.badge) {
            color: #1e293b !important;
        }

        /* =========================================================
           2. MODE GELAP (DARK): HALAMAN GELAP & FONT TABEL PUTIH
           ========================================================= */
        html[data-bs-theme="dark"] body {
            background-color: #0b1120 !important;
            color: #f8fafc !important;
        }

        html[data-bs-theme="dark"] #sidebar {
            background: #0c4d31 !important;
            border-right-color: rgba(255, 255, 255, 0.1) !important;
        }

        html[data-bs-theme="dark"] .topbar {
            background-color: #111827 !important;
            border-bottom-color: #243049 !important;
        }

        html[data-bs-theme="dark"] .topbar h6 {
            color: #f8fafc !important;
        }

        html[data-bs-theme="dark"] .topbar-badge {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border: 1px solid #334155 !important;
        }

        html[data-bs-theme="dark"] #themeToggle {
            background-color: #1e293b !important;
            border-color: #475569 !important;
            color: #fbbf24 !important;
        }

        html[data-bs-theme="dark"] #themeToggle:hover {
            background-color: #334155 !important;
        }

        /* Card & Container di Mode Gelap (Kecualikan Card Berwarna / KPI Dashboard) */
        html[data-bs-theme="dark"] .card:not([class*="bg-"]):not([style*="background"]),
        html[data-bs-theme="dark"] .bg-white {
            background-color: #162033 !important;
            border-color: #2c3b57 !important;
            color: #f8fafc !important;
        }

        /* Jaga kontras teks pada card KPI kuning & lingkaran ikonnya */
        html[data-bs-theme="dark"] .bg-warning,
        html[data-bs-theme="dark"] [style*="#ffc107"],
        html[data-bs-theme="dark"] [style*="#f59e0b"],
        html[data-bs-theme="dark"] [style*="#eab308"] {
            color: #1e293b !important;
        }

        html[data-bs-theme="dark"] .card-header,
        html[data-bs-theme="dark"] .card-footer,
        html[data-bs-theme="dark"] .bg-light {
            background-color: #1c2840 !important;
            border-color: #2c3b57 !important;
            color: #f8fafc !important;
        }

        /* Judul halaman & teks umum yang hijau gelap dijadikan putih bersih (KECUALI .badge dan isi tabel) */
        html[data-bs-theme="dark"] main h1,
        html[data-bs-theme="dark"] main h2,
        html[data-bs-theme="dark"] main h3,
        html[data-bs-theme="dark"] main h4,
        html[data-bs-theme="dark"] main h5,
        html[data-bs-theme="dark"] .text-dark:not(table *),
        html[data-bs-theme="dark"] [style*="color: #2c3e50"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color: #1e293b"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color: #333"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color: #106742"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color:#106742"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color: #0d5c3b"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color:#0d5c3b"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color: #0f5132"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color:#0f5132"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color: #155724"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color:#155724"]:not(.badge):not(table *),
        html[data-bs-theme="dark"] [style*="color: rgb(16, 103, 66)"]:not(.badge):not(table *) {
            color: #f8fafc !important;
        }

        html[data-bs-theme="dark"] .text-secondary:not(table *),
        html[data-bs-theme="dark"] .text-muted:not(table *) {
            color: #cbd5e1 !important;
        }

        html[data-bs-theme="dark"] .border,
        html[data-bs-theme="dark"] .border-top,
        html[data-bs-theme="dark"] .border-bottom {
            border-color: #2d3b55 !important;
        }

        /* Cerahkan teks label/legenda grafik (ApexCharts / SVG) saat Mode Gelap */
        html[data-bs-theme="dark"] .apexcharts-text tspan,
        html[data-bs-theme="dark"] .apexcharts-legend-text {
            fill: #cbd5e1 !important;
            color: #cbd5e1 !important;
        }

        /* Tabel di Mode Gelap: Background bawaan tema, khusus font teks diubah jadi putih */
        html[data-bs-theme="dark"] table,
        html[data-bs-theme="dark"] table thead th:not([style*="background"]),
        html[data-bs-theme="dark"] table tbody td:not([style*="background"]) {
            color: #f8fafc !important;
        }

        html[data-bs-theme="dark"] table tbody td:not([style*="background"]) .text-dark:not(.badge),
        html[data-bs-theme="dark"] table tbody td:not([style*="background"]) .text-muted:not(.badge),
        html[data-bs-theme="dark"] table tbody td:not([style*="background"]) .text-secondary:not(.badge),
        html[data-bs-theme="dark"] table tbody td:not([style*="background"]) .text-success:not(.badge),
        html[data-bs-theme="dark"] table tbody td:not([style*="background"]) span:not(.badge) {
            color: #f8fafc !important;
        }

        /* Sel/Header tabel yang memiliki latar warna pastel (seperti Rekap BBPSIP) tetap kontras */
        html[data-bs-theme="dark"] table thead th[style*="background"]:not([style*="#fff"]):not([style*="#ffffff"]):not([style*="#f8fafc"]):not([style*="#f1f5f9"]):not([style*="white"]),
        html[data-bs-theme="dark"] table tbody td[style*="background"] {
            color: #1e293b !important;
        }

        /* Header sticky berlatar putih/abu muda (No, Satker, FS, SS) di Rekap BBPSIP ikut gelap di Mode Gelap */
        html[data-bs-theme="dark"] table thead th[style*="#fff"],
        html[data-bs-theme="dark"] table thead th[style*="#ffffff"],
        html[data-bs-theme="dark"] table thead th[style*="#f8fafc"],
        html[data-bs-theme="dark"] table thead th[style*="#f1f5f9"],
        html[data-bs-theme="dark"] table thead th[style*="white"] {
            background-color: #1e293b !important;
            color: #f8fafc !important;
        }

        /* Pastikan badge PADI tetap hijau tua tegas di atas latar hijau pastel pada kedua mode */
        .badge[style*="#d1e7dd"],
        .badge[style*="#dcfce7"],
        .badge[style*="#e8f5e9"] {
            color: #0f5132 !important;
        }

        /* Input & Select di Mode Gelap */
        html[data-bs-theme="dark"] .form-control,
        html[data-bs-theme="dark"] .form-select {
            background-color: #0f172a !important;
            border-color: #3b4d6e !important;
            color: #f8fafc !important;
        }

        html[data-bs-theme="dark"] .form-control:focus,
        html[data-bs-theme="dark"] .form-select:focus {
            background-color: #0f172a !important;
            border-color: #34d399 !important;
            color: #ffffff !important;
        }

        html[data-bs-theme="dark"] .global-footer {
            background-color: #111827 !important;
            border-top-color: #243049 !important;
        }
    </style>
</head>
<body>

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
            <!-- TOMBOL GARIS TIGA SELALU TAMPIL DI SEMUA UKURAN LAYAR -->
            <button id="sidebarToggle" type="button" title="Perkecil / Perlebar Menu">
                <i class="bi bi-list fs-5"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ Route::has('dashboard') ? route('dashboard') : '#' }}" class="nav-item-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span class="nav-text">{{ $userRole === 'PIMPINAN' ? 'Executive Dashboard' : 'Dashboard' }}</span>
            </a>

            @if($userRole === 'PIMPINAN')
                <!-- SIDEBAR KHUSUS ROLE PIMPINAN -->
                <div class="sidebar-category">MONITORING NASIONAL</div>
                <a href="{{ Route::has('dashboard') ? route('dashboard') . '#analisis-capaian' : '#' }}" class="nav-item-link">
                    <i class="bi bi-bullseye"></i>
                    <span class="nav-text">Analisis Capaian & Target</span>
                </a>
                <a href="{{ Route::has('panen.index') ? route('panen.index') : '#' }}" class="nav-item-link {{ request()->routeIs('panen.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span class="nav-text">Monitoring Hasil Panen</span>
                </a>
                <a href="{{ Route::has('stok.index') ? route('stok.index') : '#' }}" class="nav-item-link {{ request()->routeIs('stok.index') ? 'active' : '' }}">
                    <i class="bi bi-boxes"></i>
                    <span class="nav-text">Stok Benih Wilayah</span>
                </a>
                <a href="{{ Route::has('stok.riwayat') ? route('stok.riwayat') : '#' }}" class="nav-item-link {{ request()->routeIs('stok.riwayat') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span class="nav-text">Riwayat Distribusi Benih</span>
                </a>

                <div class="sidebar-category">PEMETAAN & VARIETAS</div>
                <a href="{{ Route::has('wilayah.index') ? route('wilayah.index') : '#' }}" class="nav-item-link {{ request()->routeIs('wilayah.*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt"></i>
                    <span class="nav-text">Sebaran Gudang BPSIP</span>
                </a>
                <a href="{{ Route::has('benih.index') ? route('benih.index') : '#' }}" class="nav-item-link {{ request()->routeIs('benih.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i>
                    <span class="nav-text">Katalog Varietas Benih</span>
                </a>

                <div class="sidebar-category">LAPORAN EKSEKUTIF</div>
                <a href="{{ Route::has('admin.report.rekap') ? route('admin.report.rekap') : '#' }}" class="nav-item-link {{ request()->routeIs('admin.report.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span class="nav-text">Rekap BBPSIP Nasional</span>
                </a>
                <a href="{{ Route::has('logs.index') ? route('logs.index') : '#' }}" class="nav-item-link {{ request()->routeIs('logs.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i>
                    <span class="nav-text">Audit Aktivitas Sistem</span>
                </a>
            @else
                <!-- SIDEBAR ADMIN PUSAT & PETUGAS PROVINSI -->
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
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="user-avatar">
                        {{ $userInitial }}
                    </div>
                    <div class="user-info-text overflow-hidden">
                        <div class="fw-bold text-truncate text-white" style="font-size: 0.85rem;">{{ $userName }}</div>
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
                <div class="overflow-hidden" style="min-width: 0;">
                    <h6 class="fw-bold mb-0 text-truncate" style="font-size: clamp(0.78rem, 2.8vw, 0.95rem); line-height: 1.2;">
                        Sistem Informasi Pertanian & Stok Benih
                    </h6>
                    <small class="text-muted text-truncate topbar-subtitle" style="font-size: 0.65rem;">
                        SIPANEN Nasional
                    </small>
                </div>
            </div>

            <!-- TOMBOL DARK/LIGHT MODE + BADGE USER -->
            <div class="d-flex align-items-center gap-2 ms-auto flex-shrink-0">
                <button id="themeToggle" type="button" title="Ganti Tema Gelap / Terang">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>

                <div class="topbar-badge">
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
            </div>
        </header>

        <main class="p-2 p-md-4 flex-grow-1">
            @yield('content')
        </main>

        <!-- FOOTER GLOBAL SISTEM -->
        <footer class="mt-auto py-3 global-footer text-center">
            <div class="container-fluid px-4">
                <p class="mb-0 text-muted small fw-semibold" style="font-size: 12px;">
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
            const themeToggle = document.getElementById("themeToggle");
            const themeIcon = document.getElementById("themeIcon");
            const htmlEl = document.documentElement;

            // Atur kondisi awal: di layar kecil (< 992px) default collapsed agar konten tetap lega, di layar besar ikuti localStorage
            if (window.innerWidth < 992) {
                sidebar.classList.add("collapsed");
            } else if (localStorage.getItem("sipanen-sidebar-state") === "collapsed") {
                sidebar.classList.add("collapsed");
            }

            // Tombol Toggle bekerja di SEMUA ukuran layar (Desktop, Tablet, maupun HP/Split Screen)
            if (sidebarToggle) {
                sidebarToggle.addEventListener("click", function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle("collapsed");
                    if (window.innerWidth >= 992) {
                        localStorage.setItem("sipanen-sidebar-state", sidebar.classList.contains("collapsed") ? "collapsed" : "expanded");
                    }
                });
            }

            // Toggle Mode Gelap / Terang
            function updateThemeIcon(theme) {
                if (!themeIcon) return;
                if (theme === "dark") {
                    themeIcon.classList.remove("bi-moon-stars-fill");
                    themeIcon.classList.add("bi-sun-fill");
                } else {
                    themeIcon.classList.remove("bi-sun-fill");
                    themeIcon.classList.add("bi-moon-stars-fill");
                }
            }

            updateThemeIcon(htmlEl.getAttribute("data-bs-theme"));

            if (themeToggle) {
                themeToggle.addEventListener("click", function() {
                    const currentTheme = htmlEl.getAttribute("data-bs-theme");
                    const newTheme = currentTheme === "dark" ? "light" : "dark";
                    htmlEl.setAttribute("data-bs-theme", newTheme);
                    localStorage.setItem("sipanen-theme", newTheme);
                    updateThemeIcon(newTheme);
                    window.dispatchEvent(new CustomEvent("sipanen-theme-changed", { detail: { theme: newTheme } }));
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>