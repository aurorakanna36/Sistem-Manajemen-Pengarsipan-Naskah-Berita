# Sistem Manajemen Pengarsipan Naskah Berita (Harian Cakrawala)

Repositori ini memuat aplikasi backend dan antarmuka web sistem pengarsipan naskah berita untuk Harian Cakrawala berbasis Laravel dan MySQL.

---

## Prasyarat Sistem

Sebelum menjalankan proyek, pastikan komputer telah terpasang:
- PHP versi 8.2 atau lebih baru
- Composer versi 2.x
- Node.js versi 18+ atau 20+ (LTS) beserta NPM
- MySQL / MariaDB (disarankan menggunakan Laragon atau XAMPP)
- Git

---

## Panduan Instalasi dan Menjalankan Proyek

### 1. Clone Repositori
Buka terminal (Git Bash, Command Prompt, atau PowerShell), lalu jalankan perintah:

```bash
git clone <URL_REPOSITORI_GITHUB>
cd backend
```

---

### 2. Konfigurasi dan Instalasi Dependensi

Anda dapat memilih salah satu dari dua cara berikut:

#### Opsi A: Cara Otomatis (Pengguna Windows - Disarankan)
1. Buka aplikasi Laragon atau XAMPP, lalu pastikan layanan MySQL sudah aktif (**Start**).
2. Buat database baru bernama `db_pengarsipan_naskah` (melalui HeidiSQL, phpMyAdmin, atau terminal MySQL).
3. Klik dua kali pada file `setup.bat` di dalam folder proyek. File ini akan otomatis menyalin file konfigurasi `.env`, mengunduh package PHP dan Node.js, men-generate `APP_KEY`, menghubungkan storage, dan mem-build aset.
4. Lanjutkan ke langkah 3 (Migrasi Basis Data).

#### Opsi B: Cara Manual (Terminal)
Jika ingin menjalankan perintah satu per satu:

1. Salin file konfigurasi environment:
   ```bash
   cp .env.example .env
   ```
2. Pastikan pengaturan database di file `.env` sudah sesuai:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_pengarsipan_naskah
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Unduh dependensi backend (PHP):
   ```bash
   composer install
   ```
4. Buat Application Key Laravel:
   ```bash
   php artisan key:generate
   ```
5. Unduh dependensi frontend dan kompilasi aset:
   ```bash
   npm install
   npm run build
   ```
6. Hubungkan folder berkas publik:
   ```bash
   php artisan storage:link
   ```

---

### 3. Migrasi Basis Data dan Seeder Akun Uji Coba

Pastikan database `db_pengarsipan_naskah` sudah dibuat di MySQL, lalu jalankan:

```bash
php artisan migrate --seed
```

Perintah ini akan membuat seluruh tabel yang dibutuhkan dan mengisi akun serta naskah contoh secara otomatis.

---

### 4. Menjalankan Server Aplikasi

Jalankan perintah server Laravel:

```bash
php artisan serve
```

Aplikasi siap diakses melalui peramban web pada alamat:
`http://127.0.0.1:8000`

Jika rekan frontend sedang melakukan pengeditan tampilan CSS/Blade secara aktif, jalankan Vite dev server di terminal terpisah agar perubahan langsung terlihat tanpa reload (*hot reload*):
```bash
npm run dev
```

---

## Akun Uji Coba Default (Seeder)

Setelah proses `php artisan migrate --seed` selesai, akun-akun berikut dapat langsung digunakan untuk menguji alur multi-user:

| Peran (Role) | Email | Kata Sandi | Status Akun | Hak Akses Utama |
| :--- | :--- | :--- | :--- | :--- |
| **Admin Redaksi** | `admin@cakrawala.com` | `password123` | Verified | Review naskah masuk, terima dan terbitkan, atau tolak naskah |
| **Kontributor Aktif** | `kontributor@cakrawala.com` | `password123` | Verified | Dashboard naskah, kirim naskah, pantau status naskah |
| **Calon Kontributor** | `calon@cakrawala.com` | `password123` | Pending | Halaman pemberitahuan akun sedang diverifikasi |
| **Guest / Tamu** | *(Tanpa Akun)* | - | - | Melihat portal berita dan mengirim naskah via jalur tamu |

---

## Panduan Kerja Rekan Frontend (Struktur Blade)

Rekan Frontend (Rysta) dapat langsung memfokuskan pengerjaan desain visual dan styling pada file antarmuka berikut tanpa perlu mengubah tag backend:

- `resources/views/home.blade.php`: Portal utama Harian Cakrawala dan daftar berita terbit.
- `resources/views/auth/login.blade.php`: Halaman login split-screen (Pilihan: Kontributor / Admin / Guest).
- `resources/views/auth/register-kontributor.blade.php`: Formulir pendaftaran kontributor dan unggah dokumen.
- `resources/views/guest/kirim-naskah.blade.php`: Formulir pengiriman naskah tamu.
- `resources/views/kontributor/dashboard.blade.php`: Dashboard kontributor (kartu metrik statistik, form kirim naskah, list status naskah).
- `resources/views/admin/review.blade.php`: Panel peninjauan naskah dan keputusan redaksi.
- `resources/css/app.css`: File styling CSS utama.
