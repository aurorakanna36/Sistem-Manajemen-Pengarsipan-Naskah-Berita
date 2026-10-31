<template>
    <AdminLayout>
        <div class="detail-kontributor-page">
            <div class="top-nav-bar">
                <router-link to="/admin/kontributor" class="back-link">
                    &larr; Kembali ke kelola kontributor
                </router-link>
            </div>

            <div v-if="loading" class="empty-state">Memuat profil kontributor...</div>
            <div v-else-if="!kontributor" class="empty-state">Data kontributor tidak ditemukan.</div>
            <div v-else class="content-body">
                <div class="profile-card">
                    <div class="profile-avatar">
                        {{ (kontributor.nama_lengkap || kontributor.name || 'K').charAt(0).toUpperCase() }}
                    </div>
                    <div class="profile-details">
                        <div class="name-badge-row">
                            <h1 class="profile-name">{{ kontributor.nama_lengkap || kontributor.name }}</h1>
                            <span class="status-badge" :class="statusClass(kontributor.status_verifikasi)">
                                {{ statusLabel(kontributor.status_verifikasi) }}
                            </span>
                        </div>
                        <p class="profile-meta">
                            {{ kontributor.email }} · {{ kontributor.instansi || 'Independen' }}
                        </p>
                        <p class="profile-joined">
                            Bergabung {{ formatDate(kontributor.created_at) }}
                            <span v-if="kontributor.nomor_whatsapp || kontributor.phone_number">
                                · WhatsApp: {{ kontributor.nomor_whatsapp || kontributor.phone_number }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="metrics-grid">
                    <div class="metric-card">
                        <span class="metric-title">Naskah dikirim</span>
                        <div class="metric-num">{{ stats.total_dikirim }}</div>
                    </div>
                    <div class="metric-card">
                        <span class="metric-title">Diterima</span>
                        <div class="metric-num num-diterima">{{ stats.diterima }}</div>
                    </div>
                    <div class="metric-card">
                        <span class="metric-title">Ditolak</span>
                        <div class="metric-num num-ditolak">{{ stats.ditolak }}</div>
                    </div>
                    <div class="metric-card">
                        <span class="metric-title">Direvisi</span>
                        <div class="metric-num num-direvisi">{{ stats.direvisi }}</div>
                    </div>
                </div>

                <div class="charts-row">
                    <div class="chart-panel">
                        <h2 class="chart-header">Persentase naskah</h2>
                        
                        <div class="donut-wrap">
                            <svg class="donut-svg" viewBox="0 0 160 160">
                                <circle 
                                    cx="80" 
                                    cy="80" 
                                    r="58" 
                                    fill="transparent" 
                                    stroke="var(--adm-border)" 
                                    stroke-width="18"
                                />
                                <circle 
                                    v-if="diterimaStroke.length > 0"
                                    cx="80" 
                                    cy="80" 
                                    r="58" 
                                    fill="transparent" 
                                    stroke="#0F6B63" 
                                    stroke-width="18"
                                    :stroke-dasharray="diterimaStroke"
                                    :stroke-dashoffset="diterimaOffset"
                                />
                                <circle 
                                    v-if="ditolakStroke.length > 0"
                                    cx="80" 
                                    cy="80" 
                                    r="58" 
                                    fill="transparent" 
                                    stroke="#EF4444" 
                                    stroke-width="18"
                                    :stroke-dasharray="ditolakStroke"
                                    :stroke-dashoffset="ditolakOffset"
                                />
                                <circle 
                                    v-if="direvisiStroke.length > 0"
                                    cx="80" 
                                    cy="80" 
                                    r="58" 
                                    fill="transparent" 
                                    stroke="#F59E0B" 
                                    stroke-width="18"
                                    :stroke-dasharray="direvisiStroke"
                                    :stroke-dashoffset="direvisiOffset"
                                />
                            </svg>
                            <div class="donut-center-text">
                                <span class="donut-total">{{ stats.total_dikirim }}</span>
                                <span class="donut-label">Total naskah</span>
                            </div>
                        </div>

                        <div class="donut-legend">
                            <div class="legend-row">
                                <span class="legend-dot dot-diterima"></span>
                                <span class="legend-name">Diterima</span>
                                <span class="legend-pct">{{ diterimaPct }}%</span>
                            </div>
                            <div class="legend-row">
                                <span class="legend-dot dot-ditolak"></span>
                                <span class="legend-name">Ditolak</span>
                                <span class="legend-pct">{{ ditolakPct }}%</span>
                            </div>
                            <div class="legend-row">
                                <span class="legend-dot dot-direvisi"></span>
                                <span class="legend-name">Direvisi</span>
                                <span class="legend-pct">{{ direvisiPct }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="chart-panel">
                        <h2 class="chart-header">Naskah dikirim per bulan</h2>
                        
                        <div class="barchart-container">
                            <div 
                                v-for="(col, idx) in monthlyData" 
                                :key="idx" 
                                class="bar-column"
                            >
                                <span class="bar-val">{{ col.count }}</span>
                                <div class="bar-track">
                                    <div 
                                        class="bar-fill" 
                                        :style="{ height: barHeight(col.count) }"
                                    ></div>
                                </div>
                                <span class="bar-month">{{ col.month }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="author-naskah-section">
                    <h2 class="section-heading">Naskah oleh {{ kontributor.nama_lengkap || kontributor.name }}</h2>

                    <div v-if="kontributorNaskahs.length === 0" class="empty-state">
                        Kontributor belum mengirimkan naskah.
                    </div>
                    <div v-else class="naskah-cards-grid">
                        <router-link 
                            v-for="item in kontributorNaskahs" 
                            :key="item.id"
                            :to="`/admin/review-naskah/${item.id}`"
                            class="naskah-card-link"
                        >
                            <div class="naskah-thumbnail">
                                <span>Gambar sampul</span>
                            </div>
                            <div class="naskah-card-content">
                                <div class="badge-date-row">
                                    <span class="status-badge" :class="statusClass(item.status_naskah || item.status)">
                                        {{ statusLabel(item.status_naskah || item.status) }}
                                    </span>
                                    <span class="card-date">{{ formatDate(item.waktu_kirim || item.created_at) }}</span>
                                </div>
                                <h3 class="naskah-card-title">{{ item.judul }}</h3>
                                <p class="naskah-card-snippet">
                                    {{ item.abstrak || item.isi || 'Cuplikan naskah belum tersedia.' }}
                                </p>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import AdminLayout from './AdminLayout.vue'
import api from '@/api/index.js'

const route = useRoute()
const loading = ref(true)

const kontributor = ref(null)
const stats = ref({
    total_dikirim: 0,
    diterima: 0,
    ditolak: 0,
    direvisi: 0,
})

const monthlyData = ref([
    { month: 'Mei', count: 1 },
    { month: 'Jun', count: 2 },
    { month: 'Jul', count: 3 },
    { month: 'Agu', count: 4 },
    { month: 'Sep', count: 3 },
    { month: 'Okt', count: 5 },
])

const kontributorNaskahs = ref([])

const circumference = 2 * Math.PI * 58 // ~364.42

const diterimaPct = computed(() => {
    if (!stats.value.total_dikirim) return 0
    return Math.round((stats.value.diterima / stats.value.total_dikirim) * 100)
})

const ditolakPct = computed(() => {
    if (!stats.value.total_dikirim) return 0
    return Math.round((stats.value.ditolak / stats.value.total_dikirim) * 100)
})

const direvisiPct = computed(() => {
    if (!stats.value.total_dikirim) return 0
    return Math.round((stats.value.direvisi / stats.value.total_dikirim) * 100)
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

const direvisiStroke = computed(() => {
    const len = (direvisiPct.value / 100) * circumference
    return `${len} ${circumference}`
})
const direvisiOffset = computed(() => {
    return -(((diterimaPct.value + ditolakPct.value) / 100) * circumference)
})

function barHeight(val) {
    const maxVal = Math.max(...monthlyData.value.map(d => d.count), 5)
    const pct = (val / maxVal) * 100
    return `${Math.max(pct, 8)}%`
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
        verified: 'Terverifikasi',
        approved: 'Diterima',
        diterima: 'Diterima',
        rejected: 'Ditolak',
        ditolak: 'Ditolak',
        revision: 'Direvisi',
        direvisi: 'Direvisi',
        pending: 'Menunggu',
        perlu_direview: 'Perlu review',
        diterbitkan: 'Diterbitkan'
    }
    return map[status] || status
}

function statusClass(status) {
    if (status === 'verified' || status === 'approved' || status === 'diterima' || status === 'diterbitkan') {
        return 'badge-success'
    }
    if (status === 'rejected' || status === 'ditolak') {
        return 'badge-danger'
    }
    if (status === 'revision' || status === 'direvisi') {
        return 'badge-warning'
    }
    if (status === 'pending' || status === 'menunggu') {
        return 'badge-warning'
    }
    return 'badge-info'
}

async function fetchKontributorDetail() {
    loading.value = true
    const id = route.params.id
    try {
        const res = await api.admin.getKontributorDetail(id)
        kontributor.value = res.data.kontributor || res.data.user
        stats.value = res.data.stats || stats.value
        if (res.data.naskah_per_bulan) {
            monthlyData.value = res.data.naskah_per_bulan
        }
        kontributorNaskahs.value = res.data.naskahs || []
    } catch {
        try {
            const [usersRes, naskahsRes] = await Promise.all([
                api.admin.getUsers(),
                api.admin.getNaskahs()
            ])
            const users = usersRes.data.users || usersRes.data.data || []
            const found = users.find(u => String(u.id) === String(id))
            kontributor.value = found || null
            
            const allNaskahs = naskahsRes.data.naskahs || naskahsRes.data.data || []
            const userNaskahs = allNaskahs.filter(n => String(n.id_kontributor || n.author?.id) === String(id))
            kontributorNaskahs.value = userNaskahs
            
            const accDiterima = userNaskahs.filter(n => n.status_naskah === 'diterima' || n.status === 'approved' || n.status_naskah === 'diterbitkan').length
            const accDitolak = userNaskahs.filter(n => n.status_naskah === 'ditolak' || n.status === 'rejected').length
            const accDirevisi = userNaskahs.filter(n => n.status_naskah === 'direvisi' || n.status === 'revision').length
            
            stats.value = {
                total_dikirim: userNaskahs.length,
                diterima: accDiterima,
                ditolak: accDitolak,
                direvisi: accDirevisi
            }
        } catch {}
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchKontributorDetail()
})
</script>

<style scoped>
.detail-kontributor-page {
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

.content-body {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.profile-card {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    padding: 1.75rem 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.profile-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background-color: var(--adm-border-dark);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    flex-shrink: 0;
}

.name-badge-row {
    display: flex;
    align-items: center;
    gap: 0.85rem;
}

.profile-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0;
}

.profile-meta {
    font-size: 0.95rem;
    color: var(--adm-text-muted);
    margin: 0.35rem 0 0.2rem;
}

.profile-joined {
    font-size: 0.85rem;
    color: var(--adm-text-muted);
    margin: 0;
}

.metrics-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
}

.metric-card {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    display: flex;
    flex-direction: column;
}

.metric-title {
    font-size: 0.9rem;
    color: var(--adm-text-main);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.metric-num {
    font-size: 2.25rem;
    font-weight: 700;
    line-height: 1.1;
    color: var(--adm-text-main);
}

.num-diterima { color: var(--adm-teal); }
.num-ditolak { color: var(--adm-danger-text); }
.num-direvisi { color: var(--adm-warning-text); }

.charts-row {
    display: grid;
    grid-template-columns: 1fr 1.35fr;
    gap: 1.5rem;
}

.chart-panel {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
}

.chart-header {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0 0 1.25rem 0;
}

.donut-wrap {
    position: relative;
    width: 170px;
    height: 170px;
    margin: 0.5rem auto 1.5rem;
}

.donut-svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.donut-center-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    display: flex;
    flex-direction: column;
}

.donut-total {
    font-size: 1.85rem;
    font-weight: 700;
    color: var(--adm-text-main);
    line-height: 1;
}

.donut-label {
    font-size: 0.75rem;
    color: var(--adm-text-muted);
    margin-top: 0.2rem;
}

.donut-legend {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
    border-top: 1px solid var(--adm-border);
    padding-top: 1rem;
}

.legend-row {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    font-size: 0.85rem;
}

.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.dot-diterima { background-color: var(--adm-teal); }
.dot-ditolak { background-color: #EF4444; }
.dot-direvisi { background-color: #F59E0B; }

.legend-name {
    flex: 1;
    color: var(--adm-text-main);
    font-weight: 500;
}

.legend-pct {
    font-weight: 700;
    color: var(--adm-text-main);
}

.barchart-container {
    flex: 1;
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    padding-top: 1.5rem;
    min-height: 220px;
    gap: 0.75rem;
}

.bar-column {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    height: 100%;
    flex: 1;
}

.bar-val {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--adm-text-main);
}

.bar-track {
    width: 38px;
    flex: 1;
    display: flex;
    align-items: flex-end;
    background: transparent;
}

.bar-fill {
    width: 100%;
    background-color: var(--adm-teal);
    border-radius: 6px 6px 0 0;
    transition: height 0.3s;
}

.bar-month {
    font-size: 0.8rem;
    color: var(--adm-text-muted);
    font-weight: 500;
}

.author-naskah-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.section-heading {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0.5rem 0 0.5rem 0;
}

.naskah-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.naskah-card-link {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    overflow: hidden;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.2s, transform 0.2s;
}

.naskah-card-link:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.naskah-thumbnail {
    height: 160px;
    background-color: var(--adm-border-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--adm-text-muted);
    font-size: 0.9rem;
    font-weight: 500;
}

.naskah-card-content {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.badge-date-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.card-date {
    font-size: 0.8rem;
    color: var(--adm-text-muted);
}

.naskah-card-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0 0 0.5rem 0;
    line-height: 1.35;
}

.naskah-card-snippet {
    font-size: 0.85rem;
    color: var(--adm-text-muted);
    line-height: 1.5;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.badge-success {
    background-color: var(--adm-success-bg);
    color: var(--adm-success-text);
}

.badge-danger {
    background-color: var(--adm-danger-bg);
    color: var(--adm-danger-text);
}

.badge-warning {
    background-color: var(--adm-warning-bg);
    color: var(--adm-warning-text);
}

.badge-info,
.badge-pending {
    background-color: var(--adm-info-bg);
    color: var(--adm-info-text);
}

.empty-state {
    padding: 3rem 0;
    text-align: center;
    color: var(--adm-text-muted);
}

@media (max-width: 950px) {
    .metrics-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .charts-row {
        grid-template-columns: 1fr;
    }
    .naskah-cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>

