<?php
require 'vendor/autoload.php';
$app = \Config\Services::codeigniter(new \Config\App());
$app->initialize();

$db = \Config\Database::connect();
$query = $db->query("DESCRIBE subscriptions");
$results = $query->getResultArray();

print_r($results);
