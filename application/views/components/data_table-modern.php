<div class="data-table-container">
    <div class="data-table-header">
        <div class="data-table-search">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari..." value="<?= !empty($search) ? htmlspecialchars($search) : '' ?>">
            <?php if (!empty($search)): ?>
            <button class="search-clear" onclick="clearSearch()" title="Clear search">
                <i class="fas fa-times"></i>
            </button>
            <?php endif; ?>
        </div>
        <?php if (!empty($extra_filters)): ?>
        <div class="data-table-filters">
            <?= $extra_filters ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="data-table-wrapper">
        <table class="data-table" id="dataTable">
            <thead>
                <tr>
                    <?php foreach ($columns as $column): ?>
                    <th <?= !empty($column['width']) ? 'style="width: ' . $column['width'] . '"' : '' ?>>
                        <?= $column['label'] ?>
                    </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="<?= count($columns) ?>" class="text-center">
                        <div class="table-empty">
                            <i class="fas fa-inbox"></i>
                            <p><?= !empty($empty_message) ? $empty_message : 'Tidak ada data' ?></p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <?php foreach ($columns as $column): ?>
                    <td>
                        <?php if (!empty($column['render'])): ?>
                        <?= call_user_func($column['render'], $row) ?>
                        <?php elseif (!empty($column['html'])): ?>
                        <?= $column['html'] ?>
                        <?php else: ?>
                        <?= !empty($row[$column['name']]) ? htmlspecialchars($row[$column['name']]) : '-' ?>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($total_pages > 1): ?>
    <div class="data-table-footer">
        <div class="data-table-info">
            Menampilkan <?= $start_row ?> - <?= $end_row ?> dari <?= $total_rows ?> data
        </div>
        <div class="data-table-pagination">
            <?php if ($current_page > 1): ?>
            <a href="<?= build_pagination_url(1) ?>" class="page-btn" title="First">
                <i class="fas fa-angle-double-left"></i>
            </a>
            <a href="<?= build_pagination_url($current_page - 1) ?>" class="page-btn" title="Previous">
                <i class="fas fa-angle-left"></i>
            </a>
            <?php endif; ?>
            
            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            if ($start_page > 1): ?>
            <a href="<?= build_pagination_url(1) ?>" class="page-btn">1</a>
            <?php if ($start_page > 2): ?>
            <span class="page-ellipsis">...</span>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="<?= build_pagination_url($i) ?>" class="page-btn <?= $i == $current_page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($end_page < $total_pages): ?>
            <?php if ($end_page < $total_pages - 1): ?>
            <span class="page-ellipsis">...</span>
            <?php endif; ?>
            <a href="<?= build_pagination_url($total_pages) ?>" class="page-btn"><?= $total_pages ?></a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="<?= build_pagination_url($current_page + 1) ?>" class="page-btn" title="Next">
                <i class="fas fa-angle-right"></i>
            </a>
            <a href="<?= build_pagination_url($total_pages) ?>" class="page-btn" title="Last">
                <i class="fas fa-angle-double-right"></i>
            </a>
            <?php endif; ?>
        </div>
        <div class="data-table-perpage">
            <select id="perPageSelect" onchange="changePerPage(this.value)">
                <option value="10" <?= $per_page == 10 ? 'selected' : '' ?>>10</option>
                <option value="25" <?= $per_page == 25 ? 'selected' : '' ?>>25</option>
                <option value="50" <?= $per_page == 50 ? 'selected' : '' ?>>50</option>
                <option value="100" <?= $per_page == 100 ? 'selected' : '' ?>>100</option>
            </select>
            <span>/ halaman</span>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function buildPaginationUrl(page) {
    var url = new URL(window.location.href);
    url.searchParams.set('page', page);
    return url.toString();
}

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

function changePerPage(value) {
    var url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}
</script>

<style>
.data-table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    overflow: hidden;
}

.data-table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    gap: 16px;
    flex-wrap: wrap;
}

.data-table-search {
    position: relative;
    flex: 1;
    max-width: 400px;
}

.data-table-search i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 14px;
}

.data-table-search input {
    width: 100%;
    padding: 10px 36px 10px 40px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.data-table-search input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px;
}

.search-clear:hover {
    color: #6b7280;
}

.data-table-filters {
    display: flex;
    gap: 12px;
    align-items: center;
}

.data-table-wrapper {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #f9fafb;
    padding: 14px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
    white-space: nowrap;
}

.data-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
    color: #374151;
}

.data-table tbody tr {
    transition: background 0.15s;
}

.data-table tbody tr:hover {
    background: #f9fafb;
}

.data-table tbody tr:last-child td {
    border-bottom: none;
}

.text-center {
    text-align: center;
}

.table-empty {
    text-align: center;
    padding: 48px 20px;
    color: #9ca3af;
}

.table-empty i {
    font-size: 40px;
    margin-bottom: 12px;
    display: block;
}

.table-empty p {
    font-size: 14px;
    margin: 0;
}

.data-table-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
    gap: 16px;
}

.data-table-info {
    font-size: 13px;
    color: #6b7280;
}

.data-table-pagination {
    display: flex;
    align-items: center;
    gap: 4px;
}

.page-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 10px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 13px;
    color: #374151;
    text-decoration: none;
    transition: all 0.15s;
}

.page-btn:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.page-btn.active {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.page-ellipsis {
    padding: 0 8px;
    color: #9ca3af;
}

.data-table-perpage {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #6b7280;
}

.data-table-perpage select {
    padding: 6px 10px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 13px;
    color: #374151;
    background: white;
    cursor: pointer;
}

.data-table-perpage select:focus {
    outline: none;
    border-color: #3b82f6;
}

@media (max-width: 768px) {
    .data-table-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .data-table-search {
        max-width: none;
    }
    
    .data-table-footer {
        flex-direction: column;
        align-items: center;
    }
}
</style>
