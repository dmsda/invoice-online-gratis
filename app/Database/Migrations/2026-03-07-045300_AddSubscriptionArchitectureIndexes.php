<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubscriptionArchitectureIndexes extends Migration
{
    public function up()
    {
        // 1. Modifikasi Tabel Subscriptions (ENUM -> VARCHAR, Tambah pg_transaction_id)
        // Kita gunakan raw query karena Forge kadang ribet dengan ENUM modifying
        $db = \Config\Database::connect();
        
        $fields = [
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'pg_transaction_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'payment_method'
            ]
        ];
        $this->forge->modifyColumn('subscriptions', $fields);

        // 2. Tambah Index Performa Skalabilitas di Subscriptions
        $db->query('ALTER TABLE subscriptions ADD INDEX idx_user_status (user_id, status)');
        $db->query('ALTER TABLE subscriptions ADD INDEX idx_sub_status_expires (status, expires_at)');

        // 3. Tambah Index di Payments
        $db->query('ALTER TABLE payments ADD INDEX idx_payment_status (status)');
        $db->query('ALTER TABLE payments ADD INDEX idx_payment_sub_id (subscription_id)');
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Hapus Index
        $db->query('ALTER TABLE subscriptions DROP INDEX idx_user_status');
        $db->query('ALTER TABLE subscriptions DROP INDEX idx_sub_status_expires');
        $db->query('ALTER TABLE payments DROP INDEX idx_payment_status');
        $db->query('ALTER TABLE payments DROP INDEX idx_payment_sub_id');

        // Kembalikan Kolom
        $this->forge->dropColumn('subscriptions', 'pg_transaction_id');
        
        $fields = [
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['transfer', 'qris'],
                'null' => true
            ]
        ];
        $this->forge->modifyColumn('subscriptions', $fields);
    }
}
