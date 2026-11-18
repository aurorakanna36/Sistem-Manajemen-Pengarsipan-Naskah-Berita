---
title: Git & Deployment State
tags:
  - git
  - devops
  - build
  - status
---

# 🚀 Git & Deployment State

Dokumentasi status repositori git, branch aktif, commit terkini, serta prosedur build dan deploy.

---

## 🔗 Relasi & Koneksi Sistem
- Induk: [[MOC - Harian Cakrawala]]
- Panduan Handover: [[Panduan - Claude Code Handover]]
- Kompilasi Klien: [[Arsitektur - Frontend Vue]]

---

## 📌 Status Terkini Repositori

- **Branch Aktif**: `final`
- **Branch Produksi**: `main` (sudah tersinkronisasi)
- **Commit Terakhir**: `13b6612`
- **Pesan Commit**: `merge: gabungkan perubahan dari branch update-baru ke final`
- **Riwayat Penggabungan Terakhir**:
  - Menggabungkan branch `origin/update-baru` (commit `7cc3b9d`: pembaruan halaman kirim naskah tamu & layout kontributor)
- **Remote Origin**: `https://github.com/aurorakanna36/Sistem-Manajemen-Pengarsipan-Naskah-Berita`

---

## 🔨 Perintah Operasional

### 1. Build & Verifikasi Frontend
```powershell
cd c:\project_magang_monitoring_media\frontend
npm run build
```
*Catatan: Pastikan exit code 0 dan 134+ modul terkompilasi.*

### 2. Menjalankan Server Development Lokal
- **Backend Laravel**:
  ```powershell
  cd c:\project_magang_monitoring_media\backend
  php artisan serve
  ```
- **Frontend Vite**:
  ```powershell
  cd c:\project_magang_monitoring_media\frontend
  npm run dev
  ```

### 3. Alur Commit & Merge
```powershell
# 1. Pastikan di branch final
git checkout final
git add -A
git commit -m "feat/fix: deskripsi perubahan"
git push origin final

# 2. Merge ke main setelah diverifikasi
git checkout main
git merge final
git push origin main

# 3. Kembali ke branch kerja final
git checkout final
```
