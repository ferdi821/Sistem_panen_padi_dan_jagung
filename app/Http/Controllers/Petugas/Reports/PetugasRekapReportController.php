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
        
        $detailVarietas = StokBenih::with(['benih'])
            ->where('provinsi_id', $user->provinsi_id)
            ->get();

        return view('reports.petugas.rekap_wilayah', compact(
            'rekapData', 
            'detailVarietas', 
            'namaProvinsi', 
            'periode', 
            'start', 
            'end'
        ));
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
        // Mengambil daftar wilayah/kabupaten berdasarkan nama provinsi
        $wilayahs = Wilayah::where('nama_provinsi', $namaProvinsi)->orderBy('nama_kabupaten', 'asc')->get();
        $rekapData = [];

        foreach ($wilayahs as $wil) {
            $targetFsTotal = 0.00; 
            $targetSsTotal = 0.00; 
            $totalTarget = $targetFsTotal + $targetSsTotal;

            // Ambil stok benih berdasarkan provinsi_id milik petugas
            $stokItems = StokBenih::where('provinsi_id', $provinsiId)->with('benih')->get();
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
                    $volTersediaFs = (float)($stokItems->filter(fn($s) => ($s->benih->varietas ?? '') === $varName && ($s->benih->kelas_benih ?? '') === 'FS')->sum('jumlah_stok_kg') / 1000);
                    $volTersediaSs = (float)($stokItems->filter(fn($s) => ($s->benih->varietas ?? '') === $varName && ($s->benih->kelas_benih ?? '') === 'SS')->sum('jumlah_stok_kg') / 1000);

                    $volDistFs = 0.00;
                    $volDistSs = 0.00;
                    $persiapanFsVol = 0.00;
                    $persiapanSsVol = 0.00;

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

                        'persiapan_fs_vol' => $persiapanFsVol,
                        'persiapan_fs_var' => '-',
                        'persiapan_fs_tgl' => '-',
                        'persiapan_ss_vol' => $persiapanSsVol,
                        'persiapan_ss_var' => '-',
                        'persiapan_ss_tgl' => '-',
                    ];
                }
            }

            $totalDist = array_sum(array_column($subRows, 'distribusi_fs_vol')) + array_sum(array_column($subRows, 'distribusi_ss_vol'));
            $totalTersedia = array_sum(array_column($subRows, 'tersedia_fs_vol')) + array_sum(array_column($subRows, 'tersedia_ss_vol'));
            $kontrol = $totalTarget - ($totalDist + $totalTersedia);

            $rekapData[] = [
                'satker' => $wil->nama_kabupaten,
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