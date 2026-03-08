<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table            = 'invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'user_id', 'client_id', 'invoice_number', 'title', 
        'date_issued', 'due_date', 'status', 'type',
        'subtotal', 'discount', 'tax', 'total_amount', 'notes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'client_id' => 'required|numeric',
        'date_issued' => 'required|valid_date',
        'invoice_number' => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateUuid'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function generateUuid(array $data)
    {
        if (! isset($data['data']['uuid'])) {
            $data['data']['uuid'] = $this->guidv4();
        }
        return $data;
    }

    private function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
    
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Helper to generate invoice number: INV/YYYY/MM/001
    public function generateInvoiceNumber($userId)
    {
        $prefix = 'INV/' . date('Y/m') . '/';
        
        // Count invoices for this user in this month
        // Note: This is simple counter, for high concurrency might need better approach, 
        // but for shared hosting/single user usage this is fine.
        $builder = $this->builder();
        $count = $builder->where('user_id', $userId)
                         ->like('invoice_number', $prefix, 'after')
                         ->countAllResults();
        
        return $prefix . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }


}
