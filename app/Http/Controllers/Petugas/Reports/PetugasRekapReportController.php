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
use Illuminate\Support\Facades\Schema;

class PetugasRekapReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $namaProvinsi = $user->provinsi->nama_provinsi ?? 'Wilayah Saya';

        [$start, $end] = $this->getFilterDates($request->start_date, $request->end_date);
        $periode = 'custom';

        $rekapData = $this->getRekapWilayahData($user->provinsi_id, $namaProvinsi, $start, $end);
        
        return view('reports.petugas.rekap_wilayah', compact('rekapData', 'namaProvinsi', 'periode', 'start', 'end'));
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $namaProvinsi = $user->provinsi->nama_provinsi ?? 'Wilayah Saya';
        
        [$start, $end] = $this->getFilterDates($request->start_date, $request->end_date);
        $periode = 'custom';

        $rekapData = $this->getRekapWilayahData($user->provinsi_id, $namaProvinsi, $start, $end);

        $pdf = Pdf::loadView('reports.pdf.rekap_wilayah_pdf', compact('rekapData', 'namaProvinsi', 'periode', 'start', 'end'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Rekap_Stok_Benih_Kabupaten_' . str_replace(' ', '_', $namaProvinsi) . '_' . date('Ymd') . '.pdf');
    }

    private function getRekapWilayahData($provinsiId, $namaProvinsi, $start, $end)
    {
        // Deteksi nama kolom kabupaten secara otomatis agar bebas error SQL
        $kabColumn = Schema::hasColumn('wilayahs', 'kabupaten') ? 'kabupaten' : (Schema::hasColumn('wilayahs', 'nama_kabupaten') ? 'nama_kabupaten' : null);

        $query = Wilayah::query();

        if ($provinsiId) {
            $query->where(function($q) use ($provinsiId, $namaProvinsi) {
                $q->where('provinsi_id', $provinsiId);
                if (Schema::hasColumn('wilayahs', 'nama_provinsi')) {
                    $q->orWhere('nama_provinsi', $namaProvinsi);
                }
            });
        } elseif (Schema::hasColumn('wilayahs', 'nama_provinsi')) {
            $query->where('nama_provinsi', $namaProvinsi);
        }

        if ($kabColumn) {
            $query->orderBy($kabColumn, 'asc');
        } else {
            $query->latest();
        }

        $wilayahs = $query->get();

        // JIKA KOSONG: Jangan pakai dummy fiktif "Manggungharja", kembalikan array kosong agar tabel menampilkan pesan "Belum ada data"
        if ($wilayahs->isEmpty()) {
            return [];
        }

        $rekapData = [];
        $startDateStr = $start->format('Y-m-d');
        $endDateStr   = $end->format('Y-m-d');

        foreach ($wilayahs as $wil) {
            $namaKabupaten = $wil->kabupaten ?? $wil->nama_kabupaten ?? $namaProvinsi;

            // Ambil master stok benih milik provinsi ini
            $stokItems = StokBenih::where('provinsi_id', $provinsiId)->with('benih')->get();

            $subRows = [];

            if ($stokItems->isNotEmpty()) {
                foreach ($stokItems as $s) {
                    $varName = $s->benih->varietas ?? '-';
                    $kelas   = strtoupper($s->benih->kelas_benih ?? 'BR');
                    $isFS    = ($kelas === 'BD' || $kelas === 'FS');

                    // 1. Transaksi Keluar HANYA pada rentang tanggal filter
                    $volDist = (float) TransaksiStokBenih::where('stok_benih_id', $s->id)
                                ->where('jenis_transaksi', 'keluar')
                                ->whereBetween('tanggal_transaksi', [$startDateStr, $endDateStr])
                                ->sum('jumlah_kg');

                    // 2. Transaksi Masuk HANYA pada rentang tanggal filter
                    $volMasuk = (float) TransaksiStokBenih::where('stok_benih_id', $s->id)
                                ->where('jenis_transaksi', 'masuk')
                                ->whereBetween('tanggal_transaksi', [$startDateStr, $endDateStr])
                                ->sum('jumlah_kg');

                    // 3. Catatan Panen HANYA pada rentang tanggal filter
                    $panen = CatatanPanen::where('provinsi_id', $provinsiId)
                                ->whereBetween('tanggal_panen', [$startDateStr, $endDateStr])
                                ->first();

                    $volPanen = $panen ? (float) $panen->jumlah_panen_ton : 0.00;
                    $tglPanen = $panen ? Carbon::parse($panen->tanggal_panen)->format('d/m/Y') : '-';

                    $adaAktivitas = ($volDist > 0 || $volMasuk > 0 || $volPanen > 0);
                    $volTersedia  = $adaAktivitas ? (float) $s->jumlah_stok_kg : 0.00;

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
                'satker'  => $namaKabupaten,
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