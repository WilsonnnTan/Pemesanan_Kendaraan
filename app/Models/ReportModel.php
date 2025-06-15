<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vehicle_id', 'notes', 'created_by', 'created_at', 'updated_at'];
    
    // Konfigurasi timestamps
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $dateFormat = 'datetime';

    public function getVehicleReport()
    {
        // Ambil semua report
        $reports = $this->select('
                reports.*,
                vehicles.vehicle_type,
                vehicles.vehicle_number,
                vehicles.start_date,
                vehicles.end_date,
                vehicles.purpose,
                vehicles.status as vehicle_status,
                drivers.name as driver_name,
                users.name as creator_name
            ')
            ->join('vehicles', 'vehicles.id = reports.vehicle_id')
            ->join('drivers', 'drivers.id = vehicles.driver_id', 'left')
            ->join('users', 'users.id = reports.created_by')
            ->groupBy('reports.id')
            ->orderBy('reports.created_at', 'DESC')
            ->findAll();

        // Update status untuk setiap report berdasarkan approval
        foreach ($reports as &$report) {
            // Ambil semua approval untuk vehicle ini
            $approvals = $this->db->table('approvals')
                ->where('vehicle_id', $report['vehicle_id'])
                ->get()
                ->getResultArray();

            $hasLevel1Approval = false;
            $hasLevel2Approval = false;
            $hasRejection = false;

            foreach ($approvals as $approval) {
                if ($approval['status'] === 'rejected') {
                    $hasRejection = true;
                    break;
                }
                if ($approval['level'] === 1 && $approval['status'] === 'approved') {
                    $hasLevel1Approval = true;
                }
                if ($approval['level'] === 2 && $approval['status'] === 'approved') {
                    $hasLevel2Approval = true;
                }
            }

            // Tentukan status berdasarkan approval
            if ($hasRejection) {
                $report['status'] = 'rejected';
                // Update status vehicle
                $this->db->table('vehicles')
                    ->where('id', $report['vehicle_id'])
                    ->update(['status' => 'rejected']);
            } elseif ($hasLevel1Approval && $hasLevel2Approval) {
                $report['status'] = 'approved';
                // Update status vehicle
                $this->db->table('vehicles')
                    ->where('id', $report['vehicle_id'])
                    ->update(['status' => 'approved']);
            } elseif ($hasLevel1Approval) {
                $report['status'] = 'pending_level_2';
            } else {
                $report['status'] = 'pending_level_1';
            }
        }

        return $reports;
    }

    public function getVehicleStatus($vehicleId)
    {
        // Ambil semua approval untuk vehicle ini, diurutkan berdasarkan level
        $approvals = $this->db->table('approvals')
            ->where('vehicle_id', $vehicleId)
            ->orderBy('level', 'ASC')
            ->get()
            ->getResultArray();

        // Jika tidak ada approval sama sekali, kembalikan status pending level 1
        if (empty($approvals)) {
            // Update status di database menjadi pending
            $this->updateVehicleStatus($vehicleId, 'pending');
            return 'pending_level_1';
        }

        // Cek apakah ada yang reject
        foreach ($approvals as $approval) {
            if ($approval['status'] === 'rejected') {
                $this->updateVehicleStatus($vehicleId, 'rejected');
                return 'rejected';
            }
        }

        // Hitung jumlah approval untuk setiap level
        $level1Approved = false;
        $level2Approved = false;

        foreach ($approvals as $approval) {
            // Debug: Tampilkan setiap approval
            
            if ($approval['level'] == 1 && $approval['status'] === 'approved') {
                $level1Approved = true;
            }
            if ($approval['level'] == 2 && $approval['status'] === 'approved') {
                $level2Approved = true;
            }
        }

        // Tentukan status berdasarkan approval
        if ($level1Approved && $level2Approved) {
            $this->updateVehicleStatus($vehicleId, 'approved');
            return 'approved';
        } elseif ($level1Approved) {
            $this->updateVehicleStatus($vehicleId, 'pending');
            return 'pending_level_2';
        } else {
            $this->updateVehicleStatus($vehicleId, 'pending');
            return 'pending_level_1';
        }
    }

    public function updateVehicleStatus($vehicleId, $status)
    {
        // Update status di tabel vehicles
        $this->db->table('vehicles')
            ->where('id', $vehicleId)
            ->update(['status' => $status]);
    }

    private function getStatusText($status)
    {
        $statusMap = [
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'pending_level_1' => 'Menunggu Level 1',
            'pending_level_2' => 'Menunggu Level 2',
            'pending' => 'Menunggu Persetujuan'
        ];

        return $statusMap[$status] ?? 'Tidak Diketahui';
    }
} 