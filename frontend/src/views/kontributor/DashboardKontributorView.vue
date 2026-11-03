<template>
    <KontributorLayout :unreadCountOverride="unreadCount">
        <div class="dashboard-kontributor">
            <header class="page-topbar">
                <div class="topbar-titles">
                    <h1 class="page-title">Dashboard</h1>
                    <p class="page-subtitle">Selamat datang, {{ userName }}</p>
                </div>

                <div class="topbar-actions">
                    <router-link to="/kontributor/kirim-naskah" class="btn-kirim-baru">
                        + Kirim naskah baru
                    </router-link>
                    <div class="user-chip">
                        <div class="user-avatar">{{ userInitial }}</div>
                        <span class="user-name">{{ userName }}</span>
                    </div>
                </div>
            </header>

            <section class="stats-five-grid">
                <div class="stat-box">
                    <span class="stat-label">Dikirim</span>
                    <div class="stat-number num-dikirim">{{ stats.dikirim }}</div>
                </div>
                <div class="stat-box">
                    <span class="stat-label">Dalam review</span>
                    <div class="stat-number num-review">{{ stats.dalam_review }}</div>
                </div>
                <div class="stat-box">
                    <span class="stat-label">Direvisi</span>
                    <div class="stat-number num-direvisi">{{ stats.direvisi }}</div>
                </div>
                <div class="stat-box">
                    <span class="stat-label">Diterima</span>
                    <div class="stat-number num-diterima">{{ stats.diterima }}</div>
                </div>
                <div class="stat-box">
                    <span class="stat-label">Ditolak</span>
                    <div class="stat-number num-ditolak">{{ stats.ditolak }}</div>
                </div>
            </section>

            <section class="dashboard-bottom-grid">
                <div class="panel-card activity-card">
                    <div class="panel-header">
                        <h2 class="panel-title">Aktivitas terbaru</h2>
                    </div>

                    <div v-if="loadingData" class="state-box">
                        <div class="spinner"></div>
                        <span>Memuat aktivitas naskah...</span>
                    </div>

                    <div v-else-if="naskahs.length === 0" class="state-box">
                        <p class="empty-text">Belum ada naskah yang dikirim.</p>
                        <router-link to="/kontributor/kirim-naskah" class="btn-kirim-baru">
                            + Kirim naskah pertama
                        </router-link>
                    </div>

                    <div v-else class="activity-list">
                        <div
                            v-for="naskah in recentNaskahs"
                            :key="naskah.id"
                            class="activity-row"
                            @click="goToDetail(naskah.id)"
                        >
                            <span class="activity-dot" :class="dotClass(naskah.status_naskah || naskah.status)"></span>
                            <div class="activity-info">
                                <router-link
                                    :to="`/kontributor/naskah/${naskah.id}`"
                                    class="activity-title"
                                    @click.stop
                                >
                                    {{ naskah.judul }}
                                </router-link>
                                <div class="activity-meta">
                                    {{ formatKategori(naskah) }} · {{ formatDate(naskah.waktu_kirim || naskah.created_at) }}
                                </div>
                            </div>
                            <span class="status-pill" :class="badgeClass(naskah.status_naskah || naskah.status)">
                                {{ labelStatus(naskah.status_naskah || naskah.status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="panel-card chart-card">
                    <div class="panel-header">
                        <h2 class="panel-title">Persentase naskah</h2>
                    </div>

                    <div class="donut-container">
                        <div class="donut-wrap">
                            <svg class="donut-svg" viewBox="0 0 180 180">
                                <circle
                                    cx="90"
                                    cy="90"
                                    r="64"
                                    fill="transparent"
                                    stroke="var(--ktr-border)"
                                    stroke-width="22"
                                />
                                <circle
                                    v-if="diterimaPct > 0"
                                    cx="90"
                                    cy="90"
                                    r="64"
                                    fill="transparent"
                                    stroke="#0F6B63"
                                    stroke-width="22"
                                    :stroke-dasharray="diterimaStroke"
                                    :stroke-dashoffset="diterimaOffset"
                                />
                                <circle
                                    v-if="ditolakPct > 0"
                                    cx="90"
                                    cy="90"
                                    r="64"
                                    fill="transparent"
                                    stroke="#EF4444"
                                    stroke-width="22"
                                    :stroke-dasharray="ditolakStroke"
                                    :stroke-dashoffset="ditolakOffset"
                                />
                                <circle
                                    v-if="dalamProsesPct > 0"
                                    cx="90"
                                    cy="90"
                                    r="64"
                                    fill="transparent"
                                    stroke="#F59E0B"
                                    stroke-width="22"
                                    :stroke-dasharray="dalamProsesStroke"
                                    :stroke-dashoffset="dalamProsesOffset"
                                />
                            </svg>
                            <div class="donut-center">
                                <span class="donut-total-num">{{ stats.dikirim }}</span>
                                <span class="donut-total-label">Total naskah</span>
                            </div>
                        </div>

                        <div class="donut-legend">
                            <div class="legend-item">
                                <div class="legend-left">
                                    <span class="legend-dot legend-diterima"></span>
                                    <span class="legend-label">Diterima</span>
                                </div>
                                <span class="legend-value">{{ diterimaPct }}%</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-left">
                                    <span class="legend-dot legend-ditolak"></span>
                                    <span class="legend-label">Ditolak</span>
                                </div>
                                <span class="legend-value">{{ ditolakPct }}%</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-left">
                                    <span class="legend-dot legend-proses"></span>
                                    <span class="legend-label">Dalam proses</span>
                                </div>
                                <span class="legend-value">{{ dalamProsesPct }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </KontributorLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import KontributorLayout from './KontributorLayout.vue'
import api from '@/api/index.js'

const router = useRouter()
const userRaw = localStorage.getItem('user_info')
const user = ref(userRaw ? JSON.parse(userRaw) : null)

const userName = computed(() => user.value?.nama_lengkap || user.value?.name || 'Kontributor')
const userInitial = computed(() => userName.value.charAt(0).toUpperCase() || 'K')

const naskahs = ref([])
const loadingData = ref(true)
const unreadCount = ref(null)

const stats = ref({
    dikirim: 0,
    dalam_review: 0,
    direvisi: 0,
    diterima: 0,
    ditolak: 0,
    dalam_proses: 0,
})

const recentNaskahs = computed(() => naskahs.value.slice(0, 8))

const circumference = 2 * Math.PI * 64 // ~402.12

const diterimaPct = computed(() => {
    const total = stats.value.dikirim
    if (!total) return 0
    return Math.round((stats.value.diterima / total) * 100)
})

const ditolakPct = computed(() => {
    const total = stats.value.dikirim
    if (!total) return 0
    return Math.round((stats.value.ditolak / total) * 100)
})

const dalamProsesPct = computed(() => {
    const total = stats.value.dikirim
    if (!total) return 0
    const prosesCount = stats.value.dalam_review + stats.value.direvisi
    if (prosesCount === 0) return 0
    const remainder = 100 - diterimaPct.value - ditolakPct.value
    return Math.max(0, remainder)
})

const diterimaStroke = computed(() => {
    const len = (diterimaPct.value / 100) * circumference
    return `${len} ${circumference}`
})
const diterimaOffset = computed(() => 0)

const ditolakStroke = computed(() => {
    const len = (ditolakPct.value / 100) * circumference
    return `${len} ${circumference}`
})
const ditolakOffset = computed(() => {
    return -((diterimaPct.value / 100) * circumference)
})

const dalamProsesStroke = computed(() => {
    const len = (dalamProsesPct.value / 100) * circumference
    return `${len} ${circumference}`
})
const dalamProsesOffset = computed(() => {
    return -(((diterimaPct.value + ditolakPct.value) / 100) * circumference)
})

onMounted(async () => {
    try {
        const res = await api.kontributor.dashboard()
        if (res.data?.user) {
            user.value = { ...user.value, ...res.data.user }
        }
        const list = res.data?.naskahs || []
        naskahs.value = list

        const s = res.data?.stats || {}
        const dikirim = s.dikirim ?? s.total ?? list.length
        const dalamReview = s.dalam_review ?? s.draft ?? 0
        const direvisi = s.direvisi ?? s.revisi ?? 0
        const diterima = s.diterima ?? s.disetujui ?? 0
        const ditolak = s.ditolak ?? 0

        stats.value = {
            dikirim,
            dalam_review: dalamReview,
            direvisi,
            diterima,
            ditolak,
            dalam_proses: dalamReview + direvisi,
        }

        if (typeof res.data?.unread_notifications_count === 'number') {
            unreadCount.value = res.data.unread_notifications_count
            window.dispatchEvent(
                new CustomEvent('kontributor-unread-updated', { detail: unreadCount.value })
            )
        }
    } catch {
        naskahs.value = []
    } finally {
        loadingData.value = false
    }
})

function goToDetail(id) {
    router.push(`/kontributor/naskah/${id}`)
}

function formatKategori(naskah) {
    if (Array.isArray(naskah.kategoris) && naskah.kategoris.length > 0) {
        return naskah.kategoris.map((k) => k.nama_kategori).join(', ')
    }
    return naskah.kategori || 'Uncategorized'
}

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
        published: 'Diterima',
        diterbitkan: 'Diterima',
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
    if (status === 'diterima' || status === 'diterbitkan' || status === 'approved' || status === 'published') {
        return 'dot-diterima'
    }
    if (status === 'ditolak' || status === 'rejected') {
        return 'dot-ditolak'
    }
    if (status === 'direvisi' || status === 'revision') {
        return 'dot-revisi'
    }
    return 'dot-review'
}
</script>

<style scoped>
.dashboard-kontributor {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
}

.page-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.page-title {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--ktr-text-main);
    margin: 0;
    letter-spacing: -0.02em;
}

.page-subtitle {
    color: var(--ktr-text-muted);
    font-size: 0.95rem;
    margin: 0.3rem 0 0;
}

.topbar-actions {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.btn-kirim-baru {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background-color: var(--ktr-teal);
    color: var(--ktr-white);
    font-size: 0.9rem;
    font-weight: 600;
    padding: 0.7rem 1.25rem;
    border-radius: 10px;
    text-decoration: none;
    transition: background-color 0.2s, transform 0.15s;
}

.btn-kirim-baru:hover {
    background-color: var(--ktr-teal-hover);
}

.user-chip {
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

.user-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background-color: var(--ktr-teal);
    color: var(--ktr-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
}

.user-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--ktr-text-main);
}

.stats-five-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1.15rem;
}

.stat-box {
    background-color: var(--ktr-bg-card);
    border: 1px solid var(--ktr-border);
    border-radius: 14px;
    padding: 1.35rem 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 0.65rem;
}

.stat-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--ktr-text-muted);
}

.stat-number {
    font-size: 2.1rem;
    font-weight: 800;
    line-height: 1.1;
}

.num-dikirim {
    color: var(--ktr-text-main);
}

.num-review {
    color: #2563EB;
}

.num-direvisi {
    color: #D97706;
}

.num-diterima {
    color: #15803D;
}

.num-ditolak {
    color: #DC2626;
}

.dashboard-bottom-grid {
    display: grid;
    grid-template-columns: 1.45fr 1fr;
    gap: 1.5rem;
    align-items: stretch;
}

.panel-card {
    background-color: var(--ktr-bg-card);
    border: 1px solid var(--ktr-border);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    display: flex;
    flex-direction: column;
}

.panel-header {
    margin-bottom: 1.1rem;
}

.panel-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--ktr-text-main);
    margin: 0;
}

.activity-list {
    display: flex;
    flex-direction: column;
}

.activity-row {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    padding: 1rem 0.4rem;
    border-bottom: 1px solid #EEF2F6;
    cursor: pointer;
    border-radius: 8px;
    transition: background-color 0.15s;
}

.activity-row:last-child {
    border-bottom: none;
}

.activity-row:hover {
    background-color: #F8FAFC;
}

.activity-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}

.dot-diterima {
    background-color: var(--ktr-success-accent);
}

.dot-review {
    background-color: var(--ktr-info-accent);
}

.dot-revisi {
    background-color: var(--ktr-warning-accent);
}

.dot-ditolak {
    background-color: var(--ktr-danger-accent);
}

.activity-info {
    flex: 1;
    min-width: 0;
}

.activity-title {
    display: inline-block;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--ktr-text-main);
    text-decoration: none;
    line-height: 1.35;
    margin-bottom: 0.2rem;
}

.activity-title:hover {
    color: var(--ktr-teal);
    text-decoration: underline;
}

.activity-meta {
    font-size: 0.82rem;
    color: var(--ktr-text-muted);
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.32rem 0.8rem;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 600;
    white-space: nowrap;
    flex-shrink: 0;
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

.donut-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
    gap: 1.5rem;
    padding-top: 0.25rem;
}

.donut-wrap {
    position: relative;
    width: 185px;
    height: 185px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.donut-svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.donut-center {
    position: absolute;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.donut-total-num {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--ktr-text-main);
    line-height: 1.1;
}

.donut-total-label {
    font-size: 0.8rem;
    color: var(--ktr-text-muted);
    margin-top: 0.15rem;
}

.donut-legend {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
}

.legend-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.9rem;
}

.legend-left {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.legend-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    flex-shrink: 0;
}

.legend-diterima {
    background-color: #0F6B63;
}

.legend-ditolak {
    background-color: #EF4444;
}

.legend-proses {
    background-color: #F59E0B;
}

.legend-label {
    color: var(--ktr-text-muted);
    font-weight: 500;
}

.legend-value {
    color: var(--ktr-text-main);
    font-weight: 700;
}

.state-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem 1.5rem;
    gap: 0.85rem;
    color: var(--ktr-text-muted);
    text-align: center;
}

.empty-text {
    margin: 0;
    font-size: 0.95rem;
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

@media (max-width: 1100px) {
    .stats-five-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    .dashboard-bottom-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .stats-five-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
