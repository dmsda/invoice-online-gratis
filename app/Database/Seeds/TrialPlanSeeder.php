<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TrialPlanSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        $exists = $db->table('plans')->where('slug', 'trial')->countAllResults();
        
        if ($exists == 0) {
            $db->table('plans')->insert([
                'name' => 'Trial 14 Hari',
                'slug' => 'trial',
                'price_monthly' => 0,
                'price_yearly'  => 0,
                'invoice_limit' => null,
                'feature_json'  => json_encode([
                    'branding' => true,
                    'qr' => true,
                    'wa_reminder' => true
                ]),
                'is_active' => 1
            ]);
            echo "Trial plan seeded successfully.\n";
        } else {
            echo "Trial plan already exists.\n";
        }
    }
}
