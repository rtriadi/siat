<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Permintaan</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('request/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Buat Permintaan
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $this->session->flashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('success')): ?>
                            <div class="alert alert-success" role="alert">
                                <?= $this->session->flashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($requests)): ?>
                            <div class="alert alert-info" role="alert">
                                Anda belum memiliki permintaan. Silakan buat permintaan baru.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No. Permintaan</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="width: 200px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($requests as $request): ?>
                                            <?php
                                            $status = $request['status'];
                                            $badge_class = 'secondary';
                                            $status_label = ucfirst($status);
                                            if ($status === 'pending') {
                                                $badge_class = 'warning';
                                                $status_label = 'Pending';
                                            } elseif ($status === 'approved') {
                                                $badge_class = 'info';
                                                $status_label = 'Disetujui';
                                            } elseif ($status === 'rejected') {
                                                $badge_class = 'danger';
                                                $status_label = 'Ditolak';
                                            } elseif ($status === 'delivered') {
                                                $badge_class = 'success';
                                                $status_label = 'Diterima';
                                            } elseif ($status === 'cancelled') {
                                                $badge_class = 'secondary';
                                                $status_label = 'Dibatalkan';
                                            }
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($request['request_no'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td class="text-center">
                                                    <?= format_datetime_indonesia($request['created_at']) ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge badge-<?= $badge_class ?>">
                                                        <?= $status_label ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= site_url('request/detail/' . $request['id_request']) ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                    <?php if ($status === 'pending'): ?>
                                                        <a href="<?= site_url('request/cancel/' . $request['id_request']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Batalkan permintaan ini?');">
                                                            <i class="fas fa-times"></i> Batalkan
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
