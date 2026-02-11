<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Permintaan</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('request') ?>" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
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

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">No. Permintaan</p>
                                <h5><?= htmlspecialchars($request['request_no'], ENT_QUOTES, 'UTF-8') ?></h5>
                            </div>
                            <div class="col-md-6 text-md-right">
                                <p class="mb-1 text-muted">Status</p>
                                <span class="badge badge-<?= $badge_class ?>">
                                    <?= $status_label ?>
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <p class="mb-1 text-muted">Tanggal</p>
                                <div><?= format_datetime_indonesia($request['created_at']) ?></div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1 text-muted">Disetujui</p>
                                <div><?= $request['approved_at'] ? format_datetime_indonesia($request['approved_at']) : '-' ?></div>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1 text-muted">Dikirim</p>
                                <div><?= $request['delivered_at'] ? format_datetime_indonesia($request['delivered_at']) : '-' ?></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <p class="mb-1 text-muted">Catatan</p>
                                <div><?= $request['notes'] ? nl2br(htmlspecialchars($request['notes'], ENT_QUOTES, 'UTF-8')) : '-' ?></div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Diminta</th>
                                        <th class="text-center">Disetujui</th>
                                        <th class="text-center">Dikirim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($request['items'] as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td class="text-center"><?= (int) $item['qty_requested'] ?></td>
                                            <td class="text-center"><?= (int) $item['qty_approved'] ?></td>
                                            <td class="text-center"><?= (int) $item['qty_delivered'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
