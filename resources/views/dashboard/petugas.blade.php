@extends('layouts.app')

@section('title', 'Dashboard Petugas Wilayah - ' . ($namaProvinsi ?? 'Wilayah'))

@section('content')
<div class="container-fluid px-0 px-md-2">
    <!-- BANNER UTAMA HIJAU WILAYAH -->
    <div class="card border-0 shadow-sm text-white mb-4" style="background: linear-gradient(135deg, #106742 0%, #0c5234 100%); border-radius: 16px;">
        <div class="card-body p-3 p-sm-4 p-md-5 d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
            <div class="text-center text-md-start">
                <small class="text-white-50 fw-bold text-uppercase tracking-wide" style="font-size: 0.7rem;">SIPANEN PETUGAS PROVINSI</small>
                <h2 class="fw-bold mt-1 mb-2 fs-3 fs-md-2">Wilayah {{ $namaProvinsi ?? 'Saya' }}</h2>
                <p class="mb-0 text-white-50 small" style="max-width: 650px;">
                    Monitoring hasil produksi panen padi, jagung, serta persediaan stok benih wilayah {{ $namaProvinsi ?? '' }} secara real-time.
                </p>
            </div>
            <!-- TOMBOL AKSI CEPAT PETUGAS -->
            <div class="d-flex gap-2 w-100 w-md-auto flex-column flex-sm-row">
                <a href="{{ route('panen.create') }}" class="btn btn-light text-success fw-bold rounded-pill px-3 shadow-sm w-100 w-sm-auto text-nowrap">
                    <i class="bi bi-plus-circle-fill me-1"></i> Input Panen
                </a>
                <a href="{{ route('stok.keluar.create') }}" class="btn btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm w-100 w-sm-auto text-nowrap">
                    <i class="bi bi-dash-circle-fill me-1"></i> Stok Keluar
                </a>
            </div>
        </div>
    </div>

    <!-- SUB-HEADER IKHTISAR WILAYAH -->
    <div class="mb-3">
        <h5 class="fw-bold mb-1 text-dark fs-6 fs-md-5">Ikhtisar Produksi & Stok Wilayah</h5>
        <p class="text-muted small mb-0">Akumulasi data panen dan persediaan benih lokal {{ $namaProvinsi ?? '' }}.</p>
    </div>

    <!-- 4 KARTU INDIKATOR UTAMA WILAYAH -->
    <div class="row g-2 g-md-3 mb-4">
        <!-- Card 1: Padi -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-success text-white h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.7rem;">Produksi Padi Wilayah</span>
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

        <!-- Card 2: Jagung -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-warning text-dark h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-dark-50 fw-semibold text-uppercase" style="font-size: 0.7rem;">Produksi Jagung Wilayah</span>
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

        <!-- Card 3: Stok -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-primary text-white h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.7rem;">Persediaan Stok Wilayah</span>
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

        <!-- Card 4: Varietas -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bg-info text-white h-100" style="border-radius: 14px;">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.7rem;">Varietas Benih Lokal</span>
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

    <!-- TABEL RIWAYAT TRANSAKSI TERBARU -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
                <div class="card-header bg-white py-2 py-md-3 border-bottom d-flex align-items-center justify-content-between gap-2 overflow-hidden" style="min-width: 0;">
                    <div class="d-flex align-items-center overflow-hidden me-auto" style="min-width: 0;">
                        <i class="bi bi-clock-history me-1 text-primary flex-shrink-0"></i>
                        <h6 class="fw-bold mb-0 text-dark text-truncate" style="font-size: clamp(0.72rem, 2.5vw, 0.88rem); white-space: nowrap;">
                            Aktivitas Input Data Terbaru
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
                                    <th class="py-2">Komoditas</th>
                                    <th class="py-2">Kabupaten/Kota</th>
                                    <th class="text-end py-2">Jumlah</th>
                                    <th class="text-center py-2">Mutasi Stok</th>
                                    <th class="text-center pe-3 py-2">Validasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatTerbaru ?? [] as $item)
                                <tr>
                                    <td class="ps-3 fw-semibold text-nowrap py-2">
                                        {{ \Carbon\Carbon::parse($item->tanggal ?? $item->created_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="text-nowrap py-2">
                                        <span class="badge bg-{{ strtolower($item->komoditas ?? '') === 'padi' ? 'success' : 'warning text-dark' }} px-2 py-1" style="font-size: 0.65rem;">
                                            {{ strtoupper($item->komoditas ?? 'PADI') }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap py-2">{{ $item->wilayah->nama_wilayah ?? $item->kabupaten ?? 'Manggungharja' }}</td>
                                    <td class="text-end fw-bold text-nowrap py-2">
                                        {{ number_format($item->jumlah ?? $item->jumlah_panen_ton ?? 0, 2) }} Ton
                                    </td>
                                    <td class="text-center text-nowrap py-2">
                                        @if(($item->jenis_mutasi ?? 'masuk') === 'masuk')
                                            <span class="text-success fw-bold"><i class="bi bi-arrow-down-left-circle me-1"></i>+{{ number_format($item->stok_kg ?? 500, 0) }} Kg</span>
                                        @else
                                            <span class="text-danger fw-bold"><i class="bi bi-arrow-up-right-circle me-1"></i>-{{ number_format($item->stok_kg ?? 200, 0) }} Kg</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3 text-nowrap py-2">
                                        @php $status = strtolower($item->status_validasi ?? 'disetujui'); @endphp
                                        @if($status === 'disetujui')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1" style="font-size: 0.65rem;">Disetujui</span>
                                        @elseif($status === 'pending')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-2 py-1" style="font-size: 0.65rem;">Pending</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2 py-1" style="font-size: 0.65rem;">Ditolak</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="ps-3 fw-semibold text-nowrap py-2">08/11/2026</td>
                                    <td class="text-nowrap py-2"><span class="badge bg-success px-2 py-1" style="font-size: 0.65rem;">PADI</span></td>
                                    <td class="text-nowrap py-2">Manggungharja</td>
                                    <td class="text-end fw-bold text-nowrap py-2">15.50 Ton</td>
                                    <td class="text-center text-nowrap py-2"><span class="text-success fw-bold"><i class="bi bi-arrow-down-left-circle me-1"></i>+1,200 Kg</span></td>
                                    <td class="text-center pe-3 text-nowrap py-2"><span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1" style="font-size: 0.65rem;">Disetujui</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-3 fw-semibold text-nowrap py-2">06/11/2026</td>
                                    <td class="text-nowrap py-2"><span class="badge bg-warning text-dark px-2 py-1" style="font-size: 0.65rem;">JAGUNG</span></td>
                                    <td class="text-nowrap py-2">Ciparay</td>
                                    <td class="text-end fw-bold text-nowrap py-2">8.20 Ton</td>
                                    <td class="text-center text-nowrap py-2"><span class="text-danger fw-bold"><i class="bi bi-arrow-up-right-circle me-1"></i>-500 Kg</span></td>
                                    <td class="text-center pe-3 text-nowrap py-2"><span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-2 py-1" style="font-size: 0.65rem;">Pending</span></td>
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
@endsection