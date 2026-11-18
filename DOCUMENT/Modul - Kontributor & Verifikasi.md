---
title: Modul - Kontributor & Verifikasi
tags:
  - modul/kontributor
  - auth
  - verifikasi
  - mailer
---

# ✍️ Modul - Kontributor & Verifikasi

Modul ini mengelola siklus pengguna dengan peran **Kontributor**, mulai dari registrasi identitas, verifikasi administratif berkas (KTP & Tanda Tangan), pengiriman naskah, hingga pengajuan revisi berkala.

---

## 🔗 Relasi & Koneksi Sistem
- Induk: [[MOC - Harian Cakrawala]]
- Alur Naskah Lanjut: [[Modul - Review & Pengarsipan Naskah]]
- Komponen Gambar & Media: [[Modul - Gambar Sampul & Media]]
- Skema Tabel Pengguna & Migrasi: [[Arsitektur - Database & Migrasi]]
- Email Service & Controller: [[Arsitektur - Backend Laravel]]
- Antarmuka Vue Kontributor: [[Arsitektur - Frontend Vue]]

---

## 📋 Fitur Utama

### 1. Registrasi & Verifikasi Identitas
- Calon kontributor mendaftar dengan melampirkan berkas:
  - Foto Identitas / KTP (`foto_identitas`)
  - Surat Administrasi / Pengantar (`surat_administrasi`)
  - Tanda Tangan Digital (`tanda_tangan`)
  - Alamat Email Aktif (`email_gmail`)
- Admin memverifikasi di `VerifikasiKontributor.vue`.
- Sistem mengirimkan email resmi status verifikasi melalui `VerifikasiKontributorMail.php`.

### 2. Kirim Naskah Baru
- **Path**: `frontend/src/views/kontributor/KirimNaskahKontributorView.vue`
- Dropzone upload gambar sampul dengan drag-and-drop & live preview.
- Input judul naskah, kategori naskah, dan isi konten menggunakan editor kaya teks (*rich-text editor*).

### 3. Naskah Saya & Riwayat Status
- **Path**: `frontend/src/views/kontributor/NaskahSayaView.vue` & `DetailNaskahKontributorView.vue`
- Monitoring progres naskah: `Menunggu Review`, `Perlu Revisi`, `Ditolak`, atau `Diterbitkan`.
- Menampilkan feedback catatan redaksi langsung dari editor admin.

### 4. Pengajuan Revisi Naskah
- **Path**: `frontend/src/views/kontributor/RevisiNaskahKontributorView.vue`
- Membuka naskah berstatus `revisi` atau `ditolak`.
- Mengisi kembali field judul, isi, dan opsi mengganti atau mempertahankan gambar sampul lama.
