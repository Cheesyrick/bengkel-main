bengkel-broom-garage/
│
├── assets/ # Frontend (CSS, JS, Images)
├── config/ # Koneksi database (koneksi.php)
├── includes/ # Header, footer, sidebar, dan auth_check
│
├── auth/ # Modul Autentikasi [Use Case: Melakukan Log In & Log Out]
│ ├── login.php # Halaman login
│ ├── proses_login.php # Validasi role (owner, service_advisor, mechanic)
│ └── logout.php # Proses keluar sistem
│
├── owner/ # Modul Hak Akses Penuh (Owner)
│ ├── index.php # Dashboard
│ ├── akun_pengguna/ # [Use Case: Menginput, Mengedit, Melihat Akun Pengguna]
│ │ ├── list.php, add.php, edit.php, delete.php
│ ├── pelanggan_mobil/ # [Use Case: Menginput, Mengedit, Melihat Data Pelanggan & Mobil]
│ │ ├── list.php, add.php, edit.php, delete.php
│ ├── jasa/ # [Use Case: Menginput, Mengedit, Melihat Data Jasa] - Hanya Owner
│ │ ├── list.php, add.php, edit.php, delete.php
│ ├── sparepart/ # [Use Case: Menginput, Mengedit, Melihat Data Sparepart] - Hanya Owner
│ │ ├── list.php, add.php, edit.php, delete.php
│ ├── permintaan_servis/ # [Use Case: Input, Edit, Lihat Permintaan servis]
│ │ ├── list.php, add_permintaan.php, edit_permintaan.php, delete.php
│ └── laporan/ # [Use Case: Generate Laporan Bulanan]
│ └── laporan_bulanan.php
│
├── service_advisor/ # Modul Operasional (Service Advisor)
│ ├── index.php # Dashboard
│ ├── pelanggan_mobil/ # [Use Case: Menginput, Mengedit, Melihat Data Pelanggan & Mobil]
│ │ ├── list.php, add.php, edit.php, delete.php
│ ├── permintaan_servis/ # [Use Case: Input, Edit (Update/Delete), Lihat Permintaan Servis]
│ │ ├── list.php, add.php, edit_permintaan.php, delete.php
│ ├── laporan/ # [Use Case: Generate Laporan Bulanan][cite: 2]
│ │ └── laporan_bulanan.php
│ └── cetak_nota.php # Ekstraksi PDF dari pembayaran
│
├── mechanic/ # Modul Eksekusi (Mechanic)
│ ├── index.php # Dashboard daftar tugas
│ └── permintaan_servis/ # [Use Case: Edit Permintaan Servis - Alternative Flow 2][cite: 2]
│ ├── list.php # [Use Case: Melihat Data Permintaan Servis][cite: 2]
│ └── update_status.php # Mengganti status pengerjaan (dropdown menu)[cite: 2]
│
└── index.php # Redirect otomatis ke auth/login.php

🔑 Penyesuaian Hak Akses (Berdasarkan Tabel 2.17 - 2.36)
[cite: 2]

1. Hak Akses Owner (Administrator Penuh)

   Data Master Ekstensif: Owner adalah satu-satunya role yang dapat mengelola (CRUD) Data Jasa (Tabel 2.30, 2.31, 2.32) dan Data Sparepart (Tabel 2.33, 2.34, 2.35)[cite: 2].

   Manajemen Pengguna: Owner berhak menambah, mengedit, dan menghapus akun pengguna (Mekanik & SA) (Tabel 2.19, 2.20, 2.21)[cite: 2].

   Memiliki akses setara dengan SA untuk mengelola Pelanggan, Mobil, dan Permintaan Jasa[cite: 2].

2. Hak Akses Service Advisor (SA)

   Fokus Operasional: SA bertugas menerima kendaraan dengan mengelola Data Pelanggan & Mobil (Tabel 2.22 - 2.26)[cite: 2].

   Permintaan Jasa: SA melakukan pendaftaran masuk (Input Permintaan Jasa) dengan memilih pelanggan, mobil, jasa, sparepart, dan menugaskan mekanik (Tabel 2.27)[cite: 2]. SA juga berhak menghapus/mengedit data ini jika terjadi kesalahan (Tabel 2.28 Alternative Flow 1)[cite: 2].

   Laporan: SA berhak melakukan "Generate Laporan Bulanan" (Tabel 2.36)[cite: 2].

3. Hak Akses Mechanic

   Mekanik tidak dapat menambah atau menghapus data[cite: 2].

   Melihat Tugas: Hanya melihat data permintaan jasa yang terdaftar untuk dirinya (Tabel 2.29)[cite: 2].

   Update Pekerjaan: Hak akses edit terbatas hanya pada fungsi "Mengganti status pekerjaannya di dropdown menu" (Tabel 2.28 Alternative Flow 2)[cite: 2].

SUSUNAN PEMBUATAN WEB

1. Modul Autentikasi (Semua Role)

Fitur: Melakukan Log In & Log Out

    Implementasi Login: Buat form yang menerima username dan password.

    Validasi: Lakukan query ke tabel pengguna untuk mencocokkan data dan mengambil kolom role.

    Session: Simpan id_pengguna, username, dan role ke dalam $_SESSION untuk proteksi halaman.

    Implementasi Logout: Hapus semua data session dan arahkan kembali ke halaman login.

2. Modul Manajemen Akun (Khusus Owner)

Fitur: CRUD Akun Pengguna

    Input Akun: Buat form untuk menambah data ke tabel pengguna, termasuk menentukan role ('owner', 'service_advisor', 'mechanic').

    List & Edit: Tampilkan semua data dari tabel pengguna. Berikan opsi Update untuk mengubah informasi atau Delete untuk menghapus akun.

3. Modul Data Master (Khusus Owner)

Fitur: Manajemen Jasa & Sparepart

    Input Jasa: Masukkan data ke tabel jasa dengan merujuk pada id_jenis_jasa.

    Input Sparepart: Masukkan data ke tabel sparepart lengkap dengan kategori, satuan, merk, dan tipe.

    Update & Delete: Implementasikan fitur edit dan hapus untuk data Jasa dan Sparepart agar stok dan harga tetap akurat.

4. Modul Operasional (Owner & Service Advisor)

Fitur: Manajemen Pelanggan & Mobil

    Input Data: Buat form gabungan untuk mengisi tabel pelanggan dan tabel mobil secara bersamaan.

    Update: Sediakan fitur pencarian data pelanggan atau mobil berdasarkan nama atau plat nomor untuk diedit.

Fitur: Menginput Data Permintaan Jasa

    Dropdown Menu: Implementasikan form yang mengambil data (Data Pelanggan, Mobil, Jasa, Sparepart, dan Mekanik) dari database untuk dipilih.

    Simpan Transaksi: Masukkan data ke tabel permintaan_servis. Secara otomatis buat entri pada tabel detail_pengerjaan, detail_servis, dan detail_sparepart dengan ID permintaan yang sama.

5. Modul Pelaksanaan (Khusus Mekanik)

Fitur: Update Status Pengerjaan

    Melihat Tugas: Tampilkan daftar pengerjaan dari tabel detail_pengerjaan yang memiliki id_pengguna sesuai ID mekanik yang login.

    Update Status: Sediakan dropdown menu untuk mengubah status_pengerjaan menjadi 'assigned', 'pending', atau 'done'.

    Log Waktu: Otomatis perbarui tanggal_mulai_kerja saat status diubah ke 'pending' dan tanggal_selesai_kerja saat menjadi 'done'.

6. Modul Laporan & Output (Owner & SA)

Fitur: Generate Laporan Bulanan

    Proses Data: Buat perintah SQL untuk merangkum data dari detail_servis dan detail_sparepart berdasarkan rentang bulan tertentu.

    Analisis Jasa: Hitung jasa yang paling sering muncul dalam tabel detail_servis untuk melihat tren penggunaan jasa terbanyak.

    Cetak Nota PDF: Gunakan data dari tabel pembayaran dan tabel rincian lainnya untuk disusun menjadi nota fisik dalam format PDF.

---

## PROGRESS PENGEMBANGAN (TERBARU)

✅ **1. Modul Autentikasi (Semua Role)**

- [x] Halaman Login UI (`auth/login.php`) dan styling (`assets/css/loginstyle.css`).
- [x] Validasi login ke database dengan enkripsi/pencocokan password (`auth/proses_login.php`).
- [x] Session management & Proteksi halaman berdasarkan role.
- [x] Implementasi Logout (`auth/logout.php`).

✅ **2. Setup Dashboard & Struktur Navigasi**

- [x] Halaman Dashboard dasar (`auth/dashboardadmin.php`) dengan integrasi session.
- [x] Implementasi Sidebar Navigasi Dinamis (`includes/navbar.php`) yang otomatis menyesuaikan menu berdasarkan hak akses pengguna (Owner, Service Advisor, Mechanic).
- [x] Implementasi Footer (`includes/footer.php`) yang dinamis dan selalu berada di bawah layar menggunakan layout Flexbox.
- [x] Penggabungan (include) Sidebar dan Footer ke dalam halaman Dashboard.
- [x] Penyeragaman Tema UI (Styling): Pembuatan dan penyelarasan CSS untuk layout Dashboard, Sidebar, dan Footer (`assets/css/dashboard.css`, `assets/css/navbar.css`, `assets/css/footer.css`) menggunakan tema putih dengan aksen merah muda/merah gelap.

✅ **3. Pembuatan Menu Navigasi Bertingkat (Dropdown)**

- [x] Refactoring Sidebar (`includes/sidebar.php` & `includes/navbar.php`) untuk menggunakan sistem dropdown menu per modul berdasarkan hak akses pengguna.
- [x] Implementasi Pure CSS Checkbox Hack untuk fungsi navigasi dropdown interaktif tanpa memerlukan JavaScript (`assets/css/sidebar.css`).

✅ **4. Modul Manajemen Akun (Khusus Owner)**

- [x] Tampilan daftar pengguna dengan UI tabel interaktif dan indikator Role (`owner/akun_pengguna/list.php`).
- [x] Fitur penambahan akun pengguna baru beserta enkripsi password MD5 (`owner/akun_pengguna/add.php`).
- [x] Fitur edit data pengguna, dengan opsi untuk membiarkan password lama atau memperbaruinya (`owner/akun_pengguna/edit.php`).
- [x] Fitur hapus akun pengguna dengan sistem keamanan (mencegah Owner menghapus akunnya sendiri yang sedang aktif) (`owner/akun_pengguna/delete.php`).

_(Catatan progress akan terus diperbarui seiring dengan pengembangan fitur selanjutnya)_
