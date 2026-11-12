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

        [$start, $end, $periode] = $this->getFilterDates($request);

        $rekapData = $this->getRekapWilayahData($user->provinsi_id, $namaProvinsi, $start, $end);
        
        return view('reports.petugas.rekap_wilayah', compact('rekapData', 'namaProvinsi', 'periode', 'start', 'end'));
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $namaProvinsi = $user->provinsi->nama_provinsi ?? 'Wilayah Saya';
        
        [$start, $end, $periode] = $this->getFilterDates($request);

        $rekapData = $this->getRekapWilayahData($user->provinsi_id, $namaProvinsi, $start, $end);

        $pdf = Pdf::loadView('reports.pdf.rekap_wilayah_pdf', compact('rekapData', 'namaProvinsi', 'periode', 'start', 'end'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('Rekap_Stok_Benih_Kabupaten_' . str_replace(' ', '_', $namaProvinsi) . '_' . date('Ymd') . '.pdf');
    }

    private function getRekapWilayahData($provinsiId, $namaProvinsi, $start, $end)
    {
        $tableName = (new Wilayah)->getTable();
        $kabColumn = Schema::hasColumn($tableName, 'nama_kabupaten')
            ? 'nama_kabupaten'
            : (Schema::hasColumn($tableName, 'kabupaten') ? 'kabupaten' : null);

        $query = Wilayah::query();

        if ($provinsiId) {
            $query->where('provinsi_id', $provinsiId);
        } elseif (Schema::hasColumn($tableName, 'nama_provinsi')) {
            $query->where('nama_provinsi', $namaProvinsi);
        } else {
            return [];
        }

        // Filter kota berdasarkan rentang tanggal input (created_at)
        if (Schema::hasColumn($tableName, 'created_at')) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        if ($kabColumn) {
            $query->orderBy($kabColumn, 'asc');
        } else {
            $query->latest();
        }

        $wilayahs = $query->get();

        // JIKA KOSONG: Kembalikan array kosong agar tabel menampilkan pesan "Belum ada data"
        if ($wilayahs->isEmpty()) {
            return [];
        }

        $rekapData = [];
        $startDateStr = $start->format('Y-m-d');
        $endDateStr   = $end->format('Y-m-d');

        // Cek kolom relasi kota di tabel stok_benihs & catatan_panens (jika ada)
        $stokHasWilayahId  = Schema::hasColumn('stok_benihs', 'wilayah_id');
        $stokHasKabId      = Schema::hasColumn('stok_benihs', 'kabupaten_id');
        $panenHasWilayahId = Schema::hasColumn('catatan_panens', 'wilayah_id');
        $panenHasKabId     = Schema::hasColumn('catatan_panens', 'kabupaten_id');

        foreach ($wilayahs as $wil) {
            $namaKabupaten = $wil->nama_kabupaten ?? $wil->kabupaten ?? $namaProvinsi;

            // Ambil master stok benih milik provinsi/kota ini
            $stokQuery = StokBenih::where('provinsi_id', $provinsiId)->with('benih');
            if ($stokHasWilayahId) {
                $stokQuery->where('wilayah_id', $wil->id);
            } elseif ($stokHasKabId) {
                $stokQuery->where('kabupaten_id', $wil->id);
            }
            $stokItems = $stokQuery->get();

            $subRows = [];

            if ($stokItems->isNotEmpty()) {
                foreach ($stokItems as $s) {
                    $varName = $s->benih->varietas ?? '-';
                    $kelas   = strtoupper($s->benih->kelas_benih ?? 'BR');
                    $isFS    = ($kelas === 'BD' || $kelas === 'FS');

                    // 1. Transaksi Keluar pada rentang tanggal filter
                    $volDist = (float) TransaksiStokBenih::where('stok_benih_id', $s->id)
                                ->where('jenis_transaksi', 'keluar')
                                ->whereBetween('tanggal_transaksi', [$startDateStr, $endDateStr])
                                ->sum('jumlah_kg');

                    // 2. Transaksi Masuk pada rentang tanggal filter
                    $volMasuk = (float) TransaksiStokBenih::where('stok_benih_id', $s->id)
                                ->where('jenis_transaksi', 'masuk')
                                ->whereBetween('tanggal_transaksi', [$startDateStr, $endDateStr])
                                ->sum('jumlah_kg');

                    // 3. Catatan Panen pada rentang tanggal filter
                    $panenQuery = CatatanPanen::where('provinsi_id', $provinsiId)
                                ->whereBetween('tanggal_panen', [$startDateStr, $endDateStr]);
                    if ($panenHasWilayahId) {
                        $panenQuery->where('wilayah_id', $wil->id);
                    } elseif ($panenHasKabId) {
                        $panenQuery->where('kabupaten_id', $wil->id);
                    }
                    $panen = $panenQuery->first();

                    $volPanen = $panen ? (float) $panen->jumlah_panen_ton : 0.00;
                    $tglPanen = $panen ? Carbon::parse($panen->tanggal_panen)->format('d/m/Y') : '-';

                    $adaAktivitas = ($volDist > 0 || $volMasuk > 0 || $volPanen > 0 || (float) $s->jumlah_stok_kg > 0);
                    $volTersedia  = (float) $s->jumlah_stok_kg;

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

    private function getFilterDates(Request $request)
    {
        $periode = $request->get('periode', 'hari_ini');

        // Jika user memilih rentang tanggal custom dari kalender
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end   = Carbon::parse($request->end_date)->endOfDay();
            return [$start, $end, $request->get('periode', 'custom')];
        }

        // Preset periode cepat: Hari Ini (default), Mingguan, Bulanan, Tahunan
        switch ($periode) {
            case 'mingguan':
                $start = Carbon::now()->startOfWeek();
                $end   = Carbon::now()->endOfWeek();
                break;
            case 'bulanan':
                $start = Carbon::now()->startOfMonth();
                $end   = Carbon::now()->endOfMonth();
                break;
            case 'tahunan':
                $start = Carbon::now()->startOfYear();
                $end   = Carbon::now()->endOfYear();
                break;
            case 'hari_ini':
            default:
                $start = Carbon::today()->startOfDay();
                $end   = Carbon::today()->endOfDay();
                $periode = 'hari_ini';
                break;
        }

        return [$start, $end, $periode];
    }
}