<?php

namespace App\Controllers;

use App\Models\VehicleModel;
use App\Models\ApprovalModel;
use App\Models\UserModel;
use App\Models\SessionModel;

class Approval extends BaseController
{
    protected $vehicleModel;
    protected $approvalModel;
    protected $userModel;
    protected $sessionModel;

    public function __construct()
    {
        $this->vehicleModel = new VehicleModel();
        $this->approvalModel = new ApprovalModel();
        $this->userModel = new UserModel();
        $this->sessionModel = new SessionModel();
    }

    public function index()
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->to('auth/login');
        }

        // Ambil semua approval yang diassign ke user ini
        $approvals = $this->approvalModel->select('
                approvals.*,
                vehicles.vehicle_type,
                vehicles.vehicle_number,
                vehicles.start_date,
                vehicles.end_date,
                vehicles.purpose,
                drivers.name as driver_name,
                users.name as creator_name
            ')
            ->join('vehicles', 'vehicles.id = approvals.vehicle_id')
            ->join('drivers', 'drivers.id = vehicles.driver_id', 'left')
            ->join('users', 'users.id = vehicles.created_by')
            ->where('approvals.approver_id', $user['id'])
            ->orderBy('approvals.created_at', 'DESC')
            ->findAll();

        return view('approval/index', [
            'approvals' => $approvals
        ]);
    }

    public function show($id)
    {
        // Cek role
        $user = $this->getCurrentUser();
        if (!in_array($user['role'], ['admin', 'approver'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $approval = $this->approvalModel->getApprovalWithDetails($id);
        if (!$approval) {
            return redirect()->to('/approval')->with('error', 'Persetujuan tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Persetujuan',
            'approval' => $approval
        ];

        return $this->renderView('approval/show', $data);
    }

    public function approve($id)
    {
        if (!$this->request->is('POST')) {
            return redirect()->to('approval');
        }

        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->to('auth/login');
        }

        $approval = $this->approvalModel->find($id);
        if (!$approval) {
            return redirect()->to('approval')->with('error', 'Persetujuan tidak ditemukan');
        }

        // Validasi apakah user adalah approver yang ditunjuk
        if ($approval['approver_id'] !== $user['id']) {
            return redirect()->to('approval')->with('error', 'Anda tidak memiliki akses untuk menyetujui persetujuan ini');
        }

        // Update status approval
        $this->approvalModel->update($id, [
            'status' => 'approved',
            'notes' => $this->request->getPost('notes'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Cek apakah ini approval terakhir
        $vehicleApprovals = $this->approvalModel->where('vehicle_id', $approval['vehicle_id'])->findAll();
        $allApproved = true;
        foreach ($vehicleApprovals as $vehicleApproval) {
            if ($vehicleApproval['status'] !== 'approved') {
                $allApproved = false;
                break;
            }
        }

        // Jika semua level sudah disetujui, update status kendaraan menjadi approved
        if ($allApproved) {
            $this->vehicleModel->update($approval['vehicle_id'], [
                'status' => 'approved'
            ]);
        } else {
            // Jika belum semua level disetujui, status tetap pending
            $this->vehicleModel->update($approval['vehicle_id'], [
                'status' => 'pending'
            ]);
        }

        return redirect()->to('approval')->with('success', 'Persetujuan berhasil disetujui');
    }

    public function reject($id)
    {
        if (!$this->request->is('POST')) {
            return redirect()->to('approval');
        }

        $user = $this->getCurrentUser();
        if (!$user) {
            return redirect()->to('auth/login');
        }

        $approval = $this->approvalModel->find($id);
        if (!$approval) {
            return redirect()->to('approval')->with('error', 'Persetujuan tidak ditemukan');
        }

        // Validasi apakah user adalah approver yang ditunjuk
        if ($approval['approver_id'] !== $user['id']) {
            return redirect()->to('approval')->with('error', 'Anda tidak memiliki akses untuk menolak persetujuan ini');
        }

        // Update status approval
        $this->approvalModel->update($id, [
            'status' => 'rejected',
            'notes' => $this->request->getPost('notes'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Update status kendaraan menjadi rejected
        $this->vehicleModel->update($approval['vehicle_id'], [
            'status' => 'rejected'
        ]);

        return redirect()->to('approval')->with('success', 'Persetujuan berhasil ditolak');
    }
} 