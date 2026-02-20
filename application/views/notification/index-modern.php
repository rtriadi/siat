<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Notifikasi</h1>
            <p class="page-subtitle">Kelola notifikasi Anda</p>
        </div>
        <?php 
        $unread_count = 0;
        foreach ($notifications as $n) {
            if ((int) $n['is_read'] === 0) $unread_count++;
        }
        if ($unread_count > 0): ?>
        <a href="<?= site_url('notification/mark_all_read') ?>" class="btn btn-outline">
            <i class="fas fa-check-double"></i>
            Tandai Semua Dibaca (<?= $unread_count ?>)
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="content">
    
    

    <div class="card">
        <?php if (empty($notifications)): ?>
        <div class="table-empty">
            <i class="fas fa-bell-slash"></i>
            <p>Belum Ada Notifikasi</p>
            <span class="text-muted">Notifikasi akan muncul di sini</span>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Pesan</th>
                        <th>Tipe</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notifications as $notification): ?>
                        <tr class="<?= (int) $notification['is_read'] === 1 ? '' : 'unread' ?>">
                            <td>
                                <strong><?= htmlspecialchars($notification['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </td>
                            <td><?= htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="badge badge-<?= $notification['type'] === 'success' ? 'success' : ($notification['type'] === 'error' ? 'danger' : 'info') ?>">
                                    <?= htmlspecialchars($notification['type'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td><?= format_datetime_indonesia($notification['created_at']) ?></td>
                            <td>
                                <?php if ((int) $notification['is_read'] === 1): ?>
                                    <span class="status-badge secondary">Dibaca</span>
                                <?php else: ?>
                                    <span class="status-badge warning">Belum Dibaca</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int) $notification['is_read'] === 0): ?>
                                    <a href="<?= site_url('notification/mark_read/' . $notification['id_notification']) ?>" class="btn btn-sm btn-outline">
                                        <i class="fas fa-check"></i>
                                        Tandai
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.page-header { margin-bottom: 24px; }
.page-header-content { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
.page-title { font-size: 28px; font-weight: 700; margin: 0; }
.page-subtitle { font-size: 14px; color: var(--text-secondary); margin: 4px 0 0; }

.alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.alert .close { background: none; border: none; font-size: 20px; cursor: pointer; margin-left: auto; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
.data-table td { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; vertical-align: middle; }
.data-table tbody tr { transition: background 0.15s; }
.data-table tbody tr:hover { background: #f9fafb; }
.data-table tbody tr.unread { background: rgba(59, 130, 246, 0.05); }

.badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-success { background: #d1fae5; color: #065f46; }
.badge-danger { background: #fee2e2; color: #991b1b; }
.badge-info { background: #dbeafe; color: #1e40af; }

.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.status-badge.warning { background: #fef3c7; color: #92400e; }
.status-badge.secondary { background: #f3f4f6; color: #6b7280; }

.action-buttons { display: flex; gap: 8px; }
.btn-outline { padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; background: white; color: #6b7280; cursor: pointer; transition: all 0.15s; text-decoration: none; }
.btn-outline:hover { background: #f3f4f6; color: #3b82f6; border-color: #3b82f6; }
.btn-sm { padding: 6px 10px; font-size: 13px; }

.table-empty { text-align: center; padding: 48px 20px; color: #9ca3af; }
.table-empty i { font-size: 40px; margin-bottom: 12px; display: block; }
.table-empty p { font-size: 16px; font-weight: 500; margin-bottom: 4px; color: #374151; }
.text-muted { color: #9ca3af; }
</style>
