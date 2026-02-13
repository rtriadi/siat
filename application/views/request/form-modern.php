<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Buat Permintaan ATK</h1>
            <p class="page-subtitle">Pilih item ATK yang dibutuhkan</p>
        </div>
    </div>
</div>

<div class="content">
    <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $this->session->flashdata('error') ?>
    </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
    <div class="card">
        <div class="card-body">
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3>Stok tidak tersedia</h3>
                <p>Saat ini belum ada item ATK yang tersedia untuk diminta. Hubungi administrator untuk menambahkan stok.</p>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="card-body">
            <form action="<?= site_url('request/store') ?>" method="post" class="request-form">
                <div class="search-filter-bar">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="itemSearch" placeholder="Cari item..." autocomplete="off">
                    </div>
                    <div class="filter-box">
                        <select id="categoryFilter" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php 
                            $categories = [];
                            foreach ($items as $item) {
                                $cat = !empty($item['category_name']) ? $item['category_name'] : 'Tanpa Kategori';
                                if (!in_array($cat, $categories)) {
                                    $categories[] = $cat;
                                    echo '<option value="' . htmlspecialchars($cat) . '">' . htmlspecialchars($cat) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="items-grid" id="itemsGrid">
                    <?php 
                    $grouped_items = [];
                    foreach ($items as $item) {
                        $cat = !empty($item['category_name']) ? $item['category_name'] : 'Tanpa Kategori';
                        if (!isset($grouped_items[$cat])) {
                            $grouped_items[$cat] = [];
                        }
                        $grouped_items[$cat][] = $item;
                    }
                    ?>
                    <?php foreach ($grouped_items as $category => $cat_items): ?>
                    <div class="item-category" data-category="<?= htmlspecialchars($category) ?>">
                        <div class="category-header">
                            <i class="fas fa-folder"></i>
                            <?= htmlspecialchars($category) ?>
                            <span class="item-count"><?= count($cat_items) ?> item</span>
                        </div>
                        <div class="category-items">
                            <?php foreach ($cat_items as $item): ?>
                            <?php $item_id = (int) $item['id_item']; ?>
                            <div class="item-row" data-name="<?= strtolower(htmlspecialchars($item['item_name'])) ?>" data-code="<?= strtolower(htmlspecialchars($item['item_code'] ?? '')) ?>">
                                <div class="item-info">
                                    <span class="item-code"><?= htmlspecialchars($item['item_code'] ?? '-') ?></span>
                                    <span class="item-name"><?= htmlspecialchars($item['item_name']) ?></span>
                                    <span class="item-stock">Tersedia: <?= number_format($item['available_qty']) ?></span>
                                </div>
                                <div class="item-input">
                                    <input type="number" 
                                           name="qty_requested[<?= $item_id ?>]" 
                                           class="qty-input" 
                                           min="0" 
                                           max="<?= $item['available_qty'] ?>"
                                           placeholder="0"
                                           value="<?= isset($old_qtys[$item_id]) ? (int) $old_qtys[$item_id] : 0 ?>">
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="no-results" id="noResults" style="display: none;">
                    <i class="fas fa-search"></i>
                    <p>Tidak ada item yang sesuai dengan pencarian</p>
                </div>

                <div class="note-section">
                    <label for="note" class="form-label">Catatan (Opsional)</label>
                    <textarea class="form-control" id="note" name="note" rows="3" 
                              placeholder="Tambahkan catatan jika diperlukan…"><?= htmlspecialchars((string) $old_note, ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-actions">
                    <a href="<?= site_url('request') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('itemSearch').addEventListener('input', filterItems);
document.getElementById('categoryFilter').addEventListener('change', filterItems);

function filterItems() {
    const search = document.getElementById('itemSearch').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const categories = document.querySelectorAll('.item-category');
    const rows = document.querySelectorAll('.item-row');
    const noResults = document.getElementById('noResults');
    let visibleCount = 0;
    
    categories.forEach(cat => {
        const catName = cat.getAttribute('data-category');
        let catVisible = false;
        
        rows.forEach(row => {
            const rowCat = row.closest('.item-category').getAttribute('data-category');
            if (rowCat === catName) {
                const name = row.getAttribute('data-name');
                const code = row.getAttribute('data-code');
                const matchesSearch = search === '' || name.includes(search) || code.includes(search);
                const matchesCategory = category === '' || catName === category;
                
                if (matchesSearch && matchesCategory) {
                    row.style.display = 'flex';
                    catVisible = true;
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
        });
        
        cat.style.display = catVisible ? 'block' : 'none';
    });
    
    noResults.style.display = visibleCount === 0 ? 'block' : 'none';
}
</script>

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

.search-filter-bar { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.search-box { position: relative; flex: 1; min-width: 200px; }
.search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.search-box input { width: 100%; padding: 10px 14px 10px 40px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
.search-box input:focus { outline: none; border-color: #3b82f6; }
.filter-box { min-width: 180px; }
.filter-box .form-control { cursor: pointer; }

.items-grid { display: flex; flex-direction: column; gap: 12px; max-height: 450px; overflow-y: auto; padding-right: 8px; }
.items-grid::-webkit-scrollbar { width: 6px; }
.items-grid::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
.items-grid::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 3px; }

.item-category { border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
.category-header { background: #f9fafb; padding: 12px 16px; font-weight: 600; color: #374151; display: flex; align-items: center; gap: 8px; }
.category-header i { color: #f59e0b; }
.item-count { margin-left: auto; font-size: 12px; color: #6b7280; font-weight: normal; }
.category-items { padding: 8px; }

.item-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 6px; transition: background 0.15s; }
.item-row:hover { background: #f9fafb; }
.item-info { display: flex; flex-direction: column; gap: 2px; flex: 1; }
.item-code { font-size: 11px; color: #6b7280; font-weight: 500; }
.item-name { font-weight: 500; color: #1f2937; }
.item-stock { font-size: 12px; color: #10b981; }
.item-input { width: 100px; }
.qty-input { width: 100%; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; text-align: center; }
.qty-input:focus { outline: none; border-color: #3b82f6; }

.no-results { text-align: center; padding: 40px; color: #6b7280; }
.no-results i { font-size: 40px; margin-bottom: 12px; display: block; }

.note-section { margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb; }
.form-label { font-weight: 500; color: #374151; margin-bottom: 8px; display: block; }
.form-control { width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
.form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

.form-actions { display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; }

.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { width: 80px; height: 80px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
.empty-icon i { font-size: 36px; color: #3b82f6; }
.empty-state h3 { font-size: 20px; font-weight: 600; margin-bottom: 8px; color: #1f2937; }
.empty-state p { color: #6b7280; }

.btn { padding: 10px 20px; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary:hover { background: #e5e7eb; }
</style>
