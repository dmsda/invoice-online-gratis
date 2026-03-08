<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRemindersEnabledToInvoices extends Migration
{
    public function up()
    {
        $fields = [
            'reminders_enabled' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'null' => false,
                'after' => 'status'
            ]
        ];
        
        $this->forge->addColumn('invoices', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('invoices', 'reminders_enabled');
    }
}
