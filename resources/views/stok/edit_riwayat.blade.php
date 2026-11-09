@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold mb-0">Edit Riwayat Mutasi Stok</h3>
        <a href="{{ route('stok.riwayat') }}" class="btn btn-secondary">Kembali</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow col-md-8 mx-auto">
        <div class="card-body">
            <div class="mb-3 p-3 bg-light rounded">
                <p class="mb-1"><strong>Provinsi:</strong> {{ $transaksi->stokBenih->provinsi->nama_provinsi ?? '-' }}</p>
                <p class="mb-1"><strong>Benih:</strong> {{ $transaksi->stokBenih->benih->nama_benih ?? '-' }} ({{ $transaksi->stokBenih->benih->varietas ?? '-' }})</p>
                <p class="mb-0"><strong>Jenis Transaksi:</strong> 
                    <span class="badge bg-{{ $transaksi->jenis_transaksi == 'masuk' ? 'success' : 'danger' }}">
                        {{ strtoupper($transaksi->jenis_transaksi) }}
                    </span>
                </p>
            </div>

            <form action="{{ route('stok.riwayat.update', $transaksi->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal Transaksi</label>
                    <input type="date" name="tanggal_transaksi" class="form-control" value="{{ old('tanggal_transaksi', $transaksi->tanggal_transaksi) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Jumlah (Ton)</label>
                    <input type="number" step="0.01" name="jumlah_kg" class="form-control" value="{{ old('jumlah_kg', $transaksi->jumlah_kg) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Sumber / Tujuan</label>
                    <input type="text" name="sumber_tujuan" class="form-control" value="{{ old('sumber_tujuan', $transaksi->sumber_tujuan) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $transaksi->keterangan) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('stok.riwayat') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection