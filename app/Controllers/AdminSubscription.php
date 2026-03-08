<?php

namespace App\Controllers;

use App\Models\SubscriptionModel;
use App\Models\PaymentModel;

class AdminSubscription extends BaseController
{
    protected $subscriptionModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->subscriptionModel = new SubscriptionModel();
        $this->paymentModel = new PaymentModel();
    }

    public function index()
    {
        // Pastikan hanya admin yang bisa akses
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak. Anda bukan admin.');
        }

        $status = $this->request->getGet('status') ?? 'pending';
        
        $this->subscriptionModel->select('subscriptions.*, up.business_name, u.email, p.name as plan_name, py.amount, py.proof_file, py.method as payment_method, py.bank_name, py.account_name');
        $this->subscriptionModel->join('users u', 'u.id = subscriptions.user_id');
        $this->subscriptionModel->join('user_profiles up', 'up.user_id = u.id', 'left');
        $this->subscriptionModel->join('plans p', 'p.id = subscriptions.plan_id');
        $this->subscriptionModel->join('payments py', 'py.subscription_id = subscriptions.id', 'left');
        
        if ($status !== 'all') {
            $this->subscriptionModel->where('subscriptions.status', $status);
        }

        $this->subscriptionModel->orderBy('subscriptions.created_at', 'DESC');
        $subscriptions = $this->subscriptionModel->paginate(20, 'default');

        $db = \Config\Database::connect();
        $stats = [
            'pending' => $db->table('subscriptions')->where('status', 'pending')->countAllResults(),
            'active' => $db->table('subscriptions')->where('status', 'active')->countAllResults()
        ];

        $data = [
            'title' => 'Manajemen Langganan UMKM',
            'subscriptions' => $subscriptions,
            'pager' => $this->subscriptionModel->pager,
            'status' => $status,
            'current_filter' => $status,
            'stats' => $stats
        ];

        return view('admin/subscriptions/index', $data);
    }

    public function approve()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $subscriptionId = $this->request->getPost('id');
        $subscription = $this->subscriptionModel->find($subscriptionId);
        
        if (!$subscription) {
            return redirect()->back()->with('error', 'Langganan tidak ditemukan.');
        }

        // CEK SISA HARI AKTIF UNTUK DITAMBAHKAN (ROLLOVER)
        $oldActive = $this->subscriptionModel->where('user_id', $subscription['user_id'])
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

        $durationDays = ($subscription['billing_cycle'] == 'yearly') ? 365 : 30;
        $totalDuration = $durationDays + $rolloverDays;

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. NONAKTIFKAN LANGGANAN LAMA YANG MASIH AKTIF (PENCEGAHAN DOUBLE PLAN)
        $this->subscriptionModel->where('user_id', $subscription['user_id'])
                                ->where('status', 'active')
                                ->set(['status' => 'cancelled'])
                                ->update();

        // 2. AKTIFKAN LANGGANAN BARU
        $this->subscriptionModel->update($subscriptionId, [
            'status' => 'active',
            'started_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime("+$totalDuration days")),
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => session()->get('id')
        ]);

        // 3. UPDATE DATA PEMBAYARAN KE VERIFIED
        $payment = $this->paymentModel->where('subscription_id', $subscriptionId)
                                      ->where('status', 'pending')
                                      ->first();
        if ($payment) {
            $this->paymentModel->update($payment['id'], [
                'status' => 'verified',
                'verified_by' => session()->get('id'),
                'verified_at' => date('Y-m-d H:i:s')
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memproses persetujuan persetujuan.');
        }

        // Hapus cache current plan milik user agar segera terupdate di UI
        $cacheVersion = cache('cache_version_' . $subscription['user_id']) ?: 1;
        cache()->delete('current_plan_user_' . $subscription['user_id'] . '_v' . $cacheVersion);
        cache()->save('cache_version_' . $subscription['user_id'], $cacheVersion + 1, 31536000);
        
        log_audit('approve_subscription', 'subscriptions', $subscriptionId, ['plan_id' => $subscription['plan_id']]);
        
        // Kirim email notifikasi
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($subscription['user_id']);
        $profileModel = new \App\Models\UserProfileModel();
        $profile = $profileModel->where('user_id', $subscription['user_id'])->first();
        $busName = $profile ? $profile['business_name'] : 'Pelanggan Setia';
        
        if ($user && $user['email']) {
            $email = \Config\Services::email();
            $email->setTo($user['email']);
            $email->setSubject('Langganan Aktif - Invoice Online Gratis');
            $email->setMessage('Halo ' . $busName . ',<br><br>Pembayaran langganan paket Anda telah kami verifikasi dan langganan Anda sudah aktif. Anda kini dapat menikmati seluruh fitur sesuai paket pilihan Anda.<br><br>Salam,<br>Tim Invoice Online');
            $email->send();
        }

        return redirect()->back()->with('success', 'Langganan berhasil diaktifkan. Klien sudah dapat mengakses fitur Pro.');
    }

    public function reject()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $subscriptionId = $this->request->getPost('id');
        $reason = $this->request->getPost('reason'); // Optional: Admin gives reason
        $sub = $this->subscriptionModel->find($subscriptionId);

        if (!$sub) {
            return redirect()->back()->with('error', 'Langganan tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update Subscription
        $this->subscriptionModel->update($subscriptionId, [
            'status' => 'rejected'
        ]);

        // 2. Update Payment
        $payment = $this->paymentModel->where('subscription_id', $subscriptionId)
                                      ->where('status', 'pending')
                                      ->first();
        if ($payment) {
            $this->paymentModel->update($payment['id'], [
                'status' => 'rejected',
                'verified_by' => session()->get('id'),
                'verified_at' => date('Y-m-d H:i:s')
            ]);
        }

        // 3. REFUND KUPON BIAR BISA DIPAKAI LAGI
        $usageModel = clone $db->table('coupon_usages');
        $usage = $usageModel->where('subscription_id', $subscriptionId)->get()->getRowArray();
        
        if ($usage) {
            // Hapus rekam jejak pemakaian
            $db->table('coupon_usages')->where('id', $usage['id'])->delete();
            // Kembalikan 1 kuota ke kupon origin
            $db->table('coupons')->where('id', $usage['coupon_id'])->set('used_count', 'used_count-1', false)->update();
        }

        $db->transComplete();

        log_audit('reject_subscription', 'subscriptions', $subscriptionId, ['reason' => $reason]);
        
        // Send email
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($sub['user_id']);
        $profileModel = new \App\Models\UserProfileModel();
        $profile = $profileModel->where('user_id', $sub['user_id'])->first();
        $busName = $profile ? $profile['business_name'] : 'Pelanggan Setia';
        
        if ($user && $user['email']) {
            $email = \Config\Services::email();
            $email->setTo($user['email']);
            $email->setSubject('Pembayaran Langganan Ditolak - Invoice Online Gratis');
            $email->setMessage('Halo ' . $busName . ',<br><br>Mohon maaf, permohonan langganan Anda belum dapat kami proses.<br><br>Alasan: <strong>' . esc($reason) . '</strong><br><br>Silakan lakukan konfirmasi ulang atau hubungi tim support kami.<br><br>Salam,<br>Tim Invoice Online');
            $email->send();
        }

        return redirect()->back()->with('error', 'Langganan telah ditolak.');
    }

    public function viewProof($filename)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // AMANKAN NAM FILE DARI PATH TRAVERSAL LFI EXPLOT
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $filename);

        $path = WRITEPATH . 'uploads/payments/' . $filename;
        if (!is_file($path)) {
            return throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        readfile($path);
        exit;
    }

    public function export()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $status = $this->request->getGet('status') ?? 'all';
        
        $db = \Config\Database::connect();
        $builder = $db->table('subscriptions s');
        $builder->select('s.id as sub_id, up.business_name, u.email, p.name as plan_name, s.status, s.created_at, s.started_at, s.expires_at, py.method as payment_method, py.amount');
        $builder->join('users u', 'u.id = s.user_id');
        $builder->join('user_profiles up', 'up.user_id = u.id', 'left');
        $builder->join('plans p', 'p.id = s.plan_id');
        $builder->join('payments py', 'py.subscription_id = s.id', 'left');
        
        if ($status !== 'all') {
            $builder->where('s.status', $status);
        }
        $builder->orderBy('s.created_at', 'DESC');
        $data = $builder->get()->getResultArray();

        $filename = 'export_subscriptions_' . date('Ymd_His') . '.csv';
        
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; charset=UTF-8");

        $file = fopen('php://output', 'w');
        
        fputcsv($file, [
            'ID Langganan', 'Nama Usaha', 'Email', 'Paket', 'Status', 
            'Tgl Dibuat', 'Tgl Mulai', 'Tgl Kedaluwarsa', 'Metode Bayar', 'Jumlah Bayar'
        ]);

        foreach ($data as $row) {
            fputcsv($file, [
                $row['sub_id'],
                $row['business_name'],
                $row['email'],
                $row['plan_name'],
                strtoupper($row['status']),
                $row['created_at'],
                $row['started_at'] ?? '-',
                $row['expires_at'] ?? '-',
                $row['payment_method'] ?? '-',
                $row['amount'] ?? '0'
            ]);
        }

        fclose($file);
        exit;
    }

    public function bulkAction()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $subIds = $this->request->getPost('sub_ids');
        $action = $this->request->getPost('bulk_action');

        if (empty($subIds) || !is_array($subIds)) {
            return redirect()->back()->with('error', 'Pilih minimal satu langganan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $successCount = 0;
        
        foreach ($subIds as $id) {
            $sub = $this->subscriptionModel->find($id);
            if (!$sub || $sub['status'] !== 'pending') continue;

            if ($action === 'approve') {
                $oldActive = $this->subscriptionModel->where('user_id', $sub['user_id'])
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

                $durationDays = ($sub['billing_cycle'] == 'yearly') ? 365 : 30;
                $totalDuration = $durationDays + $rolloverDays;
                
                // PENCEGAHAN DOUBLE PLAN LEWAT BULK ACTION
                $this->subscriptionModel->where('user_id', $sub['user_id'])
                                        ->where('status', 'active')
                                        ->set(['status' => 'cancelled'])
                                        ->update();

                $this->subscriptionModel->update($id, [
                    'status' => 'active',
                    'started_at' => date('Y-m-d H:i:s'),
                    'expires_at' => date('Y-m-d H:i:s', strtotime("+$totalDuration days")),
                    'approved_at' => date('Y-m-d H:i:s'),
                    'approved_by' => session()->get('id')
                ]);

                $payment = $this->paymentModel->where('subscription_id', $id)
                                              ->where('status', 'pending')
                                              ->first();
                if ($payment) {
                    $this->paymentModel->update($payment['id'], [
                        'status' => 'verified',
                        'verified_by' => session()->get('id'),
                        'verified_at' => date('Y-m-d H:i:s')
                    ]);
                }

                $cacheVersion = cache('cache_version_' . $sub['user_id']) ?: 1;
                cache()->delete('current_plan_user_' . $sub['user_id'] . '_v' . $cacheVersion);
                cache()->save('cache_version_' . $sub['user_id'], $cacheVersion + 1, 31536000);
                
                log_audit('approve_subscription_bulk', 'subscriptions', $id, ['plan_id' => $sub['plan_id']]);
                
                $userModel = new \App\Models\UserModel();
                $user = $userModel->find($sub['user_id']);
                $profileModel = new \App\Models\UserProfileModel();
                $profile = $profileModel->where('user_id', $sub['user_id'])->first();
                $busName = $profile ? $profile['business_name'] : 'Pelanggan Setia';
                
                if ($user && $user['email']) {
                    $email = \Config\Services::email();
                    $email->setTo($user['email']);
                    $email->setSubject('Langganan Aktif - Invoice Online Gratis');
                    $email->setMessage('Halo ' . $busName . ',<br><br>Pembayaran langganan paket Anda telah kami verifikasi dan langganan Anda sudah aktif.<br><br>Salam,<br>Tim Invoice Online');
                    $email->send();
                }
                $successCount++;
                
            } elseif ($action === 'reject') {
                $this->subscriptionModel->update($id, ['status' => 'rejected']);
                
                $payment = $this->paymentModel->where('subscription_id', $id)
                                              ->where('status', 'pending')
                                              ->first();
                if ($payment) {
                    $this->paymentModel->update($payment['id'], [
                        'status' => 'rejected',
                        'verified_by' => session()->get('id'),
                        'verified_at' => date('Y-m-d H:i:s')
                    ]);
                }

                // REFUND KUPON BIAR BISA DIPAKAI LAGI
                $usageModel = clone $db->table('coupon_usages');
                $usage = $usageModel->where('subscription_id', $id)->get()->getRowArray();
                
                if ($usage) {
                    $db->table('coupon_usages')->where('id', $usage['id'])->delete();
                    $db->table('coupons')->where('id', $usage['coupon_id'])->set('used_count', 'used_count-1', false)->update();
                }
                
                log_audit('reject_subscription_bulk', 'subscriptions', $id, ['reason' => 'Ditolak secara massal.']);
                
                $userModel = new \App\Models\UserModel();
                $user = $userModel->find($sub['user_id']);
                $profileModel = new \App\Models\UserProfileModel();
                $profile = $profileModel->where('user_id', $sub['user_id'])->first();
                $busName = $profile ? $profile['business_name'] : 'Pelanggan Setia';
                
                if ($user && $user['email']) {
                    $email = \Config\Services::email();
                    $email->setTo($user['email']);
                    $email->setSubject('Pembayaran Langganan Ditolak - Invoice Online Gratis');
                    $email->setMessage('Halo ' . $busName . ',<br><br>Mohon maaf, permohonan langganan Anda belum dapat kami proses.<br><br>Alasan: Ditolak oleh sistem/admin.<br><br>Tim Invoice Online');
                    $email->send();
                }
                $successCount++;
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
             return redirect()->back()->with('error', 'Terjadi kesalahan. Aksi massal dibatalkan.');
        }

        if ($action === 'approve') {
            return redirect()->back()->with('success', "Berhasil mengaktifkan $successCount langganan.");
        } else {
            return redirect()->back()->with('error', "$successCount langganan telah ditolak.");
        }
    }
}
