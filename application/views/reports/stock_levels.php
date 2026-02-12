<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Stock Levels</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Stock Levels</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Filter Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Filter</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="get" action="<?= site_url('reports/stock_levels') ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="category_id">Kategori</label>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <a href="<?= site_url('reports/stock_levels') ?>" class="btn btn-default">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Current Stock Levels</h3>
                <div class="card-tools">
                    <a href="<?= site_url('reports/export_stock_levels?' . http_build_query($filters)) ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
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
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                                <?php
                                $available = (int)($row['available_qty'] ?? 0);
                                $threshold = (int)($row['low_stock_threshold'] ?? 0);
                                $is_low_stock = $available <= $threshold;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
                                    <td class="text-right"><?= number_format($row['available_qty'] ?? 0) ?></td>
                                    <td class="text-right"><?= number_format($row['reserved_qty'] ?? 0) ?></td>
                                    <td class="text-right"><?= number_format($row['used_qty'] ?? 0) ?></td>
                                    <td class="text-right"><?= number_format($row['low_stock_threshold'] ?? 0) ?></td>
                                    <td>
                                        <?php if ($is_low_stock): ?>
                                            <span class="badge badge-danger">Stok Menipis</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">OK</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
