<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPerformanceIndexes extends Migration
{
    public function up()
    {
        // Alter Tabel Clients untuk optimasi pencarian no telepon/WA
        $this->forge->addField("ALTER TABLE `clients` ADD INDEX `idx_client_phone` (`client_phone`)");
        
        // Alter Tabel Invoices untuk Laporan Bulanan (Rentang Tanggal)
        // Note: Composite index (user_id, status) sudah kita tambahkan via initial migration.
        $this->forge->addField("ALTER TABLE `invoices` ADD INDEX `idx_user_date` (`user_id`, `date_issued`)");
        
        $this->db->query("ALTER TABLE `clients` ADD INDEX `idx_client_phone` (`client_phone`)");
        $this->db->query("ALTER TABLE `invoices` ADD INDEX `idx_user_date` (`user_id`, `date_issued`)");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `clients` DROP INDEX `idx_client_phone`");
        $this->db->query("ALTER TABLE `invoices` DROP INDEX `idx_user_date`");
    }
}
