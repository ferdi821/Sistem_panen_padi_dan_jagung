<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    {!! '<!--[if gte mso 9]><' . 'xml><' . 'x:ExcelWorkbook><' . 'x:ExcelWorksheets><' . 'x:ExcelWorksheet><' . 'x:Name>Rekap Stok Benih Nasional<' . '/x:Name><' . 'x:WorksheetOptions><' . 'x:DisplayGridlines/><' . '/x:WorksheetOptions><' . '/x:ExcelWorksheet><' . '/x:ExcelWorksheets><' . '/x:ExcelWorkbook><' . '/xml><![endif]-->' !!}
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; color: #000000; }
        table { border-collapse: collapse; }
        th, td {
            border: 1px solid #000000;
            padding: 6px 8px;
            vertical-align: middle;
            font-size: 10pt;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        .num { mso-number-format: "\#\,\#\#0\.00"; text-align: right; }
        .txt { mso-number-format: "\@"; }
    </style>
</head>
<body>

    <table>
        <tr>
            <td colspan="30" style="border: none; font-size: 14pt; font-weight: bold; color: #106742;">
                REKAPITULASI STOK BENIH LINGKUP BBPSIP (38 PROVINSI)
            </td>
        </tr>
        <tr>
            <td colspan="30" style="border: none; font-size: 10pt; color: #333333;">
                Periode: {{ $start->format('d M Y') }} s.d {{ $end->format('d M Y') }} | Dicetak: {{ \Carbon\Carbon::now()->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') }} WIB
            </td>
        </tr>
        <tr>
            <td colspan="30" style="border: none; height: 10px;"></td>
        </tr>
    </table>

    <table border="1">
        <thead>
            <tr>
                <th rowspan="3" width="45" style="background-color: #e2e8f0;">No</th>
                <th rowspan="3" width="200" style="background-color: #e2e8f0;">Satker</th>
                <th colspan="4" style="background-color: #dbeafe;">Target (Ton)</th>
                <th rowspan="3" width="95" style="background-color: #64748b; color: #ffffff;">Total</th>
                <th rowspan="3" width="95" style="background-color: #facc15;">Kontrol</th>
                <th colspan="4" style="background-color: #d1fae5;">Sudah Terdistribusi</th>
                <th colspan="4" style="background-color: #e0f2fe;">Benih Tersedia (Tersertifikasi)</th>
                <th colspan="4" style="background-color: #ede9fe;">Benih (Proses Sertifikasi)</th>
                <th colspan="4" style="background-color: #fef3c7;">Benih (Prosesing)</th>
                <th colspan="6" style="background-color: #ffedd5;">Benih (Persiapan Panen)</th>
            </tr>
            <tr>
                <th colspan="2" style="background-color: #eff6ff;">FS</th>
                <th colspan="2" style="background-color: #eff6ff;">SS</th>

                <th colspan="2" style="background-color: #ecfdf5;">FS</th>
                <th colspan="2" style="background-color: #ecfdf5;">SS</th>

                <th colspan="2" style="background-color: #f0f9ff;">FS</th>
                <th colspan="2" style="background-color: #f0f9ff;">SS</th>

                <th colspan="2" style="background-color: #f5f3ff;">FS</th>
                <th colspan="2" style="background-color: #f5f3ff;">SS</th>

                <th colspan="2" style="background-color: #fffbeb;">FS</th>
                <th colspan="2" style="background-color: #fffbeb;">SS</th>

                <th colspan="3" style="background-color: #fff7ed;">FS</th>
                <th colspan="3" style="background-color: #fff7ed;">SS</th>
            </tr>
            <tr style="background-color: #f8fafc;">
                <th width="85">Volume (Ton)</th><th width="120">Varietas</th>
                <th width="85">Volume (Ton)</th><th width="120">Varietas</th>

                <th width="85">Volume (Ton)</th><th width="120">Varietas</th>
                <th width="85">Volume (Ton)</th><th width="120">Varietas</th>

                <th width="85">Volume (Ton)</th><th width="120">Varietas</th>
                <th width="85">Volume (Ton)</th><th width="120">Varietas</th>

                <th width="85">Volume (Ton)</th><th width="120">Varietas</th>
                <th width="85">Volume (Ton)</th><th width="120">Varietas</th>

                <th width="85">Volume (Ton)</th><th width="120">Varietas</th>
                <th width="85">Volume (Ton)</th><th width="120">Varietas</th>

                <th width="85">Volume (Ton)</th><th width="120">Varietas</th><th width="100">Tgl Panen</th>
                <th width="85">Volume (Ton)</th><th width="120">Varietas</th><th width="100">Tgl Panen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $index => $satkerData)
                @foreach($satkerData['rows'] as $rIdx => $row)
                <tr>
                    @if($rIdx === 0)
                        <td rowspan="{{ $satkerData['rowspan'] }}" class="text-center fw-bold">{{ $index + 1 }}</td>
                        <td rowspan="{{ $satkerData['rowspan'] }}" class="text-left fw-bold">{{ $satkerData['satker'] }}</td>
                    @endif

                    <td class="num">{{ (float)$row['target_fs_vol'] }}</td>
                    <td class="text-left txt">{{ $row['target_fs_var'] }}</td>
                    <td class="num">{{ (float)$row['target_ss_vol'] }}</td>
                    <td class="text-left txt">{{ $row['target_ss_var'] }}</td>

                    @if($rIdx === 0)
                        <td rowspan="{{ $satkerData['rowspan'] }}" class="num fw-bold" style="background-color: #f1f5f9;">{{ (float)$satkerData['total'] }}</td>
                        <td rowspan="{{ $satkerData['rowspan'] }}" class="num fw-bold" style="background-color: #fef9c3; color: #b91c1c;">{{ (float)$satkerData['kontrol'] }}</td>
                    @endif

                    <td class="num">{{ (float)$row['distribusi_fs_vol'] }}</td>
                    <td class="text-left txt">{{ $row['distribusi_fs_var'] }}</td>
                    <td class="num">{{ (float)$row['distribusi_ss_vol'] }}</td>
                    <td class="text-left txt">{{ $row['distribusi_ss_var'] }}</td>

                    <td class="num">{{ (float)$row['tersedia_fs_vol'] }}</td>
                    <td class="text-left txt">{{ $row['tersedia_fs_var'] }}</td>
                    <td class="num">{{ (float)$row['tersedia_ss_vol'] }}</td>
                    <td class="text-left txt">{{ $row['tersedia_ss_var'] }}</td>

                    <td class="num">{{ (float)($row['sertifikasi_fs_vol'] ?? 0) }}</td>
                    <td class="text-left txt">{{ $row['sertifikasi_fs_var'] ?? '-' }}</td>
                    <td class="num">{{ (float)($row['sertifikasi_ss_vol'] ?? 0) }}</td>
                    <td class="text-left txt">{{ $row['sertifikasi_ss_var'] ?? '-' }}</td>

                    <td class="num">{{ (float)($row['prosesing_fs_vol'] ?? 0) }}</td>
                    <td class="text-left txt">{{ $row['prosesing_fs_var'] ?? '-' }}</td>
                    <td class="num">{{ (float)($row['prosesing_ss_vol'] ?? 0) }}</td>
                    <td class="text-left txt">{{ $row['prosesing_ss_var'] ?? '-' }}</td>

                    <td class="num">{{ (float)$row['persiapan_fs_vol'] }}</td>
                    <td class="text-left txt">{{ $row['persiapan_fs_var'] }}</td>
                    <td class="text-center txt">{{ $row['persiapan_fs_tgl'] }}</td>

                    <td class="num">{{ (float)$row['persiapan_ss_vol'] }}</td>
                    <td class="text-left txt">{{ $row['persiapan_ss_var'] }}</td>
                    <td class="text-center txt">{{ $row['persiapan_ss_tgl'] }}</td>
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="30" class="text-center">Belum ada data stok benih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>