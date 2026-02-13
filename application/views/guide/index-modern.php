<div class="page-header">
    <div class="page-header-content">
        <div>
            <h1 class="page-title">Panduan Aplikasi</h1>
            <p class="page-subtitle">Sistem Inventori ATK Terpadu - Dokumentasi & Tutorial</p>
        </div>
    </div>
</div>

<div class="content">
    <div class="guide-container">
        <div class="guide-hero">
            <div class="guide-hero-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="guide-hero-content">
                <h2>Selamat Datang di SIAT</h2>
                <p>Panduan lengkap untuk menggunakan Sistem Inventori ATK Terpadu. Pilih tab di bawah sesuai dengan peran Anda.</p>
            </div>
        </div>

        <div class="guide-tabs">
            <button class="tab-btn active" data-tab="overview">
                <i class="fas fa-info-circle"></i>
                <span>Ringkasan</span>
            </button>
            <?php if ((int) $this->session->userdata('level') === 1): ?>
            <button class="tab-btn" data-tab="admin">
                <i class="fas fa-user-shield"></i>
                <span>Administrator</span>
            </button>
            <?php endif; ?>
            <?php if ((int) $this->session->userdata('level') === 2): ?>
            <button class="tab-btn" data-tab="employee">
                <i class="fas fa-user"></i>
                <span>Pegawai</span>
            </button>
            <?php endif; ?>
            <button class="tab-btn" data-tab="status">
                <i class="fas fa-signal"></i>
                <span>Status</span>
            </button>
            <button class="tab-btn" data-tab="faq">
                <i class="fas fa-question-circle"></i>
                <span>FAQ</span>
            </button>
        </div>

        <div class="tab-content active" id="overview">
            <div class="overview-grid">
                <div class="overview-card">
                    <div class="overview-icon" style="background: linear-gradient(135deg, #4a90d9, #2563eb);">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3>Kelola Inventori</h3>
                    <p>Kelola semua item ATK dengan mudah termasuk penambahan, pengeditan, dan pengaturan kategori.</p>
                </div>
                <div class="overview-card">
                    <div class="overview-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3>Persetujuan Cepat</h3>
                    <p>Proses permintaan ATK dari karyawan dengan persetujuan dan penolakan yang cepat.</p>
                </div>
                <div class="overview-card">
                    <div class="overview-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Laporan Lengkap</h3>
                    <p>Akses laporan mendalam termasuk pergerakan stok, audit trail, dan tingkat persediaan.</p>
                </div>
                <div class="overview-card">
                    <div class="overview-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Notifikasi Real-time</h3>
                    <p>Dapatkan notifikasi otomatis untuk permintaan masuk dan stok yang menipis.</p>
                </div>
            </div>

            <div class="flow-diagram">
                <h3><i class="fas fa-project-diagram"></i> Alur Permintaan ATK</h3>
                <div class="flow-steps">
                    <div class="flow-step">
                        <div class="flow-icon"><i class="fas fa-plus-circle"></i></div>
                        <div class="flow-label">Pegawai Buat<br>Permintaan</div>
                    </div>
                    <div class="flow-arrow"><i class="fas fa-arrow-right"></i></div>
                    <div class="flow-step">
                        <div class="flow-icon pending"><i class="fas fa-clock"></i></div>
                        <div class="flow-label">Pending</div>
                    </div>
                    <div class="flow-arrow"><i class="fas fa-arrow-right"></i></div>
                    <div class="flow-step">
                        <div class="flow-icon review"><i class="fas fa-search"></i></div>
                        <div class="flow-label">Admin Review</div>
                    </div>
                    <div class="flow-arrow"><i class="fas fa-arrow-right"></i></div>
                    <div class="flow-step">
                        <div class="flow-icon approved"><i class="fas fa-check"></i></div>
                        <div class="flow-label">Disetujui</div>
                    </div>
                    <div class="flow-arrow"><i class="fas fa-arrow-right"></i></div>
                    <div class="flow-step">
                        <div class="flow-icon delivered"><i class="fas fa-truck"></i></div>
                        <div class="flow-label">Diserahkan</div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ((int) $this->session->userdata('level') === 1): ?>
        <div class="tab-content" id="admin">
            <div class="guide-section">
                <div class="section-header">
                    <i class="fas fa-user-shield"></i>
                    <h2>Panduan Administrator</h2>
                </div>
                
                <div class="step-cards">
                    <div class="step-card">
                        <div class="step-card-number">01</div>
                        <div class="step-card-content">
                            <h3>Login & Persiapan</h3>
                            <p>Masuk menggunakan kredensial administrator. Jika pertama kali, pastikan password sudah diganti.</p>
                            <div class="code-block">
                                <code>Username: admin</code>
                                <code>Password: admin123</code>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">02</div>
                        <div class="step-card-content">
                            <h3>Setup Awal</h3>
                            <p>Sebelum operasional, lakukan setup berikut:</p>
                            <ul class="feature-list">
                                <li><i class="fas fa-folder"></i> <strong>Kelola Kategori</strong> - Buat kategori item ATK</li>
                                <li><i class="fas fa-users"></i> <strong>Import Pegawai</strong> - Data karyawan dari Excel</li>
                                <li><i class="fas fa-box"></i> <strong>Import Stock</strong> - Data inventori ATK awal</li>
                            </ul>
                            <p class="tip"><i class="fas fa-lightbulb"></i> Ikuti format template Excel yang disediakan.</p>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">03</div>
                        <div class="step-card-content">
                            <h3>Kelola Kategori & Stok</h3>
                            <ul class="feature-list">
                                <li><i class="fas fa-folder"></i> <strong>Kelola Kategori</strong> - Buat dan manage kategori item</li>
                                <li><i class="fas fa-boxes"></i> <strong>Stock Management</strong> - Tambah, edit, dan sesuaikan stok</li>
                                <li><i class="fas fa-plus"></i> <strong>Tambah Item</strong> - Input item baru dengan kategori dan threshold</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">04</div>
                        <div class="step-card-content">
                            <h3>Kelola Permintaan</h3>
                            <p>Menu <strong>Request Management</strong> untuk:</p>
                            <ul class="feature-list">
                                <li><i class="fas fa-check-circle"></i> <strong>Approve</strong> - Menyetetujui permintaan</li>
                                <li><i class="fas fa-times-circle"></i> <strong>Reject</strong> - Menolak dengan alasan</li>
                                <li><i class="fas fa-truck"></i> <strong>Deliver</strong> - Menyerahkan item ke pegawai</li>
                            </ul>
                        </div>
                    </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">03</div>
                        <div class="step-card-content">
                            <h3>Kelola Kategori & Stok</h3>
                            <ul class="feature-list">
                                <li><i class="fas fa-folder"></i> <strong>Kelola Kategori</strong> - Buat dan manage kategori item</li>
                                <li><i class="fas fa-boxes"></i> <strong>Stock Management</strong> - Tambah, edit, dan sesuaikan stok</li>
                                <li><i class="fas fa-plus"></i> <strong>Tambah Item</strong> - Input item baru dengan kategori dan threshold</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">04</div>
                        <div class="step-card-content">
                            <h3>Kelola Permintaan</h3>
                            <p>Menu <strong>Request Management</strong> untuk:</p>
                            <ul class="feature-list">
                                <li><i class="fas fa-check-circle"></i> <strong>Approve</strong> - Menyetujui permintaan</li>
                                <li><i class="fas fa-times-circle"></i> <strong>Reject</strong> - Menolak dengan alasan</li>
                                <li><i class="fas fa-truck"></i> <strong>Deliver</strong> - Menyerahkan item ke pegawai</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">05</div>
                        <div class="step-card-content">
                            <h3>Kelola Pengguna</h3>
                            <ul class="feature-list">
                                <li><i class="fas fa-users"></i> <strong>Kelola Pengguna</strong> - Lihat dan kelola akun pengguna</li>
                                <li><i class="fas fa-user-plus"></i> <strong>Tambah Manual</strong> - Tambah pengguna baru secara manual</li>
                                <li><i class="fas fa-file-import"></i> <strong>Import Pegawai</strong> - Import data dari Excel</li>
                                <li><i class="fas fa-user-check"></i> <strong>Aktivasi</strong> - Aktifkan/nonaktifkan pengguna</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">06</div>
                        <div class="step-card-content">
                            <h3>Backup & Keamanan</h3>
                            <ul class="feature-list">
                                <li><i class="fas fa-database"></i> <strong>Backup Database</strong> - Export database secara manual</li>
                                <li><i class="fas fa-key"></i> <strong>Ganti Password</strong> - Ubah password akun</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">07</div>
                        <div class="step-card-content">
                            <h3>Laporan & Monitoring</h3>
                            <ul class="feature-list">
                                <li><i class="fas fa-history"></i> <strong>Request History</strong> - Riwayat permintaan</li>
                                <li><i class="fas fa-exchange-alt"></i> <strong>Stock Movement</strong> - Pergerakan stok</li>
                                <li><i class="fas fa-shield-alt"></i> <strong>Audit Trail</strong> - Log aktivitas sistem</li>
                                <li><i class="fas fa-chart-bar"></i> <strong>Stock Levels</strong> - Stok per kategori</li>
                            </ul>
                            <p class="tip"><i class="fas fa-file-excel"></i> Semua laporan dapat diekspor ke Excel.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ((int) $this->session->userdata('level') === 2): ?>
        <div class="tab-content" id="employee">
            <div class="guide-section">
                <div class="section-header">
                    <i class="fas fa-user"></i>
                    <h2>Panduan Pegawai</h2>
                </div>
                
                <div class="step-cards">
                    <div class="step-card">
                        <div class="step-card-number">01</div>
                        <div class="step-card-content">
                            <h3>Login</h3>
                            <p>Masukkan username dan password yang diberikan administrator.</p>
                            <div class="code-block">
                                <code>Password default: NIP Anda</code>
                            </div>
                            <p class="warning"><i class="fas fa-exclamation-triangle"></i> Ganti password jika masih menggunakan NIP.</p>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">02</div>
                        <div class="step-card-content">
                            <h3>Buat Permintaan ATK</h3>
                            <ol class="ordered-list">
                                <li>Klik menu <strong>"Permintaan ATK"</strong></li>
                                <li>Klik tombol <strong>"Buat Permintaan"</strong></li>
                                <li>Pilih item yang tersedia dari daftar</li>
                                <li>Masukkan <strong>jumlah</strong> yang dibutuhkan</li>
                                <li>Tambahkan <strong>catatan</strong> jika diperlukan</li>
                                <li>Klik <strong>"Kirim Permintaan"</strong></li>
                            </ol>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">03</div>
                        <div class="step-card-content">
                            <h3>Lacak Permintaan</h3>
                            <p>Cek status permintaan di menu "Permintaan ATK":</p>
                            <div class="status-grid">
                                <div class="status-item">
                                    <span class="status-badge pending">Pending</span>
                                    <span>Menunggu persetujuan</span>
                                </div>
                                <div class="status-item">
                                    <span class="status-badge approved">Disetujui</span>
                                    <span>Siap diambil</span>
                                </div>
                                <div class="status-item">
                                    <span class="status-badge delivered">Diterima</span>
                                    <span>Sudah diambil</span>
                                </div>
                                <div class="status-item">
                                    <span class="status-badge rejected">Ditolak</span>
                                    <span>Tidak disetujui</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-card-number">04</div>
                        <div class="step-card-content">
                            <h3>Notifikasi</h3>
                            <p>Anda akan mendapat notifikasi saat:</p>
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i> Permintaan disetujui</li>
                                <li><i class="fas fa-times"></i> Permintaan ditolak</li>
                                <li><i class="fas fa-truck"></i> Item siap diambil</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="tab-content" id="status">
            <div class="guide-section">
                <div class="section-header">
                    <i class="fas fa-signal"></i>
                    <h2>Status & Kuantitas Stok</h2>
                </div>

                <div class="status-table-container">
                    <table class="status-table">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Deskripsi</th>
                                <th>Icon</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="status-tag available">Tersedia</span></td>
                                <td>Stok yang dapat diminta olehpegawai</td>
                                <td><i class="fas fa-check-circle" style="color: #10b981;"></i></td>
                            </tr>
                            <tr>
                                <td><span class="status-tag reserved">Direservasi</span></td>
                                <td>Stok yang sudah disetujui tapi belum diserahkan</td>
                                <td><i class="fas fa-clock" style="color: #f59e0b;"></i></td>
                            </tr>
                            <tr>
                                <td><span class="status-tag used">Digunakan</span></td>
                                <td>Stok yang sudah diserahkan kepegawai</td>
                                <td><i class="fas fa-check-double" style="color: #4a90d9;"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Low Stock Alert</strong>
                        <p>Item dengan stok di bawah threshold minimum akan muncul notifikasi otomatis di dashboard admin.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="faq">
            <div class="guide-section">
                <div class="section-header">
                    <i class="fas fa-question-circle"></i>
                    <h2>Pertanyaan Umum</h2>
                </div>

                <div class="faq-accordion">
                    <div class="faq-item">
                        <button class="faq-question">
                            <span>Lupa password bagaimana?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p>Hubungi administrator sistem untuk mereset password Anda. Password baru akan dikirimkan melalui email atau langsung diberikan.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question">
                            <span>Tidak bisa buat permintaan untuk item tertentu?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p>Item tersebut mungkin stoknya sudah 0 atau di bawah threshold minimum. Silakan hubungi admin untuk info lebih lanjut.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question">
                            <span>Bisakah batalkan permintaan setelah disetujui?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p>Ya, selama admin belum melakukan deliver (menyerahkan) item. Setelah status menjadi "Diterima", permintaan tidak dapat dibatalkan.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question">
                            <span>Bagaimana cara melihat riwayat permintaan saya?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p>Klik menu "Permintaan ATK" - semua permintaan Anda akan ditampilkan beserta statusnya.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <button class="faq-question">
                            <span>Berapa maksimal jumlah permintaan?</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="faq-answer">
                            <p>Batas jumlah disesuaikan dengan ketersediaan stok dan kebijakan organisasi. Jika melebihi stok tersedia, admin akan melakukan penyesuaian.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.add('active');
    });
});

document.querySelectorAll('.faq-question').forEach(btn => {
    btn.addEventListener('click', () => {
        btn.classList.toggle('active');
        const answer = btn.nextElementSibling;
        answer.style.maxHeight = answer.style.maxHeight ? null : answer.scrollHeight + 'px';
    });
});
</script>

<style>
.guide-container {
    max-width: 1000px;
    margin: 0 auto;
}

.guide-hero {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
    border-radius: 16px;
    padding: 32px;
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 32px;
    color: white;
}

.guide-hero-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.15);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    flex-shrink: 0;
}

.guide-hero-content h2 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;
}

.guide-hero-content p {
    opacity: 0.9;
    font-size: 15px;
    line-height: 1.6;
}

.guide-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0;
    overflow-x: auto;
}

.tab-btn {
    padding: 12px 20px;
    border: none;
    background: none;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
    transition: all 0.2s;
}

.tab-btn:hover {
    color: #4a90d9;
}

.tab-btn.active {
    color: #4a90d9;
    border-bottom-color: #4a90d9;
}

.tab-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

.tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.overview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.overview-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: transform 0.2s, box-shadow 0.2s;
}

.overview-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}

.overview-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    margin-bottom: 16px;
}

.overview-card h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    color: #1f2937;
}

.overview-card p {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.5;
}

.flow-diagram {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.flow-diagram h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #1f2937;
}

.flow-steps {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}

.flow-step {
    text-align: center;
}

.flow-icon {
    width: 50px;
    height: 50px;
    background: #e5e7eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    margin: 0 auto 8px;
    color: #6b7280;
}

.flow-icon.pending { background: #fef3c7; color: #f59e0b; }
.flow-icon.review { background: #dbeafe; color: #3b82f6; }
.flow-icon.approved { background: #d1fae5; color: #10b981; }
.flow-icon.delivered { background: #e0e7ff; color: #6366f1; }

.flow-label {
    font-size: 11px;
    color: #6b7280;
    font-weight: 500;
}

.flow-arrow {
    color: #9ca3af;
    font-size: 14px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e5e7eb;
}

.section-header i {
    font-size: 24px;
    color: #4a90d9;
}

.section-header h2 {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.step-cards {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.step-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    display: flex;
    gap: 20px;
    transition: transform 0.2s;
}

.step-card:hover {
    transform: translateX(4px);
}

.step-card-number {
    font-size: 32px;
    font-weight: 800;
    color: #e5e7eb;
    line-height: 1;
    flex-shrink: 0;
}

.step-card-content h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #1f2937;
}

.step-card-content p {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 12px;
}

.code-block {
    background: #f3f4f6;
    border-radius: 8px;
    padding: 12px 16px;
    margin: 12px 0;
}

.code-block code {
    display: block;
    font-family: monospace;
    font-size: 13px;
    color: #4b5563;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.feature-list li {
    padding: 8px 0;
    font-size: 14px;
    color: #4b5563;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.feature-list li i {
    color: #4a90d9;
    margin-top: 3px;
}

.tip {
    background: #ecfdf5;
    border-left: 3px solid #10b981;
    padding: 10px 14px;
    border-radius: 0 8px 8px 0;
    font-size: 13px;
    color: #065f46;
    display: flex;
    align-items: center;
    gap: 8px;
}

.warning {
    background: #fef3c7;
    border-left: 3px solid #f59e0b;
    padding: 10px 14px;
    border-radius: 0 8px 8px 0;
    font-size: 13px;
    color: #92400e;
    display: flex;
    align-items: center;
    gap: 8px;
}

.ordered-list {
    padding-left: 20px;
    margin: 0;
}

.ordered-list li {
    padding: 6px 0;
    font-size: 14px;
    color: #4b5563;
    line-height: 1.5;
}

.ordered-list li strong {
    color: #1f2937;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 12px;
    margin-top: 16px;
}

.status-item {
    display: flex;
    flex-direction: column;
    gap: 6px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    font-size: 12px;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.status-badge.pending { background: #fef3c7; color: #92400e; }
.status-badge.approved { background: #d1fae5; color: #065f46; }
.status-badge.delivered { background: #dbeafe; color: #1e40af; }
.status-badge.rejected { background: #fee2e2; color: #991b1b; }

.status-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    margin-bottom: 24px;
}

.status-table {
    width: 100%;
    border-collapse: collapse;
}

.status-table th {
    background: #f9fafb;
    padding: 14px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: #6b7280;
}

.status-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
}

.status-tag {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-tag.available { background: #d1fae5; color: #065f46; }
.status-tag.reserved { background: #fef3c7; color: #92400e; }
.status-tag.used { background: #dbeafe; color: #1e40af; }

.info-box {
    background: #eff6ff;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

.info-box > i {
    font-size: 20px;
    color: #3b82f6;
    margin-top: 2px;
}

.info-box strong {
    display: block;
    color: #1e40af;
    margin-bottom: 4px;
}

.info-box p {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
}

.faq-accordion {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.faq-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.faq-question {
    width: 100%;
    padding: 18px 20px;
    border: none;
    background: none;
    text-align: left;
    font-size: 15px;
    font-weight: 600;
    color: #1f2937;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.2s;
}

.faq-question:hover {
    background: #f9fafb;
}

.faq-question i {
    font-size: 12px;
    color: #9ca3af;
    transition: transform 0.3s;
}

.faq-question.active i {
    transform: rotate(180deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    background: #f9fafb;
}

.faq-answer p {
    padding: 16px 20px;
    margin: 0;
    font-size: 14px;
    color: #6b7280;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .guide-hero {
        flex-direction: column;
        text-align: center;
    }
    
    .step-card {
        flex-direction: column;
    }
    
    .flow-steps {
        flex-direction: column;
    }
    
    .flow-arrow {
        transform: rotate(90deg);
    }
}
</style>
