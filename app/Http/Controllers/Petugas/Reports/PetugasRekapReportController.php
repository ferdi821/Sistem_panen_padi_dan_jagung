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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $namaProvinsi = $user->provinsi->nama_provinsi ?? 'Wilayah Saya';
        
        [$start, $end, $periode] = $this->getFilterDates($request);
        $rekapData = $this->getRekapWilayahData($user->provinsi_id, $namaProvinsi, $start, $end);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Stok Benih Wilayah');
        $sheet->setShowGridlines(true);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        $sheet->setCellValue('A1', 'REKAPITULASI STOK BENIH WILAYAH ' . strtoupper($namaProvinsi));
        $sheet->mergeCells('A1:AD1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF106742');

        $sheet->setCellValue('A2', 'Periode: ' . $start->format('d M Y') . ' s.d ' . $end->format('d M Y') . ' | Dicetak: ' . Carbon::now()->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') . ' WIB');
        $sheet->mergeCells('A2:AD2');
        $sheet->getStyle('A2')->getFont()->setSize(10)->getColor()->setARGB('FF333333');

        $sheet->setCellValue('A4', 'No')->mergeCells('A4:A6');
        $sheet->setCellValue('B4', 'Kabupaten / Kota')->mergeCells('B4:B6');
        $sheet->setCellValue('C4', 'Target (Ton)')->mergeCells('C4:F4');
        $sheet->setCellValue('G4', 'Total')->mergeCells('G4:G6');
        $sheet->setCellValue('H4', 'Kontrol')->mergeCells('H4:H6');
        $sheet->setCellValue('I4', 'Sudah Terdistribusi')->mergeCells('I4:L4');
        $sheet->setCellValue('M4', 'Benih Tersedia (Tersertifikasi)')->mergeCells('M4:P4');
        $sheet->setCellValue('Q4', 'Benih (Proses Sertifikasi)')->mergeCells('Q4:T4');
        $sheet->setCellValue('U4', 'Benih (Prosesing)')->mergeCells('U4:X4');
        $sheet->setCellValue('Y4', 'Benih (Persiapan Panen)')->mergeCells('Y4:AD4');

        $sheet->setCellValue('C5', 'FS')->mergeCells('C5:D5');
        $sheet->setCellValue('E5', 'SS')->mergeCells('E5:F5');
        $sheet->setCellValue('I5', 'FS')->mergeCells('I5:J5');
        $sheet->setCellValue('K5', 'SS')->mergeCells('K5:L5');
        $sheet->setCellValue('M5', 'FS')->mergeCells('M5:N5');
        $sheet->setCellValue('O5', 'SS')->mergeCells('O5:P5');
        $sheet->setCellValue('Q5', 'FS')->mergeCells('Q5:R5');
        $sheet->setCellValue('S5', 'SS')->mergeCells('S5:T5');
        $sheet->setCellValue('U5', 'FS')->mergeCells('U5:V5');
        $sheet->setCellValue('W5', 'SS')->mergeCells('W5:X5');
        $sheet->setCellValue('Y5', 'FS')->mergeCells('Y5:AA5');
        $sheet->setCellValue('AB5', 'SS')->mergeCells('AB5:AD5');

        $subHeaders = [
            'C6' => 'Volume (Ton)', 'D6' => 'Varietas', 'E6' => 'Volume (Ton)', 'F6' => 'Varietas',
            'I6' => 'Volume (Ton)', 'J6' => 'Varietas', 'K6' => 'Volume (Ton)', 'L6' => 'Varietas',
            'M6' => 'Volume (Ton)', 'N6' => 'Varietas', 'O6' => 'Volume (Ton)', 'P6' => 'Varietas',
            'Q6' => 'Volume (Ton)', 'R6' => 'Varietas', 'S6' => 'Volume (Ton)', 'T6' => 'Varietas',
            'U6' => 'Volume (Ton)', 'V6' => 'Varietas', 'W6' => 'Volume (Ton)', 'X6' => 'Varietas',
            'Y6' => 'Volume (Ton)', 'Z6' => 'Varietas', 'AA6' => 'Tgl Panen',
            'AB6' => 'Volume (Ton)', 'AC6' => 'Varietas', 'AD6' => 'Tgl Panen',
        ];
        foreach ($subHeaders as $cell => $label) {
            $sheet->setCellValue($cell, $label);
        }

        $sheet->getStyle('A4:AD6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);

        $headerColors = [
            'A4:B6'   => 'FFE2E8F0',
            'C4:F4'   => 'FFDBEAFE', 'C5:F5' => 'FFEFF6FF',
            'G4:G6'   => 'FF64748B',
            'H4:H6'   => 'FFFACC15',
            'I4:L4'   => 'FFD1FAE5', 'I5:L5' => 'FFECFDF5',
            'M4:P4'   => 'FFE0F2FE', 'M5:P5' => 'FFF0F9FF',
            'Q4:T4'   => 'FFEDE9FE', 'Q5:T5' => 'FFF5F3FF',
            'U4:X4'   => 'FFFEF3C7', 'U5:X5' => 'FFFFFBEB',
            'Y4:AD4'  => 'FFFFEDD5', 'Y5:AD5' => 'FFFFF7ED',
            'C6:F6'   => 'FFF8FAFC', 'I6:AD6' => 'FFF8FAFC',
        ];
        foreach ($headerColors as $range => $argb) {
            $sheet->getStyle($range)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($argb);
        }
        $sheet->getStyle('G4:G6')->getFont()->getColor()->setARGB('FFFFFFFF');

        $sheet->getRowDimension(4)->setRowHeight(24);
        $sheet->getRowDimension(5)->setRowHeight(20);
        $sheet->getRowDimension(6)->setRowHeight(26);

        $rowNum = 7;
        $volCols = ['C', 'E', 'G', 'H', 'I', 'K', 'M', 'O', 'Q', 'S', 'U', 'W', 'Y', 'AB'];

        foreach ($rekapData as $index => $kabData) {
            $startRow = $rowNum;
            $rowspan  = count($kabData['rows']);

            foreach ($kabData['rows'] as $rIdx => $row) {
                if ($rIdx === 0) {
                    $sheet->setCellValue('A' . $rowNum, $index + 1);
                    $sheet->setCellValue('B' . $rowNum, $kabData['satker']);
                    $sheet->setCellValue('G' . $rowNum, (float) $kabData['total']);
                    $sheet->setCellValue('H' . $rowNum, (float) $kabData['kontrol']);
                }

                $sheet->setCellValue('C' . $rowNum, (float) $row['target_fs_vol']);
                $sheet->setCellValue('D' . $rowNum, $row['target_fs_var']);
                $sheet->setCellValue('E' . $rowNum, (float) $row['target_ss_vol']);
                $sheet->setCellValue('F' . $rowNum, $row['target_ss_var']);

                $sheet->setCellValue('I' . $rowNum, (float) $row['distribusi_fs_vol']);
                $sheet->setCellValue('J' . $rowNum, $row['distribusi_fs_var']);
                $sheet->setCellValue('K' . $rowNum, (float) $row['distribusi_ss_vol']);
                $sheet->setCellValue('L' . $rowNum, $row['distribusi_ss_var']);

                $sheet->setCellValue('M' . $rowNum, (float) $row['tersedia_fs_vol']);
                $sheet->setCellValue('N' . $rowNum, $row['tersedia_fs_var']);
                $sheet->setCellValue('O' . $rowNum, (float) $row['tersedia_ss_vol']);
                $sheet->setCellValue('P' . $rowNum, $row['tersedia_ss_var']);

                $sheet->setCellValue('Q' . $rowNum, (float) ($row['sertifikasi_fs_vol'] ?? 0));
                $sheet->setCellValue('R' . $rowNum, $row['sertifikasi_fs_var'] ?? '-');
                $sheet->setCellValue('S' . $rowNum, (float) ($row['sertifikasi_ss_vol'] ?? 0));
                $sheet->setCellValue('T' . $rowNum, $row['sertifikasi_ss_var'] ?? '-');

                $sheet->setCellValue('U' . $rowNum, (float) ($row['prosesing_fs_vol'] ?? 0));
                $sheet->setCellValue('V' . $rowNum, $row['prosesing_fs_var'] ?? '-');
                $sheet->setCellValue('W' . $rowNum, (float) ($row['prosesing_ss_vol'] ?? 0));
                $sheet->setCellValue('X' . $rowNum, $row['prosesing_ss_var'] ?? '-');

                $sheet->setCellValue('Y' . $rowNum, (float) $row['persiapan_fs_vol']);
                $sheet->setCellValue('Z' . $rowNum, $row['persiapan_fs_var']);
                $sheet->setCellValue('AA' . $rowNum, $row['persiapan_fs_tgl']);

                $sheet->setCellValue('AB' . $rowNum, (float) $row['persiapan_ss_vol']);
                $sheet->setCellValue('AC' . $rowNum, $row['persiapan_ss_var']);
                $sheet->setCellValue('AD' . $rowNum, $row['persiapan_ss_tgl']);

                $rowNum++;
            }

            $endRow = $rowNum - 1;
            if ($rowspan > 1) {
                $sheet->mergeCells("A{$startRow}:A{$endRow}");
                $sheet->mergeCells("B{$startRow}:B{$endRow}");
                $sheet->mergeCells("G{$startRow}:G{$endRow}");
                $sheet->mergeCells("H{$startRow}:H{$endRow}");
            }

            $sheet->getStyle("A{$startRow}:B{$endRow}")->getFont()->setBold(true);
            $sheet->getStyle("A{$startRow}:A{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$startRow}:G{$endRow}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF1F5F9']],
            ]);
            $sheet->getStyle("H{$startRow}:H{$endRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FFB91C1C']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFEF9C3']],
            ]);
        }

        $lastDataRow = max(7, $rowNum - 1);
        $sheet->getStyle("A7:AD{$lastDataRow}")->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
            ],
        ]);
        $sheet->getStyle("AA7:AA{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("AD7:AD{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        foreach ($volCols as $col) {
            $sheet->getStyle("{$col}7:{$col}{$lastDataRow}")
                  ->getNumberFormat()
                  ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("{$col}7:{$col}{$lastDataRow}")
                  ->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $colWidths = [
            'A' => 6,  'B' => 28,
            'C' => 13, 'D' => 18, 'E' => 13, 'F' => 18,
            'G' => 13, 'H' => 13,
            'I' => 13, 'J' => 18, 'K' => 13, 'L' => 18,
            'M' => 13, 'N' => 18, 'O' => 13, 'P' => 18,
            'Q' => 13, 'R' => 18, 'S' => 13, 'T' => 18,
            'U' => 13, 'V' => 18, 'W' => 13, 'X' => 18,
            'Y' => 13, 'Z' => 18, 'AA' => 14,
            'AB' => 13, 'AC' => 18, 'AD' => 14,
        ];
        foreach ($colWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $fileName = 'Rekap_Stok_Benih_Kabupaten_' . str_replace(' ', '_', $namaProvinsi) . '_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
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

        if ($kabColumn) {
            $query->orderBy($kabColumn, 'asc');
        } else {
            $query->latest();
        }

        $wilayahs = $query->get();

        if ($wilayahs->isEmpty()) {
            return [];
        }

        $rekapData = [];
        $startDateStr = $start->format('Y-m-d');
        $endDateStr   = $end->format('Y-m-d');

        $stokHasWilayahId  = Schema::hasColumn('stok_benihs', 'wilayah_id');
        $panenHasWilayahId = Schema::hasColumn('catatan_panens', 'wilayah_id');
        $panenHasKabId     = Schema::hasColumn('catatan_panens', 'kabupaten_id');

        foreach ($wilayahs as $wil) {
            $namaKabupaten = $wil->nama_kabupaten ?? $wil->kabupaten ?? $namaProvinsi;

            $stokQuery = StokBenih::where('provinsi_id', $provinsiId)
                ->whereBetween('updated_at', [$start, $end])
                ->with('benih');
            if ($stokHasWilayahId) {
                $stokQuery->where('wilayah_id', $wil->id);
            }
            $stokItems = $stokQuery->get();

            $subRows = [];

            if ($stokItems->isNotEmpty()) {
                foreach ($stokItems as $s) {
                    $varName = $s->benih->varietas ?? '-';
                    $kelas   = strtoupper($s->kelas_benih ?? ($s->benih->kelas_benih ?? 'FS'));
                    $isFS    = ($kelas === 'FS' || $kelas === 'BD');

                    $volDist = (float) TransaksiStokBenih::where('stok_benih_id', $s->id)
                                ->where('jenis_transaksi', 'keluar')
                                ->whereBetween('tanggal_transaksi', [$startDateStr, $endDateStr])
                                ->sum('jumlah_kg');

                    $panenQuery = CatatanPanen::where('provinsi_id', $provinsiId)
                                ->whereBetween('tanggal_panen', [$startDateStr, $endDateStr]);
                    if ($panenHasWilayahId) {
                        $panenQuery->where('wilayah_id', $wil->id);
                    } elseif ($panenHasKabId) {
                        $panenQuery->where('kabupaten_id', $wil->id);
                    }
                    $panen = $panenQuery->first();

                    $volPanen       = $panen ? (float) $panen->jumlah_panen_ton : 0.00;
                    $tglPanen       = $panen ? Carbon::parse($panen->tanggal_panen)->format('d/m/Y') : '-';
                    $volTarget      = (float) ($s->target_ton ?? 0);
                    $volTersedia    = (float) ($s->jumlah_stok_kg ?? 0);
                    $volSertifikasi = (float) ($s->proses_sertifikasi_ton ?? 0);
                    $volProsesing   = (float) ($s->prosesing_ton ?? 0);

                    $adaData = ($volTarget > 0 || $volDist > 0 || $volTersedia > 0 || $volSertifikasi > 0 || $volProsesing > 0 || $volPanen > 0);

                    if ($adaData) {
                        $subRows[] = [
                            'target_fs_vol'      => $isFS ? $volTarget : 0.00,
                            'target_fs_var'      => ($isFS && ($volTarget > 0 || $volDist > 0 || $volTersedia > 0 || $volSertifikasi > 0 || $volProsesing > 0)) ? $varName : '-',
                            'target_ss_vol'      => !$isFS ? $volTarget : 0.00,
                            'target_ss_var'      => (!$isFS && ($volTarget > 0 || $volDist > 0 || $volTersedia > 0 || $volSertifikasi > 0 || $volProsesing > 0)) ? $varName : '-',
                            
                            'distribusi_fs_vol'  => $isFS ? $volDist : 0.00,
                            'distribusi_fs_var'  => ($isFS && $volDist > 0) ? $varName : '-',
                            'distribusi_ss_vol'  => !$isFS ? $volDist : 0.00,
                            'distribusi_ss_var'  => (!$isFS && $volDist > 0) ? $varName : '-',

                            'tersedia_fs_vol'    => $isFS ? $volTersedia : 0.00,
                            'tersedia_fs_var'    => ($isFS && $volTersedia > 0) ? $varName : '-',
                            'tersedia_ss_vol'    => !$isFS ? $volTersedia : 0.00,
                            'tersedia_ss_var'    => (!$isFS && $volTersedia > 0) ? $varName : '-',

                            'sertifikasi_fs_vol' => $isFS ? $volSertifikasi : 0.00,
                            'sertifikasi_fs_var' => ($isFS && $volSertifikasi > 0) ? $varName : '-',
                            'sertifikasi_ss_vol' => !$isFS ? $volSertifikasi : 0.00,
                            'sertifikasi_ss_var' => (!$isFS && $volSertifikasi > 0) ? $varName : '-',

                            'prosesing_fs_vol'   => $isFS ? $volProsesing : 0.00,
                            'prosesing_fs_var'   => ($isFS && $volProsesing > 0) ? $varName : '-',
                            'prosesing_ss_vol'   => !$isFS ? $volProsesing : 0.00,
                            'prosesing_ss_var'   => (!$isFS && $volProsesing > 0) ? $varName : '-',

                            'persiapan_fs_vol'   => $isFS ? $volPanen : 0.00,
                            'persiapan_fs_var'   => ($isFS && $volPanen > 0) ? $varName : '-',
                            'persiapan_fs_tgl'   => ($isFS && $volPanen > 0) ? $tglPanen : '-',
                            
                            'persiapan_ss_vol'   => !$isFS ? $volPanen : 0.00,
                            'persiapan_ss_var'   => (!$isFS && $volPanen > 0) ? $varName : '-',
                            'persiapan_ss_tgl'   => (!$isFS && $volPanen > 0) ? $tglPanen : '-',
                        ];
                    }
                }
            }

            if (empty($subRows)) {
                $subRows = [[
                    'target_fs_vol' => 0.00, 'target_fs_var' => '-', 'target_ss_vol' => 0.00, 'target_ss_var' => '-',
                    'distribusi_fs_vol' => 0.00, 'distribusi_fs_var' => '-', 'distribusi_ss_vol' => 0.00, 'distribusi_ss_var' => '-',
                    'tersedia_fs_vol' => 0.00, 'tersedia_fs_var' => '-', 'tersedia_ss_vol' => 0.00, 'tersedia_ss_var' => '-',
                    'sertifikasi_fs_vol' => 0.00, 'sertifikasi_fs_var' => '-', 'sertifikasi_ss_vol' => 0.00, 'sertifikasi_ss_var' => '-',
                    'prosesing_fs_vol' => 0.00, 'prosesing_fs_var' => '-', 'prosesing_ss_vol' => 0.00, 'prosesing_ss_var' => '-',
                    'persiapan_fs_vol' => 0.00, 'persiapan_fs_var' => '-', 'persiapan_fs_tgl' => '-',
                    'persiapan_ss_vol' => 0.00, 'persiapan_ss_var' => '-', 'persiapan_ss_tgl' => '-',
                ]];
            }

            $totalTargetFS = array_sum(array_column($subRows, 'target_fs_vol'));
            $totalTargetSS = array_sum(array_column($subRows, 'target_ss_vol'));
            $totalTarget   = $totalTargetFS + $totalTargetSS;

            $rekapData[] = [
                'satker'  => $namaKabupaten,
                'total'   => $totalTarget,
                'kontrol' => $totalTarget,
                'rowspan' => count($subRows),
                'rows'    => $subRows,
            ];
        }

        return $rekapData;
    }

    private function getFilterDates(Request $request)
    {
        if ($request->filled('start_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end   = $request->filled('end_date')
                ? Carbon::parse($request->end_date)->endOfDay()
                : Carbon::parse($request->start_date)->endOfDay();
            return [$start, $end, 'harian'];
        }

        return [Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), 'hari_ini'];
    }
}