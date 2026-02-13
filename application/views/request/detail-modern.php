<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$status = $request['status'];
$status_config = [
    'pending' => ['class' => 'warning', 'icon' => 'clock', 'label' => 'Pending'],
    'approved' => ['class' => 'info', 'icon' => 'check', 'label' => 'Disetujui'],
    'rejected' => ['class' => 'danger', 'icon' => 'times', 'label' => 'Ditolak'],
    'delivered' => ['class' => 'success', 'icon' => 'check-circle', 'label' => 'Diterima'],
    'cancelled' => ['class' => 'secondary', 'icon' => 'ban', 'label' => 'Dibatalkan']
];
$config = $status_config[$status] ?? $status_config['pending'];
?>

<section class="page-header">
    <div class="header-content">
        <div class="header-info">
            <h1 class="page-title">Detail Permintaan</h1>
            <p class="page-subtitle"><?= htmlspecialchars($request['request_no'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="header-actions">
            <a href="<?= site_url('request') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>
</section>

<section class="page-content">
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?= $this->session->flashdata('error') ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="detail-grid">
        <div class="card main-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle" style="color: var(--accent);"></i>
                    Informasi Permintaan
                </h3>
                <span class="status-badge <?= $config['class'] ?>">
                    <i class="fas fa-<?= $config['icon'] ?>"></i>
                    <?= $config['label'] ?>
                </span>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">No. Permintaan</div>
                        <div class="info-value"><?= htmlspecialchars($request['request_no'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal Dibuat</div>
                        <div class="info-value"><?= format_datetime_indonesia($request['created_at']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Disetujui</div>
                        <div class="info-value"><?= $request['approved_at'] ? format_datetime_indonesia($request['approved_at']) : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Dikirim</div>
                        <div class="info-value"><?= $request['delivered_at'] ? format_datetime_indonesia($request['delivered_at']) : '-' ?></div>
                    </div>
                </div>

                <?php if (!empty($request['notes'])): ?>
                    <div class="notes-section">
                        <div class="info-label">Catatan</div>
                        <div class="notes-content"><?= nl2br(htmlspecialchars($request['notes'], ENT_QUOTES, 'UTF-8')) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card items-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-boxes" style="color: var(--success);"></i>
                    Item Permintaan
                </h3>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Diminta</th>
                                <th class="text-center">Disetujui</th>
                                <th class="text-center">Dikirim</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($request['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <span class="item-name"><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="qty-badge requested"><?= (int) $item['qty_requested'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="qty-badge approved"><?= (int) $item['qty_approved'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="qty-badge delivered"><?= (int) $item['qty_delivered'] ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if ($status === 'pending'): ?>
        <div class="cancel-section">
            <a href="<?= site_url('request/cancel/' . $request['id_request']) ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan permintaan ini?');">
                <i class="fas fa-times"></i>
                Batalkan Permintaan
            </a>
        </div>
    <?php endif; ?>
</section>

<style>
    .page-header {
        margin-bottom: 24px;
    }
    
    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    
    .page-subtitle {
        font-size: 14px;
        color: var(--text-secondary);
    }
    
    .detail-grid {
        display: grid;
        gap: 24px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    
    .info-item {
        padding: 16px;
        background: var(--surface-elevated);
        border-radius: var(--radius);
    }
    
    .info-label {
        font-size: 12px;
        color: var(--text-secondary);
        margin-bottom: 6px;
    }
    
    .info-value {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
    }
    
    .notes-section {
        margin-top: 24px;
        padding: 16px;
        background: rgba(59, 130, 246, 0.05);
        border-radius: var(--radius);
        border-left: 3px solid var(--accent);
    }
    
    .notes-content {
        margin-top: 8px;
        color: var(--text-primary);
        white-space: pre-wrap;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
    }
    
    .status-badge.warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }
    
    .status-badge.info {
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent);
    }
    
    .status-badge.success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }
    
    .status-badge.danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }
    
    .status-badge.secondary {
        background: rgba(100, 116, 139, 0.1);
        color: var(--text-secondary);
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th,
    .table td {
        padding: 14px 16px;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }
    
    .table th {
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-secondary);
        background: var(--surface-elevated);
    }
    
    .item-name {
        font-weight: 500;
    }
    
    .qty-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    
    .qty-badge.requested {
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent);
    }
    
    .qty-badge.approved {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }
    
    .qty-badge.delivered {
        background: rgba(139, 92, 246, 0.1);
        color: #8b5cf6;
    }
    
    .cancel-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }
</style>
