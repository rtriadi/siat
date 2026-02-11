<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= isset($item) ? 'Edit Item' : 'Tambah Item Baru' ?></h3>
                    </div>
                    <form action="<?= isset($item) ? site_url('stock/update/' . $item['id_item']) : site_url('stock/store') ?>" method="post">
                        <div class="card-body">
                            <?php if ($this->session->flashdata('error')): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $this->session->flashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="category_id">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="category_id" name="category_id" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id_category'] ?>" 
                                            <?= (isset($item) && $item['category_id'] == $category['id_category']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="item_name">Nama Item <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="item_name" name="item_name" 
                                    value="<?= isset($item) ? htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') : '' ?>" 
                                    placeholder="Contoh: Kertas A4 80gsm" required>
                            </div>

                            <?php if (!isset($item)): ?>
                                <div class="form-group">
                                    <label for="available_qty">Jumlah Awal <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="available_qty" name="available_qty" 
                                        value="0" min="0" required>
                                    <small class="form-text text-muted">Jumlah stok tersedia awal.</small>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Stok Saat Ini</label>
                                            <div class="card bg-light">
                                                <div class="card-body p-2">
                                                    <div class="row text-center">
                                                        <div class="col-4">
                                                            <small class="text-muted">Tersedia</small>
                                                            <h5 class="mb-0"><?= (int)$item['available_qty'] ?></h5>
                                                        </div>
                                                        <div class="col-4">
                                                            <small class="text-muted">Direservasi</small>
                                                            <h5 class="mb-0"><?= (int)$item['reserved_qty'] ?></h5>
                                                        </div>
                                                        <div class="col-4">
                                                            <small class="text-muted">Terpakai</small>
                                                            <h5 class="mb-0"><?= (int)$item['used_qty'] ?></h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="qty_adjustment">Penyesuaian Stok (Opsional)</label>
                                            <input type="number" class="form-control" id="qty_adjustment" name="qty_adjustment" 
                                                placeholder="Contoh: +10 atau -5">
                                            <small class="form-text text-muted">Gunakan +/- untuk menambah/mengurangi stok tersedia.</small>
                                        </div>
                                        <div class="form-group">
                                            <label for="adjustment_reason">Alasan Penyesuaian</label>
                                            <input type="text" class="form-control" id="adjustment_reason" name="adjustment_reason" 
                                                placeholder="Contoh: Koreksi stok opname">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="low_stock_threshold">Batas Stok Minimum <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" 
                                    value="<?= isset($item) ? (int)$item['low_stock_threshold'] : '10' ?>" min="0" required>
                                <small class="form-text text-muted">Sistem akan menampilkan alert jika stok tersedia ≤ nilai ini.</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="<?= site_url('stock') ?>" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
