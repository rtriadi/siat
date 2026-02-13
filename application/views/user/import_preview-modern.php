<section class="page-header">
    <div class="header-content">
        <div class="header-info">
            <h1 class="page-title">Preview Import Pegawai</h1>
            <p class="page-subtitle">Review data sebelum melakukan import</p>
        </div>
    </div>
</section>

<section class="page-content">
    <div class="card">
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Terdapat error:</strong>
                        <ul style="margin: 10px 0 0 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <div class="summary-stats">
                <div class="stat-item">
                    <span class="stat-label">Total Valid</span>
                    <span class="stat-value success"><?= (int) $valid_count ?> baris</span>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Baris</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Unit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr class="<?= !empty($row['errors']) ? 'error-row' : '' ?>">
                                <td><?= (int) $row['row'] ?></td>
                                <td><?= htmlspecialchars($row['nip'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if (!empty($row['errors'])): ?>
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i>
                                            Invalid
                                        </span>
                                        <div class="error-text">
                                            <?= implode(', ', $row['errors']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i>
                                            Valid
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions">
                <a href="<?= site_url('user/import') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
                <form action="<?= site_url('user/import_commit') ?>" method="post" style="display: inline;">
                    <button type="submit" class="btn btn-primary" <?= $valid_count === 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-file-import"></i>
                        Import Data (<?= (int) $valid_count ?>)
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    .page-header { margin-bottom: 24px; }
    .header-content { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
    .page-title { font-size: 28px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .page-subtitle { font-size: 14px; color: var(--text-secondary); }
    .summary-stats { display: flex; gap: 24px; margin-bottom: 20px; padding: 16px; background: var(--surface-elevated); border-radius: var(--radius); }
    .stat-item { display: flex; align-items: center; gap: 10px; }
    .stat-label { font-size: 14px; color: var(--text-secondary); }
    .stat-value { font-size: 18px; font-weight: 600; }
    .stat-value.success { color: var(--success); }
    .table-modern { width: 100%; }
    .table-modern th { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); background: var(--surface-elevated); padding: 12px 14px; }
    .table-modern td { padding: 12px 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .table-modern tbody tr { transition: background 0.15s; }
    .table-modern tbody tr:hover { background: var(--surface-elevated); }
    .table-modern tbody tr.error-row { background: rgba(239, 68, 68, 0.05); }
    .error-text { font-size: 11px; color: var(--danger); margin-top: 4px; }
    .form-actions { display: flex; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--border); }
    .form-actions .btn:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
