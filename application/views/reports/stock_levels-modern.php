<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Level Stok</h1>
            <p class="page-subtitle">Kondisi stok terkini per kategori</p>
        </div>
        <div>
            <a href="<?= site_url('reports/export_stock_levels?' . http_build_query($filters)) ?>" class="btn btn-success">
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
            <form method="get" action="<?= site_url('reports/stock_levels') ?>" class="filter-form">
                <div class="filter-grid">
                    <div class="form-group">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id_category'] ?>" <?= isset($filters['category_id']) && $filters['category_id'] == $cat['id_category'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                    <a href="<?= site_url('reports/stock_levels') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="data-table-header">
            <div class="table-info">
                <?= count($rows) ?> item
            </div>
        </div>
        
        <?php if (empty($rows)): ?>
        <div class="table-empty">
            <i class="fas fa-inbox"></i>
            <p>Tidak ada data</p>
            <span class="text-muted">Tidak ada item stok yang sesuai dengan filter</span>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Item</th>
                        <th class="text-right">Tersedia</th>
                        <th class="text-right">Direservasi</th>
                        <th class="text-right">Digunakan</th>
                        <th class="text-right">Batas Minimum</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <?php
                        $available = (int)($row['available_qty'] ?? 0);
                        $threshold = (int)($row['low_stock_threshold'] ?? 0);
                        $is_low_stock = $available <= $threshold;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
                            <td class="text-right">
                                <span class="qty-value available"><?= number_format($row['available_qty'] ?? 0) ?></span>
                            </td>
                            <td class="text-right">
                                <span class="qty-value reserved"><?= number_format($row['reserved_qty'] ?? 0) ?></span>
                            </td>
                            <td class="text-right">
                                <span class="qty-value used"><?= number_format($row['used_qty'] ?? 0) ?></span>
                            </td>
                            <td class="text-right">
                                <span class="threshold-value"><?= number_format($row['low_stock_threshold'] ?? 0) ?></span>
                            </td>
                            <td>
                                <?php if ($is_low_stock): ?>
                                    <span class="status-badge danger">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Stok Menipis
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge success">
                                        <i class="fas fa-check-circle"></i>
                                        OK
                                    </span>
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
.text-right { text-align: right; }

.qty-value { font-weight: 600; }
.qty-value.available { color: #10b981; }
.qty-value.reserved { color: #f59e0b; }
.qty-value.used { color: #3b82f6; }
.threshold-value { color: #6b7280; }

.status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
.status-badge.success { background: #d1fae5; color: #065f46; }
.status-badge.danger { background: #fee2e2; color: #991b1b; }

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
