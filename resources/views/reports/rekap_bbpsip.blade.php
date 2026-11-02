@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Rekapitulasi Pipeline Stok Benih Lingkup BBPSIP</h4>
        <p class="text-muted mb-0">Matriks monitoring 5 tahapan ketersediaan benih tingkat provinsi.</p>
    </div>
    <a href="{{ route('report.rekap.pdf') }}" class="btn btn-danger shadow-sm">
        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF Laporan
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-center">
                <thead class="table-success align-middle">
                    <tr>
                        <th rowspan="2" width="5%">No</th>
                        <th rowspan="2">Satker / Provinsi</th>
                        <th colspan="2">Tahap Pengolahan Awal</th>
                        <th>Tahap Laboratorium</th>
                        <th>Tahap Stok</th>
                        <th>Tahap Output</th>
                    </tr>
                    <tr>
                        <th>1. Persiapan Panen (Ton)</th>
                        <th>2. Prosesing (Ton)</th>
                        <th>3. Sertifikasi (Ton)</th>
                        <th>4. Tersedia / Layak (Ton)</th>
                        <th>5. Terdistribusi (Ton)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapData as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start fw-bold">{{ $row['satker'] }}</td>
                        <td>{{ number_format($row['persiapan_panen_ton'], 2) }}</td>
                        <td>{{ number_format($row['prosesing_ton'], 2) }}</td>
                        <td>{{ number_format($row['proses_sertifikasi_ton'], 2) }}</td>
                        <td class="table-primary text-primary fw-bold">{{ number_format($row['tersedia_ton'], 2) }}</td>
                        <td class="table-warning text-dark fw-bold">{{ number_format($row['terdistribusi_ton'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-4 text-muted">Belum ada data rekapitulasi provinsi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection