<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\CatatanPanen;
use App\Models\StokBenih;
use App\Models\TransaksiStokBenih;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminRekapReportController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->input('periode', 'bulan_ini');
        [$start, $end] = $this->getFilterDates($periode, $request->start_date, $request->end_date);

        $rekapData = $this->getRekapNasionalData($start, $end);

        return view('reports.admin.rekap_nasional', compact('rekapData', 'periode', 'start', 'end'));
    }

    public function exportPdf(Request $request)
    {
        $periode = $request->input('periode', 'bulan_ini');
        [$start, $end] = $this->getFilterDates($periode, $request->start_date, $request->end_date);
        $rekapData = $this->getRekapNasionalData($start, $end);

        $pdf = Pdf::loadView('reports.pdf.rekap_nasional_pdf', compact('rekapData', 'periode', 'start', 'end'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Rekap_Stok_Benih_BBPSIP_' . date('Ymd') . '.pdf');
    }

    private function getRekapNasionalData($start, $end)
    {
        $provinsis = Provinsi::orderBy('nama_provinsi', 'asc')->get();
        $rekapData = [];

        foreach ($provinsis as $prov) {
            $namaSatker = 'BPSIP ' . $prov->nama_provinsi;

            // Target Total
            $targetFsTotal = (float)($prov->target_fs_ton ?? 0.00);
            $targetSsTotal = (float)($prov->target_ss_ton ?? 0.00);
            $totalTarget = $targetFsTotal + $targetSsTotal;

            // Stok Benih & Catatan Panen di Provinsi Ini
            $stokItems = StokBenih::where('provinsi_id', $prov->id)->with('benih')->get();
            $panenItems = CatatanPanen::where('provinsi_id', $prov->id)->whereBetween('tanggal_panen', [$start, $end])->get();

            // Varietas Unik di Provinsi Ini
            $varietasUnik = $stokItems->pluck('benih.varietas')->filter()->unique()->values();

            if ($varietasUnik->isEmpty()) {
                $subRows = [[
                    'target_fs_vol' => $targetFsTotal,
                    'target_fs_var' => '-',
                    'target_ss_vol' => $targetSsTotal,
                    'target_ss_var' => '-',
                    'distribusi_fs_vol' => 0.00,
                    'distribusi_fs_var' => '-',
                    'distribusi_ss_vol' => 0.00,
                    'distribusi_ss_var' => '-',
                    'tersedia_fs_vol' => 0.00,
                    'tersedia_fs_var' => '-',
                    'tersedia_ss_vol' => 0.00,
                    'tersedia_ss_var' => '-',
                    'prosesing_fs_vol' => 0.00,
                    'prosesing_fs_var' => '-',
                    'prosesing_ss_vol' => 0.00,
                    'prosesing_ss_var' => '-',
                    'persiapan_fs_vol' => 0.00,
                    'persiapan_fs_var' => '-',
                    'persiapan_fs_tgl' => '-',
                    'persiapan_ss_vol' => 0.00,
                    'persiapan_ss_var' => '-',
                    'persiapan_ss_tgl' => '-',
                ]];
            } else {
                $subRows = [];
                foreach ($varietasUnik as $idxVar => $varName) {
                    // Tersedia FS & SS
                    $volTersediaFs = (float)($stokItems->filter(fn($s) => ($s->benih->varietas ?? '') === $varName && ($s->benih->kelas_benih ?? '') === 'FS')->sum('jumlah_stok_kg') / 1000);
                    $volTersediaSs = (float)($stokItems->filter(fn($s) => ($s->benih->varietas ?? '') === $varName && ($s->benih->kelas_benih ?? '') === 'SS')->sum('jumlah_stok_kg') / 1000);

                    // Terdistribusi FS & SS
                    $volDistFs = (float)((TransaksiStokBenih::whereHas('stokBenih', function($q) use ($prov, $varName) {
                            $q->where('provinsi_id', $prov->id)
                              ->whereHas('benih', fn($b) => $b->where('varietas', $varName)->where('kelas_benih', 'FS'));
                        })
                        ->where('jenis_transaksi', 'keluar')
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('jumlah_kg') ?? 0) / 1000);

                    $volDistSs = (float)((TransaksiStokBenih::whereHas('stokBenih', function($q) use ($prov, $varName) {
                            $q->where('provinsi_id', $prov->id)
                              ->whereHas('benih', fn($b) => $b->where('varietas', $varName)->where('kelas_benih', 'SS'));
                        })
                        ->where('jenis_transaksi', 'keluar')
                        ->whereBetween('created_at', [$start, $end])
                        ->sum('jumlah_kg') ?? 0) / 1000);

                    $rowTargetFs = $idxVar === 0 ? $targetFsTotal : 0.00;
                    $rowTargetSs = $idxVar === 0 ? $targetSsTotal : 0.00;

                    $subRows[] = [
                        'target_fs_vol' => $rowTargetFs,
                        'target_fs_var' => $idxVar === 0 && $rowTargetFs > 0 ? $varName : ($volTersediaFs > 0 ? $varName : '-'),
                        'target_ss_vol' => $rowTargetSs,
                        'target_ss_var' => $idxVar === 0 && $rowTargetSs > 0 ? $varName : ($volTersediaSs > 0 ? $varName : '-'),
                        
                        'distribusi_fs_vol' => $volDistFs,
                        'distribusi_fs_var' => $volDistFs > 0 ? $varName : '-',
                        'distribusi_ss_vol' => $volDistSs,
                        'distribusi_ss_var' => $volDistSs > 0 ? $varName : '-',

                        'tersedia_fs_vol' => $volTersediaFs,
                        'tersedia_fs_var' => $volTersediaFs > 0 ? $varName : '-',
                        'tersedia_ss_vol' => $volTersediaSs,
                        'tersedia_ss_var' => $volTersediaSs > 0 ? $varName : '-',

                        'prosesing_fs_vol' => 0.00,
                        'prosesing_fs_var' => '-',
                        'prosesing_ss_vol' => 0.00,
                        'prosesing_ss_var' => '-',

                        'persiapan_fs_vol' => 0.00,
                        'persiapan_fs_var' => '-',
                        'persiapan_fs_tgl' => '-',
                        'persiapan_ss_vol' => 0.00,
                        'persiapan_ss_var' => '-',
                        'persiapan_ss_tgl' => '-',
                    ];
                }
            }

            $totalDist = array_sum(array_column($subRows, 'distribusi_fs_vol')) + array_sum(array_column($subRows, 'distribusi_ss_vol'));
            $totalTersedia = array_sum(array_column($subRows, 'tersedia_fs_vol')) + array_sum(array_column($subRows, 'tersedia_ss_vol'));
            $kontrol = $totalTarget - ($totalDist + $totalTersedia);

            $rekapData[] = [
                'satker' => $namaSatker,
                'total' => $totalTarget,
                'kontrol' => $kontrol,
                'rowspan' => count($subRows),
                'rows' => $subRows,
            ];
        }

        return $rekapData;
    }

    private function getFilterDates($periode, $startDate, $endDate)
    {
        return match ($periode) {
            'hari_ini' => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()],
            'minggu_ini' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'tahun_ini' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            'custom' => [
                $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth(),
                $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfMonth()
            ],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }
}