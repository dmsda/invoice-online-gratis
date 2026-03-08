<?php
require 'vendor/autoload.php';
require 'system/bootstrap.php';
$locator = \Config\Services::locator();
$files = $locator->search('Database/Migrations');
print_r($files);
