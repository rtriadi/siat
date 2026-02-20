<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$status = $request['status'];
$status_config = [
    'pending' => ['class' => 'warning', 'icon' => 'clock', 'label' => 'Pending'],
    'approved' => ['class' => 'info', 'icon' => 'check', 'label' => 'Disetujui'],
    'rejected' => ['class' => 'danger', 'icon' => 'times', 'label' => 'Ditolak'],
    'delivered' => ['class' => 'success', 'icon' => 'check-circle', 'label' => 'Dikirim'],
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
            <a href="<?= site_url('request_admin') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>
</section>

<section class="page-content">
    
    

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
                        <div class="info-label">Pegawai</div>
                        <div class="info-value">
                            <?= htmlspecialchars($pegawai['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($pegawai['nip'])): ?>
                                <span class="text-muted">(<?= htmlspecialchars($pegawai['nip'], ENT_QUOTES, 'UTF-8') ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Dibuat</div>
                        <div class="info-value"><?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Disetujui</div>
                        <div class="info-value"><?= $request['approved_at'] ? date('d/m/Y H:i', strtotime($request['approved_at'])) : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Dikirim</div>
                        <div class="info-value"><?= $request['delivered_at'] ? date('d/m/Y H:i', strtotime($request['delivered_at'])) : '-' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Ditolak</div>
                        <div class="info-value"><?= $request['rejected_at'] ? date('d/m/Y H:i', strtotime($request['rejected_at'])) : '-' ?></div>
                    </div>
                </div>

                <?php if (!empty($request['notes'])): ?>
                    <div class="notes-section">
                        <div class="info-label">Catatan</div>
                        <div class="notes-content"><?= nl2br(htmlspecialchars($request['notes'], ENT_QUOTES, 'UTF-8')) ?></div>
                    </div>
                <?php endif; ?>

                <div class="history-section">
                    <h4 class="section-title">
                        <i class="fas fa-history" style="color: var(--warning);"></i>
                        Riwayat Status
                    </h4>
                    <div class="history-timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot pending"></div>
                            <div class="timeline-content">
                                <span class="timeline-label">Pending</span>
                                <span class="timeline-date"><?= date('d/m/Y H:i', strtotime($request['created_at'])) ?></span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot <?= $request['approved_at'] ? 'approved' : 'inactive' ?>"></div>
                            <div class="timeline-content">
                                <span class="timeline-label">Approved</span>
                                <span class="timeline-date"><?= $request['approved_at'] ? date('d/m/Y H:i', strtotime($request['approved_at'])) : '-' ?></span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot <?= $request['delivered_at'] ? 'delivered' : 'inactive' ?>"></div>
                            <div class="timeline-content">
                                <span class="timeline-label">Delivered</span>
                                <span class="timeline-date"><?= $request['delivered_at'] ? date('d/m/Y H:i', strtotime($request['delivered_at'])) : '-' ?></span>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot <?= $request['rejected_at'] ? 'rejected' : 'inactive' ?>"></div>
                            <div class="timeline-content">
                                <span class="timeline-label">Rejected</span>
                                <span class="timeline-date"><?= $request['rejected_at'] ? date('d/m/Y H:i', strtotime($request['rejected_at'])) : '-' ?></span>
                            </div>
                        </div>
                    </div>
                </div>
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

    <div class="action-section">
        <?php if ($status === 'pending'): ?>
            <a href="<?= site_url('request_admin/approve/' . $request['id_request']) ?>" class="btn btn-success">
                <i class="fas fa-check"></i>
                Approve
            </a>
            <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                <i class="fas fa-times"></i>
                Reject
            </button>

            <!-- Reject Modal -->
            <div class="custom-modal" id="rejectModal">
                <div class="custom-modal-overlay"></div>
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h5>Tolak Permintaan</h5>
                        <button type="button" class="close-btn" onclick="closeRejectModal()">&times;</button>
                    </div>
                    <form method="post" action="<?= site_url('request_admin/reject/' . $request['id_request']) ?>">
                        <div class="custom-modal-body">
                            <div class="form-group">
                                <label>Alasan Penolakan <span class="required">*</span></label>
                                <textarea name="note" class="form-control" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
                            </div>
                        </div>
                        <div class="custom-modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="closeRejectModal()">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($status === 'approved'): ?>
            <a href="<?= site_url('request_admin/deliver/' . $request['id_request']) ?>" class="btn btn-warning">
                <i class="fas fa-truck"></i>
                Deliver
            </a>
        <?php endif; ?>
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
    
    .detail-grid {
        display: grid;
        gap: 24px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
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
    
    .text-muted {
        color: var(--text-secondary);
        font-weight: 400;
    }
    
    .notes-section {
        margin-top: 20px;
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
    
    .history-section {
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 16px;
    }
    
    .history-timeline {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .timeline-item {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .timeline-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    .timeline-dot.pending {
        background: var(--warning);
    }
    
    .timeline-dot.approved,
    .timeline-dot.delivered {
        background: var(--success);
    }
    
    .timeline-dot.rejected {
        background: var(--danger);
    }
    
    .timeline-dot.inactive {
        background: var(--border);
    }
    
    .timeline-content {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .timeline-label {
        font-weight: 500;
        color: var(--text-primary);
        min-width: 80px;
    }
    
    .timeline-date {
        color: var(--text-secondary);
        font-size: 13px;
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
    
    .action-section {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 1px solid var(--border);
    }
    
    .required {
        color: var(--danger);
    }
    
    .custom-modal {
        display: none !important;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 9999;
    }
    
    .custom-modal.show {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    
    .custom-modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
    }
    
    .custom-modal-content {
        position: relative;
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 450px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    
    .custom-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
    }
    
    .custom-modal-header h5 {
        font-size: 18px;
        font-weight: 600;
        margin: 0;
    }
    
    .close-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: #9ca3af;
        cursor: pointer;
    }
    
    .custom-modal-body {
        padding: 24px;
    }
    
    .custom-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        padding: 16px 24px;
        border-top: 1px solid var(--border);
    }
        color: var(--text-secondary);
        cursor: pointer;
    }
</style>

<script>
    function showRejectModal() {
        document.getElementById('rejectModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }
    
    document.querySelector('.custom-modal-overlay')?.addEventListener('click', closeRejectModal);
</script>
