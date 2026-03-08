<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixDB extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:fix';
    protected $description = 'Clear locked migrations records';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        $db->query("DELETE FROM migrations WHERE class LIKE '%CreatePlansTable%' OR class LIKE '%CreateSubscriptionsTable%' OR class LIKE '%CreatePaymentsTable%'");
        $db->query("DELETE FROM migrations WHERE class LIKE '%CreatePlanTableSystem%' OR class LIKE '%CreateSubscriptionTableSystem%' OR class LIKE '%CreatePaymentTableSystem%'");
        
        CLI::write("Migration locks cleared. You can now migrate safely.", "green");
    }
}
