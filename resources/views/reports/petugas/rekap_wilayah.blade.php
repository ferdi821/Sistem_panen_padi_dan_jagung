@extends('layouts.app')

@section('title', 'Rekapitulasi Stok Benih Wilayah - Excel BBPSIP')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #106742;">Rekapitulasi Stok Benih - {{ $namaProvinsi }}</h4>
            <p class="text-muted mb-0 small">Monitoring rincian stok per Kabupaten/Kota & Varietas spesifik format Excel BBPSIP.</p>
        </div>
        <div>
            <a href="{{ route('petugas.report.rekap.pdf', request()->all()) }}" target="_blank" class="btn btn-danger btn-sm shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF Wilayah
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 10px;">
        <div class="card-body p-2">
            <form action="{{ route('petugas.report.rekap') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="hari_ini" {{ $periode == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="minggu_ini" {{ $periode == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="bulan_ini" {{ $periode == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="tahun_ini" {{ $periode == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                    </select>
                </div>
                <div class="col-auto">
                    <span class="badge bg-light text-dark border">
                        Periode: {{ $start->format('d M Y') }} s.d {{ $end->format('d M Y') }}
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Matriks Per Kabupaten Format Excel BBPSIP -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 70vh;">
                <table class="table table-bordered table-hover align-middle mb-0 text-center" style="font-size: 0.76rem; border-color: #dee2e6;">
                    <thead class="align-middle text-nowrap" style="background-color: #f8f9fa;">
                        <tr class="fw-bold">
                            <th rowspan="3" width="30px" class="bg-light">No</th>
                            <th rowspan="3" width="180px" class="bg-light text-start">Kabupaten / Kota</th>
                            <th colspan="4" class="table-primary">Target (Ton)</th>
                            <th rowspan="3" width="70px" class="bg-secondary text-white">Total</th>
                            <th rowspan="3" width="70px" class="bg-warning text-dark">Kontrol</th>
                            <th colspan="4" class="table-success">Sudah Terdistribusi</th>
                            <th colspan="4" class="table-info">Benih Tersedia</th>
                            <th colspan="4" class="table-light">Benih (Prosesing)</th>
                            <th colspan="6" class="table-warning">Benih (Persiapan Panen)</th>
                        </tr>
                        <tr class="fw-bold">
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <th colspan="3">FS</th><th colspan="3">SS</th>
                        </tr>
                        <tr class="fw-bold text-muted small">
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Tanggal Panen</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Tanggal Panen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapData as $index => $kabData)
                            @foreach($kabData['rows'] as $rIdx => $row)
                            <tr>
                                @if($rIdx === 0)
                                    <td rowspan="{{ $kabData['rowspan'] }}" class="align-middle bg-white">{{ $index + 1 }}</td>
                                    <td rowspan="{{ $kabData['rowspan'] }}" class="text-start fw-bold text-dark text-nowrap align-middle bg-white">{{ $kabData['satker'] }}</td>
                                @endif

                                <td class="fw-bold">{{ number_format((float)$row['target_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['target_fs_var'] }}</td>
                                <td class="fw-bold">{{ number_format((float)$row['target_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['target_ss_var'] }}</td>

                                @if($rIdx === 0)
                                    <td rowspan="{{ $kabData['rowspan'] }}" class="bg-secondary bg-opacity-10 fw-bold align-middle">{{ number_format((float)$kabData['total'], 2) }}</td>
                                    <td rowspan="{{ $kabData['rowspan'] }}" class="bg-warning bg-opacity-10 fw-bold text-danger align-middle">{{ number_format((float)$kabData['kontrol'], 2) }}</td>
                                @endif

                                <td class="fw-bold text-success">{{ number_format((float)$row['distribusi_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['distribusi_fs_var'] }}</td>
                                <td class="fw-bold text-success">{{ number_format((float)$row['distribusi_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['distribusi_ss_var'] }}</td>

                                <td class="fw-bold text-primary">{{ number_format((float)$row['tersedia_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['tersedia_fs_var'] }}</td>
                                <td class="fw-bold text-primary">{{ number_format((float)$row['tersedia_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['tersedia_ss_var'] }}</td>

                                <td>{{ number_format((float)$row['prosesing_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['prosesing_fs_var'] }}</td>
                                <td>{{ number_format((float)$row['prosesing_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['prosesing_ss_var'] }}</td>

                                <td class="fw-bold">{{ number_format((float)$row['persiapan_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['persiapan_fs_var'] }}</td>
                                <td class="small text-muted">{{ $row['persiapan_fs_tgl'] }}</td>

                                <td class="fw-bold">{{ number_format((float)$row['persiapan_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['persiapan_ss_var'] }}</td>
                                <td class="small text-muted">{{ $row['persiapan_ss_tgl'] }}</td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="28" class="py-4 text-muted">Belum ada data rekapitulasi wilayah.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection