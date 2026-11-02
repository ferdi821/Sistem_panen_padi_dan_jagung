<?php

namespace App\Http\Controllers;

use App\Models\Provinsi;
use App\Models\StokBenih;
use App\Models\CatatanPanen;
use App\Models\PengujianMutuBenih;
use App\Models\TransaksiStokBenih;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    private function getRekapData()
    {
        $provinsis = Provinsi::all();
        $rekapData = [];
        $hasTahapanColumn = Schema::hasColumn('catatan_panens', 'tahapan_proses');

        foreach ($provinsis as $prov) {
            if ($hasTahapanColumn) {
                $persiapanPanenTon = CatatanPanen::where('provinsi_id', $prov->id)->where('tahapan_proses', 'persiapan_panen')->sum('jumlah_panen_ton');
                $prosesingTon = CatatanPanen::where('provinsi_id', $prov->id)->where('tahapan_proses', 'prosesing')->sum('jumlah_panen_ton');
            } else {
                $persiapanPanenTon = CatatanPanen::where('provinsi_id', $prov->id)->sum('jumlah_panen_ton');
                $prosesingTon = 0;
            }

            $prosesSertifikasiKg = PengujianMutuBenih::whereHas('catatanPanen', function ($q) use ($prov) {
                $q->where('provinsi_id', $prov->id);
            })->where('status_kelayakan', 'proses')->sum('jumlah_lolos_kg');

            $stokTersediaKg = StokBenih::where('provinsi_id', $prov->id)->sum('jumlah_stok_kg');

            $terdistribusiKg = TransaksiStokBenih::whereHas('stokBenih', function ($q) use ($prov) {
                $q->where('provinsi_id', $prov->id);
            })->where('jenis_transaksi', 'keluar')->sum('jumlah_kg');

            $rekapData[] = [
                'satker' => $prov->nama_provinsi,
                'persiapan_panen_ton' => $persiapanPanenTon ?? 0,
                'prosesing_ton' => $prosesingTon ?? 0,
                'proses_sertifikasi_ton' => ($prosesSertifikasiKg ?? 0) / 1000,
                'tersedia_ton' => ($stokTersediaKg ?? 0) / 1000,
                'terdistribusi_ton' => ($terdistribusiKg ?? 0) / 1000,
            ];
        }

        return $rekapData;
    }

    public function rekapBbpsip()
    {
        $rekapData = $this->getRekapData();
        return view('reports.rekap_bbpsip', compact('rekapData'));
    }

    public function rekapPdf()
    {
        $rekapData = $this->getRekapData();
        
        // Render PDF khusus Rekap dalam posisi Kertas Landscape
        $pdf = Pdf::loadView('reports.rekap_pdf', compact('rekapData'))->setPaper('a4', 'landscape');
        return $pdf->download('Laporan_Rekapitulasi_BBPSIP_' . date('d-m-Y') . '.pdf');
    }

    public function stokPdf()
    {
        $user = auth()->user();
        $query = StokBenih::with(['provinsi', 'benih']);

        if ($user->hasRole('petugas_provinsi')) {
            $query->where('provinsi_id', $user->provinsi_id);
        }

        $stokList = $query->get();
        $pdf = Pdf::loadView('reports.stok_pdf', compact('stokList'))->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Stok_Benih_' . date('d-m-Y') . '.pdf');
    }

    public function panenPdf()
    {
        $user = auth()->user();
        $query = CatatanPanen::with('provinsi');

        if ($user->hasRole('petugas_provinsi')) {
            $query->where('provinsi_id', $user->provinsi_id);
        }

        $panenList = $query->latest()->get();
        $pdf = Pdf::loadView('reports.panen_pdf', compact('panenList'))->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Hasil_Panen_' . date('d-m-Y') . '.pdf');
    }
}