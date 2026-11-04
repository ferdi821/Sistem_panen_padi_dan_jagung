@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Persediaan Stok Benih Wilayah</h3>
    <div>
        @hasanyrole('admin_pusat|petugas_provinsi')
        <a href="{{ route('stok.keluar.create') }}" class="btn btn-danger me-2">- Input Stok Keluar</a>
        @endhasanyrole
        <a href="{{ route('stok.riwayat') }}" class="btn btn-info text-white"><i class="bi bi-clock-history"></i> Riwayat Mutasi</a>
    </div>
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
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Provinsi</th>
                    <th>Kode Benih</th>
                    <th>Nama Benih</th>
                    <th>Komoditas</th>
                    <th>Varietas</th>
                    <th>Jumlah Stok Tersedia</th>
                    @hasanyrole('admin_pusat|petugas_provinsi')
                    <th class="text-center">Aksi</th>
                    @endhasanyrole
                </tr>
            </thead>
            <tbody>
                @forelse($stokList as $item)
                <tr>
                    <td>{{ $item->provinsi->nama_provinsi ?? '-' }}</td>
                    <td><code>{{ $item->benih->kode_benih ?? '-' }}</code></td>
                    <td>{{ $item->benih->nama_benih ?? '-' }}</td>
                    <td><span class="badge bg-{{ ($item->benih->komoditas ?? 'padi') === 'padi' ? 'success' : 'warning' }}">{{ strtoupper($item->benih->komoditas ?? 'PADI') }}</span></td>
                    <td>{{ $item->benih->varietas ?? '-' }}</td>
                    <td>
                        @if($item->jumlah_stok_kg <= ($item->benih->stok_minimum ?? 0))
                            <span class="badge bg-danger fs-6">{{ number_format($item->jumlah_stok_kg, 2) }} Kg (MENIPIS)</span>
                        @else
                            <span class="badge bg-primary fs-6">{{ number_format($item->jumlah_stok_kg, 2) }} Kg</span>
                        @endif
                    </td>
                    @hasanyrole('admin_pusat|petugas_provinsi')
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('stok.edit', $item->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('stok.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data stok ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                    @endhasanyrole
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->hasAnyRole(['admin_pusat', 'petugas_provinsi']) ? 7 : 6 }}" class="text-center py-3">Belum ada persediaan stok benih.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $stokList->links() }}</div>
@endsection