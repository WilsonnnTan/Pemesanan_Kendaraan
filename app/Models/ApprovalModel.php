<?php

namespace App\Models;

use CodeIgniter\Model;

class ApprovalModel extends Model
{
    protected $table = 'approvals';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'vehicle_id',
        'approver_id',
        'level',
        'status',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validasi
    protected $validationRules = [
        'vehicle_id' => 'required',
        'approver_id' => 'required',
        'level' => 'required|in_list[1,2]',
        'status' => 'required|in_list[pending,approved,rejected]'
    ];

    protected $validationMessages = [
        'vehicle_id' => [
            'required' => 'ID Kendaraan harus diisi'
        ],
        'approver_id' => [
            'required' => 'ID Approver harus diisi'
        ],
        'level' => [
            'required' => 'Level persetujuan harus diisi',
            'in_list' => 'Level persetujuan tidak valid'
        ],
        'status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status tidak valid'
        ]
    ];

    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new \App\Models\UserModel();
    }

    public function getApprovalsByApprover($approverId)
    {
        return $this->select('approvals.*, users.name as approver_name, vehicles.*')
            ->join('users', 'users.id = approvals.approver_id')
            ->join('vehicles', 'vehicles.id = approvals.vehicle_id')
            ->where('approvals.approver_id', $approverId)
            ->orderBy('approvals.level', 'ASC')
            ->findAll();
    }

    public function getApprovalsByVehicle($vehicleId)
    {
        return $this->select('approvals.*, users.name as approver_name')
            ->join('users', 'users.id = approvals.approver_id')
            ->where('approvals.vehicle_id', $vehicleId)
            ->orderBy('approvals.level', 'ASC')
            ->findAll();
    }

    // Mendapatkan persetujuan yang pending untuk approver tertentu
    public function getPendingApprovals()
    {
        return $this->where('status', 'pending')->findAll();
    }

    public function getApprovedApprovals()
    {
        return $this->where('status', 'approved')->findAll();
    }

    public function getRejectedApprovals()
    {
        return $this->where('status', 'rejected')->findAll();
    }

    public function approve($id, $approverId, $notes = null)
    {
        $approval = $this->find($id);
        if (!$approval) {
            return false;
        }

        // Update status approval
        $this->update($id, [
            'status' => 'approved',
            'notes' => $notes ?? 'Disetujui'
        ]);

        // Ambil data vehicle dan approver
        $vehicleModel = new VehicleModel();
        $vehicle = $vehicleModel->find($approval['vehicle_id']);
        $approver = $this->userModel->find($approverId);

        // Buat report baru untuk approval
        $reportModel = new ReportModel();
        $reportData = [
            'vehicle_id' => $approval['vehicle_id'],
            'created_by' => $approverId,
            'notes' => sprintf(
                'Kendaraan %s (%s) disetujui oleh %s (Level %d). Catatan: %s',
                $vehicle['vehicle_type'],
                $vehicle['vehicle_number'],
                $approver['name'],
                $approval['level'],
                $notes ?? 'Tidak ada catatan'
            ),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $reportModel->insert($reportData);

        // Jika ini adalah level 1, cek apakah ada level 2
        if ($approval['level'] == 1) {
            $nextApproval = $this->where('vehicle_id', $approval['vehicle_id'])
                               ->where('level', 2)
                               ->first();
            
            if ($nextApproval) {
                // Jika ada level 2, tetap status pending karena masih menunggu level 2
                return $vehicleModel->update($approval['vehicle_id'], ['status' => 'pending']);
            }
        }

        // Jika ini adalah level 2 atau tidak ada level 2, update status vehicle menjadi approved
        return $vehicleModel->update($approval['vehicle_id'], ['status' => 'approved']);
    }

    public function reject($id, $approverId, $notes = null)
    {
        $approval = $this->find($id);
        if (!$approval) {
            return false;
        }

        // Update status approval
        $this->update($id, [
            'status' => 'rejected',
            'notes' => $notes ?? 'Ditolak'
        ]);

        // Ambil data vehicle dan approver
        $vehicleModel = new VehicleModel();
        $vehicle = $vehicleModel->find($approval['vehicle_id']);
        $approver = $this->userModel->find($approverId);

        // Buat report baru untuk rejection
        $reportModel = new ReportModel();
        $reportData = [
            'vehicle_id' => $approval['vehicle_id'],
            'created_by' => $approverId,
            'notes' => sprintf(
                'Kendaraan %s (%s) ditolak oleh %s (Level %d). Catatan: %s',
                $vehicle['vehicle_type'],
                $vehicle['vehicle_number'],
                $approver['name'],
                $approval['level'],
                $notes ?? 'Tidak ada catatan'
            ),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $reportModel->insert($reportData);

        // Update status vehicle menjadi rejected
        return $vehicleModel->update($approval['vehicle_id'], ['status' => 'rejected']);
    }

    public function createApprovalChain($vehicleId, $approverIds)
    {
        if (count($approverIds) < 2) {
            return false;
        }

        // Ambil data vehicle untuk mendapatkan created_by
        $vehicleModel = new VehicleModel();
        $vehicle = $vehicleModel->find($vehicleId);
        
        if (!$vehicle) {
            return false;
        }

        $currentTime = date('Y-m-d H:i:s');

        // Hapus approval yang ada untuk vehicle ini
        $this->where('vehicle_id', $vehicleId)->delete();

        // Buat approval untuk level 1
        $this->insert([
            'vehicle_id' => $vehicleId,
            'approver_id' => $approverIds[0],
            'level' => 1,
            'status' => 'pending',
            'notes' => 'Menunggu persetujuan level 1',
            'created_at' => $currentTime,
            'updated_at' => $currentTime
        ]);

        // Buat approval untuk level 2
        $this->insert([
            'vehicle_id' => $vehicleId,
            'approver_id' => $approverIds[1],
            'level' => 2,
            'status' => 'pending',
            'notes' => 'Menunggu persetujuan level 2',
            'created_at' => $currentTime,
            'updated_at' => $currentTime
        ]);

        // Buat report baru
        $reportModel = new ReportModel();
        $reportData = [
            'vehicle_id' => $vehicleId,
            'created_by' => $vehicle['created_by'],
            'notes' => sprintf(
                'Report otomatis dibuat untuk kendaraan %s (%s). Status: Pending. Approver Level 1: %s, Approver Level 2: %s',
                $vehicle['vehicle_type'],
                $vehicle['vehicle_number'],
                $this->userModel->find($approverIds[0])['name'],
                $this->userModel->find($approverIds[1])['name']
            ),
            'created_at' => $currentTime,
            'updated_at' => $currentTime
        ];
        $reportModel->insert($reportData);

        // Update status vehicle menjadi pending
        $vehicleModel->update($vehicleId, ['status' => 'pending']);

        return true;
    }

    public function getNextApprovalLevel($vehicleId)
    {
        $currentApproval = $this->where('vehicle_id', $vehicleId)
                              ->where('status', 'pending')
                              ->orderBy('level', 'ASC')
                              ->first();
        
        return $currentApproval ? $currentApproval['level'] : null;
    }

    public function isAllLevelsApproved($vehicleId)
    {
        $totalLevels = $this->where('vehicle_id', $vehicleId)->countAllResults();
        $approvedLevels = $this->where('vehicle_id', $vehicleId)
                             ->where('status', 'approved')
                             ->countAllResults();

        return $totalLevels > 0 && $totalLevels === $approvedLevels;
    }

    public function hasRejectedApproval($vehicleId)
    {
        return $this->where('vehicle_id', $vehicleId)
                   ->where('status', 'rejected')
                   ->countAllResults() > 0;
    }

    public function getRecentApprovals($approverId, $limit = 5)
    {
        return $this->select('approvals.*, users.name as approver_name, vehicles.*')
                    ->join('users', 'users.id = approvals.approver_id')
                    ->join('vehicles', 'vehicles.id = approvals.vehicle_id')
                    ->where('approvals.approver_id', $approverId)
                    ->orderBy('approvals.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getApprovalStats($approverId)
    {
        return [
            'pending' => $this->where('approver_id', $approverId)
                             ->where('status', 'pending')
                             ->countAllResults()
        ];
    }

    public function getApprovalsForUser($userId)
    {
        return $this->select('approvals.*, vehicles.vehicle_type, vehicles.vehicle_number, vehicles.start_date, vehicles.end_date, vehicles.purpose, drivers.name as driver_name, users.name as creator_name')
            ->join('vehicles', 'vehicles.id = approvals.vehicle_id')
            ->join('drivers', 'drivers.id = vehicles.driver_id', 'left')
            ->join('users', 'users.id = vehicles.created_by')
            ->where('approvals.approver_id', $userId)
            ->orderBy('approvals.created_at', 'DESC')
            ->findAll();
    }

    public function getApprovalWithDetails($id)
    {
        return $this->select('approvals.*, vehicles.vehicle_type, vehicles.vehicle_number, vehicles.start_date, vehicles.end_date, vehicles.purpose, drivers.name as driver_name, users.name as creator_name')
            ->join('vehicles', 'vehicles.id = approvals.vehicle_id')
            ->join('drivers', 'drivers.id = vehicles.driver_id', 'left')
            ->join('users', 'users.id = vehicles.created_by')
            ->where('approvals.id', $id)
            ->first();
    }
} 