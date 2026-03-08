<?php
namespace App\Controllers;

use App\Models\PlanModel;
use App\Models\FeatureModel;

class AdminPlan extends BaseController {

    public function index() {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        helper('subscription');
        $planModel = new PlanModel();
        
        $plans = $planModel->findAll();

        $data = [
            'title' => 'Paket & Pricing',
            'plans' => $plans
        ];

        return view('admin/plans/index', $data);
    }
    
    public function update()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        helper('subscription');
        $planModel = new PlanModel();
        $id = $this->request->getPost('id');
        
        $rules = [
            'name' => 'required',
            'price_monthly' => 'required|numeric',
            'price_yearly' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Semua field wajib diisi dan format harus berupa angka untuk nominal.');
        }

        $oldPlan = $planModel->find($id);

        $planModel->update($id, [
            'name' => $this->request->getPost('name'),
            'price_monthly' => $this->request->getPost('price_monthly'),
            'price_yearly' => $this->request->getPost('price_yearly'),
            'invoice_limit' => $this->request->getPost('invoice_limit') ?: null,
        ]);

        $newPlan = $planModel->find($id);

        log_audit('update_plan_price', 'plans', $id, [
            'old_monthly' => $oldPlan['price_monthly'],
            'new_monthly' => $newPlan['price_monthly'],
            'old_yearly' => $oldPlan['price_yearly'],
            'new_yearly' => $newPlan['price_yearly'],
            'ip' => $this->request->getIPAddress()
        ]);

        return redirect()->to('/admin/plans')->with('success', 'Detail paket langganan berhasil diperbarui.');
    }
}
