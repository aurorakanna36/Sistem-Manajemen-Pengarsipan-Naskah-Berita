<template>
    <KontributorLayout>
        <div class="detail-naskah-page">
            <div class="top-back-row">
                <router-link to="/kontributor/dashboard" class="back-link">
                    &larr; Kembali ke dashboard
                </router-link>
                <router-link to="/kontributor/naskah-saya" class="back-link secondary-link">
                    Lihat semua naskah saya
                </router-link>
            </div>

            <div v-if="loading" class="state-box">
                <div class="spinner"></div>
                <span>Memuat detail naskah...</span>
            </div>

            <div v-else-if="!naskah" class="state-box">
                <p>Data naskah tidak ditemukan.</p>
            </div>

            <div v-else class="detail-grid">
                <article class="naskah-main-card">
                    <div class="sampul-banner">
                        <img
                            v-if="naskah.gambar_sampul"
                            :src="naskah.gambar_sampul"
                            :alt="naskah.judul"
                            class="sampul-image"
                        />
                        <span v-else class="sampul-placeholder">Gambar sampul</span>
                    </div>

                    <div class="naskah-body-wrap">
                        <div class="badges-wrap">
                            <span class="status-pill" :class="badgeClass(naskah.status_naskah || naskah.status)">
                                {{ labelStatus(naskah.status_naskah || naskah.status) }}
                            </span>
                            <template v-if="naskah.kategoris && naskah.kategoris.length">
                                <span
                                    v-for="kat in naskah.kategoris"
                                    :key="kat.id"
                                    class="kat-pill"
                                >
                                    {{ kat.nama_kategori }}
                                </span>
                            </template>
                            <span v-else class="kat-pill">{{ naskah.kategori || 'Uncategorized' }}</span>
                        </div>

                        <h1 class="naskah-title">{{ naskah.judul }}</h1>

                        <p class="naskah-meta">
                            Dikirim {{ formatDate(naskah.waktu_kirim || naskah.created_at) }}
                            <span v-if="naskah.kata_kunci"> · Kata kunci: <strong>{{ naskah.kata_kunci }}</strong></span>
                        </p>

                        <div v-if="naskah.abstrak" class="section-block abstrak-box">
                            <h2 class="section-heading">Abstrak</h2>
                            <p class="abstrak-text">{{ naskah.abstrak }}</p>
                        </div>

                        <div class="section-block">
                            <h2 class="section-heading">Isi naskah</h2>
                            <div
                                v-if="naskah.isi"
                                class="rich-article-content"
                                v-html="naskah.isi"
                            ></div>
                            <p v-else class="empty-content">Isi naskah belum tersedia.</p>
                        </div>
                    </div>
                </article>

                <aside class="naskah-side-col">
                    <div class="side-card">
                        <h2 class="side-title">Status & Catatan Redaksi</h2>
                        <div class="side-status-row">
                            <span class="side-label">Status saat ini:</span>
                            <span class="status-pill" :class="badgeClass(naskah.status_naskah || naskah.status)">
                                {{ labelStatus(naskah.status_naskah || naskah.status) }}
                            </span>
                        </div>

                        <div class="catatan-box">
                            <span class="side-label">Catatan admin / redaksi:</span>
                            <p class="catatan-text">
                                {{ naskah.catatan_redaksi || 'Belum ada catatan dari tim redaksi.' }}
                            </p>
                        </div>

                        <div v-if="naskah.publikasi?.url_berita" class="publikasi-box">
                            <span class="side-label">Link Berita Terbit:</span>
                            <a
                                :href="naskah.publikasi.url_berita"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="publikasi-link"
                            >
                                {{ naskah.publikasi.url_berita }}
                            </a>
                        </div>
                    </div>

                    <div v-if="naskah.riwayat_status && naskah.riwayat_status.length" class="side-card">
                        <h2 class="side-title">Riwayat Status</h2>
                        <div class="timeline-list">
                            <div
                                v-for="log in naskah.riwayat_status"
                                :key="log.id"
                                class="timeline-item"
                            >
                                <span class="timeline-dot" :class="dotClass(log.status)"></span>
                                <div class="timeline-info">
                                    <div class="timeline-status">{{ labelStatus(log.status) }}</div>
                                    <div class="timeline-note">{{ log.catatan || '-' }}</div>
                                    <div class="timeline-date">{{ formatDate(log.waktu_status) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </KontributorLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import KontributorLayout from './KontributorLayout.vue'
import api from '@/api/index.js'

const route = useRoute()
const loading = ref(true)
const naskah = ref(null)

onMounted(async () => {
    const id = route.params.id
    try {
        const res = await api.kontributor.getNaskahDetail(id)
        naskah.value = res.data?.naskah || null
    } catch {
        try {
            const dash = await api.kontributor.dashboard()
            const list = dash.data?.naskahs || []
            naskah.value = list.find((n) => String(n.id) === String(id)) || null
        } catch {
            naskah.value = null
        }
    } finally {
        loading.value = false
    }
})

function formatDate(dateStr) {
    if (!dateStr) return '-'
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}

function labelStatus(status) {
    const map = {
        pending: 'Dalam review',
        perlu_direview: 'Dalam review',
        approved: 'Diterima',
        diterima: 'Diterima',
        published: 'Diterbitkan',
        diterbitkan: 'Diterbitkan',
        rejected: 'Ditolak',
        ditolak: 'Ditolak',
        revision: 'Perlu revisi',
        direvisi: 'Perlu revisi',
    }
    return map[status] || status
}

function badgeClass(status) {
    if (status === 'diterima' || status === 'diterbitkan' || status === 'approved' || status === 'published') {
        return 'pill-diterima'
    }
    if (status === 'ditolak' || status === 'rejected') {
        return 'pill-ditolak'
    }
    if (status === 'direvisi' || status === 'revision') {
        return 'pill-revisi'
    }
    return 'pill-review'
}

function dotClass(status) {
    if (status === 'diterima' || status === 'diterbitkan') return 'dot-diterima'
    if (status === 'ditolak') return 'dot-ditolak'
    if (status === 'direvisi') return 'dot-revisi'
    return 'dot-review'
}
</script>

<style scoped>
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
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
}

.back-link:hover {
    text-decoration: underline;
}

.secondary-link {
    color: var(--ktr-text-muted);
    font-weight: 500;
}

.detail-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 1.75rem;
    align-items: flex-start;
}

.naskah-main-card {
    background-color: var(--ktr-bg-card);
    border: 1px solid var(--ktr-border);
    border-radius: 14px;
    overflow: hidden;
}

.sampul-banner {
    height: 300px;
    background-color: var(--ktr-border-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
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

.naskah-body-wrap {
    padding: 2rem 2.25rem;
    display: flex;
    flex-direction: column;
    gap: 1.35rem;
}

.badges-wrap {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.55rem;
}

.status-pill {
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
    padding: 0.34rem 0.85rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.naskah-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--ktr-text-main);
    margin: 0;
    line-height: 1.3;
}

.naskah-meta {
    font-size: 0.9rem;
    color: var(--ktr-text-muted);
    margin: 0;
}

.section-block {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}

.abstrak-box {
    background-color: #F8FAFC;
    border-left: 4px solid var(--ktr-teal);
    padding: 1rem 1.25rem;
    border-radius: 0 10px 10px 0;
}

.section-heading {
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--ktr-text-muted);
    margin: 0;
}

.abstrak-text {
    margin: 0;
    font-size: 0.96rem;
    line-height: 1.6;
    color: var(--ktr-text-main);
}

.rich-article-content {
    font-size: 1rem;
    line-height: 1.75;
    color: var(--ktr-text-main);
}

.rich-article-content :deep(h1) {
    font-size: 1.85rem;
    font-weight: 800;
    margin: 1.2rem 0 0.6rem;
}

.rich-article-content :deep(h2) {
    font-size: 1.45rem;
    font-weight: 700;
    margin: 1rem 0 0.5rem;
}

.rich-article-content :deep(h3) {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0.85rem 0 0.4rem;
}

.rich-article-content :deep(blockquote) {
    border-left: 4px solid var(--ktr-teal);
    padding-left: 1rem;
    margin: 1rem 0;
    color: var(--ktr-text-muted);
    font-style: italic;
}

.rich-article-content :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 0.75rem 0;
}

.rich-article-content :deep(figure) {
    margin: 1.25rem 0;
}

.rich-article-content :deep(figcaption) {
    font-size: 0.85rem;
    color: var(--ktr-text-muted);
    margin-top: 0.35rem;
}

.naskah-side-col {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.side-card {
    background-color: var(--ktr-bg-card);
    border: 1px solid var(--ktr-border);
    border-radius: 14px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.side-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--ktr-text-main);
    margin: 0;
}

.side-status-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.side-label {
    font-size: 0.84rem;
    font-weight: 600;
    color: var(--ktr-text-muted);
}

.catatan-box {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    background-color: #F8FAFC;
    padding: 0.9rem 1rem;
    border-radius: 10px;
    border: 1px solid #E2E8F0;
}

.catatan-text {
    margin: 0;
    font-size: 0.9rem;
    color: var(--ktr-text-main);
    line-height: 1.5;
}

.publikasi-link {
    color: var(--ktr-teal);
    font-size: 0.88rem;
    word-break: break-all;
}

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
    border-radius: 50%;
    margin-top: 0.35rem;
    flex-shrink: 0;
}

.dot-diterima {
    background-color: var(--ktr-success-accent);
}
.dot-ditolak {
    background-color: var(--ktr-danger-accent);
}
.dot-revisi {
    background-color: var(--ktr-warning-accent);
}
.dot-review {
    background-color: var(--ktr-info-accent);
}

.timeline-status {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--ktr-text-main);
}

.timeline-note {
    font-size: 0.84rem;
    color: var(--ktr-text-muted);
    margin-top: 0.15rem;
}

.timeline-date {
    font-size: 0.76rem;
    color: var(--ktr-text-light);
    margin-top: 0.15rem;
}

.state-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3.5rem 1.5rem;
    gap: 0.85rem;
    color: var(--ktr-text-muted);
}

.spinner {
    width: 30px;
    height: 30px;
    border: 3px solid var(--ktr-border);
    border-top-color: var(--ktr-teal);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 980px) {
    .detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>
