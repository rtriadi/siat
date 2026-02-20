<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
table { width: 100%; border-collapse: collapse; font-size: 12px; font-family: Arial, sans-serif; }
th { background: #1e3a5f; color: white; padding: 8px; text-align: center; }
td { padding: 6px 8px; border: 1px solid #ccc; vertical-align: top; }
tr:nth-child(even) { background: #f5f5f5; }
.text-right { text-align: right; }
.text-center { text-align: center; }
</style>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Kebutuhan / Thn</th>
            <th>Harga Satuan</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($report_data)): $no = 1; $grand_total = 0; foreach ($report_data as $row): 
            $total = $row['total_qty'] * ($row['price_per_unit'] ?? 0);
            $grand_total += $total;
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['item_code'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
            <td class="text-center"><?= htmlspecialchars($row['unit'] ?? 'Pcs') ?></td>
            <td class="text-center"><?= (int)($row['total_qty'] ?? 0) ?></td>
            <td class="text-right">Rp <?= number_format($row['price_per_unit'] ?? 0, 0, ',', '.') ?></td>
            <td class="text-right">Rp <?= number_format($total, 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="6" style="text-align:right; font-weight:bold;">Grand Total</td>
            <td class="text-right" style="font-weight:bold;">Rp <?= number_format($grand_total, 0, ',', '.') ?></td>
        </tr>
        <?php else: ?>
        <tr><td colspan="7" style="text-align:center; color:#999; padding:20px;">Tidak ada data</td></tr>
        <?php endif; ?>
    </tbody>
</table>
