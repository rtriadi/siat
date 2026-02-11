<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Permintaan</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('request_admin') ?>" class="btn btn-secondary btn-sm">
                                Kembali
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

                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">No Permintaan</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($request['request_no'], ENT_QUOTES, 'UTF-8') ?></dd>
                                    <dt class="col-sm-4">Pegawai</dt>
                                    <dd class="col-sm-8">
                                        <?= htmlspecialchars($pegawai['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                        <?php if (!empty($pegawai['nip'])): ?>
                                            (<?= htmlspecialchars($pegawai['nip'], ENT_QUOTES, 'UTF-8') ?>)
                                        <?php endif; ?>
                                    </dd>
                                    <dt class="col-sm-4">Status</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge badge-<?= $request['status'] === 'pending' ? 'warning' : ($request['status'] === 'approved' ? 'info' : ($request['status'] === 'delivered' ? 'success' : ($request['status'] === 'rejected' ? 'danger' : 'secondary'))) ?>">
                                            <?= ucfirst($request['status']) ?>
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-5">Dibuat</dt>
                                    <dd class="col-sm-7"><?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></dd>
                                    <dt class="col-sm-5">Disetujui</dt>
                                    <dd class="col-sm-7"><?= $request['approved_at'] ? date('d/m/Y H:i', strtotime($request['approved_at'])) : '-' ?></dd>
                                    <dt class="col-sm-5">Dikirim</dt>
                                    <dd class="col-sm-7"><?= $request['delivered_at'] ? date('d/m/Y H:i', strtotime($request['delivered_at'])) : '-' ?></dd>
                                    <dt class="col-sm-5">Ditolak</dt>
                                    <dd class="col-sm-7"><?= $request['rejected_at'] ? date('d/m/Y H:i', strtotime($request['rejected_at'])) : '-' ?></dd>
                                </dl>
                            </div>
                        </div>

                        <?php if (!empty($request['notes'])): ?>
                            <div class="alert alert-info">
                                <strong>Catatan:</strong> <?= nl2br(htmlspecialchars($request['notes'], ENT_QUOTES, 'UTF-8')) ?>
                            </div>
                        <?php endif; ?>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Riwayat Status</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li>Pending: <?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></li>
                                    <li>Approved: <?= $request['approved_at'] ? date('d/m/Y H:i', strtotime($request['approved_at'])) : '-' ?></li>
                                    <li>Delivered: <?= $request['delivered_at'] ? date('d/m/Y H:i', strtotime($request['delivered_at'])) : '-' ?></li>
                                    <li>Rejected: <?= $request['rejected_at'] ? date('d/m/Y H:i', strtotime($request['rejected_at'])) : '-' ?></li>
                                    <li>Cancelled: <?= $request['cancelled_at'] ? date('d/m/Y H:i', strtotime($request['cancelled_at'])) : '-' ?></li>
                                </ul>
                            </div>
                        </div>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Diminta</th>
                                    <th>Disetujui</th>
                                    <th>Dikirim</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($request['items'] as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= (int) $item['qty_requested'] ?></td>
                                        <td><?= (int) $item['qty_approved'] ?></td>
                                        <td><?= (int) $item['qty_delivered'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <?php if ($request['status'] === 'pending'): ?>
                            <a href="<?= site_url('request_admin/approve/' . $request['id_request']) ?>" class="btn btn-success">
                                Approve
                            </a>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                Reject
                            </button>

                            <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tolak Permintaan</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form method="post" action="<?= site_url('request_admin/reject/' . $request['id_request']) ?>">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Alasan Penolakan <span class="text-danger">*</span></label>
                                                    <textarea name="note" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($request['status'] === 'approved'): ?>
                            <a href="<?= site_url('request_admin/deliver/' . $request['id_request']) ?>" class="btn btn-warning">
                                Deliver
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
