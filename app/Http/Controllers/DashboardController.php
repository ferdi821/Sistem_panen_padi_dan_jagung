<?php

namespace App\Http\Controllers;

use App\Models\CatatanPanen;
use App\Models\StokBenih;
use App\Models\TransaksiStokBenih;
use App\Models\Benih;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Query dasar
        $queryPanen = CatatanPanen::query();
        $queryStok = StokBenih::query();
        $queryTransaksi = TransaksiStokBenih::query();

        // Isolasi data khusus Petugas Provinsi
        if ($user->hasRole('petugas_provinsi') && $user->provinsi_id) {
            $queryPanen->where('provinsi_id', $user->provinsi_id);
            $queryStok->where('provinsi_id', $user->provinsi_id);
            $queryTransaksi->whereHas('stokBenih', function ($q) use ($user) {
                $q->where('provinsi_id', $user->provinsi_id);
            });
        }

        // 1. Total Produksi Padi (Menjumlahkan jumlah_panen_ton khusus komoditas PADI)
        $totalPadiTon = (clone $queryPanen)
            ->whereRaw('LOWER(komoditas) = ?', ['padi'])
            ->sum('jumlah_panen_ton');

        // 2. Total Produksi Jagung (Menjumlahkan jumlah_panen_ton khusus komoditas JAGUNG)
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

        // -------------------------------------------------------------
        // PENGELOMPOKAN DATA GRAFIK TREN PRODUKSI NASIONAL DINAMIS
        // -------------------------------------------------------------
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

        // Batasi grafik hanya sampai bulan aktif saat ini
        $bulanAktif = (int) date('n'); 
        $labelsBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        $labelsFinal = array_slice($labelsBulan, 0, $bulanAktif);
        $dataPadiFinal = array_slice(array_values($trenPadi), 0, $bulanAktif);
        $dataJagungFinal = array_slice(array_values($trenJagung), 0, $bulanAktif);

        // -------------------------------------------------------------
        // PEMISAHAN VIEW BERDASARKAN ROLE USER
        // -------------------------------------------------------------
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

        // Default: Admin Pusat & Role Lainnya ke dashboard.index
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