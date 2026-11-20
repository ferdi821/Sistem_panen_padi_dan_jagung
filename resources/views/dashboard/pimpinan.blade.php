@extends('layouts.app')

@section('title', 'Executive Dashboard Pimpinan - SIPANEN')

@section('content')
<style>
    .chart-box-container {
        position: relative;
        width: 100%;
        height: 270px;
    }
    @media (min-width: 992px) {
        .chart-box-container {
            height: 300px;
        }
    }
</style>

<div class="container-fluid px-0 px-md-2">
    <!-- BANNER EKSEKUTIF PIMPINAN & FILTER WILAYAH -->
    <div class="card border-0 shadow-sm text-white mb-4" style="background: linear-gradient(135deg, #106742 0%, #093e27 100%); border-radius: 16px;">
        <div class="card-body p-3 p-sm-4">
            <div class="row align-items-center g-3">
                <div class="col-12 col-lg-7">
                    <span class="badge bg-warning text-dark fw-bold mb-2" style="font-size: 0.65rem;">EXECUTIVE MONITORING</span>
                    <h2 class="fw-bold mb-1 fs-4 fs-md-3">Ikhtisar Strategis Pangan & Stok Benih</h2>
                    <p class="mb-0 text-white-50 small">
                        Pemantauan capaian produksi panen, rasio produktivitas lahan, dan peringatan ketersediaan benih nasional.
                    </p>
                </div>

                <!-- FILTER PROVINSI & TOMBOL EKSPOR LAPORAN -->
                <div class="col-12 col-lg-5">
                    <form action="{{ route('dashboard') }}" method="GET" class="bg-white bg-opacity-10 p-2 rounded-3 border border-white border-opacity-25">
                        <label class="form-label text-white-50 fw-semibold mb-1" style="font-size: 0.68rem;">FILTER CAKUPAN WILAYAH:</label>
                        <div class="d-flex gap-2">
                            <select name="provinsi_id" class="form-select form-select-sm border-0 fw-semibold" onchange="this.form.submit()">
                                <option value="">Semua Provinsi (Nasional)</option>
                                @foreach($provinsis as $prov)
                                    <option value="{{ $prov->id }}" {{ ($selectedProvinsiId == $prov->id) ? 'selected' : '' }}>
                                        {{ $prov->nama_provinsi }}
                                    </option>
                                @endforeach
                            </select>
                            @if($selectedProvinsiId)
                                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light text-nowrap fw-semibold">Reset</a>
                            @endif
                        </div>
                        <div class="d-flex gap-2 mt-2 pt-2 border-top border-white border-opacity-10">
                            <a href="{{ route('admin.report.rekap') }}" class="btn btn-sm btn-light text-success fw-bold flex-grow-1" style="font-size: 0.72rem;">
                                <i class="bi bi-file-earmark-bar-graph me-1"></i>Laporan Rekap
                            </a>
                            <a href="{{ route('admin.report.rekap.pdf') }}" class="btn btn-sm btn-danger fw-semibold" style="font-size: 0.72rem;" title="Unduh PDF">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </a>
                            <a href="{{ route('admin.report.rekap.excel') }}" class="btn btn-sm btn-success border border-white border-opacity-50 fw-semibold" style="font-size: 0.72rem;" title="Unduh Excel">
                                <i class="bi bi-file-earmark-excel"></i> Excel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 4 KARTU INDIKATOR EKSEKUTIF -->
    <div class="row g-2 g-md-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-success text-white h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Produksi Padi</span>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-flower1 fs-5 text-white"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 fs-3">
                        {{ number_format($totalPadiTon ?? 0, 2) }}
                        <span class="fs-6 fw-normal text-white-50">Ton</span>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-warning text-dark h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-dark-50 fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Produksi Jagung</span>
                        <div class="bg-dark bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-sun fs-5 text-dark"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 fs-3">
                        {{ number_format($totalJagungTon ?? 0, 2) }}
                        <span class="fs-6 fw-normal text-dark-50">Ton</span>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.7rem;">Persediaan Benih Gudang</span>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-boxes fs-5 text-white"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 fs-3">
                        @php 
                            $stokDalamTon = isset($totalStokKg) ? ($totalStokKg > 100 ? $totalStokKg / 1000 : $totalStokKg) : 0;
                        @endphp
                        {{ number_format($stokDalamTon, 2) }}
                        <span class="fs-6 fw-normal text-white-50">Ton</span>
                    </h3>
                </div>
            </div>
        </div>

        <!-- KPI PRODUKTIVITAS RATA-RATA (TON/HA) -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm text-white h-100" style="background-color: #1e293b; border-radius: 14px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.7rem;">Rasio Produktivitas</span>
                        <div class="bg-white bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-graph-up-arrow fs-5 text-info"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 fs-3">
                        {{ number_format($produktivitasRataRata ?? 0, 2) }}
                        <span class="fs-6 fw-normal text-white-50">Ton/Ha</span>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- BARIS 1: GRAFIK TREN & KONTRIBUSI PROVINSI -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark small">
                        <i class="bi bi-bar-chart-line-fill me-2 text-success"></i>Tren Produksi Bulanan
                    </h6>
                    <span class="badge bg-light text-muted border">Tahun {{ date('Y') }}</span>
                </div>
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="chart-box-container">
                        <canvas id="pimpinanTrenChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERINGKAT KONTRIBUSI PRODUKSI PER PROVINSI -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark small">
                        <i class="bi bi-trophy-fill me-2 text-warning"></i>Kontribusi & Produktivitas per Provinsi
                    </h6>
                    <a href="{{ route('panen.index') }}" class="btn btn-sm btn-outline-success rounded-pill" style="font-size: 0.68rem; padding: 2px 10px;">
                        Detail Panen
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2">Provinsi</th>
                                    <th class="text-end py-2">Padi</th>
                                    <th class="text-end py-2">Jagung</th>
                                    <th class="text-end py-2">Total Panen</th>
                                    <th class="text-end pe-3 py-2">Ton/Ha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rekapProvinsi ?? [] as $row)
                                @php
                                    $prodProv = ($row->total_luas ?? 0) > 0 ? ($row->total_semua / $row->total_luas) : 0;
                                @endphp
                                <tr>
                                    <td class="ps-3 fw-bold text-dark py-2">{{ $row->provinsi->nama_provinsi ?? '-' }}</td>
                                    <td class="text-end text-success py-2">{{ number_format($row->total_padi, 2) }} Ton</td>
                                    <td class="text-end text-warning fw-semibold py-2">{{ number_format($row->total_jagung, 2) }} Ton</td>
                                    <td class="text-end fw-bold py-2">{{ number_format($row->total_semua, 2) }} Ton</td>
                                    <td class="text-end pe-3 py-2">
                                        <span class="badge bg-light text-primary border">{{ number_format($prodProv, 2) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada data kontribusi provinsi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BARIS 2: ANALISIS CAPAIAN REALISASI VS TARGET STOK BENIH -->
    <div id="analisis-capaian" class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bullseye text-success"></i>
                        <h6 class="fw-bold mb-0 text-dark small">Analisis Capaian Realisasi vs Target Stok Benih Wilayah</h6>
                    </div>
                    <span class="badge bg-light text-muted border">{{ count($evaluasiStok ?? []) }} Unit Gudang</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2">Provinsi</th>
                                    <th class="py-2">Kabupaten / Gudang</th>
                                    <th class="py-2">Varietas Benih</th>
                                    <th class="text-end py-2">Target</th>
                                    <th class="text-end py-2">Realisasi Stok</th>
                                    <th class="pe-3 py-2" style="width: 24%;">Progres Capaian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($evaluasiStok ?? [] as $item)
                                    @php
                                        $barColor = $item->persen_capaian >= 80 ? 'bg-success' : ($item->persen_capaian >= 40 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark py-2">{{ $item->provinsi->nama_provinsi ?? '-' }}</td>
                                        <td class="py-2">{{ $item->wilayah->nama_kabupaten ?? '-' }}</td>
                                        <td class="py-2">{{ $item->benih->nama_benih ?? ($item->benih->varietas ?? '-') }}</td>
                                        <td class="text-end text-muted py-2">{{ number_format($item->target_ton, 2) }} Ton</td>
                                        <td class="text-end fw-bold text-dark py-2">{{ number_format($item->realisasi_ton, 2) }} Ton</td>
                                        <td class="pe-3 py-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 7px; border-radius: 10px;">
                                                    <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $item->persen_capaian }}%;"></div>
                                                </div>
                                                <span class="fw-bold" style="font-size: 0.72rem; min-width: 40px;">{{ $item->persen_capaian }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data target & stok benih yang tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BARIS 3: PERINGATAN STOK BENIH KRITIS / MENIPIS -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                        <h6 class="fw-bold mb-0 text-dark small">Peringatan Ketersediaan Stok Benih Rendah / Kosong (&le; 1 Ton)</h6>
                    </div>
                    <a href="{{ route('stok.index') }}" class="btn btn-sm btn-outline-danger rounded-pill" style="font-size: 0.68rem; padding: 2px 10px;">
                        Pantau Semua Gudang
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-2">Provinsi</th>
                                    <th class="py-2">Kabupaten / Gudang</th>
                                    <th class="py-2">Varietas Benih</th>
                                    <th class="text-center py-2">Status</th>
                                    <th class="text-end pe-3 py-2">Stok Tersedia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stokKritis ?? [] as $stok)
                                @php
                                    $stokTonItem = $stok->jumlah_stok_kg > 100 ? ($stok->jumlah_stok_kg / 1000) : $stok->jumlah_stok_kg;
                                @endphp
                                <tr>
                                    <td class="ps-3 fw-semibold py-2">{{ $stok->provinsi->nama_provinsi ?? '-' }}</td>
                                    <td class="py-2">{{ $stok->wilayah->nama_kabupaten ?? '-' }}</td>
                                    <td class="py-2">{{ $stok->benih->nama_benih ?? ($stok->benih->varietas ?? '-') }}</td>
                                    <td class="text-center py-2">
                                        @if($stokTonItem <= 0)
                                            <span class="badge bg-danger px-2 py-1" style="font-size: 0.65rem;">KOSONG</span>
                                        @else
                                            <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.65rem;">MENIPIS</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3 fw-bold text-danger py-2">{{ number_format($stokTonItem, 2) }} Ton</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-success fw-semibold">
                                        <i class="bi bi-check-circle-fill me-1"></i>Seluruh stok benih di gudang wilayah berada pada level aman.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT CHART.JS PIMPINAN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('pimpinanTrenChart').getContext('2d');
        
        const labelsData = {!! json_encode($labelsFinal ?? ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']) !!};
        const padiData = {!! json_encode($dataPadiFinal ?? [0,0,0,0,0,0,0,0,0,0,0,0]) !!};
        const jagungData = {!! json_encode($dataJagungFinal ?? [0,0,0,0,0,0,0,0,0,0,0,0]) !!};

        function getChartColors(theme) {
            const isDark = theme === 'dark';
            return {
                text: isDark ? '#e2e8f0' : '#475569',
                grid: isDark ? 'rgba(148, 163, 184, 0.15)' : 'rgba(148, 163, 184, 0.25)'
            };
        }

        const initialColors = getChartColors(document.documentElement.getAttribute('data-bs-theme'));

        const pimpinanChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labelsData,
                datasets: [
                    { label: 'Padi (Ton)', data: padiData, backgroundColor: '#106742', borderRadius: 6 },
                    { label: 'Jagung (Ton)', data: jagungData, backgroundColor: '#ffc107', borderRadius: 6 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 10,
                            font: { size: 11, weight: '600' },
                            color: initialColors.text
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: initialColors.text, font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: initialColors.grid },
                        ticks: { color: initialColors.text, font: { size: 11 } }
                    }
                }
            }
        });

        window.addEventListener('sipanen-theme-changed', function (e) {
            const colors = getChartColors(e.detail.theme);
            pimpinanChart.options.plugins.legend.labels.color = colors.text;
            pimpinanChart.options.scales.x.ticks.color = colors.text;
            pimpinanChart.options.scales.y.ticks.color = colors.text;
            pimpinanChart.options.scales.y.grid.color = colors.grid;
            pimpinanChart.update();
        });
    });
</script>
@endsection