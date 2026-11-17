<?php

namespace App\Http\Controllers;

use App\Models\CatatanPanen;
use App\Models\StokBenih;
use App\Models\TransaksiStokBenih;
use App\Models\Benih;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Query dasar
        $queryPanen = CatatanPanen::query();
        $queryStok = StokBenih::query();
        $queryTransaksi = TransaksiStokBenih::query();

        // Filter wilayah dinamis khusus Pimpinan
        $selectedProvinsiId = null;
        if ($user->hasRole('pimpinan') && $request->filled('provinsi_id')) {
            $selectedProvinsiId = $request->provinsi_id;
            $queryPanen->where('provinsi_id', $selectedProvinsiId);
            $queryStok->where('provinsi_id', $selectedProvinsiId);
            $queryTransaksi->whereHas('stokBenih', function ($q) use ($selectedProvinsiId) {
                $q->where('provinsi_id', $selectedProvinsiId);
            });
        }

        // Isolasi data khusus Petugas Provinsi
        if ($user->hasRole('petugas_provinsi') && $user->provinsi_id) {
            $queryPanen->where('provinsi_id', $user->provinsi_id);
            $queryStok->where('provinsi_id', $user->provinsi_id);
            $queryTransaksi->whereHas('stokBenih', function ($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id);
            });
        }

        // 1. Total Produksi Padi
        $totalPadiTon = (clone $queryPanen)
            ->whereRaw('LOWER(komoditas) = ?', ['padi'])
            ->sum('jumlah_panen_ton');

        // 2. Total Produksi Jagung
        $totalJagungTon = (clone $queryPanen)
            ->whereRaw('LOWER(komoditas) = ?', ['jagung'])
            ->sum('jumlah_panen_ton');

        // 3. Total Persediaan Benih di Gudang (Kg)
        $totalStokKg = (clone $queryStok)->sum('jumlah_stok_kg');

        // 4. Total Varietas Master Benih
        $totalVarietas = 0;
        try {
            if ($user->hasRole('petugas_provinsi') && $user->provinsi_id) {
                $totalVarietas = (clone $queryStok)->with('benih')->get()->pluck('benih.varietas')->filter()->unique()->count();
            } else {
                if (class_exists(\App\Models\Benih::class)) {
                    $totalVarietas = \App\Models\Benih::count();
                } elseif (Schema::hasTable('benihs')) {
                    $totalVarietas = DB::table('benihs')->count();
                }
            }
        } catch (\Exception $e) {
            $totalVarietas = 0;
        }

        // Akumulasi Stok Masuk & Keluar (Kg)
        $stokMasukKg = (clone $queryTransaksi)->where('jenis_transaksi', 'masuk')->sum('jumlah_kg');
        $stokKeluarKg = (clone $queryTransaksi)->where('jenis_transaksi', 'keluar')->sum('jumlah_kg');

        // 5 Aktivitas Terbaru
        $riwayatTerbaru = (clone $queryTransaksi)->with(['stokBenih.benih', 'stokBenih.provinsi', 'user'])->latest()->take(5)->get();
        $panenTerbaru = (clone $queryPanen)->with('provinsi')->latest()->take(5)->get();

        // PENGELOMPOKAN DATA GRAFIK TREN PRODUKSI NASIONAL DINAMIS
        $tahunIni = date('Y');
        $trenPadi = array_fill(1, 12, 0);
        $trenJagung = array_fill(1, 12, 0);

        $dataBulanan = (clone $queryPanen)
            ->select(
                DB::raw('MONTH(COALESCE(tanggal_panen, created_at)) as bulan'),
                DB::raw('LOWER(komoditas) as komoditas'),
                DB::raw('SUM(jumlah_panen_ton) as total')
            )
            ->whereYear(DB::raw('COALESCE(tanggal_panen, created_at)'), $tahunIni)
            ->groupBy('bulan', 'komoditas')
            ->get();

        foreach ($dataBulanan as $item) {
            if ($item->komoditas === 'padi') {
                $trenPadi[$item->bulan] = (float) $item->total;
            } elseif ($item->komoditas === 'jagung') {
                $trenJagung[$item->bulan] = (float) $item->total;
            }
        }

        $bulanAktif = (int) date('n'); 
        $labelsBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $labelsFinal = array_slice($labelsBulan, 0, $bulanAktif);
        $dataPadiFinal = array_slice(array_values($trenPadi), 0, $bulanAktif);
        $dataJagungFinal = array_slice(array_values($trenJagung), 0, $bulanAktif);

        // PEMISAHAN VIEW BERDASARKAN ROLE USER
        if ($user->hasRole('petugas_provinsi')) {
            $namaProvinsi = $user->provinsi->nama_provinsi ?? 'Wilayah Saya';

            return view('dashboard.petugas', compact(
                'namaProvinsi',
                'totalPadiTon',
                'totalJagungTon',
                'totalStokKg',
                'totalVarietas',
                'stokMasukKg',
                'stokKeluarKg',
                'panenTerbaru',
                'riwayatTerbaru',
                'labelsFinal',
                'dataPadiFinal',
                'dataJagungFinal'
            ));
        }

        if ($user->hasRole('pimpinan')) {
            $provinsis = Provinsi::orderBy('nama_provinsi')->get();

            // Hitung Rata-rata Produktivitas (Ton/Ha)
            $produktivitasRataRata = 0;
            if (Schema::hasColumn('catatan_panens', 'luas_lahan_hektar')) {
                $totalLuasHa = (clone $queryPanen)->sum('luas_lahan_hektar');
                $totalPanenSemua = $totalPadiTon + $totalJagungTon;
                $produktivitasRataRata = $totalLuasHa > 0 ? ($totalPanenSemua / $totalLuasHa) : 0;
            }

            // Rekap Kontribusi & Produktivitas Per Provinsi
            $rekapProvinsi = (clone $queryPanen)
                ->with('provinsi')
                ->select(
                    'provinsi_id',
                    DB::raw("SUM(luas_lahan_hektar) as total_luas"),
                    DB::raw("SUM(CASE WHEN LOWER(komoditas) = 'padi' THEN jumlah_panen_ton ELSE 0 END) as total_padi"),
                    DB::raw("SUM(CASE WHEN LOWER(komoditas) = 'jagung' THEN jumlah_panen_ton ELSE 0 END) as total_jagung"),
                    DB::raw("SUM(jumlah_panen_ton) as total_semua")
                )
                ->groupBy('provinsi_id')
                ->orderByDesc('total_semua')
                ->take(5)
                ->get();

            // Peringatan Stok Benih Kritis / Kosong (<= 1 Ton)
            $stokKritis = (clone $queryStok)
                ->with(['benih', 'provinsi', 'wilayah'])
                ->whereRaw('(CASE WHEN jumlah_stok_kg > 100 THEN jumlah_stok_kg / 1000 ELSE jumlah_stok_kg END) <= 1')
                ->orderByRaw('(CASE WHEN jumlah_stok_kg > 100 THEN jumlah_stok_kg / 1000 ELSE jumlah_stok_kg END) asc')
                ->take(6)
                ->get();

            // Evaluasi Realisasi vs Target Stok Benih per Gudang
            $kolomTarget = collect(['target_stok_kg', 'target_kg', 'target_stok', 'target_benih_kg'])
                ->first(fn($col) => Schema::hasColumn('stok_benihs', $col));

            $evaluasiStok = (clone $queryStok)
                ->with(['provinsi', 'wilayah', 'benih'])
                ->get()
                ->map(function ($item) use ($kolomTarget) {
                    $rawRealisasi = (float) ($item->jumlah_stok_kg ?? 0);
                    $realisasiTon = $rawRealisasi > 100 ? ($rawRealisasi / 1000) : $rawRealisasi;

                    $rawTarget = $kolomTarget ? (float) ($item->{$kolomTarget} ?? 0) : 0;
                    $targetTon = $rawTarget > 100 ? ($rawTarget / 1000) : $rawTarget;

                    $item->realisasi_ton = $realisasiTon;
                    $item->target_ton = $targetTon;
                    $item->persen_capaian = $targetTon > 0
                        ? min(100, round(($realisasiTon / $targetTon) * 100, 1))
                        : ($realisasiTon > 0 ? 100 : 0);
                    return $item;
                });

            return view('dashboard.pimpinan', compact(
                'provinsis',
                'selectedProvinsiId',
                'totalPadiTon',
                'totalJagungTon',
                'totalStokKg',
                'produktivitasRataRata',
                'stokMasukKg',
                'stokKeluarKg',
                'rekapProvinsi',
                'stokKritis',
                'evaluasiStok',
                'panenTerbaru',
                'labelsFinal',
                'dataPadiFinal',
                'dataJagungFinal'
            ));
        }

        // Default: Admin Pusat ke dashboard.index
        return view('dashboard.index', compact(
            'totalPadiTon',
            'totalJagungTon',
            'totalStokKg',
            'totalVarietas',
            'stokMasukKg',
            'stokKeluarKg',
            'panenTerbaru',
            'riwayatTerbaru',
            'labelsFinal',
            'dataPadiFinal',
            'dataJagungFinal'
        ));
    }
}