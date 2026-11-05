@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Pengaturan Parameter Sistem</h4>
        <p class="text-muted small mb-0">Konfigurasi parameter global operasional SIPANEN Nasional.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @foreach($settings as $setting)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ $setting->label }}</label>
                            <input type="{{ $setting->type }}" 
                                   name="{{ $setting->key }}" 
                                   value="{{ $setting->value }}" 
                                   class="form-control" required>
                        </div>
                        @endforeach
                        <hr class="my-4">
                        <button type="submit" class="btn btn-success text-white">
                            <i class="bi bi-save me-1"></i> Simpan Pengaturan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection