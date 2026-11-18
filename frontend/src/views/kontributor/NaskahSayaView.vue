<template>
    <KontributorLayout>
        <div class="naskah-saya-page">

            <!-- JUDUL HALAMAN -->
            <div class="page-title-block">
                <h1 class="page-title">Naskah Saya</h1>
                <p class="page-subtitle">
                    Daftar seluruh naskah yang pernah kamu kirim.
                </p>
            </div>

            <!-- FILTER STATUS -->
            <div class="filter-pills-row">

                <button v-for="tab in filterTabs" :key="tab.value" class="pill-btn"
                    :class="{ active: currentTab === tab.value }" @click="currentTab = tab.value">
                    {{ tab.label }} ({{ tab.count() }})
                </button>

            </div>

            <!-- FILTER PENCARIAN -->
            <div class="filter-card">
                <div class="filter-grid">

                    <div class="filter-field">
                        <input v-model="searchJudul" type="text" placeholder="Cari judul naskah" class="filter-input" />
                    </div>

                    <div class="filter-field date-field">

                        <span v-if="!filterDate" class="date-placeholder">
                            Tanggal
                        </span>

                        <input v-model="filterDate" type="month" class="filter-input date-input"
                            :class="{ 'date-empty': !filterDate }" />

                    </div>

                </div>
            </div>

            <!-- LOADING -->
            <div v-if="loading" class="empty-state">
                Memuat daftar naskah...
            </div>

            <!-- DATA KOSONG -->
            <div v-else-if="paginatedNaskahs.length === 0" class="empty-state">
                <p>Tidak ada naskah yang cocok dengan filter yang dipilih.</p>

                <router-link to="/kontributor/kirim-naskah" class="btn-kirim">
                    + Kirim naskah baru
                </router-link>
            </div>

            <!-- GRID CARD -->
            <div v-else class="cards-grid">

                <router-link v-for="(item, index) in paginatedNaskahs" :key="item.id"
                    :to="`/kontributor/naskah/${item.id}`" class="naskah-card-item">

                    <!-- GAMBAR SAMPUL -->
                    <div class="card-thumb-area">

                        <span class="cover-placeholder">
                            Gambar sampul
                        </span>

                        <img v-if="coverUrl(item)" :src="coverUrl(item)" :alt="'Sampul ' + item.judul"
                            class="cover-image" @error="handleImageError" />

                    </div>

                    <!-- ISI CARD -->
                    <div class="card-body-content">

                        <div class="card-meta-top">

                            <span class="status-badge" :class="statusClass(item.status_naskah || item.status)">
                                {{ statusLabel(item.status_naskah || item.status) }}
                            </span>

                            <span class="meta-date">
                                {{ formatDate(item.waktu_kirim || item.created_at) }}
                            </span>

                        </div>

                        <h2 class="card-item-title">
                            {{ item.judul }}
                        </h2>

                        <p class="card-item-excerpt">
                            {{ getExcerpt(item) }}
                        </p>

                    </div>

                </router-link>

            </div>

            <!-- PAGINATION -->
            <div v-if="totalPages > 1" class="pagination-row">

                <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">
                    ‹
                </button>

                <button v-for="p in visiblePages" :key="p" class="page-btn" :class="{ active: currentPage === p }"
                    @click="currentPage = p">
                    {{ p }}
                </button>

                <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">
                    ›
                </button>

            </div>

        </div>
    </KontributorLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import KontributorLayout from './KontributorLayout.vue'
import api from '@/api/index.js'
import { getCoverImageUrl } from '@/utils/imageUrl.js'

// STATE
const loading = ref(true)
const naskahs = ref([])

const currentTab = ref('all')
const searchJudul = ref('')
const filterDate = ref('')

const currentPage = ref(1)
const perPage = 6

// NORMALISASI STATUS
function normalizeStatus(status) {
    const map = {
        pending: 'review',
        perlu_direview: 'review',

        approved: 'diterima',
        diterima: 'diterima',
        published: 'diterima',
        diterbitkan: 'diterima',

        rejected: 'ditolak',
        ditolak: 'ditolak',

        revision: 'direvisi',
        direvisi: 'direvisi',
    }

    return map[status] || 'review'
}

// FILTER TABS
const filterTabs = [
    {
        label: 'Semua',
        value: 'all',
        count: () => naskahs.value.length
    },
    {
        label: 'Dalam review',
        value: 'review',
        count: () => countStatus('review')
    },
    {
        label: 'Diterima',
        value: 'diterima',
        count: () => countStatus('diterima')
    },
    {
        label: 'Ditolak',
        value: 'ditolak',
        count: () => countStatus('ditolak')
    },
    {
        label: 'Direvisi',
        value: 'direvisi',
        count: () => countStatus('direvisi')
    },
]

// HITUNG JUMLAH BERDASARKAN STATUS
function countStatus(status) {
    return naskahs.value.filter(item => {
        return normalizeStatus(
            item.status_naskah || item.status
        ) === status
    }).length
}

// FILTER NASKAH
const filteredNaskahs = computed(() => {

    return naskahs.value.filter(item => {

        const status = normalizeStatus(
            item.status_naskah || item.status
        )

        // FILTER STATUS
        if (
            currentTab.value !== 'all' &&
            status !== currentTab.value
        ) {
            return false
        }

        // FILTER JUDUL
        const judul = (item.judul || '').toLowerCase()

        const matchJudul = judul.includes(
            searchJudul.value.trim().toLowerCase()
        )

        // FILTER TANGGAL
        const dateStr = (
            item.waktu_kirim ||
            item.created_at ||
            ''
        ).toString()

        const matchDate =
            !filterDate.value ||
            dateStr.startsWith(filterDate.value)

        return matchJudul && matchDate
    })

})

// TOTAL HALAMAN
const totalPages = computed(() => {
    return Math.ceil(filteredNaskahs.value.length / perPage) || 1
})

// DATA PER HALAMAN
const paginatedNaskahs = computed(() => {

    const start = (currentPage.value - 1) * perPage

    return filteredNaskahs.value.slice(
        start,
        start + perPage
    )

})

// NOMOR HALAMAN
const visiblePages = computed(() => {

    const pages = []

    for (
        let i = Math.max(1, currentPage.value - 2);
        i <= Math.min(totalPages.value, currentPage.value + 2);
        i++
    ) {
        pages.push(i)
    }

    return pages
})

// RESET PAGINATION KETIKA FILTER BERUBAH
watch(
    [currentTab, searchJudul, filterDate],
    () => {
        currentPage.value = 1
    }
)

// AMBIL DATA NASKAH
async function fetchNaskahs() {

    loading.value = true

    try {

        const res = await api.kontributor.getNaskahs()

        naskahs.value = res.data?.naskahs || []

    } catch (error) {

        console.error('Gagal mengambil naskah:', error)

        try {

            const dash = await api.kontributor.dashboard()

            naskahs.value = dash.data?.naskahs || []

        } catch (fallbackError) {

            console.error('Gagal mengambil dashboard:', fallbackError)

            naskahs.value = []

        }

    } finally {

        loading.value = false

    }

}

onMounted(() => {
    fetchNaskahs()
})

// URL GAMBAR SAMPUL
function coverUrl(item) {
    return getCoverImageUrl(item) || null
}

// JIKA GAMBAR TIDAK DITEMUKAN
function handleImageError(event) {
    event.target.style.display = 'none'
}

// DESKRIPSI NASKAH
function getExcerpt(item) {

    const text =
        item.sinopsis ||
        item.ringkasan ||
        item.deskripsi ||
        item.deskripsi_singkat ||
        ''

    if (!text) {
        return 'Belum ada deskripsi singkat untuk naskah ini.'
    }

    return text.length > 100
        ? text.substring(0, 100) + '...'
        : text
}

// FORMAT TANGGAL
function formatDate(dateStr) {

    if (!dateStr) return '-'

    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    })

}

// LABEL STATUS
function statusLabel(status) {

    const map = {
        review: 'Dalam review',
        diterima: 'Diterima',
        ditolak: 'Ditolak',
        direvisi: 'Direvisi'
    }

    return map[normalizeStatus(status)]
}

// CLASS STATUS
function statusClass(status) {

    const normalized = normalizeStatus(status)

    if (normalized === 'diterima') return 'badge-success'
    if (normalized === 'ditolak') return 'badge-danger'
    if (normalized === 'direvisi') return 'badge-warning'

    return 'badge-info'
}
</script>

<style scoped>
@import '@/assets/admin-theme.css';

/* HALAMAN */
.naskah-saya-page {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    width: 100%;
    min-width: 0;
}

/* JUDUL */
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

/* FILTER STATUS */
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

/* FILTER CARD */
.filter-card {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
}

.filter-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.filter-field {
    min-width: 0;
}

.filter-input {
    width: 100%;
    padding: 0.65rem 0.95rem;
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    font-size: 0.875rem;
    color: var(--adm-text-main);
    background: #ffffff;
    outline: none;
    transition: border-color 0.2s;
    box-sizing: border-box;
}

.filter-input:focus {
    border-color: var(--adm-teal);
}

.date-field {
    position: relative;
    width: 100%;
}

.date-input {
    width: 100%;
    height: 42px;
    cursor: pointer;
    background: #ffffff;
    box-sizing: border-box;
}

.date-input.date-empty {
    color: transparent;
}

.date-input.date-empty::-webkit-datetime-edit {
    color: transparent;
}

.date-placeholder {
    position: absolute;
    left: 0.95rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--adm-text-muted);
    font-size: 0.875rem;
    pointer-events: none;
    z-index: 2;
}

/* GRID CARD */
.cards-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1.5rem;
}

/* CARD */
.naskah-card-item {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    overflow: hidden;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    min-width: 0;
    transition: box-shadow 0.2s, transform 0.2s;
}

.naskah-card-item:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

/* GAMBAR SAMPUL */
.card-thumb-area {
    height: 160px;
    position: relative;
    overflow: hidden;
    background-color: var(--adm-border-soft, #DDE5EC);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--adm-text-muted);
    font-size: 0.9rem;
    font-weight: 500;
}

.cover-placeholder {
    position: absolute;
    z-index: 0;
}

.cover-image {
    position: relative;
    z-index: 1;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* ISI CARD */
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
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
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
    white-space: nowrap;
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
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 2.5em;
}

/* PAGINATION */
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

.page-btn:hover:not(:disabled) {
    border-color: var(--adm-teal);
    color: var(--adm-teal);
}

.page-btn.active {
    background: var(--adm-teal);
    color: #ffffff;
    border-color: var(--adm-teal);
}

.page-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

/* EMPTY STATE */
.empty-state {
    padding: 4rem 1rem;
    text-align: center;
    color: var(--adm-text-muted);
    background: var(--adm-bg-card);
    border-radius: 14px;
    border: 1px solid var(--adm-border);
}

.btn-kirim {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.65rem 1.2rem;
    border-radius: 8px;
    background: var(--adm-teal);
    color: #ffffff;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
}

/* RESPONSIVE */
@media (max-width: 950px) {

    .cards-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

}

@media (max-width: 600px) {

    .naskah-saya-page {
        gap: 1rem;
    }

    .page-title {
        font-size: 1.5rem;
    }

    .filter-pills-row {
        gap: 0.5rem;
    }

    .pill-btn {
        padding: 0.5rem 0.85rem;
        font-size: 0.78rem;
    }

    .filter-card {
        padding: 1rem;
    }

    .filter-grid {
        grid-template-columns: 1fr;
    }

    .cards-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .card-thumb-area {
        height: 160px;
    }

    .card-body-content {
        padding: 1rem;
    }

}
</style>
