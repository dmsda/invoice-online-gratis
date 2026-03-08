<?php

namespace App\Controllers;

use App\Models\CouponModel;

class AdminCoupon extends BaseController
{
    protected $couponModel;

    public function __construct()
    {
        $this->couponModel = new CouponModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data = [
            'title' => 'Manajemen Kupon Diskon',
            'coupons' => $this->couponModel->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('admin/coupons/index', $data);
    }

    public function store()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $id = $this->request->getPost('id'); // untuk update
        
        $rules = [
            'code' => 'required',
            'type' => 'required|in_list[percentage,fixed]',
            'value' => 'required|numeric',
            'valid_from' => 'required|valid_date',
            'valid_until' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Pastikan semua format isian benar.');
        }

        $data = [
            'code' => strtoupper(trim($this->request->getPost('code'))),
            'type' => $this->request->getPost('type'),
            'value' => $this->request->getPost('value'),
            'max_usage' => $this->request->getPost('max_usage') ?: null,
            'valid_from' => $this->request->getPost('valid_from'),
            'valid_until' => $this->request->getPost('valid_until'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($id) {
            $this->couponModel->update($id, $data);
            $msg = 'Kupon berhasil diperbarui.';
        } else {
            // Check unique code
            $exist = $this->couponModel->where('code', $data['code'])->first();
            if ($exist) {
                return redirect()->back()->withInput()->with('error', 'Kode kupon promo tersebut sudah pernah digunakan, pilih kode lain.');
            }
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->couponModel->insert($data);
            $msg = 'Kupon promo berhasil ditambahkan dengan sukses.';
        }

        return redirect()->to('/admin/coupons')->with('success', $msg);
    }
    
    public function toggle($id)
    {
        if (session()->get('role') !== 'admin') {
             return redirect()->to('/dashboard');
        }
        
        $coupon = $this->couponModel->find($id);
        if ($coupon) {
            $this->couponModel->update($id, ['is_active' => \abs($coupon['is_active'] - 1)]);
            return redirect()->to('/admin/coupons')->with('success', 'Status validasi kupon berhasil dibalikkan.');
        }
        return redirect()->to('/admin/coupons')->with('error', 'Kupon tidak terdaftar.');
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'admin') {
             return redirect()->to('/dashboard');
        }
        
        // Proteksi cegah hapus sembarangan jika sudah dipakai
        $usageModel = new \App\Models\CouponUsageModel();
        if ($usageModel->where('coupon_id', $id)->countAllResults() > 0) {
            return redirect()->to('/admin/coupons')->with('error', 'Kupon ini ditautkan dalam sistem pembayaran karena sudah dipakai minimal 1 langganan, cukup PAUSE/Nonaktifkan saja.');
        }

        $this->couponModel->delete($id);
        return redirect()->to('/admin/coupons')->with('success', 'Promo berhasil dihapus permanen.');
    }
}
