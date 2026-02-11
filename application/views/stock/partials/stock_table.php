<div class="table-responsive mb-4">
    <table class="table table-bordered table-hover">
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
                    <td><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-center"><?= (int)$item['available_qty'] ?></td>
                    <td class="text-center"><?= (int)$item['reserved_qty'] ?></td>
                    <td class="text-center"><?= (int)$item['used_qty'] ?></td>
                    <td class="text-center"><strong><?= (int)$total_qty ?></strong></td>
                    <td class="text-center">
                        <?php if ($is_low_stock): ?>
                            <span class="badge badge-warning">
                                <i class="fas fa-exclamation-triangle"></i> Stok Rendah
                            </span>
                        <?php else: ?>
                            <span class="badge badge-success">
                                <i class="fas fa-check-circle"></i> Normal
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="<?= site_url('stock/edit/' . $item['id_item']) ?>" class="btn btn-sm btn-info">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
