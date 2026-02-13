<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<section class="page-header">
    <div class="header-content">
        <div class="header-info">
            <h1 class="page-title">Delivery Checklist</h1>
            <p class="page-subtitle"><?= htmlspecialchars($request['request_no'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="header-actions">
            <a href="<?= site_url('request_admin/detail/' . $request['id_request']) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>
</section>

<section class="page-content">
    <div class="card">
        <div class="card-body">
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="warning-banner">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="warning-content">
                    <h4>Informasi Pengiriman</h4>
                    <p>Sisa item yang tidak dikirim akan otomatis dibatalkan dan stok dikembalikan ke tersedia.</p>
                </div>
            </div>

            <form method="post" action="<?= site_url('request_admin/deliver/' . $request['id_request']) ?>" class="deliver-form">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Disetujui</th>
                                <th class="text-center">Dikirim</th>
                                <th class="text-center">Sisa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($request['items'] as $item): ?>
                                <?php
                                $approved = (int) $item['qty_approved'];
                                $remaining = max($approved, 0);
                                ?>
                                <tr>
                                    <td>
                                        <span class="item-name"><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <span class="qty-badge approved"><?= $approved ?></span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" 
                                               class="form-control qty-input" 
                                               name="qty_delivered[<?= (int) $item['item_id'] ?>]"
                                               min="0" 
                                               max="<?= $approved ?>" 
                                               value="<?= $approved ?>" 
                                               required>
                                    </td>
                                    <td class="text-center">
                                        <span class="remaining-badge"><?= $remaining ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="form-actions">
                    <a href="<?= site_url('request_admin/detail/' . $request['id_request']) ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-truck"></i>
                        Simpan Pengiriman
                    </button>
                </div>
            </form>
        </div>
    </div>
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
    
    .warning-banner {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        background: rgba(245, 158, 11, 0.05);
        border: 1px solid rgba(245, 158, 11, 0.2);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .warning-icon {
        width: 44px;
        height: 44px;
        background: rgba(245, 158, 11, 0.1);
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .warning-icon i {
        font-size: 20px;
        color: var(--warning);
    }
    
    .warning-content h4 {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    
    .warning-content p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }
    
    .deliver-form {
        max-width: 600px;
    }
    
    .table-container {
        overflow-x: auto;
        margin-bottom: 24px;
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
    
    .qty-badge.approved {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }
    
    .qty-input {
        width: 100px;
        text-align: center;
    }
    
    .remaining-badge {
        display: inline-block;
        padding: 4px 12px;
        background: var(--surface-elevated);
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }
</style>
