<?php

namespace App\Services;

use App\Models\InvoiceModel;
use App\Models\ClientModel;

class InvoiceReportService
{
    protected $invoiceModel;
    protected $clientModel;

    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->clientModel = new ClientModel();
    }

    /**
     * SSOT: Get aggregate summary metrics for the reporting dashboard.
     * Guaranteed to exclude 'draft' and 'canceled'.
     *
     * @param int $userId
     * @param string|null $startDate (Optional)
     * @param string|null $endDate (Optional)
     * @return array
     */
    public function getSummary(int $userId, ?string $startDate = null, ?string $endDate = null)
    {
        $cacheVersion = cache('cache_version_' . $userId) ?: 1;
        $cacheKey = 'dashboard_summary_' . $userId . '_v' . $cacheVersion . '_' . ($startDate ?? 'all') . '_' . ($endDate ?? 'all');
        $cachedData = cache($cacheKey);

        if ($cachedData !== null) {
            log_message('info', 'Cache hit dashboard summary user: ' . $userId);
            return $cachedData;
        }

        $today = date('Y-m-d');
        
        $builder = $this->invoiceModel->builder();
        $builder->select("
            COUNT(id) as total_invoice,
            SUM(total_amount) as total_value,
            
            SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as count_paid,
            SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as total_paid,
            
            SUM(CASE WHEN status IN ('sent', 'overdue') THEN 1 ELSE 0 END) as count_unpaid,
            SUM(CASE WHEN status IN ('sent', 'overdue') THEN total_amount ELSE 0 END) as total_unpaid,
            
            SUM(CASE WHEN status = 'overdue' OR (status = 'sent' AND due_date < '{$today}') THEN 1 ELSE 0 END) as count_overdue,
            SUM(CASE WHEN status = 'overdue' OR (status = 'sent' AND due_date < '{$today}') THEN total_amount ELSE 0 END) as total_overdue
        ")
        ->where('user_id', $userId)
        ->whereNotIn('status', ['draft', 'canceled']);

        if ($startDate && $endDate) {
            $builder->where('date_issued >=', $startDate)
                    ->where('date_issued <=', $endDate);
        }

        $result = $builder->get()->getRowArray();
        
        // Ensure nulls from SUM become 0
        $data = [
            'total_invoice' => (int) ($result['total_invoice'] ?? 0),
            'total_value'   => (float) ($result['total_value'] ?? 0),
            
            'count_paid'    => (int) ($result['count_paid'] ?? 0),
            'total_paid'    => (float) ($result['total_paid'] ?? 0),
            
            'count_unpaid'  => (int) ($result['count_unpaid'] ?? 0),
            'total_unpaid'  => (float) ($result['total_unpaid'] ?? 0),
            
            'count_overdue' => (int) ($result['count_overdue'] ?? 0),
            'total_overdue' => (float) ($result['total_overdue'] ?? 0),
        ];

        cache()->save($cacheKey, $data, 120); // 120 detik
        return $data;
    }

    /**
     * SSOT: Get details of paid invoices (Sales Report).
     */
    public function getSalesData(int $userId, string $startDate, string $endDate)
    {
        return $this->invoiceModel->select('invoices.*, clients.client_name as client_name')
            ->join('clients', 'clients.id = invoices.client_id', 'left')
            ->where('invoices.user_id', $userId)
            ->where('invoices.status', 'paid') // Implicitly excludes draft/canceled
            ->where("invoices.date_issued >= '{$startDate}'")
            ->where("invoices.date_issued <= '{$endDate}'")
            ->orderBy('invoices.date_issued', 'DESC')
            ->findAll();
    }

    /**
     * SSOT: Get details of unpaid invoices (Receivables Report).
     * Strictly targets 'sent' and 'overdue' to prevent draft inclusion.
     */
    public function getReceivablesData(int $userId, string $startDate, string $endDate)
    {
        $builder = $this->invoiceModel->select('invoices.*, clients.client_name as client_name')
            ->join('clients', 'clients.id = invoices.client_id', 'left')
            ->where('invoices.user_id', $userId)
            ->whereIn('invoices.status', ['sent', 'overdue'])
            ->where("invoices.date_issued >= '{$startDate}'")
            ->where("invoices.date_issued <= '{$endDate}'")
            ->orderBy('invoices.due_date', 'ASC'); // sort by oldest due date to chase
            
        return $builder->findAll();
    }

    /**
     * SSOT: Get details of all transactions (All Transactions Report).
     * Excludes draft and canceled.
     */
    public function getAllTransactionsData(int $userId, string $startDate, string $endDate)
    {
        return $this->invoiceModel->select('invoices.*, clients.client_name as client_name')
            ->join('clients', 'clients.id = invoices.client_id', 'left')
            ->where('invoices.user_id', $userId)
            ->whereNotIn('invoices.status', ['draft', 'canceled'])
            ->where("invoices.date_issued >= '{$startDate}'")
            ->where("invoices.date_issued <= '{$endDate}'")
            ->orderBy('invoices.date_issued', 'DESC')
            ->findAll();
    }

    /**
     * SSOT: Get aggregated client performance (Client Report).
     * Applies the same rigorous exclusions.
     */
    public function getClientPerformance(int $userId, string $startDate, string $endDate)
    {
        return $this->invoiceModel->select("
                clients.client_name as client_name, 
                COUNT(invoices.id) as total_transactions,
                SUM(invoices.total_amount) as total_value,
                SUM(CASE WHEN invoices.status = 'paid' THEN invoices.total_amount ELSE 0 END) as total_paid,
                SUM(CASE WHEN invoices.status IN ('sent', 'overdue') THEN invoices.total_amount ELSE 0 END) as total_unpaid
            ")
            ->join('clients', 'clients.id = invoices.client_id', 'left') 
            ->where('invoices.user_id', $userId)
            ->whereNotIn('invoices.status', ['draft', 'canceled'])
            ->where("invoices.date_issued >= '{$startDate}'")
            ->where("invoices.date_issued <= '{$endDate}'")
            ->groupBy('clients.id, clients.client_name')
            ->orderBy('total_value', 'DESC')
            ->findAll();
    }
}
