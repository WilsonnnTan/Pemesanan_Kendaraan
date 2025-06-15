<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Persetujuan Pemesanan</h3>
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
                            </table>

                            <form action="<?= base_url('approval/' . $approval['id'] . '/approve') ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="">Pilih Status</option>
                                        <option value="approved">Setujui</option>
                                        <option value="rejected">Tolak</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Catatan</label>
                                    <textarea name="notes" class="form-control" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan
                                    </button>
                                    <a href="<?= base_url('approval/' . $approval['id']) ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 