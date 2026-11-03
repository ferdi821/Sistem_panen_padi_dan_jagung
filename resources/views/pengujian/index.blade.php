@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Riwayat Pengujian Mutu (Konversi Panen -> Benih)</h3>
        
        @hasanyrole('admin_pusat|petugas_provinsi')
        <a href="{{ route('pengujian.create') }}" class="btn btn-success">+ Input Pengujian Mutu</a>
        @endhasanyrole
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-3">Tgl Uji</th>
                            <th class="py-3 px-3">Provinsi</th>
                            <th class="py-3 px-3">ID Panen</th>
                            <th class="py-3 px-3">Target Benih</th>
                            <th class="py-3 px-3">Kadar Air</th>
                            <th class="py-3 px-3">Daya Tumbuh</th>
                            <th class="py-3 px-3">Hasil Lolos (Kg)</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengujianMutus as $item)
                            <tr>
                                <td class="py-3 px-3">{{ $item->tanggal_uji ?? $item->created_at->format('Y-m-d') }}</td>
                                <td class="py-3 px-3">{{ $item->catatanPanen->provinsi->nama_provinsi ?? '-' }}</td>
                                <td class="py-3 px-3">Panen #{{ $item->catatan_panen_id }}</td>
                                <td class="py-3 px-3">{{ $item->benih->nama_benih ?? '-' }}</td>
                                <td class="py-3 px-3">{{ number_format($item->kadar_air_persen ?? 0, 2) }}%</td>
                                <td class="py-3 px-3">{{ number_format($item->daya_tumbuh_persen ?? 0, 2) }}%</td>
                                
                                <!-- Menampilkan jumlah lolos kg dari database -->
                                <td class="py-3 px-3 fw-bold">{{ number_format($item->jumlah_lolos_kg ?? 0, 2) }} Kg</td>
                                
                                <td class="py-3 px-3">
                                    @if(str_contains(strtolower($item->status_kelayakan), 'tidak'))
                                        <span class="badge bg-danger">TIDAK LAYAK</span>
                                    @elseif(str_contains(strtolower($item->status_kelayakan), 'layak'))
                                        <span class="badge bg-success">LAYAK (STOK MASUK)</span>
                                    @else
                                        <span class="badge bg-warning text-dark">PROSES</span>
                                    @endif
                                </td>
                                
                                <td class="py-3 px-3 text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('pengujian.edit', $item->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        
                                        <form action="{{ route('pengujian.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Belum ada data pengujian mutu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection