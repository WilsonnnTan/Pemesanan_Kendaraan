<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Persetujuan</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table">
                                <tr>
                                    <th width="200">Kendaraan</th>
                                    <td><?= $approval['vehicle_type'] ?> - <?= $approval['vehicle_number'] ?></td>
                                </tr>
                                <tr>
                                    <th>Driver</th>
                                    <td><?= $approval['driver_name'] ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td>
                                        <?= date('d/m/Y', strtotime($approval['start_date'])) ?> s/d
                                        <?= date('d/m/Y', strtotime($approval['end_date'])) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tujuan</th>
                                    <td><?= $approval['purpose'] ?></td>
                                </tr>
                                <tr>
                                    <th>Level Persetujuan</th>
                                    <td>Level <?= $approval['level'] ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <?php if ($approval['status'] === 'pending') : ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php elseif ($approval['status'] === 'approved') : ?>
                                            <span class="badge badge-success">Disetujui</span>
                                        <?php else : ?>
                                            <span class="badge badge-danger">Ditolak</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($approval['notes']) : ?>
                                    <tr>
                                        <th>Catatan</th>
                                        <td><?= $approval['notes'] ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="<?= base_url('approval') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <?php if ($approval['status'] === 'pending') : ?>
                            <a href="<?= base_url('approval/' . $approval['id'] . '/approve') ?>" class="btn btn-primary">
                                <i class="fas fa-check"></i> Setujui
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 