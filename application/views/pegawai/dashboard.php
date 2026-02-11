<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Dashboard Pegawai</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <?php if ($this->session->userdata('must_change_password')): ?>
            <div class="alert alert-warning" role="alert">
                Password Anda masih default. Silakan ubah melalui menu ubah password.
            </div>
        <?php endif; ?>
        <div class="card">
            <div class="card-body">
                <p>Selamat datang. Ajukan permintaan ATK melalui menu yang tersedia.</p>
            </div>
        </div>
    </div>
</section>
