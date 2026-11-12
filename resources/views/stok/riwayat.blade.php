@extends('layouts.app')

@section('title', 'Riwayat Mutasi Stok Benih - SIPANEN')

@section('content')
<style>
    #main-wrapper {
        min-height: auto !important;
    }
    main.flex-grow-1 {
        flex-grow: 0 !important;
    }
    .pagination .page-link {
        color: #106742;
        font-size: 13px;
    }
    .pagination .page-item.active .page-link {
        background-color: #106742;
        border-color: #106742;
        color: #fff;
    }
</style>

<div class="container-fluid px-4 py-3">

    <!-- HEADER HALAMAN & FILTER TANGGAL RINGKAS -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #106742;">Riwayat Mutasi Stok Benih</h3>
            <p class="text-muted small mb-0">Catatan log transaksi distribusi stok benih masuk dan keluar seluruh wilayah.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('stok.riwayat') }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="date" name="tanggal" class="form-control form-control-sm shadow-sm" value="{{ $tanggal ?? '' }}" style="width: 150px; height: 36px;">
                <button type="submit" class="btn btn-success shadow-sm px-3" style="background-color: #106742; border: none; font-size: 13px; font-weight: 600; height: 36px;">
                    Cari
                </button>
                @if(!empty($tanggal))
                    <a href="{{ route('stok.riwayat') }}" class="btn btn-outline-secondary shadow-sm px-3" style="font-size: 13px; font-weight: 600; height: 36px; line-height: 22px;">
                        Reset
                    </a>
                @endif
            </form>

            <a href="{{ route('stok.index') }}" class="btn btn-secondary shadow-sm px-3" style="font-size: 13px; font-weight: 600; height: 36px; line-height: 22px;">
                Kembali ke Stok Wilayah
            </a>
        </div>
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

    <!-- TABEL RIWAYAT MUTASI -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0" style="font-size: 13px;">
                    <thead style="background-color: #f3f7f5; color: #315b49;">
                        <tr>
                            <th class="py-3">Tanggal</th>
                            <th class="text-start ps-3 py-3">Provinsi</th>
                            <th class="text-start py-3">Varietas Benih</th>
                            <th class="py-3">Jenis Transaksi</th>
                            <th class="text-start py-3">Sumber / Tujuan</th>
                            <th class="py-3">Jumlah (Ton)</th>
                            <th class="text-start py-3">Petugas</th>
                            @role('admin_pusat')
                                <th class="py-3">Aksi</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mutasis as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('Y-m-d') }}</td>
                                <td class="text-start ps-3 fw-bold" style="color: #273b32;">
                                    {{ $item->stokBenih->provinsi->nama_provinsi ?? '-' }}
                                </td>
                                <td class="text-start">
                                    {{ $item->stokBenih->benih->nama_benih ?? '-' }} ({{ $item->stokBenih->benih->varietas ?? '-' }})
                                </td>
                                <td>
                                    @if($item->jenis_transaksi === 'masuk')
                                        <span class="badge px-2 py-1" style="background-color: #106742; color: #fff; font-size: 10px; font-weight: 700;">STOK MASUK</span>
                                    @else
                                        <span class="badge px-2 py-1 bg-danger text-white font-weight-bold" style="font-size: 10px; font-weight: 700;">STOK KELUAR</span>
                                    @endif
                                </td>
                                <td class="text-start">{{ $item->sumber_tujuan }}</td>
                                <td class="fw-bold {{ $item->jenis_transaksi === 'masuk' ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($item->jumlah_kg, 2) }} Ton
                                </td>
                                <td class="text-start">{{ $item->user->name ?? 'Sistem' }}</td>
                                @role('admin_pusat')
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary px-2 py-1 me-1" data-bs-toggle="modal" data-bs-target="#modalEditRiwayat{{ $item->id }}" style="background-color: #2563eb; border: none; font-size: 11px; font-weight: 600;">
                                            Edit
                                        </button>

                                        <form action="{{ route('stok.riwayat.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus riwayat transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger px-2 py-1" style="font-size: 11px; font-weight: 600;">Hapus</button>
                                        </form>
                                    </td>
                                @endrole
                            </tr>

                            @role('admin_pusat')
                                <!-- MODAL EDIT RIWAYAT MUTASI -->
                                <div class="modal fade" id="modalEditRiwayat{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                                            <div class="modal-header text-white px-4 py-3" style="background-color: #2563eb;">
                                                <h5 class="modal-title fw-bold fs-6">Edit Riwayat Mutasi Stok</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('stok.riwayat.update', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4 text-start">
                                                    <div class="p-3 mb-3 bg-light rounded" style="border: 1px solid #e2e8f0; font-size: 12px;">
                                                        <div><strong>Provinsi:</strong> {{ $item->stokBenih->provinsi->nama_provinsi ?? '-' }}</div>
                                                        <div><strong>Benih:</strong> {{ $item->stokBenih->benih->nama_benih ?? '-' }} ({{ $item->stokBenih->benih->varietas ?? '-' }})</div>
                                                        <div class="mt-1">
                                                            <strong>Jenis Transaksi:</strong> 
                                                            @if($item->jenis_transaksi === 'masuk')
                                                                <span class="badge bg-success" style="font-size: 9px;">STOK MASUK</span>
                                                            @else
                                                                <span class="badge bg-danger" style="font-size: 9px;">STOK KELUAR</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small" style="color: #34483e;">Tanggal Transaksi</label>
                                                        <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" value="{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('Y-m-d') }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small" style="color: #34483e;">Jumlah (Ton)</label>
                                                        <input type="number" step="0.01" name="jumlah_kg" class="form-control form-control-sm" value="{{ $item->jumlah_kg }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small" style="color: #34483e;">Sumber / Tujuan</label>
                                                        <input type="text" name="sumber_tujuan" class="form-control form-control-sm" value="{{ $item->sumber_tujuan }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small" style="color: #34483e;">Keterangan</label>
                                                        <textarea name="keterangan" class="form-control form-control-sm" rows="2">{{ $item->keterangan }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-bold" style="background-color: #2563eb; border: none;">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endrole
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 text-muted">
                                    {{ !empty($tanggal) ? 'Tidak ada riwayat mutasi pada tanggal tersebut.' : 'Belum ada riwayat mutasi stok benih minggu ini.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FOOTER PAGINATION (MAKSIMAL 10 DATA PER HALAMAN) -->
        @if ($mutasis->hasPages())
            <div class="card-footer bg-white border-top py-2 px-3 d-flex justify-content-end">
                {{ $mutasis->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection