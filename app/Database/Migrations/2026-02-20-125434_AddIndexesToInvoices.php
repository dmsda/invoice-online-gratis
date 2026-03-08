<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToInvoices extends Migration
{
    public function up()
    {
        // Adding indexes to speed up report aggregation queries (Standard SQL)
        $this->db->query('CREATE INDEX IF NOT EXISTS `idx_user_date_status` ON `invoices` (`user_id`, `date_issued`, `status`)');
    }

    public function down()
    {
        // Removing indexes
        $this->db->query('DROP INDEX IF EXISTS `idx_user_date_status`');
    }
}
