@extends('layouts.app')

@section('title', 'Catatan Hasil Panen - SIPANEN')

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
            <h3 class="fw-bold mb-1" style="color: #106742;">Catatan Hasil Panen</h3>
            <p class="text-muted small mb-0">Rekapitulasi produksi dan hasil panen komoditas padi/jagung per wilayah.</p>
        </div>

        @hasanyrole('admin_pusat|petugas_provinsi')
            <button type="button" class="btn btn-success shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahPanen" style="background-color: #106742; border: none; font-size: 13px; font-weight: 600;">
                + Tambah Data Panen
            </button>
        @endhasanyrole
    </div>

    <!-- NOTIFIKASI BERHASIL -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- NOTIFIKASI ERROR -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- TABEL DATA PANEN -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0" style="font-size: 13px;">
                    <thead style="background-color: #f3f7f5; color: #315b49;">
                        <tr>
                            <th class="text-start ps-4 py-3">Provinsi / Wilayah</th>
                            <th class="py-3">Komoditas</th>
                            <th class="py-3">Tanggal Panen</th>
                            <th class="py-3">Luas Lahan (Ha)</th>
                            <th class="py-3">Hasil Panen (Ton)</th>
                            <th class="py-3">Catatan</th>
                            @hasanyrole('admin_pusat|petugas_provinsi')
                                <th class="py-3">Aksi</th>
                            @endhasanyrole
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($panens as $panen)
                            <tr>
                                <td class="text-start ps-4 fw-bold" style="color: #273b32;">
                                    {{ $panen->provinsi->nama_provinsi ?? '-' }}
                                </td>
                                <td>
                                    @if(strtolower($panen->komoditas) === 'padi')
                                        <span class="badge px-2 py-1" style="background-color: #d9f2e3; color: #106742; font-size: 10px; font-weight: 700;">PADI</span>
                                    @else
                                        <span class="badge px-2 py-1" style="background-color: #fff0c2; color: #956600; font-size: 10px; font-weight: 700;">JAGUNG</span>
                                    @endif
                                </td>
                                <td>{{ date('d/m/Y', strtotime($panen->tanggal_panen)) }}</td>
                                <td>{{ number_format($panen->luas_lahan_hektar, 2) }} Ha</td>
                                <td class="fw-bold text-success">{{ number_format($panen->jumlah_panen_ton, 2) }} Ton</td>
                                <td class="text-muted">{{ $panen->catatan ?? '-' }}</td>
                                @hasanyrole('admin_pusat|petugas_provinsi')
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-warning px-2 py-1" style="font-size: 11px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalEditPanen{{ $panen->id }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('panen.destroy', $panen->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan panen ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" style="font-size: 11px; font-weight: 600;">Hapus</button>
                                        </form>
                                    </td>
                                @endhasanyrole
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 text-muted">Belum ada catatan hasil panen.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CUSTOM PAGINATION RAPI (SATU BARIS) -->
    @if ($panens->hasPages())
        <div class="d-flex justify-content-center mt-3 mb-4">
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous Page Link --}}
                    @if ($panens->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $panens->previousPageUrl() }}" style="color: #106742;">&laquo; Previous</a></li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($panens->getUrlRange(1, $panens->lastPage()) as $page => $url)
                        @if ($page == $panens->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link" style="background-color: #106742; border-color: #106742; color: #fff;">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}" style="color: #106742;">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($panens->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $panens->nextPageUrl() }}" style="color: #106742;">Next &raquo;</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    @endif

</div>

<!-- =====================================
     MODAL EDIT DATA PANEN
===================================== -->
@foreach($panens as $panen)
    <div class="modal fade" id="modalEditPanen{{ $panen->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header text-white px-4 py-3" style="background-color: #d97706;">
                    <h5 class="modal-title fw-bold fs-6">Edit Data Hasil Panen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('panen.update', $panen->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4 text-start">
                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Provinsi</label>
                            @hasrole('petugas_provinsi')
                                <input type="text" class="form-control bg-light form-control-sm" value="{{ auth()->user()->provinsi->nama_provinsi ?? 'Jawa Barat' }}" readonly>
                                <input type="hidden" name="provinsi_id" value="{{ auth()->user()->provinsi_id }}">
                            @else
                                <select name="provinsi_id" class="form-select form-select-sm" required>
                                    @foreach($provinsis as $p)
                                        <option value="{{ $p->id }}" {{ $panen->provinsi_id == $p->id ? 'selected' : '' }}>
                                            {{ $p->nama_provinsi }}
                                        </option>
                                    @endforeach
                                </select>
                            @endhasrole
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Komoditas</label>
                            <select name="komoditas" class="form-select form-select-sm" required>
                                <option value="padi" {{ $panen->komoditas == 'padi' ? 'selected' : '' }}>Padi</option>
                                <option value="jagung" {{ $panen->komoditas == 'jagung' ? 'selected' : '' }}>Jagung</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Tanggal Panen</label>
                            <input type="date" name="tanggal_panen" class="form-control form-control-sm" value="{{ $panen->tanggal_panen }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Luas Lahan (Ha)</label>
                                <input type="number" step="0.01" name="luas_lahan_hektar" class="form-control form-control-sm" value="{{ $panen->luas_lahan_hektar }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Hasil Panen (Ton)</label>
                                <input type="number" step="0.01" name="jumlah_panen_ton" class="form-control form-control-sm" value="{{ $panen->jumlah_panen_ton }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small" style="color: #34483e;">Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control form-control-sm" rows="2">{{ $panen->catatan }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm px-4 text-white fw-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- =====================================
     MODAL TAMBAH DATA PANEN
===================================== -->
<div class="modal fade" id="modalTambahPanen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white px-4 py-3" style="background-color: #106742;">
                <h5 class="modal-title fw-bold fs-6">Input Data Hasil Panen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('panen.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Provinsi</label>
                        @hasrole('petugas_provinsi')
                            <input type="text" class="form-control bg-light form-control-sm" value="{{ auth()->user()->provinsi->nama_provinsi ?? 'Jawa Barat' }}" readonly>
                            <input type="hidden" name="provinsi_id" value="{{ auth()->user()->provinsi_id }}">
                        @else
                            <select name="provinsi_id" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinsis as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_provinsi }}</option>
                                @endforeach
                            </select>
                        @endhasrole
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Komoditas</label>
                        <select name="komoditas" class="form-select form-select-sm" required>
                            <option value="padi">Padi</option>
                            <option value="jagung">Jagung</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Tanggal Panen</label>
                        <input type="date" name="tanggal_panen" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Luas Lahan (Ha)</label>
                            <input type="number" step="0.01" name="luas_lahan_hektar" class="form-control form-control-sm" placeholder="Contoh: 10.5" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Hasil Panen (Ton)</label>
                            <input type="number" step="0.01" name="jumlah_panen_ton" class="form-control form-control-sm" placeholder="Contoh: 25.0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small" style="color: #34483e;">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control form-control-sm" rows="2" placeholder="Catatan hasil panen..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4" style="background-color: #106742; border: none;">Simpan Data Panen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection