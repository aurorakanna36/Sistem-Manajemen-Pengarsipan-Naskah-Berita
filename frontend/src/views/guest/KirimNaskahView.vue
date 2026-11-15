<template>
    <div class="guest-page">
        <nav class="navbar">
            <div class="navbar-brand">
                <div class="nav-logo">HC</div>
                <span>Harian Cakrawala</span>
            </div>
            <div class="navbar-links">
                <router-link to="/" class="btn-outline">Beranda</router-link>
                <router-link to="/login" class="nav-btn">Masuk</router-link>
            </div>
        </nav>

        <div class="page-hero">
            <div class="hero-content">
                <h1>Kirim Naskah Berita</h1>
                <p>Kamu masuk sebagai guest. Hasil review naskah akan dikirim ke email yang terdaftar.</p>
            </div>
        </div>

        <div v-if="!submitSuccess" class="card-form">
            <h2>Draf tidak bisa disimpan sebagai Guest</h2>
            <p>
                Daftar sebagai kontributor untuk menyimpan draf,
                memantau status naskah, dan menerima notifikasi sistem.
            </p>
        </div>

        <div class="form-container">
            <div v-if="submitSuccess" class="success-card">
                <div class="success-icon">
                    <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>

                <h2>Naskah berhasil dikirim</h2>

                <p class="success-description">
                    Terima kasih. Hasil review akan dikirim ke
                    <strong>{{ form.email_pengirim || 'email pengirim' }}</strong>.
                </p>

                <div class="success-details">
                    <div class="detail-row">
                        <span>Judul</span>
                        <strong>{{ form.judul }}</strong>
                    </div>

                    <div class="detail-row">
                        <span>Kategori</span>
                        <strong>
                            {{kategoriOptions
                                .filter(kat => selectedKategoriIds.includes(kat.id))
                                .map(kat => kat.nama_kategori)
                                .join(', ') || '-'}}
                        </strong>
                    </div>

                    <div class="detail-row">
                        <span>Dikirim pada</span>
                        <strong>{{ tanggalKirim }}</strong>
                    </div>

                    <div class="detail-row">
                        <span>Status</span>
                        <strong class="status-review">Dalam review</strong>
                    </div>
                </div>

                <div class="success-actions">
                    <button class="btn-send-again" @click="resetForm">
                        Kirim naskah lain
                    </button>

                    <router-link to="/daftar-kontributor" class="btn-register">
                        Daftar jadi kontributor
                    </router-link>
                </div>

                <p class="success-note">
                    Sebagai guest, kamu tidak bisa memantau status naskah di sistem.
                    Daftar sebagai kontributor untuk memantau status dan menyimpan draf.
                </p>
            </div>

            <form v-else class="naskah-form" @submit.prevent="handleSubmit">
                <div v-if="error" class="alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    {{ error }}
                </div>

                <div class="form-section">
                    <h3 class="section-title">Data Pengirim</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="g-nama">Nama Lengkap <span class="required">*</span></label>
                            <input id="g-nama" v-model="form.nama_pengirim" type="text" placeholder="Nama Anda"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="g-email">Email</label>
                            <input id="g-email" v-model="form.email_pengirim" type="email"
                                placeholder="email@contoh.com" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="g-phone">Nomor Telepon / WhatsApp</label>
                            <input id="g-phone" v-model="form.phone_pengirim" type="text" placeholder="08xxxxxxxxxx" />
                        </div>
                        <div class="form-group">
                            <label for="g-sosmed">Sosial Media</label>
                            <input id="g-sosmed" v-model="form.sosmed_pengirim" type="text" placeholder="Nama sosmed" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="g-instansi">Instansi</label>
                        <input id="g-instansi" v-model="form.instansi_pengirim" type="text"
                            placeholder="Nama instansi" />
                    </div>

                </div>

                <div class="form-section">
                    <h3 class="section-title">Data Naskah</h3>
                    <div class="form-group">
                        <label for="g-judul">Judul Naskah <span class="required">*</span></label>
                        <input id="g-judul" v-model="form.judul" type="text"
                            placeholder="Tuliskan judul berita yang menarik" required />
                    </div>
                    <div class="form-group">
                        <div class="label-row">
                            <label class="field-label">
                                Kategori <span class="req">*</span>
                            </label>
                        </div>

                        <div class="kategori-radio-grid">
                            <button v-for="kat in kategoriOptions" :key="kat.id" type="button" class="radio-multi-btn"
                                :class="{ selected: isKategoriSelected(kat.id) }" @click="toggleKategori(kat.id)">
                                <span class="radio-circle">
                                    <span v-if="isKategoriSelected(kat.id)" class="radio-dot"></span>
                                </span>
                                <span class="radio-label">{{ kat.nama_kategori }}</span>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="g-isi">Isi Naskah <span class="required">*</span></label>
                        <textarea id="g-isi" v-model="form.isi" rows="6"
                            placeholder="Tulis atau tempel isi naskah di sini" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="g-file">Gambar Sampul</label>

                        <div class="file-upload-zone" :class="{ 'has-file': selectedFileName }"
                            @click="showMediaModal = true">
                            <template v-if="selectedFilePreview">
                                <img :src="selectedFilePreview" alt="Pratinjau gambar sampul" class="cover-preview" />
                            </template>

                            <template v-else>
                                <span class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </span>
                                <span>
                                    <strong>Klik untuk upload</strong> atau seret file ke sini
                                    <br />
                                    <small>JPG atau PNG, rasio 16:9</small>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <label class="disclaimer-checkbox">
                        <input type="checkbox" v-model="form.persetujuan" required>
                        <span class="disclaimer">
                            Dengan mengirimkan naskah ini, Anda menyatakan bahwa
                            informasi yang diberikan adalah benar dan bukan merupakan
                            plagiarisme. Redaksi berhak mengedit dan menerbitkan naskah
                            sesuai kebijakan editorial.
                        </span>
                    </label>

                    <button type="submit" class="btn-submit" :disabled="loading || !form.persetujuan">
                        <span v-if="loading" class="spinner"></span>
                        <span v-else>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                            Kirim Naskah
                        </span>
                    </button>
                </div>
            </form>
        </div>
        <MediaUploadGuest :visible="showMediaModal" @close="showMediaModal = false" @insert="handleMediaInsert" />

    </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/api/index.js'
import MediaUploadGuest from './MediaUploadGuest.vue'

const form = ref({
    nama_pengirim: '',
    email_pengirim: '',
    phone_pengirim: '',
    sosmed_pengirim: '',
    instansi_pengirim: '',
    judul: '',
    isi: '',
    persetujuan: false,
})

const defaultKategoriList = [
    { id: 1, nama_kategori: 'Berita', slug: 'berita' },
    { id: 2, nama_kategori: 'Nasional', slug: 'nasional' },
    { id: 3, nama_kategori: 'Pendidikan', slug: 'pendidikan' },
    { id: 4, nama_kategori: 'Teknologi', slug: 'teknologi' },
    { id: 5, nama_kategori: 'Bisnis', slug: 'bisnis' },
    { id: 6, nama_kategori: 'Daerah', slug: 'daerah' },
    { id: 7, nama_kategori: 'Ekonomi', slug: 'ekonomi' },
    { id: 8, nama_kategori: 'Kriminal', slug: 'kriminal' },
    { id: 9, nama_kategori: 'Olahraga', slug: 'olahraga' },
    { id: 10, nama_kategori: 'Opini', slug: 'opini' },
    { id: 11, nama_kategori: 'Otomotif', slug: 'otomotif' },
    { id: 12, nama_kategori: 'Politik', slug: 'politik' },
    { id: 13, nama_kategori: 'Uncategorized', slug: 'uncategorized' },
]

const kategoriOptions = ref([...defaultKategoriList])
const selectedKategoriIds = ref([])

const selectedFile = ref(null)
const selectedFileName = ref('')
const selectedFilePreview = ref('')
const showMediaModal = ref(false)

const coverMeta = ref({
    alt: '',
    judul: '',
    keterangan: '',
    deskripsi: '',
})

const loading = ref(false)
const error = ref('')
const submitSuccess = ref(false)
const tanggalKirim = ref('')


async function handleSubmit() {
    if (selectedKategoriIds.value.length === 0) {
        error.value = 'Silakan pilih minimal satu kategori naskah.'
        return
    }
    loading.value = true
    error.value = ''

    try {
        const payload = new FormData()

        payload.append('nama', form.value.nama_pengirim)
        payload.append('nama_pengirim', form.value.nama_pengirim)

        payload.append('email', form.value.email_pengirim)
        payload.append('email_pengirim', form.value.email_pengirim)

        payload.append('sosmed', form.value.sosmed_pengirim)
        payload.append('sosmed_pengirim', form.value.sosmed_pengirim)

        payload.append('instansi', form.value.instansi_pengirim)
        payload.append('instansi_pengirim', form.value.instansi_pengirim)

        payload.append('nomot_Whatsapp', form.value.phone_pengirim)
        payload.append('phone_pengirim', form.value.phone_pengirim)

        payload.append('judul', form.value.judul)
        payload.append('isi', form.value.isi)

        // Persetujuan
        payload.append('persetujuan', form.value.persetujuan ? 1 : 0)

        // Kategori
        selectedKategoriIds.value.forEach(id => {
            payload.append('kategori_ids[]', id)
        })

        // File gambar
        if (selectedFile.value) {
            payload.append('gambar_sampul', selectedFile.value)
        }

        // Metadata gambar
        payload.append('alt', coverMeta.value.alt)
        payload.append('judul_gambar', coverMeta.value.judul)
        payload.append('keterangan_gambar', coverMeta.value.keterangan)
        payload.append('deskripsi_gambar', coverMeta.value.deskripsi)

        await api.guest.kirimNaskah(payload)

        tanggalKirim.value = new Date().toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        })

        submitSuccess.value = true

    } catch (err) {
        console.error('Error kirim naskah:', err.response?.data || err)

        error.value =
            err.response?.data?.message ||
            'Gagal mengirim naskah. Silakan coba lagi.'

    } finally {
        loading.value = false
    }
}


function isKategoriSelected(id) {
    return selectedKategoriIds.value.includes(id)
}

function toggleKategori(id) {
    if (isKategoriSelected(id)) {
        selectedKategoriIds.value = selectedKategoriIds.value.filter(
            kategoriId => kategoriId !== id
        )
    } else {
        selectedKategoriIds.value.push(id)
    }
}

function resetForm() {
    if (selectedFilePreview.value) {
        URL.revokeObjectURL(selectedFilePreview.value)
    }

    form.value = {
        nama_pengirim: '',
        email_pengirim: '',
        phone_pengirim: '',
        sosmed_pengirim: '',
        instansi_pengirim: '',
        judul: '',
        isi: '',
        persetujuan: false,
    }

    selectedKategoriIds.value = []

    selectedFile.value = null
    selectedFileName.value = ''
    selectedFilePreview.value = ''

    coverMeta.value = {
        alt: '',
        judul: '',
        keterangan: '',
        deskripsi: '',
    }

    showMediaModal.value = false
    submitSuccess.value = false
    error.value = ''
}

function handleMediaInsert(media) {
    if (!media?.file) return

    if (selectedFilePreview.value) {
        URL.revokeObjectURL(selectedFilePreview.value)
    }

    selectedFile.value = media.file
    selectedFileName.value = media.file.name

    selectedFilePreview.value = URL.createObjectURL(media.file)

    coverMeta.value = {
        alt: media.alt || '',
        judul: media.judul || '',
        keterangan: media.keterangan || '',
        deskripsi: media.deskripsi || '',
    }

    showMediaModal.value = false
}
</script>

<style scoped>
.guest-page {
    min-height: 100vh;
    background: #f8fafc;
    font-family: 'Inter', sans-serif;
}

.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 2rem;
    background: white;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 10;
}

.navbar-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 700;
    font-size: 1rem;
    color: #0f2a1e;
}

.nav-logo {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #0f8168, #1e5f51);
    border-radius: 8px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 700;
}

.navbar-links {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.nav-link {
    color: #374151;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.2s;
}

.nav-link:hover {
    color: #1e5f51;
}

.nav-btn {
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #0f8168, #1e5f51);
    color: white;
    border-radius: 7px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: opacity 0.2s;
}

.nav-btn:hover {
    background: #1e5f51;
}

.btn-outline {
    padding: 0.5rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 7px;
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
    transition: border-color 0.2s;
}

.btn-outline:hover {
    border-color: #94b8a7;
}

.page-hero {
    background: linear-gradient(135deg, #0f2a22 0%, #1e5f51 100%);
    padding: 4rem 2rem;
    text-align: center;
    color: #fff;
}

.hero-content {
    max-width: 640px;
    margin: 0 auto;
}

.page-hero h1 {
    font-size: 2.25rem;
    font-weight: 800;
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
}

.page-hero p {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.7;
}

.card-form {
    width: 100%;
    max-width: 700px;
    margin: 24px auto 0px;
    padding: 12px 16px;
    background: #dbfefc;
    border: 1px solid #a4dccf;
    border-radius: 8px;
    height: auto;
    box-sizing: border-box;
}

.card-form h2 {
    font-size: 13px;
    font-weight: 700;
    color: #1e5f51;
    margin: 0 0 4px;
}

.card-form p {
    font-size: 11px;
    color: #475569;
    line-height: 1.5;
    margin: 0;
}

.form-container {
    max-width: 760px;
    margin: 0 auto;
    padding: 3rem 1.5rem;
}

.success-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 40px 42px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    max-width: 650px;
    margin: 0 auto;
}

.success-icon {
    width: 72px;
    height: 72px;
    background: #dcfce7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: #16a34a;
}

.success-card h2 {
    font-size: 26px;
    font-weight: 800;
    color: #172b43;
    margin-bottom: 16px;
}

.success-description {
    color: #475569;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 20px;
}

.success-description strong {
    color: #475569;
    font-weight: 500;
}

.success-details {
    background: #f1f5f9;
    border-radius: 10px;
    padding: 10px 20px;
    margin-bottom: 20px;
}

.success-card p {
    color: #64748b;
    line-height: 1.7;
    margin-bottom: 2rem;
}

.detail-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 13px 0;
    border-bottom: 1px solid #d5dee8;
    font-size: 14px;
    text-align: left;
}

.detail-row:last-child {
    border-bottom: 1px solid #d5dee8;
}

.detail-row span {
    color: #475569;
}

.detail-row strong {
    color: #172b43;
    font-weight: 700;
    text-align: right;
    overflow-wrap: anywhere;
}

.status-review {
    background: #dbeafe;
    color: #1d4ed8 !important;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
}

/* TOMBOL */
.success-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin: 20px 0;
    flex-wrap: wrap;
}

.btn-send-again,
.btn-register {
    padding: 13px 22px;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: 0.2s;
}

.btn-send-again {
    background: white;
    color: #172b43;
    border: 1px solid #172b43;
}

.btn-send-again:hover {
    background: #f1f5f9;
}

.btn-register {
    background: #0f766e;
    color: white;
    border: 1px solid #0f766e;
}

.btn-register:hover {
    background: #115e59;
}

/* CATATAN GUEST */
.success-note {
    font-size: 12px;
    line-height: 1.6;
    color: #475569;
    margin: 18px 0 0;
}

.naskah-form {
    background: #fff;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
}

.form-header {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.form-header h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e5f51;
    margin-bottom: 0.4rem;
}

.form-header p {
    color: #64748b;
    font-size: 0.9rem;
}

.form-section {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #1e5f51;
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e0edfa;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.required {
    color: #ef4444;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #1e5f51;
    background: #fafbfc;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: inherit;
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1e5f51;
    box-shadow: 0 0 0 3px hsla(167, 52%, 25%, 0.102);
    background: #fff;
}

.kategori-radio-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
}

.radio-multi-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.55rem 1rem;
    border-radius: 999px;
    border: 1.5px solid var(--ktr-border);
    background-color: #FAFCFF;
    color: var(--ktr-text-main);
    font-size: 0.88rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s;
}

.radio-multi-btn:hover {
    border-color: var(--ktr-teal);
    background-color: #F0F9F6;
}

.radio-multi-btn.selected {
    border-color: var(--ktr-teal);
    background-color: var(--ktr-teal-light);
    color: var(--ktr-teal);
    font-weight: 700;
}

.radio-circle {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid var(--ktr-border-dark);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #FFFFFF;
    flex-shrink: 0;
}

.radio-multi-btn.selected .radio-circle {
    border-color: var(--ktr-teal);
}

.radio-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: var(--ktr-teal);
}

.file-upload-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    color: #64748b;
    font-size: 0.875rem;
    line-height: 1.6;
}

.file-upload-zone:hover,
.file-upload-zone.has-file {
    border-color: #1e5f51;
    background: #f0f7ff;
    color: #1e5f51;
}

.file-upload-zone svg {
    display: block;
    margin: 0 auto 0.75rem;
    opacity: 0.6;
}

.cover-preview {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 10px;
    display: block;
}

.file-upload-zone.has-file {
    padding: 10px;
    overflow: hidden;
}

.file-name {
    font-weight: 600;
    font-size: 0.875rem;
}

.alert-error {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    border-radius: 10px;
    padding: 0.875rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
}

.form-footer {
    padding-top: 1.5rem;
    border-top: 1px solid #f1f5f9;
    margin-top: 25px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.disclaimer-checkbox {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
}

.disclaimer-checkbox input {
    width: 17px;
    height: 17px;
    margin-top: 3px;
    flex-shrink: 0;
    accent-color: #0d5c4d;
    cursor: pointer;
}

.disclaimer {
    font-size: 13px;
    line-height: 1.6;
    color: #64748b;
    margin: 0;
}

.btn-submit {
    width: 100%;
    padding: 0.9rem;
    background: #1e5f51;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: background 0.2s, transform 0.1s;
}

.btn-submit:hover:not(:disabled) {
    background: #1e5f51;
    transform: translateY(-1px);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    display: inline-block;
    padding: 0.75rem 2rem;
    background: #1e5f51;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 0.75rem;
    margin-right: 0.75rem;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.2s;
}

.btn-primary:hover {
    background: #cbe6e0;
}

.btn-secondary {
    display: inline-block;
    padding: 0.75rem 2rem;
    background: #f1f5f9;
    color: #475569;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.2s;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 600px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .naskah-form {
        padding: 1.5rem;
    }

    .page-hero h1 {
        font-size: 1.75rem;
    }

    .success-card {
        padding: 30px 18px;
    }

    .success-card h2 {
        font-size: 22px;
    }

    .detail-row {
        font-size: 12px;
    }

    .success-actions {
        flex-direction: column;
    }

    .btn-send-again,
    .btn-register {
        width: 100%;
        box-sizing: border-box;
    }
}
</style>
