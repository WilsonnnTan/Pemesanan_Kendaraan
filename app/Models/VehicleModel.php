<?php

namespace App\Models;

use CodeIgniter\Model;

class VehicleModel extends Model
{
    protected $table = 'vehicles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'vehicle_type',
        'vehicle_number',
        'driver_id',
        'start_date',
        'end_date',
        'purpose',
        'status',
        'created_by',
        'updated_by'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'vehicle_type' => 'required',
        'vehicle_number' => 'required',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date',
        'purpose' => 'required',
        'created_by' => 'required'
    ];

    protected $validationMessages = [
        'vehicle_type' => [
            'required' => 'Tipe kendaraan harus diisi'
        ],
        'vehicle_number' => [
            'required' => 'Nomor kendaraan harus diisi'
        ],
        'start_date' => [
            'required' => 'Tanggal mulai harus diisi',
            'valid_date' => 'Format tanggal mulai tidak valid'
        ],
        'end_date' => [
            'required' => 'Tanggal selesai harus diisi',
            'valid_date' => 'Format tanggal selesai tidak valid'
        ],
        'purpose' => [
            'required' => 'Tujuan penggunaan harus diisi'
        ],
        'created_by' => [
            'required' => 'Pembuat harus diisi'
        ]
    ];

    public function getVehicleWithCreator($id = null)
    {
        $query = $this->select('vehicles.*, users.name as creator_name')
            ->join('users', 'users.id = vehicles.created_by')
            ->orderBy('vehicles.created_at', 'DESC');

        if ($id !== null) {
            return $query->where('vehicles.id', $id)->first();
        }

        return $query->findAll();
    }

    public function getVehicleWithDriver($id = null)
    {
        $query = $this->select('vehicles.*, drivers.name as driver_name, drivers.license_number, drivers.phone_number')
            ->join('drivers', 'drivers.id = vehicles.driver_id', 'left')
            ->orderBy('vehicles.created_at', 'DESC');

        if ($id !== null) {
            return $query->where('vehicles.id', $id)->first();
        }

        return $query->findAll();
    }

    public function getAvailableVehicles()
    {
        return $this->where('status', 'available')->findAll();
    }

    public function getVehiclesByStatus($status)
    {
        return $this->where('status', $status)->findAll();
    }

    public function getVehiclesByCreator($userId)
    {
        return $this->where('created_by', $userId)->findAll();
    }

    public function getVehiclesWithDetails()
    {
        return $this->select('vehicles.*, users.name as creator_name')
            ->join('users', 'users.id = vehicles.created_by')
            ->orderBy('vehicles.created_at', 'DESC')
            ->findAll();
    }
} 