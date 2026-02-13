<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Buat Permintaan ATK</h1>
            <p class="page-subtitle">Buat permintaan ATK untuk karyawan</p>
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

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= site_url('request_admin/store') ?>" id="requestForm">
                <div class="form-section">
                    <h3 class="section-title">Informasi Karyawan</h3>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label for="user_id" class="form-label">Pilih Karyawan <span class="required">*</span></label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">-- Pilih Karyawan --</option>
                                <?php foreach ($employees as $emp): ?>
                                <option value="<?= $emp['id_user'] ?>">
                                    <?= htmlspecialchars($emp['nama']) ?> (<?= htmlspecialchars($emp['nip']) ?>) - <?= htmlspecialchars($emp['unit']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">Pilih Item ATK</h3>
                    
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
                                <div class="item-row" data-name="<?= strtolower(htmlspecialchars($item['item_name'])) ?>" data-code="<?= strtolower(htmlspecialchars($item['item_code'] ?? '')) ?>">
                                    <div class="item-info">
                                        <span class="item-code"><?= htmlspecialchars($item['item_code'] ?? '-') ?></span>
                                        <span class="item-name"><?= htmlspecialchars($item['item_name']) ?></span>
                                        <span class="item-stock">Tersedia: <?= number_format($item['available_qty']) ?></span>
                                    </div>
                                    <div class="item-input">
                                        <input type="number" 
                                               name="qty_requested[<?= $item['id_item'] ?>]" 
                                               class="qty-input" 
                                               min="0" 
                                               max="<?= $item['available_qty'] ?>"
                                               placeholder="0"
                                               data-max="<?= $item['available_qty'] ?>">
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
                </div>

                <div class="form-section">
                    <h3 class="section-title">Catatan</h3>
                    <div class="form-group">
                        <textarea name="note" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="<?= site_url('request_admin') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Simpan Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
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

.form-section { margin-bottom: 32px; }
.form-section:last-of-type { margin-bottom: 24px; }
.section-title { font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; }

.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
.form-group { display: flex; flex-direction: column; }
.form-group.full-width { grid-column: 1 / -1; }
.form-label { font-weight: 500; color: #374151; margin-bottom: 8px; font-size: 14px; }
.required { color: #dc3545; }
.form-control { padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; transition: border-color 0.15s, box-shadow 0.15s; }
.form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

.search-filter-bar { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.search-box { position: relative; flex: 1; min-width: 200px; }
.search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.search-box input { width: 100%; padding: 10px 14px 10px 40px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
.search-box input:focus { outline: none; border-color: #3b82f6; }
.filter-box { min-width: 180px; }
.filter-box .form-control { cursor: pointer; }

.items-grid { display: flex; flex-direction: column; gap: 12px; max-height: 500px; overflow-y: auto; padding-right: 8px; }
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

.form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #e5e7eb; }

.btn { padding: 10px 20px; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary:hover { background: #e5e7eb; }
</style>
