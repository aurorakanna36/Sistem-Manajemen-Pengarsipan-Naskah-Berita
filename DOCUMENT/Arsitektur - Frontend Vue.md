---
title: Arsitektur - Frontend Vue
tags:
  - architecture
  - frontend
  - vue3
  - vite
---

# ⚡ Arsitektur - Frontend Vue

Spesifikasi teknis sisi klien dibangun di atas ekosistem modern **Vue 3** dengan Vite sebagai bundler performa tinggi.

---

## 🔗 Relasi & Koneksi Sistem
- Induk: [[MOC - Harian Cakrawala]]
- Integrasi Media & Utility: [[Modul - Gambar Sampul & Media]]
- Tampilan Admin: [[Modul - Review & Pengarsipan Naskah]]
- Tampilan Kontributor: [[Modul - Kontributor & Verifikasi]]
- Tampilan Pembaca: [[Modul - Portal Berita Publik]]
- Panduan Pemrograman AI: [[Panduan - Claude Code Handover]]
- Status Build: [[Git & Deployment State]]

---

## 🏗️ Struktur Direktori Frontend

```
frontend/
├── src/
│   ├── api/
│   │   ├── axios.js           # Konfigurasi axios + Interceptor FormData
│   │   └── index.js           # Kumpulan request API per domain
│   ├── assets/                # Styling CSS global & SVG icons
│   ├── components/            # Komponen reusable (Navbar, Footer, Modals)
│   ├── router/
│   │   └── index.js           # Vue Router guard & route definitions
│   ├── utils/
│   │   └── imageUrl.js        # Normalisasi cover image naskah
│   └── views/
│       ├── HomeView.vue       # Landing page portal berita
│       ├── admin/             # Panel kelola naskah, review, verifikasi
│       ├── auth/              # Halaman Login & Registrasi
│       ├── guest/             # Jalur kirim naskah tamu
│       └── kontributor/       # Dashboard naskah, kirim, dan revisi
├── package.json
└── vite.config.js
```

---

## 🔑 Aturan Penting Klien

1. **Aturan FormData Interceptor**:
   Pada `frontend/src/api/axios.js`, terdapat interceptor yang memastikan browser menentukan multipart boundary:
   ```javascript
   apiClient.interceptors.request.use((config) => {
       if (config.data instanceof FormData) {
           delete config.headers['Content-Type'];
       }
       return config;
   });
   ```
2. **Kompilasi & Build**:
   - Jalankan `npm run build` untuk memverifikasi seluruh komponen valid sebelum commit.
   - Hasil build tersimpan di folder `frontend/dist/`.
