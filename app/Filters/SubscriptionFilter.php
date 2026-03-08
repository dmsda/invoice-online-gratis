<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SubscriptionFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments Default argument is 'pro'
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('id');
        
        // Admin Bypass - Let admin access user panel without subscription checks
        if (session()->get('role') === 'admin') {
            return;
        }
        
        helper('subscription');
        
        $sub = get_active_subscription($userId);

        // Auto expire logic on the fly
        if ($sub && $sub['expires_at'] && $sub['expires_at'] < date('Y-m-d H:i:s')) {
            $subModel = new \App\Models\SubscriptionModel();
            $subModel->update($sub['id'], ['status' => 'expired']);
            // Clear cache
            cache()->delete('current_plan_user_' . $userId . '_v' . (cache('cache_version_' . $userId) ?: 1));
            $sub = null;
        }

        $currentPlan = current_plan($userId);

        // Jika rute dikonfigurasi dengan arguments misal: ['filter' => 'subscription:pro']
        $requiredPlan = $arguments[0] ?? null; 

        // Untuk sekarang, logic sederhana: jika butuh pro tapi plan adalah free, redirect ke pricing
        if ($requiredPlan === 'pro' && $currentPlan['plan_name'] === 'free') {
            return redirect()->to('/pricing')->with('error', 'Fitur ini hanya tersedia untuk Paket Pro. Silakan upgrade paket Anda.');
        }
        
        // Batasi Aksi Setelah Trial/Subscription Habis (Soft Block)
        if (!$sub) {
            $uri = $request->getUri();
            $uri1 = $uri->getSegment(1);
            $uri2 = $uri->getTotalSegments() >= 2 ? $uri->getSegment(2) : '';
            
            $restrictedActions = ['create', 'store', 'export', 'pdf', 'zip', 'edit', 'update', 'toggle-reminder', 'status'];
            
            if ($uri1 === 'invoices' && in_array($uri2, $restrictedActions)) {
                $lastSub = (new \App\Models\SubscriptionModel())->where('user_id', $userId)->orderBy('id', 'DESC')->first();
                if ($lastSub && $lastSub['status'] === 'expired') {
                    $planModel = new \App\Models\PlanModel();
                    $plan = $planModel->find($lastSub['plan_id']);
                    $msg = ($plan['slug'] === 'trial') 
                        ? 'Masa trial Anda telah berakhir. Aktifkan paket Pro untuk lanjut menggunakan fitur lengkap.'
                        : 'Langganan Anda telah berakhir. Silakan perpanjang paket Anda.';
                    
                    return redirect()->to('/pricing')->with('error', $msg);
                }
            }
        }

        // Limit Invoice untuk Free User logic di Middleware
        // Jika filter digunakan secara default di rute seperti invoice create/store
        $uri = $request->getUri();
        if (in_array($uri->getSegment(1), ['invoices']) && in_array($uri->getTotalSegments() >= 2 ? $uri->getSegment(2) : '', ['create', 'store'])) {
            if ($currentPlan['plan_name'] === 'free') {
                $invoiceModel = new \App\Models\InvoiceModel();
                $invoiceCount = $invoiceModel->where('user_id', $userId)->countAllResults();
                // Sesuai limit yang dikonfigurasi, atau setidaknya 50 seperti instruksi
                $limit = $currentPlan['invoice_limit'] ?? 50; 
                if ($invoiceCount >= $limit) {
                    return redirect()->to('/pricing')->with('error', 'Anda telah mencapai batas maksimal '.$limit.' invoice untuk Paket Gratis. Silakan Upgrade untuk membuat invoice tanpa batas.');
                }
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
