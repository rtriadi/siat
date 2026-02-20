<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Cetak Laporan PDF</h1>
            <p class="page-subtitle">Pilih jenis laporan dan periode, lalu preview atau cetak.</p>
        </div>
    </div>
</div>

<div class="content">
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-body">
            <form method="get" action="<?= site_url('report_pdf') ?>" id="reportForm">
                <div class="filter-grid">
                    <!-- Report Type -->
                    <div class="form-group">
                        <label class="form-label">Jenis Laporan <span class="required">*</span></label>
                        <select name="report_type" class="form-control" required onchange="this.form.submit()">
                            <option value="">-- Pilih Laporan --</option>
                            <option value="buku_bantu_penerimaan" <?= $report_type == 'buku_bantu_penerimaan' ? 'selected' : '' ?>>Buku Bantu Penerimaan</option>
                            <option value="buku_bantu_pengeluaran" <?= $report_type == 'buku_bantu_pengeluaran' ? 'selected' : '' ?>>Buku Bantu Pengeluaran</option>
                            <option value="keadaan_barang" <?= $report_type == 'keadaan_barang' ? 'selected' : '' ?>>Keadaan Barang ATK Perkara</option>
                        </select>
                    </div>

                    <!-- Period Type -->
                    <div class="form-group">
                        <label class="form-label">Tipe Periode</label>
                        <select name="period_type" class="form-control" id="periodType" onchange="togglePeriodFields(); this.form.submit()">
                            <option value="monthly" <?= $period_type == 'monthly' ? 'selected' : '' ?>>Bulanan</option>
                            <option value="yearly" <?= $period_type == 'yearly' ? 'selected' : '' ?>>Tahunan</option>
                            <option value="range" <?= $period_type == 'range' ? 'selected' : '' ?>>Rentang Tanggal</option>
                        </select>
                    </div>

                    <!-- Monthly -->
                    <div class="form-group" id="monthlyFields" style="<?= $period_type == 'monthly' ? '' : 'display:none;' ?>">
                        <label class="form-label">Bulan</label>
                        <select name="month" class="form-control">
                            <?php
                            $bulan_id = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                            for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= (int)$month == $m ? 'selected' : '' ?>><?= $bulan_id[$m] ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Year -->
                    <div class="form-group" id="yearField" style="<?= in_array($period_type, ['monthly', 'yearly']) ? '' : 'display:none;' ?>">
                        <label class="form-label">Tahun</label>
                        <select name="year" class="form-control">
                            <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?= $y ?>" <?= (int)$year == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="form-group" id="rangeFields" style="<?= $period_type == 'range' ? '' : 'display:none;' ?>">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_start" class="form-control" value="<?= $date_start ?>">
                    </div>
                    <div class="form-group" id="rangeFieldsEnd" style="<?= $period_type == 'range' ? '' : 'display:none;' ?>">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_end" class="form-control" value="<?= $date_end ?>">
                    </div>

                    <!-- Print Date -->
                    <div class="form-group">
                        <label class="form-label">Tanggal Cetak / TTD</label>
                        <input type="date" name="print_date" class="form-control" value="<?= $print_date ?? date('Y-m-d') ?>" required>
                    </div>

                    <!-- Paper Size -->
                    <div class="form-group">
                        <label class="form-label">Ukuran Kertas</label>
                        <select name="paper_size" class="form-control" required>
                            <option value="A4" <?= ($paper_size ?? 'A4') == 'A4' ? 'selected' : '' ?>>A4</option>
                            <option value="F4" <?= ($paper_size ?? 'A4') == 'F4' ? 'selected' : '' ?>>F4 / Folio</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions" style="margin-top: 16px;">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Preview
                    </button>
                    <?php if ($report_type && !empty($preview_data)): ?>
                    <a href="<?= site_url('report_pdf/download?' . $_SERVER['QUERY_STRING']) ?>" target="_blank" class="btn btn-primary">
                        <i class="fas fa-print"></i> Cetak / Simpan PDF
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Section -->
    <?php if ($report_type && isset($preview_data)): ?>
    <div class="card">
        <div class="card-header" style="padding: 16px 24px; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
            <h3 style="margin: 0; font-size: 16px; font-weight: 600;">
                <i class="fas fa-eye" style="color: #3b82f6; margin-right: 8px;"></i>
                Preview Laporan
            </h3>
            <?php if (!empty($preview_data)): ?>
            <span style="font-size: 13px; color: #6b7280;"><?= count($preview_data) ?> baris data</span>
            <?php endif; ?>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($preview_data)): ?>
            <div style="text-align: center; padding: 48px; color: #6b7280;">
                <i class="fas fa-inbox" style="font-size: 40px; margin-bottom: 12px; display: block; color: #d1d5db;"></i>
                <p>Tidak ada data pada periode yang dipilih.</p>
            </div>
            <?php else: ?>
            <div style="overflow-x: auto;">
                <?php $report_data = $preview_data; include APPPATH . 'views/reports/pdf/' . $report_type . '.php'; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php else: ?>
    <div style="text-align: center; padding: 60px 20px; color: #9ca3af;">
        <i class="fas fa-file-pdf" style="font-size: 60px; margin-bottom: 16px; display: block; color: #e5e7eb;"></i>
        <h3 style="color: #374151; margin-bottom: 8px;">Pilih Jenis Laporan</h3>
        <p>Silakan pilih jenis laporan dan periode di atas untuk melihat preview data.</p>
    </div>
    <?php endif; ?>
</div>

<style>
.page-header { margin-bottom: 24px; }
.page-header-content { display: flex; align-items: center; justify-content: space-between; }
.page-title { font-size: 28px; font-weight: 700; margin: 0; }
.page-subtitle { font-size: 14px; color: var(--text-secondary); margin: 4px 0 0; }

.filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
.form-group { display: flex; flex-direction: column; }
.form-label { font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
.required { color: #dc3545; }
.form-control { padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: white; }
.form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

.form-actions { display: flex; gap: 12px; flex-wrap: wrap; }
.btn { padding: 10px 20px; border-radius: 8px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
.btn-secondary:hover { background: #e5e7eb; }
</style>

<script>
function togglePeriodFields() {
    const type = document.getElementById('periodType').value;
    document.getElementById('monthlyFields').style.display = type === 'monthly' ? '' : 'none';
    document.getElementById('yearField').style.display = (type === 'monthly' || type === 'yearly') ? '' : 'none';
    document.getElementById('rangeFields').style.display = type === 'range' ? '' : 'none';
    document.getElementById('rangeFieldsEnd').style.display = type === 'range' ? '' : 'none';
}
</script>
