<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserProfileModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin');
            }
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function process_login()
    {
        $session = session();
        $model = new UserModel();
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $data = $model->where('email', $email)->first();

        if ($data) {
            $pass = $data['password_hash'];
            $verify_pass = password_verify($password, $pass);

            if ($verify_pass) {
                $session->regenerate();
                $ses_data = [
                    'id'       => $data['id'],
                    'email'    => $data['email'],
                    'role'     => $data['role'],
                    'isLoggedIn' => true
                ];
                $session->set($ses_data);
                
                if ($data['role'] === 'admin') {
                    return redirect()->to('/admin');
                }
                return redirect()->to('/dashboard');
            } else {
                $session->setFlashdata('msg', 'Password salah.');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('msg', 'Email tidak ditemukan.');
            return redirect()->to('/login');
        }
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/register');
    }

    public function process_register()
    {
        $rules = [
            'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]|max_length[200]',
            'confpassword' => 'matches[password]'
        ];

        if ($this->validate($rules)) {
            $db = \Config\Database::connect();
            $db->transStart();

            try {
                $model = new UserModel();
                $data = [
                    'email'    => $this->request->getVar('email'),
                    'password_hash' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                    'role' => 'user',
                    'is_active' => 1
                ];
                $model->insert($data);
                $userId = $model->getInsertID();
                
                // Create empty profile
                $profileModel = new \App\Models\UserProfileModel();
                $profileModel->insert(['user_id' => $userId, 'business_name' => 'Bisnis Saya']);

                // Default Free Plan mapping
                $planModel = new \App\Models\PlanModel();
                $subModel  = new \App\Models\SubscriptionModel();
                $freePlan = $planModel->where('slug', 'free')->first();
                
                if ($freePlan) {
                    $subModel->insert([
                        'user_id'        => $userId,
                        'plan_id'        => $freePlan['id'],
                        'status'         => 'active',
                        'payment_method' => null,
                        'started_at'     => date('Y-m-d H:i:s'),
                        'expires_at'     => null // Free plan never expires
                    ]);
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Registrasi gagal (Transaction Failed).');
                }

                return redirect()->to('/login')->with('success', 'Registrasi berhasil. Silakan login.');
            } catch(\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat registrasi.');
            }
        } else {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
