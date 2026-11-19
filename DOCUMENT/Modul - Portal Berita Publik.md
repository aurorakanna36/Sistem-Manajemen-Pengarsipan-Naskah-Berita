---
title: Modul - Portal Berita Publik
tags:
  - modul/portal
  - public
  - reader
  - landing-page
---

# 📰 Modul - Portal Berita Publik

Modul ini adalah etalase publik **Portal Harian Cakrawala** yang dapat diakses oleh masyarakat umum pembaca berita tanpa perlu login.

---

## 🔗 Relasi & Koneksi Sistem
- Induk: [[MOC - Harian Cakrawala]]
- Sumber Berita: [[Modul - Review & Pengarsipan Naskah]] (Hanya naskah yang lolos review / diterbitkan)
- Thumbnail & Header Visual: [[Modul - Gambar Sampul & Media]]
- Antarmuka Utama: `frontend/src/views/HomeView.vue` di [[Arsitektur - Frontend Vue]]
- Controller API: `PublicBeritaController.php` di [[Arsitektur - Backend Laravel]]

---

## 🌟 Fitur & Perilaku

### 1. Headline & Berita Terbaru
- Menampilkan naskah utama terbit dengan banner besar.
- Menampilkan grid berita terbaru dengan cuplikan (*snippet*), kategori, tanggal, dan nama penulis.

### 2. Filter Kategori Reaktif
- Navbar kategori: `SEMUA`, `NASIONAL`, `DAERAH`, `PENDIDIKAN`, `BISNIS`, `EKONOMI`, `TEKNOLOGI`, `OPINI`.
- Menggunakan Vue reactivity: saat kategori dipilih, request mengambil berita yang terhubung dengan kategori tersebut.
- Normalisasi struktur naskah lokal di `HomeView.vue` memastikan gambar thumbnail diparsing menggunakan `getCoverImageUrl()`.

### 3. Jalur Kirim Naskah Tamu (Guest)
- Tamu yang belum memiliki akun kontributor dapat mengirim naskah melalui jalur tamu (*guest submission*).
- Path: `frontend/src/views/guest/KirimNaskahView.vue`
- Endpoint: `POST /api/guest/naskah` (`GuestController.php`)
