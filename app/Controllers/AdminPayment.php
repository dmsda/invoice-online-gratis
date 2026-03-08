<?php
namespace App\Controllers;

use App\Models\PaymentModel;

class AdminPayment extends BaseController {

    public function index() {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $paymentModel = new PaymentModel();
        
        $status = $this->request->getGet('status') ?? 'all';
        $search = $this->request->getGet('search');
        
        $builder = $paymentModel->select('payments.*, up.business_name as user_name, u.email, up.business_name as profile_bus_name, p.name as plan_name')
                                ->join('subscriptions s', 's.id = payments.subscription_id')
                                ->join('users u', 'u.id = s.user_id')
                                ->join('user_profiles up', 'up.user_id = u.id', 'left')
                                ->join('plans p', 'p.id = s.plan_id');
                                
        if ($status !== 'all') {
            $builder->where('payments.status', $status);
        }
        
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('up.business_name', $search)
                    ->orLike('u.email', $search)
                    ->orLike('payments.bank_name', $search)
                    ->groupEnd();
        }
        
        $payments = $builder->orderBy('payments.created_at', 'DESC')->paginate(20, 'default');

        $data = [
            'title' => 'Log Pembayaran',
            'payments' => $payments,
            'pager' => $paymentModel->pager,
            'status' => $status,
            'search' => $search
        ];

        return view('admin/payments/index', $data);
    }
}
