<?php

use App\Models\SubscriptionModel;
use App\Models\PlanModel;

if (!function_exists('get_active_subscription')) {
    function get_active_subscription($userId)
    {
        $model = new SubscriptionModel();
        $sub = $model->where('user_id', $userId)
                     ->where('status', 'active')
                     ->first();
                     
        if ($sub && $sub['expires_at'] && $sub['expires_at'] < date('Y-m-d H:i:s')) {
            $model->update($sub['id'], ['status' => 'expired']);
            return null;
        }
        return $sub;
    }
}

if (!function_exists('current_plan')) {
    /**
     * Get the active subscription details for the current user.
     * Checks if there's an active subscription, otherwise returns 'free' plan basics.
     * Note: Does not check expiration date here, that should be handled by Cron
     * to change the status from 'active' to 'expired' directly.
     *
     * @param int $userId
     * @return array
     */
    function current_plan($userId)
    {
        $cacheVersion = cache('cache_version_' . $userId) ?: 1;
        $cacheKey = 'current_plan_user_' . $userId . '_v' . $cacheVersion;
        $cachedPlan = cache($cacheKey);

        if ($cachedPlan !== null) {
            return $cachedPlan;
        }

        $subscriptionModel = new SubscriptionModel();
        $planModel = new PlanModel();

        // Cari langganan yang sedang "active"
        $activeSub = $subscriptionModel->where('user_id', $userId)
                                       ->where('status', 'active')
                                       ->first();

        // REAL-TIME EXPIRY TRIGGER (Anti-Cron Fail)
        if ($activeSub && $activeSub['expires_at']) {
            $nowObj = new \DateTime();
            $expObj = new \DateTime($activeSub['expires_at']);
            if ($expObj < $nowObj) {
                // Instan Kadaluwarsa
                $subscriptionModel->update($activeSub['id'], ['status' => 'expired']);
                $activeSub = null; 
            }
        }

        if ($activeSub && $activeSub['plan_id']) {
            $plan = $planModel->find($activeSub['plan_id']);
            if ($plan) {
                $planDetails = [
                    'plan_name' => strtolower($plan['slug']), // 'pro'
                    'plan_id' => $plan['id'],
                    'plan_title' => $plan['name'],
                    'invoice_limit' => $plan['invoice_limit'],
                    'features' => json_decode($plan['feature_json'], true) ?: [],
                    'expires_at' => $activeSub['expires_at'],
                    'subscription_id' => $activeSub['id']
                ];
                cache()->save($cacheKey, $planDetails, 300); // cache for 5 minutes
                return $planDetails;
            }
        }

        // Kalau tidak ada yang aktif, otomatis 'free'
        $freePlan = $planModel->where('slug', 'free')->first();
        $planDetails = [
            'plan_name' => 'free',
            'plan_id' => $freePlan ? $freePlan['id'] : null,
            'plan_title' => 'Free',
            'invoice_limit' => $freePlan ? $freePlan['invoice_limit'] : null,
            'features' => $freePlan ? (json_decode($freePlan['feature_json'], true) ?: []) : ['branding' => false, 'qr' => false, 'wa_reminder' => false],
            'expires_at' => null,
            'subscription_id' => null
        ];

        cache()->save($cacheKey, $planDetails, 300);
        return $planDetails;
    }
}

if (!function_exists('active_subscription')) {
    function active_subscription()
    {
        $model = new SubscriptionModel();

        $sub = $model->where('user_id', session('id'))
                     ->where('status', 'active')
                     ->first();
                     
        if ($sub && $sub['expires_at'] && $sub['expires_at'] < date('Y-m-d H:i:s')) {
            $model->update($sub['id'], ['status' => 'expired']);
            return null;
        }
        return $sub;
    }
}

if (!function_exists('subscription_expired')) {
    function subscription_expired()
    {
        $model = new SubscriptionModel();

        $sub = $model->where('user_id', session('id'))
                     ->where('status', 'active')
                     ->first();

        if (!$sub) return false; // Default to free plan which never expires

        if (!empty($sub['expires_at']) && $sub['expires_at'] < date('Y-m-d H:i:s')) {
            $model->update($sub['id'], ['status' => 'expired']);
            return true;
        }

        return false;
    }
}

if (!function_exists('has_feature')) {
    function has_feature($featureKey)
    {
        $plan = current_plan(session('id'));
        return isset($plan['features'][$featureKey]) && $plan['features'][$featureKey] === true;
    }
}

if (!function_exists('status_color')) {
    function status_color($status)
    {
        return match($status) {
            'pending' => 'warning',
            'active' => 'success',
            'expired' => 'secondary',
            'rejected' => 'danger',
            default => 'light'
        };
    }
}

if (!function_exists('log_audit')) {
    /**
     * Helper to log admin actions easily.
     */
    function log_audit($action, $entity, $entityId, $details = null)
    {
        $logModel = new \App\Models\AuditLogModel();
        
        $logData = [
            'admin_id'   => session('id') ?: null,
            'action'     => $action,
            'entity'     => $entity,
            'entity_id'  => $entityId,
            'details'    => is_array($details) ? json_encode($details) : $details,
            'ip_address' => service('request')->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $logModel->insert($logData);
    }
}
