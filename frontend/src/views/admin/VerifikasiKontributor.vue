<template>
    <AdminLayout>
        <div class="verifikasi-kontributor-page">

            <!-- HEADER -->
            <div class="page-header">
                <div class="header-title-row">
                    <div>
                        <h1>Verifikasi pendaftaran</h1>
                        <p>Periksa data dan dokumen pendaftar sebelum memverifikasi akun.</p>
                    </div>
                </div>
            </div>

            <!-- CONTENT -->
            <div v-if="loading" class="loading-state">
                Memuat data kontributor...
            </div>

            <div v-else-if="!kontributor || !kontributor.id" class="empty-state">
                Data kontributor tidak ditemukan.
            </div>

            <div v-else class="verification-layout">

                <!-- KOLOM KIRI -->
                <div class="left-column">

                    <!-- DATA PENDAFTAR -->
                    <section class="content-card">

                        <div class="card-heading">
                            <h2>Data pendaftar</h2>

                            <span class="status-badge" :class="statusClass(kontributor.status_verifikasi)">
                                {{ statusLabel(kontributor.status_verifikasi) }}
                            </span>
                        </div>

                        <div class="form-grid">

                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <div class="data-field">
                                    {{ kontributor.nama_lengkap || kontributor.name || '-' }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Nomor WhatsApp</label>
                                <div class="data-field">
                                    {{ kontributor.nomor_whatsapp || kontributor.phone_number || '-' }}
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label>Email</label>
                                <div class="data-field">
                                    {{ kontributor.email || '-' }}
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label>
                                    Instansi / Lembaga
                                    <span class="optional">(Opsional)</span>
                                </label>
                                <div class="data-field">
                                    {{ kontributor.instansi || '-' }}
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label>Tanggal mendaftar</label>
                                <div class="data-field">
                                    {{ formatDate(kontributor.created_at) }}
                                </div>
                            </div>

                        </div>
                    </section>

                    <!-- DOKUMEN PENDAFTAR -->
                    <section class="content-card">
                        <div class="card-heading">
                            <h2>Dokumen pendaftar</h2>
                        </div>

                        <div class="documents-grid">

                            <!-- KTP -->
                            <div class="document-card">
                                <div class="document-heading">
                                    <strong>KTP</strong>
                                    <span>PDF / Gambar, maks 5MB</span>
                                </div>

                                <div class="document-preview">
                                    <img v-if="ktpUrl && isImage(ktpUrl)" :src="ktpUrl" alt="Dokumen KTP" />
                                    <div v-else-if="ktpUrl" class="pdf-preview-indicator">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <span>Dokumen PDF</span>
                                    </div>
                                    <span v-else>Pratinjau dokumen</span>
                                </div>

                                <div class="document-footer">
                                    <div>
                                        <strong>{{ ktpUrl ? 'KTP pendaftar' : 'Dokumen belum tersedia' }}</strong>
                                    </div>

                                    <div v-if="ktpUrl" class="document-actions">
                                        <a :href="ktpUrl" target="_blank">Lihat</a>
                                        <a :href="ktpUrl" download>Unduh</a>
                                    </div>
                                </div>
                            </div>

                            <!-- SURAT ADMINISTRASI -->
                            <div class="document-card">
                                <div class="document-heading">
                                    <strong>Surat Administrasi</strong>
                                    <span>PDF / Gambar, maks 5MB</span>
                                </div>

                                <div class="document-preview">
                                    <img v-if="suratAdministrasiUrl && isImage(suratAdministrasiUrl)"
                                        :src="suratAdministrasiUrl" alt="Surat Administrasi" />
                                    <div v-else-if="suratAdministrasiUrl" class="pdf-preview-indicator">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        <span>Dokumen PDF</span>
                                    </div>
                                    <span v-else>Pratinjau dokumen</span>
                                </div>

                                <div class="document-footer">
                                    <div>
                                        <strong>
                                            {{ suratAdministrasiUrl ? 'Surat administrasi' : 'Dokumen belum tersedia' }}
                                        </strong>
                                    </div>

                                    <div v-if="suratAdministrasiUrl" class="document-actions">
                                        <a :href="suratAdministrasiUrl" target="_blank">Lihat</a>
                                        <a :href="suratAdministrasiUrl" download>Unduh</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </section>

                    <!-- TANDA TANGAN DIGITAL -->
                    <section class="content-card signature-card">
                        <div class="card-heading">
                            <h2>
                                Tanda Tangan Digital
                            </h2>
                        </div>

                        <div class="signature-preview">
                            <img v-if="signatureUrl" :src="signatureUrl" alt="Tanda tangan digital" />

                            <span v-else class="no-signature">
                                Tanda tangan belum tersedia
                            </span>

                            <p v-if="signatureUrl">
                                Ditandatangani secara digital pada
                                {{ formatDate(kontributor.updated_at || kontributor.created_at) }}
                            </p>
                        </div>
                    </section>

                </div>

                <!-- KOLOM KANAN -->
                <div class="right-column">

                    <!-- IDENTITAS PENDAFTAR -->
                    <section class="applicant-summary">
                        <div class="summary-avatar">
                            {{ (kontributor.nama_lengkap || kontributor.name || 'K').charAt(0).toUpperCase() }}
                        </div>

                        <div class="summary-details">
                            <strong>
                                {{ kontributor.nama_lengkap || kontributor.name }}
                            </strong>
                            <span>
                                Mendaftar {{ formatDate(kontributor.created_at) }}
                            </span>
                        </div>
                    </section>

                    <!-- KEPUTUSAN VERIFIKASI -->
                    <section class="content-card decision-card">

                        <h2>Keputusan verifikasi</h2>

                        <p class="decision-description">
                            Centang setelah memeriksa kelengkapan data.
                        </p>

                        <div class="verification-checklist">

                            <label>
                                <input type="checkbox" v-model="checks.dataSesuai">
                                <span>Data diri sesuai</span>
                            </label>

                            <label>
                                <input type="checkbox" v-model="checks.ktpTerbaca">
                                <span>KTP terbaca jelas</span>
                            </label>

                            <label>
                                <input type="checkbox" v-model="checks.suratValid">
                                <span>Surat administrasi valid</span>
                            </label>

                            <label>
                                <input type="checkbox" v-model="checks.tandaTangan">
                                <span>Tanda tangan digital ada</span>
                            </label>

                        </div>

                        <div class="account-preview-card" v-if="kontributor.status_verifikasi !== 'verified'">
                            <div class="preview-badge-row">
                                <span class="gmail-pill">
                                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect width="20" height="16" x="2" y="4" rx="2" />
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                                    </svg>
                                    Notifikasi Gmail Aktif
                                </span>
                            </div>
                            <div class="preview-info-row">
                                <span class="preview-label">Email Baru Dibuatkan:</span>
                                <strong class="preview-email-val">{{ generatedEmail }}</strong>
                            </div>
                            <div class="preview-info-row">
                                <span class="preview-label">Kirim Notifikasi ke:</span>
                                <span class="target-gmail-val">{{ kontributor.email_gmail || kontributor.email }}</span>
                            </div>
                        </div>

                        <div class="form-group catatan-group">
                            <label>Catatan Tambahan Redaksi / Alasan Penolakan</label>
                            <textarea v-model="catatanAdmin" placeholder="Tuliskan catatan tambahan atau alasan jika pendaftaran ditolak (akan dikirimkan ke Gmail kontributor)..."
                                rows="3"></textarea>
                        </div>

                        <button class="btn-verify" :disabled="submittingVerif" @click="handleVerification">
                            <span v-if="submittingVerif">Memproses & Mengirim Gmail...</span>
                            <span v-else>Terima & Kirim Gmail (@cakrawala.id)</span>
                        </button>

                        <button class="btn-reject" :disabled="submittingVerif" @click="handleRejection">
                            <span v-if="submittingVerif">Memproses...</span>
                            <span v-else>Tolak Pendaftaran</span>
                        </button>

                        <p class="decision-note">
                            Kontributor akan secara otomatis menerima notifikasi resmi redaksi melalui Gmail setelah keputusan disimpan.
                        </p>

                    </section>

                </div>

            </div>
        </div>
    </AdminLayout>
</template>


<script setup>

import { ref, computed, onMounted } from 'vue'
import AdminLayout from './AdminLayout.vue'
import api from '@/api/index.js'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()

const loading = ref(true)
const submittingVerif = ref(false)
const kontributor = ref({})
const catatanAdmin = ref('')

const checks = ref({
    dataSesuai: false,
    ktpTerbaca: false,
    suratValid: false,
    tandaTangan: false
})

const alasanPenolakan = ref('')

const generatedEmail = computed(() => {
    const raw = kontributor.value?.nama_lengkap || kontributor.value?.name || 'kontributor'
    const slug = raw.toLowerCase().trim().replace(/[^a-z0-9]/g, '')
    return `${slug || 'kontributor'}@cakrawala.id`
})

// Sesuaikan nama field dengan respons API Laravel kamu.
const ktpUrl = computed(() => {
    const path = kontributor.value.foto_identitas ||
        kontributor.value.dokumen_ktp ||
        kontributor.value.ktp ||
        kontributor.value.foto_ktp

    return path ? (path.startsWith('http') ? path : `/storage/${path}`) : ''
})

const suratAdministrasiUrl = computed(() => {
    const path = kontributor.value.file_path ||
        kontributor.value.surat_administrasi ||
        kontributor.value.dokumen_administrasi

    return path ? (path.startsWith('http') ? path : `/storage/${path}`) : ''
})

const signatureUrl = computed(() => {
    const path = kontributor.value.tanda_tangan ||
        kontributor.value.ttd_digital

    return path ? (path.startsWith('http') ? path : `/storage/${path}`) : ''
})

const allChecksComplete = computed(() => {
    return Object.values(checks.value).every(Boolean)
})

function isImage(url) {
    return /\.(jpg|jpeg|png|webp|gif)(\?.*)?$/i.test(url)
}

function formatDate(dateStr) {
    if (!dateStr) return '-'

    const d = new Date(dateStr)

    return d.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    })
}

function statusLabel(status) {
    const map = {
        verified: 'Terverifikasi',
        pending: 'Menunggu verifikasi',
        menunggu: 'Menunggu verifikasi',
        approved: 'Terverifikasi',
        rejected: 'Ditolak',
        ditolak: 'Ditolak'
    }

    return map[status] || status || 'Belum diverifikasi'
}

function statusClass(status) {
    if (['verified', 'approved'].includes(status)) {
        return 'badge-success'
    }

    if (['rejected', 'ditolak'].includes(status)) {
        return 'badge-danger'
    }

    return 'badge-warning'
}

async function handleVerification() {
    const targetEmail = kontributor.value.email_gmail || kontributor.value.email

    const confirm = await Swal.fire({
        icon: 'question',
        title: 'Verifikasi & Buat Akun?',
        html: `Akun akan diverifikasi dan login baru <b>${generatedEmail.value}</b> akan dibuatkan.<br><br>Notifikasi resmi redaksi akan dikirim ke Gmail: <b>${targetEmail}</b>.`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Terima & Kirim Gmail',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#087d77',
        cancelButtonColor: '#64748b',
    })

    if (!confirm.isConfirmed) return

    submittingVerif.value = true
    try {
        await api.admin.verifyUser(route.params.id, {
            status: 'verified',
            catatan_admin: catatanAdmin.value || null,
        })

        await Swal.fire({
            icon: 'success',
            title: 'Berhasil Diverifikasi!',
            text: `Akun pendaftar telah diverifikasi menjadi ${generatedEmail.value} dan notifikasi Gmail telah terkirim.`,
            confirmButtonColor: '#087d77',
        })

        router.push({ name: 'admin.kontributor' })
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Memverifikasi',
            text: err.response?.data?.message || 'Terjadi kendala saat memverifikasi akun kontributor.',
            confirmButtonColor: '#087d77',
        })
    } finally {
        submittingVerif.value = false
    }
}

async function handleRejection() {
    if (!catatanAdmin.value.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Alasan Penolakan Wajib Diisi',
            text: 'Silakan isi alasan atau catatan penolakan pendaftaran pada kolom catatan sebelum menolak.',
            confirmButtonColor: '#087d77',
        })
        return
    }

    const targetEmail = kontributor.value.email_gmail || kontributor.value.email

    const confirm = await Swal.fire({
        icon: 'warning',
        title: 'Tolak Pendaftaran?',
        html: `Pendaftaran kontributor akan ditolak dan surat pemberitahuan beserta alasan akan dikirimkan ke Gmail: <b>${targetEmail}</b>.`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Tolak Pendaftaran',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
    })

    if (!confirm.isConfirmed) return

    submittingVerif.value = true
    try {
        await api.admin.verifyUser(route.params.id, {
            status: 'rejected',
            catatan_admin: catatanAdmin.value.trim(),
        })

        await Swal.fire({
            icon: 'info',
            title: 'Pendaftaran Ditolak',
            text: 'Status pendaftaran kontributor telah ditolak dan email pemberitahuan telah dikirimkan.',
            confirmButtonColor: '#087d77',
        })

        router.push({ name: 'admin.kontributor' })
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Menolak',
            text: err.response?.data?.message || 'Terjadi kesalahan saat memproses penolakan.',
            confirmButtonColor: '#087d77',
        })
    } finally {
        submittingVerif.value = false
    }
}

async function fetchKontributorDetail() {
    loading.value = true

    try {
        const res = await api.admin.getKontributorDetail(route.params.id)

        kontributor.value =
            res.data.kontributor ||
            res.data.user ||
            {}

    } catch (error) {
        console.error('Gagal mengambil detail kontributor:', error)
        kontributor.value = {}
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchKontributorDetail()
})

</script>

<style scoped>
@import '@/assets/admin-theme.css';

.verifikasi-kontributor-page {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    width: 100%;
    max-width: 100%;
    color: #152d45;
}

/* HEADER */
.page-header {
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
}

.back-link {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    padding: 0;
    border: none;
    background: none;
    color: #087d77;
    font-family: inherit;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
}

.back-link:hover {
    text-decoration: underline;
}

.header-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.header-title-row h1 {
    margin: 0;
    font-size: 1.7rem;
    font-weight: 750;
    color: #152d45;
}

.header-title-row p {
    margin: 0.3rem 0 0;
    color: #64748b;
    font-size: 0.85rem;
}

.admin-info {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
}

.admin-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #152d45;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
}

/* LAYOUT */
.verification-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.65fr) minmax(240px, 0.9fr);
    align-items: start;
    gap: 1rem;
}

.left-column,
.right-column {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    min-width: 0;
}

/* CARD */
.content-card,
.applicant-summary {
    background: #fff;
    border: 1px solid #dce5ed;
    border-radius: 13px;
    padding: 1.25rem;
}

.card-heading {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.7rem;
    margin-bottom: 1rem;
}

.card-heading h2,
.decision-card h2 {
    margin: 0;
    font-size: 1rem;
    font-weight: 750;
    color: #152d45;
}

/* STATUS */
.status-badge {
    display: inline-block;
    padding: 0.35rem 0.85rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.badge-success {
    background: #dcfce7;
    color: #15803d;
}

.badge-danger {
    background: #fee2e2;
    color: #b91c1c;
}

.badge-warning {
    background: #fff2d6;
    color: #9a5a08;
}

/* DATA PENDAFTAR */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.8rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    min-width: 0;
}

.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    font-size: 0.85rem;
    color: #374151;
    font-weight: 600;
}

.optional {
    color: #94a3b8;
    font-size: 0.65rem;
    font-weight: 400;
}

.data-field {
    min-height: 38px;
    display: flex;
    align-items: center;
    padding: 0.55rem 0.7rem;
    border: 1px solid #dce5ed;
    border-radius: 8px;
    background: #f7f9fc;
    color: #152d45;
    font-size: 0.78rem;
    overflow-wrap: anywhere;
}

/* DOKUMEN */
.documents-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.75rem;
}

.document-card {
    padding: 0.7rem;
    border: 1px solid #dce5ed;
    border-radius: 9px;
    background: #f9fbfd;
    min-width: 0;
}

.document-heading {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    margin-bottom: 0.55rem;
}

.document-heading strong {
    font-size: 0.75rem;
    color: #152d45;
}

.document-heading span {
    font-size: 0.62rem;
    color: #94a3b8;
}

.document-preview {
    height: 110px;
    border-radius: 7px;
    background: #dce6f2;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    color: #8292a5;
    font-size: 0.7rem;
    text-align: center;
}

.document-preview img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.document-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.4rem;
    margin-top: 0.65rem;
}

.document-footer strong {
    font-size: 0.65rem;
    overflow-wrap: anywhere;
}

.document-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}

.document-actions a {
    color: #087d77;
    font-size: 0.68rem;
    font-weight: 700;
    text-decoration: none;
}

.document-actions a:hover {
    text-decoration: underline;
}

/* TANDA TANGAN */
.signature-card {
    margin-bottom: 0;
}

.required {
    color: #ef4444;
}

.signature-preview {
    min-height: 125px;
    border: 1px dashed #58aaa4;
    border-radius: 10px;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.8rem;
    gap: 0.5rem;
}

.signature-preview img {
    max-width: 85%;
    max-height: 75px;
    object-fit: contain;
}

.signature-preview p {
    margin: 0;
    font-size: 0.65rem;
    color: #64748b;
}

.no-signature {
    color: #94a3b8;
    font-size: 0.75rem;
}


/* IDENTITAS KANAN */
.applicant-summary {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 0.85rem;
    min-height: 76px;
    padding: 1rem 1.25rem;
    box-sizing: border-box;
}

.summary-avatar {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 50%;
    flex-shrink: 0;
    background: #bdcbd9;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: 700;
    font-size: 1.1rem;
}

.summary-details {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.25rem;
    min-width: 0;
}

.summary-details strong {
    font-size: 0.95rem;
    font-weight: 700;
    color: #152d45;
    overflow-wrap: anywhere;
}

.summary-details span {
    font-size: 0.75rem;
    color: #64748b;
}


/* KEPUTUSAN VERIFIKASI */
.decision-card {
    padding: 1.25rem;
}

.decision-card h2 {
    font-size: 1rem;
    color: #152d45;
}

.decision-description {
    font-size: 0.73rem;
    color: #64748b;
    margin: 0.55rem 0 0.9rem;
    line-height: 1.5;
}

.verification-checklist {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    margin-bottom: 1rem;
}

.verification-checklist label {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    cursor: pointer;
    color: #374151;
    font-size: 0.85rem;
}

.verification-checklist input {
    width: 15px;
    height: 15px;
    accent-color: #087d77;
    cursor: pointer;
    flex-shrink: 0;
}

/* ALASAN PENOLAKAN */
.rejection-group {
    margin-bottom: 0.85rem;
}

.rejection-group textarea {
    width: 100%;
    box-sizing: border-box;
    min-height: 80px;
    padding: 0.7rem;
    border: 1px solid #dce5ed;
    border-radius: 8px;
    background: #f8fafc;
    font-family: inherit;
    font-size: 0.75rem;
    color: #152d45;
    resize: vertical;
    outline: none;
}

.rejection-group textarea:focus {
    border-color: #087d77;
    box-shadow: 0 0 0 2px rgba(8, 125, 119, 0.1);
}

.rejection-group textarea::placeholder {
    color: #94a3b8;
}

/* NOTIFIKASI GMAIL & PREVIEW CARD */
.account-preview-card {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    padding: 0.85rem;
    margin-bottom: 0.9rem;
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
}

.preview-badge-row {
    display: flex;
    align-items: center;
    margin-bottom: 0.2rem;
}

.gmail-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #dcfce7;
    color: #166534;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 999px;
}

.preview-info-row {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.preview-label {
    font-size: 0.68rem;
    color: #4b5563;
    font-weight: 600;
}

.preview-email-val {
    font-size: 0.82rem;
    color: #087d77;
    font-weight: 800;
    font-family: monospace;
}

.target-gmail-val {
    font-size: 0.75rem;
    color: #374151;
    font-weight: 600;
}

.catatan-group {
    margin-bottom: 0.85rem;
}

.catatan-group label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #374151;
}

.catatan-group textarea {
    width: 100%;
    box-sizing: border-box;
    min-height: 75px;
    padding: 0.65rem;
    border: 1px solid #dce5ed;
    border-radius: 8px;
    background: #f8fafc;
    font-family: inherit;
    font-size: 0.75rem;
    color: #152d45;
    resize: vertical;
    outline: none;
}

.catatan-group textarea:focus {
    border-color: #087d77;
    box-shadow: 0 0 0 2px rgba(8, 125, 119, 0.1);
}

/* BUTTON */
.btn-verify,
.btn-reject {
    width: 100%;
    min-height: 39px;
    border-radius: 8px;
    font-family: inherit;
    font-size: 0.76rem;
    font-weight: 700;
    cursor: pointer;
    transition: 0.2s;
}

.btn-verify {
    background: #08766f;
    border: 1px solid #08766f;
    color: white;
    margin-bottom: 0.5rem;
}

.btn-verify:hover:not(:disabled) {
    background: #065c57;
}

.btn-reject {
    background: white;
    color: #b91c1c;
    border: 1px solid #dc7777;
}

.btn-reject:hover:not(:disabled) {
    background: #fff1f1;
}

.btn-verify:disabled,
.btn-reject:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.decision-note {
    font-size: 0.65rem;
    line-height: 1.5;
    color: #64748b;
    margin: 0.75rem 0 0;
}

.loading-state,
.empty-state {
    padding: 2rem;
    text-align: center;
    color: #64748b;
    background: white;
    border-radius: 12px;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .verification-layout {
        grid-template-columns: minmax(0, 1.4fr) minmax(220px, 0.9fr);
    }
}

@media (max-width: 700px) {
    .verification-layout {
        grid-template-columns: 1fr;
    }

    .header-title-row {
        align-items: flex-start;
    }

    .header-title-row h1 {
        font-size: 1.35rem;
    }

    .admin-info {
        font-size: 0.7rem;
    }
}

@media (max-width: 480px) {
    .form-grid,
    .documents-grid {
        grid-template-columns: 1fr;
    }

    .header-title-row {
        flex-direction: column;
    }

    .content-card {
        padding: 1rem;
    }

    .card-heading h2,
    .decision-card h2 {
        font-size: 0.9rem;
    }
}
</style>
