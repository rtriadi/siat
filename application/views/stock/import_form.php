<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Import Restock</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $this->session->flashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('success')): ?>
                            <div class="alert alert-success" role="alert">
                                <?= $this->session->flashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <p>Unduh template Excel lalu isi kolom Item ID (atau Category + Item Name), Qty, dan Note (opsional).</p>
                        <a href="<?= site_url('stock_import/download_template') ?>" class="btn btn-info mb-3">
                            Download Template
                        </a>

                        <form action="<?= site_url('stock_import/import_preview') ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="import_file">File Excel</label>
                                <input type="file" class="form-control" id="import_file" name="import_file" accept=".xlsx,.xls" required>
                                <small class="form-text text-muted">Format file: .xlsx atau .xls (maks 2MB).</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Preview</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
