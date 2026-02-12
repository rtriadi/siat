<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-header">MAIN MENU</li>
    <li class="nav-item">
        <a href="<?= site_url('/dashboard') ?>" class="nav-link <?= $page == 'Dashboard' ? 'active' : '' ?>">
            <i class="nav-icon fas fa-home"></i>
            <p>
                Dashboard
                <!-- <span class="right badge badge-danger">New</span> -->
            </p>
        </a>
    </li>
    <?php if ((int) $this->session->userdata('level') === 1): ?>
        <li class="nav-header">Inventori</li>
        <li class="nav-item">
            <a href="<?= site_url('stock') ?>" class="nav-link <?= $page == 'Stock Management' || $page == 'Tambah Item' || $page == 'Edit Item' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-boxes"></i>
                <p>
                    Stock Management
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('request_admin') ?>" class="nav-link <?= $page == 'Request Management' || $page == 'Detail Permintaan' || $page == 'Approve Permintaan' || $page == 'Deliver Permintaan' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>
                    Request Management
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('notification') ?>" class="nav-link <?= $page == 'Notifications' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-bell"></i>
                <p>
                    Notifications
                    <?php if (!empty($unread_notifications) && (int) $unread_notifications > 0): ?>
                        <span class="right badge badge-danger"><?= (int) $unread_notifications ?></span>
                    <?php endif; ?>
                </p>
            </a>
        </li>
        <li class="nav-header">Laporan</li>
        <li class="nav-item">
            <a href="<?= site_url('reports/request_history') ?>" class="nav-link <?= $page == 'Request History' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-history"></i>
                <p>
                    Request History
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('reports/stock_movement') ?>" class="nav-link <?= $page == 'Stock Movement' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-exchange-alt"></i>
                <p>
                    Stock Movement
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('reports/audit_trail') ?>" class="nav-link <?= $page == 'Audit Trail' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-shield-alt"></i>
                <p>
                    Audit Trail
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('reports/stock_levels') ?>" class="nav-link <?= $page == 'Stock Levels' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>
                    Stock Levels
                </p>
            </a>
        </li>
    <?php endif ?>
    <?php if ((int) $this->session->userdata('level') === 2): ?>
        <li class="nav-header">Inventori</li>
        <li class="nav-item">
            <a href="<?= site_url('request') ?>" class="nav-link <?= $page == 'Permintaan ATK' || $page == 'Buat Permintaan' || $page == 'Detail Permintaan' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>
                    Permintaan ATK
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= site_url('notification') ?>" class="nav-link <?= $page == 'Notifications' ? 'active' : '' ?>">
                <i class="nav-icon fas fa-bell"></i>
                <p>
                    Notifications
                    <?php if (!empty($unread_notifications) && (int) $unread_notifications > 0): ?>
                        <span class="right badge badge-danger"><?= (int) $unread_notifications ?></span>
                    <?php endif; ?>
                </p>
            </a>
        </li>
    <?php endif ?>
</ul>
