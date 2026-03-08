<?php

namespace App\Controllers;

use App\Models\InvoiceModel;
use App\Models\InvoiceItemModel;
use App\Models\UserProfileModel;
use App\Models\ClientModel; // Added ClientModel

class PublicInvoice extends BaseController
{
    protected $invoiceModel;
    protected $clientModel; // Changed from $itemModel
    protected $invoiceItemModel; // Changed from $itemModel
    protected $userProfileModel; // Changed from $profileModel

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->clientModel = new ClientModel(); // Added
        $this->invoiceItemModel = new InvoiceItemModel(); // Changed from $itemModel
        $this->userProfileModel = new UserProfileModel(); // Changed from $profileModel
        helper(['number', 'invoice_template']); // Added helper
    }

    public function index($uuid)
    {
        // 1. Fetch Invoice & Client
        // Note: We don't filter by user_id here because it's public access
        $invoice = $this->invoiceModel->select('invoices.*, clients.client_name, clients.client_address, clients.client_phone, clients.client_email')
                                      ->join('clients', 'clients.id = invoices.client_id', 'left')
                                      ->where('invoices.uuid', $uuid)
                                      ->first();

        if (!$invoice) {
             throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // 2. Security Check: Draft invoices are not public
        // 2. Security Check: Draft invoices are not public
        // if ($invoice['status'] === 'draft') {
        //      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        // }

        // 3. Fetch Items
        $items = $this->invoiceItemModel->where('invoice_id', $invoice['id'])->findAll();

        // Split Part Navigation Logic
        $prevSplit = null;
        $nextSplit = null;
        
        if (!empty($invoice['split_group_id'])) {
            $groupId = $invoice['split_group_id'];
            $currentPart = $invoice['split_part'];
            
            // For public links, we do not filter by user_id so anyone with the link can navigate
            $prevSplit = $this->invoiceModel->where('split_group_id', $groupId)
                                            ->where('split_part', $currentPart - 1)
                                            ->first();
            
            $nextSplit = $this->invoiceModel->where('split_group_id', $groupId)
                                            ->where('split_part', $currentPart + 1)
                                            ->first();
        }

        // 4. Fetch User Profile (Seller Info)
        $profile = $this->userProfileModel->where('user_id', $invoice['user_id'])->first();
        
        // 5. Fetch Plan Info helper
        $planDetails = current_plan($invoice['user_id']); // using helper

        $data = [
            'invoice' => $invoice,
            'items' => $items,
            'profile' => $profile,
            'plan' => $planDetails,
            'title' => 'Invoice ' . $invoice['invoice_number'],
            'isOverdue' => ($invoice['status'] !== 'paid' && $invoice['status'] !== 'canceled' && $invoice['due_date'] < date('Y-m-d')),
            'prevSplit' => $prevSplit,
            'nextSplit' => $nextSplit
        ];

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
        $data['qr_url'] = $qrUrlPublic;
        $data['qr_path'] = $qrPathAbsolute;
        $data['isPdf'] = false;

        // Retrieve 'download' query param to trigger PDF generation
        if ($this->request->getGet('action') === 'pdf') {
             return $this->generatePdf($data);
        }

        // Resolve template using helper
        $viewFile = invoice_template_view($invoice['type'] ?? null);

        // Ini mem-bypass view bawaan CI4 karena PublicInvoice me-reuse DOMPDF view
        // untuk ditampilkan di browser bila bukan opsi PDF download.
        return view($viewFile, $data);
    }

    public function downloadZip($uuid)
    {
        // 1. Fetch Init Invoice
        $invoice = $this->invoiceModel->where('uuid', $uuid)->first();

        if (!$invoice || empty($invoice['split_group_id'])) {
            return redirect()->back()->with('error', 'Invoice tidak valid atau bukan merupakan bagian dari Auto Split.');
        }

        $groupId = $invoice['split_group_id'];
        $userId = $invoice['user_id'];
        
        // Fetch all parts
        $parts = $this->invoiceModel->where('split_group_id', $groupId)
                                    ->orderBy('split_part', 'ASC')
                                    ->findAll();
                                    
        if (count($parts) > 10) {
            return redirect()->back()->with('error', 'Terlalu banyak part invoice (>10) untuk digenerate ZIP secara aman. Harap unduh satu per satu.');
        }

        if (count($parts) <= 1) {
            return redirect()->back()->with('info', 'ℹ️ Invoice ini hanya terdiri dari satu file. Gunakan tombol "Unduh PDF".');
        }

        // Setup User Profile globally for all parts
        $profile = $this->userProfileModel->find($userId);
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
        
        $planDetails = current_plan($userId);

        // Setup PHP ZipArchive
        $zip = new \ZipArchive();
        $baseNameClean = preg_replace('/[^A-Za-z0-9\-]/', '-', $groupId);
        $zipFilename = 'Invoice-' . $baseNameClean . '.zip';
        $zipFilePath = sys_get_temp_dir() . '/' . uniqid('pubzip_') . '.tmp';

        if ($zip->open($zipFilePath, \ZipArchive::CREATE) !== TRUE) {
            return redirect()->back()->with('error', 'Gagal menyiapkan file ZIP sistem. Silakan coba lagi. Jika masalah berlanjut, hubungi admin.');
        }

        // N+1 Refactor: Eager load items and clients for all parts
        $partIds = array_column($parts, 'id');
        $allItemsIndexed = [];
        if (!empty($partIds)) {
            $rawItems = $this->invoiceItemModel->whereIn('invoice_id', $partIds)->findAll();
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
            $partItems = $allItemsIndexed[$partInfo['id']] ?? [];
            $clientInfo = $allClientsIndexed[$partInfo['client_id']] ?? null;
            
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
                'plan' => $planDetails,
                'qr_url' => $qrUrlPublic,
                'qr_path' => $qrPathAbsolute,
                'isPdf' => true,
                'view_mode' => 'pdf',
                'isOverdue' => $isOverdue
            ];

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

            $orderedNumber = str_pad($partInfo['split_part'], 2, '0', STR_PAD_LEFT);
            $pdfName = $orderedNumber . '-' . $baseNameClean . '-' . $partInfo['split_part'] . 'of' . $partInfo['split_total'] . '.pdf';
            
            $zip->addFromString($pdfName, $dompdf->output());
        }

        $zip->close();

        $zipData = file_get_contents($zipFilePath);
        unlink($zipFilePath);
        
        if (ob_get_level() > 0 && ob_get_length() > 0) { ob_clean(); }
        
        return $this->response->download($zipFilename, $zipData)->setContentType('application/zip');
    }

    private function generatePdf($data)
    {
        $data['view_mode'] = 'pdf'; // Override for PDF context
        $data['isPdf'] = true;
        
        $dompdf = new \Dompdf\Dompdf();
        
        // Options
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true); // Allow remote images if needed (careful)
        $options->set('isHtml5ParserEnabled', true);
        $options->set('chroot', FCPATH);
        $dompdf->setOptions($options);

        // Render HTML using helper
        $viewFile = invoice_template_view($data['invoice']['type'] ?? null);
        $html = view($viewFile, $data);
        $dompdf->loadHtml($html);

        // Setup Paper
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();

        // Stream to browser
        // Stream PDF
        $cleanInvNumber = str_replace('/', '-', $data['invoice']['invoice_number']);
        $filename = 'Invoice-' . $cleanInvNumber . '.pdf';
        
        $pdfContent = $dompdf->output();
        if (ob_get_level() > 0 && ob_get_length() > 0) { ob_clean(); }
        
        // Inline means open in browser rather than forced attachment
        return $this->response->setBody($pdfContent)
                              ->setHeader('Content-Type', 'application/pdf')
                              ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
    }
}
