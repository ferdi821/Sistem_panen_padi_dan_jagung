<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Stok Benih Nasional</title>
    <style>
        @page { margin: 8mm; }
        body { font-family: sans-serif; font-size: 7pt; color: #111; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h3 { margin: 0; text-transform: uppercase; color: #106742; font-size: 11pt; }
        .header p { margin: 2px 0 0 0; font-size: 8pt; color: #555; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 5px; }
        th, td { border: 0.5px solid #444; padding: 3px 2px; text-align: center; word-wrap: break-word; }
        th { background-color: #e8f5e9; font-size: 6.5pt; font-weight: bold; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }
        .bg-total { background-color: #eeeeee; }
        .bg-kontrol { background-color: #fff9c4; }
    </style>
</head>
<body>

    <div class="header">
        <h3>REKAPITULASI STOK BENIH LINGKUP BBPSIP (34 PROVINSI)</h3>
        <p>Periode: {{ $start->format('d M Y') }} s.d {{ $end->format('d M Y') }} | Tanggal Cetak: {{ date('d-m-Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="3" width="3%">No</th>
                <th rowspan="3" width="12%">Satker</th>
                <th colspan="4">Target (Ton)</th>
                <th rowspan="3" width="5%">Total</th>
                <th rowspan="3" width="5%">Kontrol</th>
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
                <th>Vol</th><th>Varietas</th><th>Vol</th><th>Varietas</th>
                <th>Vol</th><th>Varietas</th><th>Vol</th><th>Varietas</th>
                <th>Vol</th><th>Varietas</th><th>Vol</th><th>Varietas</th>
                <th>Vol</th><th>Varietas</th><th>Vol</th><th>Varietas</th>
                <th>Vol</th><th>Varietas</th><th>Tgl Panen</th>
                <th>Vol</th><th>Varietas</th><th>Tgl Panen</th>
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