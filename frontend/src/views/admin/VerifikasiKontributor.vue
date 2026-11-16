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
                                    <img v-if="ktpUrl" :src="ktpUrl" alt="Dokumen KTP" />
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

                        <div class="form-group rejection-group">
                            <label>Alasan penolakan</label>
                            <textarea v-model="alasanPenolakan" placeholder="Wajib diisi jika pendaftaran ditolak..."
                                rows="4"></textarea>
                        </div>

                        <button class="btn-verify" :disabled="!allChecksComplete" @click="handleVerification">
                            Verifikasi akun
                        </button>

                        <button class="btn-reject" :disabled="!alasanPenolakan.trim()" @click="handleRejection">
                            Tolak pendaftaran
                        </button>

                        <p class="decision-note">
                            Kontributor akan diberi tahu lewat email setelah keputusan disimpan.
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

const route = useRoute()
const router = useRouter()

const loading = ref(true)
const kontributor = ref({})

const checks = ref({
    dataSesuai: false,
    ktpTerbaca: false,
    suratValid: false,
    tandaTangan: false
})

const alasanPenolakan = ref('')

// Sesuaikan nama field dengan respons API Laravel kamu.
const ktpUrl = computed(() => {
    const path = kontributor.value.dokumen_ktp ||
        kontributor.value.ktp ||
        kontributor.value.foto_ktp

    return path ? (path.startsWith('http') ? path : `/storage/${path}`) : ''
})

const suratAdministrasiUrl = computed(() => {
    const path = kontributor.value.surat_administrasi ||
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

// Tampilan validasi sementara.
// Hubungkan kedua fungsi ini ke endpoint verifikasi Laravel
// setelah nama endpoint dan method API dipastikan.

function handleVerification() {
    if (!allChecksComplete.value) {
        alert('Silakan periksa dan centang seluruh kelengkapan data.')
        return
    }

    alert('Pemeriksaan lengkap. Hubungkan tombol ini ke API verifikasi akun.')
}

function handleRejection() {
    if (!alasanPenolakan.value.trim()) {
        alert('Alasan penolakan wajib diisi.')
        return
    }

    alert('Alasan penolakan sudah diisi. Hubungkan tombol ini ke API penolakan.')
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
