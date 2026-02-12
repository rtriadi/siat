<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Audit Trail</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Audit Trail</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card card-primary collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filter</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="get" action="<?= site_url('reports/audit_trail') ?>">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_start">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="date_start" name="date_start" 
                                       value="<?= isset($filters['date_start']) ? $filters['date_start'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_end">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="date_end" name="date_end" 
                                       value="<?= isset($filters['date_end']) ? $filters['date_end'] : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="category_id">Kategori</label>
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
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="item_id">Item</label>
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
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="<?= site_url('reports/audit_trail') ?>" class="btn btn-secondary">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-shield-alt mr-2"></i>Log Audit Perubahan Stok
                    <?php if (!empty($rows)): ?>
                        <span class="badge badge-info ml-2"><?= count($rows) ?> baris</span>
                    <?php endif; ?>
                </h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Item</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                            <th class="text-center">Qty</th>
                            <th>Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    Tidak ada data audit trail
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                                <?php
                                $type_class = '';
                                $type_label = '';
                                switch ($row['movement_type']) {
                                    case 'in':
                                        $type_class = 'badge-success';
                                        $type_label = 'Masuk';
                                        break;
                                    case 'out':
                                        $type_class = 'badge-danger';
                                        $type_label = 'Keluar';
                                        break;
                                    case 'adjust':
                                        $type_class = 'badge-warning';
                                        $type_label = 'Penyesuaian';
                                        break;
                                    case 'reserve':
                                        $type_class = 'badge-info';
                                        $type_label = 'Reservasi';
                                        break;
                                    case 'cancel':
                                        $type_class = 'badge-secondary';
                                        $type_label = 'Batal Reservasi';
                                        break;
                                    case 'deliver':
                                        $type_class = 'badge-primary';
                                        $type_label = 'Pengiriman';
                                        break;
                                }
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i:s', strtotime($row['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($row['user_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                                    <td><span class="badge <?= $type_class ?>"><?= $type_label ?></span></td>
                                    <td class="text-center"><?= $row['qty_delta'] ?></td>
                                    <td><?= htmlspecialchars($row['reason'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
