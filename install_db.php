<?php

$mysqli = new mysqli("127.0.0.1", "root", "", "invoice_app");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$plans = "CREATE TABLE IF NOT EXISTS `plans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `price_monthly` decimal(15,2) NOT NULL DEFAULT '0.00',
  `price_yearly` decimal(15,2) DEFAULT NULL,
  `invoice_limit` int(11) DEFAULT NULL COMMENT 'Null means unlimited',
  `feature_json` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)";
$mysqli->query($plans);

$subs = "CREATE TABLE IF NOT EXISTS `subscriptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `plan_id` int(11) unsigned NOT NULL,
  `status` enum('pending','active','expired','cancelled','rejected') NOT NULL DEFAULT 'pending',
  `payment_method` enum('transfer','qris') DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)";
$mysqli->query($subs);

$pays = "CREATE TABLE IF NOT EXISTS `payments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method` varchar(20) NOT NULL,
  `bank_name` varchar(50) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `proof_file` varchar(255) DEFAULT NULL,
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `verified_by` int(11) unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)";
$mysqli->query($pays);

// Seeding Default Plans
$check = $mysqli->query("SELECT * FROM plans")->num_rows;
if ($check == 0) {
    $mysqli->query("INSERT INTO plans (name, slug, price_monthly, invoice_limit, feature_json, is_active) VALUES ('Free', 'free', 0.00, NULL, '{\"branding\":false,\"qr\":false,\"wa_reminder\":false}', 1)");
    $mysqli->query("INSERT INTO plans (name, slug, price_monthly, invoice_limit, feature_json, is_active) VALUES ('Pro', 'pro', 49000.00, NULL, '{\"branding\":true,\"qr\":true,\"wa_reminder\":true}', 1)");
}

echo "Database Tables Created Successfully!";
