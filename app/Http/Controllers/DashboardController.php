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

        // Total Produksi Padi 
        $totalPadi = (clone $queryPanen)->sum('jumlah_panen_ton');

        // Total Produksi Jagung 
        $totalJagung = 0; 

        // Total Persediaan Benih di Gudang (Kg)
        $totalStokKg = (clone $queryStok)->sum('jumlah_stok_kg');

        // Total Varietas Master Benih
        $totalVarietas = 0;
        try {
            if (class_exists(\App\Models\Benih::class)) {
                $totalVarietas = \App\Models\Benih::count();
            } elseif (Schema::hasTable('benihs')) {
                $totalVarietas = DB::table('benihs')->count();
            }
        } catch (\Exception $e) {
            $totalVarietas = 0;
        }

        // Akumulasi Stok Masuk & Keluar (Kg)
        $stokMasukKg = (clone $queryTransaksi)->where('jenis_transaksi', 'masuk')->sum('jumlah_kg');
        $stokKeluarKg = (clone $queryTransaksi)->where('jenis_transaksi', 'keluar')->sum('jumlah_kg');

        // 5 Aktivitas Panen Terbaru
        $panenTerbaru = (clone $queryPanen)->with('provinsi')->latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalPadi',
            'totalJagung',
            'totalStokKg',
            'totalVarietas',
            'stokMasukKg',
            'stokKeluarKg',
            'panenTerbaru'
        ));
    }
}