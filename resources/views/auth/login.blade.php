@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center">
                <h4 class="mb-0">Login Sistem Panen</h4>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 mb-3">Login</button>
                </form>
                <div class="text-center">
                    <small>Belum memiliki akun? <a href="{{ route('register') }}" class="text-success fw-bold">Daftar Akun Petugas</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
