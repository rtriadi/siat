<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (isset($is_print) && $is_print): ?>
    <?php $this->load->view('reports/pdf/report_header'); ?>
<?php endif; ?>

<div class="report-title-container">
    <h2 class="report-main-title">LAPORAN RINCIAN PERMINTAAN DAN PENGELUARAN ATK</h2>
    <h3 class="report-sub-title">PENGADILAN AGAMA GORONTALO</h3>
    <p class="report-period">
        <?php if ($period_type == 'monthly'): ?>
            BULAN <?= bulanIndo($month) ?> <?= $year ?>
        <?php elseif ($period_type == 'yearly'): ?>
            TAHUN <?= $year ?>
        <?php elseif ($period_type == 'range'): ?>
            PERIODE <?= date('d/m/Y', strtotime($date_start)) ?> s/d <?= date('d/m/Y', strtotime($date_end)) ?>
        <?php endif; ?>
    </p>
</div>

<style>
    .report-title-container { text-align: center; margin-bottom: 20px; }
    .report-main-title { font-size: 14px; font-weight: bold; margin: 0 0 4px 0; text-transform: uppercase; }
    .report-sub-title { font-size: 14px; font-weight: bold; margin: 0 0 4px 0; text-transform: uppercase; }
    .report-period { font-size: 12px; font-weight: bold; margin: 0; text-transform: uppercase; }

    .data-table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 9px; }
    .data-table th { border: 1px solid #000; padding: 6px 3px; background: #f0f0f0; text-align: center; font-weight: bold; }
    .data-table td { border: 1px solid #000; padding: 4px 3px; vertical-align: top; }
    .text-center { text-align: center; }
</style>

<table class="data-table">
    <thead>
        <tr>
            <th width="25">NO</th>
            <th width="70">TGL REQ</th>
            <th>PEGAWAI</th>
            <th>JABATAN</th>
            <th>NAMA BARANG</th>
            <th width="40">REQ</th>
            <th width="40">ACC</th>
            <th width="40">SEND</th>
            <th width="60">STATUS</th>
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
        <tr><td colspan="9" style="text-align:center; color:#999; padding:20px;">Tidak ada data ditemukan untuk periode ini.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (isset($is_print) && $is_print): ?>
    <?php $this->load->view('reports/pdf/report_footer'); ?>
<?php endif; ?>
