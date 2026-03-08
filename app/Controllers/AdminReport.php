<?php
namespace App\Controllers;

class AdminReport extends BaseController {

    public function index() {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $db = \Config\Database::connect();
        
        // 1. Total Pendapatan Keseluruhan (Verified Payments)
        $totalRevenue = $db->table('payments')
                           ->selectSum('amount')
                           ->where('status', 'verified')
                           ->get()->getRow()->amount ?? 0;
                           
        // 2. MRR (Monthly Recurring Revenue) Estimasi
        // Hitung dari user dengan active subscription ke plan berbayar bulanan (asumsi active members * monthly price)
        $mrr = $db->table('subscriptions s')
                  ->selectSum('p.price_monthly', 'mrr')
                  ->join('plans p', 'p.id = s.plan_id')
                  ->where('s.status', 'active')
                  ->where('p.slug !=', 'free')
                  ->where('p.slug !=', 'trial')
                  ->get()->getRow()->mrr ?? 0;

        // 3. User Stats
        $totalUsers = $db->table('users')->where('role', 'user')->countAllResults();
        $activeSubscribers = $db->table('subscriptions')
                                ->where('status', 'active')
                                ->where('plan_id !=', 1) // Asumsi ID 1 adalah Free/Trial
                                ->countAllResults();

        // 4. Pendapatan 6 Bulan Terakhir untuk Chart
        $sixMonthsAgo = date('Y-m-01', strtotime('-5 months'));
        $monthlyRevenueQuery = $db->table('payments')
                                  ->select('DATE_FORMAT(verified_at, "%Y-%m") as month, SUM(amount) as total')
                                  ->where('status', 'verified')
                                  ->where('verified_at >=', $sixMonthsAgo)
                                  ->groupBy('month')
                                  ->orderBy('month', 'ASC')
                                  ->get()->getResultArray();
                                  
        $chartData = [
            'labels' => [],
            'data' => []
        ];
        
        // Isi default 0 untuk 6 bulan terakhir jika kosong
        for ($i = 5; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("-$i months"));
            $chartData['labels'][] = date('M Y', strtotime("-$i months"));
            
            $found = false;
            foreach ($monthlyRevenueQuery as $row) {
                if ($row['month'] === $m) {
                    $chartData['data'][] = $row['total'];
                    $found = true;
                    break;
                }
            }
            if (!$found) $chartData['data'][] = 0;
        }

        // 5. Distribusi Paket
        $planDistribution = $db->table('subscriptions s')
                               ->select('p.name, COUNT(s.id) as total')
                               ->join('plans p', 'p.id = s.plan_id')
                               ->where('s.status', 'active')
                               ->groupBy('p.id')
                               ->get()->getResultArray();

        $data = [
            'title' => 'Laporan SaaS',
            'totalRevenue' => $totalRevenue,
            'mrr' => $mrr,
            'totalUsers' => $totalUsers,
            'activeSubscribers' => $activeSubscribers,
            'chartData' => json_encode($chartData),
            'planDistribution' => $planDistribution
        ];

        return view('admin/reports/index', $data);
    }
}
