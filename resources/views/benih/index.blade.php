@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Master Data Benih Padi & Jagung</h3>
    <a href="{{ route('benih.create') }}" class="btn btn-success">+ Tambah Benih Baru</a>
</div>

<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Benih</th>
                    <th>Komoditas</th>
                    <th>Varietas</th>
                    <th>Kelas</th>
                    <th>Stok Min (Kg)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($benihs as $item)
                <tr>
                    <td><code>{{ $item->kode_benih }}</code></td>
                    <td>{{ $item->nama_benih }}</td>
                    <td><span class="badge bg-{{ $item->komoditas === 'padi' ? 'success' : 'warning' }}">{{ strtoupper($item->komoditas) }}</span></td>
                    <td>{{ $item->varietas }}</td>
                    <td><span class="badge bg-secondary">{{ $item->kelas_benih }}</span></td>
                    <td>{{ number_format($item->stok_minimum, 2) }}</td>
                    <td>
                        <a href="{{ route('benih.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('benih.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus data benih ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-3">Belum ada master data benih.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $benihs->links() }}</div>
@endsection