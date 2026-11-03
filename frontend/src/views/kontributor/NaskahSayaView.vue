<template>
    <KontributorLayout>
        <div class="naskah-saya-page">
            <header class="page-topbar">
                <div class="topbar-titles">
                    <h1 class="page-title">Naskah Saya</h1>
                    <p class="page-subtitle">Daftar seluruh naskah yang pernah kamu kirim.</p>
                </div>

                <div class="user-chip">
                    <div class="user-avatar">{{ userInitial }}</div>
                    <span class="user-name">{{ userName }}</span>
                </div>
            </header>

            <div class="filter-toolbar">
                <div class="filter-pills">
                    <button
                        v-for="tab in filterTabs"
                        :key="tab.value"
                        type="button"
                        class="filter-pill"
                        :class="{ active: activeFilter === tab.value }"
                        @click="activeFilter = tab.value"
                    >
                        {{ tab.label }}
                    </button>
                </div>

                <div class="search-box">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari judul naskah..."
                        class="search-input"
                    />
                </div>
            </div>

            <div class="table-card">
                <div v-if="loading" class="state-box">
                    <div class="spinner"></div>
                    <span>Memuat daftar naskah...</span>
                </div>

                <div v-else-if="filteredNaskahs.length === 0" class="state-box">
                    <p class="empty-text">Tidak ada naskah yang ditemukan.</p>
                    <router-link to="/kontributor/kirim-naskah" class="btn-kirim">
                        + Kirim naskah baru
                    </router-link>
                </div>

                <table v-else class="naskah-table">
                    <thead>
                        <tr>
                            <th>JUDUL NASKAH</th>
                            <th>KATEGORI</th>
                            <th>TANGGAL KIRIM</th>
                            <th>STATUS</th>
                            <th>CATATAN ADMIN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in filteredNaskahs"
                            :key="item.id"
                            class="table-row-clickable"
                            @click="goToDetail(item.id)"
                        >
                            <td class="col-judul">
                                <router-link
                                    :to="`/kontributor/naskah/${item.id}`"
                                    class="judul-link"
                                    @click.stop
                                >
                                    {{ item.judul }}
                                </router-link>
                            </td>
                            <td class="col-kategori">
                                <div class="kategori-badges">
                                    <template v-if="item.kategoris && item.kategoris.length">
                                        <span
                                            v-for="kat in item.kategoris"
                                            :key="kat.id"
                                            class="kat-tag"
                                        >
                                            {{ kat.nama_kategori }}
                                        </span>
                                    </template>
                                    <span v-else class="kat-tag">{{ item.kategori || 'Uncategorized' }}</span>
                                </div>
                            </td>
                            <td class="col-tanggal">{{ formatDate(item.waktu_kirim || item.created_at) }}</td>
                            <td>
                                <span class="status-pill" :class="badgeClass(item.status_naskah || item.status)">
                                    {{ labelStatus(item.status_naskah || item.status) }}
                                </span>
                            </td>
                            <td class="col-catatan">{{ item.catatan_redaksi || '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
const user = userRaw ? JSON.parse(userRaw) : null

const userName = computed(() => user?.nama_lengkap || user?.name || 'Kontributor')
const userInitial = computed(() => userName.value.charAt(0).toUpperCase() || 'K')

const loading = ref(true)
const naskahs = ref([])
const activeFilter = ref('all')
const searchQuery = ref('')

const filterTabs = [
    { label: 'Semua', value: 'all' },
    { label: 'Dalam review', value: 'review' },
    { label: 'Perlu revisi', value: 'revisi' },
    { label: 'Diterima', value: 'diterima' },
    { label: 'Ditolak', value: 'ditolak' },
]

const filteredNaskahs = computed(() => {
    return naskahs.value.filter((n) => {
        const s = n.status_naskah || n.status
        let matchFilter = true
        if (activeFilter.value === 'review') {
            matchFilter = s === 'perlu_direview' || s === 'pending'
        } else if (activeFilter.value === 'revisi') {
            matchFilter = s === 'direvisi' || s === 'revision'
        } else if (activeFilter.value === 'diterima') {
            matchFilter = s === 'diterima' || s === 'diterbitkan' || s === 'approved' || s === 'published'
        } else if (activeFilter.value === 'ditolak') {
            matchFilter = s === 'ditolak' || s === 'rejected'
        }

        const q = searchQuery.value.trim().toLowerCase()
        const matchSearch = !q || (n.judul || '').toLowerCase().includes(q)
        return matchFilter && matchSearch
    })
})

onMounted(async () => {
    try {
        const res = await api.kontributor.getNaskahs()
        naskahs.value = res.data?.naskahs || []
    } catch {
        try {
            const dash = await api.kontributor.dashboard()
            naskahs.value = dash.data?.naskahs || []
        } catch {
            naskahs.value = []
        }
    } finally {
        loading.value = false
    }
})

function goToDetail(id) {
    router.push(`/kontributor/naskah/${id}`)
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
</script>

<style scoped>
.naskah-saya-page {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
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
}

.page-subtitle {
    color: var(--ktr-text-muted);
    font-size: 0.95rem;
    margin: 0.3rem 0 0;
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

.filter-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-pills {
    display: flex;
    align-items: center;
    gap: 0.55rem;
    flex-wrap: wrap;
}

.filter-pill {
    padding: 0.5rem 1.05rem;
    border-radius: 999px;
    border: 1px solid var(--ktr-border);
    background-color: var(--ktr-bg-card);
    color: var(--ktr-text-main);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s;
}

.filter-pill:hover {
    border-color: var(--ktr-teal);
}

.filter-pill.active {
    background-color: var(--ktr-teal);
    border-color: var(--ktr-teal);
    color: var(--ktr-white);
    font-weight: 600;
}

.search-box {
    min-width: 260px;
}

.search-input {
    width: 100%;
    padding: 0.58rem 1rem;
    border: 1px solid var(--ktr-border);
    border-radius: 10px;
    background-color: var(--ktr-bg-card);
    font-size: 0.9rem;
    color: var(--ktr-text-main);
}

.search-input:focus {
    outline: none;
    border-color: var(--ktr-teal);
}

.table-card {
    background-color: var(--ktr-bg-card);
    border: 1px solid var(--ktr-border);
    border-radius: 14px;
    overflow: hidden;
}

.naskah-table {
    width: 100%;
    border-collapse: collapse;
}

.naskah-table th {
    padding: 1rem 1.4rem;
    text-align: left;
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--ktr-text-muted);
    letter-spacing: 0.04em;
    border-bottom: 1px solid var(--ktr-border);
    background-color: #FAFCFF;
}

.naskah-table td {
    padding: 1.15rem 1.4rem;
    font-size: 0.92rem;
    color: var(--ktr-text-main);
    border-bottom: 1px solid #EEF2F6;
    vertical-align: middle;
}

.naskah-table tr:last-child td {
    border-bottom: none;
}

.table-row-clickable {
    cursor: pointer;
    transition: background-color 0.15s;
}

.table-row-clickable:hover td {
    background-color: #F8FAFC;
}

.judul-link {
    font-weight: 700;
    color: var(--ktr-text-main);
    text-decoration: none;
}

.judul-link:hover {
    color: var(--ktr-teal);
    text-decoration: underline;
}

.kategori-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.35rem;
}

.kat-tag {
    display: inline-block;
    font-size: 0.82rem;
    color: var(--ktr-text-muted);
    background-color: #F1F5F9;
    padding: 0.2rem 0.6rem;
    border-radius: 6px;
}

.col-tanggal {
    color: var(--ktr-text-muted);
    white-space: nowrap;
}

.col-catatan {
    color: var(--ktr-text-muted);
    max-width: 280px;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.32rem 0.8rem;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 600;
    white-space: nowrap;
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

.state-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3.5rem 1.5rem;
    gap: 0.9rem;
    color: var(--ktr-text-muted);
}

.btn-kirim {
    background-color: var(--ktr-teal);
    color: var(--ktr-white);
    text-decoration: none;
    padding: 0.65rem 1.2rem;
    border-radius: 9px;
    font-weight: 600;
    font-size: 0.88rem;
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
</style>
