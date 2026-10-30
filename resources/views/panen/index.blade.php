@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Daftar Catatan Panen</h3>
    @if(in_array(auth()->user()->role, ['admin_pusat', 'petugas_provinsi']))
        <a href="{{ route('panen.create') }}" class="btn btn-success">+ Input Data Panen</a>
    @endif
</div>

<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Provinsi</th>
                    <th>Komoditas</th>
                    <th>Luas Lahan (Ha)</th>
                    <th>Hasil Panen (Ton)</th>
                    <th>Petugas Input</th>
                    @if(in_array(auth()->user()->role, ['admin_pusat', 'petugas_provinsi']))
                        <th>Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($panenList as $item)
                <tr>
                    <td>{{ $item->tanggal_panen }}</td>
                    <td>{{ $item->provinsi->nama_provinsi }}</td>
                    <td><span class="badge bg-{{ $item->komoditas === 'padi' ? 'success' : 'warning' }}">{{ strtoupper($item->komoditas) }}</span></td>
                    <td>{{ number_format($item->luas_lahan_hektar, 2) }}</td>
                    <td>{{ number_format($item->jumlah_panen_ton, 2) }}</td>
                    <td>{{ $item->user->name }}</td>
                    @if(in_array(auth()->user()->role, ['admin_pusat', 'petugas_provinsi']))
                    <td>
                        <form action="{{ route('panen.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-3">Belum ada data catatan panen.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">
    {{ $panenList->links() }}
</div>
@endsection
