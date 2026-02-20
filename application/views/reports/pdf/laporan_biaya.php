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
            <th>Tgl Permintaan</th>
            <th>Pegawai</th>
            <th>Jabatan</th>
            <th>Nama Barang</th>
            <th>Qty Diminta</th>
            <th>Qty Disetujui</th>
            <th>Qty Dikirim</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($report_data)): $no = 1; foreach ($report_data as $row): ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
            <td><?= htmlspecialchars($row['nama'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['jabatan'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
            <td class="text-center"><?= (int)$row['qty_requested'] ?></td>
            <td class="text-center"><?= (int)$row['qty_approved'] ?></td>
            <td class="text-center"><?= (int)$row['qty_delivered'] ?></td>
            <td class="text-center"><?= ucfirst($row['status']) ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="9" style="text-align:center; color:#999; padding:20px;">Tidak ada data</td></tr>
        <?php endif; ?>
    </tbody>
</table>
