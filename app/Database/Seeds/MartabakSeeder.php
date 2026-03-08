<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class MartabakSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // 1. Create User
        $userEmail = 'bos@martabakberkah.com';
        // Clear old if exists to re-run safely
        $existingUser = $db->table('users')->where('email', $userEmail)->get()->getRow();
        if ($existingUser) {
            $userId = $existingUser->id;
            // Clean up related data to avoid bloat on re-run
            $db->table('invoice_items')->whereIn('invoice_id', function($builder) use ($userId) {
                return $builder->select('id')->from('invoices')->where('user_id', $userId);
            })->delete();
            $db->table('invoices')->where('user_id', $userId)->delete();
            $db->table('clients')->where('user_id', $userId)->delete();
            $db->table('user_profiles')->where('user_id', $userId)->delete();
            $db->table('users')->where('id', $userId)->delete();
        }

        $db->table('users')->insert([
            'email' => $userEmail,
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'user',
            'is_active' => 1,
            'created_at' => Time::now('Asia/Jakarta')->subMonths(4)->toDateTimeString(),
            'updated_at' => Time::now('Asia/Jakarta')->subMonths(4)->toDateTimeString(),
        ]);
        $userId = $db->insertID();

        // 2. Create User Profile
        $db->table('user_profiles')->insert([
            'user_id' => $userId,
            'business_name' => 'Martabak Berkah Jaya',
            'business_address' => 'Jl. Boulevard Raya No. 88, Bekasi Barat, Jawa Barat',
            'business_phone' => '081299887766',
            'business_email' => 'hello@martabakberkah.com',
            'bank_name' => 'BCA',
            'bank_number' => '8765432109',
            'bank_account_name' => 'Berkah Jaya Abadi',
        ]);

        // 3. Create Clients (20 Variations)
        $clientNames = [
            ['nama' => 'Budi Santoso', 'tipe' => 'Perorangan', 'catatan' => 'Pelanggan setia tiap jumat'],
            ['nama' => 'Siti Aminah', 'tipe' => 'Perorangan', 'catatan' => 'Suka porsi jumbo'],
            ['nama' => 'EO Bintang Pesta', 'tipe' => 'Event Organizer', 'catatan' => 'Sering pesan > 50 box'],
            ['nama' => 'Katering Cita Rasa', 'tipe' => 'Catering', 'catatan' => 'Order rutin akhir pekan'],
            ['nama' => 'Rapat RT 04', 'tipe' => 'Toko/Komunitas', 'catatan' => 'Pembayaran selalu via transfer'],
            ['nama' => 'PT. Maju Mundur', 'tipe' => 'Corporate', 'catatan' => 'Suka minta diskon grosir'],
            ['nama' => 'Ibu Ningsih', 'tipe' => 'Perorangan', 'catatan' => ''],
            ['nama' => 'Warung Kopi Senja', 'tipe' => 'Toko Kecil', 'catatan' => 'Titip jual'],
            ['nama' => 'Cafe Indie Bekasi', 'tipe' => 'Toko Kecil', 'catatan' => 'Bayar bulanan'],
            ['nama' => 'Andi Wijaya', 'tipe' => 'Perorangan', 'catatan' => 'Langganan grab pick-up'],
            ['nama' => 'Kartika EO', 'tipe' => 'Event Organizer', 'catatan' => 'Pesan H-1 acara'],
            ['nama' => 'Kelurahan Kayuringin', 'tipe' => 'Komunitas', 'catatan' => 'Pesanan rutin arisan'],
            ['nama' => 'Bapak Joko', 'tipe' => 'Perorangan', 'catatan' => ''],
            ['nama' => 'Kantin SMA 1', 'tipe' => 'Toko Kecil', 'catatan' => 'Term of payment 14 hari'],
            ['nama' => 'Panitia 17an', 'tipe' => 'Event', 'catatan' => 'Event tahunan'],
            ['nama' => 'Rizky EO', 'tipe' => 'Event Organizer', 'catatan' => 'Sering telat bayar'],
            ['nama' => 'Rumah Sakit Mitra', 'tipe' => 'Corporate', 'catatan' => 'Beli untuk paramedis'],
            ['nama' => 'Ibu Dina (Arisan)', 'tipe' => 'Perorangan', 'catatan' => 'Beli khusus Red Velvet'],
            ['nama' => 'Pabrik Sepatu Bekasi', 'tipe' => 'Corporate', 'catatan' => 'Snack lembur buruh'],
            ['nama' => 'Dwi Handoko', 'tipe' => 'Perorangan', 'catatan' => 'Pelanggan walk-in VIP'],
        ];

        $clientIds = [];
        foreach ($clientNames as $index => $cInfo) {
            $db->table('clients')->insert([
                'user_id' => $userId,
                'client_name' => $cInfo['nama'],
                'client_address' => 'Perumahan Bekasi ' . rand(1, 20) . ', RT ' . rand(1, 10),
                'client_phone' => '081' . rand(100000000, 999999999),
                'client_email' => strtolower(str_replace(' ', '', $cInfo['nama'])) . '@gmail.com',
            ]);
            $clientIds[] = [
                'id' => $db->insertID(),
                'catatan' => $cInfo['catatan']
            ];
        }

        // 4. Data Produk
        $products = [
            ['name' => 'Martabak Manis Coklat', 'price' => 35000],
            ['name' => 'Martabak Manis Keju', 'price' => 40000],
            ['name' => 'Martabak Manis Kacang', 'price' => 30000],
            ['name' => 'Martabak Manis Keju Coklat', 'price' => 45000],
            ['name' => 'Martabak Manis Red Velvet', 'price' => 50000],
            ['name' => 'Martabak Manis Green Tea', 'price' => 50000],
            ['name' => 'Martabak Manis Ovomaltine', 'price' => 70000],
            ['name' => 'Martabak Manis Tiramisu', 'price' => 60000],
            ['name' => 'Martabak Telur Ayam', 'price' => 35000],
            ['name' => 'Martabak Telur Bebek', 'price' => 40000],
            ['name' => 'Martabak Telur Special Daging', 'price' => 60000],
            ['name' => 'Martabak Telur Jumbo', 'price' => 80000],
        ];

        // 5. Create Invoices (80-150) -> Let's do 120
        $totalInvoices = 120;
        
        $statuses = [];
        for ($i=0; $i<48; $i++) $statuses[] = 'paid';
        for ($i=0; $i<36; $i++) $statuses[] = 'sent';
        for ($i=0; $i<24; $i++) $statuses[] = 'draft';
        for ($i=0; $i<12; $i++) $statuses[] = 'overdue';
        
        shuffle($statuses);

        for ($i = 1; $i <= $totalInvoices; $i++) {
            $clientData = $clientIds[array_rand($clientIds)];
            $statusRaw = $statuses[$i - 1];

            $daysAgo = rand(1, 90);
            $issuedDate = Time::now('Asia/Jakarta')->subDays($daysAgo);
            
            $termChoice = [7, 14, 30][array_rand([7, 14, 30])];
            $dueDate = $issuedDate->addDays($termChoice);

            $statusDB = $statusRaw;
            if ($statusRaw === 'overdue') {
                $statusDB = 'sent';
                $dueDate = Time::now('Asia/Jakarta')->subDays(rand(2, 10));
                $issuedDate = $dueDate->subDays($termChoice); 
            } else if ($statusRaw === 'sent') {
                $dueDate = Time::now('Asia/Jakarta')->addDays(rand(2, 5));
                $issuedDate = Time::now('Asia/Jakarta')->subDays(rand(1, 4));
            }

            $uuid = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            // Invoice number with padding
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            
            $db->table('invoices')->insert([
                'uuid' => $uuid,
                'user_id' => $userId,
                'client_id' => $clientData['id'],
                'invoice_number' => $invoiceNumber,
                'title' => 'Tagihan Pesanan Martabak',
                'date_issued' => $issuedDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'status' => $statusDB,
                'notes' => $clientData['catatan'] ? "Notes: " . $clientData['catatan'] : 'Terima kasih atas pesanannya!',
                'subtotal' => 0, 
                'discount' => 0,
                'tax' => 0,
                'total_amount' => 0,
                'created_at' => $issuedDate->toDateTimeString(),
                'updated_at' => $statusRaw === 'paid' ? $issuedDate->addDays(rand(1,3))->toDateTimeString() : $issuedDate->toDateTimeString()
            ]);
            $invoiceId = $db->insertID();

            // 6. Generate Items
            $sizeType = rand(1, 10);
            if ($sizeType <= 5) {
                $numItems = rand(1, 2); 
            } else if ($sizeType <= 8) {
                $numItems = rand(3, 5); 
            } else {
                $numItems = rand(10, 15); 
            }

            $subtotal = 0;
            $itemsToInsert = [];
            
            for ($k = 0; $k < $numItems; $k++) {
                $prod = $products[array_rand($products)];
                $qty = ($numItems > 5 && rand(1, 10) > 4) ? rand(10, 50) : rand(1, 5);

                $amount = $prod['price'] * $qty;
                $subtotal += $amount;

                $itemsToInsert[] = [
                    'invoice_id' => $invoiceId,
                    'item_name' => $prod['name'],
                    'description' => 'Level pedas/manis standar',
                    'quantity' => $qty,
                    'price' => $prod['price'],
                    'amount' => $amount
                ];
            }
            $db->table('invoice_items')->insertBatch($itemsToInsert);

            $discount = ($subtotal > 1000000 && rand(1,10) > 5) ? ($subtotal * 0.05) : 0;
            $totalAmount = $subtotal - $discount;

            $db->table('invoices')->where('id', $invoiceId)->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $totalAmount
            ]);
        }
    }
}
