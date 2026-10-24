<template>
    <div class="admin-page">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">HC</div>
                <span>Harian Cakrawala</span>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section-label">Admin Panel</div>
                <router-link to="/admin/review-naskah" class="nav-item" active-class="active">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Review Naskah
                </router-link>
                <router-link to="/admin/users" class="nav-item" active-class="active">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Manajemen User
                </router-link>
                <router-link to="/" class="nav-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    Beranda
                </router-link>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">A</div>
                    <div class="user-detail">
                        <div class="user-name">{{ user?.name || 'Admin' }}</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
                <button class="btn-logout" @click="handleLogout">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </div>
        </aside>

        <main class="main-content">
            <header class="page-header">
                <div>
                    <h1>Review Naskah</h1>
                    <p>Tinjau dan tentukan keputusan editorial untuk setiap naskah yang masuk.</p>
                </div>
            </header>

            <div class="tab-bar">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    class="tab-btn"
                    :class="{ active: activeTab === tab.key }"
                    @click="activeTab = tab.key"
                >
                    {{ tab.label }}
                    <span class="tab-count" v-if="countByStatus[tab.key] !== undefined">
                        {{ countByStatus[tab.key] }}
                    </span>
                </button>
            </div>

            <div v-if="loading" class="loading-state">
                <div class="spinner-lg"></div>
                <span>Memuat naskah...</span>
            </div>

            <div v-else-if="filteredNaskahs.length === 0" class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <h3>Tidak Ada Naskah</h3>
                <p>Belum ada naskah dengan status ini.</p>
            </div>

            <div v-else class="naskah-list">
                <div v-for="naskah in filteredNaskahs" :key="naskah.id" class="naskah-card">
                    <div class="card-header">
                        <div class="card-meta">
                            <span class="badge" :class="'badge-' + naskah.status">{{ labelStatus(naskah.status) }}</span>
                            <span class="card-date">{{ formatDate(naskah.created_at) }}</span>
                        </div>
                        <h3 class="card-title">{{ naskah.judul }}</h3>
                        <div class="card-author">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            {{ naskah.author?.name || naskah.nama_pengirim || 'Anonim' }}
                            <span v-if="naskah.author?.instansi" class="author-instansi">· {{ naskah.author.instansi }}</span>
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="card-excerpt">{{ truncate(naskah.isi, 200) }}</p>
                    </div>

                    <div v-if="naskah.catatan_redaksi" class="card-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Catatan: {{ naskah.catatan_redaksi }}</span>
                    </div>

                    <div class="card-actions" v-if="naskah.status === 'pending'">
                        <button class="action-btn approve" @click="openDecideModal(naskah, 'approved')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            Setujui
                        </button>
                        <button class="action-btn revise" @click="openDecideModal(naskah, 'revision')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                            Minta Revisi
                        </button>
                        <button class="action-btn reject" @click="openDecideModal(naskah, 'rejected')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Tolak
                        </button>
                    </div>
                    <div class="card-actions" v-else-if="naskah.status === 'approved'">
                        <button class="action-btn publish" @click="openDecideModal(naskah, 'published')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
                            Terbitkan
                        </button>
                        <button class="action-btn reject" @click="openDecideModal(naskah, 'rejected')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Tolak
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <div v-if="decideModal.show" class="modal-overlay" @click.self="decideModal.show = false">
            <div class="modal">
                <div class="modal-header">
                    <h2>Konfirmasi Keputusan</h2>
                    <button class="modal-close" @click="decideModal.show = false">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="decision-badge" :class="'decision-' + decideModal.action">
                        {{ labelAction(decideModal.action) }} Naskah
                    </div>
                    <p class="modal-naskah-title">{{ decideModal.naskah?.judul }}</p>
                    <div class="form-group">
                        <label for="catatan">Catatan Redaksi</label>
                        <textarea id="catatan" v-model="decideModal.catatan" rows="4" :placeholder="catatanPlaceholder"></textarea>
                    </div>
                    <div v-if="decideModal.error" class="alert-error">{{ decideModal.error }}</div>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" @click="decideModal.show = false">Batal</button>
                    <button class="btn-decide" :class="'btn-' + decideModal.action" @click="submitDecision" :disabled="decideModal.loading">
                        <span v-if="decideModal.loading" class="spinner-sm"></span>
                        <span v-else>{{ labelAction(decideModal.action) }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/index.js'

const router = useRouter()
const userRaw = localStorage.getItem('user_info')
const user = userRaw ? JSON.parse(userRaw) : null

const naskahs = ref([])
const loading = ref(true)
const activeTab = ref('pending')

const tabs = [
    { key: 'pending', label: 'Menunggu Review' },
    { key: 'approved', label: 'Disetujui' },
    { key: 'revision', label: 'Perlu Revisi' },
    { key: 'published', label: 'Terbit' },
    { key: 'rejected', label: 'Ditolak' },
]

const countByStatus = computed(() => {
    return naskahs.value.reduce((acc, n) => {
        acc[n.status] = (acc[n.status] || 0) + 1
        return acc
    }, {})
})

const filteredNaskahs = computed(() =>
    naskahs.value.filter(n => n.status === activeTab.value)
)

const decideModal = ref({
    show: false,
    naskah: null,
    action: '',
    catatan: '',
    loading: false,
    error: '',
})

const catatanPlaceholder = computed(() => {
    const map = {
        approved: 'Opsional — catatan tambahan untuk kontributor.',
        revision: 'Jelaskan bagian yang perlu direvisi...',
        rejected: 'Opsional — alasan penolakan naskah.',
        published: 'Opsional — catatan penerbitan.',
    }
    return map[decideModal.value.action] || ''
})

onMounted(async () => {
    try {
        const res = await api.admin.getNaskahs()
        naskahs.value = res.data.naskahs || res.data.data || []
    } catch {
        naskahs.value = []
    } finally {
        loading.value = false
    }
})

function openDecideModal(naskah, action) {
    decideModal.value = { show: true, naskah, action, catatan: '', loading: false, error: '' }
}

async function submitDecision() {
    decideModal.value.loading = true
    decideModal.value.error = ''
    try {
        await api.admin.decideNaskah(decideModal.value.naskah.id, {
            status: decideModal.value.action,
            action: decideModal.value.action,
            catatan_redaksi: decideModal.value.catatan,
            alasan_penolakan: decideModal.value.catatan,
        })
        const idx = naskahs.value.findIndex(n => n.id === decideModal.value.naskah.id)
        if (idx !== -1) {
            naskahs.value[idx].status = decideModal.value.action
            naskahs.value[idx].catatan_redaksi = decideModal.value.catatan
        }
        decideModal.value.show = false
    } catch (err) {
        decideModal.value.error = err.response?.data?.message || 'Terjadi kesalahan.'
    } finally {
        decideModal.value.loading = false
    }
}

async function handleLogout() {
    try { await api.auth.logout() } catch {}
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_info')
    router.push({ name: 'login' })
}

function formatDate(d) {
    if (!d) return '-'
    return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function labelStatus(status) {
    const map = { pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak', revision: 'Perlu Revisi', published: 'Terbit' }
    return map[status] || status
}

function labelAction(action) {
    const map = { approved: 'Setujui', rejected: 'Tolak', revision: 'Minta Revisi', published: 'Terbitkan' }
    return map[action] || action
}

function truncate(text, len) {
    if (!text) return ''
    return text.length > len ? text.substring(0, len) + '...' : text
}
</script>

<style scoped>
.admin-page {
    display: flex;
    min-height: 100vh;
    background: #f8fafc;
    font-family: 'Inter', sans-serif;
}

.sidebar {
    width: 260px;
    background: #0f172a;
    display: flex;
    flex-direction: column;
    padding: 1.5rem 0;
    position: fixed;
    top: 0; left: 0;
    height: 100vh;
    z-index: 50;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0 1.5rem 1.5rem;
    font-weight: 700;
    font-size: 1rem;
    color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    margin-bottom: 1rem;
}

.brand-logo {
    width: 36px; height: 36px;
    background: #0f4c81;
    color: #fff;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 0.85rem; flex-shrink: 0;
}

.sidebar-nav {
    flex: 1;
    padding: 0 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.nav-section-label {
    font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: rgba(255,255,255,0.3);
    padding: 0 0.75rem; margin-bottom: 0.5rem;
}

.nav-item {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.6rem 0.75rem;
    border-radius: 8px;
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    font-size: 0.875rem; font-weight: 500;
    transition: background 0.15s, color 0.15s;
}

.nav-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
.nav-item.active { background: rgba(15,76,129,0.5); color: #fff; }

.sidebar-footer {
    padding: 1rem 1.5rem 0;
    border-top: 1px solid rgba(255,255,255,0.08);
}

.user-info {
    display: flex; align-items: center; gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.user-avatar {
    width: 36px; height: 36px;
    background: #1e3a5f;
    color: #93c5fd;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.85rem; flex-shrink: 0;
}

.user-name { font-size: 0.85rem; font-weight: 600; color: #fff; }
.user-role { font-size: 0.75rem; color: rgba(255,255,255,0.4); }

.btn-logout {
    width: 100%;
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    background: none; color: rgba(255,255,255,0.5);
    font-size: 0.8rem; font-weight: 500;
    cursor: pointer; transition: border-color 0.15s, color 0.15s;
}

.btn-logout:hover { border-color: rgba(239,68,68,0.4); color: #fca5a5; }

.main-content {
    margin-left: 260px;
    flex: 1;
    padding: 2rem 2.5rem;
}

.page-header {
    margin-bottom: 1.75rem;
}

.page-header h1 {
    font-size: 1.75rem; font-weight: 800;
    color: #0f172a; letter-spacing: -0.02em;
}

.page-header p { color: #64748b; font-size: 0.9rem; margin-top: 0.25rem; }

.tab-bar {
    display: flex;
    gap: 0.25rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.5rem;
    margin-bottom: 1.5rem;
    width: fit-content;
    flex-wrap: wrap;
}

.tab-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}

.tab-btn:hover { background: #f1f5f9; }
.tab-btn.active { background: #0f4c81; color: #fff; }

.tab-count {
    background: rgba(0,0,0,0.1);
    border-radius: 999px;
    padding: 0.1rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    min-width: 20px;
    text-align: center;
}

.tab-btn.active .tab-count { background: rgba(255,255,255,0.2); }

.naskah-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.naskah-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    transition: box-shadow 0.2s;
}

.naskah-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); }

.card-header { margin-bottom: 1rem; }

.card-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.card-date { font-size: 0.8rem; color: #94a3b8; }

.card-title {
    font-size: 1.1rem; font-weight: 700;
    color: #0f172a; margin-bottom: 0.5rem;
    line-height: 1.4;
}

.card-author {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.8rem;
    color: #64748b;
}

.author-instansi { color: #94a3b8; }

.card-body { margin-bottom: 1rem; }

.card-excerpt {
    font-size: 0.875rem;
    color: #475569;
    line-height: 1.7;
}

.card-note {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    background: #fefce8;
    border: 1px solid #fef08a;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.8rem;
    color: #854d0e;
    margin-bottom: 1rem;
    flex-shrink: 0;
}

.card-actions {
    display: flex;
    gap: 0.75rem;
    padding-top: 1rem;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    border: 1.5px solid;
    transition: all 0.15s;
}

.action-btn.approve { border-color: #16a34a; color: #16a34a; background: #f0fdf4; }
.action-btn.approve:hover { background: #16a34a; color: #fff; }

.action-btn.revise { border-color: #ea580c; color: #ea580c; background: #fff7ed; }
.action-btn.revise:hover { background: #ea580c; color: #fff; }

.action-btn.reject { border-color: #dc2626; color: #dc2626; background: #fef2f2; }
.action-btn.reject:hover { background: #dc2626; color: #fff; }

.action-btn.publish { border-color: #2563eb; color: #2563eb; background: #eff6ff; }
.action-btn.publish:hover { background: #2563eb; color: #fff; }

.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.6rem;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 600;
}

.badge-pending { background: #fef9c3; color: #854d0e; }
.badge-approved { background: #dcfce7; color: #15803d; }
.badge-published { background: #dbeafe; color: #1d4ed8; }
.badge-rejected { background: #fee2e2; color: #991b1b; }
.badge-revision { background: #fed7aa; color: #9a3412; }

.loading-state, .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #64748b;
}

.loading-state { display: flex; flex-direction: column; align-items: center; gap: 1rem; }
.empty-state svg { opacity: 0.3; margin: 0 auto 1rem; display: block; }
.empty-state h3 { font-size: 1.1rem; font-weight: 700; color: #374151; margin-bottom: 0.5rem; }

.spinner-lg {
    width: 36px; height: 36px;
    border: 3px solid #e2e8f0;
    border-top-color: #0f4c81;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

.spinner-sm {
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    display: inline-block;
}

@keyframes spin { to { transform: rotate(360deg); } }

.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex; align-items: center; justify-content: center;
    z-index: 200;
    backdrop-filter: blur(4px);
}

.modal {
    background: #fff; border-radius: 16px;
    width: 520px; max-width: 95vw;
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.modal-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem;
}

.modal-header h2 { font-size: 1.2rem; font-weight: 700; color: #0f172a; }

.modal-close {
    width: 32px; height: 32px;
    border: none; background: #f1f5f9;
    border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: #64748b;
}

.modal-close:hover { background: #e2e8f0; }

.modal-body { margin-bottom: 1.5rem; }

.decision-badge {
    display: inline-block;
    padding: 0.35rem 1rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.decision-approved { background: #dcfce7; color: #15803d; }
.decision-rejected { background: #fee2e2; color: #991b1b; }
.decision-revision { background: #fed7aa; color: #9a3412; }
.decision-published { background: #dbeafe; color: #1d4ed8; }

.modal-naskah-title {
    font-size: 0.95rem; font-weight: 600;
    color: #0f172a; margin-bottom: 1rem;
    line-height: 1.4;
}

.form-group { margin-bottom: 1rem; }

.form-group label {
    display: block;
    font-size: 0.85rem; font-weight: 600;
    color: #374151; margin-bottom: 0.5rem;
}

.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.875rem; color: #0f172a;
    font-family: inherit; resize: vertical;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group textarea:focus {
    outline: none; border-color: #0f4c81;
    box-shadow: 0 0 0 3px rgba(15,76,129,0.1);
}

.alert-error {
    background: #fef2f2; border: 1px solid #fecaca;
    color: #dc2626; border-radius: 10px;
    padding: 0.75rem 1rem; font-size: 0.85rem;
    font-weight: 500; margin-top: 0.75rem;
}

.modal-footer {
    display: flex; gap: 0.75rem; justify-content: flex-end;
    padding-top: 1.5rem;
    border-top: 1px solid #f1f5f9;
}

.btn-cancel {
    padding: 0.65rem 1.25rem;
    background: #f1f5f9; color: #475569;
    border: none; border-radius: 10px;
    font-size: 0.875rem; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
}

.btn-cancel:hover { background: #e2e8f0; }

.btn-decide {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.65rem 1.5rem;
    border: none; border-radius: 10px;
    font-size: 0.875rem; font-weight: 600;
    cursor: pointer; transition: filter 0.15s;
    color: #fff;
}

.btn-decide:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-decide.btn-approved { background: #16a34a; }
.btn-decide.btn-rejected { background: #dc2626; }
.btn-decide.btn-revision { background: #ea580c; }
.btn-decide.btn-published { background: #2563eb; }

@media (max-width: 768px) {
    .sidebar { display: none; }
    .main-content { margin-left: 0; padding: 1.25rem; }
    .tab-bar { width: 100%; }
}
</style>
