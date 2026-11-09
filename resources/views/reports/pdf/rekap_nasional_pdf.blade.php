<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Stok Benih Nasional</title>
    <style>
        @page { margin: 5mm; }
        body { font-family: sans-serif; font-size: 5.5pt; color: #111; }
        
        /* Header Grid Table */
        .header-table { width: 100%; border: none; margin-bottom: 6px; }
        .header-table td { border: none; padding: 0; }
        .title-main { color: #106742; font-size: 9.5pt; font-weight: bold; text-transform: uppercase; }
        .sub-title { font-size: 6.5pt; color: #444; margin-top: 2px; }
        .print-date { text-align: right; font-size: 6.5pt; color: #555; vertical-align: bottom; }

        table.data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.data-table th, table.data-table td { border: 0.4px solid #333; padding: 2px 1px; text-align: center; word-wrap: break-word; overflow: hidden; }
        table.data-table th { background-color: #e8f5e9; font-size: 5pt; font-weight: bold; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .bg-total { background-color: #eeeeee; }
        .bg-kontrol { background-color: #fff9c4; }
    </style>
</head>
<body>

    <!-- Header Informasi dengan Tanggal Cetak Sebelah Kanan -->
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="title-main">REKAPITULASI STOK BENIH LINGKUP BBPSIP (34 PROVINSI)</div>
                <div class="sub-title">Periode: {{ $start->format('d M Y') }} s.d {{ $end->format('d M Y') }}</div>
            </td>
            <td style="width: 30%;" class="print-date">
                Tanggal Cetak: <strong>{{ date('d-m-Y H:i') }} WIB</strong>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="3" style="width: 2%;">No</th>
                <th rowspan="3" style="width: 9%;">Satker</th>
                <th colspan="4" style="width: 14%;">Target (Ton)</th>
                <th rowspan="3" style="width: 4%;">Total</th>
                <th rowspan="3" style="width: 4%;">Kontrol</th>
                <th colspan="4" style="width: 14%;">Sudah Terdistribusi</th>
                <th colspan="4" style="width: 14%;">Benih Tersedia</th>
                <th colspan="4" style="width: 14%;">Benih (Prosesing)</th>
                <th colspan="6" style="width: 25%;">Benih (Persiapan Panen)</th>
            </tr>
            <tr>
                <th colspan="2">FS</th><th colspan="2">SS</th>
                <th colspan="2">FS</th><th colspan="2">SS</th>
                <th colspan="2">FS</th><th colspan="2">SS</th>
                <th colspan="2">FS</th><th colspan="2">SS</th>
                <th colspan="3">FS</th><th colspan="3">SS</th>
            </tr>
            <tr>
                <th style="width: 3%;">Vol</th><th style="width: 4%;">Var</th><th style="width: 3%;">Vol</th><th style="width: 4%;">Var</th>
                <th style="width: 3%;">Vol</th><th style="width: 4%;">Var</th><th style="width: 3%;">Vol</th><th style="width: 4%;">Var</th>
                <th style="width: 3%;">Vol</th><th style="width: 4%;">Var</th><th style="width: 3%;">Vol</th><th style="width: 4%;">Var</th>
                <th style="width: 3%;">Vol</th><th style="width: 4%;">Var</th><th style="width: 3%;">Vol</th><th style="width: 4%;">Var</th>
                <th style="width: 3%;">Vol</th><th style="width: 4%;">Var</th><th style="width: 5.5%;">Tgl Panen</th>
                <th style="width: 3%;">Vol</th><th style="width: 4%;">Var</th><th style="width: 5.5%;">Tgl Panen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $index => $satkerData)
                @foreach($satkerData['rows'] as $rIdx => $row)
                <tr>
                    @if($rIdx === 0)
                        <td rowspan="{{ $satkerData['rowspan'] }}">{{ $index + 1 }}</td>
                        <td rowspan="{{ $satkerData['rowspan'] }}" class="text-start fw-bold">{{ $satkerData['satker'] }}</td>
                    @endif

                    <td>{{ number_format((float)$row['target_fs_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['target_fs_var'] }}</td>
                    <td>{{ number_format((float)$row['target_ss_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['target_ss_var'] }}</td>

                    @if($rIdx === 0)
                        <td rowspan="{{ $satkerData['rowspan'] }}" class="bg-total fw-bold">{{ number_format((float)$satkerData['total'], 2) }}</td>
                        <td rowspan="{{ $satkerData['rowspan'] }}" class="bg-kontrol fw-bold">{{ number_format((float)$satkerData['kontrol'], 2) }}</td>
                    @endif

                    <td>{{ number_format((float)$row['distribusi_fs_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['distribusi_fs_var'] }}</td>
                    <td>{{ number_format((float)$row['distribusi_ss_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['distribusi_ss_var'] }}</td>

                    <td>{{ number_format((float)$row['tersedia_fs_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['tersedia_fs_var'] }}</td>
                    <td>{{ number_format((float)$row['tersedia_ss_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['tersedia_ss_var'] }}</td>

                    <td>0.00</td><td>-</td><td>0.00</td><td>-</td>

                    <td>{{ number_format((float)$row['persiapan_fs_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['persiapan_fs_var'] }}</td>
                    <td>{{ $row['persiapan_fs_tgl'] }}</td>

                    <td>{{ number_format((float)$row['persiapan_ss_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['persiapan_ss_var'] }}</td>
                    <td>{{ $row['persiapan_ss_tgl'] }}</td>
                </tr>
                @endforeach
            @empty
            <tr>
                <td colspan="28">Belum ada data stok benih.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>