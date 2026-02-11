<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Baris</th>
                <th>Item ID</th>
                <th>Kategori</th>
                <th>Nama Item</th>
                <th>Qty</th>
                <th>Note</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int) $row['row'] ?></td>
                    <td><?= htmlspecialchars($row['item_id'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['qty'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($row['note'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if (!empty($row['errors'])): ?>
                            <span class="badge badge-danger">Invalid</span>
                            <br><small class="text-danger"><?= implode(', ', $row['errors']) ?></small>
                        <?php else: ?>
                            <span class="badge badge-success">Valid</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
