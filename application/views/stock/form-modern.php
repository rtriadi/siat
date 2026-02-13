<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $is_edit = isset($item) && !empty($item); ?>

<section class="page-header">
    <div class="header-content">
        <div class="header-info">
            <h1 class="page-title"><?= $is_edit ? 'Edit Item' : 'Tambah Item' ?></h1>
            <p class="page-subtitle"><?= $is_edit ? 'Perbarui informasi item stok' : 'Tambah item baru ke inventori' ?></p>
        </div>
    </div>
</section>

<section class="page-content">
    <div class="card">
        <div class="card-body">
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url($is_edit ? 'stock/update/' . $item['id_item'] : 'stock/store') ?>" method="post" class="modern-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Kategori <span class="required">*</span></label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id_category'] ?>" <?= $is_edit && $item['category_id'] == $cat['id_category'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="item_name" class="form-label">Nama Item <span class="required">*</span></label>
                        <input type="text" name="item_name" id="item_name" class="form-control" 
                               value="<?= $is_edit ? htmlspecialchars($item['item_name']) : '' ?>" 
                               placeholder="Masukkan nama item" required maxlength="255">
                    </div>
                </div>

                <?php if ($is_edit): ?>
                    <div class="form-section">
                        <h4 class="section-title">Informasi Stok</h4>
                        <div class="stock-info-grid">
                            <div class="stock-info-card">
                                <div class="stock-info-icon available">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="stock-info-content">
                                    <div class="stock-info-label">Tersedia</div>
                                    <div class="stock-info-value"><?= (int)$item['available_qty'] ?></div>
                                </div>
                            </div>
                            <div class="stock-info-card">
                                <div class="stock-info-icon reserved">
                                    <i class="fas fa-bookmark"></i>
                                </div>
                                <div class="stock-info-content">
                                    <div class="stock-info-label">Direservasi</div>
                                    <div class="stock-info-value"><?= (int)$item['reserved_qty'] ?></div>
                                </div>
                            </div>
                            <div class="stock-info-card">
                                <div class="stock-info-icon used">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stock-info-content">
                                    <div class="stock-info-label">Terpakai</div>
                                    <div class="stock-info-value"><?= (int)$item['used_qty'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="qty_adjustment" class="form-label">Penyesuaian Stok</label>
                            <div class="input-with-info">
                                <input type="number" name="qty_adjustment" id="qty_adjustment" class="form-control" 
                                       placeholder="Contoh: 10 atau -5" value="0">
                                <span class="input-info">Gunakan angka positif untuk menambah, negatif untuk mengurangi</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="adjustment_reason" class="form-label">Alasan Penyesuaian</label>
                            <input type="text" name="adjustment_reason" id="adjustment_reason" class="form-control" 
                                   placeholder="Jelaskan alasan penyesuaian...">
                        </div>
                    </div>
                <?php else: ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="available_qty" class="form-label">Jumlah Awal <span class="required">*</span></label>
                            <input type="number" name="available_qty" id="available_qty" class="form-control" 
                                   placeholder="Masukkan jumlah awal" min="0" value="0" required>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="low_stock_threshold" class="form-label">Batas Stok Minimum <span class="required">*</span></label>
                        <input type="number" name="low_stock_threshold" id="low_stock_threshold" class="form-control" 
                               placeholder="Batas minimum untuk notifikasi" min="0" 
                               value="<?= $is_edit ? (int)$item['low_stock_threshold'] : '10' ?>" required>
                        <span class="input-info">Akan menerima notifikasi jika stok di bawah batas ini</span>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= site_url('stock') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <?= $is_edit ? 'Simpan Perubahan' : 'Tambah Item' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
    .page-header {
        margin-bottom: 24px;
    }
    
    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    
    .page-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
    }
    
    .required {
        color: var(--danger);
    }
    
    .modern-form {
        max-width: 600px;
    }
    
    .form-row {
        margin-bottom: 20px;
    }
    
    .form-section {
        background: var(--surface-elevated);
        border-radius: var(--radius-lg);
        padding: 24px;
        margin: 24px 0;
    }
    
    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 16px;
    }
    
    .stock-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    
    .stock-info-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .stock-info-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    
    .stock-info-icon.available {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }
    
    .stock-info-icon.reserved {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }
    
    .stock-info-icon.used {
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent);
    }
    
    .stock-info-content {
        flex: 1;
    }
    
    .stock-info-label {
        font-size: 12px;
        color: var(--text-secondary);
        margin-bottom: 4px;
    }
    
    .stock-info-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
    }
    
    .input-with-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .input-info {
        font-size: 12px;
        color: var(--text-secondary);
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }
    
    @media (max-width: 768px) {
        .stock-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
