@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Dashboard Ringkasan Panen & Stok</h3>
        <p class="text-muted mb-0">Pantauan real-time produksi dan ketersediaan benih nasional.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('report.panen.pdf') }}" class="btn btn-outline-danger shadow-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> PDF Panen
        </a>
        <a href="{{ route('report.stok.pdf') }}" class="btn btn-danger shadow-sm">
            <i class="bi bi-file-earmark-pdf-fill me-1"></i> PDF Stok Benih
        </a>
    </div>
</div>

<!-- BARIS 1: KARTU INDIKATOR UTAMA -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-white-50 fw-semibold">Total Produksi Padi</span>
                    <i class="bi bi-flower1 fs-3 text-white-50"></i>
                </div>
                <h2 class="fw-bold mb-0 display-6">{{ number_format($totalPadiTon ?? 0, 2) }} <span class="fs-5 fw-normal">Ton</span></h2>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-warning text-dark h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-dark-50 fw-semibold">Total Produksi Jagung</span>
                    <i class="bi bi-sun fs-3 text-dark-50"></i>
                </div>
                <h2 class="fw-bold mb-0 display-6">{{ number_format($totalJagungTon ?? 0, 2) }} <span class="fs-5 fw-normal">Ton</span></h2>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-white-50 fw-semibold">Total Persediaan Benih</span>
                    <i class="bi bi-boxes fs-3 text-white-50"></i>
                </div>
                <h2 class="fw-bold mb-0 display-6">{{ number_format($totalStokKg ?? 0, 2) }} <span class="fs-5 fw-normal">Kg</span></h2>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-info text-white h-100">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-white-50 fw-semibold">Varietas Benih</span>
                    <i class="bi bi-tags fs-3 text-white-50"></i>
                </div>
                <h2 class="fw-bold mb-0 display-6">{{ $totalVarietas ?? 0 }} <span class="fs-5 fw-normal">Varietas</span></h2>
            </div>
        </div>
    </div>
</div>

<!-- BARIS 2: INDIKATOR MUTASI STOK -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm border-start border-4 border-success h-100">
            <div class="card-body py-3">
                <small class="text-muted fw-bold text-uppercase">Akumulasi Stok Masuk (Konversi/Pengadaan)</small>
                <h3 class="text-success fw-bold mt-1 mb-0">{{ number_format($stokMasukKg ?? 0, 2) }} Kg</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm border-start border-4 border-danger h-100">
            <div class="card-body py-3">
                <small class="text-muted fw-bold text-uppercase">Akumulasi Stok Keluar (Distribusi/Bantuan)</small>
                <h3 class="text-danger fw-bold mt-1 mb-0">{{ number_format($stokKeluarKg ?? 0, 2) }} Kg</h3>
            </div>
        </div>
    </div>
</div>

<!-- BARIS 3: TABEL PEMANTAUAN CEPAT (MENGISI AREA KOSONG) -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-success"></i>Aktivitas Panen Terbaru</h5>
        <a href="{{ route('panen.index') }}" class="btn btn-sm btn-outline-success">Lihat Semua Data</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Tanggal</th>
                        <th>Provinsi</th>
                        <th>Komoditas</th>
                        <th>Varietas</th>
                        <th class="text-end pe-3">Hasil Panen (Ton)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($panenTerbaru ?? [] as $panen)
                    <tr>
                        <td class="ps-3">{{ $panen->tanggal_panen_realisasi ?? $panen->tanggal_estimasi_panen }}</td>
                        <td>{{ $panen->provinsi->nama_provinsi ?? '-' }}</td>
                        <td><span class="badge bg-secondary">{{ strtoupper($panen->komoditas) }}</span></td>
                        <td>{{ $panen->varietas }}</td>
                        <td class="text-end pe-3 fw-bold">{{ number_format($panen->jumlah_panen_ton, 2) }} Ton</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada aktivitas data panen terbaru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection