@extends('layouts.app')

@section('title', 'Persetujuan Koreksi Data')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Persetujuan Koreksi Data</h4>
        <p class="text-muted small mb-0">Verifikasi dan persetujuan permohonan pembatalan/koreksi data dari Petugas Provinsi.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Pemohon</th>
                            <th>Modul</th>
                            <th>Aksi Pengajuan</th>
                            <th>Alasan Koreksi</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Keputusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvals as $a)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $a->user->name ?? 'Petugas' }}</div>
                                <small class="text-muted">{{ $a->created_at->format('d M Y H:i') }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $a->jenis_transaksi }}</span></td>
                            <td>
                                <span class="badge {{ $a->tipe_pengajuan == 'Hapus' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }} border">
                                    {{ $a->tipe_pengajuan }}
                                </span>
                            </td>
                            <td style="max-width: 250px;" class="text-truncate">{{ $a->alasan }}</td>
                            <td>
                                @if($a->status == 'pending')
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                @elseif($a->status == 'approved')
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-danger">Ditolak</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($a->status == 'pending')
                                    <form action="{{ route('approval.approve', $a->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success me-1"><i class="bi bi-check-lg"></i> Setujui</button>
                                    </form>
                                    <form action="{{ route('approval.reject', $a->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-lg"></i> Tolak</button>
                                    </form>
                                @else
                                    <small class="text-muted">Proses Selesai</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada pengajuan koreksi data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($approvals->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $approvals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection