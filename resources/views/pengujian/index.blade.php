@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Riwayat Pengujian Mutu (Konversi Panen -> Benih)</h3>
    
    {{-- Tombol hanya muncul untuk Admin Pusat dan Petugas Provinsi --}}
    @hasanyrole('admin_pusat|petugas_provinsi')
    <a href="{{ route('pengujian.create') }}" class="btn btn-success">+ Input Pengujian Mutu</a>
    @endhasanyrole
</div>

<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tgl Uji</th>
                    <th>Provinsi</th>
                    <th>ID Panen</th>
                    <th>Target Benih</th>
                    <th>Kadar Air</th>
                    <th>Daya Tumbuh</th>
                    <th>Hasil Lolos (Kg)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengujians as $item)
                <tr>
                    <td>{{ $item->tanggal_uji }}</td>
                    <td>{{ $item->catatanPanen->provinsi->nama_provinsi ?? '-' }}</td>
                    <td>Panen #{{ $item->catatan_panen_id }}</td>
                    <td>{{ $item->benih->nama_benih ?? '-' }}</td>
                    <td>{{ $item->kadar_air_persen }}%</td>
                    <td>{{ $item->daya_tumbuh_persen }}%</td>
                    <td><strong>{{ number_format($item->jumlah_lolos_kg, 2) }} Kg</strong></td>
                    <td>
                        @if($item->status_kelayakan === 'layak')
                            <span class="badge bg-success">LAYAK (STOK MASUK)</span>
                        @elseif($item->status_kelayakan === 'tidak_layak')
                            <span class="badge bg-danger">TIDAK LAYAK</span>
                        @else
                            <span class="badge bg-warning text-dark">PROSES</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-3">Belum ada riwayat pengujian mutu.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $pengujians->links() }}</div>
@endsection