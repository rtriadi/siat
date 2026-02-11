<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Approve Permintaan</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('request_admin/detail/' . $request['id_request']) ?>" class="btn btn-secondary btn-sm">
                                Kembali
                            </a>
                        </div>
                    </div>
                    <form method="post" action="<?= site_url('request_admin/approve/' . $request['id_request']) ?>">
                        <div class="card-body">
                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $this->session->flashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <div class="alert alert-info">
                                Anda dapat mengurangi jumlah persetujuan dari jumlah yang diminta. Jumlah persetujuan tidak boleh melebihi jumlah permintaan.
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Diminta</th>
                                        <th>Disetujui</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($request['items'] as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= (int) $item['qty_requested'] ?></td>
                                            <td style="max-width: 120px;">
                                                <input type="number" class="form-control" name="qty_approved[<?= (int) $item['item_id'] ?>]"
                                                    min="0" max="<?= (int) $item['qty_requested'] ?>" value="<?= (int) $item['qty_requested'] ?>" required>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <div class="form-group">
                                <label for="note">Catatan Persetujuan (Opsional)</label>
                                <textarea name="note" id="note" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="<?= site_url('request_admin/detail/' . $request['id_request']) ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-success">
                                Simpan Persetujuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
