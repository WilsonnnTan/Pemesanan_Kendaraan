<?php

namespace App\Models;

use CodeIgniter\Model;

class DriverModel extends Model
{
    protected $table = 'drivers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'name',
        'license_number',
        'phone_number',
        'status'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required',
        'license_number' => 'required|is_unique[drivers.license_number,id,{id}]',
        'phone_number' => 'required'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama driver harus diisi'
        ],
        'license_number' => [
            'required' => 'Nomor SIM harus diisi',
            'is_unique' => 'Nomor SIM sudah terdaftar'
        ],
        'phone_number' => [
            'required' => 'Nomor telepon harus diisi'
        ]
    ];
} 