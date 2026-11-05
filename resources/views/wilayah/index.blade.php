@extends('layouts.app')

@section('title', 'Master Wilayah & Gudang BPSIP')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Master Wilayah & Gudang BPSIP</h4>
            <p class="text-muted small mb-0">Kelola pemetaan provinsi, kabupaten, dan gudang persediaan benih nasional.</p>
        </div>
        <button class="btn btn-success text-white" data-bs-toggle="modal" data-bs-target="#modalTambahWilayah">
            <i class="bi bi-plus-circle me-1"></i> Tambah Wilayah / Gudang
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode Wilayah</th>
                            <th>Provinsi</th>
                            <th>Kabupaten/Kota</th>
                            <th>Nama Gudang / Balai</th>
                            <th>Kapasitas (Ton)</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wilayahs as $w)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $w->kode_wilayah }}</td>
                            <td>{{ $w->nama_provinsi }}</td>
                            <td>{{ $w->nama_kabupaten }}</td>
                            <td>{{ $w->nama_gudang }}</td>
                            <td><span class="badge bg-info text-dark">{{ number_format($w->kapasitas_ton, 0, ',', '.') }} Ton</span></td>
                            <td>
                                @if($w->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('wilayah.destroy', $w->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data wilayah ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data wilayah / gudang tersimpan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($wilayahs->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $wilayahs->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahWilayah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('wilayah.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Tambah Wilayah / Gudang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-semibold">Kode Wilayah</label>
                        <input type="text" name="kode_wilayah" class="form-control" placeholder="Contoh: WLY-3201" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Provinsi</label>
                            <input type="text" name="nama_provinsi" class="form-control" placeholder="Jawa Barat" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kabupaten/Kota</label>
                            <input type="text" name="nama_kabupaten" class="form-control" placeholder="Bogor" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Gudang / Balai BPSIP</label>
                        <input type="text" name="nama_gudang" class="form-control" placeholder="Gudang BPSIP Ciawi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kapasitas Maksimal (Ton)</label>
                        <input type="number" name="kapasitas_ton" class="form-control" placeholder="5000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Gudang</label>
                        <textarea name="alamat_gudang" class="form-control" rows="2" placeholder="Jl. Raya Ciawi No. 12..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success text-white">Simpan Wilayah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection