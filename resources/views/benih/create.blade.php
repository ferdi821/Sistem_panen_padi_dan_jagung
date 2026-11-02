@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Tambah Master Benih Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('benih.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Benih</label>
                            <input type="text" name="kode_benih" class="form-control @error('kode_benih') is-invalid @enderror" value="{{ old('kode_benih') }}" placeholder="Contoh: BNH-PDI-001" required>
                            @error('kode_benih')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Benih</label>
                            <input type="text" name="nama_benih" class="form-control @error('nama_benih') is-invalid @enderror" value="{{ old('nama_benih') }}" placeholder="Contoh: Benih Padi Inpari 32" required>
                            @error('nama_benih')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Komoditas</label>
                            <select name="komoditas" class="form-select" required>
                                <option value="padi">Padi</option>
                                <option value="jagung">Jagung</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Varietas</label>
                            <input type="text" name="varietas" class="form-control" placeholder="Contoh: Ciherang / Bisi 18" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kelas Benih</label>
                            <select name="kelas_benih" class="form-select" required>
                                <option value="BR">BR (Benih Sebar)</option>
                                <option value="BP">BP (Benih Pokok)</option>
                                <option value="BD">BD (Benih Dasar)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="kg" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Minimum Peringatan (Kg)</label>
                            <input type="number" step="0.01" name="stok_minimum" class="form-control" value="100" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi / Spesifikasi</label>
                        <textarea name="deskripsi" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('benih.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">Simpan Master Benih</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection