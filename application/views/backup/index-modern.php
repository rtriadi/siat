<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Cadangkan Database</h1>
            <p class="page-subtitle">Export dan kelola file backup database</p>
        </div>
        <a href="<?= site_url('backup/export') ?>" class="btn btn-primary">
            <i class="fas fa-download"></i>
            <span>Export Database</span>
        </a>
    </div>
</div>

<div class="content">
    

    

    <div class="card">
        <div class="data-table-header">
            <div class="table-info">
                <?= count($backups) ?> file backup
            </div>
        </div>
        
        <?php if (empty($backups)): ?>
        <div class="table-empty">
            <i class="fas fa-database"></i>
            <p>Belum Ada Backup</p>
            <span class="text-muted">Klik tombol "Export Database" untuk membuat backup pertama.</span>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama File</th>
                        <th>Tanggal</th>
                        <th>Ukuran</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $backup): ?>
                    <tr>
                        <td>
                            <div class="file-name">
                                <i class="fas fa-file-code"></i>
                                <?= htmlspecialchars($backup['name']) ?>
                            </div>
                        </td>
                        <td><?= date('d M Y, H:i', $backup['date']) ?></td>
                        <td><?= number_format($backup['size'] / 1024, 2) ?> KB</td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?= site_url('backup/download/' . $backup['name']) ?>" 
                                   class="btn btn-sm btn-outline" 
                                   title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Hapus"
                                        onclick="confirmDelete('<?= htmlspecialchars($backup['name']) ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="custom-modal" id="deleteModal">
    <div class="custom-modal-overlay"></div>
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5>Hapus Backup</h5>
            <button type="button" class="close-btn" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="custom-modal-body">
            <p>Apakah Anda yakin ingin menghapus file backup <strong id="deleteFileName"></strong>?</p>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
            <form id="deleteForm" method="POST" action="" style="display: inline;">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(filename) {
    document.getElementById('deleteFileName').textContent = filename;
    document.getElementById('deleteForm').action = '<?= site_url('backup/delete/') ?>' + filename;
    jQuery('#deleteModal').fadeIn(200);
    jQuery('body').css('overflow', 'hidden');
}

function closeDeleteModal() {
    jQuery('#deleteModal').fadeOut(200);
    jQuery('body').css('overflow', 'auto');
}

jQuery('.custom-modal-overlay').click(function() {
    closeDeleteModal();
});
</script>

<style>
.page-header { margin-bottom: 24px; }
.page-header-content { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
.page-title { font-size: 28px; font-weight: 700; margin: 0; }
.page-subtitle { font-size: 14px; color: var(--text-secondary); margin: 4px 0 0; }

.alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.alert .close { background: none; border: none; font-size: 20px; cursor: pointer; margin-left: auto; }

.data-table-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; }
.table-info { font-size: 13px; color: var(--text-secondary); }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
.data-table td { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
.data-table tbody tr:hover { background: #f9fafb; }

.file-name { display: flex; align-items: center; gap: 10px; font-weight: 500; }
.file-name i { color: #6b7280; }

.action-buttons { display: flex; gap: 8px; }
.btn-outline { padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; background: white; color: #6b7280; cursor: pointer; transition: all 0.15s; }
.btn-outline:hover { background: #f3f4f6; color: #3b82f6; border-color: #3b82f6; }
.btn-outline-danger { color: #dc3545; border-color: #dc3545; }
.btn-outline-danger:hover { background: #dc3545; color: white; }
.btn-sm { padding: 6px 10px; font-size: 13px; }

.table-empty { text-align: center; padding: 48px 20px; color: #9ca3af; }
.table-empty i { font-size: 40px; margin-bottom: 12px; display: block; }
.table-empty p { font-size: 16px; font-weight: 500; margin-bottom: 4px; color: #374151; }
.text-muted { font-size: 14px; }

.custom-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; }
.custom-modal.show { display: block; }
.custom-modal-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
.custom-modal-content { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 12px; width: 90%; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.custom-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e5e7eb; }
.custom-modal-header h5 { font-size: 18px; font-weight: 600; margin: 0; }
.close-btn { background: none; border: none; font-size: 24px; color: #9ca3af; cursor: pointer; }
.custom-modal-body { padding: 24px; }
.custom-modal-body p { margin: 0; color: #4b5563; }
.custom-modal-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 16px 24px; border-top: 1px solid #e5e7eb; }

.btn { padding: 10px 20px; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary:hover { background: #e5e7eb; }
.btn-danger { background: #dc3545; color: white; }
.btn-danger:hover { background: #c82333; }
</style>
