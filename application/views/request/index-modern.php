<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Permintaan ATK</h1>
            <p class="page-subtitle">Kelola dan lacak permintaan ATK Anda</p>
        </div>
        <a href="<?= site_url('request/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Buat Permintaan
        </a>
    </div>
</div>

<div class="content">
    <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $this->session->flashdata('error') ?>
    </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $this->session->flashdata('success') ?>
    </div>
    <?php endif; ?>

    <?php if (empty($requests)): ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <h3>Belum ada permintaan</h3>
            <p>Buat permintaan ATK pertama Anda</p>
            <a href="<?= site_url('request/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Buat Permintaan
            </a>
        </div>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="data-table-header">
            <div class="table-info">
                <?= count($requests) ?> permintaan
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No. Permintaan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
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
                        <tr>
                            <td>
                                <span class="request-no"><?= htmlspecialchars($request['request_no'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td>
                                <span class="date-text"><?= format_datetime_indonesia($request['created_at']) ?></span>
                            </td>
                            <td>
                                <span class="status-badge <?= $config['class'] ?>">
                                    <i class="fas fa-<?= $config['icon'] ?>"></i>
                                    <?= $config['label'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= site_url('request/detail/' . $request['id_request']) ?>" class="btn btn-sm btn-outline" title="Detail">
                                        <i class="fas fa-eye"></i>
                                        Detail
                                    </a>
                                    <?php if ($status === 'pending'): ?>
                                        <a href="<?= site_url('request/cancel/' . $request['id_request']) ?>" class="btn btn-sm btn-outline-danger" title="Batalkan" onclick="return confirm('Batalkan permintaan ini?');">
                                            <i class="fas fa-times"></i>
                                            Batalkan
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.page-header { margin-bottom: 24px; }
.page-header-content { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
.page-title { font-size: 28px; font-weight: 700; margin: 0; }
.page-subtitle { font-size: 14px; color: var(--text-secondary); margin: 4px 0 0; }

.alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
.alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
.alert .close { background: none; border: none; font-size: 20px; cursor: pointer; margin-left: auto; }

.empty-state { text-align: center; padding: 60px 20px; }
.empty-icon { width: 80px; height: 80px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
.empty-icon i { font-size: 36px; color: #3b82f6; }
.empty-state h3 { font-size: 20px; font-weight: 600; margin-bottom: 8px; color: #1f2937; }
.empty-state p { color: #6b7280; margin-bottom: 24px; }

.data-table-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #e5e7eb; }
.table-info { font-size: 13px; color: var(--text-secondary); }

.data-table { width: 100%; border-collapse: collapse; }
.data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
.data-table td { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
.data-table tbody tr:hover { background: #f9fafb; }

.request-no { font-weight: 600; color: #1f2937; }
.date-text { color: #6b7280; }

.status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
.status-badge.warning { background: #fef3c7; color: #92400e; }
.status-badge.info { background: #dbeafe; color: #1e40af; }
.status-badge.success { background: #d1fae5; color: #065f46; }
.status-badge.danger { background: #fee2e2; color: #991b1b; }
.status-badge.secondary { background: #f3f4f6; color: #6b7280; }

.action-buttons { display: flex; gap: 8px; }
.btn-outline { padding: 6px 12px; border: 1px solid #e5e7eb; border-radius: 6px; background: white; color: #6b7280; cursor: pointer; transition: all 0.15s; text-decoration: none; font-size: 13px; }
.btn-outline:hover { background: #f3f4f6; color: #3b82f6; border-color: #3b82f6; }
.btn-outline-danger { color: #dc3545; border-color: #dc3545; }
.btn-outline-danger:hover { background: #dc3545; color: white; }
.btn-sm { padding: 6px 12px; font-size: 13px; }

.btn { padding: 10px 20px; border-radius: 6px; font-weight: 500; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; border: none; }
.btn-primary { background: #3b82f6; color: white; }
.btn-primary:hover { background: #2563eb; }
</style>
