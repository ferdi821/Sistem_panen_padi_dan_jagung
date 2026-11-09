@extends('layouts.app')

@section('title', 'Persediaan Stok Benih Wilayah - SIPANEN')

@section('content')
<style>
    #main-wrapper {
        min-height: auto !important;
    }
    main.flex-grow-1 {
        flex-grow: 0 !important;
    }
</style>

<div class="container-fluid px-4 py-3">

    <!-- HEADER HALAMAN -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #106742;">Persediaan Stok Benih Wilayah</h3>
            <p class="text-muted small mb-0">Monitoring ketersediaan stok benih dan transaksi distribusi per wilayah.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @hasanyrole('admin_pusat|petugas_provinsi')
                <button type="button" class="btn btn-success shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalStokMasuk" style="background-color: #106742; border: none; font-size: 13px; font-weight: 600;">
                    + Input Stok Masuk
                </button>
                <button type="button" class="btn btn-danger shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalStokKeluar" style="font-size: 13px; font-weight: 600;">
                    - Input Stok Keluar
                </button>
            @endhasanyrole

            <a href="{{ route('stok.riwayat') }}" class="btn btn-info text-white shadow-sm px-3 py-2" style="background-color: #0ea5e9; border: none; font-size: 13px; font-weight: 600;">
                <i class="bi bi-clock-history me-1"></i> Riwayat Mutasi
            </a>
        </div>
    </div>

    <!-- NOTIFIKASI BERHASIL -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- NOTIFIKASI ERROR -->
    @if(session('error') || $errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            @if(session('error'))
                {{ session('error') }}
            @else
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- TABEL STOK BENIH -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0" style="font-size: 13px;">
                    <thead style="background-color: #f3f7f5; color: #315b49;">
                        <tr>
                            <th class="text-start ps-4 py-3">Provinsi</th>
                            <th class="py-3">Kode Benih</th>
                            <th class="text-start py-3">Nama Benih</th>
                            <th class="py-3">Komoditas</th>
                            <th class="py-3">Varietas</th>
                            <th class="py-3">Jumlah Stok Tersedia</th>
                            @hasanyrole('admin_pusat|petugas_provinsi')
                                <th class="py-3">Aksi</th>
                            @endhasanyrole
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stokList as $stok)
                            <tr>
                                <td class="text-start ps-4 fw-bold" style="color: #273b32;">
                                    {{ $stok->provinsi->nama_provinsi ?? '-' }}
                                </td>
                                <td><code>{{ $stok->benih->kode_benih ?? '-' }}</code></td>
                                <td class="text-start">{{ $stok->benih->nama_benih ?? '-' }}</td>
                                <td>
                                    @if(strtolower($stok->benih->komoditas ?? '') === 'padi')
                                        <span class="badge px-2 py-1" style="background-color: #d9f2e3; color: #106742; font-size: 10px; font-weight: 700;">PADI</span>
                                    @else
                                        <span class="badge px-2 py-1" style="background-color: #fff0c2; color: #956600; font-size: 10px; font-weight: 700;">JAGUNG</span>
                                    @endif
                                </td>
                                <td>{{ $stok->benih->varietas ?? '-' }}</td>
                                <td class="fw-bold text-success">{{ number_format($stok->jumlah_stok_kg, 2) }} Ton</td>
                                @hasanyrole('admin_pusat|petugas_provinsi')
                                    <td>
                                        <!-- BUTTON EDIT MODAL POPUP -->
                                        <button type="button" class="btn btn-sm btn-outline-warning px-2 py-1" data-bs-toggle="modal" data-bs-target="#editModalStok{{ $stok->id }}" style="font-size: 11px; font-weight: 600;">
                                            Edit
                                        </button>

                                        <form action="{{ route('stok.destroy', $stok->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data stok ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" style="font-size: 11px; font-weight: 600;">Hapus</button>
                                        </form>
                                    </td>
                                @endhasanyrole
                            </tr>

                            <!-- MODAL EDIT STOK BENIH WILAYAH -->
                            <div class="modal fade" id="editModalStok{{ $stok->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                                        <div class="modal-header text-white px-4 py-3" style="background-color: #e67e22;">
                                            <h5 class="modal-title fw-bold fs-6">Edit Stok Benih Wilayah</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('stok.update', $stok->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4 text-start">
                                                
                                                <!-- PROVINSI KONDISIONAL ROLE -->
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Provinsi</label>
                                                    @if(auth()->user()->hasRole('admin_pusat'))
                                                        <!-- Admin Pusat Bebas Pilih -->
                                                        <select name="provinsi_id" class="form-select form-select-sm" required>
                                                            @foreach($provinsis as $prov)
                                                                <option value="{{ $prov->id }}" {{ $stok->provinsi_id == $prov->id ? 'selected' : '' }}>
                                                                    {{ $prov->nama_provinsi }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <!-- Petugas Provinsi Dikunci (Readonly) -->
                                                        <input type="text" class="form-control form-control-sm bg-light" value="{{ $stok->provinsi->nama_provinsi ?? '-' }}" readonly>
                                                        <input type="hidden" name="provinsi_id" value="{{ $stok->provinsi_id }}">
                                                    @endif
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Benih</label>
                                                    <select name="benih_id" class="form-select form-select-sm" required>
                                                        @foreach($benihs as $b)
                                                            <option value="{{ $b->id }}" {{ $stok->benih_id == $b->id ? 'selected' : '' }}>
                                                                [{{ $b->kode_benih }}] {{ $b->nama_benih }} - {{ $b->varietas }} ({{ strtoupper($b->komoditas) }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Jumlah Stok Tersedia (Ton)</label>
                                                    <input type="number" step="0.01" name="jumlah_stok_kg" class="form-control form-control-sm" value="{{ $stok->jumlah_stok_kg }}" required>
                                                </div>

                                            </div>
                                            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                                                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold text-white" style="background-color: #e67e22; border: none;">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-muted">Belum ada data persediaan stok benih wilayah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CUSTOM PAGINATION RAPI -->
    @if ($stokList->hasPages())
        <div class="d-flex justify-content-center mt-3 mb-4">
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    @if ($stokList->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $stokList->previousPageUrl() }}" style="color: #106742;">&laquo; Previous</a></li>
                    @endif

                    @foreach ($stokList->getUrlRange(1, $stokList->lastPage()) as $page => $url)
                        @if ($page == $stokList->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link" style="background-color: #106742; border-color: #106742; color: #fff;">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}" style="color: #106742;">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if ($stokList->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $stokList->nextPageUrl() }}" style="color: #106742;">Next &raquo;</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif

</div>

<!-- MODAL INPUT STOK MASUK -->
<div class="modal fade" id="modalStokMasuk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white px-4 py-3" style="background-color: #106742;">
                <h5 class="modal-title fw-bold fs-6">Form Transaksi Stok Masuk (Penambahan Stok)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('stok.masuk.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Pilih Stok Benih Wilayah</label>
                        <select name="stok_benih_id" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Stok Tersedia --</option>
                            @foreach($stokOptions as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->provinsi->nama_provinsi ?? '-' }} - {{ $s->benih->nama_benih ?? '-' }} (Stok Saat Ini: {{ number_format($s->jumlah_stok_kg, 2) }} Ton)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Wilayah / Daerah Spesifik (Kecamatan / Kabupaten)</label>
                        <input type="text" name="sumber_tujuan" class="form-control form-control-sm" placeholder="Contoh: Ciparay / Majalaya / Kota Jambi" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Jumlah Ditambahkan (Ton)</label>
                            <input type="number" step="0.01" name="jumlah_kg" class="form-control form-control-sm" placeholder="Contoh: 10.00" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small" style="color: #34483e;">Keterangan / Catatan (Opsional)</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Catatan tambahan stok masuk..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" style="background-color: #106742; border: none;">Proses Stok Masuk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL INPUT STOK KELUAR -->
<div class="modal fade" id="modalStokKeluar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white px-4 py-3" style="background-color: #dc3545;">
                <h5 class="modal-title fw-bold fs-6">Form Transaksi Stok Keluar / Distribusi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('stok.keluar.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Pilih Stok Benih Wilayah</label>
                        <select name="stok_benih_id" class="form-select form-select-sm" required>
                            <option value="">-- Pilih Stok Tersedia --</option>
                            @foreach($stokOptions as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->provinsi->nama_provinsi ?? '-' }} - {{ $s->benih->nama_benih ?? '-' }} (Sisa: {{ number_format($s->jumlah_stok_kg, 2) }} Ton)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Tujuan Daerah / Wilayah Distribusi</label>
                        <input type="text" name="sumber_tujuan" class="form-control form-control-sm" placeholder="Contoh: Poktan Mungkur Jaya / Wilayah Majalaya" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Jumlah Dikeluarkan (Ton)</label>
                            <input type="number" step="0.01" name="jumlah_kg" class="form-control form-control-sm" placeholder="Contoh: 1.50" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small" style="color: #34483e;">Keterangan / Catatan Alokasi</label>
                        <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Catatan tambahan alokasi stok keluar..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold">Proses Stok Keluar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection