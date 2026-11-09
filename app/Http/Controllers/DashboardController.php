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

        // 5 Aktivitas Panen Terbaru
        $panenTerbaru = (clone $queryPanen)->with('provinsi')->latest()->take(5)->get();

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
                'panenTerbaru'
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
            'panenTerbaru'
        ));
    }
}