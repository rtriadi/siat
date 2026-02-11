<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Delivery Checklist</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('request_admin/detail/' . $request['id_request']) ?>" class="btn btn-secondary btn-sm">
                                Kembali
                            </a>
                        </div>
                    </div>
                    <form method="post" action="<?= site_url('request_admin/deliver/' . $request['id_request']) ?>">
                        <div class="card-body">
                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $this->session->flashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <div class="alert alert-warning">
                                Sisa item yang tidak dikirim akan otomatis dibatalkan dan stok dikembalikan ke tersedia.
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Disetujui</th>
                                        <th>Dikirim</th>
                                        <th>Sisa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($request['items'] as $item): ?>
                                        <?php
                                        $approved = (int) $item['qty_approved'];
                                        $remaining = max($approved, 0);
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= $approved ?></td>
                                            <td style="max-width: 120px;">
                                                <input type="number" class="form-control" name="qty_delivered[<?= (int) $item['item_id'] ?>]"
                                                    min="0" max="<?= $approved ?>" value="<?= $approved ?>" required>
                                            </td>
                                            <td><?= $remaining ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <a href="<?= site_url('request_admin/detail/' . $request['id_request']) ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-warning">
                                Simpan Pengiriman
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
