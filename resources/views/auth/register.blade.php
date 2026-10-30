@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0">Registrasi Petugas Provinsi</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Wilayah Provinsi</label>
                        <select name="provinsi_id" class="form-select @error('provinsi_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinsis as $prov)
                                <option value="{{ $prov->id }}" {{ old('provinsi_id') == $prov->id ? 'selected' : '' }}>
                                    {{ $prov->nama_provinsi }}
                                </option>
                            @endforeach
                        </select>
                        @error('provinsi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mb-3">Daftar Akun Baru</button>

                    <div class="text-center">
                        <small>Sudah punya akun? <a href="{{ route('login') }}" class="text-success fw-bold">Login di sini</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
