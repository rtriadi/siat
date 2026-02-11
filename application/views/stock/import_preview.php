<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Preview Import Restock</h3>
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

                        <?php $this->load->view('stock/partials/import_table', ['rows' => $rows]); ?>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="<?= site_url('stock_import/import') ?>" class="btn btn-secondary">Kembali</a>
                            <form action="<?= site_url('stock_import/import_commit') ?>" method="post">
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
