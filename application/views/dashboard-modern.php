<?php defined('BASEPATH') or exit('No direct script access allowed'); 
$level = $level ?? 'admin';
?>

<div class="page-header">
    <div class="page-header-content">
        <div class="welcome-section">
            <div class="welcome-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div>
                <h1 class="page-title">
                    <?php if ($level === 'admin'): ?>
                    Dashboard Admin
                    <?php else: ?>
                    Dashboard Pegawai
                    <?php endif; ?>
                </h1>
                <p class="page-subtitle">
                    Selamat datang, <strong><?= htmlspecialchars($user_name ?? 'User') ?></strong>! 
                    <?php 
                    $hari_id = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
                    $bulan_id = ['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember'];
                    $hari = $hari_id[date('l')] ?? date('l');
                    $bulan = $bulan_id[date('F')] ?? date('F');
                    echo $hari . ', ' . date('d') . ' ' . $bulan . ' ' . date('Y');
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <?php if ($level === 'admin'): ?>
    
    <!-- Admin Dashboard -->
    <div class="stats-grid">
        <div class="stat-card stat-primary">
            <div class="stat-bg-icon"><i class="fas fa-boxes"></i></div>
            <div class="stat-content">
                <div class="stat-icon"><i class="fas fa-boxes"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Total Jenis Barang</span>
                    <span class="stat-value"><?= number_format($total_items ?? 0) ?></span>
                    <span class="stat-sub"><?= number_format($total_stock ?? 0) ?> unit tersedia</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-warning">
            <div class="stat-bg-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-content">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Menunggu Persetujuan</span>
                    <span class="stat-value"><?= number_format($pending_requests ?? 0) ?></span>
                    <span class="stat-sub">permintaan</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-danger">
            <div class="stat-bg-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-content">
                <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Stok Menipis</span>
                    <span class="stat-value"><?= number_format($low_stock_items ?? 0) ?></span>
                    <span class="stat-sub">item perlu restock</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-success">
            <div class="stat-bg-icon"><i class="fas fa-truck"></i></div>
            <div class="stat-content">
                <div class="stat-icon"><i class="fas fa-truck"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Diserahkan Hari Ini</span>
                    <span class="stat-value"><?= number_format($delivered_today ?? 0) ?></span>
                    <span class="stat-sub">permintaan</span>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Quick Actions -->
        <div class="card quick-actions-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt"></i> Aksi Cepat</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="<?= site_url('stock/create') ?>" class="quick-action">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span>Tambah Item</span>
                    </a>
                    <a href="<?= site_url('category/create') ?>" class="quick-action">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <i class="fas fa-folder-plus"></i>
                        </div>
                        <span>Tambah Kategori</span>
                    </a>
                    <a href="<?= site_url('stock_import') ?>" class="quick-action">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <i class="fas fa-file-import"></i>
                        </div>
                        <span>Import Stok</span>
                    </a>
                    <a href="<?= site_url('request_admin') ?>" class="quick-action">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <span>Kelola Request</span>
                    </a>
                    <a href="<?= site_url('reports/stock_levels') ?>" class="quick-action">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <span>Laporan Stok</span>
                    </a>
                    <a href="<?= site_url('guide') ?>" class="quick-action">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #6b7280, #4b5563);">
                            <i class="fas fa-book"></i>
                        </div>
                        <span>Panduan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Requests -->
        <div class="card recent-requests-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Permintaan Terbaru</h3>
                <a href="<?= site_url('request_admin') ?>" class="view-all">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card-body" style="padding: 0;">
                <?php if (!empty($recent_requests)): ?>
                <div class="request-list">
                    <?php foreach ($recent_requests as $req): ?>
                    <div class="request-item">
                        <div class="request-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="request-info">
                            <span class="request-name"><?= htmlspecialchars($req['user_name'] ?? 'User') ?></span>
                                <span class="request-item-name"><?= htmlspecialchars($req['item_names'] ?? 'Item') ?> — No. <?= $req['request_no'] ?></span>
                        </div>
                        <div class="request-qty">x<?= $req['total_qty'] ?? 0 ?></div>
                        <span class="request-status status-<?= $req['status'] ?>">
                            <?php 
                            $status_text = [
                                'pending' => 'Pending',
                                'approved' => 'Disetujui', 
                                'rejected' => 'Ditolak',
                                'delivered' => 'Diterima'
                            ];
                            echo $status_text[$req['status']] ?? $req['status'];
                            ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state-small">
                    <i class="fas fa-inbox"></i>
                    <p>Belum ada permintaan</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Info Cards Row -->
    <div class="info-cards-row">
        <div class="info-card gradient-blue">
            <div class="ic-content">
                <i class="fas fa-folder-tree"></i>
                <div>
                    <span class="ic-value"><?= number_format($category_count ?? 0) ?></span>
                    <span class="ic-label">Kategori</span>
                </div>
            </div>
        </div>
        <div class="info-card gradient-purple">
            <div class="ic-content">
                <i class="fas fa-users"></i>
                <div>
                    <span class="ic-value"><?= number_format($pending_requests ?? 0) ?></span>
                    <span class="ic-label">Menunggu</span>
                </div>
            </div>
        </div>
        <div class="info-card gradient-orange">
            <div class="ic-content">
                <i class="fas fa-check-circle"></i>
                <div>
                    <span class="ic-value"><?= number_format($delivered_today ?? 0) ?></span>
                    <span class="ic-label">Selesai Hari Ini</span>
                </div>
            </div>
        </div>
    </div>

    <?php else: ?>
    
    <!-- Employee Dashboard -->
    <div class="stats-grid">
        <div class="stat-card stat-warning">
            <div class="stat-bg-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-content">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Pending</span>
                    <span class="stat-value"><?= number_format($pending_count ?? 0) ?></span>
                    <span class="stat-sub">menunggu persetujuan</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-success">
            <div class="stat-bg-icon"><i class="fas fa-check"></i></div>
            <div class="stat-content">
                <div class="stat-icon"><i class="fas fa-check"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Disetujui</span>
                    <span class="stat-value"><?= number_format($approved_count ?? 0) ?></span>
                    <span class="stat-sub">siap diambil</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card stat-primary">
            <div class="stat-bg-icon"><i class="fas fa-box"></i></div>
            <div class="stat-content">
                <div class="stat-icon"><i class="fas fa-box"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Diterima</span>
                    <span class="stat-value"><?= number_format($delivered_count ?? 0) ?></span>
                    <span class="stat-sub">total diterima</span>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Quick Actions -->
        <div class="card quick-actions-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bolt"></i> Aksi Cepat</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="<?= site_url('request/create') ?>" class="quick-action">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span>Buat Permintaan</span>
                    </a>
                    <a href="<?= site_url('request') ?>" class="quick-action">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <i class="fas fa-list"></i>
                        </div>
                        <span>Daftar Request</span>
                    </a>
                    <a href="<?= site_url('notification') ?>" class="quick-action">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="fas fa-bell"></i>
                        </div>
                        <span>Notifikasi</span>
                    </a>
                    <a href="<?= site_url('guide') ?>" class="quick-action">
                        <div class="qa-icon" style="background: linear-gradient(135deg, #6b7280, #4b5563);">
                            <i class="fas fa-book"></i>
                        </div>
                        <span>Panduan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- My Requests -->
        <div class="card recent-requests-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history"></i> Riwayat Permintaan</h3>
                <a href="<?= site_url('request') ?>" class="view-all">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card-body" style="padding: 0;">
                <?php if (!empty($all_my_requests)): ?>
                <div class="request-list">
                    <?php foreach ($all_my_requests as $req): ?>
                    <div class="request-item">
                        <div class="request-avatar">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="request-info">
                            <span class="request-name"><?= htmlspecialchars($req['item_names'] ?? 'Item') ?></span>
                                <span class="request-item-name"><?= date('d M Y, H:i', strtotime($req['created_at'])) ?> — No. <?= $req['request_no'] ?></span>
                        </div>
                        <div class="request-qty">x<?= $req['total_qty'] ?? 0 ?></div>
                        <span class="request-status status-<?= $req['status'] ?>">
                            <?php 
                            $status_text = [
                                'pending' => 'Pending',
                                'approved' => 'Disetujui', 
                                'rejected' => 'Ditolak',
                                'delivered' => 'Diterima'
                            ];
                            echo $status_text[$req['status']] ?? $req['status'];
                            ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state-small">
                    <i class="fas fa-inbox"></i>
                    <p>Belum ada permintaan</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<style>
.welcome-section {
    display: flex;
    align-items: center;
    gap: 16px;
}

.welcome-avatar {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, #4a90d9, #2563eb);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.stat-bg-icon {
    position: absolute;
    right: -10px;
    bottom: -10px;
    font-size: 80px;
    opacity: 0.08;
    color: #6b7280;
}

.stat-card.stat-primary .stat-bg-icon { color: #3b82f6; }
.stat-card.stat-warning .stat-bg-icon { color: #f59e0b; }
.stat-card.stat-danger .stat-bg-icon { color: #ef4444; }
.stat-card.stat-success .stat-bg-icon { color: #10b981; }

.stat-content {
    display: flex;
    align-items: center;
    gap: 16px;
    position: relative;
    z-index: 1;
}

.stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.stat-card.stat-primary .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.stat-card.stat-warning .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.stat-card.stat-danger .stat-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.stat-card.stat-success .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 4px;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
}

.stat-sub {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 4px;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 24px;
    margin-bottom: 24px;
}

.quick-actions-card .card-header {
    border-bottom: none;
    padding-bottom: 0;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 20px 12px;
    background: #f9fafb;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s;
}

.quick-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.qa-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
}

.quick-action span {
    font-size: 12px;
    font-weight: 500;
    color: #374151;
    text-align: center;
}

.recent-requests-card .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.view-all {
    font-size: 13px;
    color: #4a90d9;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 6px;
}

.view-all:hover {
    text-decoration: underline;
}

.request-list {
    display: flex;
    flex-direction: column;
}

.request-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
}

.request-item:hover {
    background: #f9fafb;
}

.request-item:last-child {
    border-bottom: none;
}

.request-avatar {
    width: 40px;
    height: 40px;
    background: #e5e7eb;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 16px;
}

.request-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.request-name {
    font-size: 14px;
    font-weight: 500;
    color: #1f2937;
}

.request-item-name {
    font-size: 12px;
    color: #6b7280;
}

.request-qty {
    font-size: 14px;
    font-weight: 600;
    color: #4b5563;
    padding: 4px 10px;
    background: #f3f4f6;
    border-radius: 6px;
}

.request-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-approved { background: #d1fae5; color: #065f46; }
.status-rejected { background: #fee2e2; color: #991b1b; }
.status-delivered { background: #dbeafe; color: #1e40af; }

.empty-state-small {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.empty-state-small i {
    font-size: 36px;
    margin-bottom: 12px;
    display: block;
}

.info-cards-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.info-card {
    border-radius: 16px;
    padding: 20px;
    color: white;
}

.info-card.gradient-blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.info-card.gradient-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
.info-card.gradient-orange { background: linear-gradient(135deg, #f59e0b, #d97706); }

.ic-content {
    display: flex;
    align-items: center;
    gap: 16px;
}

.ic-content > i {
    font-size: 32px;
    opacity: 0.9;
}

.ic-content > div {
    display: flex;
    flex-direction: column;
}

.ic-value {
    font-size: 28px;
    font-weight: 700;
    line-height: 1;
}

.ic-label {
    font-size: 13px;
    opacity: 0.9;
    margin-top: 4px;
}

@media (max-width: 1200px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .dashboard-grid { grid-template-columns: 1fr; }
    .info-cards-row { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .stats-grid { grid-template-columns: 1fr; }
    .quick-actions { grid-template-columns: repeat(2, 1fr); }
}
</style>
