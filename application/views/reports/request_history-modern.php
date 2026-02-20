<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Riwayat Permintaan</h1>
            <p class="page-subtitle">Riwayat lengkap permintaan ATK</p>
        </div>
        <div>
            <?php
            $export_params = '';
            if (!empty($filters)) {
                $export_params = '?' . http_build_query($filters);
            }
            ?>
            <a href="<?= site_url('reports/export_request_history' . $export_params) ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </a>
        </div>
    </div>
</div>

<div class="content">
    <div class="card filter-card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter" style="color: #f59e0b;"></i>
                Filter
            </h3>
        </div>
        <div class="card-body">
            <form method="get" action="<?= site_url('reports/request_history') ?>" class="filter-form">
                <div class="filter-grid">
                    <div class="form-group">
                        <label for="date_start" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="date_start" name="date_start" 
                               value="<?= isset($filters['date_start']) ? $filters['date_start'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="date_end" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="date_end" name="date_end" 
                               value="<?= isset($filters['date_end']) ? $filters['date_end'] : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_id" class="form-label">Pegawai</label>
                        <select class="form-control" id="user_id" name="user_id">
                            <option value="">-- Semua Pegawai --</option>
                            <?php foreach ($pegawai_list as $pegawai): ?>
                                <option value="<?= $pegawai['id_user'] ?>" 
                                    <?= isset($filters['user_id']) && $filters['user_id'] == $pegawai['id_user'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pegawai['nama']) ?> (<?= htmlspecialchars($pegawai['nip']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">-- Semua Status --</option>
                            <option value="pending" <?= isset($filters['status']) && $filters['status'] === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="approved" <?= isset($filters['status']) && $filters['status'] === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                            <option value="rejected" <?= isset($filters['status']) && $filters['status'] === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                            <option value="delivered" <?= isset($filters['status']) && $filters['status'] === 'delivered' ? 'selected' : '' ?>>Dikirim</option>
                            <option value="cancelled" <?= isset($filters['status']) && $filters['status'] === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filter
                    </button>
                    <a href="<?= site_url('reports/request_history') ?>" class="btn btn-secondary">
                        <i class="fas fa-undo"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="data-table-header">
            <div class="table-info">
                <?= count($rows) ?> baris
            </div>
        </div>
        
        <?php if (empty($rows)): ?>
        <div class="table-empty">
            <i class="fas fa-inbox"></i>
            <p>Tidak ada data</p>
            <span class="text-muted">Tidak ada request yang sesuai dengan filter</span>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Request</th>
                        <th>Tanggal</th>
                        <th>Pegawai</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Item</th>
                        <th class="text-center">Qty Diminta</th>
                        <th class="text-center">Qty Disetujui</th>
                        <th class="text-center">Qty Dikirim</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <?php
                        $status_class = '';
                        $status_label = '';
                        switch ($row['status']) {
                            case 'pending':
                                $status_class = 'warning';
                                $status_label = 'Menunggu';
                                break;
                            case 'approved':
                                $status_class = 'info';
                                $status_label = 'Disetujui';
                                break;
                            case 'rejected':
                                $status_class = 'danger';
                                $status_label = 'Ditolak';
                                break;
                            case 'delivered':
                                $status_class = 'success';
                                $status_label = 'Dikirim';
                                break;
                            case 'cancelled':
                                $status_class = 'secondary';
                                $status_label = 'Dibatalkan';
                                break;
                        }
                        ?>
                        <tr>
                            <td><strong><?= $row['request_no'] ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                            <td><?= htmlspecialchars($row['nama'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['nip'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['jabatan'] ?? '-') ?></td>
                            <td>
                                <span class="status-badge <?= $status_class ?>"><?= $status_label ?></span>
                            </td>
                            <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
                            <td class="text-center"><?= $row['qty_requested'] ?></td>
                            <td class="text-center"><?= $row['qty_approved'] ?></td>
                            <td class="text-center"><?= $row['qty_delivered'] ?></td>
                            <td><?= htmlspecialchars($row['item_note'] ?? '') ?></td>
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

.filter-card { margin-bottom: 24px; }
.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px; }
.filter-actions { display: flex; gap: 12px; }
.form-group { margin-bottom: 0; }
.form-label { display: block; margin-bottom: 6px; font-weight: 500; color: #374151; font-size: 14px; }
.form-control { width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
.form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

.card-header { padding: 16px 20px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between; }
.card-title { font-size: 16px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px; }
.card-body { padding: 20px; }

.data-table-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; }
.table-info { font-size: 13px; color: var(--text-secondary); }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; white-space: nowrap; }
.data-table td { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; white-space: nowrap; }
.data-table tbody tr:hover { background: #f9fafb; }
.text-center { text-align: center; }

.status-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
.status-badge.warning { background: #fef3c7; color: #92400e; }
.status-badge.info { background: #dbeafe; color: #1e40af; }
.status-badge.success { background: #d1fae5; color: #065f46; }
.status-badge.danger { background: #fee2e2; color: #991b1b; }
.status-badge.secondary { background: #f3f4f6; color: #6b7280; }

.table-empty { text-align: center; padding: 48px 20px; color: #9ca3af; }
.table-empty i { font-size: 40px; margin-bottom: 12px; display: block; }
.table-empty p { font-size: 16px; font-weight: 500; margin-bottom: 4px; color: #374151; }
.text-muted { color: #9ca3af; font-size: 14px; }

.btn { padding: 10px 20px; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary:hover { background: #e5e7eb; }
.btn-success { background: #10b981; color: white; }
.btn-success:hover { background: #059669; }
</style>
