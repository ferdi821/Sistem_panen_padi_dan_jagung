@extends('layouts.app')

@section('content')
<style>
    #main-wrapper {
        min-height: auto !important;
    }
    main.flex-grow-1 {
        flex-grow: 0 !important;
    }
</style>

<div class="container-fluid px-4 py-3">

    <!-- HEADER HALAMAN -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #106742;">Manajemen Pengguna (User)</h3>
            <p class="text-muted small mb-0">Kelola akun pengguna, hak akses role, dan penetapan wilayah provinsi.</p>
        </div>
        
        <!-- TOMBOL PEMICU MODAL TAMBAH USER -->
        <button type="button" class="btn btn-success shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahUser" style="background-color: #106742; border: none; font-size: 13px; font-weight: 600;">
            + Tambah User Baru
        </button>
    </div>

    <!-- NOTIFIKASI BERHASIL -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- NOTIFIKASI ERROR -->
    @if(session('error') || $errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            @if(session('error'))
                {{ session('error') }}
            @else
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- TABEL USER -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0" style="font-size: 13px;">
                    <thead style="background-color: #f3f7f5; color: #315b49;">
                        <tr>
                            <th class="text-start ps-4 py-3">Nama Lengkap</th>
                            <th class="text-start py-3">Email</th>
                            <th class="py-3">Role / Hak Akses</th>
                            <th class="py-3">Wilayah Provinsi</th>
                            <th class="py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="text-start ps-4 fw-bold" style="color: #273b32;">{{ $user->name }}</td>
                                <td class="text-start">{{ $user->email }}</td>
                                <td>
                                    @php $roleName = $user->roles->pluck('name')->first() ?? '-'; @endphp
                                    @if($roleName === 'admin_pusat')
                                        <span class="badge px-2 py-1" style="background-color: #106742; color: #fff; font-size: 10px; font-weight: 700;">ADMIN_PUSAT</span>
                                    @elseif($roleName === 'petugas_provinsi')
                                        <span class="badge px-2 py-1" style="background-color: #0ea5e9; color: #fff; font-size: 10px; font-weight: 700;">PETUGAS_PROVINSI</span>
                                    @else
                                        <span class="badge px-2 py-1 bg-secondary font-weight-bold" style="font-size: 10px;">{{ strtoupper($roleName) }}</span>
                                    @endif
                                </td>
                                <td>{{ $user->provinsi->nama_provinsi ?? 'Nasional / Semua' }}</td>
                                <td>
                                    <!-- TOMBOL PEMICU MODAL EDIT -->
                                    <button type="button" class="btn btn-sm btn-outline-warning px-2 py-1" data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $user->id }}" style="font-size: 11px; font-weight: 600;">
                                        Edit
                                    </button>

                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" style="font-size: 11px; font-weight: 600;">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- MODAL EDIT USER PER BARIS DATA -->
                            <div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                                        <div class="modal-header text-white px-4 py-3" style="background-color: #e67e22;">
                                            <h5 class="modal-title fw-bold fs-6">Edit Data Pengguna</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4 text-start">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Nama Lengkap</label>
                                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $user->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Email</label>
                                                    <input type="email" name="email" class="form-control form-control-sm" value="{{ $user->email }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Role / Hak Akses</label>
                                                    <select name="role" class="form-select form-select-sm" required>
                                                        @foreach($roles as $r)
                                                            <option value="{{ $r->name }}" {{ $user->hasRole($r->name) ? 'selected' : '' }}>
                                                                {{ strtoupper(str_replace('_', ' ', $r->name)) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Wilayah Provinsi (Opsional)</label>
                                                    <select name="provinsi_id" class="form-select form-select-sm">
                                                        <option value="">-- Tidak Terikat Provinsi (Admin / Pimpinan) --</option>
                                                        @foreach($provinsis as $prov)
                                                            <option value="{{ $prov->id }}" {{ $user->provinsi_id == $prov->id ? 'selected' : '' }}>
                                                                {{ $prov->nama_provinsi }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Password Baru (Kosongkan jika tidak ingin diubah)</label>
                                                    <input type="password" name="password" class="form-control form-control-sm" placeholder="Minimal 8 karakter">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold text-white" style="background-color: #e67e22; border: none;">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-muted">Belum ada data pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PAGINATION -->
    @if ($users->hasPages())
        <div class="d-flex justify-content-center mt-3 mb-4">
            {{ $users->links() }}
        </div>
    @endif

</div>

<!-- MODAL TAMBAH USER BARU -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white px-4 py-3" style="background-color: #106742;">
                <h5 class="modal-title fw-bold fs-6">Tambah Pengguna (User) Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Contoh: Ahmad Subagja" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Email</label>
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="nama@pertanian.go.id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Role / Hak Akses</label>
                        <select name="role" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}">{{ strtoupper(str_replace('_', ' ', $r->name)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Wilayah Provinsi (Opsional)</label>
                        <select name="provinsi_id" class="form-select form-select-sm">
                            <option value="">-- Pilih Provinsi (Kosongkan jika Admin/Pimpinan) --</option>
                            @foreach($provinsis as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->nama_provinsi }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted" style="font-size: 11px;">*Wajib diisi jika role yang dipilih adalah Petugas Provinsi.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Password Default</label>
                        <input type="password" name="password" class="form-control form-control-sm" placeholder="Minimal 8 karakter" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" style="background-color: #106742; border: none;">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection