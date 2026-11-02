<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Stok Benih BBPSIP</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1b5e20;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #1b5e20;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header p {
            margin: 4px 0 0 0;
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #b0bec5;
            padding: 6px 8px;
            text-align: center;
            word-wrap: break-word;
        }
        th {
            background-color: #e8f5e9;
            color: #1b5e20;
            font-weight: bold;
            font-size: 10px;
        }
        .text-start { text-align: left; }
        .text-bold { font-weight: bold; }
        .bg-highlight { background-color: #f1f8e9; }
        .footer-note {
            margin-top: 15px;
            font-size: 9px;
            color: #777;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI PIPELINE STOK BENIH LINGKUP BBPSIP</h2>
        <p>Kementerian Pertanian Republik Indonesia | Tanggal Cetak: {{ date('d-m-Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 23%;">SATKER / PROVINSI</th>
                <th style="width: 14%;">1. PERSIAPAN PANEN (TON)</th>
                <th style="width: 14%;">2. PROSESING (TON)</th>
                <th style="width: 14%;">3. SERTIFIKASI (TON)</th>
                <th style="width: 15%;">4. TERSEDIA / LAYAK (TON)</th>
                <th style="width: 15%;">5. TERDISTRIBUSI (TON)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rekapData as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-start text-bold">{{ $row['satker'] }}</td>
                <td>{{ number_format($row['persiapan_panen_ton'], 2) }}</td>
                <td>{{ number_format($row['prosesing_ton'], 2) }}</td>
                <td>{{ number_format($row['proses_sertifikasi_ton'], 2) }}</td>
                <td class="bg-highlight text-bold" style="color: #1b5e20;">{{ number_format($row['tersedia_ton'], 2) }}</td>
                <td class="text-bold" style="color: #b71c1c;">{{ number_format($row['terdistribusi_ton'], 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7">Belum ada data rekapitulasi provinsi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        * Dokumen ini dihasilkan secara otomatis oleh sistem SIPANEN & Stok Benih Nasional.
    </div>

</body>
</html>