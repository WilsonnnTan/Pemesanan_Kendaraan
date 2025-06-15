<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;
use Ramsey\Uuid\Uuid;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Seed Users (Admin dan Approvers)
        $users = [
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Admin',
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'level' => null,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Approver Level 1',
                'username' => 'approver1',
                'password' => password_hash('approver123', PASSWORD_DEFAULT),
                'role' => 'approver',
                'level' => 1,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Approver Level 2',
                'username' => 'approver2',
                'password' => password_hash('approver123', PASSWORD_DEFAULT),
                'role' => 'approver',
                'level' => 2,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];

        $this->db->table('users')->insertBatch($users);

        // Get admin ID for vehicle creation
        $adminId = $users[0]['id'];

        // Seed Drivers
        $drivers = [
            [
                'name' => 'Budi Santoso',
                'license_number' => 'SIM-A-123456',
                'phone_number' => '081234567890',
                'status' => 'active',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Ahmad Rizki',
                'license_number' => 'SIM-A-234567',
                'phone_number' => '081234567891',
                'status' => 'active',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Dewi Putri',
                'license_number' => 'SIM-A-345678',
                'phone_number' => '081234567892',
                'status' => 'active',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Rudi Hermawan',
                'license_number' => 'SIM-A-456789',
                'phone_number' => '081234567893',
                'status' => 'active',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'name' => 'Siti Aminah',
                'license_number' => 'SIM-A-567890',
                'phone_number' => '081234567894',
                'status' => 'active',
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];

        $this->db->table('drivers')->insertBatch($drivers);

        // Seed Vehicles (without assigned drivers)
        $vehicles = [
            [
                'vehicle_type' => 'Toyota Avanza',
                'vehicle_number' => 'B 1234 ABC',
                'driver_id' => null,
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 year')),
                'purpose' => 'Kendaraan operasional',
                'status' => 'available',
                'created_by' => $adminId,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'vehicle_type' => 'Honda Brio',
                'vehicle_number' => 'B 2345 DEF',
                'driver_id' => null,
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 year')),
                'purpose' => 'Kendaraan operasional',
                'status' => 'available',
                'created_by' => $adminId,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'vehicle_type' => 'Suzuki Ertiga',
                'vehicle_number' => 'B 3456 GHI',
                'driver_id' => null,
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 year')),
                'purpose' => 'Kendaraan operasional',
                'status' => 'available',
                'created_by' => $adminId,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'vehicle_type' => 'Daihatsu Xenia',
                'vehicle_number' => 'B 4567 JKL',
                'driver_id' => null,
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 year')),
                'purpose' => 'Kendaraan operasional',
                'status' => 'available',
                'created_by' => $adminId,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ],
            [
                'vehicle_type' => 'Mitsubishi Xpander',
                'vehicle_number' => 'B 5678 MNO',
                'driver_id' => null,
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 year')),
                'purpose' => 'Kendaraan operasional',
                'status' => 'available',
                'created_by' => $adminId,
                'created_at' => Time::now(),
                'updated_at' => Time::now()
            ]
        ];

        $this->db->table('vehicles')->insertBatch($vehicles);
    }
} 