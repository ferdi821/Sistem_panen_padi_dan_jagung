@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Form Pengujian Mutu & Konversi Panen ke Stok Benih</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('pengujian.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Data Hasil Panen</label>
                        <select name="catatan_panen_id" class="form-select" required>
                            <option value="">-- Pilih Record Hasil Panen --</option>
                            @foreach($catatanPanens as $p)
                                <option value="{{ $p->id }}">
                                    [ID: {{ $p->id }}] {{ $p->provinsi->nama_provinsi }} - {{ strtoupper($p->komoditas) }} ({{ $p->jumlah_panen_ton }} Ton) - Tgl: {{ $p->tanggal_panen }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Varietas Benih Hasil Konversi</label>
                        <select name="benih_id" class="form-select" required>
                            <option value="">-- Pilih Varietas Benih --</option>
                            @foreach($benihs as $b)
                                <option value="{{ $b->id }}">{{ $b->kode_benih }} - {{ $b->nama_benih }} ({{ $b->varietas }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Pengujian Lab</label>
                            <input type="date" name="tanggal_uji" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Volume Sampel Diuji (Kg)</label>
                            <input type="number" step="0.01" name="volume_sampel_kg" class="form-control" placeholder="Contoh: 10" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kadar Air (%)</label>
                            <input type="number" step="0.1" name="kadar_air_persen" class="form-control" placeholder="Contoh: 12.5" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Daya Tumbuh (%)</label>
                            <input type="number" step="0.1" name="daya_tumbuh_persen" class="form-control" placeholder="Contoh: 85" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status Kelayakan</label>
                            <select name="status_kelayakan" class="form-select" required>
                                <option value="layak">LAYAK (Otomatis Tambah Stok Benih)</option>
                                <option value="tidak_layak">TIDAK LAYAK</option>
                                <option value="proses">DALAM PROSES</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jumlah Benih Lolos Seleksi (Kg)</label>
                            <input type="number" step="0.01" name="jumlah_lolos_kg" class="form-control" placeholder="Contoh: 500" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Pengujian</label>
                        <textarea name="catatan_pengujian" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('pengujian.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-success">Simpan & Diproses</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection