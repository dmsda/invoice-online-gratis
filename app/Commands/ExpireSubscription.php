<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\SubscriptionModel;

class ExpireSubscription extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Subscription';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'expire:subscription';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Automatically change expired active subscriptions to expired status.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'expire:subscription';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $subModel = new SubscriptionModel();

        $now = date('Y-m-d H:i:s');
        
        // Cari semua yang aktif tapi sudah kelewat tanggal kadaluwarsa
        $expiredSubs = $subModel->where('status', 'active')
                                ->where('expires_at <', $now)
                                ->findAll();

        if (empty($expiredSubs)) {
            CLI::write("No active subscriptions are currently expired.", 'green');
            return;
        }

        $count = 0;
        foreach ($expiredSubs as $sub) {
            $subModel->update($sub['id'], ['status' => 'expired']);
            
            // Hapus cache current plan milik user sehingga pada akses ke web akan terdeteksi expired/free
            $cacheVersion = cache('cache_version_' . $sub['user_id']) ?: 1;
            cache()->delete('current_plan_user_' . $sub['user_id'] . '_v' . $cacheVersion);
            cache()->save('cache_version_' . $sub['user_id'], $cacheVersion + 1, 31536000);
            
            $count++;
        }

        CLI::write("Successfully expired $count subscriptions.", 'bg_green');
    }
}
