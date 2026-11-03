@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 font-weight-bold text-primary">Edit Pengujian Mutu Benih</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pengujian.update', $pengujianMutu->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Varietas Benih</label>
                            <select name="benih_id" class="form-select" required>
                                <option value="">-- Pilih Varietas Benih --</option>
                                @foreach($benihs as $b)
                                    <option value="{{ $b->id }}" {{ $pengujianMutu->benih_id == $b->id ? 'selected' : '' }}>
                                        {{ $b->kode_benih }} - {{ $b->nama_benih }} ({{ $b->varietas }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Kadar Air (%)</label>
                            <input type="number" step="0.01" name="kadar_air_persen" value="{{ old('kadar_air_persen', $pengujianMutu->kadar_air_persen) }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Daya Tumbuh (%)</label>
                            <input type="number" step="0.01" name="daya_tumbuh_persen" value="{{ old('daya_tumbuh_persen', $pengujianMutu->daya_tumbuh_persen) }}" class="form-control" required>
                        </div>

                        <!-- INI BAGIAN YANG KURANG TADI: ATRIBUT NAME JUMLAH_LOLOS_KG -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jumlah Benih Lolos Seleksi (Kg)</label>
                            <input type="number" step="0.01" name="jumlah_lolos_kg" value="{{ old('jumlah_lolos_kg', $pengujianMutu->hasil_lolos_kg) }}" class="form-control" required>
                        </div>

                        <<div class="mb-3">
                            <label class="form-label fw-bold">Status Mutu</label>
                            <select name="status" class="form-select" required>
                                <option value="PROSES" {{ $pengujianMutu->status_kelayakan == 'proses' ? 'selected' : '' }}>PROSES</option>
                                <option value="LAYAK (STOK MASUK)" {{ $pengujianMutu->status_kelayakan == 'layak' ? 'selected' : '' }}>LAYAK (STOK MASUK)</option>
                                <option value="TIDAK LAYAK" {{ $pengujianMutu->status_kelayakan == 'tidak_layak' ? 'selected' : '' }}>TIDAK LAYAK</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('pengujian.index') }}" class="text-secondary text-decoration-none">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection