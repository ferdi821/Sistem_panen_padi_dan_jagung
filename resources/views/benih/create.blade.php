@extends('layouts.app')

@section('title', 'Tambah Master Benih - SIPANEN')

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
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header text-white px-4 py-3" style="background-color: #106742;">
                    <h5 class="mb-0 fw-bold fs-6">Tambah Master Benih Baru</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('benih.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Kode Benih</label>
                                <input type="text" name="kode_benih" class="form-control form-control-sm" placeholder="Contoh: 002" value="{{ old('kode_benih') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Nama Benih</label>
                                <input type="text" name="nama_benih" class="form-control form-control-sm" placeholder="Contoh: benih jagung hibrida" value="{{ old('nama_benih') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Komoditas</label>
                                <select name="komoditas" class="form-select form-select-sm" required>
                                    <option value="padi">Padi</option>
                                    <option value="jagung">Jagung</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Varietas</label>
                                <input type="text" name="varietas" class="form-control form-control-sm" placeholder="Contoh: Ciherang / Bisi 18" value="{{ old('varietas') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Kelas Benih</label>
                                <select name="kelas_benih" class="form-select form-select-sm" required>
                                    <option value="BR">BR (Benih Sebar)</option>
                                    <option value="BP">BP (Benih Pokok)</option>
                                    <option value="BD">BD (Benih Dasar)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Satuan</label>
                                <input type="text" name="satuan" class="form-control form-control-sm bg-light" value="Ton" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small" style="color: #34483e;">Stok Minimum Peringatan (Ton)</label>
                                <input type="number" step="0.01" name="stok_minimum" class="form-control form-control-sm" placeholder="Contoh: 0.10" value="{{ old('stok_minimum', '0.10') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small" style="color: #34483e;">Deskripsi / Spesifikasi (Opsional)</label>
                            <textarea name="deskripsi" class="form-control form-control-sm" rows="2" placeholder="Keterangan spesifikasi benih...">{{ old('deskripsi') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between pt-2">
                            <a href="{{ route('benih.index') }}" class="btn btn-secondary btn-sm px-3">Batal</a>
                            <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" style="background-color: #106742; border: none;">Simpan Master Benih</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection