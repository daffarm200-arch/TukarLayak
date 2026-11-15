# TukarLayak

**Barang Tak Terpakai, Bisa Jadi Berarti.**

TukarLayak adalah aplikasi web berbasis platform berbagi dan bertukar barang bekas yang masih layak pakai. Aplikasi ini mempertemukan masyarakat yang memiliki barang tidak terpakai dengan masyarakat yang membutuhkan barang tersebut.

Pengguna dapat menawarkan barang secara gratis atau mengajukan pertukaran dengan barang milik pengguna lain tanpa menggunakan sistem jual beli atau pembayaran.

## Fitur Aplikasi

### 1. Pengguna
- Registrasi dan login.
- Dashboard pengguna.
- Melihat katalog barang.
- Mencari dan memfilter barang.
- Menambahkan barang beserta foto.
- Mengedit dan menghapus barang milik sendiri.
- Melihat detail barang.
- Mengajukan permintaan barang gratis.
- Mengajukan pertukaran barang.
- Menerima atau menolak permintaan sebagai pemilik barang.
- Melihat riwayat permintaan dan pertukaran.

### 2. Administrator
- Dashboard statistik.
- Manajemen pengguna.
- Manajemen kategori.
- Manajemen seluruh barang.
- Melihat aktivitas permintaan dan pertukaran.
- Laporan statistik.
- Ekspor laporan dalam format CSV.

## Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| PHP 8.2 | Bahasa pemrograman |
| Laravel 12 | Backend framework |
| MySQL / MariaDB | Database |
| Blade Template | Template frontend |
| Tailwind CSS | Desain antarmuka |
| JavaScript | Interaksi frontend |
| Vite | Pengelolaan aset frontend |
| Git dan GitHub | Version control |

## Penerapan Keamanan

TukarLayak menerapkan beberapa mekanisme keamanan, antara lain:

- Password hashing.
- CSRF protection.
- Validasi input.
- Proteksi SQL Injection melalui mekanisme query Laravel.
- Pencegahan XSS melalui output escaping.
- Pembatasan percobaan login (rate limiting).
- Session management.
- Middleware untuk pembatasan akses Admin dan User.
- Authorization dan Policy untuk mencegah IDOR.
- Validasi tipe dan ukuran file upload.
- Database transaction dan penguncian data untuk menjaga konsistensi proses pertukaran.

## Struktur Database

Database utama menggunakan MySQL dengan tabel:

- `users`: Menyimpan akun pengguna dan administrator.
- `categories`: Menyimpan kategori barang.
- `items`: Menyimpan informasi barang.
- `item_requests`: Menyimpan permintaan gratis dan pertukaran barang.
- `migrations`: Menyimpan riwayat migration Laravel.
- Tabel pendukung Laravel untuk session, cache, dan queue.

## Persyaratan Instalasi

Sebelum menjalankan aplikasi, pastikan perangkat sudah memiliki:

- PHP minimal 8.2.
- Composer.
- Node.js dan NPM.
- MySQL atau MariaDB.

## Cara Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/daffarm200-arch/TukarLayak.git
cd TukarLayak
```

### 2. Install Dependensi

```bash
composer install
npm install
```

### 3. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`.

```bash
copy .env.example .env
```

Sesuaikan konfigurasi database pada file `.env` menggunakan database lokal yang sudah dibuat.

Contoh konfigurasi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tukarlayak_db
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan username dan password dengan konfigurasi MySQL masing-masing.

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Jalankan Migration

```bash
php artisan migrate
```

### 6. Masukkan Kategori Awal

```bash
php artisan db:seed --class=CategorySeeder
```

### 7. Hubungkan Storage

```bash
php artisan storage:link
```

### 8. Build Frontend

```bash
npm run build
```

### 9. Jalankan Aplikasi

```bash
php artisan serve
```

Buka aplikasi melalui:

`http://127.0.0.1:8000`

## Pengujian Aplikasi

Untuk menjalankan seluruh pengujian otomatis:

```bash
php artisan test
```

Pengujian mencakup autentikasi, pengelolaan barang, permintaan, pertukaran, dan pembatasan hak akses.

Pengujian menggunakan konfigurasi database testing yang terpisah dari database pengembangan.

## Akun Demo

Akun administrator dibuat melalui perintah Artisan:

```bash
php artisan make:admin
```

Akun pengguna dapat dibuat melalui halaman registrasi.

Kredensial demo untuk demonstrasi aplikasi akan disediakan secara terpisah dan tidak disimpan dalam repository publik.

## Repository dan Deployment

- Repository GitHub: Akan ditambahkan setelah publikasi repository.
- URL aplikasi publik: Akan ditambahkan setelah deployment.
- Protokol akses: HTTPS.

## Pengembangan Selanjutnya

Pengembangan TukarLayak dapat dilanjutkan dengan penambahan notifikasi, penyempurnaan pelaporan, peningkatan keamanan, dan pengembangan fitur kolaborasi masyarakat.

## Penggunaan AI

AI digunakan sebagai alat bantu selama proses pengembangan, termasuk perencanaan struktur aplikasi, bantuan penulisan kode, debugging, pemeriksaan keamanan, dan penyusunan dokumentasi.

Seluruh implementasi tetap melalui pemeriksaan dan pengujian sebelum digunakan.

---

**TukarLayak — Barang Tak Terpakai, Bisa Jadi Berarti.**