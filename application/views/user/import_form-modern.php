<section class="page-header">
    <div class="header-content">
        <div class="header-info">
            <h1 class="page-title">Import Pegawai</h1>
            <p class="page-subtitle">Tambah data pegawai melalui file Excel</p>
        </div>
    </div>
</section>

<section class="page-content">
    <div class="card">
        <div class="card-body">
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $this->session->flashdata('error') ?>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="import-instructions">
                <div class="instruction-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="instruction-content">
                    <h3>Petunjuk Import</h3>
                    <ol class="instruction-list">
                        <li>Unduh template Excel di bawah ini</li>
                        <li>Isi kolom <strong>NIP</strong> (Nomor Induk Pegawai)</li>
                        <li>Isi kolom <strong>Nama</strong> lengkap pegawai</li>
                        <li>Isi kolom <strong>Unit</strong> (satker/unit kerja)</li>
                        <li>Upload file yang sudah diisi</li>
                    </ol>
                </div>
            </div>

            <div class="download-section">
                <a href="<?= site_url('user/download_template') ?>" class="btn btn-secondary">
                    <i class="fas fa-download"></i>
                    Download Template
                </a>
            </div>

            <form action="<?= site_url('user/import_preview') ?>" method="post" enctype="multipart/form-data" class="modern-form">
                <div class="form-group">
                    <label for="import_file" class="form-label">File Excel <span class="required">*</span></label>
                    <div class="upload-area" id="uploadArea">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h3>Pilih File</h3>
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
                    <span class="input-info">Format: .xlsx atau .xls (maks 2MB)</span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-eye"></i>
                        Preview
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
    .page-header { margin-bottom: 24px; }
    .header-content { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; }
    .page-title { font-size: 28px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; }
    .page-subtitle { font-size: 14px; color: var(--text-secondary); }
    .import-instructions { background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: var(--radius-lg); padding: 20px; display: flex; gap: 16px; margin-bottom: 24px; }
    .instruction-icon { width: 48px; height: 48px; background: rgba(59, 130, 246, 0.1); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .instruction-icon i { font-size: 20px; color: var(--accent); }
    .instruction-content h3 { font-size: 15px; font-weight: 600; color: var(--text-primary); margin-bottom: 10px; }
    .instruction-list { padding-left: 18px; color: var(--text-secondary); font-size: 13px; }
    .instruction-list li { margin-bottom: 4px; }
    .download-section { margin-bottom: 20px; }
    .modern-form { max-width: 500px; }
    .form-group { margin-bottom: 20px; }
    .required { color: var(--danger); }
    .upload-area { border: 2px dashed var(--border); border-radius: var(--radius-lg); padding: 40px 20px; text-align: center; cursor: pointer; transition: all 0.2s; position: relative; }
    .upload-area:hover { border-color: var(--accent); background: rgba(59, 130, 246, 0.02); }
    .upload-area.dragover { border-color: var(--accent); background: rgba(59, 130, 246, 0.05); }
    .upload-input { position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; cursor: pointer; }
    .upload-icon { width: 56px; height: 56px; background: var(--surface-elevated); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
    .upload-icon i { font-size: 24px; color: var(--text-secondary); }
    .upload-area h3 { font-size: 15px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
    .upload-area p { color: var(--text-secondary); font-size: 13px; }
    .file-info { display: flex; align-items: center; gap: 12px; padding: 14px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius); margin-top: 12px; }
    .file-info i { font-size: 20px; color: var(--success); }
    .file-info span { flex: 1; font-weight: 500; color: var(--text-primary); font-size: 14px; }
    .remove-file { background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 4px; transition: color 0.2s; }
    .remove-file:hover { color: var(--danger); }
    .input-info { font-size: 12px; color: var(--text-secondary); margin-top: 6px; display: block; }
    .form-actions { margin-top: 24px; }
    .form-actions .btn:disabled { opacity: 0.5; cursor: not-allowed; }
</style>

<script>
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('import_file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitBtn');
    const removeFile = document.getElementById('removeFile');

    uploadArea.addEventListener('dragover', (e) => { e.preventDefault(); uploadArea.classList.add('dragover'); });
    uploadArea.addEventListener('dragleave', () => { uploadArea.classList.remove('dragover'); });
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        if (e.dataTransfer.files.length) { fileInput.files = e.dataTransfer.files; handleFileSelect(e.dataTransfer.files[0]); }
    });
    fileInput.addEventListener('change', (e) => { if (e.target.files.length) { handleFileSelect(e.target.files[0]); } });

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
