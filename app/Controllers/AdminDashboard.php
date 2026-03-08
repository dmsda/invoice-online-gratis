<?php

namespace App\Controllers;

use App\Models\SubscriptionModel;
use App\Models\PaymentModel;

class AdminDashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $paymentModel = new PaymentModel();
        $subscriptionModel = new SubscriptionModel();

        // Total payments verified
        $totalRevenue = $paymentModel->selectSum('amount')
                                     ->where('status', 'verified')
                                     ->first()['amount'] ?? 0;

        // Total subscriptions
        $totalActive = $subscriptionModel->where('status', 'active')->countAllResults();
        $totalPending = $subscriptionModel->where('status', 'pending')->countAllResults();
        $totalUsers = (new \App\Models\UserModel())->countAllResults();

        // Recent Payments
        $recentPayments = $paymentModel->select('payments.*, user_profiles.business_name, plans.name as plan_name')
                                       ->join('subscriptions', 'subscriptions.id = payments.subscription_id')
                                       ->join('users', 'users.id = subscriptions.user_id')
                                       ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
                                       ->join('plans', 'plans.id = subscriptions.plan_id')
                                       ->where('payments.status', 'verified')
                                       ->orderBy('payments.created_at', 'DESC')
                                       ->findAll(5);

        $data = [
            'title' => 'Admin Dashboard',
            'totalRevenue' => $totalRevenue,
            'totalActive' => $totalActive,
            'totalPending' => $totalPending,
            'totalUsers' => $totalUsers,
            'recentPayments' => $recentPayments
        ];

        return view('admin/dashboard', $data);
    }
}
