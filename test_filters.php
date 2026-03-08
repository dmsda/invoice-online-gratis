<?php
require 'vendor/autoload.php';
require 'system/bootstrap.php';
$config = new \Config\Filters();
print_r($config->aliases);
