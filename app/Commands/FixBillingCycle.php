<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixBillingCycle extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:fix-billing';
    protected $description = 'Add billing_cycle and price_yearly if missing';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        try {
            $db->query("ALTER TABLE subscriptions ADD COLUMN billing_cycle VARCHAR(20) DEFAULT 'monthly' AFTER payment_method");
            CLI::write("Added billing_cycle to subscriptions.", "green");
        } catch (\Throwable $e) {
            CLI::write("Skip subscriptions: " . $e->getMessage(), "yellow");
        }

        try {
            $db->query("ALTER TABLE plans ADD COLUMN price_yearly DECIMAL(15,2) DEFAULT 0 AFTER price_monthly");
            CLI::write("Added price_yearly to plans.", "green");
        } catch (\Throwable $e) {
            CLI::write("Skip plans: " . $e->getMessage(), "yellow");
        }

        try {
            $db->table('plans')->where('slug', 'pro')->update(['price_yearly' => 469000]);
            CLI::write("Updated pro plan yearly price.", "green");
        } catch (\Throwable $e) {
            CLI::write("Error update plan: " . $e->getMessage(), "red");
        }

        CLI::write("Done fixing DB.", "green");
    }
}
