<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class InvoiceStressSeeder extends Seeder
{
    private $faker;
    private $userId = 2; // ID User untuk Stress Test
    private $products_manis = [
        ['name' => 'Martabak Manis Coklat', 'price' => 25000],
        ['name' => 'Martabak Manis Keju', 'price' => 30000],
        ['name' => 'Martabak Manis Kacang', 'price' => 25000],
        ['name' => 'Martabak Manis Keju Coklat', 'price' => 35000],
        ['name' => 'Martabak Manis Red Velvet', 'price' => 45000],
        ['name' => 'Martabak Manis Green Tea', 'price' => 45000],
        ['name' => 'Martabak Manis Ovomaltine', 'price' => 55000],
        ['name' => 'Martabak Manis Tiramisu', 'price' => 45000],
    ];
    private $products_telur = [
        ['name' => 'Martabak Telur Ayam Biasa', 'price' => 25000],
        ['name' => 'Martabak Telur Bebek Biasa', 'price' => 30000],
        ['name' => 'Martabak Telur Ayam Spesial', 'price' => 35000],
        ['name' => 'Martabak Telur Bebek Spesial', 'price' => 40000],
        ['name' => 'Martabak Telur Jumbo Daging Sapi', 'price' => 55000],
        ['name' => 'Martabak Telur Kornet', 'price' => 35000],
        ['name' => 'Martabak Telur Tuna', 'price' => 40000],
    ];

    public function run()
    {
        // Gunakan locale Indonesia
        $this->faker = \Faker\Factory::create('id_ID');

        // 0. Disable foreign key checks for clean sweep
        $this->db->disableForeignKeyChecks();

        // Warning: Jika ingin mereset data stress test sebelumnya
        // Hapus data yang ber-user ID 2
        echo "Menghapus data Stress (User ID {$this->userId}) lama (jika ada)...\n";
        
        // Hapus invoice_items (subquery)
        $this->db->query("DELETE FROM invoice_items WHERE invoice_id IN (SELECT id FROM invoices WHERE user_id = {$this->userId})");
        $this->db->table('invoices')->where('user_id', $this->userId)->delete();
        $this->db->table('clients')->where('user_id', $this->userId)->delete();
        $this->db->table('user_profiles')->where('user_id', $this->userId)->delete();
        $this->db->table('users')->where('id', $this->userId)->delete();
        
        $this->db->enableForeignKeyChecks();

        // 1. BUAT USER (OWNER UMKM)
        echo "Membuat User Owner...\n";
        $this->db->table('users')->insert([
            'id' => $this->userId,
            'email' => 'stresstest@martabakberkah.com',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('user_profiles')->insert([
            'user_id' => $this->userId,
            'business_name' => 'Martabak Berkah Jaya (Stress Test)',
            'business_address' => "Jl. Raya Jatiwaringin No. 88\nPondok Gede, Kota Bekasi\nJawa Barat 17411",
            'business_phone' => '081234567890',
            'bank_name' => 'BCA',
            'bank_number' => '8765432109',
            'bank_account_name' => 'Martabak Berkah Jaya',
        ]);

        // 2. BUAT 50 PELANGGAN RANDOM
        echo "Membuat 50 Pelanggan Indonesia...\n";
        $clientsToInsert = [];
        $clientIds = [];
        for ($i = 0; $i < 50; $i++) {
            $company = $this->faker->optional(0.3)->company();
            $clientsToInsert[] = [
                'user_id'      => $this->userId,
                'client_name'  => $this->faker->name(),
                'client_email' => $this->faker->optional(0.5)->safeEmail(),
                'client_phone' => $this->faker->phoneNumber(),
                'client_address' => $this->faker->address(),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ];
        }
        $this->db->table('clients')->insertBatch($clientsToInsert);
        
        // Ambil ID Client yang barusan dibuat untuk relasi
        $clients = $this->db->table('clients')->where('user_id', $this->userId)->select('id')->get()->getResultArray();
        foreach ($clients as $client) {
            $clientIds[] = $client['id'];
        }

        // 3. GENERATE 1000 INVOICE & RIBUAN ITEMS DENGAN BATCHING
        echo "Membuat 1000 Invoice & Items (Batching)...\n";
        
        $totalInvoices = 1000;
        $batchSize = 100;
        
        $allProducts = array_merge($this->products_manis, $this->products_telur);
        $dueDaysOptions = [7, 14, 30];

        $invoicesBatchCount = 0;
        
        for ($batchIndex = 0; $batchIndex < ($totalInvoices / $batchSize); $batchIndex++) {
            $invoicesBatch = [];
            // Simpan array untuk insert items nanti, karena ID invoice butuh dicapture
            // Karena di CodeIgniter getInsertId() tidak jalan massal sempurna setelah insertBatch, 
            // Kita terpaksa insert manual 1 per 1 khusus invoice untuk mendapat ID, 
            // ATAU insertBatch Invoice lalu select balik id-nya (yang ini lebih performan).
            
            // Generate UUIDs upfront for selection
            $invoicesUuids = [];
            $invoiceDataDraft = [];

            for ($i = 0; $i < $batchSize; $i++) {
                $statusProb = rand(1, 100);
                if ($statusProb <= 45) {
                    $status = 'paid';
                } elseif ($statusProb <= 70) {
                    $status = 'sent';
                } elseif ($statusProb <= 90) {
                    $status = 'draft';
                } else {
                    $status = 'sent'; // Will be effectively OVERDUE physically if un-paid and due_date < today
                }

                // Random date last 6 months
                // 180 days approx
                $daysAgo = rand(0, 180);
                $dateIssued = Time::now()->subDays($daysAgo);
                
                $dueDays = $dueDaysOptions[array_rand($dueDaysOptions)];
                $dueDate = (clone $dateIssued)->addDays($dueDays);
                
                // Force Overdue Logic based on probability (10% telat)
                if ($statusProb > 90) {
                    // Harus telat: Date issued harus lebih lama dari dueDays dari sekarang
                    // Contoh: Telat = due_date < today, status != paid
                    $daysAgo = rand($dueDays + 1, 180); 
                    $dateIssued = Time::now()->subDays($daysAgo);
                    $dueDate = (clone $dateIssued)->addDays($dueDays);
                    $status = 'sent'; // Still sent, but logically overdue
                }

                $uuid = $this->generateUuidV4();
                $invoicesUuids[] = $uuid;

                // Nanti kita hitung total amount setelah items dibuat
                $invoicesBatch[] = [
                    'uuid' => $uuid,
                    'user_id' => $this->userId,
                    'client_id' => $clientIds[array_rand($clientIds)],
                    'invoice_number' => 'INV/TEST/' . date('Y/m', strtotime($dateIssued)) . '/' . str_pad($invoicesBatchCount + $i + 1, 4, '0', STR_PAD_LEFT),
                    'title' => 'Pesanan Martabak ' . $this->faker->words(2, true),
                    'date_issued' => $dateIssued->format('Y-m-d'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'status' => $status,
                    'type' => 'produk',
                    'subtotal' => 0, // Placeholder
                    'discount' => 0, // Placeholder
                    'tax' => 0,
                    'total_amount' => 0, // Placeholder
                    'notes' => $this->faker->optional(0.3)->sentence(),
                    'created_at' => $dateIssued->format('Y-m-d H:i:s'),
                    'updated_at' => $status === 'paid' ? (clone $dateIssued)->addDays(rand(1, $dueDays))->format('Y-m-d H:i:s') : $dateIssued->format('Y-m-d H:i:s'),
                ];
            }

            // Insert Invoice Batch
            $this->db->table('invoices')->insertBatch($invoicesBatch);
            
            // Select back to get IDs
            $insertedInvoices = $this->db->table('invoices')
                ->whereIn('uuid', $invoicesUuids)
                ->select('id, uuid, status')
                ->get()->getResultArray();

            $itemsBatch = [];
            $invoiceUpdates = []; // For updating totals

            foreach ($insertedInvoices as $invRow) {
                // Determine item count
                // 1-5 item (normal), 6-20 (event besar, 15% cases)
                $itemCount = (rand(1, 100) <= 85) ? rand(1, 5) : rand(6, 20);
                
                $subtotal = 0;
                
                for ($j = 0; $j < $itemCount; $j++) {
                    $prod = $allProducts[array_rand($allProducts)];
                    // Grosir if item count is large => quantity higher
                    $qty = ($itemCount > 5) ? rand(5, 50) : rand(1, 4);
                    
                    $price = $prod['price'];
                    $amount = $price * $qty;
                    $subtotal += $amount;

                    $itemsBatch[] = [
                        'invoice_id' => $invRow['id'],
                        'item_name' => $prod['name'],
                        'description' => $this->faker->optional(0.2)->words(3, true),
                        'quantity' => $qty,
                        'price'    => $price,
                        'amount'   => $amount,
                    ];
                }

                // Diskon prob 10% dari total invoice batch ini
                $discount = 0;
                if (rand(1, 100) <= 10) {
                    // Diskon fixed misal 10.000, 20.000, 50.000 atau percentage
                    $discount = rand(1, 5) * 10000;
                    if ($discount > $subtotal) $discount = $subtotal;
                }

                $totalAmount = $subtotal - $discount;

                $invoiceUpdates[] = [
                    'id' => $invRow['id'], // update by id
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total_amount' => $totalAmount,
                ];
            }

            // Insert Items Batch
            if (!empty($itemsBatch)) {
                // CI4 max batch is usually safe around 100-500 depending on max_allowed_packet
                // We chunk items to safe arrays of 500
                $itemChunks = array_chunk($itemsBatch, 500);
                foreach($itemChunks as $chunk) {
                    $this->db->table('invoice_items')->insertBatch($chunk);
                }
            }

            // Update Invoices Batch for the totals
            if (!empty($invoiceUpdates)) {
                $this->db->table('invoices')->updateBatch($invoiceUpdates, 'id');
            }

            $invoicesBatchCount += $batchSize;
            echo "-> Seeded {$invoicesBatchCount} / {$totalInvoices} Invoices\n";
        }

        echo "Stress Seeding Selesai!\n";
    }

    private function generateUuidV4()
    {
        $data = random_bytes(16);
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
