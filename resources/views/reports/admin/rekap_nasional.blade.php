@extends('layouts.app')

@section('title', 'Rekapitulasi Stok Benih Nasional - SIPANEN')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- HEADER & TOMBOL EXPORT -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #106742;">Rekapitulasi Stok Benih Nasional (Lingkup BBPSIP)</h3>
            <p class="text-muted small mb-0">Laporan rincian stok benih 38 Provinsi sesuai standar format Excel BBPSIP.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.report.rekap.excel', request()->query()) }}" class="btn btn-success shadow-sm px-3 py-2" style="background-color: #106742; border: none; font-size: 13px; font-weight: 600;">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel Nasional
            </a>
            <a href="{{ route('admin.report.rekap.pdf', request()->query()) }}" class="btn btn-danger shadow-sm px-3 py-2" style="font-size: 13px; font-weight: 600;">
                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF Nasional
            </a>
        </div>
    </div>

    <!-- FILTER TANGGAL -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
        <div class="card-body py-3 px-4">
            <form action="{{ route('admin.report.rekap') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                        <input type="date" name="start_date" class="form-control border-start-0" value="{{ request('start_date', $start->format('Y-m-d')) }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success btn-sm w-100 fw-bold" style="background-color: #106742; border: none; padding: 6px;">
                        <i class="bi bi-funnel me-1"></i> Filter Laporan
                    </button>
                </div>
                <div class="col-md-5 text-md-end text-muted small">
                    <span class="badge bg-light text-dark border px-3 py-2">
                        <i class="bi bi-clock me-1"></i> Periode Aktif: <strong>{{ $start->format('d M Y') }}</strong> s.d <strong>{{ $end->format('d M Y') }}</strong>
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL REKAPITULASI -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 72vh; overflow-x: auto; overflow-y: auto;">
                <table class="table table-bordered align-middle text-center mb-0" style="font-size: 11px; min-width: 2450px; border-color: #cbd5e1;">
                    <thead style="position: sticky; top: 0; z-index: 10;">
                        <tr>
                            <th rowspan="3" style="background-color: #f1f5f9; width: 45px;">No</th>
                            <th rowspan="3" style="background-color: #f1f5f9; width: 170px;">Satker</th>
                            <th colspan="4" style="background-color: #dbeafe; border-right: 2px solid #0f172a;">Target (Ton)</th>
                            <th rowspan="3" style="background-color: #64748b; color: #fff; width: 70px; border-right: 2px solid #0f172a;">Total</th>
                            <th rowspan="3" style="background-color: #facc15; width: 70px; border-right: 2px solid #0f172a;">Kontrol</th>
                            <th colspan="4" style="background-color: #d1fae5;">Sudah Terdistribusi</th>
                            <th colspan="4" style="background-color: #e0f2fe;">Benih Tersedia (Tersertifikasi)</th>
                            <th colspan="4" style="background-color: #ede9fe;">Benih (Proses Sertifikasi)</th>
                            <th colspan="4" style="background-color: #fef3c7;">Benih (Prosesing)</th>
                            <th colspan="6" style="background-color: #ffedd5;">Benih (Persiapan Panen)</th>
                        </tr>
                        <tr style="background-color: #f8fafc;">
                            <th colspan="2">FS</th>
                            <th colspan="2" style="border-right: 2px solid #0f172a;">SS</th>
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <th colspan="2">FS</th><th colspan="2">SS</th>
                            <th colspan="3">FS</th><th colspan="3">SS</th>
                        </tr>
                        <tr style="background-color: #ffffff; font-size: 10px;">
                            <th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th style="border-right: 2px solid #0f172a;">Varietas</th>

                            <th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th>

                            <th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th>

                            <th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th>

                            <th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th>

                            <th>Volume (Ton)</th><th>Varietas</th><th>Tanggal Panen</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Tanggal Panen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapData as $index => $satkerData)
                            @foreach($satkerData['rows'] as $rIdx => $row)
                            <tr>
                                @if($rIdx === 0)
                                    <td rowspan="{{ $satkerData['rowspan'] }}" class="fw-bold bg-white">{{ $index + 1 }}</td>
                                    <td rowspan="{{ $satkerData['rowspan'] }}" class="text-start fw-bold bg-white ps-2">{{ $satkerData['satker'] }}</td>
                                @endif

                                <td class="fw-bold">{{ number_format((float)$row['target_fs_vol'], 2) }}</td>
                                <td class="text-start">{{ $row['target_fs_var'] }}</td>
                                <td class="fw-bold">{{ number_format((float)$row['target_ss_vol'], 2) }}</td>
                                <td class="text-start" style="border-right: 2px solid #0f172a;">{{ $row['target_ss_var'] }}</td>

                                @if($rIdx === 0)
                                    <td rowspan="{{ $satkerData['rowspan'] }}" class="fw-bold bg-light" style="border-right: 2px solid #0f172a;">{{ number_format((float)$satkerData['total'], 2) }}</td>
                                    <td rowspan="{{ $satkerData['rowspan'] }}" class="fw-bold text-danger" style="background-color: #fefce8; border-right: 2px solid #0f172a;">{{ number_format((float)$satkerData['kontrol'], 2) }}</td>
                                @endif

                                <td class="text-success fw-bold">{{ number_format((float)$row['distribusi_fs_vol'], 2) }}</td>
                                <td class="text-start">{{ $row['distribusi_fs_var'] }}</td>
                                <td class="text-success fw-bold">{{ number_format((float)$row['distribusi_ss_vol'], 2) }}</td>
                                <td class="text-start">{{ $row['distribusi_ss_var'] }}</td>

                                <td class="text-primary fw-bold">{{ number_format((float)$row['tersedia_fs_vol'], 2) }}</td>
                                <td class="text-start">{{ $row['tersedia_fs_var'] }}</td>
                                <td class="text-primary fw-bold">{{ number_format((float)$row['tersedia_ss_vol'], 2) }}</td>
                                <td class="text-start">{{ $row['tersedia_ss_var'] }}</td>

                                <td class="fw-bold" style="color: #6d28d9;">{{ number_format((float)($row['sertifikasi_fs_vol'] ?? 0), 2) }}</td>
                                <td class="text-start">{{ $row['sertifikasi_fs_var'] ?? '-' }}</td>
                                <td class="fw-bold" style="color: #6d28d9;">{{ number_format((float)($row['sertifikasi_ss_vol'] ?? 0), 2) }}</td>
                                <td class="text-start">{{ $row['sertifikasi_ss_var'] ?? '-' }}</td>

                                <td class="text-warning fw-bold">{{ number_format((float)($row['prosesing_fs_vol'] ?? 0), 2) }}</td>
                                <td class="text-start">{{ $row['prosesing_fs_var'] ?? '-' }}</td>
                                <td class="text-warning fw-bold">{{ number_format((float)($row['prosesing_ss_vol'] ?? 0), 2) }}</td>
                                <td class="text-start">{{ $row['prosesing_ss_var'] ?? '-' }}</td>

                                <td>{{ number_format((float)$row['persiapan_fs_vol'], 2) }}</td>
                                <td class="text-start">{{ $row['persiapan_fs_var'] }}</td>
                                <td>{{ $row['persiapan_fs_tgl'] }}</td>

                                <td>{{ number_format((float)$row['persiapan_ss_vol'], 2) }}</td>
                                <td class="text-start">{{ $row['persiapan_ss_var'] }}</td>
                                <td>{{ $row['persiapan_ss_tgl'] }}</td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="32" class="py-4 text-muted">Belum ada data stok benih.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection