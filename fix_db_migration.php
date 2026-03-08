<?php
require 'vendor/autoload.php';
$app = \Config\Services::codeigniter(new \Config\App());
$app->initialize();

$db = \Config\Database::connect();

try {
    $db->query("ALTER TABLE subscriptions ADD COLUMN billing_cycle VARCHAR(20) DEFAULT 'monthly' AFTER payment_method");
    echo "Added billing_cycle to subscriptions.\n";
} catch (\Throwable $e) {
    echo "Error subscriptions: " . $e->getMessage() . "\n";
}

try {
    $db->query("ALTER TABLE plans ADD COLUMN price_yearly DECIMAL(15,2) DEFAULT 0 AFTER price_monthly");
    echo "Added price_yearly to plans.\n";
} catch (\Throwable $e) {
    echo "Error plans: " . $e->getMessage() . "\n";
}

try {
    $db->table('plans')->where('slug', 'pro')->update(['price_yearly' => 469000]);
    echo "Updated pro plan yearly price.\n";
} catch (\Throwable $e) {
    echo "Error update plan: " . $e->getMessage() . "\n";
}

echo "Done.\n";
