<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (isset($is_print) && $is_print): ?>
    <?php $this->load->view('reports/pdf/report_header'); ?>
<?php endif; ?>

<div class="report-title-container">
    <h2 class="report-main-title">BUKU BANTU PENERIMAAN BARANG PERSEDIAAN</h2>
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
    .report-period { font-size: 14px; font-weight: bold; margin: 0; text-transform: uppercase; }

    .data-table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10px; }
    .data-table th { border: 1px solid #000; padding: 8px 4px; background: #f0f0f0; text-align: center; font-weight: bold; }
    .data-table td { border: 1px solid #000; padding: 6px 4px; vertical-align: top; }
    .text-center { text-align: center; }
</style>

<table class="data-table">
    <thead>
        <tr>
            <th width="30">NO</th>
            <th width="80">TANGGAL</th>
            <th>NAMA BARANG</th>
            <th width="60">SATUAN</th>
            <th width="80">QTY MASUK</th>
            <th>KETERANGAN / SUMBER</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($report_data)): $no = 1; foreach ($report_data as $row): ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= date('d/m/Y', strtotime($row['display_date'])) ?></td>
            <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
            <td class="text-center"><?= htmlspecialchars($row['unit'] ?? 'Pcs') ?></td>
            <td class="text-center"><?= (int)($row['qty_delta'] ?? 0) ?></td>
            <td><?= htmlspecialchars($row['reason'] ?? '') ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6" style="text-align:center; color:#999; padding:20px;">Tidak ada data ditemukan untuk periode ini.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (isset($is_print) && $is_print): ?>
    <?php $this->load->view('reports/pdf/report_footer'); ?>
<?php endif; ?>
