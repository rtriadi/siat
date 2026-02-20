<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<section class="page-header">
    <div class="header-content">
        <div class="header-info">
            <h1 class="page-title">Import Stok</h1>
            <p class="page-subtitle">Tambah barang baru atau tambah stok barang yang sudah ada</p>
        </div>
    </div>
</section>

<section class="page-content">
    <div class="card">
        <div class="card-body">
            
            

            <div class="import-instructions">
                <div class="instruction-icon">
                    <i class="fas fa-file-excel"></i>
                </div>
                <div class="instruction-content">
                    <h3>Petunjuk Import</h3>
                    <p class="format-info">Import berfungsi untuk <strong>membuat barang baru</strong> atau <strong>menambah stok</strong> barang yang sudah ada berdasarkan kategori dan nama barang.</p>
                    <ol class="instruction-list">
                        <li>Unduh template Excel di bawah ini</li>
                        <li>Isi data sesuai kolom:
                            <ul>
                                <li><strong>Kategori</strong> - Nama kategori (wajib, contoh: ATK, Elektronik)</li>
                                <li><strong>Nama Barang</strong> - Nama item (wajib)</li>
                                <li><strong>Jumlah</strong> - Stok awal/jumlah yang ditambah (wajib)</li>
                                <li><strong>Satuan</strong> - Opsional (default: Pcs)</li>
                                <li><strong>Stok Minimum</strong> - Opsional untuk batas minimum (default: 10)</li>
                            </ul>
                        </li>
                        <li>Jika barang dengan nama yang sama di kategori yang sama sudah ada, stok akan <strong>ditambahkan</strong></li>
                        <li>Jika barang belum ada, akan <strong>dibuat baru</strong></li>
                        <li>Upload file yang sudah diisi</li>
                    </ol>
                </div>
            </div>

            <div class="download-section">
                <a href="<?= site_url('stock_import/download_template') ?>" class="btn btn-primary">
                    <i class="fas fa-download"></i>
                    Download Template
                </a>
            </div>

            <?php if (isset($needs_rollover) && $needs_rollover): ?>
            <div class="alert alert-warning" style="background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 24px; border-radius: var(--radius-lg); margin-top: 16px;">
                <h3 style="margin-top: 0; font-size: 18px; font-weight: 600;"><i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i> Perhatian: Saldo Awal Belum Ditarik</h3>
                <p style="margin-bottom: 20px;">Data stok sisa dari tahun sebelumnya belum ditarik ke tahun <strong><?= $login_year ?></strong>. Anda wajib menyelesaikan proses penarikan saldo awal ini terlebih dahulu sebelum dapat mengimpor stok baru.</p>
                <form action="<?= site_url('stock_import/do_rollover') ?>" method="post">
                    <button type="submit" class="btn btn-warning" style="background-color: #f59e0b; color: #fff; border: none; font-weight: bold; padding: 10px 20px;">
                        <i class="fas fa-file-import" style="margin-right: 8px;"></i> Tarik Data Stok Sisa Tahun Sebelumnya
                    </button>
                </form>
            </div>
            <?php else: ?>
            <form action="<?= site_url('stock_import/import_preview') ?>" method="post" enctype="multipart/form-data" class="import-form">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h3>Upload File Excel</h3>
                    <p>atau klik untuk memilih file</p>
                    <input type="file" name="import_file" id="import_file" class="upload-input" accept=".xlsx,.xls" required>
                </div>
                <div class="file-info" id="fileInfo" style="display: none;">
                    <i class="fas fa-file-excel"></i>
                    <span id="fileName"></span>
                    <button type="button" class="remove-file" id="removeFile">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <?php 
                    $session_year = $this->session->userdata('login_year') ?? date('Y');
                    $min_date = $session_year . '-01-01'; 
                    $max_date = $session_year . '-12-31'; 
                    $default_date = (date('Y') == $session_year) ? date('Y-m-d') : $max_date;
                ?>
                <div class="form-group" style="margin-top: 20px; max-width: 320px;">
                    <label for="purchase_date" class="form-label">
                        Tanggal Pembelian <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="date" name="purchase_date" id="purchase_date" class="form-control"
                           value="<?= $default_date ?>" min="<?= $min_date ?>" max="<?= $max_date ?>" required>
                    <span class="input-info">Tanggal saat barang dibeli/diterima</span>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-eye"></i>
                        Preview
                    </button>
                </div>
            </form>
            <?php endif; ?>
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
    
    .import-instructions {
        background: rgba(59, 130, 246, 0.05);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: var(--radius-lg);
        padding: 24px;
        display: flex;
        gap: 20px;
        margin-bottom: 24px;
    }
    
    .instruction-icon {
        width: 56px;
        height: 56px;
        background: rgba(59, 130, 246, 0.1);
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .instruction-icon i {
        font-size: 24px;
        color: var(--accent);
    }
    
    .instruction-content h3 {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 12px;
    }
    
    .format-info {
        color: var(--text-secondary);
        margin-bottom: 12px;
        font-size: 14px;
    }
    
    .format-info strong {
        color: var(--text-primary);
    }
    
    .instruction-list {
        padding-left: 20px;
        color: var(--text-secondary);
    }
    
    .instruction-list li {
        margin-bottom: 8px;
    }
    
    .instruction-list ul {
        margin-top: 8px;
        padding-left: 20px;
    }
    
    .instruction-list ul li {
        margin-bottom: 4px;
    }
    
    .download-section {
        margin-bottom: 24px;
    }
    
    .import-form {
        max-width: 500px;
    }
    
    .upload-area {
        border: 2px dashed var(--border);
        border-radius: var(--radius-lg);
        padding: 48px 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }
    
    .upload-area:hover {
        border-color: var(--accent);
        background: rgba(59, 130, 246, 0.02);
    }
    
    .upload-area.dragover {
        border-color: var(--accent);
        background: rgba(59, 130, 246, 0.05);
    }
    
    .upload-input {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }
    
    .upload-icon {
        width: 64px;
        height: 64px;
        background: var(--surface-elevated);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }
    
    .upload-icon i {
        font-size: 28px;
        color: var(--text-secondary);
    }
    
    .upload-area h3 {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 4px;
    }
    
    .upload-area p {
        color: var(--text-secondary);
        font-size: 14px;
    }
    
    .file-info {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: var(--radius);
        margin-top: 16px;
    }
    
    .file-info i {
        font-size: 24px;
        color: var(--success);
    }
    
    .file-info span {
        flex: 1;
        font-weight: 500;
        color: var(--text-primary);
    }
    
    .remove-file {
        background: none;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        padding: 4px;
        transition: color 0.2s;
    }
    
    .remove-file:hover {
        color: var(--danger);
    }
    
    .form-actions {
        margin-top: 24px;
    }
    
    .form-actions .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

<script>
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('import_file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitBtn');
    const removeFile = document.getElementById('removeFile');

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleFileSelect(e.target.files[0]);
        }
    });

    function handleFileSelect(file) {
        fileName.textContent = file.name;
        fileInfo.style.display = 'flex';
        uploadArea.style.display = 'none';
        submitBtn.disabled = false;
    }

    removeFile.addEventListener('click', () => {
        fileInput.value = '';
        fileInfo.style.display = 'none';
        uploadArea.style.display = 'block';
        submitBtn.disabled = true;
    });
</script>
