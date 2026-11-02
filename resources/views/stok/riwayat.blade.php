@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Riwayat Mutasi Transaksi Stok Benih</h3>
    <a href="{{ route('stok.index') }}" class="btn btn-secondary">Kembali ke Stok</a>
</div>

<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Provinsi</th>
                    <th>Benih</th>
                    <th>Jenis</th>
                    <th>Jumlah (Kg)</th>
                    <th>Stok Sebelum</th>
                    <th>Stok Sesudah</th>
                    <th>Sumber / Tujuan</th>
                    <th>Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mutasis as $m)
                <tr>
                    <td>{{ $m->tanggal_transaksi }}</td>
                    <td>{{ $m->stokBenih->provinsi->nama_provinsi ?? '-' }}</td>
                    <td>{{ $m->stokBenih->benih->nama_benih ?? '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $m->jenis_transaksi === 'masuk' ? 'success' : 'danger' }}">
                            {{ strtoupper($m->jenis_transaksi) }}
                        </span>
                    </td>
                    <td><strong>{{ number_format($m->jumlah_kg, 2) }} Kg</strong></td>
                    <td>{{ number_format($m->stok_sebelum, 2) }}</td>
                    <td>{{ number_format($m->stok_sesudah, 2) }}</td>
                    <td>{{ $m->sumber_tujuan }}</td>
                    <td>{{ $m->user->name ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-3">Belum ada riwayat mutasi stok.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $mutasis->links() }}</div>
@endsection