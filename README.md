# Sistem Manajemen Pengarsipan Naskah (Harian Cakrawala)

Repositori ini menggunakan arsitektur Monorepo yang memisahkan aplikasi menjadi dua bagian utama:
1. **Backend**: REST API berbasis Laravel 12 & MySQL.
2. **Frontend**: Single Page Application (SPA) berbasis Vue 3, Vite, dan Vue Router.

---

## 1. Struktur Direktori Proyek

```text
project_magang_monitoring_media/
├── backend/
│   ├── app/
│   │   ├── Http/Controllers/Api/   # REST API Controllers (JSON response)
│   │   └── Models/                 # User, Naskah, Guest, Notifikasi
│   ├── database/migrations/        # Migrasi tabel MySQL & Sanctum
│   ├── routes/api.php              # Rute REST API
│   ├── config/cors.php             # Konfigurasi CORS localhost:5173
│   └── .env
│
├── frontend/
│   ├── src/
│   │   ├── api/                    # Axios instance & helper endpoints
│   │   ├── router/                 # Vue Router & auth navigation guards
│   │   ├── views/                  # Halaman aplikasi (.vue components)
│   │   │   ├── HomeView.vue                    (Portal Berita Publik)
│   │   │   ├── auth/
│   │   │   │   ├── LoginView.vue               (Login Admin & Kontributor)
│   │   │   │   ├── RegisterKontributorView.vue (Pendaftaran & Upload Berkas)
│   │   │   │   └── PendingNoticeView.vue       (Menunggu Verifikasi Redaksi)
│   │   │   ├── guest/
│   │   │   │   └── KirimNaskahView.vue         (Kirim Naskah Publik)
│   │   │   ├── kontributor/
│   │   │   │   └── DashboardKontributorView.vue(Dashboard & Pengiriman Naskah)
│   │   │   └── admin/
│   │   │       ├── ReviewNaskahView.vue        (Review, Revisi, & Terbit Naskah)
│   │   │       └── UserManagementView.vue      (Verifikasi Kontributor)
│   │   ├── App.vue
│   │   └── main.js
│   ├── package.json
│   └── vite.config.js
│
├── DOCUMENT/                       # Berkas proposal dan dokumentasi magang
└── README.md
```

---

## 2. Persyaratan Sistem

- PHP >= 8.2 dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`.
- Composer >= 2.0
- Node.js >= 18.0 & npm >= 9.0
- MySQL Server (via Laragon, XAMPP, atau instalasi mandiri MySQL).

---

## 3. Langkah Menjalankan Aplikasi

### A. Menjalankan Backend (Laravel API)

1. Buka terminal pertama, arahkan ke folder `backend`:
   ```bash
   cd backend
   ```
2. Pastikan file `.env` sudah sesuai dengan pengaturan database MySQL Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_pengarsipan_naskah
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Pasang dependensi dan jalankan migrasi data (bila belum):
   ```bash
   composer install
   php artisan migrate --seed
   ```
4. Jalankan development server backend:
   ```bash
   php artisan serve
   ```
   API akan berjalan pada: `http://localhost:8000`

### B. Menjalankan Frontend (Vue 3 + Vite)

1. Buka terminal kedua, arahkan ke folder `frontend`:
   ```bash
   cd frontend
   ```
2. Pasang dependensi Node.js (hanya pada pertama kali):
   ```bash
   npm install
   ```
3. Jalankan development server frontend:
   ```bash
   npm run dev
   ```
   Aplikasi web dapat diakses melalui browser pada: `http://localhost:5173`

---

## 4. Akun Uji Coba Default

Berikut akun yang terdaftar dari seeder database:

| Peran (Role) | Email | Kata Sandi | Status Verifikasi | Hak Akses |
| :--- | :--- | :--- | :--- | :--- |
| **Admin Redaksi** | `admin@cakrawala.com` | `password123` | verified | Review naskah masuk, verifikasi akun kontributor |
| **Kontributor Aktif** | `kontributor@cakrawala.com` | `password123` | verified | Dashboard naskah, kirim naskah baru, pantau riwayat |
| **Calon Kontributor** | `calon@cakrawala.com` | `password123` | pending | Halaman status peninjauan akun |
| **Guest / Tamu** | *(Tanpa Akun)* | - | - | Portal berita publik & kirim naskah via form tamu |

---

