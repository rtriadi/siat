<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<section class="page-header">
    <div class="header-content">
        <div class="header-info">
            <h1 class="page-title">Approve Permintaan</h1>
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
            

            <div class="info-banner">
                <div class="info-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="info-content">
                    <h4>Petunjuk Persetujuan</h4>
                    <p>Anda dapat mengurangi jumlah persetujuan dari jumlah yang diminta. Jumlah persetujuan tidak boleh melebihi jumlah permintaan.</p>
                </div>
            </div>

            <form method="post" action="<?= site_url('request_admin/approve/' . $request['id_request']) ?>" class="approve-form">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Diminta</th>
                                <th class="text-center">Disetujui</th>
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
                                        <input type="number" 
                                               class="form-control qty-input" 
                                               name="qty_approved[<?= (int) $item['item_id'] ?>]"
                                               min="0" 
                                               max="<?= (int) $item['qty_requested'] ?>" 
                                               value="<?= (int) $item['qty_requested'] ?>" 
                                               required>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="note-section">
                    <label for="note" class="form-label">Catatan Persetujuan (Opsional)</label>
                    <textarea name="note" id="note" class="form-control" rows="3" placeholder="Tambahkan catatan..."></textarea>
                </div>

                <div class="form-actions">
                    <a href="<?= site_url('request_admin/detail/' . $request['id_request']) ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i>
                        Simpan Persetujuan
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
    
    .info-banner {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        background: rgba(59, 130, 246, 0.05);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .info-icon {
        width: 44px;
        height: 44px;
        background: rgba(59, 130, 246, 0.1);
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .info-icon i {
        font-size: 20px;
        color: var(--accent);
    }
    
    .info-content h4 {
        font-size: 15px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    
    .info-content p {
        font-size: 14px;
        color: var(--text-secondary);
        margin: 0;
    }
    
    .approve-form {
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
    
    .qty-badge.requested {
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent);
    }
    
    .qty-input {
        width: 100px;
        text-align: center;
    }
    
    .note-section {
        margin-bottom: 24px;
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }
</style>
