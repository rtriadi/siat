<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php if (isset($is_print) && $is_print): ?>
    <?php $this->load->view('reports/pdf/report_header'); ?>
<?php endif; ?>

<div class="report-title-container">
    <h2 class="report-main-title">RENCANA KEBUTUHAN BARANG PERSEDIAAN (RKBP)</h2>
    <h3 class="report-sub-title">PENGADILAN AGAMA GORONTALO</h3>
    <p class="report-period">TAHUN <?= $year ?></p>
</div>

<style>
    .report-title-container { text-align: center; margin-bottom: 20px; }
    .report-main-title { font-size: 14px; font-weight: bold; margin: 0 0 4px 0; text-transform: uppercase; }
    .report-sub-title { font-size: 14px; font-weight: bold; margin: 0 0 4px 0; text-transform: uppercase; }
    .report-period { font-size: 12px; font-weight: bold; margin: 0; text-transform: uppercase; }

    .data-table { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10px; }
    .data-table th { border: 1px solid #000; padding: 8px 4px; background: #f0f0f0; text-align: center; font-weight: bold; }
    .data-table td { border: 1px solid #000; padding: 6px 4px; vertical-align: top; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
</style>

<table class="data-table">
    <thead>
        <tr>
            <th width="30">NO</th>
            <th width="80">KODE BARANG</th>
            <th>NAMA BARANG</th>
            <th width="60">SATUAN</th>
            <th width="60">KEBUTUHAN / THN</th>
            <th width="80">HARGA SATUAN</th>
            <th width="100">TOTAL ESTIMASI</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($report_data)): $no = 1; $grand_total = 0; foreach ($report_data as $row): 
            $total = $row['total_qty'] * ($row['price_per_unit'] ?? 0);
            $grand_total += $total;
        ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= htmlspecialchars($row['item_code'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['item_name'] ?? '-') ?></td>
            <td class="text-center"><?= htmlspecialchars($row['unit'] ?? 'Pcs') ?></td>
            <td class="text-center"><?= (int)($row['total_qty'] ?? 0) ?></td>
            <td class="text-right">Rp <?= number_format($row['price_per_unit'] ?? 0, 0, ',', '.') ?></td>
            <td class="text-right">Rp <?= number_format($total, 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="6" style="text-align:right;">TOTAL ANGGARAN</th>
            <th class="text-right">Rp <?= number_format($grand_total, 0, ',', '.') ?></th>
        </tr>
        <?php else: ?>
        <tr><td colspan="7" style="text-align:center; color:#999; padding:20px;">Tidak ada data ditemukan untuk periode ini.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (isset($is_print) && $is_print): ?>
    <?php $this->load->view('reports/pdf/report_footer'); ?>
<?php endif; ?>
