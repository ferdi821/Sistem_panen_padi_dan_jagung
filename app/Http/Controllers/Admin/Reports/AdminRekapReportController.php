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
        [$start, $end] = $this->getFilterDates($request->start_date, $request->end_date);
        $periode = 'custom';

        $rekapData = $this->getRekapNasionalData($start, $end);

        return view('reports.admin.rekap_nasional', compact('rekapData', 'periode', 'start', 'end'));
    }

    public function exportPdf(Request $request)
    {
        [$start, $end] = $this->getFilterDates($request->start_date, $request->end_date);
        $periode = 'custom';
        $rekapData = $this->getRekapNasionalData($start, $end);

        $pdf = Pdf::loadView('reports.pdf.rekap_nasional_pdf', compact('rekapData', 'periode', 'start', 'end'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Rekap_Stok_Benih_BBPSIP_' . date('Ymd') . '.pdf');
    }

    private function getRekapNasionalData($start, $end)
    {
        $provinsis = Provinsi::orderBy('nama_provinsi', 'asc')->get();
        $rekapData = [];

        $startDateStr = $start->format('Y-m-d');
        $endDateStr   = $end->format('Y-m-d');

        foreach ($provinsis as $prov) {
            $namaSatker = 'BPSIP ' . $prov->nama_provinsi;

            // Ambil stok master
            $stokItems = StokBenih::where('provinsi_id', $prov->id)->with('benih')->get();

            $subRows = [];

            if ($stokItems->isNotEmpty()) {
                foreach ($stokItems as $s) {
                    $varName = $s->benih->varietas ?? '-';
                    $kelas   = strtoupper($s->benih->kelas_benih ?? 'BR');
                    $isFS    = ($kelas === 'BD' || $kelas === 'FS');

                    // 1. Cek Transaksi Keluar HANYA pada rentang tanggal filter
                    $volDist = (float) TransaksiStokBenih::where('stok_benih_id', $s->id)
                                ->where('jenis_transaksi', 'keluar')
                                ->whereBetween('tanggal_transaksi', [$startDateStr, $endDateStr])
                                ->sum('jumlah_kg');

                    // 2. Cek Transaksi Masuk HANYA pada rentang tanggal filter
                    $volMasuk = (float) TransaksiStokBenih::where('stok_benih_id', $s->id)
                                ->where('jenis_transaksi', 'masuk')
                                ->whereBetween('tanggal_transaksi', [$startDateStr, $endDateStr])
                                ->sum('jumlah_kg');

                    // 3. Cek Catatan Panen HANYA pada rentang tanggal filter
                    $panen = CatatanPanen::where('provinsi_id', $prov->id)
                                ->whereBetween('tanggal_panen', [$startDateStr, $endDateStr])
                                ->first();

                    $volPanen = $panen ? (float) $panen->jumlah_panen_ton : 0.00;
                    $tglPanen = $panen ? Carbon::parse($panen->tanggal_panen)->format('d/m/Y') : '-';

                    // Hanya tampilkan saldo stok jika ada aktivitas transaksi / panen pada periode ini
                    $adaAktivitas = ($volDist > 0 || $volMasuk > 0 || $volPanen > 0);
                    $volTersedia  = $adaAktivitas ? (float) $s->jumlah_stok_kg : 0.00;

                    // Jika ada aktivitas transaksi/panen pada item stok ini di periode terkait, masukkan ke subRows
                    if ($adaAktivitas) {
                        $subRows[] = [
                            'target_fs_vol'     => $isFS ? ($volTersedia + $volDist) : 0.00,
                            'target_fs_var'     => $isFS ? $varName : '-',
                            'target_ss_vol'     => !$isFS ? ($volTersedia + $volDist) : 0.00,
                            'target_ss_var'     => !$isFS ? $varName : '-',
                            
                            'distribusi_fs_vol' => $isFS ? $volDist : 0.00,
                            'distribusi_fs_var' => ($isFS && $volDist > 0) ? $varName : '-',
                            'distribusi_ss_vol' => !$isFS ? $volDist : 0.00,
                            'distribusi_ss_var' => (!$isFS && $volDist > 0) ? $varName : '-',

                            'tersedia_fs_vol'   => $isFS ? $volTersedia : 0.00,
                            'tersedia_fs_var'   => $isFS ? $varName : '-',
                            'tersedia_ss_vol'   => !$isFS ? $volTersedia : 0.00,
                            'tersedia_ss_var'   => !$isFS ? $varName : '-',

                            'prosesing_fs_vol'  => 0.00,
                            'prosesing_fs_var'  => '-',
                            'prosesing_ss_vol'  => 0.00,
                            'prosesing_ss_var'  => '-',

                            'persiapan_fs_vol'  => $isFS ? $volPanen : 0.00,
                            'persiapan_fs_var'  => ($isFS && $volPanen > 0) ? $varName : '-',
                            'persiapan_fs_tgl'  => ($isFS && $volPanen > 0) ? $tglPanen : '-',
                            
                            'persiapan_ss_vol'  => !$isFS ? $volPanen : 0.00,
                            'persiapan_ss_var'  => (!$isFS && $volPanen > 0) ? $varName : '-',
                            'persiapan_ss_tgl'  => (!$isFS && $volPanen > 0) ? $tglPanen : '-',
                        ];
                    }
                }
            }

            // JIKA TIDAK ADA AKTIVITAS TRANSAKSI SAMA SEKALI PADA PERIODE TERSEBUT (Seperti Bulan Desember / Harian Kosong)
            // Cukup tampilkan 1 BARIS KOSONG RAPI (0.00)
            if (empty($subRows)) {
                $subRows = [[
                    'target_fs_vol' => 0.00, 'target_fs_var' => '-', 'target_ss_vol' => 0.00, 'target_ss_var' => '-',
                    'distribusi_fs_vol' => 0.00, 'distribusi_fs_var' => '-', 'distribusi_ss_vol' => 0.00, 'distribusi_ss_var' => '-',
                    'tersedia_fs_vol' => 0.00, 'tersedia_fs_var' => '-', 'tersedia_ss_vol' => 0.00, 'tersedia_ss_var' => '-',
                    'prosesing_fs_vol' => 0.00, 'prosesing_fs_var' => '-', 'prosesing_ss_vol' => 0.00, 'prosesing_ss_var' => '-',
                    'persiapan_fs_vol' => 0.00, 'persiapan_fs_var' => '-', 'persiapan_fs_tgl' => '-',
                    'persiapan_ss_vol' => 0.00, 'persiapan_ss_var' => '-', 'persiapan_ss_tgl' => '-',
                ]];
            }

            $totalDist = array_sum(array_column($subRows, 'distribusi_fs_vol')) + array_sum(array_column($subRows, 'distribusi_ss_vol'));
            $totalTersedia = array_sum(array_column($subRows, 'tersedia_fs_vol')) + array_sum(array_column($subRows, 'tersedia_ss_vol'));
            $totalTarget = $totalDist + $totalTersedia;

            $rekapData[] = [
                'satker'  => $namaSatker,
                'total'   => $totalTarget,
                'kontrol' => 0.00,
                'rowspan' => count($subRows),
                'rows'    => $subRows,
            ];
        }

        return $rekapData;
    }

    private function getFilterDates($startDate, $endDate)
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end   = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfMonth();

        return [$start, $end];
    }
}