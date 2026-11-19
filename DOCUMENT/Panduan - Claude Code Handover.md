---
title: Panduan - Claude Code Handover
tags:
  - guide/claude
  - handover
  - prompt
  - instructions
---

# 🤖 Panduan - Claude Code Handover

Dokumen ini berisi instruksi terstandarisasi untuk melanjutkan pengembangan, *troubleshooting*, atau penambahan fitur menggunakan **Claude Code CLI** di terminal Anda.

---

## 🔗 Relasi & Koneksi Sistem
- Induk: [[MOC - Harian Cakrawala]]
- Aturan Media: [[Modul - Gambar Sampul & Media]]
- Aturan Klien: [[Arsitektur - Frontend Vue]]
- Aturan Server: [[Arsitektur - Backend Laravel]]
- Status Kode: [[Git & Deployment State]]

---

## 🎯 Instruksi Awalan (Copy & Paste ke Claude Code)

Jalankan perintah ini atau tempel teks berikut pada awal sesi Claude Code:

```text
Halo Claude! Kita melanjutkan pengerjaan proyek Portal Harian Cakrawala (Media Monitoring).
Workspace: c:\project_magang_monitoring_media
Active Branch: "final"

Silakan baca dokumentasi Obsidian yang ada di folder: DOCUMENT/
- Hub Utama: [[MOC - Harian Cakrawala]]
- Aturan Penanganan Gambar: [[Modul - Gambar Sampul & Media]]
- Arsitektur Frontend: [[Arsitektur - Frontend Vue]]
- Arsitektur Backend: [[Arsitektur - Backend Laravel]]

Aturan utama yang harus selalu dipatuhi:
1. Resolusi URL Gambar: Semua gambar naskah wajib melalui `getCoverImageUrl()` dari `@/utils/imageUrl.js`.
2. Template Vue Gambar: Selalu gunakan pola `<img v-if="...">` dan `<span v-else>Gambar sampul belum tersedia</span>` agar placeholder tidak muncul ganda.
3. Axios Header: Jangan set manual header `Content-Type: multipart/form-data` pada pengiriman FormData.
4. Git Branch: Semua perubahan commit ke branch "final", dan setelah dites berhasil dapat di-merge ke branch "main".
```

---

## 🛠️ Checklist Tugas Pengembangan Berikutnya

- [ ] Pengujian manual siklus lengkap: Kirim Naskah Kontributor -> Review & Putusan Admin -> Tampil di HomeView.
- [ ] Optimasi lazy loading gambar sampul naskah pada list berita panjang.
- [ ] Penambahan export laporan arsip naskah ke format PDF / Excel jika diperlukan.
