<?php
define('ENVIRONMENT', 'development');
require 'vendor/autoload.php';
require 'system/bootstrap.php';

$db = \Config\Database::connect();
$fields = $db->getFieldNames('subscriptions');
echo "SUBSCRIPTIONS: " . implode(", ", $fields) . "\n";
$fields2 = $db->getFieldNames('plans');
echo "PLANS: " . implode(", ", $fields2) . "\n";
$fields3 = $db->getFieldNames('payments');
echo "PAYMENTS: " . implode(", ", $fields3) . "\n";
