use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

public function exportExcel(Request $request)
{
    [$start, $end] = $this->getFilterDates($request->start_date, $request->end_date);
    $rekapData = $this->getRekapNasionalData($start, $end);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Rekap Nasional');

    $sheet->setShowGridlines(true);

    // Judul Laporan
    $sheet->setCellValue('A1', 'REKAPITULASI STOK BENIH NASIONAL LINGKUP BBPSIP (38 PROVINSI)');
    $sheet->setCellValue('A2', 'Periode: ' . $start->format('d M Y') . ' s.d ' . $end->format('d M Y') . ' | Dicetak: ' . now()->timezone('Asia/Jakarta')->format('d-m-Y H:i') . ' WIB');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13)->getColor()->setARGB('106742');

    // Header Tabel (Baris 4)
    $headers = [
        'A4' => 'No', 'B4' => 'Satker', 
        'C4' => 'Target FS Vol', 'D4' => 'Target FS Var', 'E4' => 'Target SS Vol', 'F4' => 'Target SS Var',
        'G4' => 'Total', 'H4' => 'Kontrol',
        'I4' => 'Distribusi FS Vol', 'J4' => 'Distribusi FS Var', 'K4' => 'Distribusi SS Vol', 'L4' => 'Distribusi SS Var',
        'M4' => 'Tersedia FS Vol', 'N4' => 'Tersedia FS Var', 'O4' => 'Tersedia SS Vol', 'P4' => 'Tersedia SS Var',
        'Q4' => 'Sertifikasi FS Vol', 'R4' => 'Sertifikasi FS Var', 'S4' => 'Sertifikasi SS Vol', 'T4' => 'Sertifikasi SS Var',
        'U4' => 'Prosesing FS Vol', 'V4' => 'Prosesing FS Var', 'W4' => 'Prosesing SS Vol', 'X4' => 'Prosesing SS Var',
        'Y4' => 'Panen FS Vol', 'Z4' => 'Panen FS Var', 'AA4' => 'Panen FS Tgl',
        'AB4' => 'Panen SS Vol', 'AC4' => 'Panen SS Var', 'AD4' => 'Panen SS Tgl'
    ];

    foreach ($headers as $cell => $text) {
        $sheet->setCellValue($cell, $text);
    }

    $sheet->getStyle('A4:AD4')->applyFromArray([
        'font' => ['bold' => true, 'size' => 10],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'E2E8F0']],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ]);

    // Looping Data
    $rowNum = 5;
    foreach ($rekapData as $index => $satkerData) {
        $firstRow = true;
        $rowspan = count($satkerData['rows']);

        foreach ($satkerData['rows'] as $row) {
            if ($firstRow) {
                $sheet->setCellValue('A' . $rowNum, $index + 1);
                $sheet->setCellValue('B' . $rowNum, $satkerData['satker']);
                $sheet->setCellValue('G' . $rowNum, (float)$satkerData['total']);
                $sheet->setCellValue('H' . $rowNum, (float)$satkerData['kontrol']);
            }

            $sheet->setCellValue('C' . $rowNum, (float)$row['target_fs_vol']);
            $sheet->setCellValue('D' . $rowNum, $row['target_fs_var']);
            $sheet->setCellValue('E' . $rowNum, (float)$row['target_ss_vol']);
            $sheet->setCellValue('F' . $rowNum, $row['target_ss_var']);

            $sheet->setCellValue('I' . $rowNum, (float)$row['distribusi_fs_vol']);
            $sheet->setCellValue('J' . $rowNum, $row['distribusi_fs_var']);
            $sheet->setCellValue('K' . $rowNum, (float)$row['distribusi_ss_vol']);
            $sheet->setCellValue('L' . $rowNum, $row['distribusi_ss_var']);

            $sheet->setCellValue('M' . $rowNum, (float)$row['tersedia_fs_vol']);
            $sheet->setCellValue('N' . $rowNum, $row['tersedia_fs_var']);
            $sheet->setCellValue('O' . $rowNum, (float)$row['tersedia_ss_vol']);
            $sheet->setCellValue('P' . $rowNum, $row['tersedia_ss_var']);

            $sheet->setCellValue('Q' . $rowNum, (float)($row['sertifikasi_fs_vol'] ?? 0));
            $sheet->setCellValue('R' . $rowNum, $row['sertifikasi_fs_var'] ?? '-');
            $sheet->setCellValue('S' . $rowNum, (float)($row['sertifikasi_ss_vol'] ?? 0));
            $sheet->setCellValue('T' . $rowNum, $row['sertifikasi_ss_var'] ?? '-');

            $sheet->setCellValue('U' . $rowNum, (float)($row['prosesing_fs_vol'] ?? 0));
            $sheet->setCellValue('V' . $rowNum, $row['prosesing_fs_var'] ?? '-');
            $sheet->setCellValue('W' . $rowNum, (float)($row['prosesing_ss_vol'] ?? 0));
            $sheet->setCellValue('X' . $rowNum, $row['prosesing_ss_var'] ?? '-');

            $sheet->setCellValue('Y' . $rowNum, (float)$row['persiapan_fs_vol']);
            $sheet->setCellValue('Z' . $rowNum, $row['persiapan_fs_var']);
            $sheet->setCellValue('AA' . $rowNum, $row['persiapan_fs_tgl']);

            $sheet->setCellValue('AB' . $rowNum, (float)$row['persiapan_ss_vol']);
            $sheet->setCellValue('AC' . $rowNum, $row['persiapan_ss_var']);
            $sheet->setCellValue('AD' . $rowNum, $row['persiapan_ss_tgl']);

            $sheet->getStyle("A{$rowNum}:AD{$rowNum}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            $rowNum++;
            $firstRow = false;
        }
    }

    $fileName = 'Rekap_Stok_Benih_BBPSIP_' . date('Ymd_His') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}