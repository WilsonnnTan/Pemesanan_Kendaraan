<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4">Laporan Kendaraan</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan</h6>
            <div>
                <a href="<?= base_url('report/export/excel') ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="<?= base_url('report/export/pdf') ?>" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reportTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tipe Kendaraan</th>
                            <th>Nomor Kendaraan</th>
                            <th>Driver</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Tujuan</th>
                            <th>Status Approval</th>
                            <th>Catatan</th>
                            <th>Dibuat Oleh</th>
                            <th>Tanggal Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $index => $report): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $report['vehicle_type'] ?></td>
                            <td><?= $report['vehicle_number'] ?></td>
                            <td><?= $report['driver_name'] ?? '-' ?></td>
                            <td><?= date('d/m/Y', strtotime($report['start_date'])) ?></td>
                            <td><?= date('d/m/Y', strtotime($report['end_date'])) ?></td>
                            <td><?= $report['purpose'] ?></td>
                            <td>
                                <?php
                                $statusMap = [
                                    'approved' => ['class' => 'success', 'text' => 'Disetujui'],
                                    'rejected' => ['class' => 'danger', 'text' => 'Ditolak'],
                                    'pending_level_1' => ['class' => 'warning', 'text' => 'Menunggu Level 1'],
                                    'pending_level_2' => ['class' => 'info', 'text' => 'Menunggu Level 2'],
                                    'pending' => ['class' => 'warning', 'text' => 'Menunggu Persetujuan']
                                ];

                                $approvalStatus = $report['approval_status'] ?? $report['status'];
                                $status = $statusMap[$approvalStatus] ?? ['class' => 'secondary', 'text' => 'Tidak Diketahui'];
                                ?>
                                <span class="badge bg-<?= $status['class'] ?>"><?= $status['text'] ?></span>
                            </td>
                            <td><?= $report['notes'] ?></td>
                            <td><?= $report['creator_name'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
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
    $('#reportTable').DataTable();
});
</script>
<?= $this->endSection() ?> 