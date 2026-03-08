<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DummyAccountSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // --- CLEAN UP DULU ---
        $db->table('users')->whereIn('email', ['admin@admin.com', 'user@user.com'])->delete();

        // --- BUAT ADMIN DUMMY ---
        $adminEmail = 'admin@admin.com';
        $admin = $db->table('users')->where('email', $adminEmail)->get()->getRow();
        
        if (!$admin) {
            $db->table('users')->insert([
                'email' => $adminEmail,
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "Akun Admin Dummy Berhasil Dibuat.\n";
        } else {
            echo "Akun Admin sudah ada.\n";
        }

        // --- BUAT USER DUMMY ---
        $userEmail = 'user@user.com';
        $userObj = $db->table('users')->where('email', $userEmail)->get()->getRow();
        
        if (!$userObj) {
            $db->table('users')->insert([
                'email' => $userEmail,
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'user',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            $userId = $db->insertID();

            // Insert User Profile
            $db->table('user_profiles')->insert([
                'user_id' => $userId,
                'business_name' => 'Toko Dummy Makmur',
                'business_address' => 'Jl. Jenderal Sudirman No. 123, Jakarta',
                'business_phone' => '081234567890'
            ]);
            
            // Insert Subscription (Free Plan as Base)
            $db->table('subscriptions')->insert([
                'user_id' => $userId,
                'plan_id' => 1, // Asumsi 1 adalah Paket Free/Trial Default
                'status' => 'active',
                'billing_cycle' => 'monthly',
                'started_at' => date('Y-m-d H:i:s'),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+14 days')), // Trial 14 Hari Default
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "Akun User Dummy Berhasil Dibuat.\n";
        } else {
            echo "Akun User sudah ada.\n";
        }
    }
}
