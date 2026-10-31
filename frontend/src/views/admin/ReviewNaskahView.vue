<template>
    <AdminLayout>
        <div class="review-page">
            <div class="page-title-block">
                <h1 class="page-title">Review naskah</h1>
                <p class="page-subtitle">Naskah yang menunggu keputusanmu. Klik naskah untuk langsung membuka detailnya.</p>
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

            <div v-if="loading" class="empty-state">Memuat daftar naskah...</div>
            <div v-else-if="paginatedNaskahs.length === 0" class="empty-state">
                Tidak ada naskah yang menunggu review sesuai pencarian.
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
                            <span class="status-badge-blue">Perlu review</span>
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
const naskahsList = ref([])

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

const filteredNaskahs = computed(() => {
    return naskahsList.value.filter(n => {
        const isReviewStatus = n.status_naskah === 'perlu_direview' || n.status === 'pending'
        if (!isReviewStatus) return false

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

async function fetchNaskahs() {
    loading.value = true
    try {
        const res = await api.admin.getNaskahs()
        naskahsList.value = res.data.naskahs || res.data.data || []
    } catch {
        naskahsList.value = []
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchNaskahs()
})
</script>

<style scoped>
.review-page {
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

.status-badge-blue {
    background-color: var(--adm-info-bg);
    color: var(--adm-info-text);
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
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
