@extends('layouts.app')

@section('title', 'Dashboard Ringkasan Panen & Stok Nasional')

@section('content')
<style>
    .chart-box-container {
        position: relative;
        width: 100%;
        height: 280px;
    }
    @media (min-width: 992px) {
        .chart-box-container {
            height: 320px;
        }
    }
</style>

<div class="container-fluid px-0 px-md-2">
    <!-- BANNER UTAMA NASIONAL -->
    <div class="card border-0 shadow-sm text-white mb-4" style="background: linear-gradient(135deg, #106742 0%, #0c5234 100%); border-radius: 16px;">
        <div class="card-body p-3 p-sm-4 p-md-5">
            <small class="text-white-50 fw-bold text-uppercase tracking-wide" style="font-size: 0.7rem;">SIPANEN KEMENTAN</small>
            <h2 class="fw-bold mt-1 mb-2 fs-3 fs-md-2">Ringkasan Panen & Stok Nasional</h2>
            <p class="mb-0 text-white-50 small" style="max-width: 650px;">
                Ikhtisar real-time hasil produksi panen padi, jagung, serta akumulasi mutasi stok benih nasional.
            </p>
        </div>
    </div>

    <!-- 4 KARTU INDIKATOR -->
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
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.7rem;">Total Persediaan Benih</span>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-boxes fs-5 text-white"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 fs-3">
                        {{ number_format($totalStokKg ?? 0, 2) }} 
                        <span class="fs-6 fw-normal text-white-50">Kg</span>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-info text-white h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.7rem;">Varietas Benih</span>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-tags fs-5 text-white"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 fs-3">
                        {{ $totalVarietas ?? 0 }} 
                        <span class="fs-6 fw-normal text-white-50">Varietas</span>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- BARIS GRAFIK & TABEL ADMIN -->
    <div class="row g-3">
        <!-- GRAFIK TREN NASIONAL -->
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white py-2 py-md-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark small">
                        <i class="bi bi-bar-chart-line-fill me-2 text-success"></i>Tren Produksi Nasional
                    </h6>
                    <span class="badge bg-light text-muted border">Tahun {{ date('Y') }}</span>
                </div>
                <div class="card-body p-2 p-md-3 d-flex align-items-center">
                    <div class="chart-box-container">
                        <canvas id="adminTrenChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABEL PANEN TERBARU -->
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-white py-2 py-md-3 border-bottom d-flex align-items-center justify-content-between gap-2 overflow-hidden" style="min-width: 0;">
                    <div class="d-flex align-items-center overflow-hidden me-auto" style="min-width: 0;">
                        <i class="bi bi-journal-text me-1 text-success flex-shrink-0"></i>
                        <h6 class="fw-bold mb-0 text-dark text-truncate" style="font-size: clamp(0.72rem, 2.5vw, 0.88rem); white-space: nowrap;">
                            Aktivitas Panen Terbaru
                        </h6>
                    </div>
                    <a href="{{ route('panen.index') }}" class="btn btn-sm btn-outline-success rounded-pill text-nowrap flex-shrink-0" style="font-size: 0.65rem; padding: 2px 8px; line-height: 1.2;">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="-webkit-overflow-scrolling: touch;">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.78rem;">
                            <thead class="table-light text-nowrap">
                                <tr>
                                    <th class="ps-3 py-2">Tanggal</th>
                                    <th class="py-2">Provinsi</th>
                                    <th class="py-2">Komoditas</th>
                                    <th class="text-end pe-3 py-2">Hasil (Ton)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($panenTerbaru ?? [] as $panen)
                                <tr>
                                    <td class="ps-3 fw-semibold text-nowrap py-2">
                                        {{ $panen->tanggal_panen_realisasi ?? $panen->tanggal_panen ?? $panen->tanggal_estimasi_panen }}
                                    </td>
                                    <td class="text-nowrap py-2">{{ $panen->provinsi->nama_provinsi ?? '-' }}</td>
                                    <td class="text-nowrap py-2">
                                        <span class="badge bg-{{ strtolower($panen->komoditas) === 'padi' ? 'success' : 'warning text-dark' }} px-2 py-1" style="font-size: 0.65rem;">
                                            {{ strtoupper($panen->komoditas) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-3 fw-bold text-nowrap py-2">
                                        {{ number_format($panen->jumlah_panen_ton, 2) }} Ton
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada aktivitas data panen terbaru.</td>
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

<!-- SCRIPT CHART.JS ADMIN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('adminTrenChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov'],
                datasets: [
                    { label: 'Padi (Ton)', data: [120, 145, 160, 130, 175, 168], backgroundColor: '#106742', borderRadius: 6 },
                    { label: 'Jagung (Ton)', data: [65, 80, 75, 90, 60, 55], backgroundColor: '#ffc107', borderRadius: 6 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { borderDash: [4, 4] } }
                }
            }
        });
    });
</script>
@endsection