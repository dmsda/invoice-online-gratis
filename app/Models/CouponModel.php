<?php

namespace App\Models;

use CodeIgniter\Model;

class CouponModel extends Model
{
    protected $table            = 'coupons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'type',
        'value',
        'max_usage',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = false;
}
