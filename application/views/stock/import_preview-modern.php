<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Preview Import</h1>
            <p class="page-subtitle">Review data sebelum melakukan import</p>
        </div>
    </div>
</div>

<div class="content">
    <div class="card">
        <div class="card-body">
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Terdapat error:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>

            <div class="summary-stats">
                <div class="stat-item">
                    <span class="stat-label">Total Valid</span>
                    <span class="stat-value"><?= (int) $valid_count ?> baris</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Barang Baru</span>
                    <span class="stat-value success"><?= (int) $create_count ?> dibuat</span>
                </div>
                <div class="stat-item">
                    <span class="stat-label">Stok Ditambah</span>
                    <span class="stat-value info"><?= (int) $update_count ?> diperbarui</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Baris</th>
                            <th>Kategori</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                            <th>Stok Min</th>
                            <th>Aksi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr class="<?= !empty($row['errors']) ? 'error-row' : '' ?>">
                                <td><?= (int) $row['row'] ?></td>
                                <td><?= htmlspecialchars($row['category_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= (int) $row['qty'] ?></td>
                                <td><?= htmlspecialchars($row['satuan'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= (int) $row['min_stock'] ?></td>
                                <td>
                                    <?php if ($row['action'] === 'create'): ?>
                                        <span class="badge badge-info">Baru</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">+<?= (int) $row['qty'] ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['errors'])): ?>
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i>
                                            Invalid
                                        </span>
                                        <div class="error-text">
                                            <?= implode(', ', $row['errors']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i>
                                            OK
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <a href="<?= site_url('stock_import/import') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
                <form action="<?= site_url('stock_import/import_commit') ?>" method="post" style="display: inline;">
                    <button type="submit" class="btn btn-primary" <?= $valid_count === 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-file-import"></i>
                        Import Data
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.page-header { margin-bottom: 24px; }
.page-header-content { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
.page-title { font-size: 28px; font-weight: 700; margin: 0; }
.page-subtitle { font-size: 14px; color: var(--text-secondary); margin: 4px 0 0; }

.alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 10px; }
.alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

.summary-stats { display: flex; gap: 24px; margin-bottom: 20px; padding: 16px; background: #f9fafb; border-radius: 8px; flex-wrap: wrap; }
.stat-item { display: flex; align-items: center; gap: 10px; }
.stat-label { font-size: 14px; color: var(--text-secondary); }
.stat-value { font-size: 18px; font-weight: 600; }
.stat-value.success { color: #10b981; }
.stat-value.info { color: #3b82f6; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
.data-table td { padding: 12px 14px; border-bottom: 1px solid #f3f4f6; font-size: 14px; vertical-align: middle; }
.data-table tbody tr:hover { background: #f9fafb; }
.data-table tbody tr.error-row { background: rgba(239, 68, 68, 0.05); }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-success { background: #d1fae5; color: #065f46; }
.badge-danger { background: #fee2e2; color: #991b1b; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-info { background: #dbeafe; color: #1e40af; }
.error-text { font-size: 11px; color: #dc3545; margin-top: 4px; }

.form-actions { display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
.form-actions .btn:disabled { opacity: 0.5; cursor: not-allowed; }

.btn { padding: 10px 20px; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary:hover { background: #e5e7eb; }
</style>
