<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCouponUsagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'coupon_id' => [
                'type' => 'INT',
            ],
            'user_id' => [
                'type' => 'INT',
            ],
            'subscription_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'used_at DATETIME default current_timestamp',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('coupon_usages');
    }

    public function down()
    {
        $this->forge->dropTable('coupon_usages');
    }
}
