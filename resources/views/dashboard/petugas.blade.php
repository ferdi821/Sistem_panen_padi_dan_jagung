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
                <a href="{{ route('panen.index') }}?action=tambah" class="btn btn-light text-success fw-bold rounded-pill px-3 shadow-sm w-100 w-sm-auto text-nowrap">
                    <i class="bi bi-plus-circle-fill me-1"></i> Input Panen
                </a>
                <a href="{{ route('stok.index') }}?action=masuk" class="btn btn-emerald text-white fw-bold rounded-pill px-3 shadow-sm w-100 w-sm-auto text-nowrap" style="background-color: #2e7d32; border: none;">
                    <i class="bi bi-plus-lg me-1"></i> Stok Masuk
                </a>
                <a href="{{ route('stok.index') }}?action=keluar" class="btn btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm w-100 w-sm-auto text-nowrap">
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
                        @php 
                            $stokDalamTon = isset($totalStokKg) ? ($totalStokKg > 100 ? $totalStokKg / 1000 : $totalStokKg) : 0;
                        @endphp
                        {{ number_format($stokDalamTon, 2) }} 
                        <span class="fs-6 fw-normal text-white-50">Ton</span>
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
                    <a href="{{ route('stok.riwayat') }}" class="btn btn-sm btn-outline-success rounded-pill text-nowrap flex-shrink-0" style="font-size: 0.65rem; padding: 2px 8px; line-height: 1.2;">
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
                                        {{ \Carbon\Carbon::parse($item->tanggal_transaksi ?? $item->created_at)->format('d/m/Y') }}
                                    </td>
                                    <td class="text-nowrap py-2">
                                        @php
                                            $komoditasName = $item->stokBenih->benih->komoditas ?? 'PADI';
                                        @endphp
                                        <span class="badge bg-{{ strtolower($komoditasName) === 'padi' ? 'success' : 'warning text-dark' }} px-2 py-1" style="font-size: 0.65rem;">
                                            {{ strtoupper($komoditasName) }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap py-2">{{ $item->sumber_tujuan ?? '-' }}</td>
                                    <td class="text-end fw-bold text-nowrap py-2">
                                        @php
                                            $valKg = $item->jumlah_kg ?? 0;
                                            $valTon = $valKg >= 100 ? $valKg / 1000 : $valKg;
                                        @endphp
                                        {{ number_format($valTon, 2) }} Ton
                                    </td>
                                    <td class="text-center text-nowrap py-2">
                                        @if(($item->jenis_transaksi ?? 'masuk') === 'masuk')
                                            <span class="text-success fw-bold"><i class="bi bi-arrow-down-left-circle me-1"></i>+{{ number_format($valTon, 2) }} Ton</span>
                                        @else
                                            <span class="text-danger fw-bold"><i class="bi bi-arrow-up-right-circle me-1"></i>-{{ number_format($valTon, 2) }} Ton</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3 text-nowrap py-2">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1" style="font-size: 0.65rem;">Disetujui</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada aktivitas input data terbaru di wilayah ini.
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
@endsection