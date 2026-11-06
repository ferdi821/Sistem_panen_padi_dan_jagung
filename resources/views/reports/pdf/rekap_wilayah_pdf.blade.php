<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Stok Benih Wilayah</title>
    <<style>
        @page { margin: 6mm; }
        body { font-family: sans-serif; font-size: 7pt; color: #111; } /* Ukuran font dinaikin jadi 7pt */
        .header { text-align: center; margin-bottom: 8px; }
        .header h3 { margin: 0; text-transform: uppercase; color: #106742; font-size: 10.5pt; }
        .header p { margin: 2px 0 0 0; font-size: 7.5pt; color: #555; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 5px; }
        th, td { border: 0.5px solid #444; padding: 4px 2px; text-align: center; word-wrap: break-word; } /* Padding dilebarin biar proporsional */
        th { background-color: #e8f5e9; font-size: 6.5pt; font-weight: bold; white-space: nowrap; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .bg-total { background-color: #eeeeee; }
        .bg-kontrol { background-color: #fff9c4; }
    </style>
</head>
<body>

    <div class="header">
        <h3>REKAPITULASI STOK BENIH WILAYAH {{ strtoupper($namaProvinsi) }}</h3>
        <p>Periode: {{ $start->format('d M Y') }} s.d {{ $end->format('d M Y') }} | Tanggal Cetak: {{ date('d-m-Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="3" width="2.5%">No</th>
                <th rowspan="3" width="11%">Kabupaten / Kota</th>
                <th colspan="4">Target (Ton)</th>
                <th rowspan="3" width="4%">Total</th>
                <th rowspan="3" width="4%">Kontrol</th>
                <th colspan="4">Sudah Terdistribusi</th>
                <th colspan="4">Benih Tersedia</th>
                <th colspan="4">Benih (Prosesing)</th>
                <th colspan="6">Benih (Persiapan Panen)</th>
            </tr>
            <tr>
                <th colspan="2">FS</th><th colspan="2">SS</th>
                <th colspan="2">FS</th><th colspan="2">SS</th>
                <th colspan="2">FS</th><th colspan="2">SS</th>
                <th colspan="2">FS</th><th colspan="2">SS</th>
                <th colspan="3">FS</th><th colspan="3">SS</th>
            </tr>
            <tr>
                <th>Volume</th><th>Varietas</th><th>Volume</th><th>Varietas</th>
                <th>Volume</th><th>Varietas</th><th>Volume</th><th>Varietas</th>
                <th>Volume</th><th>Varietas</th><th>Volume</th><th>Varietas</th>
                <th>Volume</th><th>Varietas</th><th>Volume</th><th>Varietas</th>
                <th>Volume</th><th>Varietas</th><th>Tanggal Panen</th>
                <th>Volume</th><th>Varietas</th><th>Tanggal Panen</th>
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
                    <td class="text-start">{{ $row['target_ss_var'] }}</td>

                    @if($rIdx === 0)
                        <td rowspan="{{ $kabData['rowspan'] }}" class="bg-total fw-bold">{{ number_format((float)$kabData['total'], 2) }}</td>
                        <td rowspan="{{ $kabData['rowspan'] }}" class="bg-kontrol fw-bold">{{ number_format((float)$kabData['kontrol'], 2) }}</td>
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
                <td colspan="28">Belum ada data rekapitulasi wilayah.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>