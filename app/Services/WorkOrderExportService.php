<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Dompdf\Dompdf;
use Dompdf\Options;

class WorkOrderExportService
{
    /**
     * Generate & stream Department Work Orders Excel using PhpSpreadsheet
     */
    public function exportExcel(array $workOrders, string $downloadedByName = 'Staff'): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(current_locale() === 'en' ? 'Work Order Report' : 'Laporan Work Order');

        // Hotel Header
        $sheet->setCellValue('A1', lang('Report.hotel_title'));
        $sheet->setCellValue('A2', lang('Report.excel_main_header'));
        $sheet->setCellValue('A3', lang('Report.download_date', [date('d F Y H:i:s'), $downloadedByName]));

        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A3')->getFont()->setItalic(true)->setSize(9);

        // Table Headers
        $headers = [
            'A5' => lang('Report.col_no'),
            'B5' => lang('Report.col_wo_code'),
            'C5' => lang('Report.col_title'),
            'D5' => lang('Report.col_dept'),
            'E5' => lang('Report.col_category'),
            'F5' => lang('Report.col_sla_priority'),
            'G5' => lang('Report.col_status'),
            'H5' => lang('Report.col_requester'),
            'I5' => lang('Report.col_handler'),
            'J5' => lang('Report.col_date'),
            'K5' => lang('Report.col_duration_min'),
            'L5' => lang('Report.col_cost_idr'),
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A5:L5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $row = 6;
        $no = 1;
        $totalCostAll = 0.0;

        foreach ($workOrders as $item) {
            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $item['kode_wo']);
            $sheet->setCellValue("C{$row}", $item['judul_wo']);
            $sheet->setCellValue("D{$row}", $item['nama_departemen']);
            $sheet->setCellValue("E{$row}", translate_category($item['nama_kategori']));
            $sheet->setCellValue("F{$row}", translate_priority($item['tingkat_prioritas']) . ' (' . lang('WorkOrder.sla_hours', [$item['target_jam']]) . ')');
            $sheet->setCellValue("G{$row}", translate_status($item['nama_status']));
            $sheet->setCellValue("H{$row}", $item['nama_pengaju']);
            $sheet->setCellValue("I{$row}", !empty($item['nama_handler']) ? $item['nama_handler'] : lang('WorkOrder.unassigned'));
            $sheet->setCellValue("J{$row}", $item['tanggal_ajuan']);
            $sheet->setCellValue("K{$row}", $item['durasi_menit_pengerjaan'] ?? 0);
            $sheet->setCellValue("L{$row}", (float)$item['total_biaya_sparepart']);

            $totalCostAll += (float)$item['total_biaya_sparepart'];
            $row++;
        }

        // Summary Row
        $sheet->setCellValue("A{$row}", lang('Report.total_expense_label'));
        $sheet->mergeCells("A{$row}:K{$row}");
        $sheet->setCellValue("L{$row}", $totalCostAll);

        $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
        ]);

        // Currency format for column L
        $sheet->getStyle("L6:L{$row}")->getNumberFormat()->setFormatCode('#,##0');

        // Auto width
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Work_Order_GoldenHill_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit();
    }

    /**
     * Generate & stream Single Work Order PDF using Dompdf
     */
    public function exportPdf(array $wo, array $spareparts, array $slaInfo): void
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);

        $html = view('work_order/export_pdf', [
            'wo'         => $wo,
            'spareparts' => $spareparts,
            'slaInfo'    => $slaInfo,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream('Work_Order_' . $wo['kode_wo'] . '.pdf', ['Attachment' => 0]);
        exit();
    }
}
