<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\VehicleModel;
use App\Models\DriverModel;
use App\Models\ApprovalModel;

class Admin extends BaseController
{
    protected $userModel;
    protected $vehicleModel;
    protected $driverModel;
    protected $approvalModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->vehicleModel = new VehicleModel();
        $this->driverModel = new DriverModel();
        $this->approvalModel = new ApprovalModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Admin Dashboard',
            'totalVehicles' => $this->vehicleModel->countAll(),
            'availableVehicles' => $this->vehicleModel->where('status', 'available')->countAllResults(),
            'inUseVehicles' => $this->vehicleModel->where('status', 'in_use')->countAllResults(),
            'maintenanceVehicles' => $this->vehicleModel->where('status', 'maintenance')->countAllResults()
        ];

        return view('admin/dashboard', $data);
    }

    public function users()
    {
        $data = [
            'title' => 'User Management',
            'users' => $this->userModel->findAll()
        ];

        return view('admin/users', $data);
    }

    public function getUsers()
    {
        $draw = $this->request->getGet('draw');
        $start = $this->request->getGet('start');
        $length = $this->request->getGet('length');
        $search = $this->request->getGet('search')['value'];

        $total = $this->userModel->countAll();
        $totalFiltered = $total;

        if (!empty($search)) {
            $this->userModel->groupStart()
                ->like('name', $search)
                ->orLike('username', $search)
                ->orLike('role', $search)
                ->groupEnd();
            $totalFiltered = $this->userModel->countAllResults(false);
        }

        $users = $this->userModel->select('id, name, username, role, level, created_at')
            ->limit($length, $start)
            ->find();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'name' => $user['name'],
                'username' => $user['username'],
                'role' => ucfirst($user['role']),
                'level' => $user['level'],
                'created_at' => date('Y-m-d H:i:s', strtotime($user['created_at'])),
                'id' => $user['id']
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }
    public function getUser($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        return $this->response->setJSON($user);
    }

    public function vehicles()
    {
        $vehicles = $this->vehicleModel->findAll();
        $users = $this->userModel->findAll();
        $drivers = $this->driverModel->findAll();

        // Buat mapping untuk user names
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user['id']] = $user['name'];
        }

        // Buat mapping untuk driver names
        $driverMap = [];
        foreach ($drivers as $driver) {
            $driverMap[$driver['id']] = $driver['name'];
        }

        // Tambahkan creator name dan driver name ke setiap vehicle
        foreach ($vehicles as &$vehicle) {
            $vehicle['creator_name'] = $userMap[$vehicle['created_by']] ?? '-';
            $vehicle['driver_name'] = $driverMap[$vehicle['driver_id']] ?? '-';
        }

        return view('admin/vehicles', [
            'vehicles' => $vehicles,
            'drivers' => $drivers
        ]);
    }

    public function getVehicles()
    {
        $draw = $this->request->getGet('draw');
        $start = $this->request->getGet('start');
        $length = $this->request->getGet('length');
        $search = $this->request->getGet('search')['value'];

        $total = $this->vehicleModel->countAll();
        $totalFiltered = $total;

        if (!empty($search)) {
            $this->vehicleModel->groupStart()
                ->like('vehicle_number', $search)
                ->orLike('type', $search)
                ->orLike('status', $search)
                ->groupEnd();
            $totalFiltered = $this->vehicleModel->countAllResults(false);
        }

        $vehicles = $this->vehicleModel->select('id, vehicle_number, type, status, last_maintenance')
            ->limit($length, $start)
            ->find();

        $data = [];
        foreach ($vehicles as $vehicle) {
            $data[] = [
                'id' => $vehicle['id'],
                'vehicle_number' => $vehicle['vehicle_number'],
                'type' => $vehicle['type'],
                'status' => $vehicle['status'],
                'last_maintenance' => $vehicle['last_maintenance']
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function addVehicle()
    {
        if ($this->request->getMethod() === 'POST') {
            $user = $this->getCurrentUser();
            if (!$user) {
                return redirect()->back()->with('error', 'Sesi anda telah berakhir. Silakan login kembali.');
            }

            $data = [
                'vehicle_type' => $this->request->getPost('vehicle_type'),
                'vehicle_number' => $this->request->getPost('vehicle_number'),
                'driver_id' => $this->request->getPost('driver_id') ?: null,
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'purpose' => $this->request->getPost('purpose'),
                'status' => $this->request->getPost('status'),
                'created_by' => $user['id']
            ];

            if ($this->vehicleModel->insert($data)) {
                return redirect()->to('admin/vehicles')->with('success', 'Kendaraan berhasil ditambahkan');
            }

            return redirect()->back()->with('error', 'Gagal menambahkan kendaraan');
        }

        return redirect()->back();
    }

    public function deleteVehicle($id)
    {
        if ($this->vehicleModel->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false]);
    }

    public function getDrivers()
    {
        $drivers = $this->userModel->where('role', 'user')->findAll();
        return $this->response->setJSON($drivers);
    }

    public function getApprovers()
    {
        $approvers = $this->userModel->where('role', 'approver')->findAll();
        return $this->response->setJSON($approvers);
    }

    public function bookVehicle()
    {
        $rules = [
            'vehicle_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'approver_id' => 'required|integer',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'purpose' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $this->validator->getErrors()
            ]);
        }

        // Check if vehicle is available
        $vehicle = $this->vehicleModel->find($this->request->getPost('vehicle_id'));
        if (!$vehicle || $vehicle['status'] !== 'available') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vehicle is not available'
            ]);
        }

        // Check if dates are valid
        $startDate = strtotime($this->request->getPost('start_date'));
        $endDate = strtotime($this->request->getPost('end_date'));
        if ($startDate >= $endDate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'End date must be after start date'
            ]);
        }

        // Check if vehicle is already booked for these dates
        $existingBooking = $this->vehicleRequestModel->where('vehicle_id', $this->request->getPost('vehicle_id'))
            ->where('status !=', 'rejected')
            ->where('(start_date <= ? AND end_date >= ?) OR (start_date <= ? AND end_date >= ?) OR (start_date >= ? AND end_date <= ?)',
                [$endDate, $endDate, $startDate, $startDate, $startDate, $endDate])
            ->first();

        if ($existingBooking) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Vehicle is already booked for these dates'
            ]);
        }

        $data = [
            'vehicle_id' => $this->request->getPost('vehicle_id'),
            'driver_id' => $this->request->getPost('driver_id'),
            'approver_id' => $this->request->getPost('approver_id'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'purpose' => $this->request->getPost('purpose'),
            'status' => 'pending'
        ];

        if ($this->vehicleRequestModel->insert($data)) {
            // Update vehicle status to in_use
            $this->vehicleModel->update($this->request->getPost('vehicle_id'), ['status' => 'in_use']);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to book vehicle']);
    }

    public function reports()
    {
        $data = [
            'title' => 'Reports',
            'totalVehicles' => $this->vehicleModel->countAll(),
            'availableVehicles' => $this->vehicleModel->where('status', 'available')->countAllResults(),
            'inUseVehicles' => $this->vehicleModel->where('status', 'in_use')->countAllResults(),
            'maintenanceVehicles' => $this->vehicleModel->where('status', 'maintenance')->countAllResults()
        ];

        return view('admin/reports', $data);
    }

    public function assignApprovers($vehicleId)
    {
        if ($this->request->getMethod() === 'POST') {
            $user = $this->getCurrentUser();
            if (!$user) {
                return redirect()->back()->with('error', 'Sesi anda telah berakhir. Silakan login kembali.');
            }

            $approverIds = $this->request->getPost('approvers');
            if (count($approverIds) < 2) {
                return redirect()->back()->with('error', 'Harus memilih minimal 2 approver');
            }

            // Cek apakah vehicle ada
            $vehicle = $this->vehicleModel->find($vehicleId);
            if (!$vehicle) {
                return redirect()->back()->with('error', 'Kendaraan tidak ditemukan');
            }

            // Cek apakah user yang dipilih adalah approver
            foreach ($approverIds as $approverId) {
                $approver = $this->userModel->find($approverId);
                if (!$approver || $approver['role'] !== 'approver') {
                    return redirect()->back()->with('error', 'User yang dipilih harus memiliki role approver');
                }
            }

            // Buat approval chain
            if ($this->approvalModel->createApprovalChain($vehicleId, $approverIds)) {
                return redirect()->back()->with('success', 'Approver berhasil ditetapkan');
            }

            return redirect()->back()->with('error', 'Gagal menetapkan approver');
        }

        // Ambil semua user dengan role approver
        $approvers = $this->userModel->where('role', 'approver')->findAll();
        
        // Ambil data vehicle
        $vehicle = $this->vehicleModel->find($vehicleId);
        if (!$vehicle) {
            return redirect()->back()->with('error', 'Kendaraan tidak ditemukan');
        }

        // Ambil data approval yang sudah ada
        $existingApprovals = $this->approvalModel->getApprovalsByVehicle($vehicleId);

        return view('admin/assign_approvers', [
            'title' => 'Tentukan Approver',
            'vehicle' => $vehicle,
            'approvers' => $approvers,
            'existingApprovals' => $existingApprovals
        ]);
    }
} 