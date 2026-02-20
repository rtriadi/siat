<?php $is_edit = isset($category); ?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title"><?= $is_edit ? 'Edit Kategori' : 'Tambah Kategori' ?></h1>
            <p class="page-subtitle"><?= $is_edit ? 'Ubah informasi kategori' : 'Tambah kategori baru untuk mengorganisir item' ?></p>
        </div>
    </div>
</div>

<div class="content">
    

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= site_url($is_edit ? 'category/update/' . $category['id_category'] : 'category/store') ?>" class="form-modern">
                <div class="form-group">
                    <label for="category_name" class="form-label">
                        Nama Kategori <span class="required">*</span>
                    </label>
                    <input type="text" 
                           name="category_name" 
                           id="category_name" 
                           class="form-control <?= form_error('category_name') ? 'is-invalid' : '' ?>"
                           value="<?= $is_edit ? htmlspecialchars($category['category_name']) : set_value('category_name') ?>"
                           placeholder="Contoh: ATK, Elektronik, Furnitur"
                           required
                           maxlength="100">
                    <?php if (form_error('category_name')): ?>
                    <div class="invalid-feedback"><?= form_error('category_name') ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">
                        Deskripsi
                    </label>
                    <textarea name="description" 
                              id="description" 
                              class="form-control <?= form_error('description') ? 'is-invalid' : '' ?>"
                              placeholder="Deskripsi opsional untuk kategori ini..."
                              rows="4"
                              maxlength="500"><?= $is_edit ? htmlspecialchars($category['description'] ?? '') : set_value('description') ?></textarea>
                    <?php if (form_error('description')): ?>
                    <div class="invalid-feedback"><?= form_error('description') ?></div>
                    <?php endif; ?>
                    <small class="form-text text-muted">Maksimal 500 karakter</small>
                </div>

                <div class="form-actions">
                    <a href="<?= site_url('category') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        <span>Kembali</span>
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        <span><?= $is_edit ? 'Simpan Perubahan' : 'Simpan' ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-modern {
    max-width: 600px;
}
.form-group {
    margin-bottom: 24px;
}
.form-label {
    font-weight: 500;
    margin-bottom: 8px;
    display: block;
    color: #333;
}
.form-label .required {
    color: #dc3545;
}
.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus {
    outline: none;
    border-color: #4a90d9;
    box-shadow: 0 0 0 3px rgba(74, 144, 217, 0.1);
}
.form-control.is-invalid {
    border-color: #dc3545;
}
.invalid-feedback {
    color: #dc3545;
    font-size: 13px;
    margin-top: 6px;
}
.form-text {
    font-size: 12px;
    margin-top: 4px;
    display: block;
}
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #eee;
}
.btn {
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    text-decoration: none;
    border: none;
}
.btn-secondary {
    background: #f5f5f5;
    color: #333;
}
.btn-secondary:hover {
    background: #e5e5e5;
}
.btn-primary {
    background: #4a90d9;
    color: white;
}
.btn-primary:hover {
    background: #3a7bc8;
}
</style>
