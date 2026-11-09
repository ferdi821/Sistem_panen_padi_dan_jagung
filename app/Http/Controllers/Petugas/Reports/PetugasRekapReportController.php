<?php

namespace App\Http\Controllers\Petugas\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wilayah;
use App\Models\CatatanPanen;
use App\Models\StokBenih;
use App\Models\TransaksiStokBenih;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PetugasRekapReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $namaProvinsi = $user->provinsi->nama_provinsi ?? 'Wilayah Saya';

        $periode = $request->input('periode', 'bulan_ini');
        [$start, $end] = $this->getFilterDates($periode, $request->start_date, $request->end_date);

        $rekapData = $this->getRekapWilayahData($user->provinsi_id, $namaProvinsi, $start, $end);
        
        return view('reports.petugas.rekap_wilayah', compact('rekapData', 'namaProvinsi', 'periode', 'start', 'end'));
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $namaProvinsi = $user->provinsi->nama_provinsi ?? 'Wilayah Saya';
        $periode = $request->input('periode', 'bulan_ini');
        [$start, $end] = $this->getFilterDates($periode, $request->start_date, $request->end_date);

        $rekapData = $this->getRekapWilayahData($user->provinsi_id, $namaProvinsi, $start, $end);

        $pdf = Pdf::loadView('reports.pdf.rekap_wilayah_pdf', compact('rekapData', 'namaProvinsi', 'periode', 'start', 'end'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Rekap_Stok_Benih_Kabupaten_' . str_replace(' ', '_', $namaProvinsi) . '_' . date('Ymd') . '.pdf');
    }

    private function getRekapWilayahData($provinsiId, $namaProvinsi, $start, $end)
    {
        $wilayahs = Wilayah::where('nama_provinsi', $namaProvinsi)->orderBy('nama_kabupaten', 'asc')->get();
        if ($wilayahs->isEmpty()) {
            $wilayahs = collect([(object)['nama_kabupaten' => $namaProvinsi]]);
        }

        $rekapData = [];

        foreach ($wilayahs as $wil) {
            $stokItems = StokBenih::where('provinsi_id', $provinsiId)->with('benih')->get();

            if ($stokItems->isEmpty()) {
                $subRows = [[
                    'target_fs_vol' => 0.00, 'target_fs_var' => '-', 'target_ss_vol' => 0.00, 'target_ss_var' => '-',
                    'distribusi_fs_vol' => 0.00, 'distribusi_fs_var' => '-', 'distribusi_ss_vol' => 0.00, 'distribusi_ss_var' => '-',
                    'tersedia_fs_vol' => 0.00, 'tersedia_fs_var' => '-', 'tersedia_ss_vol' => 0.00, 'tersedia_ss_var' => '-',
                    'prosesing_fs_vol' => 0.00, 'prosesing_fs_var' => '-', 'prosesing_ss_vol' => 0.00, 'prosesing_ss_var' => '-',
                    'persiapan_fs_vol' => 0.00, 'persiapan_fs_var' => '-', 'persiapan_fs_tgl' => '-',
                    'persiapan_ss_vol' => 0.00, 'persiapan_ss_var' => '-', 'persiapan_ss_tgl' => '-',
                ]];
            } else {
                $subRows = [];
                foreach ($stokItems as $s) {
                    $varName = $s->benih->varietas ?? '-';
                    $kelas = strtoupper($s->benih->kelas_benih ?? 'BR');
                    $isFS = ($kelas === 'BD' || $kelas === 'FS');

                    // Ambil transaksi keluar real-time berdasarkan stok_benih_id & periode
                    $volDist = (float) TransaksiStokBenih::where('stok_benih_id', $s->id)
                                ->where('jenis_transaksi', 'keluar')
                                ->whereBetween('tanggal_transaksi', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                                ->sum('jumlah_kg');

                    // Ambil data catatan panen real-time berdasarkan periode
                    $panen = CatatanPanen::where('provinsi_id', $provinsiId)
                                ->whereBetween('tanggal_panen', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                                ->first();

                    $volTersedia = (float) $s->jumlah_stok_kg;
                    $volPanen = $panen ? (float) $panen->jumlah_panen_ton : 0.00;
                    $tglPanen = $panen ? Carbon::parse($panen->tanggal_panen)->format('d/m/Y') : '-';

                    $subRows[] = [
                        'target_fs_vol'     => $isFS ? ($volTersedia + $volDist) : 0.00,
                        'target_fs_var'     => $isFS ? $varName : '-',
                        'target_ss_vol'     => !$isFS ? ($volTersedia + $volDist) : 0.00,
                        'target_ss_var'     => !$isFS ? $varName : '-',
                        
                        'distribusi_fs_vol' => $isFS ? $volDist : 0.00,
                        'distribusi_fs_var' => $isFS && $volDist > 0 ? $varName : '-',
                        'distribusi_ss_vol' => !$isFS ? $volDist : 0.00,
                        'distribusi_ss_var' => !$isFS && $volDist > 0 ? $varName : '-',

                        'tersedia_fs_vol'   => $isFS ? $volTersedia : 0.00,
                        'tersedia_fs_var'   => $isFS ? $varName : '-',
                        'tersedia_ss_vol'   => !$isFS ? $volTersedia : 0.00,
                        'tersedia_ss_var'   => !$isFS ? $varName : '-',

                        'prosesing_fs_vol'  => 0.00,
                        'prosesing_fs_var'  => '-',
                        'prosesing_ss_vol'  => 0.00,
                        'prosesing_ss_var'  => '-',

                        'persiapan_fs_vol'  => $isFS ? $volPanen : 0.00,
                        'persiapan_fs_var'  => $isFS ? $varName : '-',
                        'persiapan_fs_tgl'  => $isFS ? $tglPanen : '-',
                        
                        'persiapan_ss_vol'  => !$isFS ? $volPanen : 0.00,
                        'persiapan_ss_var'  => !$isFS ? $varName : '-',
                        'persiapan_ss_tgl'  => !$isFS ? $tglPanen : '-',
                    ];
                }
            }

            $totalDist = array_sum(array_column($subRows, 'distribusi_fs_vol')) + array_sum(array_column($subRows, 'distribusi_ss_vol'));
            $totalTersedia = array_sum(array_column($subRows, 'tersedia_fs_vol')) + array_sum(array_column($subRows, 'tersedia_ss_vol'));
            $totalTarget = $totalDist + $totalTersedia;

            $rekapData[] = [
                'satker'  => $wil->nama_kabupaten,
                'total'   => $totalTarget,
                'kontrol' => 0.00,
                'rowspan' => count($subRows),
                'rows'    => $subRows,
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