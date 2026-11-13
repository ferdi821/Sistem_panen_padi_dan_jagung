<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Stok Benih Wilayah</title>
    <style>
        @page { margin: 5mm; }
        body { font-family: sans-serif; font-size: 5pt; color: #111; }
        
        .header-table { width: 100%; border: none; margin-bottom: 6px; }
        .header-table td { border: none; padding: 0; }
        .title-main { color: #106742; font-size: 9.5pt; font-weight: bold; text-transform: uppercase; }
        .sub-title { font-size: 6.5pt; color: #444; margin-top: 2px; }
        .print-date { text-align: right; font-size: 6.5pt; color: #555; vertical-align: bottom; }

        table.data-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.data-table th, table.data-table td { border: 0.4px solid #333; padding: 2px 1px; text-align: center; word-wrap: break-word; overflow: hidden; }
        table.data-table th { background-color: #e8f5e9; font-size: 4.5pt; font-weight: bold; }
        
        .border-thick-bottom { border-bottom: 1.2pt solid #000 !important; }
        .border-thick-right { border-right: 1.2pt solid #000 !important; }

        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .bg-total { background-color: #eeeeee; }
        .bg-kontrol { background-color: #fff9c4; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="title-main">REKAPITULASI STOK BENIH WILAYAH {{ strtoupper($namaProvinsi) }}</div>
                <div class="sub-title">Periode: {{ $start->format('d M Y') }} s.d {{ $end->format('d M Y') }}</div>
            </td>
            <td style="width: 30%;" class="print-date">
                Tanggal Cetak: <strong>{{ \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB</strong>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="3" style="width: 2%;">No</th>
                <th rowspan="3" style="width: 8%;">Kabupaten / Kota</th>
                <th colspan="4" style="width: 12%;" class="border-thick-bottom border-thick-right">Target (Ton)</th>
                <th rowspan="3" style="width: 3.5%;" class="border-thick-right">Total</th>
                <th rowspan="3" style="width: 3.5%;" class="border-thick-right">Kontrol</th>
                <th colspan="4" style="width: 12%;" class="border-thick-bottom">Sudah Terdistribusi</th>
                <th colspan="4" style="width: 12%;" class="border-thick-bottom">Benih Tersedia (Tersertifikasi)</th>
                <th colspan="4" style="width: 12%;" class="border-thick-bottom">Benih (Proses Sertifikasi)</th>
                <th colspan="4" style="width: 12%;" class="border-thick-bottom">Benih (Prosesing)</th>
                <th colspan="6" style="width: 23%;" class="border-thick-bottom">Benih (Persiapan Panen)</th>
            </tr>
            <tr>
                <th colspan="2" class="border-thick-bottom">FS</th>
                <th colspan="2" class="border-thick-bottom border-thick-right">SS</th>
                <th colspan="2" class="border-thick-bottom">FS</th><th colspan="2" class="border-thick-bottom">SS</th>
                <th colspan="2" class="border-thick-bottom">FS</th><th colspan="2" class="border-thick-bottom">SS</th>
                <th colspan="2" class="border-thick-bottom">FS</th><th colspan="2" class="border-thick-bottom">SS</th>
                <th colspan="2" class="border-thick-bottom">FS</th><th colspan="2" class="border-thick-bottom">SS</th>
                <th colspan="3" class="border-thick-bottom">FS</th><th colspan="3" class="border-thick-bottom">SS</th>
            </tr>
            <tr>
                <th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th><th style="width: 2.5%;">Vol</th><th style="width: 3.5%;" class="border-thick-right">Var</th>
                <th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th><th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th>
                <th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th><th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th>
                <th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th><th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th>
                <th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th><th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th>
                <th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th><th style="width: 5.5%;">Tgl Panen</th>
                <th style="width: 2.5%;">Vol</th><th style="width: 3.5%;">Var</th><th style="width: 5.5%;">Tgl Panen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $index => $kabData)
                @foreach($kabData['rows'] as $rIdx => $row)
                <tr>
                    @if($rIdx === 0)
                        <td rowspan="{{ $kabData['rowspan'] }}">{{ $index + 1 }}</td>
                        <td rowspan="{{ $kabData['rowspan'] }}" class="text-start fw-bold">{{ $kabData['satker'] }}</td>
                    @endif

                    <td>{{ number_format((float)$row['target_fs_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['target_fs_var'] }}</td>
                    <td>{{ number_format((float)$row['target_ss_vol'], 2) }}</td>
                    <td class="text-start border-thick-right">{{ $row['target_ss_var'] }}</td>

                    @if($rIdx === 0)
                        <td rowspan="{{ $kabData['rowspan'] }}" class="bg-total fw-bold border-thick-right">{{ number_format((float)$kabData['total'], 2) }}</td>
                        <td rowspan="{{ $kabData['rowspan'] }}" class="bg-kontrol fw-bold border-thick-right">{{ number_format((float)$kabData['kontrol'], 2) }}</td>
                    @endif

                    <td>{{ number_format((float)$row['distribusi_fs_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['distribusi_fs_var'] }}</td>
                    <td>{{ number_format((float)$row['distribusi_ss_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['distribusi_ss_var'] }}</td>

                    <td>{{ number_format((float)$row['tersedia_fs_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['tersedia_fs_var'] }}</td>
                    <td>{{ number_format((float)$row['tersedia_ss_vol'], 2) }}</td>
                    <td class="text-start">{{ $row['tersedia_ss_var'] }}</td>

                    <td>{{ number_format((float)($row['sertifikasi_fs_vol'] ?? 0), 2) }}</td>
                    <td class="text-start">{{ $row['sertifikasi_fs_var'] ?? '-' }}</td>
                    <td>{{ number_format((float)($row['sertifikasi_ss_vol'] ?? 0), 2) }}</td>
                    <td class="text-start">{{ $row['sertifikasi_ss_var'] ?? '-' }}</td>

                    <td>{{ number_format((float)($row['prosesing_fs_vol'] ?? 0), 2) }}</td>
                    <td class="text-start">{{ $row['prosesing_fs_var'] ?? '-' }}</td>
                    <td>{{ number_format((float)($row['prosesing_ss_vol'] ?? 0), 2) }}</td>
                    <td class="text-start">{{ $row['prosesing_ss_var'] ?? '-' }}</td>

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
                <td colspan="32">Belum ada data rekapitulasi wilayah.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>