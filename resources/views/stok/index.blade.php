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
            <p class="text-muted small mb-0">Monitoring ketersediaan stok benih kelas FS & SS, proses sertifikasi, dan prosesing per wilayah.</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @role('petugas_provinsi')
                <button type="button" class="btn btn-success shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalStokMasuk" style="background-color: #106742; border: none; font-size: 13px; font-weight: 600;">
                    + Input Stok Masuk
                </button>
                <button type="button" class="btn btn-danger shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalStokKeluar" style="font-size: 13px; font-weight: 600;">
                    - Input Stok Keluar
                </button>
            @endrole

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

    <!-- TABEL STOK BENIH (GROUPED BY KABUPATEN DENGAN ROWSPAN) -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center mb-0" style="font-size: 13px; border-color: #cbd5e1;">
                    <thead style="background-color: #f3f7f5; color: #315b49;">
                        <tr>
                            <th class="ps-3 py-3" style="width: 180px;">Kabupaten / Kota</th>
                            <th class="py-3" style="width: 110px;">Kode Benih</th>
                            <th class="text-start py-3">Nama Benih</th>
                            <th class="py-3" style="width: 90px;">Komoditas</th>
                            <th class="py-3" style="width: 110px;">Varietas</th>
                            <th class="py-3" style="width: 70px;">Kelas</th>
                            <th class="py-3" style="width: 120px;">Tersedia (Tersertifikasi)</th>
                            <th class="py-3" style="width: 120px;">Proses Sertifikasi</th>
                            <th class="py-3" style="width: 110px;">Prosesing</th>
                            @role('petugas_provinsi')
                                <th class="py-3" style="width: 130px;">Aksi</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $groupedStok = $stokList->getCollection()->groupBy(function($item) {
                                return $item->wilayah_id ?? $item->provinsi_id;
                            });
                        @endphp

                        @forelse($groupedStok as $wilayahId => $items)
                            @foreach($items as $rIdx => $stok)
                                @php
                                    $kelasLabel = strtoupper($stok->kelas_benih ?? 'FS');
                                    if ($kelasLabel === 'BD') $kelasLabel = 'FS';
                                    if ($kelasLabel === 'BP') $kelasLabel = 'SS';
                                @endphp
                                <tr>
                                    @if($rIdx === 0)
                                        <td rowspan="{{ count($items) }}" class="fw-bold bg-white align-middle" style="color: #273b32;">
                                            {{ $stok->wilayah->nama_kabupaten ?? ($stok->provinsi->nama_provinsi ?? '-') }}
                                        </td>
                                    @endif

                                    <td><code>{{ $stok->benih->kode_benih ?? '-' }}</code></td>
                                    <td class="text-start">{{ $stok->benih->nama_benih ?? '-' }}</td>
                                    <td>
                                        @if(strtolower($stok->benih->komoditas ?? '') === 'padi')
                                            <span class="badge px-2 py-1" style="background-color: #d9f2e3; color: #106742; font-size: 10px; font-weight: 700;">PADI</span>
                                        @else
                                            <span class="badge px-2 py-1" style="background-color: #fff0c2; color: #956600; font-size: 10px; font-weight: 700;">JAGUNG</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ $stok->benih->varietas ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $kelasLabel === 'FS' ? 'bg-primary' : 'bg-info text-dark' }} px-2 py-1" style="font-size: 10px;">
                                            {{ $kelasLabel }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-success">{{ number_format((float) $stok->jumlah_stok_kg, 2) }} Ton</td>
                                    <td class="fw-bold text-primary">{{ number_format((float) ($stok->proses_sertifikasi_ton ?? 0), 2) }} Ton</td>
                                    <td class="fw-bold text-warning">{{ number_format((float) ($stok->prosesing_ton ?? 0), 2) }} Ton</td>
                                    @role('petugas_provinsi')
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-warning px-2 py-1" data-bs-toggle="modal" data-bs-target="#editModalStok{{ $stok->id }}" style="font-size: 11px; font-weight: 600;">
                                                Edit
                                            </button>

                                            <form action="{{ route('stok.destroy', $stok->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data stok ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" style="font-size: 11px; font-weight: 600;">Hapus</button>
                                            </form>
                                        </td>
                                    @endrole
                                </tr>

                                @role('petugas_provinsi')
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
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small" style="color: #34483e;">Kabupaten / Kota</label>
                                                            <select name="wilayah_id" class="form-select form-select-sm" required>
                                                                <option value="">-- Pilih Kabupaten / Kota --</option>
                                                                @foreach($wilayahs as $wil)
                                                                    <option value="{{ $wil->id }}" {{ $stok->wilayah_id == $wil->id ? 'selected' : '' }}>
                                                                        {{ $wil->nama_kabupaten }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <input type="hidden" name="provinsi_id" value="{{ $stok->provinsi_id }}">
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-7 mb-3">
                                                                <label class="form-label fw-bold small" style="color: #34483e;">Varietas Benih</label>
                                                                <select name="benih_id" class="form-select form-select-sm" required>
                                                                    @foreach($benihs as $b)
                                                                        <option value="{{ $b->id }}" {{ $stok->benih_id == $b->id ? 'selected' : '' }}>
                                                                            {{ $b->varietas }} ({{ $b->nama_benih }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-5 mb-3">
                                                                <label class="form-label fw-bold small" style="color: #34483e;">Kelas Benih</label>
                                                                <select name="kelas_benih" class="form-select form-select-sm" required>
                                                                    <option value="FS" {{ $kelasLabel == 'FS' ? 'selected' : '' }}>FS (Benih Dasar)</option>
                                                                    <option value="SS" {{ $kelasLabel == 'SS' ? 'selected' : '' }}>SS (Benih Pokok)</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label fw-bold small" style="color: #34483e;">Tersedia (Ton)</label>
                                                                <input type="number" step="0.01" min="0" name="jumlah_stok_kg" class="form-control form-control-sm" value="{{ $stok->jumlah_stok_kg }}" required>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label fw-bold small" style="color: #34483e;">Proses Sertifikasi</label>
                                                                <input type="number" step="0.01" min="0" name="proses_sertifikasi_ton" class="form-control form-control-sm" value="{{ $stok->proses_sertifikasi_ton ?? 0 }}">
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label fw-bold small" style="color: #34483e;">Prosesing (Ton)</label>
                                                                <input type="number" step="0.01" min="0" name="prosesing_ton" class="form-control form-control-sm" value="{{ $stok->prosesing_ton ?? 0 }}">
                                                            </div>
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
                                @endrole
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('petugas_provinsi') ? 10 : 9 }}" class="py-4 text-muted">Belum ada data persediaan stok benih wilayah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CUSTOM PAGINATION RAPI (SATU BARIS) -->
    @if ($stokList->hasPages())
        <div class="d-flex justify-content-center mt-3 mb-4">
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if ($stokList->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $stokList->previousPageUrl() }}" style="color: #106742;">&laquo; Previous</a></li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($stokList->getUrlRange(1, $stokList->lastPage()) as $page => $url)
                        @if ($page == $stokList->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link" style="background-color: #106742; border-color: #106742; color: #fff;">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}" style="color: #106742;">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
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

@role('petugas_provinsi')
    <!-- MODAL INPUT STOK MASUK -->
    <div class="modal fade" id="modalStokMasuk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header text-white px-4 py-3" style="background-color: #106742;">
                    <h5 class="modal-title fw-bold fs-6">Form Input Stok Benih Wilayah</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('stok.masuk.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 text-start">
                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Kabupaten / Kota Gudang</label>
                            <select name="wilayah_id" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Kabupaten / Kota --</option>
                                @foreach($wilayahs as $wil)
                                    <option value="{{ $wil->id }}">{{ $wil->nama_kabupaten }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Pilih Varietas Benih</label>
                                <select name="benih_id" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Varietas --</option>
                                    @foreach($benihs as $b)
                                        <option value="{{ $b->id }}">
                                            {{ $b->varietas }} - {{ $b->nama_benih }} ({{ strtoupper($b->komoditas) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Kelas Benih</label>
                                <select name="kelas_benih" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <option value="FS">FS (Benih Dasar)</option>
                                    <option value="SS">SS (Benih Pokok)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Wilayah / Sumber Transaksi</label>
                            <input type="text" name="sumber_tujuan" class="form-control form-control-sm" placeholder="Contoh: Hasil Panen Poktan Jaya / BPSIP Jawa Barat" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Tersedia (Ton)</label>
                                <input type="number" step="0.01" min="0" name="jumlah_kg" class="form-control form-control-sm" value="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Proses Sertifikasi</label>
                                <input type="number" step="0.01" min="0" name="proses_sertifikasi_ton" class="form-control form-control-sm" value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Prosesing (Ton)</label>
                                <input type="number" step="0.01" min="0" name="prosesing_ton" class="form-control form-control-sm" value="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small" style="color: #34483e;">Keterangan / Catatan (Opsional)</label>
                            <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Catatan tambahan stok masuk..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" style="background-color: #106742; border: none;">Simpan Data Stok</button>
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
                                    @php
                                        $kls = strtoupper($s->kelas_benih ?? 'FS');
                                        if ($kls === 'BD') $kls = 'FS';
                                        if ($kls === 'BP') $kls = 'SS';
                                    @endphp
                                    <option value="{{ $s->id }}">
                                        {{ $s->wilayah->nama_kabupaten ?? ($s->provinsi->nama_provinsi ?? '-') }} - {{ $s->benih->varietas ?? '-' }} [Kelas {{ $kls }}] (Sisa: {{ number_format($s->jumlah_stok_kg, 2) }} Ton)
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
                                <input type="number" step="0.01" name="jumlah_kg" class="form-control form-control-sm" placeholder="Contoh: 9.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Tanggal Transaksi</label>
                                <input type="date" name="tanggal_transaksi" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small" style="color: #34483e;">Keterangan / Catatan Alokasi</label>
                            <textarea name="keterangan" class="form-control form-control-sm" rows="2" placeholder="Catatan tambahan alokasi distribusi..."></textarea>
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
@endrole
@endsection