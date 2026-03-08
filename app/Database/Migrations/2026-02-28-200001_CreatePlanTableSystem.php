<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlanTableSystem extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT', 
                'constraint' => 11, 
                'unsigned' => true, 
                'auto_increment' => true
            ],
            'name' => [
                'type' => 'VARCHAR', 
                'constraint' => 50
            ],
            'slug' => [
                'type' => 'VARCHAR', 
                'constraint' => 50
            ],
            'price_monthly' => [
                'type' => 'DECIMAL', 
                'constraint' => '15,2',
                'default' => '0.00'
            ],
            'price_yearly' => [
                'type' => 'DECIMAL', 
                'constraint' => '15,2', 
                'null' => true
            ],
            'invoice_limit' => [
                'type' => 'INT', 
                'constraint' => 11, 
                'null' => true,
                'comment' => 'Null means unlimited'
            ],
            'feature_json' => [
                'type' => 'TEXT', 
                'null' => true
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP'
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('plans');
        
        // Insert Default Plans (Seeding on Migration up)
        $db = \Config\Database::connect();
        $db->table('plans')->insertBatch([
            [
                'name' => 'Free',
                'slug' => 'free',
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'invoice_limit' => null, // unlimited as per requirement
                'feature_json' => json_encode(['branding' => false, 'qr' => false, 'wa_reminder' => false]),
                'is_active' => 1
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price_monthly' => 49000.00,
                'price_yearly' => null,
                'invoice_limit' => null,
                'feature_json' => json_encode(['branding' => true, 'qr' => true, 'wa_reminder' => true]),
                'is_active' => 1
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('plans');
    }
}
