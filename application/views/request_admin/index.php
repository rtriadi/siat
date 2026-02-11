<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Permintaan Barang</h3>
                        <div class="card-tools">
                            <form method="get" class="form-inline">
                                <label class="mr-2" for="status">Status</label>
                                <select name="status" id="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                    <?php
                                    $statuses = [
                                        'all' => 'Semua',
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'delivered' => 'Delivered',
                                        'rejected' => 'Rejected',
                                        'cancelled' => 'Cancelled'
                                    ];
                                    foreach ($statuses as $value => $label):
                                    ?>
                                        <option value="<?= $value ?>" <?= $selected_status === $value ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
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

                        <table id="request-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Permintaan</th>
                                    <th>Pegawai</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($requests)): ?>
                                    <?php foreach ($requests as $index => $request): ?>
                                        <?php
                                        $pegawai = $user_map[$request['user_id']] ?? null;
                                        $pegawai_label = $pegawai ? ($pegawai['nama'] . ' (' . $pegawai['nip'] . ')') : 'Tidak diketahui';
                                        ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($request['request_no'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($pegawai_label, ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <span class="badge badge-<?= $request['status'] === 'pending' ? 'warning' : ($request['status'] === 'approved' ? 'info' : ($request['status'] === 'delivered' ? 'success' : ($request['status'] === 'rejected' ? 'danger' : 'secondary'))) ?>">
                                                    <?= ucfirst($request['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= site_url('request_admin/detail/' . $request['id_request']) ?>" class="btn btn-sm btn-primary">
                                                    Detail
                                                </a>
                                                <?php if ($request['status'] === 'pending'): ?>
                                                    <a href="<?= site_url('request_admin/approve/' . $request['id_request']) ?>" class="btn btn-sm btn-success">
                                                        Approve
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal<?= $request['id_request'] ?>">
                                                        Reject
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($request['status'] === 'approved'): ?>
                                                    <a href="<?= site_url('request_admin/deliver/' . $request['id_request']) ?>" class="btn btn-sm btn-warning">
                                                        Deliver
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                        <?php if ($request['status'] === 'pending'): ?>
                                            <div class="modal fade" id="rejectModal<?= $request['id_request'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
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
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(function () {
        $('#request-table').DataTable({
            "responsive": true,
            "autoWidth": false
        });
    });
</script>
