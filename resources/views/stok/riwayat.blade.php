@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Riwayat Mutasi Stok Benih</h3>
    <a href="{{ route('stok.index') }}" class="btn btn-secondary">Kembali ke Stok Wilayah</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Provinsi</th>
                        <th>Varietas Benih</th>
                        <th>Jenis Transaksi</th>
                        <th>Sumber / Tujuan</th>
                        <th>Jumlah (Kg)</th>
                        <th>Petugas</th>
                        @hasrole('admin_pusat')
                        <th class="text-center">Aksi</th>
                        @endhasrole
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutasis as $item)
                    <tr>
                        <td>{{ $item->tanggal_transaksi }}</td>
                        <td>{{ $item->stokBenih->provinsi->nama_provinsi ?? '-' }}</td>
                        <td>{{ $item->stokBenih->benih->nama_benih ?? '-' }} ({{ $item->stokBenih->benih->varietas ?? '-' }})</td>
                        <td>
                            @if($item->jenis_transaksi == 'masuk')
                                <span class="badge bg-success">STOK MASUK</span>
                            @else
                                <span class="badge bg-danger">STOK KELUAR</span>
                            @endif
                        </td>
                        <td>{{ $item->sumber_tujuan }}</td>
                        <td class="fw-bold">{{ number_format($item->jumlah_kg, 2) }} Kg</td>
                        <td>{{ $item->user->name ?? 'Sistem' }}</td>
                        @hasrole('admin_pusat')
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('stok.riwayat.edit', $item->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('stok.riwayat.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat mutasi ini? Saldo stok gudang akan otomatis dikembalikan/disesuaikan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                        @endhasrole
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->hasRole('admin_pusat') ? 8 : 7 }}" class="text-center py-3">Belum ada riwayat mutasi stok.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $mutasis->links() }}</div>
@endsection