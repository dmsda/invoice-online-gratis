<?php

namespace App\Controllers;

use App\Models\UserProfileModel;
use App\Models\UserModel;

class Settings extends BaseController
{
    protected $userModel;
    protected $profileModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->profileModel = new UserProfileModel();
    }

    public function profile()
    {
        $userId = session()->get('id');
        
        $user = $this->userModel->find($userId);
        $profile = $this->profileModel->find($userId); // profile PK is user_id based on schema

        if (!$profile) {
            // Should exist from registration, but just in case
            $this->profileModel->insert(['user_id' => $userId, 'business_name' => 'Bisnis Saya']);
            $profile = $this->profileModel->find($userId);
        }

        return view('settings/profile', [
            'title' => 'Pengaturan Profil Usaha',
            'user' => $user,
            'profile' => $profile
        ]);
    }

    public function update_profile()
    {
        $userId = session()->get('id');
        
        // Validation
        $rules = [
            'business_name' => 'required|min_length[3]|max_length[100]',
            'logo' => 'permit_empty|is_image[logo]|max_size[logo,1024]|mime_in[logo,image/jpg,image/jpeg,image/png]',
            'qr_code' => 'permit_empty|is_image[qr_code]|max_size[qr_code,1024]|mime_in[qr_code,image/jpg,image/jpeg,image/png]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle File Upload
        $logoPath = null;
        $file = $this->request->getFile('logo');
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads/logos', $newName);
            $logoPath = 'uploads/logos/' . $newName;
        }

        // Prepare Data
        $data = [
            'business_name' => $this->request->getVar('business_name'),
            'business_address' => $this->request->getVar('business_address'),
            'business_phone' => $this->request->getVar('business_phone'),
            'business_email' => $this->request->getVar('business_email'),
            'bank_name' => $this->request->getVar('bank_name'),
            'bank_number' => $this->request->getVar('bank_number'),
            'bank_account_name' => $this->request->getVar('bank_account_name'),
        ];

        if ($logoPath) {
            $data['logo_path'] = $logoPath;
            
            // Delete old logo if exists?
            // Optional: Implement cleanup later
        }

        // Handle QR Code Upload
        $qrFile = $this->request->getFile('qr_code');
        
        if ($qrFile && $qrFile->isValid() && !$qrFile->hasMoved()) {
            // Rename: qris_[user_id].ext (Overwrite existing)
            $ext = $qrFile->getExtension();
            $newQrName = 'qris_' . $userId . '.' . $ext;
            
            $qrFile->move(ROOTPATH . 'public/uploads/qris', $newQrName, true);
            $data['qr_code_path'] = 'uploads/qris/' . $newQrName;
        }

        // Update User Profile
        $this->profileModel->update($userId, $data);

        return redirect()->to('/settings/profile')->with('success', 'Profil usaha dan QR pembayaran berhasil disimpan.');
    }

    public function delete_qr()
    {
        $userId = session()->get('id');
        $profile = $this->profileModel->find($userId);

        if ($profile && !empty($profile['qr_code_path'])) {
            // 1. Hapus file fisik
            if (file_exists(ROOTPATH . 'public/' . $profile['qr_code_path'])) {
                unlink(ROOTPATH . 'public/' . $profile['qr_code_path']);
            }

            // 2. Update DB jadi NULL
            $this->profileModel->update($userId, ['qr_code_path' => null]);

            return redirect()->to('/settings/profile')->with('success', 'QR pembayaran berhasil dihapus.');
        }

        return redirect()->to('/settings/profile')->with('error', 'Tidak ada QR yang dihapus.');
    }
}
