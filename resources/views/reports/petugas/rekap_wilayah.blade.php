@extends('layouts.app')

@section('title', 'Rekapitulasi Stok Benih Wilayah - Excel BBPSIP')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_green.css">

<style>
    .table-rekap-excel th, .table-rekap-excel td {
        border-color: #a0a0a0 !important;
    }
    .border-thick-bottom {
        border-bottom: 1.5px solid #212529 !important;
    }
    .border-thick-right {
        border-right: 1.5px solid #212529 !important;
    }
</style>

<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1" style="color: #106742;">Rekapitulasi Stok Benih - {{ $namaProvinsi }}</h4>
            <p class="text-muted mb-0 small">Monitoring rincian stok per Kabupaten/Kota & Varietas spesifik format Excel BBPSIP.</p>
        </div>
        <div>
            <a href="{{ route('petugas.report.rekap.pdf', request()->all()) }}" target="_blank" class="btn btn-danger btn-sm shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Cetak PDF Wilayah
            </a>
        </div>
    </div>

    <!-- Filter Form Direct Date Range Picker Kalender -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius: 10px;">
        <div class="card-body p-2">
            <form action="{{ route('petugas.report.rekap') }}" method="GET" class="row g-2 align-items-center">
                
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0" id="btnCalendarIcon" style="cursor: pointer;">
                            <i class="bi bi-calendar-range text-success"></i>
                        </span>
                        <input type="text" id="rangePickerPetugas" class="form-control bg-white border-start-0" placeholder="Pilih Rentang Tanggal (Kalender)..." readonly style="cursor: pointer;">
                        <input type="hidden" name="start_date" id="startDatePetugas" value="{{ $start->format('Y-m-d') }}">
                        <input type="hidden" name="end_date" id="endDatePetugas" value="{{ $end->format('Y-m-d') }}">
                    </div>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-success px-3" style="background-color: #106742; border: none;">
                        <i class="bi bi-funnel-fill me-1"></i> Filter Laporan
                    </button>
                    @if(request()->has('start_date'))
                        <a href="{{ route('petugas.report.rekap') }}" class="btn btn-sm btn-outline-secondary px-2">Reset</a>
                    @endif
                </div>

                <div class="col-auto ms-auto">
                    <span class="badge bg-light text-dark border py-2 px-3">
                        <i class="bi bi-clock me-1 text-muted"></i> Periode Aktif: {{ $start->format('d M Y') }} s.d {{ $end->format('d M Y') }}
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Matriks Per Kabupaten -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 75vh;">
                <table class="table table-bordered table-hover align-middle mb-0 text-center table-rekap-excel" style="font-size: 0.76rem;">
                    <thead class="align-middle text-nowrap" style="background-color: #f8f9fa;">
                        <tr class="fw-bold">
                            <th rowspan="3" width="30px" class="bg-light">No</th>
                            <th rowspan="3" width="180px" class="bg-light text-start">Kabupaten / Kota</th>
                            <th colspan="4" class="table-primary border-thick-bottom border-thick-right">Target (Ton)</th>
                            <th rowspan="3" width="70px" class="bg-secondary text-white border-thick-right">Total</th>
                            <th rowspan="3" width="70px" class="bg-warning text-dark border-thick-right">Kontrol</th>
                            <th colspan="4" class="table-success border-thick-bottom">Sudah Terdistribusi</th>
                            <th colspan="4" class="table-info border-thick-bottom">Benih Tersedia</th>
                            <th colspan="4" class="table-light border-thick-bottom">Benih (Prosesing)</th>
                            <th colspan="6" class="table-warning border-thick-bottom">Benih (Persiapan Panen)</th>
                        </tr>
                        <tr class="fw-bold">
                            <th colspan="2" class="border-thick-bottom">FS</th>
                            <th colspan="2" class="border-thick-bottom border-thick-right">SS</th>
                            <th colspan="2" class="border-thick-bottom">FS</th><th colspan="2" class="border-thick-bottom">SS</th>
                            <th colspan="2" class="border-thick-bottom">FS</th><th colspan="2" class="border-thick-bottom">SS</th>
                            <th colspan="2" class="border-thick-bottom">FS</th><th colspan="2" class="border-thick-bottom">SS</th>
                            <th colspan="3" class="border-thick-bottom">FS</th><th colspan="3" class="border-thick-bottom">SS</th>
                        </tr>
                        <tr class="fw-bold text-muted small">
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th class="border-thick-right">Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Volume (Ton)</th><th>Varietas</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Tanggal Panen</th>
                            <th>Volume (Ton)</th><th>Varietas</th><th>Tanggal Panen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapData as $index => $kabData)
                            @foreach($kabData['rows'] as $rIdx => $row)
                            <tr>
                                @if($rIdx === 0)
                                    <td rowspan="{{ $kabData['rowspan'] }}" class="align-middle bg-white">{{ $index + 1 }}</td>
                                    <td rowspan="{{ $kabData['rowspan'] }}" class="text-start fw-bold text-dark text-nowrap align-middle bg-white">{{ $kabData['satker'] }}</td>
                                @endif

                                <td class="fw-bold">{{ number_format((float)$row['target_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['target_fs_var'] }}</td>
                                <td class="fw-bold">{{ number_format((float)$row['target_ss_vol'], 2) }}</td>
                                <td class="text-start small border-thick-right">{{ $row['target_ss_var'] }}</td>

                                @if($rIdx === 0)
                                    <td rowspan="{{ $kabData['rowspan'] }}" class="bg-secondary bg-opacity-10 fw-bold align-middle border-thick-right">{{ number_format((float)$kabData['total'], 2) }}</td>
                                    <td rowspan="{{ $kabData['rowspan'] }}" class="bg-warning bg-opacity-10 fw-bold text-danger align-middle border-thick-right">{{ number_format((float)$kabData['kontrol'], 2) }}</td>
                                @endif

                                <td class="fw-bold text-success">{{ number_format((float)$row['distribusi_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['distribusi_fs_var'] }}</td>
                                <td class="fw-bold text-success">{{ number_format((float)$row['distribusi_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['distribusi_ss_var'] }}</td>

                                <td class="fw-bold text-primary">{{ number_format((float)$row['tersedia_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['tersedia_fs_var'] }}</td>
                                <td class="fw-bold text-primary">{{ number_format((float)$row['tersedia_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['tersedia_ss_var'] }}</td>

                                <td>{{ number_format((float)$row['prosesing_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['prosesing_fs_var'] }}</td>
                                <td>{{ number_format((float)$row['prosesing_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['prosesing_ss_var'] }}</td>

                                <td class="fw-bold">{{ number_format((float)$row['persiapan_fs_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['persiapan_fs_var'] }}</td>
                                <td class="small text-muted">{{ $row['persiapan_fs_tgl'] }}</td>

                                <td class="fw-bold">{{ number_format((float)$row['persiapan_ss_vol'], 2) }}</td>
                                <td class="text-start small">{{ $row['persiapan_ss_var'] }}</td>
                                <td class="small text-muted">{{ $row['persiapan_ss_tgl'] }}</td>
                            </tr>
                            @endforeach
                        @empty
                        <tr>
                            <td colspan="28" class="py-4 text-muted">Belum ada data rekapitulasi wilayah pada periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
(function initPetugasDatePicker() {
    function setup() {
        const pickerEl = document.getElementById('rangePickerPetugas');
        const startEl  = document.getElementById('startDatePetugas');
        const endEl    = document.getElementById('endDatePetugas');
        const iconEl   = document.getElementById('btnCalendarIcon');

        if (!pickerEl || typeof flatpickr === 'undefined') return;

        const fp = flatpickr(pickerEl, {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [startEl.value, endEl.value],
            clickOpens: true,
            allowInput: false,
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 1) {
                    const singleDate = instance.formatDate(selectedDates[0], "Y-m-d");
                    startEl.value = singleDate;
                    endEl.value   = singleDate;
                } else if (selectedDates.length === 2) {
                    startEl.value = instance.formatDate(selectedDates[0], "Y-m-d");
                    endEl.value   = instance.formatDate(selectedDates[1], "Y-m-d");
                }
            }
        });

        if (iconEl) {
            iconEl.addEventListener('click', function() {
                fp.open();
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setup);
    } else {
        setup();
    }
})();
</script>
@endsection