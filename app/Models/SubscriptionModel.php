<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionModel extends Model
{
    protected $table            = 'subscriptions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'user_id',
        'plan_id',
        'status',
        'payment_method',
        'billing_cycle',
        'started_at',
        'expires_at',
        'approved_at',
        'approved_by',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = false; 
}
