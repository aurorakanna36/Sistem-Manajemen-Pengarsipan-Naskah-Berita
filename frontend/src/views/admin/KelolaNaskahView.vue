<template>
    <AdminLayout>
        <div class="kelola-naskah-page">
            <div class="page-title-block">
                <h1 class="page-title">Kelola naskah</h1>
                <p class="page-subtitle">Semua naskah dari seluruh kontributor.</p>
            </div>

            <div class="filter-pills-row">
                <button 
                    class="pill-btn" 
                    :class="{ active: currentTab === 'all' }"
                    @click="currentTab = 'all'"
                >
                    Semua ({{ countAll }})
                </button>
                <button 
                    class="pill-btn" 
                    :class="{ active: currentTab === 'diterima' }"
                    @click="currentTab = 'diterima'"
                >
                    Diterima ({{ countDiterima }})
                </button>
                <button 
                    class="pill-btn" 
                    :class="{ active: currentTab === 'ditolak' }"
                    @click="currentTab = 'ditolak'"
                >
                    Ditolak ({{ countDitolak }})
                </button>
                <button 
                    class="pill-btn" 
                    :class="{ active: currentTab === 'perlu_direview' }"
                    @click="currentTab = 'perlu_direview'"
                >
                    Perlu review ({{ countPerluReview }})
                </button>
            </div>

            <div class="filter-card">
                <div class="filter-grid">
                    <div class="filter-field">
                        <input 
                            v-model="searchJudul" 
                            type="text" 
                            placeholder="Cari judul naskah"
                            class="filter-input"
                        />
                    </div>
                    <div class="filter-field">
                        <input 
                            v-model="searchKontributor" 
                            type="text" 
                            placeholder="Cari nama kontributor"
                            class="filter-input"
                        />
                    </div>
                    <div class="filter-field">
                        <input 
                            v-model="filterDate" 
                            type="text" 
                            placeholder="Tanggal (contoh: 2026-10)"
                            class="filter-input"
                        />
                    </div>
                </div>
            </div>

            <div v-if="loading" class="empty-state">Memuat semua naskah...</div>
            <div v-else-if="paginatedNaskahs.length === 0" class="empty-state">
                Tidak ada naskah yang cocok dengan filter yang dipilih.
            </div>
            <div v-else class="cards-grid">
                <router-link 
                    v-for="item in paginatedNaskahs" 
                    :key="item.id"
                    :to="`/admin/review-naskah/${item.id}`"
                    class="naskah-card-item"
                >
                    <div class="card-thumb-area">
                        <span>Gambar sampul</span>
                    </div>

                    <div class="card-body-content">
                        <div class="card-meta-top">
                            <span class="status-badge" :class="statusClass(item.status_naskah || item.status)">
                                {{ statusLabel(item.status_naskah || item.status) }}
                            </span>
                            <span class="meta-date">{{ formatDate(item.waktu_kirim || item.created_at) }}</span>
                        </div>

                        <h2 class="card-item-title">{{ item.judul }}</h2>

                        <p class="card-item-excerpt">
                            {{ item.abstrak || item.isi || 'Cuplikan naskah belum tersedia.' }}
                        </p>

                        <div class="card-author-name">
                            {{ item.nama_kontributor || item.penulis?.name || item.author?.name || 'Kontributor' }}
                        </div>
                    </div>
                </router-link>
            </div>

            <div v-if="totalPages > 1" class="pagination-row">
                <button 
                    v-for="p in totalPages" 
                    :key="p"
                    class="page-btn"
                    :class="{ active: currentPage === p }"
                    @click="currentPage = p"
                >
                    {{ p }}
                </button>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminLayout from './AdminLayout.vue'
import api from '@/api/index.js'

const loading = ref(true)
const allNaskahs = ref([])

const currentTab = ref('all')
const searchJudul = ref('')
const searchKontributor = ref('')
const filterDate = ref('')

const currentPage = ref(1)
const perPage = 6

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
        direvisi: 'Direvisi',
        revision: 'Direvisi',
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

const countAll = computed(() => allNaskahs.value.length)
const countDiterima = computed(() => {
    return allNaskahs.value.filter(n => n.status_naskah === 'diterima' || n.status === 'approved' || n.status_naskah === 'diterbitkan').length
})
const countDitolak = computed(() => {
    return allNaskahs.value.filter(n => n.status_naskah === 'ditolak' || n.status === 'rejected').length
})
const countPerluReview = computed(() => {
    return allNaskahs.value.filter(n => n.status_naskah === 'perlu_direview' || n.status === 'pending').length
})

const filteredNaskahs = computed(() => {
    return allNaskahs.value.filter(n => {
        const s = n.status_naskah || n.status

        if (currentTab.value === 'diterima' && !(s === 'diterima' || s === 'approved' || s === 'diterbitkan')) return false
        if (currentTab.value === 'ditolak' && !(s === 'ditolak' || s === 'rejected')) return false
        if (currentTab.value === 'perlu_direview' && !(s === 'perlu_direview' || s === 'pending')) return false

        const matchJudul = !searchJudul.value || n.judul?.toLowerCase().includes(searchJudul.value.toLowerCase())
        const authorName = (n.nama_kontributor || n.penulis?.name || n.author?.name || '').toLowerCase()
        const matchKontributor = !searchKontributor.value || authorName.includes(searchKontributor.value.toLowerCase())
        const dateStr = n.waktu_kirim || n.created_at || ''
        const matchDate = !filterDate.value || dateStr.includes(filterDate.value)

        return matchJudul && matchKontributor && matchDate
    })
})

const totalPages = computed(() => {
    return Math.ceil(filteredNaskahs.value.length / perPage) || 1
})

const paginatedNaskahs = computed(() => {
    const start = (currentPage.value - 1) * perPage
    return filteredNaskahs.value.slice(start, start + perPage)
})

async function fetchAllNaskahs() {
    loading.value = true
    try {
        const res = await api.admin.getNaskahs()
        allNaskahs.value = res.data.naskahs || res.data.data || []
    } catch {
        allNaskahs.value = []
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchAllNaskahs()
})
</script>

<style scoped>
.kelola-naskah-page {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.page-title-block {
    margin-bottom: 0.25rem;
}

.page-title {
    font-size: 1.85rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0 0 0.35rem 0;
}

.page-subtitle {
    font-size: 0.95rem;
    color: var(--adm-text-muted);
    margin: 0;
}

.filter-pills-row {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.pill-btn {
    padding: 0.6rem 1.25rem;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 600;
    background: #ffffff;
    border: 1px solid var(--adm-border);
    color: var(--adm-text-main);
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}

.pill-btn:hover {
    background: var(--adm-bg-page);
}

.pill-btn.active {
    background: var(--adm-teal);
    color: #ffffff;
    border-color: var(--adm-teal);
}

.filter-card {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
}

.filter-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 1rem;
}

.filter-input {
    width: 100%;
    padding: 0.65rem 0.95rem;
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    font-size: 0.875rem;
    color: var(--adm-text-main);
    outline: none;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.filter-input:focus {
    border-color: var(--adm-teal);
}

.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.naskah-card-item {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    overflow: hidden;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.2s, transform 0.2s;
}

.naskah-card-item:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.card-thumb-area {
    height: 160px;
    background-color: var(--adm-border-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--adm-text-muted);
    font-size: 0.9rem;
    font-weight: 500;
}

.card-body-content {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.card-meta-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.8rem;
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

.meta-date {
    font-size: 0.8rem;
    color: var(--adm-text-muted);
}

.card-item-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0 0 0.5rem 0;
    line-height: 1.35;
}

.card-item-excerpt {
    font-size: 0.85rem;
    color: var(--adm-text-muted);
    line-height: 1.5;
    margin: 0 0 0.75rem 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
}

.card-author-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--adm-teal);
    margin-top: auto;
}

.pagination-row {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.page-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid var(--adm-border);
    background: #ffffff;
    color: var(--adm-text-main);
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, color 0.2s;
}

.page-btn.active {
    background: var(--adm-teal);
    color: #ffffff;
    border-color: var(--adm-teal);
}

.empty-state {
    padding: 4rem 0;
    text-align: center;
    color: var(--adm-text-muted);
    background: var(--adm-bg-card);
    border-radius: 14px;
    border: 1px solid var(--adm-border);
}

@media (max-width: 950px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
    .cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>

