<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Reports</h1>
        <div>
            <button class="btn btn-success" id="exportExcel">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
            <button class="btn btn-danger" id="exportPDF">
                <i class="fas fa-file-pdf"></i> Export to PDF
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Vehicles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalVehicles ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-car fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Available Vehicles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $availableVehicles ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                In Use Vehicles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $inUseVehicles ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Maintenance Vehicles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $maintenanceVehicles ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Vehicle Usage Report</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reportsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Vehicle Number</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Last Used By</th>
                            <th>Last Used Date</th>
                            <th>Last Maintenance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#reportsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?= base_url('admin/reports/data') ?>',
        columns: [
            { data: 'vehicle_number' },
            { data: 'type' },
            { data: 'status' },
            { data: 'last_used_by' },
            { data: 'last_used_date' },
            { data: 'last_maintenance' }
        ]
    });

    $('#exportExcel').click(function() {
        window.location.href = '<?= base_url('admin/reports/export/excel') ?>';
    });

    $('#exportPDF').click(function() {
        window.location.href = '<?= base_url('admin/reports/export/pdf') ?>';
    });
});
</script>
<?= $this->endSection() ?> 