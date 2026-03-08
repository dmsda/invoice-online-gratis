<?php

namespace App\Controllers;

use App\Models\PlanModel;
use App\Models\SubscriptionModel;
use App\Models\PaymentModel;

class Subscription extends BaseController
{
    protected $planModel;
    protected $subscriptionModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->planModel = new PlanModel();
        $this->subscriptionModel = new SubscriptionModel();
        $this->paymentModel = new PaymentModel();
    }

    public function pricing()
    {
        $data = [
            'title' => 'Upgrade Paket Anda',
            'plans' => $this->planModel->where('is_active', 1)->findAll()
        ];
        
        // Cek login. Kalau belum, bisa lihat tapi tak bisa subscribe
        return view('pages/pricing', $data);
    }

    public function subscribe()
    {
        // Harus login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu untuk berlangganan.');
        }

        $userId = session()->get('id');
        $planId = $this->request->getPost('plan_id');
        $method = $this->request->getPost('payment_method'); // transfer atau qris
        $couponId = $this->request->getPost('coupon_id'); // nullable

        $plan = $this->planModel->find($planId);
        if (!$plan) {
            return redirect()->back()->with('error', 'Paket tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart(); // START TRANSACTION PROTECTION

        // 1. CEGAH SPAM / DOUBLE PENDING SUBSCRIPTION
        $existingPending = $this->subscriptionModel->where('user_id', $userId)
                                                   ->where('status', 'pending')
                                                   ->first();
        if ($existingPending) {
            $db->transRollback();
            return redirect()->to('/subscription/upload/' . $existingPending['id'])
                             ->with('error', 'Anda masih memiliki pesanan langganan yang belum diselesaikan.');
        }

        // 2. HITUNG HARGA DAN KUNCI KUPON (PENCEGAHAN RACE CONDITION)
        $cycle = $this->request->getPost('billing_cycle') ?? 'monthly';
        $baseAmount = ($cycle === 'yearly') ? $plan['price_yearly'] : $plan['price_monthly'];
        $discount = 0;
        
        if ($couponId) {
            // FOR UPDATE akan mengunci baris kupon ini sampai transaksi selesai
            $couponQuery = $db->query("SELECT * FROM coupons WHERE id = ? LIMIT 1 FOR UPDATE", [$couponId]);
            $coupon = $couponQuery->getRowArray();
            
            if ($coupon && $coupon['is_active']) {
                $now = date('Y-m-d H:i:s');
                if ($now < $coupon['valid_from'] || $now > $coupon['valid_until']) {
                    $db->transRollback();
                    return redirect()->back()->with('error', 'Kupon sudah kedaluwarsa atau belum masa aktif.');
                }
                if ($coupon['max_usage'] !== null && $coupon['used_count'] >= $coupon['max_usage']) {
                    $db->transRollback();
                    return redirect()->back()->with('error', 'Kupon ini sudah kehabisan kuota penggunaan.');
                }
                
                // Pastikan user tidak memakai 2x
                $usageModel = new \App\Models\CouponUsageModel();
                $used = $usageModel->where('coupon_id', $coupon['id'])
                                   ->where('user_id', $userId)
                                   ->first();
                if ($used) {
                    $db->transRollback();
                    return redirect()->back()->with('error', 'Anda sudah pernah menggunakan kode promosi ini.');
                }

                if ($coupon['type'] == 'percentage') {
                    $discount = ($baseAmount * $coupon['value']) / 100;
                } else {
                    $discount = $coupon['value'];
                }
            } else {
                $couponId = null; // Kupon tidak valid
            }
        }
        
        $finalAmount = max($baseAmount - $discount, 0);

        // JIKA HARGA FINAL RP 0 (FULL GRATIS)
        if ($finalAmount == 0) {
            
            // CEK SISA HARI AKTIF UNTUK DITAMBAHKAN (ROLLOVER)
            $oldActive = $this->subscriptionModel->where('user_id', $userId)
                                                 ->where('status', 'active')
                                                 ->first();
            $rolloverDays = 0;
            if ($oldActive && $oldActive['expires_at']) {
                $nowObj = new \DateTime();
                $expObj = new \DateTime($oldActive['expires_at']);
                if ($expObj > $nowObj) {
                    $rolloverDays = $nowObj->diff($expObj)->days;
                }
            }

            // Hanya di kasus gratis instan ini kita amankan dan matikan paket sebelumnya
            $this->subscriptionModel->where('user_id', $userId)
                                    ->where('status', 'active')
                                    ->set(['status' => 'cancelled'])
                                    ->update();

            $duration = ($cycle === 'yearly') ? 365 : 30;
            $totalDuration = $duration + $rolloverDays;
            
            $subscriptionId = $this->subscriptionModel->insert([
                'user_id' => $userId,
                'plan_id' => $planId,
                'payment_method' => $method,
                'billing_cycle' => $cycle,
                'status' => 'active',
                'started_at' => date('Y-m-d H:i:s'),
                'expires_at' => date('Y-m-d H:i:s', strtotime("+$totalDuration days"))
            ]);
            
            $this->paymentModel->insert([
                'subscription_id' => $subscriptionId,
                'amount' => 0,
                'method' => $method,
                'status' => 'verified',
                'verified_at' => date('Y-m-d H:i:s'),
                'verified_by' => 0 // System
            ]);
            
            if ($couponId) {
                $db->table('coupon_usages')->insert([
                    'coupon_id' => $couponId,
                    'user_id' => $userId,
                    'subscription_id' => $subscriptionId
                ]);
                $db->table('coupons')->where('id', $couponId)->set('used_count', 'used_count+1', false)->update();
            }
            
            $db->transComplete(); // COMMIT
            
            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Sistem sedang sibuk. Silakan coba beberapa saat lagi.');
            }
            
            cache()->delete('current_plan_user_' . $userId . '_v' . (cache('cache_version_' . $userId) ?: 1));
            return redirect()->to('/dashboard')->with('success', 'Selamat! Paket Anda berhasil diaktifkan secara instan berkat Kupon Spesial.');
        }

        // FLOW BERBAYAR NORMAL
        // Catatan Audit: HARUS MEMATIKAN PAKET LAMA JIKA RACE CONDITION TERJADI.
        // Di sini kita cancel paket aktif lain milik user ini jika request manual pembayaran dibuat (Anti Double Plan)
        $this->subscriptionModel->where('user_id', $userId)
                                ->where('status', 'active')
                                ->set(['status' => 'cancelled'])
                                ->update();

        $subscriptionId = $this->subscriptionModel->insert([
            'user_id' => $userId,
            'plan_id' => $planId,
            'payment_method' => $method,
            'billing_cycle' => $cycle,
            'status' => 'pending'
        ]);

        $uniqueCode = ($method === 'transfer') ? rand(100, 999) : 0;
        $totalAmount = $finalAmount + $uniqueCode;

        $this->paymentModel->insert([
            'subscription_id' => $subscriptionId,
            'amount' => $totalAmount,
            'method' => $method,
            'status' => 'pending'
        ]);
        
        if ($couponId) {
            $db->table('coupon_usages')->insert([
                'coupon_id' => $couponId,
                'user_id' => $userId,
                'subscription_id' => $subscriptionId
            ]);
            $db->table('coupons')->where('id', $couponId)->set('used_count', 'used_count+1', false)->update();
        }

        $db->transComplete(); // COMMIT
        
        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat pemesanan paket, mohon coba lagi.');
        }

        return redirect()->to('/subscription/upload/' . $subscriptionId);
    }

    public function applyCoupon()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['error' => 'Akses ditolak. Silakan login.', 'csrf_token' => csrf_hash()]);
        }

        $code = strtoupper(trim($this->request->getPost('code')));
        $planPrice = (float) $this->request->getPost('plan_price');

        $couponModel = new \App\Models\CouponModel();
        $usageModel = new \App\Models\CouponUsageModel();

        $coupon = $couponModel->where('code', $code)
                              ->where('is_active', 1)
                              ->first();

        if (!$coupon) {
            return $this->response->setJSON(['error' => 'Kupon tidak valid atau tidak ditemukan.', 'csrf_token' => csrf_hash()]);
        }

        // Cek tanggal berlaku
        $now = date('Y-m-d H:i:s');
        if ($now < $coupon['valid_from'] || $now > $coupon['valid_until']) {
            return $this->response->setJSON(['error' => 'Kupon sudah kedaluwarsa atau belum aktif.', 'csrf_token' => csrf_hash()]);
        }

        // Cek batas penggunaan global (Max Usage)
        if ($coupon['max_usage'] !== null && $coupon['used_count'] >= $coupon['max_usage']) {
            return $this->response->setJSON(['error' => 'Batas kuota kupon ini sudah habis digunakan.', 'csrf_token' => csrf_hash()]);
        }

        // Cek apakah user ini sudah pernah memakainya? (1 User 1 Kupon seumur hidup)
        $used = $usageModel->where('coupon_id', $coupon['id'])
                           ->where('user_id', session()->get('id'))
                           ->first();

        if ($used) {
            return $this->response->setJSON(['error' => 'Anda sudah pernah menggunakan kode kupon ini.', 'csrf_token' => csrf_hash()]);
        }

        // Kalkulasi Harga Promo
        if ($coupon['type'] == 'percentage') {
            $discount = ($planPrice * $coupon['value']) / 100;
        } else {
            $discount = $coupon['value'];
        }

        $final = max($planPrice - $discount, 0);

        return $this->response->setJSON([
            'success'     => true,
            'discount'    => $discount,
            'final_price' => $final,
            'coupon_id'   => $coupon['id'],
            'csrf_token'  => csrf_hash()
        ]);
    }

    public function uploadView($subscriptionId)
    {
        // Harus login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('id');
        
        // Verifikasi kepemilikan dan status
        $subscription = $this->subscriptionModel->find($subscriptionId);
        if (!$subscription || $subscription['user_id'] != $userId) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $payment = $this->paymentModel->where('subscription_id', $subscriptionId)
                                      ->where('status', 'pending')
                                      ->orderBy('id', 'DESC')
                                      ->first();
                                      
        if (!$payment) {
            return redirect()->to('/dashboard')->with('error', 'Tidak ada pembayaran yang tertunda atau sudah diverifikasi.');
        }
        
        $plan = $this->planModel->find($subscription['plan_id']);

        $data = [
            'title' => 'Selesaikan Pembayaran',
            'subscription' => $subscription,
            'payment' => $payment,
            'plan' => $plan
        ];

        return view('subscription/upload', $data);
    }

    public function uploadProof()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $subscriptionId = $this->request->getPost('subscription_id');
        $paymentId = $this->request->getPost('payment_id');
        $bankName = $this->request->getPost('bank_name');
        $accountName = $this->request->getPost('account_name');

        $validationRule = [
            'proof' => [
                'label' => 'Bukti Pembayaran',
                'rules' => 'uploaded[proof]|is_image[proof]|mime_in[proof,image/jpg,image/jpeg,image/png,application/pdf]|max_size[proof,2048]',
            ],
        ];

        if (! $this->validate($validationRule)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getError('proof'));
        }

        $file = $this->request->getFile('proof');
        if (! $file->isValid()) {
            return redirect()->back()->with('error', 'File upload error.');
        }

        $newName = $file->getRandomName();
        // Simpan ke direktori aman (writable/uploads/payments)
        $file->move(WRITEPATH . 'uploads/payments/', $newName);

        // Update payment table
        $updateData = [
            'proof_file' => $newName,
            'status' => 'pending' // Still waiting for admin verification
        ];

        if ($bankName) $updateData['bank_name'] = $bankName;
        if ($accountName) $updateData['account_name'] = $accountName;

        $this->paymentModel->update($paymentId, $updateData);

        return redirect()->to('/dashboard')->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin (1-12 Jam Kerja).');
    }
}
