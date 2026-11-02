@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Form Transaksi Stok Keluar / Distribusi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('stok.keluar.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Stok Benih Wilayah</label>
                        <select name="stok_benih_id" class="form-select" required>
                            <option value="">-- Pilih Stok Tersedia --</option>
                            @foreach($stokOptions as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->provinsi->nama_provinsi }} - {{ $s->benih->nama_benih }} (Sisa Stok: {{ number_format($s->jumlah_stok_kg, 2) }} Kg)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tujuan Distribusi / Penerima</label>
                        <input type="text" name="sumber_tujuan" class="form-control" placeholder="Contoh: Poktan Mungkur Jaya / Dinas Banten" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jumlah Dikeluarkan (Kg)</label>
                            <input type="number" step="0.01" name="jumlah_kg" class="form-control" placeholder="Contoh: 100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Tanggal Transaksi</label>
                            <input type="date" name="tanggal_transaksi" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan / Bantuan Alokasi</label>
                        <textarea name="keterangan" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('stok.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-danger">Proses Stok Keluar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection