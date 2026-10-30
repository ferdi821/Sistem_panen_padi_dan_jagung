@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard Ringkasan Panen</h2>
    <div>
        <a href="{{ route('panen.index') }}" class="btn btn-primary">Lihat Data Panen</a>
        @if(in_array(auth()->user()->role, ['admin_pusat', 'petugas_provinsi']))
            <a href="{{ route('panen.create') }}" class="btn btn-success">+ Input Hasil Panen</a>
        @endif
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-success shadow">
            <div class="card-body">
                <h5 class="card-title">Total Produksi Padi</h5>
                <h2 class="card-text">{{ number_format($totalPadi, 2) }} Ton</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning shadow">
            <div class="card-body">
                <h5 class="card-title">Total Produksi Jagung</h5>
                <h2 class="card-text">{{ number_format($totalJagung, 2) }} Ton</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info shadow">
            <div class="card-body">
                <h5 class="card-title">Total Luas Lahan</h5>
                <h2 class="card-text">{{ number_format($totalLuasLahan, 2) }} Ha</h2>
            </div>
        </div>
    </div>
</div>

@if($ringkasanProvinsi)
<div class="card shadow">
    <div class="card-header bg-white">
        <h5 class="mb-0">Rekapitulasi Produksi per Provinsi</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Provinsi</th>
                    <th>Padi (Ton)</th>
                    <th>Jagung (Ton)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ringkasanProvinsi as $prov)
                <tr>
                    <td>{{ $prov->kode_provinsi }}</td>
                    <td>{{ $prov->nama_provinsi }}</td>
                    <td>{{ number_format($prov->total_padi ?? 0, 2) }}</td>
                    <td>{{ number_format($prov->total_jagung ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
