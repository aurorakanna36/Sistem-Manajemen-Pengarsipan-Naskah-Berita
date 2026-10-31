<template>
    <AdminLayout>
        <div class="dashboard-container">
            <div class="page-title-block">
                <h1 class="page-title">Dashboard admin</h1>
                <p class="page-subtitle">Ringkasan naskah dan kontributor.</p>
            </div>

            <div class="summary-cards-grid">
                <div class="summary-card">
                    <span class="card-label">Total naskah</span>
                    <div class="card-metric">{{ stats.total_naskah }}</div>
                    <span class="card-desc">Semua naskah dari seluruh kontributor</span>
                </div>

                <div class="summary-card">
                    <span class="card-label">Total kontributor</span>
                    <div class="card-metric">{{ stats.total_kontributor }}</div>
                    <span class="card-desc">Kontributor yang sudah mendaftar</span>
                </div>

                <div class="summary-card card-warning">
                    <span class="card-label label-warning">Perlu direview</span>
                    <div class="card-metric metric-warning">{{ stats.perlu_direview }}</div>
                    <span class="card-desc desc-warning">Naskah menunggu keputusan admin</span>
                </div>
            </div>

            <div class="lists-grid">
                <div class="panel-card panel-left">
                    <h2 class="panel-title">Naskah menunggu review</h2>
                    
                    <div v-if="loading" class="empty-state">Memuat data naskah...</div>
                    <div v-else-if="naskahMenunggu.length === 0" class="empty-state">
                        Tidak ada naskah yang menunggu review saat ini.
                    </div>
                    <div v-else class="items-list">
                        <div 
                            v-for="item in naskahMenunggu" 
                            :key="item.id" 
                            class="item-row"
                        >
                            <div class="item-info">
                                <h3 class="item-title">{{ item.judul }}</h3>
                                <p class="item-meta">
                                    {{ item.nama_kontributor || item.penulis?.name || 'Kontributor' }} · Dikirim {{ formatDate(item.waktu_kirim || item.created_at) }}
                                </p>
                            </div>
                            <router-link 
                                :to="`/admin/review-naskah/${item.id}`" 
                                class="action-link"
                            >
                                Buka
                            </router-link>
                        </div>
                    </div>
                </div>

                <div class="panel-card panel-right">
                    <h2 class="panel-title">Kontributor perlu diverifikasi</h2>

                    <div v-if="loading" class="empty-state">Memuat data kontributor...</div>
                    <div v-else-if="kontributorPending.length === 0" class="empty-state">
                        Semua kontributor telah diverifikasi.
                    </div>
                    <div v-else class="items-list">
                        <div 
                            v-for="kontri in kontributorPending" 
                            :key="kontri.id" 
                            class="item-row kontri-row"
                        >
                            <div class="kontri-avatar">
                                {{ (kontri.nama_lengkap || kontri.name || 'K').charAt(0).toUpperCase() }}
                            </div>
                            <div class="item-info">
                                <h3 class="item-title">{{ kontri.nama_lengkap || kontri.name }}</h3>
                                <p class="item-meta">Mendaftar {{ formatDate(kontri.created_at) }}</p>
                            </div>
                            <router-link 
                                :to="`/admin/kontributor/${kontri.id}`" 
                                class="action-link"
                            >
                                Verifikasi
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from './AdminLayout.vue'
import api from '@/api/index.js'

const loading = ref(true)
const stats = ref({
    total_naskah: 0,
    total_kontributor: 0,
    perlu_direview: 0,
})

const naskahMenunggu = ref([])
const kontributorPending = ref([])

function formatDate(dateStr) {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
}

async function fetchDashboard() {
    loading.value = true
    try {
        const res = await api.admin.getDashboard()
        stats.value = res.data.stats || stats.value
        naskahMenunggu.value = res.data.naskah_menunggu_review || []
        kontributorPending.value = res.data.kontributor_perlu_verifikasi || []
    } catch {
        try {
            const [naskahRes, userRes] = await Promise.all([
                api.admin.getNaskahs(),
                api.admin.getUsers({ status: 'pending' })
            ])
            const allNaskahs = naskahRes.data.naskahs || naskahRes.data.data || []
            const pendingNaskahs = allNaskahs.filter(n => n.status_naskah === 'perlu_direview' || n.status === 'pending')
            
            stats.value = {
                total_naskah: allNaskahs.length,
                total_kontributor: userRes.data.meta?.total || userRes.data.stats?.total || 0,
                perlu_direview: pendingNaskahs.length
            }
            naskahMenunggu.value = pendingNaskahs.slice(0, 5)
            kontributorPending.value = userRes.data.users || userRes.data.data || []
        } catch {}
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchDashboard()
})
</script>

<style scoped>
.dashboard-container {
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
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

.summary-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.summary-card {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    display: flex;
    flex-direction: column;
}

.card-label {
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--adm-text-main);
    margin-bottom: 0.5rem;
}

.card-metric {
    font-size: 2.75rem;
    font-weight: 700;
    line-height: 1.1;
    color: var(--adm-text-main);
    margin-bottom: 0.75rem;
}

.card-desc {
    font-size: 0.85rem;
    color: var(--adm-text-muted);
}

.card-warning {
    background: var(--adm-warning-bg);
    border-color: var(--adm-warning-border);
}

.label-warning {
    color: var(--adm-warning-text);
}

.metric-warning {
    color: var(--adm-warning-text);
}

.desc-warning {
    color: var(--adm-warning-text);
}

.lists-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 1.5rem;
}

.panel-card {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    padding: 1.75rem;
}

.panel-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0 0 1.25rem 0;
}

.items-list {
    display: flex;
    flex-direction: column;
}

.item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 0;
    border-bottom: 1px solid var(--adm-border);
    gap: 1rem;
}

.item-row:last-child {
    border-bottom: none;
    padding-bottom: 0.25rem;
}

.item-info {
    flex: 1;
    min-width: 0;
}

.item-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--adm-text-main);
    margin: 0 0 0.35rem 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.item-meta {
    font-size: 0.825rem;
    color: var(--adm-text-muted);
    margin: 0;
}

.action-link {
    color: var(--adm-teal);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    padding: 0.35rem 0.65rem;
    border-radius: 6px;
    transition: background 0.2s;
    flex-shrink: 0;
}

.action-link:hover {
    background-color: var(--adm-teal-light);
}

.kontri-row {
    gap: 0.85rem;
}

.kontri-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: var(--adm-border-dark);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.empty-state {
    padding: 2rem 0;
    text-align: center;
    color: var(--adm-text-muted);
    font-size: 0.9rem;
}

@media (max-width: 950px) {
    .summary-cards-grid {
        grid-template-columns: 1fr;
    }
    .lists-grid {
        grid-template-columns: 1fr;
    }
}
</style>

