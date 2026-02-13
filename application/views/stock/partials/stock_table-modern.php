<div class="table-container">
    <table class="table table-modern">
        <thead>
            <tr>
                <th>Nama Item</th>
                <th class="text-center">Tersedia</th>
                <th class="text-center">Direservasi</th>
                <th class="text-center">Terpakai</th>
                <th class="text-center">Total</th>
                <th class="text-center">Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <?php
                $total_qty = $item['available_qty'] + $item['reserved_qty'] + $item['used_qty'];
                $is_low_stock = $item['available_qty'] <= $item['low_stock_threshold'];
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td class="text-center">
                        <span class="qty-badge <?= $is_low_stock ? 'low' : 'normal' ?>">
                            <?= (int)$item['available_qty'] ?>
                        </span>
                    </td>
                    <td class="text-center"><?= (int)$item['reserved_qty'] ?></td>
                    <td class="text-center"><?= (int)$item['used_qty'] ?></td>
                    <td class="text-center"><strong><?= (int)$total_qty ?></strong></td>
                    <td class="text-center">
                        <?php if ($is_low_stock): ?>
                            <span class="badge badge-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Stok Rendah
                            </span>
                        <?php else: ?>
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i>
                                Normal
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="<?= site_url('stock/edit/' . $item['id_item']) ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-edit"></i>
                            Edit
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
    .table-modern { width: 100%; }
    .table-modern th { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); background: var(--surface-elevated); padding: 14px 16px; }
    .table-modern td { padding: 14px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .table-modern tbody tr { transition: background 0.15s; }
    .table-modern tbody tr:hover { background: var(--surface-elevated); }
    .qty-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; }
    .qty-badge.normal { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .qty-badge.low { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
</style>
