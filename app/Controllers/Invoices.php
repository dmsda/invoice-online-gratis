<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\InvoiceItemModel;
use App\Models\ClientModel;
use App\Models\UserProfileModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Invoices extends BaseController
{
    protected $invoiceModel;
    protected $itemModel;
    protected $clientModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->itemModel = new InvoiceItemModel();
        $this->clientModel = new ClientModel();
        helper(['form', 'number', 'invoice_template']);
    }

    public function index()
    {
        $userId = session()->get('id');
        $status = $this->request->getGet('status');

        $query = $this->invoiceModel->select('invoices.id, invoices.uuid, invoices.client_id, invoices.invoice_number, invoices.title, invoices.date_issued, invoices.due_date, invoices.status, invoices.total_amount, clients.client_name')
                                    ->join('clients', 'clients.id = invoices.client_id', 'left')
                                    ->where('invoices.user_id', $userId);
        
        if ($status && in_array($status, ['draft', 'sent', 'paid', 'canceled'])) {
            $query->where('status', $status);
        }

        $query->orderBy('invoices.created_at', 'DESC');

        $data = [
            'title' => 'Daftar Invoice',
            'invoices' => $query->paginate(20),
            'pager' => $this->invoiceModel->pager,
            'clients' => $this->clientModel->where('user_id', $userId)->findAll(), // For filtering if needed later
        ];

        return view('invoices/index', $data);
    }

    public function export()
    {
        if (!has_feature('export_excel')) {
            return redirect()->back()->with('error', 'Fitur Export tersedia di Paket Pro.');
        }

        $userId = session()->get('id');
        $status = $this->request->getGet('status');
        $format = $this->request->getGet('format') ?? 'xlsx';
        $month = $this->request->getGet('month') ?? date('m');
        $year = $this->request->getGet('year') ?? date('Y');

        $profileModel = new UserProfileModel();
        $profile = $profileModel->where('user_id', $userId)->first();
        $businessName = $profile['business_name'] ?? 'Usaha_Saya';
        $cleanBusinessName = preg_replace('/[^a-zA-Z0-9]/', '', $businessName);

        $startDate = "$year-$month-01";
        $endDate = date("Y-m-t", strtotime($startDate));
        
        // Setup Date Titles (Indonesian format manually derived)
        $monthsId = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agt',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
        ];
        $periodStr = "01 " . $monthsId[$month] . " $year – " . date("d", strtotime($endDate)) . " " . $monthsId[$month] . " $year";

        $query = $this->invoiceModel->select('invoices.*, clients.client_name')
                                    ->join('clients', 'clients.id = invoices.client_id', 'left')
                                    ->where('invoices.user_id', $userId)
                                    ->where('invoices.date_issued >=', $startDate)
                                    ->where('invoices.date_issued <=', $endDate);
        
        if ($status && in_array($status, ['draft', 'sent', 'paid', 'canceled'])) {
            $query->where('status', $status);
        }

        $invoices = $query->findAll();

        // N+1 Refactor: Eager load all items for these invoices in 1 query
        $invoiceIds = array_column($invoices, 'id');
        $allItemsIndexed = [];
        if (!empty($invoiceIds)) {
            $rawItems = $this->itemModel->whereIn('invoice_id', $invoiceIds)->findAll();
            foreach ($rawItems as $item) {
                $allItemsIndexed[$item['invoice_id']][] = $item;
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Invoice');

        // Header Metadata
        $sheet->setCellValue('A1', 'LAPORAN INVOICE');
        $sheet->setCellValue('A2', 'Periode: ' . $periodStr);
        $sheet->setCellValue('A3', 'Nama Usaha: ' . $businessName);

        // Header Tabel
        $headers = [
            'A5' => 'Tanggal Invoice',
            'B5' => 'Nomor Invoice',
            'C5' => 'Pelanggan',
            'D5' => 'Deskripsi Singkat',
            'E5' => 'Status',
            'F5' => 'Subtotal',
            'G5' => 'Diskon',
            'H5' => 'Pajak',
            'I5' => 'Total',
            'J5' => 'Metode Pembayaran',
            'K5' => 'Tanggal Lunas'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $row = 6;
        $totalSubtotal = 0;
        $totalDiscount = 0;
        $totalTax = 0;
        $totalAmount = 0;

        if (empty($invoices)) {
            $sheet->setCellValue("A{$row}", 'Tidak ada invoice pada periode ini');
            $row++;
        } else {
            foreach ($invoices as $inv) {
                // Formatting Status
                $statusMap = [
                    'draft' => 'Draf',
                    'sent' => 'Terkirim',
                    'paid' => 'Lunas',
                    'canceled' => 'Dibatalkan'
                ];
                $statusStr = $statusMap[$inv['status']] ?? $inv['status'];

                // Formatting Items for description
                $items = $allItemsIndexed[$inv['id']] ?? [];
                $descList = [];
                foreach ($items as $item) {
                     $descList[] = $item['item_name'];
                }
                $shortDesc = implode(', ', $descList);
                if (strlen($shortDesc) > 80) $shortDesc = substr($shortDesc, 0, 77) . '...';

                // Method & Paid Date
                $paymentMethod = $inv['status'] === 'paid' ? ($profile['bank_name'] ?? 'Lainnya') : '-';
                $paidDate = $inv['status'] === 'paid' ? date('Y-m-d', strtotime($inv['updated_at'])) : '-';

                $sheet->setCellValue("A{$row}", $inv['date_issued']);
                $sheet->setCellValueExplicit("B{$row}", $inv['invoice_number'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue("C{$row}", $inv['client_name']);
                $sheet->setCellValue("D{$row}", $shortDesc);
                $sheet->setCellValue("E{$row}", $statusStr);
                $sheet->setCellValue("F{$row}", $inv['subtotal']);
                $sheet->setCellValue("G{$row}", $inv['discount']);
                $sheet->setCellValue("H{$row}", $inv['tax']);
                $sheet->setCellValue("I{$row}", $inv['total_amount']);
                $sheet->setCellValue("J{$row}", $paymentMethod);
                $sheet->setCellValue("K{$row}", $paidDate);
                
                // Keep track of totals for CSV
                $totalSubtotal += $inv['subtotal'];
                $totalDiscount += $inv['discount'];
                $totalTax += $inv['tax'];
                $totalAmount += $inv['total_amount'];

                $row++;
            }
        }

        // Summary Section
        $summaryStartRow = $row + 1;
        
        $sheet->setCellValue("E{$summaryStartRow}", "Subtotal Keseluruhan:");
        $sheet->setCellValue("E" . ($summaryStartRow + 1), "Total Diskon:");
        $sheet->setCellValue("E" . ($summaryStartRow + 2), "Total Pajak:");
        $sheet->setCellValue("E" . ($summaryStartRow + 3), "TOTAL OMZET:");

        if ($format === 'xlsx' && !empty($invoices)) {
            $lastDataRow = $row - 1;
            // Use SUM formulas for XLSX
            $sheet->setCellValue("F{$summaryStartRow}", "=SUM(F6:F{$lastDataRow})");
            $sheet->setCellValue("F".($summaryStartRow+1), "=SUM(G6:G{$lastDataRow})");
            $sheet->setCellValue("F".($summaryStartRow+2), "=SUM(H6:H{$lastDataRow})");
            $sheet->setCellValue("F".($summaryStartRow+3), "=SUM(I6:I{$lastDataRow})");
        } else {
            // Use static values for CSV or Empty
            $sheet->setCellValue("F{$summaryStartRow}", $totalSubtotal);
            $sheet->setCellValue("F".($summaryStartRow+1), $totalDiscount);
            $sheet->setCellValue("F".($summaryStartRow+2), $totalTax);
            $sheet->setCellValue("F".($summaryStartRow+3), $totalAmount);
        }

        if ($format === 'xlsx') {
            // Apply XLSX Styles
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A2:A3')->getFont()->setSize(11);

            // Header Tabel Styling
            $headerStyleArray = [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                    'name' => 'Calibri'
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2563EB'] // Blue Brand
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFE2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A5:K5')->applyFromArray($headerStyleArray);

            // Data Styling
            if (!empty($invoices)) {
                $dataStyleArray = [
                    'font' => [
                        'size' => 11,
                        'name' => 'Calibri'
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFE2E8F0'],
                        ],
                    ],
                ];
                $lastDataRow = $row - 1;
                $sheet->getStyle("A6:K{$lastDataRow}")->applyFromArray($dataStyleArray);

                // Status Coloring
                for ($r = 6; $r <= $lastDataRow; $r++) {
                    $stat = $sheet->getCell("E{$r}")->getValue();
                    $fillColor = 'FFFFFFFF';
                    if ($stat === 'Draf') $fillColor = 'FFFFFBEB'; // Kuning pucat
                    if ($stat === 'Terkirim') $fillColor = 'FFEFF6FF'; // Biru muda
                    if ($stat === 'Lunas') $fillColor = 'FFECFDF5'; // Hijau muda
                    if ($stat === 'Dibatalkan') $fillColor = 'FFFEF2F2'; // Merah muda
                    
                    $sheet->getStyle("E{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($fillColor);
                }
            }
            
            // Format number columns as accounting/number
            $sheet->getStyle("F6:I".($summaryStartRow+3))->getNumberFormat()->setFormatCode('#,##0');

            // Auto-size columns
            foreach (range('A', 'K') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
        }

        $fileName = "Laporan-Invoice-{$cleanBusinessName}-{$year}-{$month}";

        if ($format === 'csv') {
            $fileName .= '.csv';
            
            if (ob_get_level() > 0 && ob_get_length() > 0) { ob_clean(); }
            ob_start();
            echo "\xEF\xBB\xBF"; // UTF-8 BOM
            $writer = new Csv($spreadsheet);
            $writer->save('php://output');
            $csvData = ob_get_clean();
            
            return $this->response->download($fileName, $csvData)->setContentType('text/csv');
        } else {
            $fileName .= '.xlsx';
            
            $writer = new Xlsx($spreadsheet);
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            $writer->save($tempFile);
            $excelData = file_get_contents($tempFile);
            unlink($tempFile);
            
            if (ob_get_level() > 0 && ob_get_length() > 0) { ob_clean(); }
            
            return $this->response->download($fileName, $excelData)->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }
    }

    public function create()
    {
        if (subscription_expired()) {
            return redirect()->to('/pricing')->with('error', 'Upgrade untuk membuat invoice baru.');
        }

        $userId = session()->get('id');
        $clients = $this->clientModel->where('user_id', $userId)->findAll();

        return view('invoices/create', [
            'title' => 'Buat Invoice Baru',
            'clients' => $clients
        ]);
    }

    public function store()
    {
        if (subscription_expired()) {
            return redirect()->to('/pricing')->with('error', 'Upgrade untuk membuat invoice baru.');
        }

        $userId = session()->get('id');
        
        // 1. Validation
        $rules = [
            'client_id' => 'required|numeric',
            'date_issued' => 'required|valid_date',
            'due_date' => 'permit_empty|valid_date',
            'items.item_name.*' => 'required',
            'items.quantity.*' => 'required|numeric',
            'items.price.*' => 'required|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Prepare Data
        $clientId = $this->request->getVar('client_id');
        
        // Verify Client Ownership
        $clientCheck = $this->clientModel->where('user_id', $userId)->where('id', $clientId)->first();
        if (!$clientCheck) {
            return redirect()->back()->withInput()->with('error', 'Klien tidak valid.');
        }

        // Calculate Totals & Prepare Base Values
        $itemsInput = $this->request->getVar('items');
        if (!$itemsInput || !isset($itemsInput['item_name']) || !is_array($itemsInput['item_name']) || count($itemsInput['item_name']) === 0) {
             return redirect()->back()->withInput()->with('error', 'Invoice harus memiliki minimal 1 item.');
        }

        $itemsName = $itemsInput['item_name'];
        $itemsDesc = $itemsInput['description'];
        $itemsQty = $itemsInput['quantity'];
        $itemsPrice = $itemsInput['price'];
        
        $totalItemsCount = count($itemsName);
        $discount = (float) $this->request->getVar('discount');
        $tax = (float) $this->request->getVar('tax');

        // Prepare the raw flat list of all items
        $allPreparedItems = [];
        $grandSubtotal = 0;

        for ($i = 0; $i < $totalItemsCount; $i++) {
            $qty = (float) $itemsQty[$i];
            $price = (float) $itemsPrice[$i];
            $amount = $qty * $price;
            $grandSubtotal += $amount;
            
            $allPreparedItems[] = [
                'item_name' => $itemsName[$i],
                'description' => $itemsDesc[$i] ?? '',
                'quantity' => $qty,
                'price' => $price,
                'amount' => $amount
            ];
        }

        // Logic Chunking (Auto Split > 50 items)
        $limitPerInvoice = 50;
        $chunks = array_chunk($allPreparedItems, $limitPerInvoice);
        $totalParts = count($chunks);

        $baseInvoiceNumber = $this->invoiceModel->generateInvoiceNumber($userId);
        
        // 3. Database Transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            foreach ($chunks as $index => $chunkItems) {
                $partNumber = $index + 1;
                
                // Kalkulasi Subtotal part ini
                $partSubtotal = 0;
                foreach ($chunkItems as $item) {
                    $partSubtotal += $item['amount'];
                }

                // Kalkulasi Pajak & Diskon Proporsional part ini terhadap Grand Total
                $partDiscount = 0;
                $partTax = 0;
                
                if ($grandSubtotal > 0) {
                    $proportion = $partSubtotal / $grandSubtotal;
                    $partDiscount = round($discount * $proportion, 2);
                    $partTax = round($tax * $proportion, 2);
                }

                $partTotalAmount = $partSubtotal - $partDiscount + $partTax;

                // Penentuan Nama Invoice dan Split Group
                $currentInvoiceNumber = $baseInvoiceNumber;
                $splitGroupId = null;
                $splitPart = null;
                $splitTotal = null;

                if ($totalParts > 1) {
                    $currentInvoiceNumber = $baseInvoiceNumber . '-' . $partNumber . '/' . $totalParts;
                    $splitGroupId = $baseInvoiceNumber;
                    $splitPart = $partNumber;
                    $splitTotal = $totalParts;
                }

                // A. Insert Header Part
                $invoiceData = [
                    'user_id' => $userId,
                    'client_id' => $clientId,
                    'invoice_number' => $currentInvoiceNumber,
                    'title' => $this->request->getVar('title'),
                    'date_issued' => $this->request->getVar('date_issued'),
                    'due_date' => $this->request->getVar('due_date'),
                    'status' => 'draft',
                    'type' => $this->request->getVar('type'),
                    'subtotal' => $partSubtotal,
                    'discount' => $partDiscount,
                    'tax' => $partTax,
                    'total_amount' => $partTotalAmount,
                    'notes' => $this->request->getVar('notes'),
                    // Splitting columns
                    'split_group_id' => $splitGroupId,
                    'split_part' => $splitPart,
                    'split_total' => $splitTotal
                ];

                $this->invoiceModel->insert($invoiceData);
                $invoiceId = $this->invoiceModel->getInsertID();

                // B. Insert Items Part
                foreach ($chunkItems as &$item) {
                    $item['invoice_id'] = $invoiceId;
                }
                $this->itemModel->insertBatch($chunkItems);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                 throw new \Exception('Transaction failed');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan invoice: Terjadi kesalahan saat memecah tagihan.');
        }

        $successMessage = 'Invoice berhasil dibuat.';
        if ($totalParts > 1) {
            $successMessage = "<strong>✅ Invoice otomatis dibagi!</strong><br>Invoice Anda memiliki $totalItemsCount item, sistem otomatis membaginya menjadi $totalParts invoice agar rapi dan stabil dicetak.";
        }

        // Flush Cache Dashboard & Client Report
        $cacheVersion = cache('cache_version_' . $userId) ?: 1;
        cache()->save('cache_version_' . $userId, $cacheVersion + 1, 86400 * 30);

        return redirect()->to('/invoices')->with('success', $successMessage);

    }

    public function edit($uuid)
    {
        $userId = session()->get('id');
        
        // 1. Get Invoice
        $invoice = $this->invoiceModel->where('user_id', $userId)
                                      ->where('uuid', $uuid)
                                      ->first();

        if (!$invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 2. Get Items
        $items = $this->itemModel->where('invoice_id', $invoice['id'])->findAll();

        // 3. Get Clients
        $clients = $this->clientModel->where('user_id', $userId)->findAll();

        return view('invoices/edit', [
            'title' => 'Edit Invoice',
            'invoice' => $invoice,
            'items' => $items,
            'clients' => $clients
        ]);
    }

    public function update($uuid)
    {
        $userId = session()->get('id');

        // 1. Get Invoice & Check Ownership
        $invoice = $this->invoiceModel->where('user_id', $userId)
                                      ->where('uuid', $uuid)
                                      ->first();

        if (!$invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 2. Check Status (Only Draft can be edited)
        if ($invoice['status'] !== 'draft') {
            return redirect()->back()->withInput()->with('error', 'Invoice yang sudah dikirim/dibayar tidak dapat diedit. Silakan ubah status kembali ke Draft jika ingin mengedit.');
        }

        // 3. Validation (Same as Store)
        $rules = [
            'client_id' => 'required|numeric',
            'date_issued' => 'required|valid_date',
            'due_date' => 'permit_empty|valid_date',
            'items.item_name.*' => 'required',
            'items.quantity.*' => 'required|numeric',
            'items.price.*' => 'required|numeric',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 4. Prepare Data
        $itemsInput = $this->request->getVar('items');
        if (!$itemsInput || !isset($itemsInput['item_name']) || !is_array($itemsInput['item_name']) || count($itemsInput['item_name']) === 0) {
             return redirect()->back()->withInput()->with('error', 'Invoice harus memiliki minimal 1 item.');
        }

        if (count($itemsInput['item_name']) > 50) {
             return redirect()->back()->withInput()->with('error', 'Tidak dapat menyimpan lebih dari 50 item saat Edit. Jika item bertambah banyak, silakan buat Invoice Lanjutan baru untuk menjaga konsistensi part.');
        }

        $itemsName = $itemsInput['item_name'];
        $itemsDesc = $itemsInput['description'];
        $itemsQty = $itemsInput['quantity'];
        $itemsPrice = $itemsInput['price'];
        $clientId = $this->request->getVar('client_id');

        $subtotal = 0;
        $preparedItems = [];

        for ($i = 0; $i < count($itemsName); $i++) {
            $qty = (float) $itemsQty[$i];
            $price = (float) $itemsPrice[$i];
            $amount = $qty * $price;
            
            $subtotal += $amount;
            
            $preparedItems[] = [
                'item_name' => $itemsName[$i],
                'description' => $itemsDesc[$i] ?? '',
                'quantity' => $qty,
                'price' => $price,
                'amount' => $amount
            ];
        }

        $discount = (float) $this->request->getVar('discount');
        $tax = (float) $this->request->getVar('tax');
        $totalAmount = $subtotal - $discount + $tax;

        // 5. Database Transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // A. Update Header
            $invoiceData = [
                'client_id' => $clientId,
                'title' => $this->request->getVar('title'),
                'date_issued' => $this->request->getVar('date_issued'),
                'due_date' => $this->request->getVar('due_date'),
                'type' => $this->request->getVar('type'),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'notes' => $this->request->getVar('notes'),
            ];

            $this->invoiceModel->update($invoice['id'], $invoiceData);

            // B. Replace Items (Defensive Deletion)
            
            // 1. Attempt Delete via Model
            $this->itemModel->where('invoice_id', $invoice['id'])->delete();

            // 2. Verify Deletion
            $remaining = $this->itemModel->where('invoice_id', $invoice['id'])->countAllResults();

            // 3. Force SQL Delete if Model failed (Safety Net)
            if ($remaining > 0) {
                $db->simpleQuery("DELETE FROM invoice_items WHERE invoice_id = " . $db->escape($invoice['id']));
                $remaining = $this->itemModel->where('invoice_id', $invoice['id'])->countAllResults();
            }

            // 4. Critical Guard: If still items, FAIL.
            if ($remaining > 0) {
                // Rollback triggers in catch or transStatus
                throw new \Exception('Gagal menghapus item lama. Integrity check failed.');
            }
            
            // 5. Insert New Items
            foreach ($preparedItems as &$item) {
                $item['invoice_id'] = $invoice['id'];
            }
            
            if (!empty($preparedItems)) {
                $this->itemModel->insertBatch($preparedItems);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                 return redirect()->back()->withInput()->with('error', 'Gagal mengupdate invoice (Transaction Failed).');
            }

        } catch(\Exception $e) {
            $db->transRollback(); // Ensure rollback
            return redirect()->back()->withInput()->with('error', 'Gagal mengupdate: ' . $e->getMessage());
        }

        // Flush Cache Dashboard & Client Report
        $cacheVersion = cache('cache_version_' . $userId) ?: 1;
        cache()->save('cache_version_' . $userId, $cacheVersion + 1, 86400 * 30);

        return redirect()->to('/invoices/show/' . $uuid)->with('success', 'Invoice berhasil diperbarui.');
    }

    public function show($uuid)
    {
        $userId = session()->get('id');
        
        // Join with Client to get names
        $invoice = $this->invoiceModel->select('invoices.*, clients.client_name, clients.client_address, clients.client_phone, clients.client_email')
                                      ->join('clients', 'clients.id = invoices.client_id', 'left')
                                      ->where('invoices.user_id', $userId)
                                      ->where('invoices.uuid', $uuid)
                                      ->first();

        if (!$invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $items = $this->itemModel->where('invoice_id', $invoice['id'])->findAll();
        
        // Split Part Navigation Logic
        $prevSplit = null;
        $nextSplit = null;
        
        if (!empty($invoice['split_group_id'])) {
            $groupId = $invoice['split_group_id'];
            $currentPart = $invoice['split_part'];
            
            $prevSplit = $this->invoiceModel->where('user_id', $userId)
                                            ->where('split_group_id', $groupId)
                                            ->where('split_part', $currentPart - 1)
                                            ->first();
            
            $nextSplit = $this->invoiceModel->where('user_id', $userId)
                                            ->where('split_group_id', $groupId)
                                            ->where('split_part', $currentPart + 1)
                                            ->first();
        }

        // Load Helper
        helper('wa_helper');

        // Fetch User Profile for QR and payment info
        $profileModel = new \App\Models\UserProfileModel();
        $profile = $profileModel->find($userId);

        if (!has_feature('qr')) {
            $profile['qr_code_path'] = null;
        }

        // WA Zip Link Logic
        $waZipLink = null;
        if (!empty($invoice['split_group_id']) && $invoice['split_total'] > 1 && !empty($invoice['client_phone'])) {
            $clientName = trim($invoice['client_name']);
            $businessName = trim($profile['business_name'] ?? 'Nama Bisnis');
            $splitTotal = $invoice['split_total'];
            $zipUrl = base_url('v/zip/' . $invoice['uuid']);
            
            $waZipMessage = "Halo {$clientName} 👋\n\n" .
                            "Berikut kami kirimkan *invoice {$businessName}*\n" .
                            "untuk pekerjaan / layanan yang telah kami selesaikan.\n\n" .
                            "📦 *Invoice Lengkap (ZIP)*\n" .
                            "File ini berisi seluruh invoice ({$splitTotal} bagian)\n" .
                            "dalam satu folder ZIP agar lebih mudah diunduh.\n\n" .
                            "🔗 Download di sini:\n" .
                            "{$zipUrl}\n\n" .
                            "Jika ada yang ingin ditanyakan, silakan hubungi kami.\n" .
                            "Terima kasih atas kerja samanya 🙏\n\n" .
                            "— {$businessName}";
                            
            $waZipLink = generate_wa_link($invoice['client_phone'], $waZipMessage);
        }

        // WA Reminder Logic (H-3 and Overdue)
        $waReminderLink = null;
        $waReminderType = null; // 'h3' or 'overdue'

        // CEK FAIL-SAFE: REMINDER HANYA JALAN JIKA reminders_enabled = true (default 1 / true in DB)
        // Ensure to handle case where column might be null in old data
        $remindersEnabled = !isset($invoice['reminders_enabled']) || $invoice['reminders_enabled'] == 1;

        if ($remindersEnabled && $invoice['status'] === 'sent' && !empty($invoice['due_date']) && !empty($invoice['client_phone'])) {
            $clientName = trim($invoice['client_name']);
            $businessName = trim($profile['business_name'] ?? 'Nama Bisnis');
            $invoiceNumber = trim($invoice['invoice_number']);
            $dueDateFormatted = date('d M Y', strtotime($invoice['due_date']));
            $invoiceUrl = base_url('v/' . $invoice['uuid']);
            // Generate zip url for reminder if it's a split invoice
            if (!empty($invoice['split_group_id']) && $invoice['split_total'] > 1) {
                 $invoiceUrl = base_url('v/zip/' . $invoice['uuid']) . " (ZIP semua part)";
            }

            $currentDate = new \DateTime(date('Y-m-d'));
            $dueDateObj = new \DateTime($invoice['due_date']);
            $interval = $currentDate->diff($dueDateObj);
            
            $daysDiff = (int)$interval->format('%R%a'); // + if future, - if past

            if ($daysDiff > 0 && $daysDiff <= 3) {
                // H-3 Reminder
                $waReminderType = 'h3';
                $waReminderMessage = "Halo {$clientName} 👋\n\n" .
                                     "Kami ingin mengingatkan secara sopan bahwa\n" .
                                     "*invoice {$invoiceNumber}* dari *{$businessName}*\n" .
                                     "akan jatuh tempo pada *{$dueDateFormatted}*.\n\n" .
                                     "🔗 Detail invoice:\n" .
                                     "{$invoiceUrl}\n\n" .
                                     "Pesan ini hanya pengingat ya,\n" .
                                     "silakan abaikan jika pembayaran sudah dilakukan.\n" .
                                     "Terima kasih 🙏\n\n" .
                                     "— {$businessName}";
                                     
                $waReminderLink = generate_wa_link($invoice['client_phone'], $waReminderMessage);
            } elseif ($daysDiff < 0) {
                // Overdue Reminder
                $waReminderType = 'overdue';
                $waReminderMessage = "Halo {$clientName} 👋\n\n" .
                                     "Kami ingin mengonfirmasi terkait\n" .
                                     "*invoice {$invoiceNumber}* dari *{$businessName}*\n" .
                                     "yang jatuh tempo pada *{$dueDateFormatted}*.\n\n" .
                                     "🔗 Detail invoice:\n" .
                                     "{$invoiceUrl}\n\n" .
                                     "Jika pembayaran sudah dilakukan,\n" .
                                     "mohon abaikan pesan ini.\n" .
                                     "Apabila ada kendala, silakan hubungi kami.\n" .
                                     "Terima kasih 🙏\n\n" .
                                     "— {$businessName}";
                                     
                $waReminderLink = generate_wa_link($invoice['client_phone'], $waReminderMessage);
            }
        }
        
        // WA Thank You Logic (Paid Invoices)
        $waThankYouLink = null;
        if ($invoice['status'] === 'paid' && !empty($invoice['client_phone'])) {
            $clientName = trim($invoice['client_name']);
            $businessName = trim($profile['business_name'] ?? 'Nama Bisnis');
            $invoiceNumber = trim($invoice['invoice_number']);
            $invoiceUrl = base_url('v/' . $invoice['uuid']);
            // Generate zip url for if it's a split invoice
            if (!empty($invoice['split_group_id']) && $invoice['split_total'] > 1) {
                 $invoiceUrl = base_url('v/zip/' . $invoice['uuid']) . " (ZIP semua part)";
            }

            $waThankYouMessage = "Halo {$clientName} 👋\n\n" .
                                 "Terima kasih 🙏\n" .
                                 "Pembayaran untuk *invoice {$invoiceNumber}*\n" .
                                 "dari *{$businessName}* telah kami terima dengan baik.\n\n" .
                                 "🔗 Salinan invoice:\n" .
                                 "{$invoiceUrl}\n\n" .
                                 "Terima kasih atas kepercayaannya.\n" .
                                 "Semoga kita bisa terus bekerja sama 🤝\n\n" .
                                 "— {$businessName}";
                                 
            $waThankYouLink = generate_wa_link($invoice['client_phone'], $waThankYouMessage);
        }
        
        // WA Send Link (Standard)
        $waLink = null;
        if ($invoice['status'] !== 'draft' && !empty($invoice['client_phone'])) {
            $clientName = trim($invoice['client_name']);
            $businessName = trim($profile['business_name'] ?? 'Nama Bisnis');
            $invoiceNumber = trim($invoice['invoice_number']);
            $invoiceUrl = base_url('v/' . $invoice['uuid']);
            
            $waMessage = "Halo {$clientName} 👋\n\n" .
                         "Berikut kami kirimkan *invoice {$invoiceNumber}*\n" .
                         "dari *{$businessName}*.\n\n" .
                         "🔗 Lihat Detail & Download PDF:\n" .
                         "{$invoiceUrl}\n\n" .
                         "Mohon untuk dapat melakukan pembayaran sesuai\n" .
                         "nominal dan rekening yang tertera pada invoice.\n\n" .
                         "Apabila ada pertanyaan, silakan hubungi kami.\n" .
                         "Terima kasih 🙏\n\n" .
                         "— {$businessName}";
            $waLink = generate_wa_link($invoice['client_phone'], $waMessage);
        }

        return view('invoices/show', [
            'title' => 'Detail Invoice ' . $invoice['invoice_number'],
            'invoice' => $invoice,
            'items' => $items,
            'profile' => $profile,
            'prevSplit' => $prevSplit,
            'nextSplit' => $nextSplit,
            'waZipLink' => $waZipLink,
            'waReminderLink' => $waReminderLink,
            'waReminderType' => $waReminderType,
            'waThankYouLink' => $waThankYouLink,
            'waLink' => $waLink
        ]);
    }
    public function updateStatus($uuid)
    {
        $userId = session()->get('id');
        $invoice = $this->invoiceModel->where('user_id', $userId)->where('uuid', $uuid)->first();

        if (!$invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $status = $this->request->getVar('status');
        if (!in_array($status, ['draft', 'sent', 'paid', 'canceled'])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $updateData = ['status' => $status];
        
        $message = 'Status invoice diperbarui.';
        if ($status === 'paid') {
            // MATIKAN SEMUA REMINDER SECARA PERMANEN
            $updateData['reminders_enabled'] = false;
            $message = "<strong>Pembayaran Berhasil Dikonfirmasi</strong><br>Pembayaran dari pelanggan telah diterima. Invoice ini sekarang berstatus <strong>Lunas</strong>. Pengingat otomatis telah dimatikan.";
        }

        $this->invoiceModel->update($invoice['id'], $updateData);

        // Flush Cache Dashboard & Client Report
        $cacheVersion = cache('cache_version_' . $userId) ?: 1;
        cache()->save('cache_version_' . $userId, $cacheVersion + 1, 86400 * 30);

        return redirect()->back()->with('success', $message);
    }
    
    public function toggleReminder($uuid)
    {
        $userId = session()->get('id');
        $invoice = $this->invoiceModel->where('user_id', $userId)->where('uuid', $uuid)->first();

        if (!$invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $currentState = isset($invoice['reminders_enabled']) ? $invoice['reminders_enabled'] : 1;
        $newState = !$currentState;

        $this->invoiceModel->update($invoice['id'], ['reminders_enabled' => $newState]);

        $msg = $newState ? '🔔 Reminder otomatis kembali diaktifkan.' : '🔕 Reminder otomatis berhasil dimatikan.';
        // Flush Cache Dashboard
        $cacheVersion = cache('cache_version_' . $userId) ?: 1;
        cache()->save('cache_version_' . $userId, $cacheVersion + 1, 86400 * 30);

        return redirect()->back()->with('success', $msg);
    }

    public function downloadPdf($uuid)
    {
        $userId = session()->get('id');
        $invoice = $this->invoiceModel->select('invoices.*, clients.client_name, clients.client_address, clients.client_phone, clients.client_email')
                                      ->join('clients', 'clients.id = invoices.client_id', 'left')
                                      ->where('invoices.user_id', $userId)
                                      ->where('invoices.uuid', $uuid)
                                      ->first();

        if (!$invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $items = $this->itemModel->where('invoice_id', $invoice['id'])->findAll();
        
        // Retrieve User Profile for Logo and Business Info
        $profileModel = new \App\Models\UserProfileModel();
        $profile = $profileModel->find($userId);

        if (!has_feature('qr')) {
            $profile['qr_code_path'] = null;
        }

        // LOGIKA BARU: Absolute Path & Validasi untuk Dompdf
        $qrFile = $profile['qr_code_path'] ?? null;
        $qrUrlPublic = null;
        $qrPathAbsolute = null;

        if ($qrFile) {
            $fullPath = FCPATH . ltrim($qrFile, '/');
            if (file_exists($fullPath) && is_file($fullPath)) {
                $qrUrlPublic = base_url($qrFile);
                $qrPathAbsolute = $fullPath;
            }
        }

        // Calculate Overdue
        // Logic: Not Paid/Canceled AND Due Date < Today
        $isOverdue = ($invoice['status'] !== 'paid' && $invoice['status'] !== 'canceled' && $invoice['due_date'] && $invoice['due_date'] < date('Y-m-d'));

        $data = [
            'invoice' => $invoice,
            'items' => $items,
            'profile' => $profile,
            'qr_url' => $qrUrlPublic,
            'qr_path' => $qrPathAbsolute,
            'isPdf' => true,
            'view_mode' => 'pdf', // Identify as PDF for UX Fallback
            'isOverdue' => $isOverdue,
            'plan' => current_plan($userId)
        ];

        // Generate PDF using Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $options = new \Dompdf\Options();
        // Set options
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', FCPATH);
        $dompdf->setOptions($options);

        // Resolve template using helper
        $viewFile = invoice_template_view($invoice['type'] ?? null);

        // Load View PDF
        $html = view($viewFile, $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Stream PDF (Download)
        $cleanInvNumber = str_replace('/', '-', $invoice['invoice_number']);
        $filename = 'Invoice-' . $cleanInvNumber . '.pdf';
        
        $pdfContent = $dompdf->output();
        if (ob_get_level() > 0 && ob_get_length() > 0) { ob_clean(); }
        
        return $this->response->download($filename, $pdfContent)->setContentType('application/pdf');
    }
    
    public function downloadZip($uuid)
    {
        $userId = session()->get('id');
        $invoice = $this->invoiceModel->where('user_id', $userId)
                                      ->where('uuid', $uuid)
                                      ->first();

        if (!$invoice || empty($invoice['split_group_id'])) {
            return redirect()->back()->with('error', 'Invoice tidak valid atau bukan merupakan bagian dari Auto Split.');
        }

        $groupId = $invoice['split_group_id'];
        
        // Fetch all parts
        $parts = $this->invoiceModel->where('user_id', $userId)
                                    ->where('split_group_id', $groupId)
                                    ->orderBy('split_part', 'ASC')
                                    ->findAll();
                                    
        if (count($parts) > 10) {
            return redirect()->back()->with('error', 'Terlalu banyak part invoice (>10) untuk digenerate ZIP secara aman. Harap unduh satu per satu.');
        }

        if (count($parts) <= 1) {
            return redirect()->back()->with('info', 'ℹ️ Invoice ini hanya terdiri dari satu file. Gunakan tombol "Unduh PDF".');
        }

        // Setup User Profile globally for all parts
        $profileModel = new \App\Models\UserProfileModel();
        $profile = $profileModel->find($userId);
        
        if (!has_feature('qr')) {
            $profile['qr_code_path'] = null;
        }
        
        $qrFile = $profile['qr_code_path'] ?? null;
        $qrUrlPublic = null;
        $qrPathAbsolute = null;

        if ($qrFile) {
            $fullPath = FCPATH . ltrim($qrFile, '/');
            if (file_exists($fullPath) && is_file($fullPath)) {
                $qrUrlPublic = base_url($qrFile);
                $qrPathAbsolute = $fullPath;
            }
        }

        // Setup PHP ZipArchive
        $zip = new \ZipArchive();
        $baseNameClean = preg_replace('/[^A-Za-z0-9\-]/', '-', $groupId);
        $zipFilename = 'Invoice-' . $baseNameClean . '.zip';
        $zipFilePath = sys_get_temp_dir() . '/' . uniqid('invzip_') . '.tmp';

        if ($zip->open($zipFilePath, \ZipArchive::CREATE) !== TRUE) {
            return redirect()->back()->with('error', 'Gagal menyiapkan file ZIP sistem. Silakan coba lagi. Jika masalah berlanjut, hubungi admin.');
        }

        // N+1 Refactor: Eager load items and clients for all parts
        $partIds = array_column($parts, 'id');
        $allItemsIndexed = [];
        if (!empty($partIds)) {
            $rawItems = $this->itemModel->whereIn('invoice_id', $partIds)->findAll();
            foreach ($rawItems as $item) {
                $allItemsIndexed[$item['invoice_id']][] = $item;
            }
        }

        $clientIds = array_unique(array_filter(array_column($parts, 'client_id')));
        $allClientsIndexed = [];
        if (!empty($clientIds)) {
            $rawClients = $this->clientModel->whereIn('id', $clientIds)->findAll();
            foreach ($rawClients as $client) {
                $allClientsIndexed[$client['id']] = $client;
            }
        }

        // Generate each PDF and add to Zip
        foreach ($parts as $partInfo) {
            // Get Items and Client for this specific part from pre-fetched arrays
            $partItems = $allItemsIndexed[$partInfo['id']] ?? [];
            $clientInfo = $allClientsIndexed[$partInfo['client_id']] ?? null;
            
            // Merge Client Info since our view uses $invoice['client_name']
            if ($clientInfo) {
                $partInfo['client_name'] = $clientInfo['client_name'];
                $partInfo['client_address'] = $clientInfo['client_address'];
                $partInfo['client_phone'] = $clientInfo['client_phone'];
                $partInfo['client_email'] = $clientInfo['client_email'];
            }
            
            $isOverdue = ($partInfo['status'] !== 'paid' && $partInfo['status'] !== 'canceled' && $partInfo['due_date'] && $partInfo['due_date'] < date('Y-m-d'));

            $data = [
                'invoice' => $partInfo,
                'items' => $partItems,
                'profile' => $profile,
                'qr_url' => $qrUrlPublic,
                'qr_path' => $qrPathAbsolute,
                'isPdf' => true,
                'view_mode' => 'pdf',
                'isOverdue' => $isOverdue,
                'plan' => current_plan($userId)
            ];

            // Setup Dompdf for this iteration
            $dompdf = new \Dompdf\Dompdf();
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('chroot', FCPATH);
            $dompdf->setOptions($options);

            $viewFile = invoice_template_view($partInfo['type'] ?? null);
            $html = view($viewFile, $data);
            
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Construct neat filename inside zip: 01-INV-2026-02-001-1of3.pdf
            $orderedNumber = str_pad($partInfo['split_part'], 2, '0', STR_PAD_LEFT);
            $pdfName = $orderedNumber . '-' . $baseNameClean . '-' . $partInfo['split_part'] . 'of' . $partInfo['split_total'] . '.pdf';
            
            // Add raw PDF string output to zip
            $zip->addFromString($pdfName, $dompdf->output());
        }

        $zip->close();

        // Stream Zip Download and Destroy Temp
        $zipData = file_get_contents($zipFilePath);
        unlink($zipFilePath);
        
        if (ob_get_level() > 0 && ob_get_length() > 0) { ob_clean(); }
        
        return $this->response->download($zipFilename, $zipData)->setContentType('application/zip');
    }
    
    public function delete($uuid)
    {
        $userId = session()->get('id');
        $invoice = $this->invoiceModel->where('user_id', $userId)->where('uuid', $uuid)->first();

        if (!$invoice) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Hardening: Prevent deleting Paid invoices
        if ($invoice['status'] === 'paid') {
            return redirect()->back()->with('error', 'Invoice yang sudah LUNAS tidak boleh dihapus demi arsip.');
        }

        $isSplit = !empty($invoice['split_group_id']);

        // Items will be deleted via cascade if set in DB, or we delete manually
        $this->itemModel->where('invoice_id', $invoice['id'])->delete();
        $this->invoiceModel->delete($invoice['id']);

        $msg = 'Invoice berhasil dihapus.';
        if ($isSplit) {
            $msg = 'Part invoice berhasil dihapus. Harap pastikan total dan part invoice lainnya dalam grup ini masih relevan secara akuntansi.';
        }

        // Flush Cache Dashboard & Client Report
        $cacheVersion = cache('cache_version_' . $userId) ?: 1;
        cache()->save('cache_version_' . $userId, $cacheVersion + 1, 86400 * 30);

        return redirect()->to('/invoices')->with('success', $msg);
    }
}
