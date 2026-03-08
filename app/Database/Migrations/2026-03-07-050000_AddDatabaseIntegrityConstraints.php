<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDatabaseIntegrityConstraints extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // 1. CLEANSING ORPHAN RECORDS SEBELUM APPLY FOREIGN KEY
        // Jangan sampai alter table gagal karena ada orphan record yang nyasar
        $db->query("DELETE FROM subscriptions WHERE user_id NOT IN (SELECT id FROM users)");
        $db->query("DELETE FROM payments WHERE subscription_id NOT IN (SELECT id FROM subscriptions)");
        $db->query("DELETE FROM coupon_usages WHERE user_id NOT IN (SELECT id FROM users)");
        $db->query("DELETE FROM coupon_usages WHERE coupon_id NOT IN (SELECT id FROM coupons)");

        // 2. ADD FOREIGN KEYS (CASCADE DELETE)
        $db->query("ALTER TABLE subscriptions ADD CONSTRAINT fk_sub_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
        $db->query("ALTER TABLE subscriptions ADD CONSTRAINT fk_sub_plan FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE");
        $db->query("ALTER TABLE payments ADD CONSTRAINT fk_pay_sub FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE");
        $db->query("ALTER TABLE coupon_usages ADD CONSTRAINT fk_cu_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
        $db->query("ALTER TABLE coupon_usages ADD CONSTRAINT fk_cu_coupon FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE");

        // 3. ADD UNIQUE CONSTRAINTS (ANTI RACE-CONDITION)
        $db->query("ALTER TABLE coupon_usages ADD UNIQUE INDEX idx_unique_user_coupon (user_id, coupon_id)");
        $db->query("ALTER TABLE invoices ADD UNIQUE INDEX idx_unique_user_inv_num (user_id, invoice_number)");
    }

    public function down()
    {
        $db = \Config\Database::connect();

        // 1. DROP UNIQUE CONSTRAINTS
        $db->query("ALTER TABLE coupon_usages DROP INDEX idx_unique_user_coupon");
        $db->query("ALTER TABLE invoices DROP INDEX idx_unique_user_inv_num");

        // 2. DROP FOREIGN KEYS
        $db->query("ALTER TABLE subscriptions DROP FOREIGN KEY fk_sub_user");
        $db->query("ALTER TABLE subscriptions DROP FOREIGN KEY fk_sub_plan");
        $db->query("ALTER TABLE payments DROP FOREIGN KEY fk_pay_sub");
        $db->query("ALTER TABLE coupon_usages DROP FOREIGN KEY fk_cu_user");
        $db->query("ALTER TABLE coupon_usages DROP FOREIGN KEY fk_cu_coupon");
    }
}
