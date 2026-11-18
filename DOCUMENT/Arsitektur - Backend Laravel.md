---
title: Arsitektur - Backend Laravel
tags:
  - architecture
  - backend
  - laravel
  - api
---

# 🐘 Arsitektur - Backend Laravel

Spesifikasi teknis sisi server menggunakan framework **Laravel 11** dengan arsitektur RESTful JSON API.

---

## 🔗 Relasi & Koneksi Sistem
- Induk: [[MOC - Harian Cakrawala]]
- Integrasi Media & Storage: [[Modul - Gambar Sampul & Media]]
- Controller Redaksi: [[Modul - Review & Pengarsipan Naskah]]
- Controller Kontributor: [[Modul - Kontributor & Verifikasi]]
- Skema Tabel & Relasi: [[Arsitektur - Database & Migrasi]]
- Status Operasional: [[Git & Deployment State]]

---

## 🏗️ Struktur Controller & Services

```
backend/app/
├── Http/Controllers/Api/
│   ├── AdminReviewController.php    # Approval, penolakan, catatan naskah
│   ├── AdminUserController.php      # Verifikasi kontributor & user list
│   ├── AuthController.php           # Login, Sanctum token, session
│   ├── GuestController.php          # Kirim naskah tamu tanpa auth
│   ├── KontributorController.php    # Kirim naskah, revisi naskah, naskah saya
│   └── PublicBeritaController.php   # Feed naskah terbit untuk HomeView
├── Mail/
│   └── VerifikasiKontributorMail.php # Email HTML Blade notifikasi verifikasi
└── Models/
    ├── Naskah.php                   # Model naskah + accessor gambar_sampul_url
    ├── Kategori.php                 # Relasi kategori naskah
    └── User.php                     # Model user (Admin, Kontributor)
```

---

## ⚙️ Layanan Tambahan (Mailer & Storage)

### 1. Storage Symlink
- Aset publik disimpan di: `storage/app/public/`
- Folder publik:
  - `sampul_naskah/` : Banner utama naskah
  - `media_naskah/` : Gambar pendukung naskah
  - `administrasi/` : Berkas KTP & Surat administrasi
- Aksesibilitas: Di Windows menggunakan Junction Link dari `public/storage` ke `storage/app/public`.

### 2. Notifikasi Email (SMTP Gmail)
- File Mailer: `backend/app/Mail/VerifikasiKontributorMail.php`
- Template Blade: `backend/resources/views/emails/verifikasi_kontributor.blade.php`
- Digunakan saat admin menyetujui atau menolak akun kontributor baru.
