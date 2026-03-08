<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCouponsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['percentage', 'fixed'],
            ],
            'value' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],
            'max_usage' => [
                'type' => 'INT',
                'null' => true,
            ],
            'used_count' => [
                'type'    => 'INT',
                'default' => 0,
            ],
            'valid_from' => [
                'type' => 'DATETIME',
            ],
            'valid_until' => [
                'type' => 'DATETIME',
            ],
            'is_active' => [
                'type'    => 'BOOLEAN',
                'default' => 1,
            ],
            'created_at DATETIME default current_timestamp',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('coupons');
    }

    public function down()
    {
        $this->forge->dropTable('coupons');
    }
}
