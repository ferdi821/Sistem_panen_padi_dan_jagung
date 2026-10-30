@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Matriks Role & Permissions</h3>
    <a href="{{ route('roles.create') }}" class="btn btn-primary">+ Tambah Role Baru</a>
</div>

<div class="row">
    @foreach($roles as $role)
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ strtoupper($role->name) }}</h5>
                <div>
                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-light">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <h6>Hak Akses (Permissions):</h6>
                @forelse($role->permissions as $permission)
                    <span class="badge bg-secondary mb-1">{{ $permission->name }}</span>
                @empty
                    <small class="text-muted">Tidak ada permission khusus.</small>
                @endforelse
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
