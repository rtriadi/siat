<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<section class="page-header">
    <div class="header-content">
        <div class="header-info">
            <h1 class="page-title">Dashboard Pegawai</h1>
            <p class="page-subtitle">Selamat datang di SIAT - Sistem Inventori ATK Terpadu</p>
        </div>
        <div class="header-actions">
            <span class="current-date">
                <i class="fas fa-calendar-alt"></i>
                <?= date('l, d F Y') ?>
            </span>
        </div>
    </div>
</section>

<section class="dashboard-content">
    <!-- Password Reminder (optional) -->
    <?php if ((int) $this->session->userdata('must_change_password') === 1): ?>
    <div class="alert alert-info" style="margin-bottom: 24px;">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Tip:</strong> Password Anda masih default. Silakan ubah melalui menu ubah password untuk keamanan akun.
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Permintaan</div>
                <div class="stat-value"><?= isset($total_requests) ? number_format($total_requests) : '0' ?></div>
                <div class="stat-change">
                    <i class="fas fa-list"></i>
                    Semua waktu
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Menunggu Persetujuan</div>
                <div class="stat-value"><?= isset($pending_requests) ? number_format($pending_requests) : '0' ?></div>
                <div class="stat-change warning">
                    <i class="fas fa-hourglass-half"></i>
                    Pending
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Disetujui</div>
                <div class="stat-value"><?= isset($approved_requests) ? number_format($approved_requests) : '0' ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-check"></i>
                    Approved
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-content">
                <div class="stat-label">Diterima</div>
                <div class="stat-value"><?= isset($delivered_requests) ? number_format($delivered_requests) : '0' ?></div>
                <div class="stat-change positive">
                    <i class="fas fa-check-double"></i>
                    Completed
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-bolt" style="color: var(--warning);"></i>
                Quick Actions
            </h3>
        </div>
        <div class="card-body">
            <div class="action-buttons">
                <a href="<?= site_url('request/create') ?>" class="action-btn">
                    <div class="action-icon primary">
                        <i class="fas fa-plus"></i>
                    </div>
                    <span>Buat Permintaan</span>
                </a>
                <a href="<?= site_url('request') ?>" class="action-btn">
                    <div class="action-icon success">
                        <i class="fas fa-list"></i>
                    </div>
                    <span>Daftar Permintaan</span>
                </a>
                <a href="<?= site_url('notification') ?>" class="action-btn">
                    <div class="action-icon warning">
                        <i class="fas fa-bell"></i>
                    </div>
                    <span>Notifikasi</span>
                    <?php if (!empty($unread_notifications) && (int) $unread_notifications > 0): ?>
                        <span class="nav-badge"><?= (int) $unread_notifications ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= site_url('auth/change_password') ?>" class="action-btn">
                    <div class="action-icon danger">
                        <i class="fas fa-key"></i>
                    </div>
                    <span>Ubah Password</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Welcome Card -->
    <div class="card info-banner">
        <div class="info-content">
            <div class="info-icon">
                <i class="fas fa-hand-wave"></i>
            </div>
            <div class="info-text">
                <h4>Selamat Datang, <?= $this->fungsi->user_login()->nama ?? 'Pegawai' ?>!</h4>
                <p>Ajukan permintaan ATK dengan mudah melalui menu Buat Permintaan. Pantau status permintaan Anda di Daftar Permintaan.</p>
            </div>
            <a href="<?= site_url('guide') ?>" class="btn btn-primary">
                <i class="fas fa-book"></i>
                Baca Panduan
            </a>
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
    
    .current-date {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-size: 14px;
        color: var(--text-secondary);
    }
    
    .dashboard-content {
        animation: fadeIn 0.4s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
    }
    
    .action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        padding: 24px 16px;
        background: var(--surface-elevated);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        text-decoration: none;
        color: var(--text-primary);
        transition: all 0.2s ease;
        position: relative;
    }
    
    .action-btn:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-md);
        border-color: var(--accent);
    }
    
    .action-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    
    .action-icon.primary {
        background: rgba(59, 130, 246, 0.1);
        color: var(--accent);
    }
    
    .action-icon.success {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }
    
    .action-icon.warning {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }
    
    .action-icon.danger {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }
    
    .action-btn span {
        font-size: 13px;
        font-weight: 500;
        text-align: center;
    }
    
    .info-banner {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        border: none;
    }
    
    .info-content {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .info-icon {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        flex-shrink: 0;
    }
    
    .info-text {
        flex: 1;
        min-width: 200px;
    }
    
    .info-text h4 {
        font-size: 16px;
        font-weight: 600;
        color: white;
        margin-bottom: 4px;
    }
    
    .info-text p {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.8);
    }
    
    .info-banner .btn {
        background: white;
        color: var(--primary);
    }
    
    .info-banner .btn:hover {
        background: var(--surface-elevated);
    }
    
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .info-content {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
