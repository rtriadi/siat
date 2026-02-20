<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
table { width: 100%; border-collapse: collapse; font-size: 11px; font-family: Arial, sans-serif; }
th { background: #1e3a5f; color: white; padding: 7px; text-align: center; }
td { padding: 6px 8px; border: 1px solid #ccc; vertical-align: top; }
tr:nth-child(even) { background: #f5f5f5; }
.text-right { text-align: right; }
.text-center { text-align: center; }
</style>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>No. Request</th>
            <th>Pegawai</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Qty Diterima</th>
            <th>Ket</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($report_data)): $no = 1; foreach ($report_data as $row): ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
            <td><?= htmlspecialchars($row['request_no'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['nama'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
            <td class="text-center"><?= htmlspecialchars($row['satuan'] ?? 'Pcs') ?></td>
            <td class="text-center"><?= (int)($row['qty_delivered'] ?? 0) ?></td>
            <td></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="8" style="text-align:center; color:#999; padding:20px;">Tidak ada data</td></tr>
        <?php endif; ?>
    </tbody>
</table>
