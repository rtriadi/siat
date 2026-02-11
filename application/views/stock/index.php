<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Stok Item</h3>
                        <div class="card-tools">
                            <a href="<?= site_url('stock/create') ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Tambah Item
                            </a>
                        </div>
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

                        <?php if (empty($grouped_items)): ?>
                            <div class="alert alert-info">
                                Belum ada item stok. <a href="<?= site_url('stock/create') ?>">Tambah item pertama</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($grouped_items as $category_name => $items): ?>
                                <h5 class="mt-3 mb-2">
                                    <i class="fas fa-folder-open"></i> <?= htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8') ?>
                                </h5>
                                <?php $this->load->view('stock/partials/stock_table', ['items' => $items]); ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
