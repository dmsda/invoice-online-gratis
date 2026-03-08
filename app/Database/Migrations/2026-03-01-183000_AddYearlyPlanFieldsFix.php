<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddYearlyPlanFieldsFix extends Migration
{
    public function up()
    {
        $this->forge->addColumn('plans', [
            'price_yearly' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'after'      => 'price_monthly',
                'default'    => 0,
            ]
        ]);

        $this->forge->addColumn('subscriptions', [
            'billing_cycle' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'after'      => 'payment_method',
                'default'    => 'monthly',
            ]
        ]);
        
        // Update data existing Pro plan
        $db = \Config\Database::connect();
        $db->table('plans')->where('slug', 'pro')->update(['price_yearly' => 469000]);
    }

    public function down()
    {
        $this->forge->dropColumn('plans', 'price_yearly');
        $this->forge->dropColumn('subscriptions', 'billing_cycle');
    }
}
