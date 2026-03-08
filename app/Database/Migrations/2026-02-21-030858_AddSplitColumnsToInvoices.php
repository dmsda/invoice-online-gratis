<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSplitColumnsToInvoices extends Migration
{
    public function up()
    {
        $fields = [
            'split_group_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'split_part' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'split_total' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ];
        $this->forge->addColumn('invoices', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('invoices', ['split_group_id', 'split_part', 'split_total']);
    }
}
