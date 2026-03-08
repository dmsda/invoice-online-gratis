<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\UserProfileModel;
use App\Models\InvoiceItemModel;
use App\Services\InvoiceReportService;
use App\Services\ClientReportService;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Reports extends BaseController
{
    protected $userProfileModel;
    protected $reportService;
    protected $clientReportService;

    public function __construct()
    {
        $this->reportService = new InvoiceReportService();
        $this->clientReportService = new ClientReportService();
        $this->userProfileModel = new UserProfileModel();
    }

    public function index()
    {
        $userId = (int) session()->get('id');
        
        // 1. Ambil Parameter Default
        $period = $this->request->getGet('period') ?? 'bulan_ini'; // Default: Bulan Ini
        $type   = $this->request->getGet('type') ?? 'sales'; // Default: Penjualan

        // 2. Logic Filter Periode (Sederhana untuk Mobile)
        $startDate = date('Y-m-d');
        $endDate   = date('Y-m-d');
        $periodLabel = 'Hari Ini';

        switch ($period) {
            case 'hari_ini':
                $startDate = date('Y-m-d');
                $endDate   = date('Y-m-d');
                $periodLabel = 'Hari Ini';
                break;
            case 'minggu_ini':
                $startDate = date('Y-m-d', strtotime('monday this week'));
                $endDate   = date('Y-m-d', strtotime('sunday this week'));
                $periodLabel = 'Minggu Ini';
                break;
            case 'bulan_ini':
                $startDate = date('Y-m-01');
                $endDate   = date('Y-m-t');
                $periodLabel = 'Bulan Ini (' . date('M Y') . ')';
                break;
            case 'bulan_lalu':
                $startDate = date('Y-m-01', strtotime('first day of last month'));
                $endDate   = date('Y-m-t', strtotime('last day of last month'));
                $periodLabel = 'Bulan Lalu (' . date('M Y', strtotime('last month')) . ')';
                break;
            case 'tahun_ini':
                $startDate = date('Y-01-01');
                $endDate   = date('Y-12-31');
                $periodLabel = 'Tahun Ini (' . date('Y') . ')';
                break;
            default:
                // Fallback custom if needed, else locked to bulan ini
                $startDate = date('Y-m-01');
                $endDate   = date('Y-m-t');
                $period = 'bulan_ini';
                $periodLabel = 'Bulan Ini (' . date('M Y') . ')';
                break;
        }

        // 3. Query Summary Meta (Cepat & Ringan via Service SSOT)
        $summary = $this->reportService->getSummary($userId, $startDate, $endDate);

        // 4. Query Tipe Laporan (Rows via Service SSOT)
        $rows = [];
        if ($type === 'sales') {
            $rows = $this->reportService->getSalesData($userId, $startDate, $endDate);
        } elseif ($type === 'receivables') {
            $rows = $this->reportService->getReceivablesData($userId, $startDate, $endDate);
        } elseif ($type === 'all') {
            $rows = $this->reportService->getAllTransactionsData($userId, $startDate, $endDate);
        } elseif ($type === 'clients') {
            $rows = $this->reportService->getClientPerformance($userId, $startDate, $endDate);
        }

        // Prepare data for view
        $data = [
            'meta'    => [
                'start' => $startDate, 
                'end' => $endDate, 
                'type' => $type, 
                'period' => $period,
                'periodLabel' => $periodLabel
            ],
            'summary' => $summary,
            'rows'    => $rows,
            'title'   => 'Laporan & Analitik',
            'user'    => $this->userProfileModel->where('user_id', $userId)->first()
        ];

        // 4. Jika export PDF
        if ($this->request->getGet('export') === 'pdf') {
            return $this->generatePdfReport($data); 
        }

        return view('reports/index', $data);
    }

    /**
     * Generate PDF Report for UMKM
     */
    private function generatePdfReport(array $data)
    {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', FCPATH);
        
        $dompdf = new Dompdf($options);

        // Load specific PDF view
        $html = view('reports/pdf', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Laporan_' . ucfirst($data['meta']['type']) . '_' . $data['meta']['start'] . '_sd_' . $data['meta']['end'];
        $dompdf->stream($filename . ".pdf", ["Attachment" => true]);
        exit();
    }

    /**
     * Laporan Per Pelanggan (UI & Controller)
     */
    public function clients()
    {
        $userId = (int) session()->get('id');
        
        // Parameter Default
        $period = $this->request->getGet('period') ?? 'bulan_ini'; 
        $status = $this->request->getGet('status') ?? 'Semua';
        $clientId = $this->request->getGet('client_id') ?? 'Semua';

        // Logic Filter Periode 
        $startDate = date('Y-m-d');
        $endDate   = date('Y-m-d');
        $periodLabel = 'Hari Ini';

        switch ($period) {
            case 'hari_ini':
                $startDate = date('Y-m-d');
                $endDate   = date('Y-m-d');
                $periodLabel = 'Hari Ini';
                break;
            case 'minggu_ini':
                $startDate = date('Y-m-d', strtotime('monday this week'));
                $endDate   = date('Y-m-d', strtotime('sunday this week'));
                $periodLabel = 'Minggu Ini';
                break;
            case 'bulan_ini':
                $startDate = date('Y-m-01');
                $endDate   = date('Y-m-t');
                $periodLabel = 'Bulan Ini (' . date('M Y') . ')';
                break;
            case 'bulan_lalu':
                $startDate = date('Y-m-01', strtotime('first day of last month'));
                $endDate   = date('Y-m-t', strtotime('last day of last month'));
                $periodLabel = 'Bulan Lalu (' . date('M Y', strtotime('last month')) . ')';
                break;
            case 'tahun_ini':
                $startDate = date('Y-01-01');
                $endDate   = date('Y-12-31');
                $periodLabel = 'Tahun Ini (' . date('Y') . ')';
                break;
            case 'semua':
                $startDate = '2000-01-01'; // arbitrary past date
                $endDate   = '2099-12-31'; // arbitrary future date
                $periodLabel = 'Semua Waktu';
                break;
            default:
                $startDate = date('Y-m-01');
                $endDate   = date('Y-m-t');
                $period = 'bulan_ini';
                $periodLabel = 'Bulan Ini (' . date('M Y') . ')';
                break;
        }

        // Fetch clients for dropdown
        $clientModel = new \App\Models\ClientModel();
        $allClientsOptions = $clientModel->where('user_id', $userId)->findAll();

        if ($clientId === 'Semua') {
            $reportData = $this->clientReportService->getClientSummary($userId, $startDate, $endDate, $status);
            $viewMode = 'summary';
        } else {
            $reportData = $this->clientReportService->getClientDetail($userId, $clientId, $startDate, $endDate, $status);
            $viewMode = 'detail';
        }

        $data = [
            'meta'    => [
                'start' => $startDate, 
                'end' => $endDate, 
                'status' => $status,
                'client_id' => $clientId,
                'period' => $period,
                'periodLabel' => $periodLabel,
                'view_mode' => $viewMode
            ],
            'report'  => $reportData,
            'clients' => $allClientsOptions,
            'title'   => 'Laporan Per Pelanggan',
            'user'    => $this->userProfileModel->where('user_id', $userId)->first()
        ];

        // Jika export
        $format = $this->request->getGet('export');
        if ($format === 'xlsx') {
            return $this->exportClientsXlsx($data);
        } else if ($format === 'pdf') {
            return $this->exportClientsPdf($data);
        }

        return view('reports/clients', $data);
    }

    /**
     * Export Excel Laporan Per Pelanggan
     */
    private function exportClientsXlsx(array $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $businessName = $data['user']['business_name'] ?? 'Perusahaan Saya';
        $periodLabel = $data['meta']['periodLabel'];
        $cleanBusinessName = preg_replace('/[^a-zA-Z0-9]/', '', $businessName);
        $periodFile = date('Y_m', strtotime($data['meta']['start']));

        if ($data['meta']['view_mode'] === 'summary') {
            $sheet->setTitle('Laporan Pelanggan');
            $sheet->setCellValue('A1', 'LAPORAN RINGKASAN PELANGGAN');
            $sheet->setCellValue('A2', 'Nama Usaha: ' . $businessName);
            $sheet->setCellValue('A3', 'Periode: ' . $periodLabel);

            $headers = ['A5' => 'Nama Pelanggan', 'B5' => 'Jml Invoice', 'C5' => 'Total Omzet', 'D5' => 'Total Lunas', 'E5' => 'Total Belum Lunas', 'F5' => 'Invoice Terakhir', 'G5' => 'Status Pelanggan'];
            foreach ($headers as $cell => $val) {
                $sheet->setCellValue($cell, $val);
            }

            $row = 6;
            if (empty($data['report']['data'])) {
                $sheet->setCellValue("A{$row}", 'Belum ada transaksi pada periode ini');
                $row++;
            } else {
                foreach ($data['report']['data'] as $item) {
                    $sheet->setCellValue("A{$row}", $item['client_name']);
                    $sheet->setCellValue("B{$row}", $item['total_invoices']);
                    $sheet->setCellValue("C{$row}", $item['total_revenue']);
                    $sheet->setCellValue("D{$row}", $item['total_paid']);
                    $sheet->setCellValue("E{$row}", $item['total_unpaid']);
                    $sheet->setCellValue("F{$row}", $item['last_invoice_date'] ? date('Y-m-d', strtotime($item['last_invoice_date'])) : '-');
                    $sheet->setCellValue("G{$row}", $item['client_status']);
                    $row++;
                }
            }

            // Summary Bottom
            $sumRow = $row + 1;
            $sheet->setCellValue("A{$sumRow}", 'Ringkasan:');
            $sheet->setCellValue("A".($sumRow+1), 'Total Pelanggan:');
            $sheet->setCellValue("B".($sumRow+1), $data['report']['meta']['total_clients']);
            
            $sheet->setCellValue("A".($sumRow+2), 'Pelanggan Aktif:');
            $sheet->setCellValue("B".($sumRow+2), $data['report']['meta']['active_clients']);
            
            $sheet->setCellValue("A".($sumRow+3), 'Total Omzet Periode:');
            $sheet->setCellValue("B".($sumRow+3), $data['report']['meta']['total_revenue']);
            
            $sheet->setCellValue("A".($sumRow+4), 'Rata-rata Invoice/Plg:');
            $sheet->setCellValue("B".($sumRow+4), $data['report']['meta']['avg_invoice_per_client']);

            // Styling Header
            $sheet->getStyle('A5:G5')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']]
            ]);

            // Number formats
            $sheet->getStyle("C6:E".($row-1))->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("B".($sumRow+3))->getNumberFormat()->setFormatCode('#,##0');

            // Status Colors
            for ($r = 6; $r < $row; $r++) {
                $status = $sheet->getCell("G{$r}")->getValue();
                $color = 'FFFFFFFF';
                if ($status === 'Aktif') $color = 'FFDCFCE7'; // Green
                if ($status === 'Bermasalah') $color = 'FFFEE2E2'; // Red
                if ($status === 'Tidak Aktif') $color = 'FFF3F4F6'; // Gray
                if ($status === 'Jarang Transaksi') $color = 'FFFEF3C7'; // Yellow
                $sheet->getStyle("G{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);
            }

            $fileName = "Laporan-Pelanggan-{$cleanBusinessName}-{$periodFile}.xlsx";

        } else {
            // Drill down
            $clientName = $data['report']['client_name'];
            $cleanClientName = preg_replace('/[^a-zA-Z0-9]/', '', $clientName);
            $sheet->setTitle('Detail Invoice');
            
            $sheet->setCellValue('A1', 'DETAIL INVOICE PELANGGAN');
            $sheet->setCellValue('A2', 'Nama Pelanggan: ' . $clientName);
            $sheet->setCellValue('A3', 'Periode: ' . $periodLabel);

            $headers = ['A5' => 'Tanggal Invoice', 'B5' => 'Nomor Invoice', 'C5' => 'Status', 'D5' => 'Jatuh Tempo', 'E5' => 'Total Invoice', 'F5' => 'Tanggal Lunas', 'G5' => 'Keterangan'];
            foreach ($headers as $cell => $val) {
                $sheet->setCellValue($cell, $val);
            }

            $row = 6;
            if (empty($data['report']['data'])) {
                $sheet->setCellValue("A{$row}", 'Belum ada transaksi pada periode ini');
                $row++;
            } else {
                foreach ($data['report']['data'] as $item) {
                    $statusMap = ['draft' => 'Draf', 'sent' => 'Terkirim', 'paid' => 'Lunas', 'canceled' => 'Dibatalkan'];
                    $statusStr = $statusMap[$item['status']] ?? $item['status'];

                    $sheet->setCellValue("A{$row}", $item['date_issued']);
                    $sheet->setCellValue("B{$row}", $item['invoice_number']);
                    $sheet->setCellValue("C{$row}", $statusStr);
                    $sheet->setCellValue("D{$row}", $item['due_date']);
                    $sheet->setCellValue("E{$row}", $item['total_amount']);
                    $sheet->setCellValue("F{$row}", $item['paid_date'] ?? '-');
                    $sheet->setCellValue("G{$row}", $item['keterangan']);
                    $row++;
                }
            }

            // Summary
            $sumRow = $row + 1;
            $sheet->setCellValue("D{$sumRow}", 'Total Keseluruhan:');
            $sheet->setCellValue("E{$sumRow}", $data['report']['meta']['total_revenue']);
            $sheet->getStyle("E{$sumRow}")->getFont()->setBold(true);

            // Styling
            $sheet->getStyle('A5:G5')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'name' => 'Calibri'],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']]
            ]);
            $sheet->getStyle("E6:E".($row-1))->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("E{$sumRow}")->getNumberFormat()->setFormatCode('#,##0');

            $fileName = "Detail-Pelanggan-{$cleanClientName}-{$periodFile}.xlsx";
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export PDF Laporan Per Pelanggan
     */
    private function exportClientsPdf(array $data)
    {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', FCPATH);
        
        $dompdf = new Dompdf($options);

        // We will create a new view specifically for PDF client report
        $html = view('reports/clients_pdf', $data);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $businessName = $data['user']['business_name'] ?? 'Perusahaan';
        $cleanBusinessName = preg_replace('/[^a-zA-Z0-9]/', '', $businessName);
        $periodFile = date('Y_m', strtotime($data['meta']['start']));
        
        if ($data['meta']['view_mode'] === 'summary') {
            $filename = "Laporan-Pelanggan-{$cleanBusinessName}-{$periodFile}.pdf";
        } else {
            $clientName = preg_replace('/[^a-zA-Z0-9]/', '', $data['report']['client_name']);
            $filename = "Detail-Pelanggan-{$clientName}-{$periodFile}.pdf";
        }

        $dompdf->stream($filename, ["Attachment" => true]);
        exit();
    }
}
