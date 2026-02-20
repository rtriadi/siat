<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Kelola Stok</h1>
            <p class="page-subtitle">Kelola inventori ATK tersedia</p>
        </div>
        <a href="<?= site_url('stock/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Tambah Item</span>
        </a>
    </div>
</div>

<div class="content">
    
    
    

    <?php if (empty($items)): ?>
    <div class="card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Item Name</th>
                        <th style="width: 100px;">Satuan</th>
                        <th style="width: 120px;">Tersedia</th>
                        <th style="width: 120px;">Threshold</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6">
                            <div class="table-empty">
                                <div class="empty-icon">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <h3><?= !empty($search) ? 'Tidak ada hasil pencarian' : 'Belum ada item stok' ?></h3>
                                <p><?= !empty($search) ? 'Coba kata kunci lain' : 'Mulai dengan menambahkan item pertama ke inventori' ?></p>
                                <?php if (!empty($search)): ?>
                                <a href="<?= site_url('stock') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i>
                                    Lihat Semua
                                </a>
                                <?php else: ?>
                                <a href="<?= site_url('stock/create') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    Tambah Item Pertama
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    
    <div class="card">
        <div class="data-table-header">
            <div class="data-table-search">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari item..." value="<?= htmlspecialchars($search) ?>">
                <?php if (!empty($search)): ?>
                <button class="search-clear" onclick="clearSearch()">
                    <i class="fas fa-times"></i>
                </button>
                <?php endif; ?>
            </div>
            <div class="table-info">
                Menampilkan <?= $start_row ?> - <?= $end_row ?> dari <?= $total_rows ?> data
            </div>
        </div>
        
        <?php foreach ($grouped_items as $category_name => $items): ?>
        <div class="category-section">
            <div class="category-header">
                <h3 class="category-title">
                    <i class="fas fa-folder"></i>
                    <?= htmlspecialchars($category_name) ?>
                </h3>
                <span class="category-count"><?= count($items) ?> item</span>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Item Name</th>
                            <th style="width: 100px;">Satuan</th>
                            <th style="width: 120px;">Tersedia</th>
                            <th style="width: 120px;">Threshold</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <span class="item-code"><?= htmlspecialchars($item['item_code'] ?? '-') ?></span>
                            </td>
                            <td>
                                <div class="item-name">
                                    <i class="fas fa-box"></i>
                                    <?= htmlspecialchars($item['item_name']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="unit-badge"><?= htmlspecialchars($item['unit'] ?? 'Pcs') ?></span>
                            </td>
                            <td>
                                <span class="qty"><?= number_format($item['available_qty']) ?></span>
                            </td>
                            <td>
                                <span class="threshold"><?= number_format($item['low_stock_threshold']) ?></span>
                            </td>
                            <td>
                                <?php if ($item['available_qty'] <= $item['low_stock_threshold']): ?>
                                <span class="status-badge danger">Habis</span>
                                <?php elseif ($item['available_qty'] <= $item['low_stock_threshold'] * 1.5): ?>
                                <span class="status-badge warning">Menipis</span>
                                <?php else: ?>
                                <span class="status-badge success">Tersedia</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= site_url('stock/edit/' . $item['id_item']) ?>" class="btn btn-sm btn-outline" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if ($total_pages > 1): ?>
        <div class="data-table-footer">
            <div></div>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="page-btn" title="First">
                    <i class="fas fa-angle-double-left"></i>
                </a>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page - 1])) ?>" class="page-btn">
                    <i class="fas fa-angle-left"></i>
                </a>
                <?php endif; ?>
                
                <?php
                $start = max(1, $current_page - 2);
                $end = min($total_pages, $current_page + 2);
                for ($i = $start; $i <= $end; $i++):
                ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="page-btn <?= $i == $current_page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page + 1])) ?>" class="page-btn">
                    <i class="fas fa-angle-right"></i>
                </a>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" class="page-btn" title="Last">
                    <i class="fas fa-angle-double-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <div class="per-page">
                <select onchange="window.location.href='?<?= http_build_query(array_merge($_GET, ['per_page' => '', 'page' => 1])) ?>'.replace('per_page=','per_page='+this.value)">
                    <option value="10" <?= $per_page == 10 ? 'selected' : '' ?>>10</option>
                    <option value="25" <?= $per_page == 25 ? 'selected' : '' ?>>25</option>
                    <option value="50" <?= $per_page == 50 ? 'selected' : '' ?>>50</option>
                </select>
                <span>/ halaman</span>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
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
</script>

<style>
.page-header { margin-bottom: 24px; }
.page-header-content { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
.page-title { font-size: 28px; font-weight: 700; margin: 0; }
.page-subtitle { font-size: 14px; color: var(--text-secondary); margin: 4px 0 0; }

.unit-badge { font-size: 12px; color: #6b7280; background: #f3f4f6; padding: 2px 8px; border-radius: 4px; border: 1px solid #e5e7eb; }

.alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.alert-success i, .alert-danger i { font-size: 18px; }
.alert .close { background: none; border: none; font-size: 20px; cursor: pointer; margin-left: auto; opacity: 0.7; }

.empty-state, .table-empty { text-align: center; padding: 60px 20px; }
.table-empty td { padding: 40px 20px !important; }
.empty-icon { width: 80px; height: 80px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
.empty-icon i { font-size: 36px; color: #3b82f6; }
.empty-state h3, .table-empty h3 { font-size: 20px; font-weight: 600; margin-bottom: 8px; color: #1f2937; }
.empty-state p, .table-empty p { color: var(--text-secondary); margin-bottom: 24px; }

.data-table-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; gap: 16px; flex-wrap: wrap; }
.data-table-search { position: relative; flex: 1; max-width: 400px; }
.data-table-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.data-table-search input { width: 100%; padding: 10px 36px 10px 40px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
.data-table-search input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.search-clear { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; }
.table-info { font-size: 13px; color: var(--text-secondary); }

.category-section { border-bottom: 1px solid #e5e7eb; }
.category-section:last-child { border-bottom: none; }
.category-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: #f9fafb; }
.category-title { font-size: 14px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px; }
.category-title i { color: #f59e0b; }
.category-count { font-size: 12px; color: var(--text-secondary); background: #e5e7eb; padding: 4px 10px; border-radius: 12px; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #6b7280; background: white; border-bottom: 1px solid #e5e7eb; }
.data-table td { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
.data-table tbody tr:hover { background: #f9fafb; }

.item-name { display: flex; align-items: center; gap: 10px; font-weight: 500; }
.item-name i { color: #6b7280; }
.item-code { font-weight: 600; color: #6b7280; font-size: 13px; }
.qty { font-weight: 600; }
.threshold { color: var(--text-secondary); }

.status-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.status-badge.success { background: #d1fae5; color: #065f46; }
.status-badge.warning { background: #fef3c7; color: #92400e; }
.status-badge.danger { background: #fee2e2; color: #991b1b; }

.action-buttons { display: flex; gap: 8px; }
.btn-outline { padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; background: white; color: #6b7280; cursor: pointer; transition: all 0.15s; }
.btn-outline:hover { background: #f3f4f6; color: #3b82f6; border-color: #3b82f6; }

.data-table-footer { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid #e5e7eb; flex-wrap: wrap; gap: 16px; }
.pagination { display: flex; gap: 4px; }
.page-btn { min-width: 36px; height: 36px; padding: 0 10px; border: 1px solid #e5e7eb; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 13px; color: #374151; text-decoration: none; transition: all 0.15s; }
.page-btn:hover { background: #f3f4f6; }
.page-btn.active { background: #3b82f6; border-color: #3b82f6; color: white; }
.per-page { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary); }
.per-page select { padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; }

.btn { padding: 10px 20px; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary:hover { background: #e5e7eb; }
</style>
