@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Edit Stok Benih Wilayah</h3>
        <a href="{{ route('stok.index') }}" class="btn btn-secondary">Kembali</a>
    </div>

    <div class="card shadow col-md-8 mx-auto" style="border-radius: 14px;">
        <div class="card-body p-4">
            <form action="{{ route('stok.update', $stok->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Provinsi</label>
                    <select name="provinsi_id" class="form-select" required>
                        @foreach($provinsis as $p)
                            <option value="{{ $p->id }}" {{ $stok->provinsi_id == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_provinsi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Benih</label>
                    <select name="benih_id" class="form-select" required>
                        @foreach($benihs as $b)
                            <option value="{{ $b->id }}" {{ $stok->benih_id == $b->id ? 'selected' : '' }}>
                                [{{ $b->kode_benih }}] {{ $b->nama_benih }} - {{ $b->varietas }} ({{ strtoupper($b->komoditas) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- LABEL DISUDAH DIUBAH KE TON -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Jumlah Stok Tersedia (Ton)</label>
                    <input type="number" step="0.01" name="jumlah_stok_kg" class="form-control" value="{{ old('jumlah_stok_kg', $stok->jumlah_stok_kg) }}" required>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('stok.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection