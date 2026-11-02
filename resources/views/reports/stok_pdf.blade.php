<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Persediaan Stok Benih Nasional</title>
    <style>
        /* Pengaturan Margin Kertas A4 Portrait */
        @page {
            margin: 12mm 10mm 12mm 10mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #1b5e20;
            padding-bottom: 8px;
        }

        .header h2 {
            margin: 0;
            color: #1b5e20;
            font-size: 14pt;
            text-transform: uppercase;
            font-weight: bold;
        }

        .header p {
            margin: 4px 0 0 0;
            font-size: 8.5pt;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed; /* Mencegah kolom meluber keluar batas margin */
        }

        th, td {
            border: 1px solid #b0bec5;
            padding: 6px 8px;
            vertical-align: middle;
            word-wrap: break-word; /* Otomatis bungkus teks panjang */
        }

        th {
            background-color: #e8f5e9;
            color: #1b5e20;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
        }

        td {
            font-size: 9.5pt;
        }

        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }

        .footer-note {
            margin-top: 15px;
            font-size: 8pt;
            color: #777;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN PERSEDIAAN STOK BENIH PADI & JAGUNG</h2>
        <p>Kementerian Pertanian Republik Indonesia | Tanggal Cetak: {{ date('d-m-Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th style="width: 18%;">Provinsi</th>
                <th style="width: 14%;">Kode Benih</th>
                <th style="width: 22%;">Nama Benih</th>
                <th style="width: 13%;">Komoditas</th>
                <th style="width: 10%;">Varietas</th>
                <th style="width: 17%;">Stok (Kg)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stokList as $index => $stok)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $stok->provinsi->nama_provinsi ?? '-' }}</td>
                <td class="text-center">{{ $stok->benih->kode_benih ?? '-' }}</td>
                <td>{{ $stok->benih->nama_benih ?? '-' }}</td>
                <td class="text-center">{{ strtoupper($stok->benih->komoditas ?? '-') }}</td>
                <td class="text-center">{{ $stok->benih->varietas ?? '-' }}</td>
                <td class="text-end fw-bold" style="color: #1b5e20;">
                    {{ number_format($stok->jumlah_stok_kg, 2) }} Kg
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 15px; color: #777;">
                    Belum ada data ketersediaan stok benih.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        * Dokumen resmi dikeluarkan secara otomatis oleh Sistem Informasi SIPANEN.
    </div>

</body>
</html>