<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Ubah Password</h1>
            <p class="page-subtitle">Perbarui password akun Anda</p>
        </div>
    </div>
</div>

<div class="content">
    
    
    
    
    

    <div class="card">
        <div class="card-body">
            <form action="<?= site_url('auth/change_password') ?>" method="post" class="form-modern">
                <div class="form-group">
                    <label for="current_password" class="form-label">Password Saat Ini <span class="required">*</span></label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password" class="form-label">Password Baru <span class="required">*</span></label>
                    <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" required>
                    <small class="form-text text-muted">Minimal 8 karakter</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Konfirmasi Password <span class="required">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required>
                </div>
                
                <div class="form-actions">
                    <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-modern { max-width: 500px; }
.form-group { margin-bottom: 20px; }
.form-label { display: block; font-weight: 500; margin-bottom: 8px; color: var(--text-primary); }
.form-label .required { color: var(--danger); }
.form-text.text-muted { font-size: 12px; color: var(--text-secondary); margin-top: 4px; display: block; }
.form-actions { display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); }
</style>
