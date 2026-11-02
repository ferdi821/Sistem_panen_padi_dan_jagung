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
                </tr>
            </thead>
            <tbody>
                @forelse($stokList as $item)
                <tr>
                    <td>{{ $item->provinsi->nama_provinsi }}</td>
                    <td><code>{{ $item->benih->kode_benih }}</code></td>
                    <td>{{ $item->benih->nama_benih }}</td>
                    <td><span class="badge bg-{{ $item->benih->komoditas === 'padi' ? 'success' : 'warning' }}">{{ strtoupper($item->benih->komoditas) }}</span></td>
                    <td>{{ $item->benih->varietas }}</td>
                    <td>
                        @if($item->jumlah_stok_kg <= $item->benih->stok_minimum)
                            <span class="badge bg-danger fs-6">{{ number_format($item->jumlah_stok_kg, 2) }} Kg (MENIPIS)</span>
                        @else
                            <span class="badge bg-primary fs-6">{{ number_format($item->jumlah_stok_kg, 2) }} Kg</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-3">Belum ada persediaan stok benih.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $stokList->links() }}</div>
@endsection