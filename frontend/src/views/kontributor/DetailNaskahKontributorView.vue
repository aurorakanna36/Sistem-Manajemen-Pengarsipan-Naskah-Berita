<template>
    <KontributorLayout>
        <div class="detail-naskah-page">

            <!-- LOADING -->
            <div v-if="loading" class="state-box">
                <div class="spinner"></div>
                <span>Memuat detail naskah...</span>
            </div>

            <!-- DATA TIDAK DITEMUKAN -->
            <div v-else-if="!naskah" class="state-box">
                <p>Data naskah tidak ditemukan.</p>
            </div>

            <!-- DETAIL NASKAH -->
            <div v-else class="detail-grid">

                <!-- KONTEN UTAMA -->
                <article class="naskah-main-card">

                    <!-- SAMPUL -->
                    <div class="sampul-banner">
                        <img v-if="getCoverImageUrl(naskah) && !sampulError"
                            :src="getCoverImageUrl(naskah)"
                            :alt="naskah.judul || 'Sampul naskah'"
                            class="sampul-image"
                            @error="sampulError = true" />
                        <span v-else class="sampul-placeholder">
                            Gambar sampul belum tersedia
                        </span>
                    </div>

                    <div class="naskah-body-wrap">

                        <!-- STATUS DAN KATEGORI -->
                        <div class="badges-wrap">
                            <span class="status-pill" :class="badgeClass(currentStatus)">
                                {{ labelStatus(currentStatus) }}
                            </span>

                            <template v-if="naskah.kategoris?.length">
                                <span v-for="kategori in naskah.kategoris" :key="kategori.id" class="kat-pill">
                                    {{ kategori.nama_kategori }}
                                </span>
                            </template>

                            <span v-else class="kat-pill">
                                {{ naskah.kategori || 'Uncategorized' }}
                            </span>
                        </div>

                        <!-- JUDUL -->
                        <h1 class="naskah-title">
                            {{ naskah.judul || 'Tanpa judul' }}
                        </h1>

                        <p class="naskah-meta">
                            Dikirim {{ formatDate(naskah.waktu_kirim || naskah.created_at) }}
                        </p>

                        <!-- ISI NASKAH -->
                        <section class="section-block">
                            <h2 class="section-heading">Isi naskah</h2>

                            <div v-if="naskah.isi" class="rich-article-content" v-html="naskah.isi"></div>

                            <p v-else class="empty-content">
                                Isi naskah belum tersedia.
                            </p>
                        </section>

                    </div>
                </article>

                <!-- SIDEBAR -->
                <aside class="naskah-side-col">

                    <!-- STATUS DAN CATATAN -->
                    <section class="side-card">
                        <h2 class="side-title">
                            Status & Catatan Redaksi
                        </h2>

                        <div class="side-status-row">
                            <span class="side-label">
                                Status saat ini:
                            </span>

                            <span class="status-pill" :class="badgeClass(currentStatus)">
                                {{ labelStatus(currentStatus) }}
                            </span>
                        </div>

                        <div class="catatan-box">
                            <span class="side-label">
                                Catatan admin / redaksi:
                            </span>

                            <p class="catatan-text">
                                {{ naskah.catatan_redaksi || 'Belum ada catatan dari tim redaksi.' }}
                            </p>
                        </div>

                        <!-- LINK PUBLIKASI -->
                        <div v-if="naskah.publikasi?.url_berita" class="publikasi-box">
                            <span class="side-label">
                                Link Berita Terbit:
                            </span>

                            <a :href="naskah.publikasi.url_berita" target="_blank" rel="noopener noreferrer"
                                class="publikasi-link">
                                {{ naskah.publikasi.url_berita }}
                            </a>
                        </div>
                    </section>

                    <!-- RIWAYAT STATUS -->
                    <section v-if="naskah.riwayat_status?.length" class="side-card">
                        <h2 class="side-title">
                            Riwayat Status
                        </h2>

                        <div class="timeline-list">
                            <div v-for="log in naskah.riwayat_status" :key="log.id" class="timeline-item">
                                <span class="timeline-dot" :class="badgeClass(log.status)"></span>

                                <div class="timeline-info">
                                    <div class="timeline-status">
                                        {{ labelStatus(log.status) }}
                                    </div>

                                    <div class="timeline-note">
                                        {{ log.catatan || '-' }}
                                    </div>

                                    <div class="timeline-date">
                                        {{ formatDate(log.waktu_status) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- AJUKAN REVISI -->
                    <section v-if="['revisi', 'ditolak'].includes(normalizeStatus(currentStatus))" class="side-card">
                        <h2 class="side-title">
                            Revisi Naskah
                        </h2>

                        <router-link :to="`/kontributor/naskah/${naskah.id}/revisi`" class="btn-verify">
                            + Ajukan Revisi
                        </router-link>
                    </section>

                </aside>
            </div>
        </div>
    </KontributorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import KontributorLayout from './KontributorLayout.vue'
import api from '@/api/index.js'
import { getCoverImageUrl } from '@/utils/imageUrl.js'

const route = useRoute()

const loading = ref(true)
const naskah = ref(null)
const sampulError = ref(false)

// STATUS NASKAH
const currentStatus = computed(() => {
    return naskah.value?.status_naskah ||
        naskah.value?.status ||
        ''
})

// AMBIL DETAIL NASKAH
async function fetchNaskah() {
    loading.value = true

    const id = route.params.id

    try {
        const response = await api.kontributor.getNaskahDetail(id)
        naskah.value = response.data?.naskah || null
    } catch (error) {
        console.error('Gagal mengambil detail naskah:', error)
        naskah.value = null
    } finally {
        loading.value = false
    }
}

onMounted(fetchNaskah)

// FORMAT TANGGAL
function formatDate(dateStr) {
    if (!dateStr) return '-'

    const date = new Date(dateStr)

    if (Number.isNaN(date.getTime())) return '-'

    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}

// NORMALISASI STATUS
function normalizeStatus(status) {
    const value = String(status || '').toLowerCase()

    const statusMap = {
        pending: 'review',
        perlu_direview: 'review',

        approved: 'diterima',
        diterima: 'diterima',

        published: 'diterbitkan',
        diterbitkan: 'diterbitkan',

        rejected: 'ditolak',
        ditolak: 'ditolak',

        revision: 'revisi',
        direvisi: 'revisi',
    }

    return statusMap[value] || value
}

// LABEL STATUS
function labelStatus(status) {
    const labels = {
        review: 'Dalam review',
        diterima: 'Diterima',
        diterbitkan: 'Diterbitkan',
        ditolak: 'Ditolak',
        revisi: 'Perlu revisi',
    }

    const normalized = normalizeStatus(status)

    return labels[normalized] || status || 'Tidak diketahui'
}

// CLASS STATUS
function badgeClass(status) {
    const normalized = normalizeStatus(status)

    const classes = {
        diterima: 'pill-diterima',
        diterbitkan: 'pill-diterima',
        ditolak: 'pill-ditolak',
        revisi: 'pill-revisi',
        review: 'pill-review',
    }

    return classes[normalized] || 'pill-review'
}
</script>

<style scoped>
/* =========================
   LAYOUT UTAMA
========================= */
.detail-naskah-page {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.top-back-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.back-link {
    color: var(--ktr-teal);
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
}

.back-link:hover {
    text-decoration: underline;
}

.back-button {
    padding: 0;
    border: none;
    background: none;
    cursor: pointer;
    font-family: inherit;
}

.secondary-link {
    color: var(--ktr-text-muted);
    font-weight: 500;
}

/* =========================
   GRID DETAIL
========================= */
.detail-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 1.75rem;
    align-items: start;
}

.naskah-main-card,
.side-card {
    background-color: var(--ktr-bg-card);
    border: 1px solid var(--ktr-border);
    border-radius: 14px;
}

.naskah-main-card {
    min-width: 0;
    overflow: hidden;
}

/* =========================
   SAMPUL
========================= */
.sampul-banner {
    height: 300px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background-color: var(--ktr-border-soft);
}

.sampul-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sampul-placeholder {
    color: var(--ktr-text-muted);
    font-weight: 500;
}

/* =========================
   KONTEN NASKAH
========================= */
.naskah-body-wrap {
    display: flex;
    flex-direction: column;
    gap: 1.35rem;
    padding: 2rem 2.25rem;
}

.badges-wrap {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.55rem;
}

.status-pill,
.kat-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.34rem 0.85rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.pill-diterima {
    background-color: var(--ktr-success-bg);
    color: var(--ktr-success-text);
}

.pill-review {
    background-color: var(--ktr-info-bg);
    color: var(--ktr-info-text);
}

.pill-revisi {
    background-color: var(--ktr-warning-bg);
    color: var(--ktr-warning-text);
}

.pill-ditolak {
    background-color: var(--ktr-danger-bg);
    color: var(--ktr-danger-text);
}

.kat-pill {
    background-color: #F1F5F9;
    color: var(--ktr-text-muted);
}

.naskah-title {
    margin: 0;
    color: var(--ktr-text-main);
    font-size: 1.8rem;
    font-weight: 800;
    line-height: 1.3;
    overflow-wrap: anywhere;
}

.naskah-meta {
    margin: 0;
    color: var(--ktr-text-muted);
    font-size: 0.9rem;
}

.section-block {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}

.section-heading {
    margin: 0;
    color: var(--ktr-text-muted);
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.rich-article-content {
    color: var(--ktr-text-main);
    font-size: 1rem;
    line-height: 1.75;
    overflow-wrap: anywhere;
}

.empty-content {
    color: var(--ktr-text-muted);
    font-size: 0.9rem;
}

/* FORMAT ISI ARTIKEL */
.rich-article-content :deep(h1) {
    margin: 1.2rem 0 0.6rem;
    font-size: 1.85rem;
    font-weight: 800;
}

.rich-article-content :deep(h2) {
    margin: 1rem 0 0.5rem;
    font-size: 1.45rem;
    font-weight: 700;
}

.rich-article-content :deep(h3) {
    margin: 0.85rem 0 0.4rem;
    font-size: 1.2rem;
    font-weight: 700;
}

.rich-article-content :deep(blockquote) {
    margin: 1rem 0;
    padding-left: 1rem;
    border-left: 4px solid var(--ktr-teal);
    color: var(--ktr-text-muted);
    font-style: italic;
}

.rich-article-content :deep(img) {
    max-width: 100%;
    height: auto;
    margin: 0.75rem 0;
    border-radius: 10px;
}

.rich-article-content :deep(figure) {
    margin: 1.25rem 0;
}

.rich-article-content :deep(figcaption) {
    margin-top: 0.35rem;
    color: var(--ktr-text-muted);
    font-size: 0.85rem;
}

/* =========================
   SIDEBAR
========================= */
.naskah-side-col {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    min-width: 0;
}

.side-card {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.5rem;
}

.side-title {
    margin: 0;
    color: var(--ktr-text-main);
    font-size: 1.05rem;
    font-weight: 700;
}

.side-status-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.side-label {
    color: var(--ktr-text-muted);
    font-size: 0.84rem;
    font-weight: 600;
}

.catatan-box {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding: 0.9rem 1rem;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    background-color: #F8FAFC;
}

.catatan-text {
    margin: 0;
    color: var(--ktr-text-main);
    font-size: 0.9rem;
    line-height: 1.5;
    overflow-wrap: anywhere;
}

.publikasi-box {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.publikasi-link {
    color: var(--ktr-teal);
    font-size: 0.88rem;
    overflow-wrap: anywhere;
}

/* =========================
   RIWAYAT STATUS
========================= */
.timeline-list {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.timeline-dot {
    width: 10px;
    height: 10px;
    margin-top: 0.35rem;
    flex-shrink: 0;
    border-radius: 50%;
}

.timeline-dot.pill-diterima {
    background-color: var(--ktr-success-accent);
}

.timeline-dot.pill-review {
    background-color: var(--ktr-info-accent);
}

.timeline-dot.pill-revisi {
    background-color: var(--ktr-warning-accent);
}

.timeline-dot.pill-ditolak {
    background-color: var(--ktr-danger-accent);
}

.timeline-info {
    min-width: 0;
}

.timeline-status {
    color: var(--ktr-text-main);
    font-size: 0.88rem;
    font-weight: 700;
}

.timeline-note {
    margin-top: 0.15rem;
    color: var(--ktr-text-muted);
    font-size: 0.84rem;
    overflow-wrap: anywhere;
}

.timeline-date {
    margin-top: 0.15rem;
    color: var(--ktr-text-light);
    font-size: 0.76rem;
}

/* =========================
   LOADING DAN EMPTY STATE
========================= */
.state-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.85rem;
    padding: 3.5rem 1.5rem;
    color: var(--ktr-text-muted);
    text-align: center;
}

.spinner {
    width: 30px;
    height: 30px;
    border: 3px solid var(--ktr-border);
    border-top-color: var(--ktr-teal);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

.btn-verify {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 0.8rem 1rem;
    background-color: var(--ktr-teal);
    color: #ffffff;
    border: none;
    border-radius: 9px;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.2s ease, transform 0.2s ease;
}

.btn-verify:hover {
    background-color: #087d78;
    transform: translateY(-1px);
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 980px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }

    .naskah-side-col {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .top-back-row {
        align-items: flex-start;
    }

    .back-link {
        font-size: 0.85rem;
    }

    .detail-grid {
        gap: 1rem;
    }

    .sampul-banner {
        height: 210px;
    }

    .naskah-body-wrap {
        padding: 1.25rem;
        gap: 1rem;
    }

    .naskah-title {
        font-size: 1.4rem;
    }

    .naskah-side-col {
        display: flex;
    }

    .side-card {
        padding: 1.2rem;
    }
}
</style>
