<?php

namespace App\Models;

use CodeIgniter\Model;

class UserProfileModel extends Model
{
    protected $table            = 'user_profiles';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = false; // user_id is PK
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id', 
        'business_name', 
        'business_address', 
        'business_phone', 
        'business_email',
        'bank_name',
        'bank_number',
        'bank_account_name',
        'logo_path',
        'qr_code_path'
    ];
}
