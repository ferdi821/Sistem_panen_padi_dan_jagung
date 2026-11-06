@extends('layouts.app')

@section('title', 'Rekapitulasi Stok Benih Nasional - Excel BBPSIP')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #106742;">Data Stok Benih Padi/Jagung Lingkup BBPSIP</h4>
            <p class="text-muted mb-0 small">Format detail breakdown per varietas presisi Excel BBPSIP (34 Provinsi).</p>
        </div>
        <div>
            <a href="{{ route('admin.report.rekap.pdf', request()->all()) }}" target="_blank" class="btn btn-danger btn-sm shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF Laporan
            </a>
        </div>
    </div>

    <!-- Filter Periode -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 10px;">
        <div class="card-body p-2">
            <form action="{{ route('admin.report.rekap') }}" method="GET" class="row g-2 align-items-center">
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

    <!-- Table Matriks Format Breakdown Rowspan Excel BBPSIP -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 75vh;">
                <table class="table table-bordered table-hover align-middle mb-0 text-center" style="font-size: 0.76rem; border-color: #dee2e6;">
                    <thead class="align-middle text-nowrap" style="background-color: #f8f9fa;">
                        <!-- Header Baris 1 -->
                        <tr class="fw-bold">
                            <th rowspan="3" width="30px" class="bg-light">No</th>
                            <th rowspan="3" width="200px" class="bg-light text-start">Satker</th>
                            <th colspan="4" class="table-primary">Target (Ton)</th>
                            <th rowspan="3" width="70px" class="bg-secondary text-white">Total</th>
                            <th rowspan="3" width="70px" class="bg-warning text-dark">Kontrol</th>
                            <th colspan="4" class="table-success">Sudah Terdistribusi</th>
                            <th colspan="4" class="table-info">Benih Tersedia</th>
                            <th colspan="4" class="table-light">Benih (Prosesing)</th>
                            <th colspan="6" class="table-warning">Benih (Persiapan Panen)</th>
                        </tr>
                        <!-- Header Baris 2 -->
                        <tr class="fw-bold">
                            <!-- Target -->
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <!-- Terdistribusi -->
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <!-- Tersedia -->
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <!-- Prosesing -->
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <!-- Persiapan Panen -->
                            <th colspan="3">FS</th><th colspan="3">SS</th>
                        </tr>
                        <!-- Header Baris 3 (Sub-Kolom) -->
                        <tr class="fw-bold text-muted small">
                            <!-- Target -->
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <!-- Terdistribusi -->
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <!-- Tersedia -->
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <!-- Prosesing -->
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <!-- Persiapan Panen -->
                            <th>Volume (Ton)</th><th>Varietas</th><th>Tanggal Panen</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Tanggal Panen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapData as $index => $satkerData)
                            @foreach($satkerData['rows'] as $rIdx => $row)
                            <tr>
                                @if($rIdx === 0)
                                    <td rowspan="{{ $satkerData['rowspan'] }}" class="align-middle bg-white">{{ $index + 1 }}</td>
                                    <td rowspan="{{ $satkerData['rowspan'] }}" class="text-start fw-bold text-dark text-nowrap align-middle bg-white">{{ $satkerData['satker'] }}</td>
                                @endif

                                <!-- Target -->
                                <td class="fw-bold">{{ number_format((float)$row['target_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['target_fs_var'] }}</td>
                                <td class="fw-bold">{{ number_format((float)$row['target_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['target_ss_var'] }}</td>

                                @if($rIdx === 0)
                                    <!-- Total & Kontrol (Merged per Satker) -->
                                    <td rowspan="{{ $satkerData['rowspan'] }}" class="bg-secondary bg-opacity-10 fw-bold align-middle">{{ number_format((float)$satkerData['total'], 2) }}</td>
                                    <td rowspan="{{ $satkerData['rowspan'] }}" class="bg-warning bg-opacity-10 fw-bold text-danger align-middle">{{ number_format((float)$satkerData['kontrol'], 2) }}</td>
                                @endif

                                <!-- Terdistribusi -->
                                <td class="fw-bold text-success">{{ number_format((float)$row['distribusi_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['distribusi_fs_var'] }}</td>
                                <td class="fw-bold text-success">{{ number_format((float)$row['distribusi_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['distribusi_ss_var'] }}</td>

                                <!-- Tersedia -->
                                <td class="fw-bold text-primary">{{ number_format((float)$row['tersedia_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['tersedia_fs_var'] }}</td>
                                <td class="fw-bold text-primary">{{ number_format((float)$row['tersedia_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['tersedia_ss_var'] }}</td>

                                <!-- Prosesing -->
                                <td>{{ number_format((float)$row['prosesing_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['prosesing_fs_var'] }}</td>
                                <td>{{ number_format((float)$row['prosesing_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['prosesing_ss_var'] }}</td>

                                <!-- Persiapan Panen -->
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
                            <td colspan="28" class="py-4 text-muted">Belum ada data stok benih.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection