<?php

namespace App\Services;

use App\Models\InvoiceModel;
use App\Models\ClientModel;

class ClientReportService
{
    protected $db;
    protected $invoiceModel;
    protected $clientModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->invoiceModel = new InvoiceModel();
        $this->clientModel = new ClientModel();
    }

    /**
     * Mode 1: Ringkasan Semua Pelanggan
     */
    public function getClientSummary($userId, $startDate, $endDate, $status = null)
    {
        $cacheVersion = cache('cache_version_' . $userId) ?: 1;
        $cacheKey = 'client_summary_' . $userId . '_v' . $cacheVersion . '_' . ($startDate ?? 'all') . '_' . ($endDate ?? 'all') . '_' . ($status ?? 'all');
        $cachedData = cache($cacheKey);

        if ($cachedData !== null) {
            log_message('info', 'Cache hit client summary user: ' . $userId);
            return $cachedData;
        }

        $builder = $this->db->table('clients c')
                            ->select("
                                c.id as client_id,
                                c.client_name,
                                COUNT(i.id) as total_invoices,
                                SUM(i.total_amount) as total_revenue,
                                SUM(CASE WHEN i.status = 'paid' THEN i.total_amount ELSE 0 END) as total_paid,
                                SUM(CASE WHEN i.status != 'paid' THEN i.total_amount ELSE 0 END) as total_unpaid,
                                MAX(i.date_issued) as last_invoice_date
                            ")
                            ->join('invoices i', 'i.client_id = c.id AND i.date_issued >= ' . $this->db->escape($startDate) . ' AND i.date_issued <= ' . $this->db->escape($endDate), 'left')
                            ->where('c.user_id', $userId)
                            ->groupBy('c.id');

        if ($status && $status !== 'Semua') {
            // Need a subquery approach or having clause if filtering by invoice status but we want all clients
            // Actually, if filtering by status, we only want clients who have invoices of that status IN THAT PERIOD.
            // But wait, the prompt says "Filter: Status Invoice". This usually applies to the invoices being counted.
            // Let's refine the join condition.
            $builder = $this->db->table('clients c')
                                ->select("
                                    c.id as client_id,
                                    c.client_name,
                                    COUNT(i.id) as total_invoices,
                                    SUM(i.total_amount) as total_revenue,
                                    SUM(CASE WHEN i.status = 'paid' THEN i.total_amount ELSE 0 END) as total_paid,
                                    SUM(CASE WHEN i.status != 'paid' THEN i.total_amount ELSE 0 END) as total_unpaid,
                                    MAX(i.date_issued) as last_invoice_date
                                ")
                                ->join('invoices i', "i.client_id = c.id AND i.date_issued >= " . $this->db->escape($startDate) . " AND i.date_issued <= " . $this->db->escape($endDate) . ($status && $status !== 'all' ? " AND i.status = " . $this->db->escape($status) : ""), 'left')
                                ->where('c.user_id', $userId)
                                ->groupBy('c.id');
        }

        $clients = $builder->get()->getResultArray();
        
        // We need to determine "Bermasalah" status based on ALL their invoices, not just the filtered ones?
        // Prompt implies status is based on their general activity. "Ada invoice overdue > 30 hari".
        // Let's do a subquery to find if they have ANY overdue invoice > 30 days.
        $overdueQuery = $this->db->table('invoices')
                                 ->select('client_id')
                                 ->where('user_id', $userId)
                                 ->where('status', 'sent')
                                 ->where('due_date <', date('Y-m-d', strtotime('-30 days')))
                                 ->groupBy('client_id');
        $problematicClients = array_column($overdueQuery->get()->getResultArray(), 'client_id');

        $today = new \DateTime();

        $processedClients = [];
        $meta = [
            'total_clients' => 0,
            'active_clients' => 0,
            'total_revenue' => 0,
            'avg_invoice_per_client' => 0,
            'total_aggregated_invoices' => 0
        ];

        foreach ($clients as $client) {
            // Only include clients that have invoices in this period if a filter is applied?
            // "Filter ... Pelanggan: Semua pelanggan". Usually we show all clients, but if they have 0 invoices in the period, they show 0.
            
            // Determine Status
            $statusLabel = 'Tidak Aktif';
            if (in_array($client['client_id'], $problematicClients)) {
                $statusLabel = 'Bermasalah';
            } else if ($client['last_invoice_date']) {
                $lastDate = new \DateTime($client['last_invoice_date']);
                $diff = $today->diff($lastDate)->days;
                if ($diff <= 30) {
                    $statusLabel = 'Aktif';
                    $meta['active_clients']++;
                } else if ($diff <= 90) {
                    $statusLabel = 'Jarang Transaksi';
                }
            }

            $client['client_status'] = $statusLabel;
            
            // Handle nulls
            $client['total_invoices'] = (int) $client['total_invoices'];
            $client['total_revenue'] = (float) $client['total_revenue'];
            $client['total_paid'] = (float) $client['total_paid'];
            $client['total_unpaid'] = (float) $client['total_unpaid'];

            $meta['total_clients']++;
            $meta['total_revenue'] += $client['total_revenue'];
            $meta['total_aggregated_invoices'] += $client['total_invoices'];

            $processedClients[] = $client;
        }

        if ($meta['total_clients'] > 0) {
            $meta['avg_invoice_per_client'] = round($meta['total_aggregated_invoices'] / $meta['total_clients'], 1);
        }

        $data = [
            'data' => $processedClients,
            'meta' => $meta
        ];
        
        cache()->save($cacheKey, $data, 300); // 300 detik cache
        return $data;
    }

    /**
     * Mode 2: Drill-down per Pelanggan
     */
    public function getClientDetail($userId, $clientId, $startDate, $endDate, $status = null)
    {
        $builder = $this->invoiceModel->where('user_id', $userId)
                                      ->where('client_id', $clientId)
                                      ->where('date_issued >=', $startDate)
                                      ->where('date_issued <=', $endDate);

        if ($status && $status !== 'Semua' && $status !== 'all') {
            $builder->where('status', $status);
        }

        $invoices = $builder->orderBy('date_issued', 'DESC')->findAll();
        
        $client = $this->clientModel->where('user_id', $userId)->find($clientId);

        $processedInvoices = [];
        $totalRevenue = 0;
        
        $today = new \DateTime();

        foreach ($invoices as $inv) {
            $keterangan = '-';
            if ($inv['status'] === 'paid') {
                $keterangan = 'Tepat Waktu';
                // Very basic check, could be improved if payment date is recorded accurately vs due date
                if ($inv['updated_at'] > $inv['due_date']) {
                    $keterangan = 'Terlambat Dibayar';
                }
            } else if ($inv['status'] === 'sent' && $inv['due_date'] < $today->format('Y-m-d')) {
                 $keterangan = 'Overdue';
            }

            $paidDate = $inv['status'] === 'paid' ? date('Y-m-d', strtotime($inv['updated_at'])) : null;

            $processedInvoices[] = [
                'date_issued' => $inv['date_issued'],
                'invoice_number' => $inv['invoice_number'],
                'status' => $inv['status'],
                'due_date' => $inv['due_date'],
                'total_amount' => $inv['total_amount'],
                'paid_date' => $paidDate,
                'keterangan' => $keterangan
            ];
            
            $totalRevenue += $inv['total_amount'];
        }

        return [
            'client_name' => $client ? $client['client_name'] : 'Unknown',
            'data' => $processedInvoices,
            'meta' => [
                'total_invoices' => count($processedInvoices),
                'total_revenue' => $totalRevenue
            ]
        ];
    }
}
