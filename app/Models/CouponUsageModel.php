<?php

namespace App\Models;

use CodeIgniter\Model;

class CouponUsageModel extends Model
{
    protected $table            = 'coupon_usages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'coupon_id',
        'user_id',
        'subscription_id',
        'used_at'
    ];

    // Dates
    protected $useTimestamps = false;
}
