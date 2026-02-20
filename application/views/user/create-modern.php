<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Tambah Pengguna</h1>
            <p class="page-subtitle">Tambah pengguna baru secara manual</p>
        </div>
    </div>
</div>

<div class="content">
    

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= site_url('user/store') ?>" class="form-horizontal">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="username" class="form-label">Username <span class="required">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" required 
                               placeholder="Masukkan username">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password <span class="required">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Masukkan password">
                    </div>

                    <div class="form-group">
                        <label for="nama" class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" required 
                               placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="form-group">
                        <label for="nip" class="form-label">NIP</label>
                        <input type="text" class="form-control" id="nip" name="nip" 
                               placeholder="Masukkan NIP (opsional)">
                    </div>

                    <div class="form-group">
                        <label for="jabatan" class="form-label">Jabatan</label>
                        <input type="text" class="form-control" id="jabatan" name="jabatan" 
                               placeholder="Masukkan jabatan (opsional)">
                    </div>

                    <div class="form-group">
                        <label for="level" class="form-label">Role <span class="required">*</span></label>
                        <select class="form-control" id="level" name="level" required>
                            <option value="">Pilih Role</option>
                            <option value="1">Administrator</option>
                            <option value="2" selected>Pegawai</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= site_url('user/lists') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.page-header { margin-bottom: 24px; }
.page-header-content { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
.page-title { font-size: 28px; font-weight: 700; margin: 0; }
.page-subtitle { font-size: 14px; color: var(--text-secondary); margin: 4px 0 0; }

.alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.alert .close { background: none; border: none; font-size: 20px; cursor: pointer; margin-left: auto; }

.card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
.card-body { padding: 24px; }

.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 24px; }
.form-group { display: flex; flex-direction: column; }
.form-label { font-weight: 500; color: #374151; margin-bottom: 8px; font-size: 14px; }
.required { color: #dc3545; }
.form-control { padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: border-color 0.15s, box-shadow 0.15s; }
.form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

.form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #e5e7eb; }

.btn { padding: 10px 20px; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary:hover { background: #e5e7eb; }
</style>
