---
title: Arsitektur - Database & Migrasi
tags:
  - architecture
  - database
  - migrations
  - mysql
---

# 🗄️ Arsitektur - Database & Migrasi

Dokumentasi skema basis data, relasi tabel utama, dan migrasi penting yang telah dijalankan pada proyek Harian Cakrawala.

---

## 🔗 Relasi & Koneksi Sistem
- Induk: [[MOC - Harian Cakrawala]]
- Model Laravel: [[Arsitektur - Backend Laravel]]
- Pengguna & Dokumen: [[Modul - Kontributor & Verifikasi]]
- Naskah & Status: [[Modul - Review & Pengarsipan Naskah]]

---

## 📊 Relasi Tabel Utama

```mermaid
erDiagram
    USERS ||--o{ NASKAH : "menulis / mengirim"
    USERS ||--o{ RIWAYAT_STATUS : "memperbarui status"
    NASKAH ||--o{ KATEGORI_NASKAH : "memiliki kategori"
    KATEGORI ||--o{ KATEGORI_NASKAH : "terhubung"
    NASKAH ||--o{ RIWAYAT_STATUS : "mencatat log"

    USERS {
        bigint id PK
        string name
        string email
        string email_gmail
        string role "admin / kontributor"
        string status_verifikasi "pending / diverifikasi / ditolak"
        string foto_identitas
        string tanda_tangan
    }

    NASKAH {
        bigint id PK
        bigint user_id FK
        string judul
        longtext isi
        string gambar_sampul
        string gambar_sampul_keterangan
        string status_naskah "draft / dalam_review / diterima / ditolak / revisi"
        text catatan_redaksi
        timestamp waktu_kirim
    }
```

---

## 🚀 Migrasi Terbaru

1. **`2026_11_16_000001_add_foto_identitas_and_tanda_tangan_to_users_table.php`**:
   - Menambahkan kolom `foto_identitas` (string, nullable)
   - Menambahkan kolom `tanda_tangan` (string, nullable)
2. **`2026_11_16_000002_drop_abstrak_from_naskah_table.php`**:
   - Menghapus kolom legacy `abstrak` karena isi naskah kini langsung dikelola sebagai artikel utuh.
3. **`2026_11_16_000003_add_email_gmail_to_users_table.php`**:
   - Menambahkan kolom `email_gmail` untuk jalur kirim notifikasi verifikasi.
