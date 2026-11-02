@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Input Data Hasil Panen</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('panen.store') }}" method="POST">
                        @csrf

                        {{-- Pilihan / Tampilan Provinsi --}}
                        @if(auth()->user()->hasRole('admin_pusat'))
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Pilih Provinsi</label>
                                <select name="provinsi_id" class="form-select @error('provinsi_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Provinsi --</option>
                                    @foreach($provinsis as $prov)
                                        <option value="{{ $prov->id }}" {{ old('provinsi_id') == $prov->id ? 'selected' : '' }}>
                                            {{ $prov->nama_provinsi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('provinsi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label font-weight-bold">Provinsi</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->provinsi?->nama_provinsi ?? '-' }}" readonly>
                                <input type="hidden" name="provinsi_id" value="{{ auth()->user()->provinsi_id }}">
                            </div>
                        @endif

                        {{-- Komoditas --}}
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Komoditas</label>
                            <select name="komoditas" class="form-select @error('komoditas') is-invalid @enderror" required>
                                <option value="padi" {{ old('komoditas') == 'padi' ? 'selected' : '' }}>Padi</option>
                                <option value="jagung" {{ old('komoditas') == 'jagung' ? 'selected' : '' }}>Jagung</option>
                            </select>
                            @error('komoditas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Panen --}}
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Tanggal Panen</label>
                            <input type="date" name="tanggal_panen" class="form-control @error('tanggal_panen') is-invalid @enderror" value="{{ old('tanggal_panen', date('Y-m-d')) }}" required>
                            @error('tanggal_panen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Luas Lahan (Ha) --}}
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Luas Lahan (Ha)</label>
                            <input type="number" step="0.01" name="luas_lahan_hektar" class="form-control @error('luas_lahan_hektar') is-invalid @enderror" value="{{ old('luas_lahan_hektar') }}" placeholder="Contoh: 10.5" required>
                            @error('luas_lahan_hektar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jumlah Hasil (Ton) --}}
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Hasil Panen (Ton)</label>
                            <input type="number" step="0.01" name="jumlah_panen_ton" class="form-control @error('jumlah_panen_ton') is-invalid @enderror" value="{{ old('jumlah_panen_ton') }}" placeholder="Contoh: 25.0" required>
                            @error('jumlah_panen_ton')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Catatan (Opsional) --}}
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="2" placeholder="Catatan tambahan lokasi / kondisi panen">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('panen.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-success">Simpan Data Panen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection