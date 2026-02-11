<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Buat Permintaan ATK</h3>
                    </div>
                    <form action="<?= site_url('request/store') ?>" method="post">
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

                            <?php if (empty($items)): ?>
                                <div class="alert alert-info" role="alert">
                                    Stok belum tersedia untuk permintaan. Hubungi admin untuk menambah stok.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-center">Stok Tersedia</th>
                                                <th class="text-center" style="width: 160px;">Jumlah</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($items as $item): ?>
                                                <?php $item_id = (int) $item['id_item']; ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                                    <td class="text-center">
                                                        <?= (int) $item['available_qty'] ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <input
                                                            type="number"
                                                            class="form-control form-control-sm"
                                                            name="qty_requested[<?= $item_id ?>]"
                                                            min="0"
                                                            max="<?= (int) $item['available_qty'] ?>"
                                                            value="<?= isset($old_qtys[$item_id]) ? (int) $old_qtys[$item_id] : 0 ?>"
                                                            inputmode="numeric"
                                                        >
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="form-group">
                                    <label for="note">Catatan (Opsional)</label>
                                    <textarea
                                        class="form-control"
                                        id="note"
                                        name="note"
                                        rows="3"
                                        placeholder="Tambahkan catatan jika diperlukan…"
                                    ><?= htmlspecialchars((string) $old_note, ENT_QUOTES, 'UTF-8') ?></textarea>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer">
                            <a href="<?= site_url('request') ?>" class="btn btn-secondary">Kembali</a>
                            <?php if (!empty($items)): ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Kirim Permintaan
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
