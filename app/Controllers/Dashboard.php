<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $userId = session()->get('id');
        $invoiceModel = new \App\Models\InvoiceModel();
        $reportService = new \App\Services\InvoiceReportService();
        $clientModel = new \App\Models\ClientModel();
        $profileModel = new \App\Models\UserProfileModel();

        // Get Business Name
        $profile = $profileModel->where('user_id', $userId)->first();
        $businessName = $profile['business_name'] ?? 'Juragan';

        // Stats via SSOT Report Service (Lifetime)
        $summary = $reportService->getSummary($userId);
        
        $totalInvoices = $summary['total_invoice'];
        $unpaidInvoices = $summary['count_unpaid'];
        $paidInvoices = $summary['count_paid']; 
                                     
        $totalClients = $clientModel->where('user_id', $userId)->countAllResults();

        // Recent Invoices
        $recentInvoices = $invoiceModel->select('invoices.uuid, invoices.invoice_number, invoices.title, invoices.status, invoices.total_amount, invoices.created_at, invoices.date_issued, invoices.due_date, clients.client_name')
                                       ->join('clients', 'clients.id = invoices.client_id', 'left')
                                       ->where('invoices.user_id', $userId)
                                       ->orderBy('invoices.created_at', 'DESC')
                                       ->findAll(5);

        return view('dashboard/index', [
            'title' => 'Dashboard',
            'totalInvoices' => $totalInvoices,
            'unpaidInvoices' => $unpaidInvoices,
            'paidInvoices' => $paidInvoices,
            'totalClients' => $totalClients,
            'recentInvoices' => $recentInvoices,
            'businessName' => $businessName
        ]);
    }
}
