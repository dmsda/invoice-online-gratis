<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
chdir(__DIR__ . '/../');
require 'app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$db = \Config\Database::connect();
$fields = $db->getFieldData('subscriptions');
echo "SUBSCRIPTIONS: ";
foreach($fields as $f) { echo $f->name . " | "; }
