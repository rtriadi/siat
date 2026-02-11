<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Notifications</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?= $this->session->flashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Pesan</th>
                            <th>Tipe</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($notifications)): ?>
                            <?php foreach ($notifications as $notification): ?>
                                <tr class="<?= (int) $notification['is_read'] === 1 ? '' : 'font-weight-bold' ?>">
                                    <td><?= htmlspecialchars($notification['title'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($notification['type'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= format_datetime_indonesia($notification['created_at']) ?></td>
                                    <td>
                                        <?php if ((int) $notification['is_read'] === 1): ?>
                                            <span class="badge badge-secondary">Dibaca</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Belum dibaca</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ((int) $notification['is_read'] === 0): ?>
                                            <a href="<?= site_url('notification/mark_read/' . $notification['id_notification']) ?>"
                                                class="btn btn-sm btn-primary">
                                                Tandai dibaca
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada notifikasi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
