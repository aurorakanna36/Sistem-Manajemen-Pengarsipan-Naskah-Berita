<template>
    <AdminLayout>
        <div class="detail-review-page">
            <div class="top-nav-bar">
            </div>

            <div v-if="loading" class="empty-state">Memuat detail naskah...</div>
            <div v-else-if="!naskah" class="empty-state">Data naskah tidak ditemukan.</div>
            <div v-else class="review-grid">
                <div class="naskah-main-panel">
                    <div class="naskah-header-image">
                        <figure v-if="coverImageUrl" class="sampul-figure">
                            <img :src="coverImageUrl" :alt="naskah.judul || 'Sampul naskah'" class="sampul-img" @error="handleSampulError" />

                            <figcaption v-if="naskah.gambar_sampul_keterangan">
                                Keterangan: {{ naskah.gambar_sampul_keterangan }}
                            </figcaption>
                        </figure>

                        <span v-else class="sampul-empty-text">Gambar sampul belum tersedia</span>
                    </div>

                    <div class="naskah-content-area">
                        <div class="badges-row" style="flex-wrap: wrap;">
                            <span class="status-badge" :class="statusClass(naskah.status_naskah || naskah.status)">
                                {{ statusLabel(naskah.status_naskah || naskah.status) }}
                            </span>
                            <template v-if="naskah.kategoris && naskah.kategoris.length">
                                <span v-for="kat in naskah.kategoris" :key="kat.id" class="category-badge">
                                    {{ kat.nama_kategori }}
                                </span>
                            </template>
                            <span v-else class="category-badge">{{ naskah.kategori || 'Umum' }}</span>
                        </div>

                        <h1 class="naskah-title">{{ naskah.judul }}</h1>

                        <p class="naskah-author-line">
                            Dikirim {{ formatDate(naskah.waktu_kirim || naskah.created_at) }} · {{ authorName }}
                        </p>

                        <div class="content-section">
                            <h2 class="section-label">Isi naskah</h2>
                            <div v-if="isHtmlContent" class="article-paragraphs rich-html-body"
                                v-html="renderedIsiContent"></div>
                            <div v-else class="article-paragraphs">
                                <p v-for="(p, i) in articleParagraphs" :key="i">
                                    {{ p }}
                                </p>
                            </div>
                        </div>

                        <div v-if="naskah.file_path" class="file-attachment-bar">
                            <div class="file-info-col">
                                <span class="file-icon">&#128196;</span>
                                <span class="file-name">{{ fileName }}</span>
                            </div>
                            <a :href="naskah.file_path" target="_blank" class="btn-download-file">
                                Unduh
                            </a>
                        </div>
                    </div>
                </div>

                <div class="side-action-panel">
                    <div class="side-card contributor-card">
                        <h2 class="side-card-title">Kontributor</h2>

                        <div class="contributor-head">
                            <div class="contributor-avatar">
                                {{ authorName.charAt(0).toUpperCase() }}
                            </div>
                            <div class="contributor-identity">
                                <h3 class="contributor-name">{{ authorName }}</h3>
                                <p class="contributor-email">{{ authorEmail }}</p>
                            </div>
                        </div>

                        <p class="contributor-instansi">
                            {{ authorInstansi || 'Instansi belum diatur' }}
                        </p>

                        <p class="contributor-stats-line">
                            {{ authorStatsText }}
                        </p>

                        <router-link v-if="authorId" :to="`/admin/kontributor/${authorId}`" class="link-profile-author">
                            Lihat profil kontributor
                        </router-link>
                    </div>

                    <div class="side-card decision-card">
                        <h2 class="side-card-title">Keputusan</h2>

                        <form @submit.prevent="submitDecision" class="decision-form">
                            <div v-if="decisionError" class="decision-error-box">
                                {{ decisionError }}
                            </div>

                            <div class="radio-options-list">
                                <label class="radio-card-label" :class="{ selected: selectedDecision === 'approved' }">
                                    <input type="radio" value="approved" v-model="selectedDecision" name="decision" />
                                    <span>Terima dan terbitkan</span>
                                </label>

                                <label class="radio-card-label" :class="{ selected: selectedDecision === 'revision' }">
                                    <input type="radio" value="revision" v-model="selectedDecision" name="decision" />
                                    <span>Minta revisi</span>
                                </label>

                                <label class="radio-card-label" :class="{ selected: selectedDecision === 'rejected' }">
                                    <input type="radio" value="rejected" v-model="selectedDecision" name="decision" />
                                    <span>Tolak naskah</span>
                                </label>
                            </div>

                            <div class="form-field-group">
                                <label class="field-label">Catatan atau alasan</label>
                                <textarea v-model="decisionNote" rows="4"
                                    placeholder="Wajib diisi jika naskah ditolak atau perlu direvisi"
                                    class="note-textarea"></textarea>
                            </div>

                            <div v-if="selectedDecision === 'approved'" class="form-field-group">
                                <label class="field-label">URL Publikasi Berita (Opsional)</label>
                                <input v-model="urlBerita" type="url" placeholder="https://cakrawala.com/berita/..."
                                    class="note-input" />
                            </div>

                            <button type="submit" class="btn-save-decision" :disabled="savingDecision">
                                {{ savingDecision ? 'Menyimpan...' : 'Simpan keputusan' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from './AdminLayout.vue'
import api from '@/api/index.js'
import Swal from 'sweetalert2'
import { getCoverImageUrl } from '@/utils/imageUrl.js'

const route = useRoute()
const router = useRouter()
const loading = ref(true)
const imageLoadFailed = ref(false)

const naskah = ref(null)
const selectedDecision = ref('approved')
const decisionNote = ref('')
const urlBerita = ref('')
const savingDecision = ref(false)
const decisionError = ref('')

const authorName = computed(() => {
    return naskah.value?.nama_kontributor || naskah.value?.penulis?.name || naskah.value?.author?.name || 'Kontributor'
})

const authorEmail = computed(() => {
    return naskah.value?.penulis?.email || naskah.value?.author?.email || '-'
})

const authorInstansi = computed(() => {
    return naskah.value?.penulis?.instansi || naskah.value?.author?.instansi || ''
})

const authorId = computed(() => {
    return naskah.value?.id_kontributor || naskah.value?.author?.id || null
})

const authorStatsText = computed(() => {
    const total = naskah.value?.author_total_naskah || 1
    const accepted = naskah.value?.author_diterima_naskah || 0
    return `${total} naskah dikirim · ${accepted} diterima`
})

const fileName = computed(() => {
    if (!naskah.value?.file_path) return 'berkas_naskah.pdf'
    const parts = naskah.value.file_path.split('/')
    return parts[parts.length - 1]
})

const isHtmlContent = computed(() => {
    const text = naskah.value?.isi || naskah.value?.isi_naskah || ''
    return /<[a-z][\s\S]*>/i.test(text)
})

const renderedIsiContent = computed(() => {
    const raw = naskah.value?.isi || naskah.value?.isi_naskah || ''
    if (!raw) return ''

    const baseUrl = (import.meta.env.VITE_APP_URL || 'http://localhost:8000').replace(/\/$/, '')

    return raw.replace(/<img([^>]+)src=["']([^"']+)["']([^>]*)>/gi, (match, prefix, src, suffix) => {
        if (src.startsWith('data:') || src.startsWith('blob:')) {
            return match
        }
        if (src.startsWith('/storage/')) {
            return `<img${prefix}src="${baseUrl}${src}"${suffix}>`
        }
        if (src.includes('/storage/')) {
            const storagePath = src.substring(src.indexOf('/storage/'))
            return `<img${prefix}src="${baseUrl}${storagePath}"${suffix}>`
        }
        return match
    })
})

const articleParagraphs = computed(() => {
    const text = naskah.value?.isi || naskah.value?.isi_naskah || ''
    if (!text) {
        return ['Isi naskah belum dilampirkan secara teks langsung. Silakan unduh berkas naskah di bawah untuk membaca naskah selengkapnya.']
    }
    return text.split('\n\n').filter(p => p.trim())
})

const coverImageUrl = computed(() => {
    if (imageLoadFailed.value) return ''
    return getCoverImageUrl(naskah.value)
})

function handleSampulError() {
    imageLoadFailed.value = true
}

function formatDate(dateStr) {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
}

function statusLabel(status) {
    const map = {
        diterima: 'Diterima',
        approved: 'Diterima',
        ditolak: 'Ditolak',
        rejected: 'Ditolak',
        direvisi: 'Perlu revisi',
        revision: 'Perlu revisi',
        perlu_direview: 'Perlu review',
        pending: 'Perlu review',
        diterbitkan: 'Diterbitkan'
    }
    return map[status] || status
}

function statusClass(status) {
    if (status === 'diterima' || status === 'approved' || status === 'diterbitkan') return 'badge-success'
    if (status === 'ditolak' || status === 'rejected') return 'badge-danger'
    if (status === 'direvisi' || status === 'revision') return 'badge-warning'
    return 'badge-info'
}


async function fetchNaskahDetail() {
    loading.value = true
    imageLoadFailed.value = false
    const id = route.params.id

    try {
        const res = await api.admin.getNaskahDetail(id)
        naskah.value = res.data.naskah || res.data.data

        console.log('Data naskah:', naskah.value)
        console.log('Gambar sampul:', naskah.value?.gambar_sampul)
        console.log('Alt gambar:', naskah.value?.gambar_sampul_alt)
        console.log('Keterangan gambar:', naskah.value?.gambar_sampul_keterangan)

        if (res.data.review?.catatan) {
            decisionNote.value = res.data.review.catatan
        }
    } catch (error) {
        console.error('Gagal mengambil detail naskah:', error)

        try {
            const listRes = await api.admin.getNaskahs()
            const all = listRes.data.naskahs || listRes.data.data || []
            naskah.value = all.find(item => String(item.id) === String(id)) || null
        } catch (err) {
            console.error(err)
        }
    } finally {
        loading.value = false
    }
}


async function submitDecision() {
    decisionError.value = ''
    if ((selectedDecision.value === 'revision' || selectedDecision.value === 'rejected') && !decisionNote.value.trim()) {
        decisionError.value = 'Wajib mengisi catatan atau alasan untuk keputusan ini.'
        return
    }

    savingDecision.value = true
    try {
        const res = await api.admin.decideNaskah(naskah.value.id, {
            action: selectedDecision.value,
            status: selectedDecision.value,
            catatan: decisionNote.value,
            url_berita: urlBerita.value,
        })

        const baseMessage = res.data?.message || 'Keputusan editorial naskah berhasil disimpan.'
        let popupHtml = `<p style="margin: 0 0 12px; font-size: 1rem; color: #334155;">${baseMessage}</p>`

        if (res.data?.is_guest && res.data?.recipient_email) {
            if (res.data?.email_sent) {
                popupHtml += `
                    <div style="background: #e6f5f2; border: 1px solid #99d5ca; border-radius: 8px; padding: 12px 14px; text-align: left; font-size: 0.88rem; color: #0d5c4d;">
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; margin-bottom: 4px;">
                            <span>&#9993; Notifikasi Email Terkirim</span>
                        </div>
                        Pemberitahuan pembaruan status naskah telah dikirimkan ke alamat Gmail guest:
                        <div style="margin-top: 4px; font-weight: 600; color: #094338;">${res.data.recipient_email}</div>
                    </div>
                `
            } else {
                popupHtml += `
                    <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 14px; text-align: left; font-size: 0.88rem; color: #92400e;">
                        <div style="font-weight: 700; margin-bottom: 4px;">&#9888; Notifikasi Email</div>
                        Naskah guest terdeteksi (${res.data.recipient_email}), namun pengiriman email mengalami kendala atau sedang dalam antrean.
                    </div>
                `
            }
        }

        await Swal.fire({
            icon: 'success',
            title: 'Keputusan Tersimpan',
            html: popupHtml,
            confirmButtonColor: '#1e5f51',
            confirmButtonText: 'Mengerti'
        })

        router.push({ name: 'admin.kelola.naskah' })
    } catch (err) {
        decisionError.value = err.response?.data?.message || 'Gagal menyimpan keputusan editorial.'
        Swal.fire({
            icon: 'error',
            title: 'Gagal Menyimpan',
            text: decisionError.value,
            confirmButtonColor: '#1e5f51',
        })
    } finally {
        savingDecision.value = false
    }
}

onMounted(() => {
    fetchNaskahDetail()
})
</script>

<style scoped>
@import '@/assets/admin-theme.css';

.detail-review-page {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.top-nav-bar {
    margin-bottom: 0.25rem;
}

.review-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.75rem;
    align-items: flex-start;
}

.naskah-main-panel {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.naskah-header-image {
    min-height: 300px;
    height: auto;
    background-color: var(--adm-border-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--adm-text-muted);
    font-size: 1rem;
    font-weight: 500;
    overflow: hidden;
}

.sampul-figure {
    width: 100%;
    max-height: 350px;
    object-fit: cover;
    display: block;
}

.sampul-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sampul-figure figcaption {
    width: 100%;
    padding: 0.75rem 1.5rem;
    box-sizing: border-box;
    background: var(--adm-bg-page);
    color: var(--adm-text-muted);
    font-size: 0.85rem;
    font-style: italic;
    text-align: center;
    line-height: 1.5;
}

.rich-html-body :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 0.75rem 0;
}

.rich-html-body :deep(figure) {
    margin: 1.5rem 0;
    text-align: center;
}

.rich-html-body :deep(figure.align-left) {
    float: left;
    width: auto;
    margin: 0.5rem 1.25rem 1rem 0;
    clear: none;
}

.rich-html-body :deep(figure.align-right) {
    float: right;
    width: auto;
    margin: 0.5rem 0 1rem 1.25rem;
    clear: none;
}

.rich-html-body :deep(figure.align-center) {
    display: block;
    width: fit-content;
    max-width: 100%;
    margin: 1.25rem auto;
    float: none;
    clear: both;
    text-align: center;
}

.rich-html-body :deep(figure.align-full) {
    display: block;
    width: 100%;
    margin: 1.25rem 0;
    float: none;
    clear: both;
}

.rich-html-body :deep(figure.align-full img) {
    width: 100% !important;
    max-width: 100% !important;
    height: auto !important;
}

.rich-html-body :deep(figure img) {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    display: block;
    margin: 0 auto;
}

.rich-html-body :deep(figcaption) {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    line-height: 1.5;
    color: var(--adm-text-muted);
    font-style: italic;
    text-align: center;
}

.naskah-content-area {
    padding: 2.25rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.badges-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.status-badge {
    padding: 0.35rem 0.85rem;
    border-radius: 999px;
    font-size: 0.825rem;
    font-weight: 600;
}

.badge-info {
    background-color: #E0EDFB;
    color: #1E40AF;
    background-color: var(--adm-info-bg);
    color: var(--adm-info-text);
}

.badge-success {
    background-color: var(--adm-success-bg);
    color: var(--adm-success-text);
}

.badge-warning {
    background-color: var(--adm-warning-bg);
    color: var(--adm-warning-text);
}

.badge-danger {
    background-color: var(--adm-danger-bg);
    color: var(--adm-danger-text);
}

.category-badge {
    background-color: #F1F5F9;
    background-color: var(--adm-bg-page);
    color: var(--adm-text-muted);
    padding: 0.35rem 0.85rem;
    border-radius: 999px;
    font-size: 0.825rem;
    font-weight: 600;
}

.naskah-title {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--adm-text-main);
    margin: 0;
    line-height: 1.25;
}

.naskah-author-line {
    font-size: 0.9rem;
    color: var(--adm-text-muted);
    margin: -0.5rem 0 0.5rem;
}

.content-section {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.section-label {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0;
}

.section-body {
    font-size: 0.95rem;
    color: #475569;
    color: var(--adm-text-muted);
    line-height: 1.7;
    margin: 0;
}

.article-paragraphs {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    font-size: 0.95rem;
    color: #334155;
    color: var(--adm-text-main);
    line-height: 1.75;
}

.file-attachment-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #F1F5F9;
    background: var(--adm-bg-page);
    border-radius: 10px;
    padding: 1rem 1.25rem;
    margin-top: 0.5rem;
}

.file-info-col {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.file-icon {
    font-size: 1.25rem;
}

.file-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--adm-text-main);
}

.btn-download-file {
    color: var(--adm-teal);
    font-size: 0.9rem;
    font-weight: 700;
    text-decoration: none;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    transition: background 0.2s;
}

.btn-download-file:hover {
    background: var(--adm-teal-light);
}

.side-action-panel {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.side-card {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
}

.side-card-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0 0 1.25rem 0;
}

.contributor-head {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    margin-bottom: 0.75rem;
}

.contributor-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background-color: #BCC8D4;
    background-color: var(--adm-border-dark);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
}

.contributor-identity {
    min-width: 0;
}

.contributor-name {
    font-size: 1rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0;
}

.contributor-email {
    font-size: 0.825rem;
    color: var(--adm-text-muted);
    margin: 0.15rem 0 0;
}

.contributor-instansi {
    font-size: 0.85rem;
    color: var(--adm-text-muted);
    margin: 0 0 0.5rem;
}

.contributor-stats-line {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--adm-text-main);
    margin: 0 0 1rem;
}

.link-profile-author {
    color: var(--adm-teal);
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
}

.link-profile-author:hover {
    text-decoration: underline;
}

.decision-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.decision-error-box {
    background: var(--adm-danger-bg);
    color: var(--adm-danger-text);
    padding: 0.75rem;
    border-radius: 8px;
    font-size: 0.825rem;
}

.radio-options-list {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
}

.radio-card-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    border: 1px solid var(--adm-border);
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--adm-text-main);
    transition: border-color 0.2s, background 0.2s;
}

.radio-card-label.selected {
    border-color: var(--adm-teal);
    background: #F0FDF4;
    background: var(--adm-teal-light);
}

.radio-card-label input[type="radio"] {
    accent-color: var(--adm-teal);
    width: 18px;
    height: 18px;
}

.form-field-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.field-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--adm-text-main);
}

.note-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    font-size: 0.85rem;
    outline: none;
    box-sizing: border-box;
    font-family: inherit;
    resize: vertical;
}

.note-textarea:focus {
    border-color: var(--adm-teal);
}

.note-input {
    width: 100%;
    padding: 0.65rem 0.75rem;
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    font-size: 0.85rem;
    outline: none;
    box-sizing: border-box;
}

.note-input:focus {
    border-color: var(--adm-teal);
}

.btn-save-decision {
    width: 100%;
    padding: 0.85rem;
    background: var(--adm-teal);
    color: #ffffff;
    border: none;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-save-decision:hover:not(:disabled) {
    background: var(--adm-teal-hover);
}

.btn-save-decision:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.empty-state {
    padding: 4rem 0;
    text-align: center;
    color: var(--adm-text-muted);
}

.rich-html-body :deep(a) {
    color: #1a56db;
    text-decoration: underline;
    text-underline-offset: 2px;
    cursor: pointer;
    font-weight: 500;
    word-break: break-all;
}
.rich-html-body :deep(a:hover) {
    color: #1e4fc2;
}

@media (max-width: 950px) {
    .review-grid {
        grid-template-columns: 1fr;
    }
}
</style>
