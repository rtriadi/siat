<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Request History</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Request History</li>
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
                <form method="get" action="<?= site_url('reports/request_history') ?>">
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
                                <label for="user_id">Pegawai</label>
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
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="status">Status</label>
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
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Filter
                            </button>
                            <a href="<?= site_url('reports/request_history') ?>" class="btn btn-secondary">
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
                    <i class="fas fa-list mr-2"></i>Daftar Request
                    <?php if (!empty($rows)): ?>
                        <span class="badge badge-info ml-2"><?= count($rows) ?> baris</span>
                    <?php endif; ?>
                </h3>
                <div class="card-tools">
                    <?php
                    $export_params = '';
                    if (!empty($filters)) {
                        $export_params = '?' . http_build_query($filters);
                    }
                    ?>
                    <a href="<?= site_url('reports/export_request_history' . $export_params) ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>No. Request</th>
                            <th>Tanggal</th>
                            <th>Pegawai</th>
                            <th>NIP</th>
                            <th>Unit</th>
                            <th>Status</th>
                            <th>Item</th>
                            <th class="text-center">Qty Diminta</th>
                            <th class="text-center">Qty Disetujui</th>
                            <th class="text-center">Qty Dikirim</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                    Tidak ada data request
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                                <?php
                                $status_class = '';
                                $status_label = '';
                                switch ($row['status']) {
                                    case 'pending':
                                        $status_class = 'badge-warning';
                                        $status_label = 'Menunggu';
                                        break;
                                    case 'approved':
                                        $status_class = 'badge-info';
                                        $status_label = 'Disetujui';
                                        break;
                                    case 'rejected':
                                        $status_class = 'badge-danger';
                                        $status_label = 'Ditolak';
                                        break;
                                    case 'delivered':
                                        $status_class = 'badge-success';
                                        $status_label = 'Dikirim';
                                        break;
                                    case 'cancelled':
                                        $status_class = 'badge-secondary';
                                        $status_label = 'Dibatalkan';
                                        break;
                                }
                                ?>
                                <tr>
                                    <td><strong><?= $row['request_no'] ?></strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($row['nama'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['nip'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['unit'] ?? '-') ?></td>
                                    <td><span class="badge <?= $status_class ?>"><?= $status_label ?></span></td>
                                    <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
                                    <td class="text-center"><?= $row['qty_requested'] ?></td>
                                    <td class="text-center"><?= $row['qty_approved'] ?></td>
                                    <td class="text-center"><?= $row['qty_delivered'] ?></td>
                                    <td><?= htmlspecialchars($row['item_note'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
