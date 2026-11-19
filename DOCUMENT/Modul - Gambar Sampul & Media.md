---
title: Modul - Gambar Sampul & Media
tags:
  - modul/media
  - frontend
  - backend
  - fix-bug
---

# 🖼️ Modul - Gambar Sampul & Media

Modul ini menangani pengelolaan aset visual naskah berita, mencakup proses pengunggahan (*upload*), penyimpanan (*storage*), normalisasi URL, hingga penanganan galat (*fallback placeholder*) di antarmuka pengguna.

---

## 🔗 Relasi & Koneksi Sistem
- Induk: [[MOC - Harian Cakrawala]]
- Komponen Frontend Terkait: [[Arsitektur - Frontend Vue]]
- Komponen Backend Terkait: [[Arsitektur - Backend Laravel]]
- Dipakai oleh: 
  - [[Modul - Review & Pengarsipan Naskah]] (Admin review cover)
  - [[Modul - Kontributor & Verifikasi]] (Kontributor kirim & revisi)
  - [[Modul - Portal Berita Publik]] (Thumbnail berita)
- Standar Instruksi AI: [[Panduan - Claude Code Handover]]

---

## ⚠️ Akar Masalah yang Pernah Terjadi
1. **Header Multipart Tanpa Boundary**: Penulisan manual `'Content-Type': 'multipart/form-data'` pada Axios merusak boundary dinamis browser.
2. **Duplikasi / Relatif Path**: DB menyimpan `sampul_naskah/abc.jpg`, namun frontend memanggil `/storage/storage/...` atau sebaliknya.
3. **Bug Placeholder Tampil Ganda**: Elemen `<span>Gambar sampul belum tersedia</span>` tidak menggunakan `v-else` sehingga tetap terlihat saat `<img>` berhasil dimuat.

---

## 🛠️ Solusi & Standar Implementasi

### 1. Frontend Helper: `getCoverImageUrl()`
Lokasi: `frontend/src/utils/imageUrl.js`
Menangani berbagai bentuk format data gambar secara terpusat:
- URL absolut (`http://...`, `https://...`)
- Data URI (`data:image/...`)
- Path relatif backend (`sampul_naskah/...` -> `http://localhost:8000/storage/sampul_naskah/...`)
- Menghindari duplikasi `/storage/storage/`
- Fallback mengekstrak tag `<img>` pertama dari isi naskah (HTML body) jika `gambar_sampul` kosong.

### 2. Standar Template Vue (Wajib v-if / v-else)
Hindari merender `<span>` placeholder tanpa `v-else`. Pola yang benar:
```html
<div class="sampul-container">
    <img v-if="getCoverImageUrl(naskah) && !sampulError"
         :src="getCoverImageUrl(naskah)"
         :alt="naskah.judul || 'Sampul'"
         class="sampul-img"
         @error="sampulError = true" />
    <span v-else class="sampul-placeholder">
        Gambar sampul belum tersedia
    </span>
</div>
```

### 3. Backend Accessor & Resolver
Lokasi: `backend/app/Models/Naskah.php`
- `Naskah::resolveGambarSampulUrl(?string $gambarSampul, ?string $isi)`
- Accessor `$naskah->gambar_sampul_url` otomatis dikirim dalam respon JSON API.

---

## 📁 Direktori Storage Fisik
- **Storage Path**: `backend/storage/app/public/sampul_naskah/`
- **Public Symlink**: `backend/public/storage` (Windows Junction)
