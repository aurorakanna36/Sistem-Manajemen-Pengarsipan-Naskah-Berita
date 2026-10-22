# Sistem Manajemen Pengarsipan Naskah Berita (Harian Cakrawala)

Repositori ini memuat aplikasi backend dan antarmuka web sistem pengarsipan naskah berita untuk Harian Cakrawala berbasis **Laravel** dan **MySQL**.

---

## 👥 Akun Uji Coba (Default Seeder)

Setelah menjalankan `php artisan migrate --seed`, akun berikut siap langsung digunakan untuk login:

| Peran (Role) | Email | Kata Sandi | Status Akun | Hak Akses Utama |
| :--- | :--- | :--- | :--- | :--- |
| **Admin Redaksi** | `admin@cakrawala.com` | `password123` | Verified | Review naskah masuk, terima/tolak naskah |
| **Kontributor Aktif** | `kontributor@cakrawala.com` | `password123` | Verified | Dashboard naskah, kirim naskah, pantau status |
| **Calon Kontributor** | `calon@cakrawala.com` | `password123` | Pending | Halaman tunggu info verifikasi redaksi |
| **Guest / Tamu** | *(Tanpa Login)* | - | - | Portal berita & kirim naskah via jalur tamu |

---

## 🚀 Panduan Setup Cepat untuk Anggota Tim (Windows)

### Cara Otomatis (Sangat Direkomendasikan):
1. Pastikan **Laragon** (atau XAMPP) sudah aktif dengan layanan **MySQL Running**.
2. Buat database baru di MySQL bernama: `db_pengarsipan_naskah`.
3. Klik 2x file **`setup.bat`** di dalam folder `backend/`.
4. Jalankan perintah migrasi & data akun:
   ```bash
   php artisan migrate --seed
   ```
5. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
   Akses di browser: `http://127.0.0.1:8000`.

---

## 📁 Struktur Halaman Blade (Untuk Rekan Frontend):
- `resources/views/home.blade.php`: Portal utama Harian Cakrawala & daftar berita terbit.
- `resources/views/auth/login.blade.php`: Halaman login split-screen (Pilihan: Kontributor / Admin / Guest).
- `resources/views/auth/register-kontributor.blade.php`: Formulir pendaftaran kontributor & upload berkas.
- `resources/views/guest/kirim-naskah.blade.php`: Formulir pengiriman naskah tamu.
- `resources/views/kontributor/dashboard.blade.php`: Dashboard kontributor (4 kartu metrik, form kirim naskah, list status).
- `resources/views/admin/review.blade.php`: Panel review naskah & penetapan keputusan redaksi.
