<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Vehicle Management</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            <i class="fas fa-plus"></i> Tambah Kendaraan
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Kendaraan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="vehiclesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tipe Kendaraan</th>
                            <th>Nomor Kendaraan</th>
                            <th>Driver</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Tujuan</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th>Tanggal Dibuat</th>
                            <th>Terakhir Diupdate</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                        <tr>
                            <td><?= $vehicle['vehicle_type'] ?></td>
                            <td><?= $vehicle['vehicle_number'] ?></td>
                            <td><?= $vehicle['driver_name'] ?? '-' ?></td>
                            <td><?= date('d/m/Y', strtotime($vehicle['start_date'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($vehicle['end_date'])) ?></td>
                            <td><?= $vehicle['purpose'] ?></td>
                            <td>
                                <span class="badge badge-<?= $vehicle['status'] === 'available' ? 'success' : ($vehicle['status'] === 'pending' ? 'warning' : 'primary') ?>">
                                    <?= ucfirst($vehicle['status']) ?>
                                </span>
                            </td>
                            <td><?= $vehicle['creator_name'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($vehicle['created_at'])) ?></td>
                            <td><?= $vehicle['updated_at'] ? date('d/m/Y H:i', strtotime($vehicle['updated_at'])) : '-' ?></td>
                            <td>
                                <a href="<?= base_url('admin/vehicles/' . $vehicle['id'] . '/assign-approvers') ?>" class="btn btn-info btn-sm">
                                    <i class="fas fa-user-check"></i> Assign Approver
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kendaraan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/vehicles/add') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tipe Kendaraan</label>
                        <select class="form-select" name="vehicle_type" required>
                            <option value="">Pilih Tipe Kendaraan</option>
                            <option value="MPV">MPV</option>
                            <option value="City Car">City Car</option>
                            <option value="SUV">SUV</option>
                            <option value="Pickup">Pickup</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor Kendaraan</label>
                        <input type="text" class="form-control" name="vehicle_number" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Driver</label>
                        <select class="form-select" name="driver_id">
                            <option value="">Pilih Driver</option>
                            <?php foreach ($drivers as $driver): ?>
                            <option value="<?= $driver['id'] ?>"><?= $driver['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tujuan</label>
                        <textarea class="form-control" name="purpose" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="available">Available</option>
                            <option value="pending">Pending</option>
                            <option value="in_use">In Use</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Book Vehicle Modal -->
<div class="modal fade" id="bookVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Book Vehicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bookVehicleForm" action="<?= base_url('admin/vehicles/book') ?>" method="POST">
                <input type="hidden" name="vehicle_id" id="bookVehicleId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Vehicle</label>
                        <input type="text" class="form-control" id="bookVehicleNumber" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Driver</label>
                        <select class="form-select" name="driver_id" required>
                            <option value="">Select Driver</option>
                            <!-- Will be populated dynamically -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Approver</label>
                        <select class="form-select" name="approver_id" required>
                            <option value="">Select Approver</option>
                            <!-- Will be populated dynamically -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="datetime-local" class="form-control" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">End Date</label>
                        <input type="datetime-local" class="form-control" name="end_date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <textarea class="form-control" name="purpose" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Book Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#vehiclesTable').DataTable();

    // Load drivers and approvers when booking modal opens
    $('#bookVehicleModal').on('show.bs.modal', function(e) {
        const button = $(e.relatedTarget);
        const vehicleId = button.data('id');
        const vehicleNumber = button.data('number');
        
        $('#bookVehicleId').val(vehicleId);
        $('#bookVehicleNumber').val(vehicleNumber);
        
        // Load drivers
        $.get('<?= base_url('admin/users/drivers') ?>', function(data) {
            const driverSelect = $('select[name="driver_id"]');
            driverSelect.empty().append('<option value="">Select Driver</option>');
            data.forEach(driver => {
                driverSelect.append(`<option value="${driver.id}">${driver.name}</option>`);
            });
        });
        
        // Load approvers
        $.get('<?= base_url('admin/users/approvers') ?>', function(data) {
            const approverSelect = $('select[name="approver_id"]');
            approverSelect.empty().append('<option value="">Select Approver</option>');
            data.forEach(approver => {
                approverSelect.append(`<option value="${approver.id}">${approver.name} (Level ${approver.level})</option>`);
            });
        });
    });

    // Handle form submissions
    $('#addVehicleForm').on('submit', function(e) {
        e.preventDefault();
        $.post($(this).attr('action'), $(this).serialize(), function(response) {
            if (response.success) {
                $('#addVehicleModal').modal('hide');
                $('#vehiclesTable').DataTable().ajax.reload();
                $('#addVehicleForm')[0].reset();
            } else {
                alert('Failed to add vehicle');
            }
        });
    });

    $('#bookVehicleForm').on('submit', function(e) {
        e.preventDefault();
        $.post($(this).attr('action'), $(this).serialize(), function(response) {
            if (response.success) {
                $('#bookVehicleModal').modal('hide');
                $('#vehiclesTable').DataTable().ajax.reload();
                $('#bookVehicleForm')[0].reset();
            } else {
                alert('Failed to book vehicle');
            }
        });
    });

    // Handle delete vehicle
    $(document).on('click', '.delete-vehicle', function() {
        const vehicleId = $(this).data('id');
        if (confirm('Are you sure you want to delete this vehicle?')) {
            $.post(`<?= base_url('admin/vehicles/delete/') ?>${vehicleId}`, function(response) {
                if (response.success) {
                    $('#vehiclesTable').DataTable().ajax.reload();
                } else {
                    alert('Failed to delete vehicle');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?> 