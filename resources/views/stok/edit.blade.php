@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Edit Stok Benih Wilayah</h3>
        <a href="{{ route('stok.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow col-md-8 mx-auto">
        <div class="card-body">
            <form action="{{ route('stok.update', $stok->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Provinsi</label>
                    <select name="provinsi_id" class="form-select @error('provinsi_id') is-invalid @enderror" required>
                        @foreach($provinsis as $provinsi)
                            <option value="{{ $provinsi->id }}" {{ $stok->provinsi_id == $provinsi->id ? 'selected' : '' }}>
                                {{ $provinsi->nama_provinsi }}
                            </option>
                        @endforeach
                    </select>
                    @error('provinsi_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Benih</label>
                    <select name="benih_id" class="form-select @error('benih_id') is-invalid @enderror" required>
                        @foreach($benihs as $benih)
                            <option value="{{ $benih->id }}" {{ $stok->benih_id == $benih->id ? 'selected' : '' }}>
                                [{{ $benih->kode_benih }}] {{ $benih->nama_benih }} - {{ $benih->varietas }} ({{ strtoupper($benih->komoditas) }})
                            </option>
                        @endforeach
                    </select>
                    @error('benih_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Jumlah Stok Tersedia (Kg)</label>
                    <input type="number" step="0.01" name="jumlah_stok_kg" class="form-class form-control @error('jumlah_stok_kg') is-invalid @enderror" value="{{ old('jumlah_stok_kg', $stok->jumlah_stok_kg) }}" required>
                    @error('jumlah_stok_kg')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('stok.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection