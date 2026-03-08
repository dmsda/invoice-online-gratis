<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'subscription_id',
        'amount',
        'method',
        'bank_name',
        'account_name',
        'payment_date',
        'proof_file',
        'status',
        'verified_by',
        'verified_at',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = false; 
}
