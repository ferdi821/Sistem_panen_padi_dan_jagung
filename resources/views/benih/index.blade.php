@extends('layouts.app')

@section('title', 'Master Data Varietas & Komoditas - SIPANEN')

@section('content')
<style>
    #main-wrapper {
        min-height: auto !important;
    }
    main.flex-grow-1 {
        flex-grow: 0 !important;
    }
    /* Styling Pagination Bootstrap */
    .pagination {
        margin-bottom: 0;
    }
    .pagination .page-item .page-link {
        color: #106742;
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 6px;
        margin: 0 2px;
    }
    .pagination .page-item.active .page-link {
        background-color: #106742;
        border-color: #106742;
        color: #fff;
    }
</style>

<div class="container-fluid px-4 py-3">

    <!-- HEADER HALAMAN -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1" style="color: #106742;">Master Data Varietas & Komoditas</h3>
            <p class="text-muted small mb-0">Kelola data kode benih, komoditas, varietas, dan batas minimum stok.</p>
        </div>

        @role('petugas_provinsi')
            <div>
                <button type="button" class="btn btn-success shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahBenih" style="background-color: #106742; border: none; font-size: 13px; font-weight: 600;">
                    + Tambah Varietas Baru
                </button>
            </div>
        @endrole
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
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- TABEL BENIH -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0" style="font-size: 13px;">
                    <thead style="background-color: #f3f7f5; color: #315b49;">
                        <tr>
                            <th class="py-3">Kode</th>
                            <th class="text-start py-3">Nama Benih</th>
                            <th class="py-3">Komoditas</th>
                            <th class="py-3">Varietas</th>
                            <th class="py-3">Stok Min (Ton)</th>
                            @role('petugas_provinsi')
                                <th class="py-3">Aksi</th>
                            @endrole
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($benihs as $benih)
                            <tr>
                                <td><code>{{ $benih->kode_benih }}</code></td>
                                <td class="text-start fw-bold" style="color: #273b32;">{{ $benih->nama_benih }}</td>
                                <td>
                                    @if(strtolower($benih->komoditas) === 'padi')
                                        <span class="badge px-2 py-1" style="background-color: #d9f2e3; color: #106742; font-size: 10px; font-weight: 700;">PADI</span>
                                    @else
                                        <span class="badge px-2 py-1" style="background-color: #fff0c2; color: #956600; font-size: 10px; font-weight: 700;">JAGUNG</span>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $benih->varietas }}</td>
                                <td class="fw-bold text-dark">{{ number_format($benih->stok_minimum, 2) }} Ton</td>
                                @role('petugas_provinsi')
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-warning px-2 py-1 me-1" data-bs-toggle="modal" data-bs-target="#modalEditBenih{{ $benih->id }}" style="font-size: 11px; font-weight: 600;">
                                            Edit
                                        </button>

                                        <form action="{{ route('benih.destroy', $benih->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus varietas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" style="font-size: 11px; font-weight: 600;">Hapus</button>
                                        </form>
                                    </td>
                                @endrole
                            </tr>

                            @role('petugas_provinsi')
                                <!-- MODAL EDIT BENIH -->
                                <div class="modal fade" id="modalEditBenih{{ $benih->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                                            <div class="modal-header text-white px-4 py-3" style="background-color: #e67e22;">
                                                <h5 class="modal-title fw-bold fs-6">Edit Data Varietas</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('benih.update', $benih->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4 text-start">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small" style="color: #34483e;">Kode Benih</label>
                                                        <input type="text" name="kode_benih" class="form-control form-control-sm" value="{{ $benih->kode_benih }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small" style="color: #34483e;">Nama Benih</label>
                                                        <input type="text" name="nama_benih" class="form-control form-control-sm" value="{{ $benih->nama_benih }}" required>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold small" style="color: #34483e;">Komoditas</label>
                                                            <select name="komoditas" class="form-select form-select-sm" required>
                                                                <option value="padi" {{ strtolower($benih->komoditas) == 'padi' ? 'selected' : '' }}>Padi</option>
                                                                <option value="jagung" {{ strtolower($benih->komoditas) == 'jagung' ? 'selected' : '' }}>Jagung</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold small" style="color: #34483e;">Varietas</label>
                                                            <input type="text" name="varietas" class="form-control form-control-sm" value="{{ $benih->varietas }}" required>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small" style="color: #34483e;">Stok Min Peringatan (Ton)</label>
                                                        <input type="number" step="0.01" min="0" name="stok_minimum" class="form-control form-control-sm" value="{{ $benih->stok_minimum }}" required>
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
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasRole('petugas_provinsi') ? 6 : 5 }}" class="py-4 text-muted">Belum ada data master varietas benih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PAGINATION -->
    @if ($benihs->hasPages())
        <div class="d-flex justify-content-center mt-3 mb-4">
            {{ $benihs->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>

@role('petugas_provinsi')
    <!-- MODAL TAMBAH BENIH BARU -->
    <div class="modal fade" id="modalTambahBenih" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header text-white px-4 py-3" style="background-color: #106742;">
                    <h5 class="modal-title fw-bold fs-6">Tambah Varietas Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('benih.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 text-start">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Kode Benih</label>
                            <input type="text" name="kode_benih" class="form-control form-control-sm" placeholder="Contoh: bnh-pdi-003" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Nama Benih</label>
                            <input type="text" name="nama_benih" class="form-control form-control-sm" placeholder="Contoh: Benih Padi Ciherang" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Komoditas</label>
                                <select name="komoditas" class="form-select form-select-sm" required>
                                    <option value="">-- Pilih Komoditas --</option>
                                    <option value="padi">Padi</option>
                                    <option value="jagung">Jagung</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Varietas</label>
                                <input type="text" name="varietas" class="form-control form-control-sm" placeholder="Contoh: Ciherang / Bisi" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Stok Min Peringatan (Ton)</label>
                            <input type="number" step="0.01" min="0" name="stok_minimum" class="form-control form-control-sm" placeholder="Contoh: 0.10" value="{{ old('stok_minimum', '0.10') }}" required>
                        </div>

                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" style="background-color: #106742; border: none;">Simpan Varietas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endrole
@endsection