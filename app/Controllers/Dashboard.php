<?php
namespace App\Controllers;

use App\Models\VehicleModel;
use App\Models\ApprovalModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $vehicleModel;
    protected $approvalModel;
    protected $userModel;

    public function __construct()
    {
        $this->vehicleModel = new VehicleModel();
        $this->approvalModel = new ApprovalModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $user = $this->getCurrentUser();
        
        // Data dasar untuk semua role
        $data = [
            'title' => 'Dashboard',
            'user' => $user,
            'totalVehicles' => $this->vehicleModel->countAll(),
            'availableVehicles' => $this->vehicleModel->where('status', 'available')->countAllResults(),
            'pendingVehicles' => $this->vehicleModel->where('status', 'pending')->countAllResults()
        ];

        // Jika admin, tampilkan semua statistik
        if ($user['role'] === 'admin') {
            $data['vehiclesByStatus'] = [
                'total' => $this->vehicleModel->countAll(),
                'available' => $this->vehicleModel->where('status', 'available')->countAllResults(),
                'pending' => $this->vehicleModel->where('status', 'pending')->countAllResults(),
                'approved' => $this->vehicleModel->where('status', 'approved')->countAllResults(),
                'rejected' => $this->vehicleModel->where('status', 'rejected')->countAllResults(),
                'in_use' => $this->vehicleModel->where('status', 'in_use')->countAllResults()
            ];
        }
        // Jika approver, tampilkan statistik persetujuan
        elseif ($user['role'] === 'approver') {
            $data['approvalStats'] = $this->approvalModel->getApprovalStats($user['id']);
            $data['recentApprovals'] = $this->approvalModel->getRecentApprovals($user['id'], 5);
            $data['pendingApprovals'] = $this->approvalModel->where('approver_id', $user['id'])
                                                           ->where('status', 'pending')
                                                           ->countAllResults();
        }
        // Jika user biasa, tampilkan kendaraan mereka
        else {
            $data['userVehicles'] = $this->vehicleModel->getVehiclesByCreator($user['id']);
            $data['pendingUserVehicles'] = $this->vehicleModel->where('created_by', $user['id'])
                                                             ->where('status', 'pending')
                                                             ->countAllResults();
        }

        return $this->renderView('dashboard/index', $data);
    }
}
?>