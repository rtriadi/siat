<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Preview Import Pegawai</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <strong>Total valid:</strong> <?= (int) $valid_count ?> baris
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
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
                                        <tr>
                                            <td><?= (int) $row['row'] ?></td>
                                            <td><?= htmlspecialchars($row['nip'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?= htmlspecialchars($row['unit'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td>
                                                <?php if (!empty($row['errors'])): ?>
                                                    <span class="badge badge-danger">Invalid</span>
                                                <?php else: ?>
                                                    <span class="badge badge-success">Valid</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="<?= site_url('user/import') ?>" class="btn btn-secondary">Kembali</a>
                            <form action="<?= site_url('user/import_commit') ?>" method="post">
                                <button type="submit" class="btn btn-primary" <?= $valid_count === 0 ? 'disabled' : '' ?>>
                                    Import Data
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
