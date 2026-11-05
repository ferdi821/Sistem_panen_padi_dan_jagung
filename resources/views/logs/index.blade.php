@extends('layouts.app')

@section('title', 'Log Aktivitas Sistem')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Log Aktivitas & Audit Trail</h4>
        <p class="text-muted small mb-0">Catatan riwayat aktivitas transaksi dan penggunaan sistem secara real-time.</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Waktu</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Deskripsi Aktivitas</th>
                            <th class="pe-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="fw-bold">{{ $log->user->name ?? 'System' }}</td>
                            <td>
                                <span class="badge {{ $log->action == 'DELETE' ? 'bg-danger' : ($log->action == 'CREATE' ? 'bg-success' : 'bg-info') }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td>{{ $log->description }}</td>
                            <td class="pe-4 font-monospace small text-muted">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada log aktivitas tercatat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection