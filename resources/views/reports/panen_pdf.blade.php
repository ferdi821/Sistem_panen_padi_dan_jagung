<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Panen Padi dan Jagung Nasional</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2e7d32; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #2e7d32; }
        .header p { margin: 5px 0 0 0; font-size: 9pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; font-size: 9pt; }
        th { background-color: #e8f5e9; color: #1b5e20; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { font-weight: bold; padding: 2px 5px; border-radius: 3px; font-size: 8pt; }
        .badge-padi { background-color: #c8e6c9; color: #1b5e20; }
        .badge-jagung { background-color: #fff9c4; color: #f57f17; }
        .summary-box { margin-top: 15px; width: 40%; float: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN HASIL PANEN PADI & JAGUNG</h2>
        <p>Kementerian Pertanian Republik Indonesia | Tanggal Cetak: {{ date('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th class="text-center" width="12%">Tanggal</th>
                <th>Provinsi</th>
                <th class="text-center" width="12%">Komoditas</th>
                <th class="text-right" width="15%">Luas Lahan (Ha)</th>
                <th class="text-right" width="15%">Hasil Panen (Ton)</th>
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($panenList as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $item->tanggal_panen }}</td>
                <td>{{ $item->provinsi->nama_provinsi ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge {{ $item->komoditas === 'padi' ? 'badge-padi' : 'badge-jagung' }}">
                        {{ strtoupper($item->komoditas) }}
                    </span>
                </td>
                <td class="text-right">{{ number_format($item->luas_lahan_hektar, 2) }}</td>
                <td class="text-right">{{ number_format($item->jumlah_panen_ton, 2) }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data hasil panen.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary-box">
        <tr>
            <th>Total Produksi Padi</th>
            <td class="text-right"><strong>{{ number_format($panenList->where('komoditas', 'padi')->sum('jumlah_panen_ton'), 2) }} Ton</strong></td>
        </tr>
        <tr>
            <th>Total Produksi Jagung</th>
            <td class="text-right"><strong>{{ number_format($panenList->where('komoditas', 'jagung')->sum('jumlah_panen_ton'), 2) }} Ton</strong></td>
        </tr>
        <tr>
            <th>Total Luas Lahan</th>
            <td class="text-right"><strong>{{ number_format($panenList->sum('luas_lahan_hektar'), 2) }} Ha</strong></td>
        </tr>
    </table>
</body>
</html>
