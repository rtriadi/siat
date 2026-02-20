<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Kelola Permintaan</h1>
            <p class="page-subtitle">Kelola semua permintaan ATK</p>
        </div>
        <div class="header-actions">
            <a href="<?= site_url('request_admin/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Buat Permintaan
            </a>
        </div>
    </div>
</div>

<div class="content">
    
    

    <div class="card">
        <div class="data-table-header">
            <div class="data-table-search">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari permintaan..." value="<?= htmlspecialchars($search ?? '') ?>">
                <?php if (!empty($search)): ?>
                <button class="search-clear" onclick="clearSearch()">
                    <i class="fas fa-times"></i>
                </button>
                <?php endif; ?>
            </div>
            <div class="table-filters">
                <select name="status" id="status" class="form-select" onchange="filterStatus(this.value)">
                    <?php
                    $statuses = [
                        'all' => 'Semua',
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'delivered' => 'Dikirim',
                        'rejected' => 'Ditolak',
                        'cancelled' => 'Dibatalkan'
                    ];
                    foreach ($statuses as $value => $label):
                    ?>
                        <option value="<?= $value ?>" <?= $selected_status === $value ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="table-info">
                Menampilkan <?= count($requests) ?> data
            </div>
        </div>
        
        <?php if (empty($requests)): ?>
        <div class="table-empty">
            <i class="fas fa-inbox"></i>
            <p><?= !empty($search) ? 'Tidak ada hasil pencarian' : 'Tidak ada permintaan' ?></p>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No Permintaan</th>
                        <th>Pegawai</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $index => $request): ?>
                        <?php
                        $pegawai = $user_map[$request['user_id']] ?? null;
                        $pegawai_label = $pegawai ? htmlspecialchars($pegawai['nama'] . ' (' . $pegawai['nip'] . ')', ENT_QUOTES, 'UTF-8') : 'Tidak diketahui';
                        $status_class = '';
                        $status_icon = '';
                        switch ($request['status']) {
                            case 'pending':
                                $status_class = 'warning';
                                $status_icon = 'clock';
                                break;
                            case 'approved':
                                $status_class = 'info';
                                $status_icon = 'check';
                                break;
                            case 'delivered':
                                $status_class = 'success';
                                $status_icon = 'check-circle';
                                break;
                            case 'rejected':
                                $status_class = 'danger';
                                $status_icon = 'times';
                                break;
                            default:
                                $status_class = 'secondary';
                                $status_icon = 'ban';
                        }
                        ?>
                        <tr>
                            <td>
                                <span class="request-no"><?= htmlspecialchars($request['request_no'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td>
                                <span class="pegawai-name"><?= $pegawai_label ?></span>
                            </td>
                            <td>
                                <span class="status-badge <?= $status_class ?>">
                                    <i class="fas fa-<?= $status_icon ?>"></i>
                                    <?= ucfirst($request['status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="date-text"><?= date('d M Y, H:i', strtotime($request['created_at'])) ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= site_url('request_admin/detail/' . $request['id_request']) ?>" class="btn btn-sm btn-outline" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($request['status'] === 'pending'): ?>
                                        <a href="<?= site_url('request_admin/approve/' . $request['id_request']) ?>" class="btn btn-sm btn-outline-success" title="Setuju">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Tolak" onclick="showRejectModal(<?= $request['id_request'] ?>)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($request['status'] === 'approved'): ?>
                                        <a href="<?= site_url('request_admin/deliver/' . $request['id_request']) ?>" class="btn btn-sm btn-outline-warning" title="Kirim">
                                            <i class="fas fa-truck"></i>
                                        </a>
                                    <?php endif; ?>
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

<!-- Reject Modal -->
<div class="custom-modal" id="rejectModal">
    <div class="custom-modal-overlay"></div>
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5>Tolak Permintaan</h5>
            <button type="button" class="close-btn" onclick="closeRejectModal()">&times;</button>
        </div>
        <form method="post" id="rejectForm" action="">
            <div class="custom-modal-body">
                <div class="form-group">
                    <label>Alasan Penolakan <span class="required">*</span></label>
                    <textarea name="note" class="form-control" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
            </div>
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        performSearch(this.value);
    }
});

function performSearch(value) {
    var url = new URL(window.location.href);
    if (value) {
        url.searchParams.set('search', value);
    } else {
        url.searchParams.delete('search');
    }
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

function clearSearch() {
    performSearch('');
}

function filterStatus(status) {
    var url = new URL(window.location.href);
    if (status && status !== 'all') {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location.href = url.toString();
}

function showRejectModal(id) {
    document.getElementById('rejectForm').action = '<?= site_url('request_admin/reject/') ?>' + id;
    document.getElementById('rejectModal').classList.add('show');
    jQuery('body').css('overflow', 'hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('show');
    jQuery('body').css('overflow', 'auto');
    document.getElementById('rejectForm').reset();
}

jQuery('.custom-modal-overlay').click(function() {
    closeRejectModal();
});
</script>

<style>
.page-header { margin-bottom: 24px; }
.page-header-content { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
.page-title { font-size: 28px; font-weight: 700; margin: 0; }
.page-subtitle { font-size: 14px; color: var(--text-secondary); margin: 4px 0 0; }
.header-actions { display: flex; gap: 12px; }

.alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.alert .close { background: none; border: none; font-size: 20px; cursor: pointer; margin-left: auto; }

.data-table-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; gap: 16px; flex-wrap: wrap; }
.data-table-search { position: relative; flex: 1; max-width: 400px; }
.data-table-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.data-table-search input { width: 100%; padding: 10px 36px 10px 40px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
.data-table-search input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.search-clear { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; }
.table-filters { display: flex; gap: 12px; }
.form-select { padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: white; cursor: pointer; }
.form-select:focus { outline: none; border-color: #3b82f6; }
.table-info { font-size: 13px; color: var(--text-secondary); }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
.data-table td { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
.data-table tbody tr:hover { background: #f9fafb; }

.request-no { font-weight: 600; color: #1f2937; }
.pegawai-name { color: #374151; }
.date-text { color: #6b7280; }

.status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
.status-badge.warning { background: #fef3c7; color: #92400e; }
.status-badge.info { background: #dbeafe; color: #1e40af; }
.status-badge.success { background: #d1fae5; color: #065f46; }
.status-badge.danger { background: #fee2e2; color: #991b1b; }
.status-badge.secondary { background: #f3f4f6; color: #6b7280; }

.action-buttons { display: flex; gap: 8px; }
.btn-outline { padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; background: white; color: #6b7280; cursor: pointer; transition: all 0.15s; }
.btn-outline:hover { background: #f3f4f6; color: #3b82f6; border-color: #3b82f6; }
.btn-outline-success { color: #10b981; border-color: #10b981; }
.btn-outline-success:hover { background: #10b981; color: white; }
.btn-outline-danger { color: #dc3545; border-color: #dc3545; }
.btn-outline-danger:hover { background: #dc3545; color: white; }
.btn-outline-warning { color: #f59e0b; border-color: #f59e0b; }
.btn-outline-warning:hover { background: #f59e0b; color: white; }
.btn-sm { padding: 6px 10px; font-size: 13px; }

.table-empty { text-align: center; padding: 48px 20px; color: #9ca3af; }
.table-empty i { font-size: 40px; margin-bottom: 12px; display: block; }
.modal-content { border-radius: 12px; border: none; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.modal-header { border-bottom: 1px solid #e5e7eb; padding: 16px 20px; }
.modal-title { font-weight: 600; font-size: 18px; }
.modal-body { padding: 20px; }
.modal-footer { border-top: 1px solid #e5e7eb; padding: 16px 20px; }
.close { background: none; border: none; font-size: 24px; color: #9ca3af; cursor: pointer; }
.required { color: #dc3545; }

.btn { padding: 10px 20px; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary:hover { background: #e5e7eb; }
.btn-danger { background: #dc3545; color: white; }
.btn-danger:hover { background: #c82333; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; margin-bottom: 6px; font-weight: 500; color: #374151; }
.form-control { width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
.form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

.custom-modal { display: none !important; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; }
.custom-modal.show { display: flex !important; align-items: center; justify-content: center; }
.custom-modal-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
.custom-modal-content { position: relative; background: white; border-radius: 12px; width: 90%; max-width: 450px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.custom-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 24px; border-bottom: 1px solid #e5e7eb; }
.custom-modal-header h5 { font-size: 18px; font-weight: 600; margin: 0; }
.close-btn { background: none; border: none; font-size: 24px; color: #9ca3af; cursor: pointer; }
.custom-modal-body { padding: 24px; }
.custom-modal-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 16px 24px; border-top: 1px solid #e5e7eb; }
</style>
