<?php

namespace App\Http\Controllers;

use App\Models\CatatanPanen;
use App\Models\Provinsi;

class DashboardController extends Controller {
    public function index() {
        $user = auth()->user();
        $query = CatatanPanen::query();

        // Jika petugas provinsi, batasi hanya provinsi miliknya
        if ($user->hasRole('petugas_provinsi')) {
            $query->where('provinsi_id', $user->provinsi_id);
        }

        $totalPadi = (clone $query)->where('komoditas', 'padi')->sum('jumlah_panen_ton');
        $totalJagung = (clone $query)->where('komoditas', 'jagung')->sum('jumlah_panen_ton');
        $totalLuasLahan = (clone $query)->sum('luas_lahan_hektar');

        // Rekapitulasi untuk Admin Pusat dan Pimpinan
        $ringkasanProvinsi = null;
        if ($user->hasAnyRole(['admin_pusat', 'pimpinan'])) {
            $ringkasanProvinsi = Provinsi::withSum(['catatanPanens as total_padi' => function ($q) {
                $q->where('komoditas', 'padi');
            }], 'jumlah_panen_ton')
            ->withSum(['catatanPanens as total_jagung' => function ($q) {
                $q->where('komoditas', 'jagung');
            }], 'jumlah_panen_ton')
            ->get();
        }

        return view('dashboard.index', compact('totalPadi', 'totalJagung', 'totalLuasLahan', 'ringkasanProvinsi'));
    }
}
