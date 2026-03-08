<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTypeToInvoices extends Migration
{
    public function up()
    {
        $this->forge->addColumn('invoices', [
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['jasa', 'produk'],
                'default'    => 'produk',
                'after'      => 'status'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('invoices', 'type');
    }
}
