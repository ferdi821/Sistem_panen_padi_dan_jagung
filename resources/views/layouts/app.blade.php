<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pencatatan Padi & Jagung Nasional</title> <!--[cite: 3] -->

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <!--[cite: 3] -->
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light"> <!--[cite: 3] -->

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm mb-4"> <!--[cite: 3] -->
        <div class="container"> <!--[cite: 3] -->
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}"> <!--[cite: 3] -->
                <i class="bi bi-tree-fill me-2"></i> SIPANEN NASIONAL
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                @auth
                <!-- Menu Kiri -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('panen.*') ? 'active fw-bold' : '' }}" href="{{ route('panen.index') }}">
                            <i class="bi bi-journal-text"></i> Data Panen
                        </a>
                    </li>

                    {{-- Menu Khusus Admin Pusat --}}
                    @role('admin_pusat')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('users.*') || request()->routeIs('roles.*') ? 'active fw-bold' : '' }}"
                           href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear-fill"></i> Management Sistem
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('users.index') }}">
                                    <i class="bi bi-people-fill me-1"></i> Kelola User
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('roles.index') }}">
                                    <i class="bi bi-shield-lock-fill me-1"></i> Kelola Role & Permission
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endrole
                </ul>

                <!-- Menu Kanan (User Info & Logout) -->
                <div class="navbar-nav ms-auto align-items-lg-center"> <!--[cite: 3] -->
                    <div class="nav-link text-white me-lg-3"> <!--[cite: 3] -->
                        <i class="bi bi-person-circle me-1"></i>
                        <strong>{{ auth()->user()->name }}</strong> <!--[cite: 3] -->
                        <span class="badge bg-light text-dark ms-1">
                            {{ strtoupper(str_replace('_', ' ', auth()->user()->getRoleNames()->first() ?? 'GUEST')) }}
                        </span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline"> <!--[cite: 3] -->
                        @csrf <!--[cite: 3] -->
                        <button class="btn btn-outline-light btn-sm w-100" type="submit"> <!--[cite: 3] -->
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form> <!--[cite: 3] -->
                </div>
                @endauth
            </div>
        </div> <!--[cite: 3] -->
    </nav> <!--[cite: 3] -->

    <!-- Main Content Container -->
    <main class="container mb-5"> <!--[cite: 3] -->
        <!-- Flash Alert Message Sukses -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert"> <!--[cite: 3] -->
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }} <!--[cite: 3] -->
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> <!--[cite: 3] -->
            </div>
        @endif

        <!-- Flash Alert Message Error -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content') <!--[cite: 3] -->
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-white border-top text-center text-muted">
        <div class="container">
            <small>&copy; {{ date('Y') }} Kementerian Pertanian Republik Indonesia. All rights reserved.</small>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> <!--[cite: 3] -->
    @stack('scripts')
</body>
</html>
