<?php
namespace App\Controllers;

use App\Models\UserModel;

class AdminUser extends BaseController {

    public function index() {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $db = \Config\Database::connect();
        
        $search = $this->request->getGet('search');
        
        $userModel = new \App\Models\UserModel();
        $userModel->select('users.id, users.email, users.role, users.is_active, users.created_at, up.business_name as profile_bus_name, p.name as plan_name, s.status as sub_status');
        $userModel->join('user_profiles up', 'up.user_id = users.id', 'left');
        $userModel->join('subscriptions s', 's.user_id = users.id AND s.status = "active"', 'left');
        $userModel->join('plans p', 'p.id = s.plan_id', 'left');
        $userModel->where('users.role', 'user');
        
        if (!empty($search)) {
            $userModel->groupStart()
                      ->like('users.email', $search)
                      ->orLike('up.business_name', $search)
                      ->groupEnd();
        }
        
        $userModel->orderBy('users.created_at', 'DESC');
        $userModel->groupBy('users.id');
        
        $users = $userModel->paginate(20, 'default');

        $data = [
            'title' => 'Manajemen User',
            'users' => $users,
            'pager' => $userModel->pager,
            'search' => $search
        ];

        return view('admin/users/index', $data);
    }
    
    public function toggleStatus($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        
        if ($user && $user['role'] !== 'admin') {
            $newStatus = abs($user['is_active'] - 1);
            $userModel->update($id, ['is_active' => $newStatus]);
            
            $msg = $newStatus == 1 ? 'Akun berhasil diaktifkan kembali.' : 'Akun berhasil ditangguhkan/suspend.';
            return redirect()->to('/admin/users')->with('success', $msg);
        }
        
        return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan atau aksi ditolak.');
    }
}
