<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun Petugas - SIPANEN</title>

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Font: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-green: #146c43;
            --primary-green-hover: #0f5132;
            --bg-dark-start: #072b1c;
            --bg-dark-end: #0d472e;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
            background-color: var(--bg-dark-start);
            background-image: 
                radial-gradient(rgba(255, 255, 255, 0.07) 1.2px, transparent 1.2px),
                radial-gradient(circle at 50% 20%, #125c3c 0%, #062417 85%);
            background-size: 26px 26px, 100% 100%;
            color: #1e293b;
        }

        .auth-card {
            width: 100%;
            max-width: 470px;
            background: #ffffff;
            border-radius: 24px;
            padding: 2.35rem 2.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
        }

        .logo-badge {
            width: 64px;
            height: 64px;
            margin: 0 auto 1.15rem;
            border-radius: 18px;
            background: linear-gradient(135deg, #198754 0%, #106742 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            box-shadow: 0 10px 24px rgba(20, 108, 67, 0.28);
        }

        .auth-title {
            font-weight: 800;
            font-size: 1.38rem;
            color: #111827;
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
        }

        .auth-subtitle {
            font-size: 0.84rem;
            color: #64748b;
            margin-bottom: 1.65rem;
        }

        .form-label {
            font-size: 0.84rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-left {
            position: absolute;
            left: 1rem;
            color: #94a3b8;
            font-size: 1rem;
            pointer-events: none;
            z-index: 4;
        }

        .form-control-custom,
        .form-select-custom {
            width: 100%;
            height: 45px;
            padding: 0.6rem 2.75rem 0.6rem 2.75rem;
            font-size: 0.89rem;
            color: #1e293b;
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s ease;
        }

        .form-select-custom {
            padding-right: 2.25rem;
            cursor: pointer;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            outline: none;
            background-color: #ffffff;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 4px rgba(20, 108, 67, 0.12);
        }

        .form-control-custom::placeholder {
            color: #94a3b8;
        }

        .btn-toggle-pw {
            position: absolute;
            right: 0.85rem;
            background: transparent;
            border: none;
            color: #94a3b8;
            padding: 0.25rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            z-index: 4;
        }

        .btn-toggle-pw:hover {
            color: #475569;
        }

        .helper-note {
            font-size: 0.76rem;
            color: #64748b;
            line-height: 1.45;
            margin-bottom: 1.15rem;
        }

        .link-green {
            color: var(--primary-green);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .link-green:hover {
            color: var(--primary-green-hover);
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            height: 48px;
            background-color: var(--primary-green);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.94rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(20, 108, 67, 0.25);
        }

        .btn-submit:hover {
            background-color: var(--primary-green-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(20, 108, 67, 0.32);
        }

        .auth-divider {
            border-top: 1px solid #f1f5f9;
            margin: 1.4rem 0 1.15rem;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 1.85rem 1.4rem;
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="text-center">
            <div class="logo-badge">
                <i class="bi bi-tree-fill"></i>
            </div>
            <h1 class="auth-title">Pendaftaran Akun Petugas Baru</h1>
            <p class="auth-subtitle">Lengkapi data untuk pembuatan akun operasional SIPANEN</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 rounded-3 small mb-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Nama Lengkap -->
            <div class="mb-3">
                <label for="name" class="form-label">Nama Lengkap</label>
                <div class="input-group-custom">
                    <i class="bi bi-person-fill input-icon-left"></i>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           class="form-control-custom @error('name') border-danger @enderror" 
                           placeholder="Nama lengkap petugas" 
                           required 
                           autofocus>
                </div>
            </div>

            <!-- Alamat Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <div class="input-group-custom">
                    <i class="bi bi-envelope-fill input-icon-left"></i>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           class="form-control-custom @error('email') border-danger @enderror" 
                           placeholder="email@pertanian.go.id" 
                           required>
                </div>
            </div>

            <!-- Provinsi Wilayah Tugas -->
            <div class="mb-2">
                <label for="provinsi_id" class="form-label">Provinsi Wilayah Tugas</label>
                <div class="input-group-custom">
                    <i class="bi bi-geo-alt-fill input-icon-left"></i>
                    <select id="provinsi_id" 
                            name="provinsi_id" 
                            class="form-select form-select-custom @error('provinsi_id') border-danger @enderror" 
                            required>
                        <option value="">-- Pilih Provinsi Wilayah Tugas --</option>
                        @if(isset($provinsis))
                            @foreach($provinsis as $prov)
                                <option value="{{ $prov->id }}" {{ old('provinsi_id') == $prov->id ? 'selected' : '' }}>
                                    {{ $prov->nama_provinsi ?? $prov->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <p class="helper-note">
                Akun baru akan diberikan akses Petugas Provinsi sesuai wilayah tugas yang dipilih. Pengaturan hak akses lanjutan dikelola oleh Admin Pusat.
            </p>

            <!-- Kata Sandi -->
            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <div class="input-group-custom">
                    <i class="bi bi-lock-fill input-icon-left"></i>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control-custom @error('password') border-danger @enderror" 
                           placeholder="Minimal 8 karakter" 
                           required>
                    <button type="button" class="btn-toggle-pw" onclick="togglePassword('password', 'icon-pw-reg')" title="Lihat Kata Sandi">
                        <i class="bi bi-eye-fill" id="icon-pw-reg"></i>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Kata Sandi -->
            <div class="mb-4">
                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                <div class="input-group-custom">
                    <i class="bi bi-lock-fill input-icon-left"></i>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control-custom" 
                           placeholder="Ulangi kata sandi" 
                           required>
                    <button type="button" class="btn-toggle-pw" onclick="togglePassword('password_confirmation', 'icon-pw-conf')" title="Lihat Konfirmasi Kata Sandi">
                        <i class="bi bi-eye-fill" id="icon-pw-conf"></i>
                    </button>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-submit">
                <i class="bi bi-person-plus-fill fs-5"></i>
                <span>Daftar Akun Baru</span>
            </button>
        </form>

        <div class="auth-divider"></div>

        <p class="text-center text-secondary mb-0" style="font-size: 0.84rem;">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="link-green">Masuk di sini</a>
        </p>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-fill');
                icon.classList.add('bi-eye-slash-fill');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash-fill');
                icon.classList.add('bi-eye-fill');
            }
        }
    </script>
</body>
</html>