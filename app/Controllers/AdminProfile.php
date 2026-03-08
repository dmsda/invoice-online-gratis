<?php

namespace App\Controllers;

use App\Models\UserModel;

class AdminProfile extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $userModel = new UserModel();
        $admin = $userModel->find(session()->get('id'));

        $profileModel = new \App\Models\UserProfileModel();
        $profile = $profileModel->where('user_id', session()->get('id'))->first();

        $data = [
            'title' => 'Profil Admin',
            'admin' => $admin,
            'profile' => $profile
        ];

        return view('admin/profile/index', $data);
    }
    
    public function update()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }
        
        $rules = [
            'business_name' => 'required',
            'email' => 'required|valid_email'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Pastikan nama bisnis dan email terisi dengan benar.');
        }
        
        $userModel = new UserModel();
        $profileModel = new \App\Models\UserProfileModel();
        $adminId = session()->get('id');
        
        $updateData = [
            'email' => $this->request->getPost('email')
        ];
        
        // Update password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $updateData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        $userModel->update($adminId, $updateData);

        // Update profile
        $profile = $profileModel->where('user_id', $adminId)->first();
        if ($profile) {
            $profileModel->update($profile['id'], [
                'business_name' => $this->request->getPost('business_name')
            ]);
        } else {
             $profileModel->insert([
                'user_id' => $adminId,
                'business_name' => $this->request->getPost('business_name')
            ]);
        }
        
        // Update session
        session()->set([
            'business_name' => $this->request->getPost('business_name'),
            'email' => $updateData['email']
        ]);
        
        return redirect()->to('/admin/profile')->with('success', 'Profil Admin berhasil diperbarui.');
    }
}
