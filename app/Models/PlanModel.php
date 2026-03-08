<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanModel extends Model
{
    protected $table            = 'plans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'name',
        'slug',
        'price_monthly',
        'price_yearly',
        'invoice_limit',
        'feature_json',
        'is_active',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = false; // Using default CURRENT_TIMESTAMP in DB
}
