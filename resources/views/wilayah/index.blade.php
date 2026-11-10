@extends('layouts.app')

@section('title', 'Master Wilayah & Gudang BPSIP - SIPANEN')

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
            <h3 class="fw-bold mb-1" style="color: #106742;">Master Wilayah & Gudang BPSIP</h3>
            <p class="text-muted small mb-0">Kelola pemetaan provinsi, kabupaten/kota, serta unit gudang penyimpanan BPSIP.</p>
        </div>

        <div>
            <button type="button" class="btn btn-success shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahWilayah" style="background-color: #106742; border: none; font-size: 13px; font-weight: 600;">
                + Tambah Wilayah / Gudang
            </button>
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

    <!-- TABEL WILAYAH -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle text-center mb-0" style="font-size: 13px;">
                    <thead style="background-color: #f3f7f5; color: #315b49;">
                        <tr>
                            <th class="py-3">Kode Wilayah</th>
                            <th class="text-start py-3">Provinsi</th>
                            <th class="text-start py-3">Kabupaten/Kota</th>
                            <th class="text-start py-3">Nama Gudang / Balai</th>
                            <th class="py-3">Kapasitas (Ton)</th>
                            <th class="py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wilayahs as $item)
                            <tr>
                                <td><code>{{ $item->kode_wilayah }}</code></td>
                                <td class="text-start fw-bold" style="color: #273b32;">
                                    {{ $item->provinsi->nama_provinsi ?? '-' }}
                                </td>
                                <td class="text-start">{{ $item->kabupaten }}</td>
                                <td class="text-start">{{ $item->nama_gudang }}</td>
                                <td>
                                    <span class="badge px-2 py-1" style="background-color: #0ea5e9; color: #fff; font-size: 11px;">
                                        {{ number_format($item->kapasitas_ton, 0) }} Ton
                                    </span>
                                </td>
                                <td>
                                    <!-- BUTTON EDIT MODAL POPUP -->
                                    <button type="button" class="btn btn-sm btn-outline-warning px-2 py-1 me-1" data-bs-toggle="modal" data-bs-target="#modalEditWilayah{{ $item->id }}" style="font-size: 11px; font-weight: 600;">
                                        Edit
                                    </button>

                                    <form action="{{ route('wilayah.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data wilayah ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger px-2 py-1" style="font-size: 11px; font-weight: 600;">Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- MODAL EDIT WILAYAH PER BARIS DATA -->
                            <div class="modal fade" id="modalEditWilayah{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
                                        <div class="modal-header text-white px-4 py-3" style="background-color: #e67e22;">
                                            <h5 class="modal-title fw-bold fs-6">Edit Data Wilayah / Gudang</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('wilayah.update', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4 text-start">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Kode Wilayah</label>
                                                    <input type="text" name="kode_wilayah" class="form-control form-control-sm" value="{{ $item->kode_wilayah }}" required>
                                                </div>

                                                <div class="row">
                                                    <!-- DROPDOWN PROVINSI -->
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold small" style="color: #34483e;">Provinsi</label>
                                                        <select name="provinsi_id" class="form-select form-select-sm" required>
                                                            <option value="">-- Pilih Provinsi --</option>
                                                            @foreach($provinsis as $prov)
                                                                <option value="{{ $prov->id }}" {{ ($item->provinsi_id == $prov->id) ? 'selected' : '' }}>
                                                                    {{ $prov->nama_provinsi }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold small" style="color: #34483e;">Kabupaten/Kota</label>
                                                        <input type="text" name="kabupaten" class="form-control form-control-sm" value="{{ $item->kabupaten }}" required>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Nama Gudang / Balai BPSIP</label>
                                                    <input type="text" name="nama_gudang" class="form-control form-control-sm" value="{{ $item->nama_gudang }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small" style="color: #34483e;">Kapasitas Maksimal (Ton)</label>
                                                    <input type="number" step="0.01" name="kapasitas_ton" class="form-control form-control-sm" value="{{ $item->kapasitas_ton }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small" style="color: #34483e;">Alamat Gudang</label>
                                                    <textarea name="alamat" class="form-control form-control-sm" rows="2">{{ $item->alamat }}</textarea>
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
                                <td colspan="6" class="py-4 text-muted">Belum ada data wilayah / gudang BPSIP.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- PAGINATION -->
    @if ($wilayahs->hasPages())
        <div class="d-flex justify-content-center mt-3 mb-4">
            {{ $wilayahs->links() }}
        </div>
    @endif

</div>

<!-- MODAL TAMBAH WILAYAH BARU -->
<div class="modal fade" id="modalTambahWilayah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white px-4 py-3" style="background-color: #106742;">
                <h5 class="modal-title fw-bold fs-6">Tambah Wilayah / Gudang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('wilayah.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Kode Wilayah</label>
                        <input type="text" name="kode_wilayah" class="form-control form-control-sm" placeholder="Contoh: WLY-3201" required>
                    </div>

                    <div class="row">
                        <!-- DROPDOWN PROVINSI -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Provinsi</label>
                            <select name="provinsi_id" class="form-select form-select-sm" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinsis as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->nama_provinsi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small" style="color: #34483e;">Kabupaten/Kota</label>
                            <input type="text" name="kabupaten" class="form-control form-control-sm" placeholder="Contoh: Bogor" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Nama Gudang / Balai BPSIP</label>
                        <input type="text" name="nama_gudang" class="form-control form-control-sm" placeholder="Contoh: Gudang BPSIP Ciawi" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small" style="color: #34483e;">Kapasitas Maksimal (Ton)</label>
                        <input type="number" step="0.01" name="kapasitas_ton" class="form-control form-control-sm" placeholder="Contoh: 5000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small" style="color: #34483e;">Alamat Gudang</label>
                        <textarea name="alamat" class="form-control form-control-sm" rows="2" placeholder="Jl. Raya Ciawi No. 12..."></textarea>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" style="background-color: #106742; border: none;">Simpan Wilayah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection