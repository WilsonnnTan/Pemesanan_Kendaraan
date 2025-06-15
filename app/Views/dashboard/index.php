<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4">Dashboard</h1>

    <div class="row">
        <!-- Total Kendaraan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Kendaraan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalVehicles ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-car fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kendaraan Tersedia -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Kendaraan Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $availableVehicles ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kendaraan Pending -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Kendaraan Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $pendingVehicles ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($user['role'] === 'admin'): ?>
    <!-- Statistik Kendaraan untuk Admin -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Kendaraan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total</td>
                                    <td><?= $vehiclesByStatus['total'] ?></td>
                                </tr>
                                <tr>
                                    <td>Tersedia</td>
                                    <td><?= $vehiclesByStatus['available'] ?></td>
                                </tr>
                                <tr>
                                    <td>Pending</td>
                                    <td><?= $vehiclesByStatus['pending'] ?></td>
                                </tr>
                                <tr>
                                    <td>Sedang Digunakan</td>
                                    <td><?= $vehiclesByStatus['in_use'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($user['role'] === 'approver'): ?>
    <!-- Statistik Persetujuan untuk Approver -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Persetujuan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Pending</td>
                                    <td><?= $approvalStats['pending'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($user['role'] === 'user'): ?>
    <!-- Daftar Kendaraan User -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Kendaraan Saya</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tipe Kendaraan</th>
                                    <th>Nomor Kendaraan</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userVehicles as $vehicle): ?>
                                <tr>
                                    <td><?= $vehicle['vehicle_type'] ?></td>
                                    <td><?= $vehicle['vehicle_number'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= $vehicle['status'] === 'available' ? 'success' : ($vehicle['status'] === 'pending' ? 'warning' : 'primary') ?>">
                                            <?= ucfirst($vehicle['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($vehicle['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>