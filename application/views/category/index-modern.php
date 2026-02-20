<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Kelola Kategori</h1>
            <p class="page-subtitle">Kelola kategori item inventori</p>
        </div>
        <a href="<?= site_url('category/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Tambah Kategori</span>
        </a>
    </div>
</div>

<div class="content">
    

    

    <div class="card">
        <div class="data-table-header">
            <div class="data-table-search">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari kategori..." value="<?= htmlspecialchars($search) ?>">
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
        
        <?php if (empty($categories)): ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3">
                            <div class="table-empty">
                                <i class="fas fa-folder-open"></i>
                                <p><?= !empty($search) ? 'Tidak ada hasil pencarian' : 'Belum ada kategori' ?></p>
                                <?php if (empty($search)): ?>
                                <a href="<?= site_url('category/create') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus"></i>
                                    <span>Tambah Kategori</span>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <div class="category-name">
                                <i class="fas fa-folder" style="color: #f59e0b;"></i>
                                <?= htmlspecialchars($category['category_name']) ?>
                            </div>
                        </td>
                        <td><?= !empty($category['description']) ? htmlspecialchars($category['description']) : '<span class="text-muted">-</span>' ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="<?= site_url('category/edit/' . $category['id_category']) ?>" 
                                   class="btn btn-sm btn-outline" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        title="Hapus"
                                        onclick="confirmDelete(<?= $category['id_category'] ?>, '<?= htmlspecialchars($category['category_name']) ?>')">
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
        
        <?php if ($total_pages > 1): ?>
        <div class="data-table-footer">
            <div></div>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="page-btn">
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
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" class="page-btn">
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
</div>

<!-- Delete Confirmation Modal -->
<div class="custom-modal" id="deleteModal">
    <div class="custom-modal-overlay"></div>
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5>Hapus Kategori</h5>
            <button type="button" class="close-btn" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="custom-modal-body">
            <p>Apakah Anda yakin ingin menghapus kategori <strong id="deleteCategoryName"></strong>?</p>
        </div>
        <div class="custom-modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
            <form id="deleteForm" method="POST" action="<?= site_url('category/delete') ?>">
                <input type="hidden" name="id_category" id="deleteCategoryId" value="">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch(this.value);
    }, 500);
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

jQuery(document).ready(function() {
    jQuery('#deleteModal').hide();
});

function confirmDelete(id, name) {
    document.getElementById('deleteCategoryName').textContent = name;
    document.getElementById('deleteCategoryId').value = id;
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

.data-table-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; gap: 16px; flex-wrap: wrap; }
.data-table-search { position: relative; flex: 1; max-width: 400px; }
.data-table-search i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.data-table-search input { width: 100%; padding: 10px 36px 10px 40px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; }
.data-table-search input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.search-clear { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #9ca3af; cursor: pointer; }
.table-info { font-size: 13px; color: var(--text-secondary); }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
.data-table td { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
.data-table tbody tr:hover { background: #f9fafb; }

.category-name { display: flex; align-items: center; gap: 10px; font-weight: 500; }
.category-name i { color: #f59e0b; }
.text-muted { color: #9ca3af; }

.action-buttons { display: flex; gap: 8px; }
.btn-outline { padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; background: white; color: #6b7280; cursor: pointer; transition: all 0.15s; }
.btn-outline:hover { background: #f3f4f6; color: #3b82f6; border-color: #3b82f6; }
.btn-outline-danger { color: #dc3545; border-color: #dc3545; }
.btn-outline-danger:hover { background: #dc3545; color: white; }

.table-empty { text-align: center; padding: 48px 20px; color: #9ca3af; }
.table-empty i { font-size: 40px; margin-bottom: 12px; display: block; }
.table-empty p { margin-bottom: 16px; }

.data-table-footer { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-top: 1px solid #e5e7eb; flex-wrap: wrap; gap: 16px; }
.pagination { display: flex; gap: 4px; }
.page-btn { min-width: 36px; height: 36px; padding: 0 10px; border: 1px solid #e5e7eb; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 13px; color: #374151; text-decoration: none; transition: all 0.15s; }
.page-btn:hover { background: #f3f4f6; }
.page-btn.active { background: #3b82f6; border-color: #3b82f6; color: white; }
.per-page { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary); }
.per-page select { padding: 6px 10px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; }

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
.btn-sm { padding: 6px 12px; font-size: 13px; }
</style>
