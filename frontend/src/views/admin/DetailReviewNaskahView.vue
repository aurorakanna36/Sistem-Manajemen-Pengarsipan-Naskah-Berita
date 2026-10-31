<template>
    <AdminLayout>
        <div class="detail-review-page">
            <div class="top-nav-bar">
                <router-link to="/admin/review-naskah" class="back-link">
                    &larr; Kembali ke review naskah
                </router-link>
            </div>

            <div v-if="loading" class="empty-state">Memuat detail naskah...</div>
            <div v-else-if="!naskah" class="empty-state">Data naskah tidak ditemukan.</div>
            <div v-else class="review-grid">
                <div class="naskah-main-panel">
                    <div class="naskah-header-image">
                        <span>Gambar sampul</span>
                    </div>

                    <div class="naskah-content-area">
                        <div class="badges-row">
                            <span class="status-badge" :class="statusClass(naskah.status_naskah || naskah.status)">
                                {{ statusLabel(naskah.status_naskah || naskah.status) }}
                            </span>
                            <span class="category-badge">{{ naskah.kategori || 'Umum' }}</span>
                        </div>

                        <h1 class="naskah-title">{{ naskah.judul }}</h1>

                        <p class="naskah-author-line">
                            Dikirim {{ formatDate(naskah.waktu_kirim || naskah.created_at) }} · {{ authorName }}
                        </p>

                        <div v-if="naskah.abstrak" class="content-section">
                            <h2 class="section-label">Abstrak</h2>
                            <p class="section-body">{{ naskah.abstrak }}</p>
                        </div>

                        <div class="content-section">
                            <h2 class="section-label">Isi naskah</h2>
                            <div class="article-paragraphs">
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

                        <router-link 
                            v-if="authorId"
                            :to="`/admin/kontributor/${authorId}`"
                            class="link-profile-author"
                        >
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
                                <label 
                                    class="radio-card-label"
                                    :class="{ selected: selectedDecision === 'approved' }"
                                >
                                    <input 
                                        type="radio" 
                                        value="approved" 
                                        v-model="selectedDecision"
                                        name="decision"
                                    />
                                    <span>Terima dan terbitkan</span>
                                </label>

                                <label 
                                    class="radio-card-label"
                                    :class="{ selected: selectedDecision === 'revision' }"
                                >
                                    <input 
                                        type="radio" 
                                        value="revision" 
                                        v-model="selectedDecision"
                                        name="decision"
                                    />
                                    <span>Minta revisi</span>
                                </label>

                                <label 
                                    class="radio-card-label"
                                    :class="{ selected: selectedDecision === 'rejected' }"
                                >
                                    <input 
                                        type="radio" 
                                        value="rejected" 
                                        v-model="selectedDecision"
                                        name="decision"
                                    />
                                    <span>Tolak naskah</span>
                                </label>
                            </div>

                            <div class="form-field-group">
                                <label class="field-label">Catatan atau alasan</label>
                                <textarea 
                                    v-model="decisionNote"
                                    rows="4" 
                                    placeholder="Wajib diisi jika naskah ditolak atau perlu direvisi"
                                    class="note-textarea"
                                ></textarea>
                            </div>

                            <div v-if="selectedDecision === 'approved'" class="form-field-group">
                                <label class="field-label">URL Publikasi Berita (Opsional)</label>
                                <input 
                                    v-model="urlBerita" 
                                    type="url" 
                                    placeholder="https://cakrawala.com/berita/..."
                                    class="note-input"
                                />
                            </div>

                            <button 
                                type="submit" 
                                class="btn-save-decision"
                                :disabled="savingDecision"
                            >
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

const route = useRoute()
const router = useRouter()
const loading = ref(true)

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

const articleParagraphs = computed(() => {
    const text = naskah.value?.isi || naskah.value?.isi_naskah || ''
    if (!text) {
        return ['Isi naskah belum dilampirkan secara teks langsung. Silakan unduh berkas naskah di bawah untuk membaca naskah selengkapnya.']
    }
    return text.split('\n\n').filter(p => p.trim())
})

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
    const id = route.params.id
    try {
        const res = await api.admin.getNaskahDetail(id)
        naskah.value = res.data.naskah || res.data.data
        if (res.data.review?.catatan) {
            decisionNote.value = res.data.review.catatan
        }
    } catch {
        try {
            const listRes = await api.admin.getNaskahs()
            const all = listRes.data.naskahs || listRes.data.data || []
            naskah.value = all.find(item => String(item.id) === String(id)) || null
        } catch {}
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
        await api.admin.decideNaskah(naskah.value.id, {
            action: selectedDecision.value,
            status: selectedDecision.value,
            catatan: decisionNote.value,
            url_berita: urlBerita.value,
        })
        router.push({ name: 'admin.review' })
    } catch (err) {
        decisionError.value = err.response?.data?.message || 'Gagal menyimpan keputusan editorial.'
    } finally {
        savingDecision.value = false
    }
}

onMounted(() => {
    fetchNaskahDetail()
})
</script>

<style scoped>
.detail-review-page {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.top-nav-bar {
    margin-bottom: 0.25rem;
}

.back-link {
    color: var(--adm-teal);
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.back-link:hover {
    text-decoration: underline;
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
    height: 300px;
    background-color: var(--adm-border-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--adm-text-muted);
    font-size: 1rem;
    font-weight: 500;
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
    color: var(--adm-text-muted);
    line-height: 1.7;
    margin: 0;
}

.article-paragraphs {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    font-size: 0.95rem;
    color: var(--adm-text-main);
    line-height: 1.75;
}

.file-attachment-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
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

@media (max-width: 950px) {
    .review-grid {
        grid-template-columns: 1fr;
    }
}
</style>

