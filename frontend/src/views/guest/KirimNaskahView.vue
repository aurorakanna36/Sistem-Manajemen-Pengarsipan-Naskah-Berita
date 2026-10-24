<template>
    <div class="guest-page">
        <nav class="navbar">
            <div class="navbar-brand">
                <div class="nav-logo">HC</div>
                <span>Harian Cakrawala</span>
            </div>
            <div class="navbar-links">
                <router-link to="/" class="nav-link">Beranda</router-link>
                <router-link to="/login" class="nav-btn">Masuk</router-link>
            </div>
        </nav>

        <div class="page-hero">
            <div class="hero-content">
                <div class="hero-badge">Kontribusi Publik</div>
                <h1>Kirim Naskah Berita</h1>
                <p>Punya informasi atau berita penting? Kirimkan naskah Anda kepada redaksi Harian Cakrawala. Setiap naskah yang masuk akan ditinjau oleh tim redaksi kami.</p>
            </div>
        </div>

        <div class="form-container">
            <div v-if="submitSuccess" class="success-card">
                <div class="success-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <h2>Naskah Berhasil Dikirim!</h2>
                <p>Terima kasih atas kontribusi Anda. Tim redaksi Harian Cakrawala akan meninjau naskah Anda dan menghubungi Anda jika diperlukan.</p>
                <button class="btn-primary" @click="resetForm">Kirim Naskah Lain</button>
                <router-link to="/" class="btn-secondary">Kembali ke Beranda</router-link>
            </div>

            <form v-else class="naskah-form" @submit.prevent="handleSubmit">
                <div class="form-header">
                    <h2>Detail Naskah</h2>
                    <p>Isi semua kolom berikut dengan informasi yang lengkap dan akurat.</p>
                </div>

                <div v-if="error" class="alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ error }}
                </div>

                <div class="form-section">
                    <h3 class="section-title">Informasi Pengirim</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="g-nama">Nama Lengkap <span class="required">*</span></label>
                            <input id="g-nama" v-model="form.nama_pengirim" type="text" placeholder="Nama Anda" required />
                        </div>
                        <div class="form-group">
                            <label for="g-email">Email</label>
                            <input id="g-email" v-model="form.email_pengirim" type="email" placeholder="email@contoh.com" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="g-phone">Nomor Telepon / WhatsApp</label>
                        <input id="g-phone" v-model="form.phone_pengirim" type="text" placeholder="08xxxxxxxxxx" />
                    </div>
                </div>

                <div class="form-section">
                    <h3 class="section-title">Naskah Berita</h3>
                    <div class="form-group">
                        <label for="g-judul">Judul Naskah <span class="required">*</span></label>
                        <input id="g-judul" v-model="form.judul" type="text" placeholder="Tuliskan judul berita yang menarik" required />
                    </div>
                    <div class="form-group">
                        <label for="g-isi">Isi / Deskripsi Singkat <span class="required">*</span></label>
                        <textarea id="g-isi" v-model="form.isi" rows="6" placeholder="Tuliskan ringkasan atau isi berita Anda di sini..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="g-file">Lampiran Dokumen / Berkas</label>
                        <div class="file-upload-zone" :class="{ 'has-file': selectedFileName }" @click="triggerFileInput">
                            <svg v-if="!selectedFileName" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <svg v-else width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                            <span v-if="selectedFileName" class="file-name">{{ selectedFileName }}</span>
                            <span v-else>
                                <strong>Klik untuk upload</strong> atau seret file ke sini<br/>
                                <small>PDF, DOC, DOCX, JPG, PNG — Maks. 10MB</small>
                            </span>
                            <input id="g-file" ref="fileInput" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" @change="handleFileChange" hidden />
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <p class="disclaimer">
                        Dengan mengirimkan naskah ini, Anda menyatakan bahwa informasi yang diberikan adalah benar dan bukan merupakan plagiarisme. Redaksi berhak mengedit dan menerbitkan naskah sesuai kebijakan editorial.
                    </p>
                    <button type="submit" class="btn-submit" :disabled="loading">
                        <span v-if="loading" class="spinner"></span>
                        <span v-else>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                            Kirim Naskah
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '@/api/index.js'

const form = ref({
    nama_pengirim: '',
    email_pengirim: '',
    phone_pengirim: '',
    judul: '',
    isi: '',
})
const selectedFile = ref(null)
const selectedFileName = ref('')
const fileInput = ref(null)
const loading = ref(false)
const error = ref('')
const submitSuccess = ref(false)

function triggerFileInput() {
    fileInput.value.click()
}

function handleFileChange(event) {
    const file = event.target.files[0]
    if (file) {
        selectedFile.value = file
        selectedFileName.value = file.name
    }
}

async function handleSubmit() {
    loading.value = true
    error.value = ''
    try {
        const payload = new FormData()
        payload.append('nama_pengirim', form.value.nama_pengirim)
        payload.append('email_pengirim', form.value.email_pengirim)
        payload.append('phone_pengirim', form.value.phone_pengirim)
        payload.append('judul', form.value.judul)
        payload.append('isi', form.value.isi)
        if (selectedFile.value) {
            payload.append('file', selectedFile.value)
        }
        await api.guest.kirimNaskah(payload)
        submitSuccess.value = true
    } catch (err) {
        error.value = err.response?.data?.message || 'Gagal mengirim naskah. Silakan coba lagi.'
    } finally {
        loading.value = false
    }
}

function resetForm() {
    form.value = { nama_pengirim: '', email_pengirim: '', phone_pengirim: '', judul: '', isi: '' }
    selectedFile.value = null
    selectedFileName.value = ''
    submitSuccess.value = false
    error.value = ''
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
    padding: 0 2rem;
    height: 64px;
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 100;
}

.navbar-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 700;
    font-size: 1.1rem;
    color: #0f172a;
}

.nav-logo {
    width: 36px;
    height: 36px;
    background: #0f4c81;
    color: #fff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
}

.navbar-links {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.nav-link {
    color: #64748b;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.2s;
}

.nav-link:hover { color: #0f4c81; }

.nav-btn {
    background: #0f4c81;
    color: #fff;
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    transition: background 0.2s;
}

.nav-btn:hover { background: #0a3460; }

.page-hero {
    background: linear-gradient(135deg, #0f4c81 0%, #1e3a5f 100%);
    padding: 4rem 2rem;
    text-align: center;
    color: #fff;
}

.hero-content {
    max-width: 640px;
    margin: 0 auto;
}

.hero-badge {
    display: inline-block;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    padding: 0.35rem 1rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.page-hero h1 {
    font-size: 2.25rem;
    font-weight: 800;
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
}

.page-hero p {
    font-size: 1rem;
    color: rgba(255,255,255,0.8);
    line-height: 1.7;
}

.form-container {
    max-width: 760px;
    margin: 0 auto;
    padding: 3rem 1.5rem;
}

.success-card {
    background: #fff;
    border-radius: 16px;
    padding: 3rem;
    text-align: center;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    border: 1px solid #e2e8f0;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: #f0fdf4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: #16a34a;
}

.success-card h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 0.75rem;
}

.success-card p {
    color: #64748b;
    line-height: 1.7;
    margin-bottom: 2rem;
}

.naskah-form {
    background: #fff;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
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
    color: #0f172a;
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
    color: #0f4c81;
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

.required { color: #ef4444; }

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #0f172a;
    background: #fafbfc;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: inherit;
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #0f4c81;
    box-shadow: 0 0 0 3px rgba(15,76,129,0.1);
    background: #fff;
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
    border-color: #0f4c81;
    background: #f0f7ff;
    color: #0f4c81;
}

.file-upload-zone svg {
    display: block;
    margin: 0 auto 0.75rem;
    opacity: 0.6;
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
}

.disclaimer {
    font-size: 0.78rem;
    color: #94a3b8;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.btn-submit {
    width: 100%;
    padding: 0.9rem;
    background: #0f4c81;
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
    background: #0a3460;
    transform: translateY(-1px);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    display: inline-block;
    padding: 0.75rem 2rem;
    background: #0f4c81;
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

.btn-primary:hover { background: #0a3460; }

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

.btn-secondary:hover { background: #e2e8f0; }

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

@media (max-width: 600px) {
    .form-row { grid-template-columns: 1fr; }
    .naskah-form { padding: 1.5rem; }
    .page-hero h1 { font-size: 1.75rem; }
}
</style>
