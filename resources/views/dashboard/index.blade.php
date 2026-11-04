@extends('layouts.app')

@section('title', 'Dashboard Ringkasan Panen & Stok')

@section('content')
<!-- BANNER UTAMA HIJAU -->
<div class="card border-0 shadow-sm text-white mb-4" style="background: linear-gradient(135deg, #106742 0%, #0c5234 100%); border-radius: 16px;">
    <div class="card-body p-4 p-md-5">
        <small class="text-white-50 fw-bold text-uppercase tracking-wide" style="font-size: 0.75rem;">SIPANEN KEMENTAN</small>
        <h2 class="fw-bold mt-1 mb-2">Ringkasan Panen & Stok</h2>
        <p class="mb-0 text-white-50" style="max-width: 650px;">
            Ikhtisar real-time hasil produksi panen padi, jagung, serta akumulasi mutasi stok benih nasional.
        </p>
    </div>
</div>

<!-- SUB-HEADER IKHTISAR -->
<div class="mb-3">
    <h5 class="fw-bold mb-1 text-dark">Ikhtisar Produksi & Stok</h5>
    <p class="text-muted small mb-0">Ringkasan berdasarkan akumulasi data komoditas dan persediaan yang tersedia.</p>
</div>

<!-- BARIS 1: 4 KARTU INDIKATOR UTAMA BERWARNA -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Produksi Padi (Hijau) -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100" style="border-radius: 14px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-white-50 fw-semibold small text-uppercase">Total Produksi Padi</span>
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-flower1 fs-4 text-white"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0 display-6">
                    {{ number_format($totalPadiTon ?? $totalPadi ?? 0, 2) }} 
                    <span class="fs-6 fw-normal text-white-50">Ton</span>
                </h2>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Produksi Jagung (Kuning/Orange) -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-warning text-dark h-100" style="border-radius: 14px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-dark-50 fw-semibold small text-uppercase">Total Produksi Jagung</span>
                    <div class="bg-dark bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-sun fs-4 text-dark"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0 display-6">
                    {{ number_format($totalJagungTon ?? $totalJagung ?? 0, 2) }} 
                    <span class="fs-6 fw-normal text-dark-50">Ton</span>
                </h2>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Persediaan Benih (Biru) -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100" style="border-radius: 14px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-white-50 fw-semibold small text-uppercase">Total Persediaan Benih</span>
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-boxes fs-4 text-white"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0 display-6">
                    {{ number_format($totalStokKg ?? 0, 2) }} 
                    <span class="fs-6 fw-normal text-white-50">Kg</span>
                </h2>
            </div>
        </div>
    </div>

    <!-- Card 4: Varietas Benih (Cyan/Info) -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-info text-white h-100" style="border-radius: 14px;">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-white-50 fw-semibold small text-uppercase">Varietas Benih</span>
                    <div class="bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-tags fs-4 text-white"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-0 display-6">
                    {{ $totalVarietas ?? 0 }} 
                    <span class="fs-6 fw-normal text-white-50">Varietas</span>
                </h2>
            </div>
        </div>
    </div>
</div>

<!-- SUB-HEADER AKTIVITAS TERBARU -->
<div class="mb-3">
    <h5 class="fw-bold mb-1 text-dark">Aktivitas Terbaru</h5>
    <p class="text-muted small mb-0">Aktivitas data panen dan pencatatan mutasi stok benih terkini.</p>
</div>

<!-- BARIS 2: TABEL AKTIVITAS PANEN & KARTU STOK MUTASI -->
<div class="row g-3">
    <!-- Tabel Aktivitas Panen Terbaru -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 14px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-journal-text me-2 text-success"></i>Aktivitas Panen Terbaru
                </h6>
                <a href="{{ route('panen.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 py-3">Tanggal</th>
                                <th class="py-3">Provinsi</th>
                                <th class="py-3">Komoditas</th>
                                <th class="text-end pe-3 py-3">Hasil (Ton)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($panenTerbaru ?? [] as $panen)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $panen->tanggal_panen_realisasi ?? $panen->tanggal_panen ?? $panen->tanggal_estimasi_panen }}</td>
                                <td>{{ $panen->provinsi->nama_provinsi ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ strtolower($panen->komoditas) === 'padi' ? 'success' : 'warning' }} px-2 py-1">
                                        {{ strtoupper($panen->komoditas) }}
                                    </span>
                                </td>
                                <td class="text-end pe-3 fw-bold">{{ number_format($panen->jumlah_panen_ton, 2) }} Ton</td>
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

    <!-- Kartu Akumulasi Stok Masuk & Keluar -->
    <div class="col-lg-5 d-flex flex-column gap-3">
        <!-- Stok Masuk -->
        <div class="card border-0 shadow-sm border-start border-4 border-success h-100" style="border-radius: 14px;">
            <div class="card-body py-3 px-4 d-flex flex-column justify-content-center">
                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">Akumulasi Stok Masuk (Konversi/Pengadaan)</small>
                <h3 class="text-success fw-bold mt-2 mb-0">+{{ number_format($stokMasukKg ?? 0, 2) }} Kg</h3>
            </div>
        </div>

        <!-- Stok Keluar -->
        <div class="card border-0 shadow-sm border-start border-4 border-danger h-100" style="border-radius: 14px;">
            <div class="card-body py-3 px-4 d-flex flex-column justify-content-center">
                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">Akumulasi Stok Keluar (Distribusi/Bantuan)</small>
                <h3 class="text-danger fw-bold mt-2 mb-0">-{{ number_format($stokKeluarKg ?? 0, 2) }} Kg</h3>
            </div>
        </div>
    </div>
</div>
@endsection