<ul class="nav-sidebar" style="list-style: none; padding: 0; margin: 0;">
    <li class="nav-section">
        <div class="nav-section-title">Menu Utama</div>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li class="nav-item">
                <a href="<?= site_url('/dashboard') ?>" class="nav-link <?= $page == 'Dashboard' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>
    </li>
    
    <?php if ((int) $this->session->userdata('level') === 1): ?>
    <li class="nav-section">
        <div class="nav-section-title">Inventori</div>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li class="nav-item">
                <a href="<?= site_url('stock') ?>" class="nav-link <?= $page == 'Stock Management' || $page == 'Tambah Item' || $page == 'Edit Item' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-boxes"></i>
                    <span>Kelola Stok</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('category') ?>" class="nav-link <?= $page == 'Kelola Kategori' || $page == 'Tambah Kategori' || $page == 'Edit Kategori' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-folder"></i>
                    <span>Kelola Kategori</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('stock_import') ?>" class="nav-link <?= $page == 'Import Restock' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-file-import"></i>
                    <span>Import Stock</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('user/lists') ?>" class="nav-link <?= $page == 'Kelola Pengguna' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-users"></i>
                    <span>Kelola Pengguna</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('user/import') ?>" class="nav-link <?= $page == 'Import Pegawai' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-users-cog"></i>
                    <span>Import Pegawai</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('request_admin') ?>" class="nav-link <?= $page == 'Request Management' || $page == 'Detail Permintaan' || $page == 'Approve Permintaan' || $page == 'Deliver Permintaan' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-clipboard-list"></i>
                    <span>Kelola Permintaan</span>
                    <?php if (isset($active_requests_count) && (int) $active_requests_count > 0): ?>
                        <span class="nav-badge"><?= (int) $active_requests_count ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('request_admin/create') ?>" class="nav-link <?= $page == 'Buat Permintaan' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-plus-circle"></i>
                    <span>Buat Permintaan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('notification') ?>" class="nav-link <?= $page == 'Notifications' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-bell"></i>
                    <span>Notifikasi</span>
                    <?php if (!empty($unread_notifications) && (int) $unread_notifications > 0): ?>
                        <span class="nav-badge"><?= (int) $unread_notifications ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </li>
    
    <li class="nav-section">
        <div class="nav-section-title">Laporan</div>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li class="nav-item">
                <a href="<?= site_url('reports/request_history') ?>" class="nav-link <?= $page == 'Request History' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-history"></i>
                    <span>Riwayat Permintaan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('reports/stock_movement') ?>" class="nav-link <?= $page == 'Stock Movement' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-exchange-alt"></i>
                    <span>Pergerakan Stok</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('reports/audit_trail') ?>" class="nav-link <?= $page == 'Audit Trail' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-shield-alt"></i>
                    <span>Jejak Audit</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('reports/stock_levels') ?>" class="nav-link <?= $page == 'Stock Levels' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <span>Level Stok</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('backup') ?>" class="nav-link <?= $page == 'Backup Database' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-database"></i>
                    <span>Cadangkan Database</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('report_pdf') ?>" class="nav-link <?= $page == 'Cetak Laporan' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-print"></i>
                    <span>Cetak Laporan PDF</span>
                </a>
            </li>
        </ul>
    </li>
    
    <li class="nav-section">
        <div class="nav-section-title">Bantuan</div>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li class="nav-item">
                <a href="<?= site_url('guide') ?>" class="nav-link <?= $page == 'Panduan Aplikasi' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-question-circle"></i>
                    <span>Panduan</span>
                </a>
            </li>
        </ul>
    </li>
    <?php endif; ?>
    
    <?php if ((int) $this->session->userdata('level') === 2): ?>
    <li class="nav-section">
        <div class="nav-section-title">Inventori</div>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li class="nav-item">
                <a href="<?= site_url('request') ?>" class="nav-link <?= $page == 'Permintaan ATK' || $page == 'Buat Permintaan' || $page == 'Detail Permintaan' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-clipboard-list"></i>
                    <span>Permintaan ATK</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('notification') ?>" class="nav-link <?= $page == 'Notifications' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-bell"></i>
                    <span>Notifikasi</span>
                    <?php if (!empty($unread_notifications) && (int) $unread_notifications > 0): ?>
                        <span class="nav-badge"><?= (int) $unread_notifications ?></span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </li>
    
    <li class="nav-section">
        <div class="nav-section-title">Akun</div>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li class="nav-item">
                <a href="<?= site_url('auth/change_password') ?>" class="nav-link <?= $page == 'Ubah Password' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-key"></i>
                    <span>Ubah Password</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('guide') ?>" class="nav-link <?= $page == 'Panduan Aplikasi' ? 'active' : '' ?>">
                    <i class="nav-icon fas fa-question-circle"></i>
                    <span>Panduan</span>
                </a>
            </li>
        </ul>
    </li>
    <?php endif; ?>
</ul>
