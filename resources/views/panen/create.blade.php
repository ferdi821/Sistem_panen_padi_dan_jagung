@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Input Catatan Panen Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('panen.store') }}" method="POST">
                    @csrf

                    @if(auth()->user()->role === 'admin_pusat')
                    <div class="mb-3">
                        <label class="form-label">Provinsi</label>
                        <select name="provinsi_id" class="form-select" required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinsis as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->nama_provinsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="mb-3">
                        <label class="form-label">Provinsi</label>
                        <input type="text" class="form-control" value="{{ auth()->user()->provinsi->nama_provinsi }}" readonly>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Komoditas</label>
                        <select name="komoditas" class="form-select" required>
                            <option value="padi">Padi</option>
                            <option value="jagung">Jagung</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tanggal Panen</label>
                        <input type="date" name="tanggal_panen" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Luas Lahan (Hektar)</label>
                        <input type="number" step="0.01" name="luas_lahan_hektar" class="form-control" placeholder="Contoh: 12.5" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hasil Panen (Ton)</label>
                        <input type="number" step="0.01" name="jumlah_panen_ton" class="form-control" placeholder="Contoh: 45.2" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Tambahan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('panen.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">Simpan Data Panen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
