# Sistem Manajemen Pengarsipan Naskah Berita (Harian Cakrawala)

Repositori ini memuat aplikasi backend dan antarmuka web sistem pengarsipan naskah berita untuk Harian Cakrawala berbasis Laravel dan MySQL.

---

### 1. Clone Repositori

Buka terminal (Git Bash, Command Prompt, atau PowerShell), lalu jalankan perintah:

```bash
git clone <URL_REPOSITORI_GITHUB>
cd backend
```

---

### 2. Konfigurasi dan Instalasi Dependensi

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

---

### 4. Menjalankan Server Aplikasi

Jalankan perintah server Laravel:

```bash
php artisan serve
```

Aplikasi siap diakses melalui peramban web pada alamat:
`http://127.0.0.1:8000`
ato

```bash
npm run dev
```

---

## Akun Uji Coba Default (Seeder)

Setelah proses `php artisan migrate --seed` test account:

| Peran (Role)          | Email                       | Kata Sandi    | Status Akun | Hak Akses Utama                                              |
| :-------------------- | :-------------------------- | :------------ | :---------- | :----------------------------------------------------------- |
| **Admin Redaksi**     | `admin@cakrawala.com`       | `password123` | Verified    | Review naskah masuk, terima dan terbitkan, atau tolak naskah |
| **Kontributor Aktif** | `kontributor@cakrawala.com` | `password123` | Verified    | Dashboard naskah, kirim naskah, pantau status naskah         |
| **Calon Kontributor** | `calon@cakrawala.com`       | `password123` | Pending     | Halaman pemberitahuan akun sedang diverifikasi               |
| **Guest / Tamu**      | _(Tanpa Akun)_              | -             | -           | Melihat portal berita dan mengirim naskah via jalur tamu     |

---
