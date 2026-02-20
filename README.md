# SIAT - Sistem Inventori ATK Terpadu

SIAT (Sistem Inventori ATK Terpadu) adalah sistem informasi berbasis web untuk memanajemen inventaris Alat Tulis Kantor (ATK) yang memfasilitasi proses permintaan barang dari Pegawai hingga proses persetujuan dan pengelolaan stok oleh Admin secara terpusat.

Aplikasi ini dirancang khusus untuk memenuhi standar administrasi dengan fitur cetak laporan PDF beserta tanda tangan pejabat terkait.

## Fitur Unggulan Baru
- **Global Year Filter**: Seluruh data transaksi otomatis menyesuaikan dengan tahun yang dipilih saat login.
- **Tutup Periode & Tarik Sisa Stok**: Mengunci perubahan data akhir tahun dan memindahkan otomatis sisa stok ke tahun berikutnya.
- **Pencarian Real-Time**: Kolom pencarian data bereaksi instan tanpa perlu menekan tombol Enter (menggunakan algoritma *debouncing* anti-*lag*).

## Peran Pengguna (Roles)
Sistem ini menggunakan dua level pengguna utama:
1. **Admin (Level 1)**: Administrator yang mengelola master data barang, stok, kategori, pengguna, master penandatangan laporan, serta memberikan persetujuan (approve/deliver/reject) atas permintaan ATK dari pegawai.
2. **Pegawai (Level 2)**: Pengguna sistem yang dapat melihat sisa stok tersedia dan mengajukan permintaan ATK sesuai kebutuhan bidang/jabatan kerjanya.

---

## Panduan Lengkap Menu Aplikasi (Admin)

### 1. Menu Utama
- **Dashboard**
  Halaman ringkasan yang menampilkan statistik utama (Total Jenis Barang, Menunggu Persetujuan, Stok Menipis, Diserahkan Hari Ini), permintaan terbaru, dan aksi cepat.

### 2. Inventori
- **Kelola Stok**
  Menu untuk melihat, menambah, mengubah, dan menghapus data barang ATK. Lengkap dengan informasi stok tersedia, stok direservasi (ditahan sementara menunggu persetujuan), batas stok minimum, fungsi penyesuaian (adjustment) stok, dan tombol **Tutup Periode** untuk mengunci tahun aktif.

- **Kelola Kategori**
  Menu untuk mengelompokkan barang-barang ATK berdasarkan jenisnya (contoh: Kertas, Alat Tulis, Tinta, dll).

- **Import Stok**
  Fungsi manajemen stok massal. Memungkinkan Admin mengunduh template Excel, mengisi data awal atau data restock barang, mencatat **Tanggal Pembelian**, lalu mengunggah (.xls/.xlsx) dan memprosesnya sekaligus. Jika ada pergantian tahun, di sinilah admin akan menekan tombol raksasa **Tarik Data Stok Sisa Tahun Sebelumnya**.

- **Kelola Pengguna**
  Menu manajemen akun. Mengatur status aktif/non-aktif akun, reset password, hapus, dan pendaftaran pengguna baru. Data pegawai mencakup Nama, NIP, dan **Jabatan**.

- **Import Pegawai**
  Fasilitas untuk mendaftarkan akun pegawai baru secara massal menggunakan file Excel (kolom: Nama, Username, NIP, Jabatan).

- **Kelola Permintaan**
  Pusat kendali permintaan. Admin melihat semua permohonan ATK yang diajukan pegawai. Tersedia fitur Reject, Approve, dan Deliver.

- **Buat Permintaan**
  Hak bagi Admin untuk membuat permintaan ATK secara langsung tanpa melalui akun pegawai.

- **Notifikasi**
  Pusat pemberitahuan sistem, misalnya jika ada permintaan ATK baru dari pegawai.

### 3. Laporan
- **Riwayat Permintaan**
  Laporan rekam jejak historis seluruh permintaan ATK berdasarkan rentang waktu tertentu yang bisa diekspor ke Excel.

- **Pergerakan Stok**
  Laporan histori masuk/keluar stok dengan label tipe: **Masuk** (stok masuk dari import) dan **Keluar** (stok keluar karena permintaan).

- **Jejak Audit**
  Fungsi audit log yang mencatat pergerakan barang setiap user sebagai pertanggungjawaban gudang.

- **Level Stok**
  Fungsi monitoring posisi sisa stok saat ini per kategori dengan indikator low stock.

- **Cadangkan Database**
  Alat bantu administrator untuk mengunduh keseluruhan basis data demi keamanan (disaster recovery).

- **Cetak Laporan (PDF)**
  Menu pembuatan laporan resmi berskala cetak dengan periode fleksibel (Bulanan, Tahunan, Rentang Tanggal). Mencakup 3 jenis laporan:
  1. **Buku Bantu Penerimaan** - Rincian barang masuk harian
  2. **Buku Bantu Pengeluaran** - Rincian barang keluar harian
  3. **Daftar Keadaan Barang** - Mutasi Saldo Awal, Masuk, Keluar, dan Saldo Akhir

### 4. Bantuan
- **Panduan**
  Halaman penjelasan alur sistem yang dapat diakses dari dalam aplikasi.

---

## Panduan Lengkap Menu Aplikasi (Pegawai)

### 1. Inventori
- **Permintaan ATK**
  Menu utama bagi pegawai untuk mengajukan kebutuhan alat tulis. Pegawai akan melihat daftar barang yang Available, menentukan kuantitas, dan menunggu status pengajuannya (Pending, Disetujui, Diterima, atau Ditolak).

- **Notifikasi**
  Notifikasi masuk jika pengajuan ATK disetujui, ditolak, atau pesanan telah siap diambil.

### 2. Akun
- **Ubah Password**
  Fasilitas mandiri pegawai untuk memperbarui kata sandi akunnya.

- **Panduan**
  Menu panduan dasar penggunaan bagi pegawai.

---

## Alur Kerja Aplikasi (Workflow)

1. **Inisialisasi Master Data** (Oleh Admin):
   Menggunakan menu `Kelola Kategori`, `Kelola Pengguna`, `Import Stok`, lalu menentukan Master Penandatangan.

2. **Pengajuan Permintaan** (Oleh Pegawai):
   Pegawai membuat permohonan ATK. Sistem memotong Available Qty menjadi Reserved Qty agar stok tidak minus/oversold.

3. **Persetujuan & Penyerahan** (Oleh Admin):
   Admin menilai di menu **Kelola Permintaan**.
   - **Approve**: Permintaan disetujui, barang dapat disiapkan.
   - **Deliver**: Barang diserahkan, stok pindah status ke Used Qty (keluar gudang permanen).
   - **Reject**: Permintaan dibatalkan, Reserved Qty dikembalikan ke Available Qty.

4. **Pelaporan & Evaluasi**:
   Untuk keperluan administrasi/audit, Admin mencetak dokumen via menu **Cetak Laporan**. Sistem merangkum Buku Keadaan, Buku Bantu Penerimaan/Pengeluaran, dan membubuhkan data pengesahan 3 pejabat penandatangan.

---

## Instalasi

1. Pastikan XAMPP (PHP 7.2+, MySQL) sudah terinstal
2. Clone/extract project ke folder `htdocs/siat`
3. Import database: buat database `siat_db` lalu import file SQL dari folder `database/`
4. Akses: `http://localhost/siat`
5. Login admin default: Username `admin`, Password `admin123`

---
*Dibuat untuk Pengadilan Agama Bengkulu.*
