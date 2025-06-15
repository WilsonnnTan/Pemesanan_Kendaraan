<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4">Persetujuan Kendaraan</h1>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Persetujuan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="approvalsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Tipe Kendaraan</th>
                            <th>Nomor Kendaraan</th>
                            <th>Driver</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Tujuan</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($approvals as $approval): ?>
                        <tr>
                            <td>Level <?= $approval['level'] ?></td>
                            <td><?= $approval['vehicle_type'] ?></td>
                            <td><?= $approval['vehicle_number'] ?></td>
                            <td><?= $approval['driver_name'] ?? '-' ?></td>
                            <td><?= date('d/m/Y', strtotime($approval['start_date'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($approval['end_date'])) ?></td>
                            <td><?= $approval['purpose'] ?></td>
                            <td>
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                switch ($approval['status']) {
                                    case 'approved':
                                        $statusClass = 'success';
                                        $statusText = 'Disetujui';
                                        break;
                                    case 'rejected':
                                        $statusClass = 'danger';
                                        $statusText = 'Ditolak';
                                        break;
                                    default:
                                        $statusClass = 'warning';
                                        $statusText = 'Menunggu Persetujuan';
                                }
                                ?>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td><?= $approval['creator_name'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($approval['created_at'])) ?></td>
                            <td>
                                <?php if ($approval['status'] === 'pending'): ?>
                                    <button type="button" class="btn btn-success btn-sm" 
                                            onclick="confirmApprove(<?= $approval['id'] ?>)"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#approveModal">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm"
                                            onclick="confirmReject(<?= $approval['id'] ?>)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <?= $approval['status'] === 'approved' ? 'Approved' : 'Rejected' ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Setujui Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approveForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approveNotes">Catatan (Opsional)</label>
                        <textarea class="form-control" id="approveNotes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Tolak Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" action="" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejectNotes">Catatan (Wajib)</label>
                        <textarea class="form-control" id="rejectNotes" name="notes" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmApprove(id) {
    document.getElementById('approveForm').action = `<?= base_url('approval/approve/') ?>/${id}`;
}

function confirmReject(id) {
    document.getElementById('rejectForm').action = `<?= base_url('approval/reject/') ?>/${id}`;
}
</script>
<?= $this->endSection() ?>