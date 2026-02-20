<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Pergerakan Stok</h1>
            <p class="page-subtitle">Pergerakan stok masuk dan keluar</p>
        </div>
        <div>
            <?php
            $export_params = '';
            if (!empty($filters)) {
                $export_params = '?' . http_build_query($filters);
            }
            ?>
            <a href="<?= site_url('reports/export_stock_movement' . $export_params) ?>" class="btn btn-success">
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
            <form method="get" action="<?= site_url('reports/stock_movement') ?>" class="filter-form">
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
                        <label for="category_id" class="form-label">Kategori</label>
                        <select class="form-control" id="category_id" name="category_id">
                            <option value="">-- Semua Kategori --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id_category'] ?>" 
                                    <?= isset($filters['category_id']) && $filters['category_id'] == $category['id_category'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="item_id" class="form-label">Item</label>
                        <select class="form-control" id="item_id" name="item_id">
                            <option value="">-- Semua Item --</option>
                            <?php foreach ($items as $item): ?>
                                <option value="<?= $item['id_item'] ?>" 
                                    <?= isset($filters['item_id']) && $filters['item_id'] == $item['id_item'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($item['item_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Filter
                    </button>
                    <a href="<?= site_url('reports/stock_movement') ?>" class="btn btn-secondary">
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
            <span class="text-muted">Tidak ada pergerakan stok yang sesuai dengan filter</span>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th class="text-center">Qty</th>
                        <th>Reason</th>
                        <th>User</th>
                        <th class="text-center">Running Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <?php
                        $type_class = '';
                        $type_label = '';
                        switch ($row['movement_type']) {
                            case 'in':
                                $type_class = 'success';
                                $type_label = 'Masuk';
                                break;
                            case 'out':
                            case 'reserve':
                            case 'deliver':
                                $type_class = 'danger';
                                $type_label = 'Keluar';
                                break;
                            case 'cancel':
                                $type_class = 'warning';
                                $type_label = 'Batal';
                                break;
                            case 'adjust':
                                $type_class = 'info';
                                $type_label = 'Penyesuaian';
                                break;
                            default:
                                // Fallback: guess from reason text
                                $reason = strtolower($row['reason'] ?? '');
                                if (strpos($reason, 'reservasi') !== false || strpos($reason, 'pengiriman') !== false) {
                                    $type_class = 'danger';
                                    $type_label = 'Keluar';
                                } else {
                                    $type_class = 'success';
                                    $type_label = 'Masuk';
                                }
                        }
                        ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                            <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                            <td>
                                <span class="type-badge <?= $type_class ?>"><?= $type_label ?></span>
                            </td>
                            <td class="text-center">
                                <span class="qty-value <?= $row['movement_type'] === 'in' ? 'positive' : ($row['movement_type'] === 'out' ? 'negative' : '') ?>">
                                    <?= $row['qty_delta'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['reason'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['user_name'] ?? '-') ?></td>
                            <td class="text-center">
                                <strong><?= $row['running_balance'] ?></strong>
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
.text-center { text-align: center; }

.type-badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 500; }
.type-badge.success { background: #d1fae5; color: #065f46; }
.type-badge.danger { background: #fee2e2; color: #991b1b; }
.type-badge.warning { background: #fef3c7; color: #92400e; }
.type-badge.info { background: #dbeafe; color: #1e40af; }
.type-badge.primary { background: #ede9fe; color: #7c3aed; }
.type-badge.secondary { background: #f3f4f6; color: #6b7280; }

.qty-value.positive { color: #10b981; font-weight: 600; }
.qty-value.negative { color: #ef4444; font-weight: 600; }

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
