<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Tentukan Approver</h1>
        <a href="<?= base_url('admin/vehicles') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Kendaraan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tipe Kendaraan:</strong> <?= $vehicle['vehicle_type'] ?></p>
                    <p><strong>Nomor Kendaraan:</strong> <?= $vehicle['vehicle_number'] ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> 
                        <span class="badge badge-<?= $vehicle['status'] === 'available' ? 'success' : ($vehicle['status'] === 'pending' ? 'warning' : 'primary') ?>">
                            <?= ucfirst($vehicle['status']) ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Approver Saat Ini</h6>
        </div>
        <div class="card-body">
            <?php if (empty($existingApprovals)): ?>
                <p class="text-muted">Belum ada approver yang ditetapkan</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>Nama Approver</th>
                                <th>Status</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($existingApprovals as $approval): ?>
                            <tr>
                                <td>Level <?= $approval['level'] ?></td>
                                <td><?= $approval['approver_name'] ?></td>
                                <td>
                                    <span class="badge badge-<?= $approval['status'] === 'approved' ? 'success' : ($approval['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst($approval['status']) ?>
                                    </span>
                                </td>
                                <td><?= $approval['notes'] ?? '-' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tentukan Approver Baru</h6>
        </div>
        <div class="card-body">
            <form action="<?= base_url('admin/vehicles/' . $vehicle['id'] . '/assign-approvers') ?>" method="POST">
                <div class="mb-3">
                    <label class="form-label">Approver Level 1</label>
                    <select class="form-select" name="approvers[]" required>
                        <option value="">Pilih Approver Level 1</option>
                        <?php foreach ($approvers as $approver): ?>
                        <option value="<?= $approver['id'] ?>"><?= $approver['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Approver Level 2</label>
                    <select class="form-select" name="approvers[]" required>
                        <option value="">Pilih Approver Level 2</option>
                        <?php foreach ($approvers as $approver): ?>
                        <option value="<?= $approver['id'] ?>"><?= $approver['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Mencegah memilih approver yang sama untuk kedua level
    $('select[name="approvers[]"]').change(function() {
        var selectedValues = [];
        $('select[name="approvers[]"]').each(function() {
            if ($(this).val()) {
                selectedValues.push($(this).val());
            }
        });

        $('select[name="approvers[]"]').each(function() {
            var currentValue = $(this).val();
            $(this).find('option').each(function() {
                if ($(this).val() && $(this).val() !== currentValue) {
                    if (selectedValues.includes($(this).val())) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?> 