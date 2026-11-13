@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Edit Master Benih: {{ $benih->nama_benih }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('benih.update', $benih->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Benih</label>
                            <input type="text" name="kode_benih" class="form-control" value="{{ old('kode_benih', $benih->kode_benih) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Benih</label>
                            <input type="text" name="nama_benih" class="form-control" value="{{ old('nama_benih', $benih->nama_benih) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Komoditas</label>
                            <select name="komoditas" class="form-select" required>
                                <option value="padi" {{ strtolower($benih->komoditas) == 'padi' ? 'selected' : '' }}>Padi</option>
                                <option value="jagung" {{ strtolower($benih->komoditas) == 'jagung' ? 'selected' : '' }}>Jagung</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Varietas</label>
                            <input type="text" name="varietas" class="form-control" value="{{ old('varietas', $benih->varietas) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kelas Benih</label>
                            <select name="kelas_benih" class="form-select" required>
                                <option value="FS" {{ in_array(strtoupper($benih->kelas_benih), ['FS', 'BD']) ? 'selected' : '' }}>FS - Benih Dasar (Foundation Seed)</option>
                                <option value="SS" {{ in_array(strtoupper($benih->kelas_benih), ['SS', 'BP']) ? 'selected' : '' }}>SS - Benih Pokok (Stock Seed)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="{{ old('satuan', $benih->satuan) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Minimum Peringatan (Ton)</label>
                            <input type="number" step="0.01" name="stok_minimum" class="form-control" value="{{ old('stok_minimum', $benih->stok_minimum) }}" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('benih.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-warning">Update Master Benih</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection