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
                    <h1>Manajemen User</h1>
                    <p>Kelola akun kontributor — verifikasi pendaftar baru atau batalkan akses kontributor.</p>
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
                    <span class="tab-count">{{ countByStatus[tab.key] || 0 }}</span>
                </button>
            </div>

            <div v-if="loading" class="loading-state">
                <div class="spinner-lg"></div>
                <span>Memuat data user...</span>
            </div>

            <div v-else-if="filteredUsers.length === 0" class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
                <h3>Tidak Ada Kontributor</h3>
                <p>Belum ada kontributor dengan status ini.</p>
            </div>

            <div v-else class="user-table-wrapper">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>Kontributor</th>
                            <th>Instansi</th>
                            <th>Telepon</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            <th>Berkas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="u in filteredUsers" :key="u.id">
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-sm">{{ u.name?.charAt(0)?.toUpperCase() }}</div>
                                    <div>
                                        <div class="user-full-name">{{ u.name }}</div>
                                        <div class="user-email">{{ u.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ u.instansi || '-' }}</td>
                            <td>{{ u.phone_number || '-' }}</td>
                            <td class="col-date">{{ formatDate(u.created_at) }}</td>
                            <td>
                                <span class="badge" :class="'badge-' + u.status_verifikasi">
                                    {{ labelStatus(u.status_verifikasi) }}
                                </span>
                            </td>
                            <td>
                                <a v-if="u.surat_administrasi" :href="fileUrl(u.surat_administrasi)" target="_blank" class="btn-file">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    Lihat
                                </a>
                                <span v-else class="no-file">Tidak ada</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button
                                        v-if="u.status_verifikasi === 'pending'"
                                        class="action-btn approve"
                                        @click="openVerifyModal(u, 'verified')"
                                    >
                                        Verifikasi
                                    </button>
                                    <button
                                        v-if="u.status_verifikasi === 'pending' || u.status_verifikasi === 'verified' || u.status_verifikasi === 'approved'"
                                        class="action-btn reject"
                                        @click="openVerifyModal(u, 'rejected')"
                                    >
                                        Tolak
                                    </button>
                                    <button
                                        v-if="u.status_verifikasi === 'rejected'"
                                        class="action-btn approve"
                                        @click="openVerifyModal(u, 'verified')"
                                    >
                                        Aktifkan
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>

        <div v-if="verifyModal.show" class="modal-overlay" @click.self="verifyModal.show = false">
            <div class="modal">
                <div class="modal-header">
                    <h2>Konfirmasi Aksi</h2>
                    <button class="modal-close" @click="verifyModal.show = false">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="decision-badge" :class="'decision-' + (verifyModal.action === 'verified' ? 'approved' : verifyModal.action)">
                        {{ (verifyModal.action === 'verified' || verifyModal.action === 'approved') ? 'Verifikasi' : 'Tolak' }} Akun
                    </div>
                    <div class="user-preview">
                        <div class="user-avatar-md">{{ verifyModal.user?.name?.charAt(0)?.toUpperCase() }}</div>
                        <div>
                            <div class="user-full-name">{{ verifyModal.user?.name }}</div>
                            <div class="user-email">{{ verifyModal.user?.email }}</div>
                            <div class="user-instansi">{{ verifyModal.user?.instansi || 'Instansi tidak diisi' }}</div>
                        </div>
                    </div>
                    <div v-if="verifyModal.error" class="alert-error">{{ verifyModal.error }}</div>
                </div>
                <div class="modal-footer">
                    <button class="btn-cancel" @click="verifyModal.show = false">Batal</button>
                    <button
                        class="btn-decide"
                        :class="(verifyModal.action === 'verified' || verifyModal.action === 'approved') ? 'btn-approved' : 'btn-rejected'"
                        @click="submitVerify"
                        :disabled="verifyModal.loading"
                    >
                        <span v-if="verifyModal.loading" class="spinner-sm"></span>
                        <span v-else>{{ (verifyModal.action === 'verified' || verifyModal.action === 'approved') ? 'Verifikasi Akun' : 'Tolak Akun' }}</span>
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

const users = ref([])
const loading = ref(true)
const activeTab = ref('pending')

const tabs = [
    { key: 'pending', label: 'Menunggu Verifikasi' },
    { key: 'verified', label: 'Terverifikasi' },
    { key: 'rejected', label: 'Ditolak' },
]

const countByStatus = computed(() =>
    users.value.reduce((acc, u) => {
        const key = u.status_verifikasi === 'approved' ? 'verified' : u.status_verifikasi
        acc[key] = (acc[key] || 0) + 1
        return acc
    }, {})
)

const filteredUsers = computed(() =>
    users.value.filter(u => {
        const status = u.status_verifikasi === 'approved' ? 'verified' : u.status_verifikasi
        return status === activeTab.value
    })
)

const verifyModal = ref({
    show: false,
    user: null,
    action: '',
    loading: false,
    error: '',
})

onMounted(async () => {
    try {
        const res = await api.admin.getUsers()
        users.value = (res.data.users || res.data.data || []).map(u => ({
            ...u,
            status_verifikasi: u.status_verifikasi === 'approved' ? 'verified' : u.status_verifikasi,
        }))
    } catch {
        users.value = []
    } finally {
        loading.value = false
    }
})

function openVerifyModal(u, action) {
    verifyModal.value = { show: true, user: u, action, loading: false, error: '' }
}

async function submitVerify() {
    verifyModal.value.loading = true
    verifyModal.value.error = ''
    try {
        await api.admin.verifyUser(verifyModal.value.user.id, { status: verifyModal.value.action })
        const idx = users.value.findIndex(u => u.id === verifyModal.value.user.id)
        if (idx !== -1) users.value[idx].status_verifikasi = verifyModal.value.action
        verifyModal.value.show = false
    } catch (err) {
        verifyModal.value.error = err.response?.data?.message || 'Terjadi kesalahan.'
    } finally {
        verifyModal.value.loading = false
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
    const map = { pending: 'Menunggu', verified: 'Terverifikasi', approved: 'Terverifikasi', rejected: 'Ditolak' }
    return map[status] || status
}

function fileUrl(path) {
    if (!path) return '#'
    return path.startsWith('http') ? path : `http://localhost:8000/storage/${path}`
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
    display: flex; flex-direction: column;
    padding: 1.5rem 0;
    position: fixed; top: 0; left: 0;
    height: 100vh; z-index: 50;
}

.sidebar-brand {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0 1.5rem 1.5rem;
    font-weight: 700; font-size: 1rem; color: #fff;
    border-bottom: 1px solid rgba(255,255,255,0.08);
    margin-bottom: 1rem;
}

.brand-logo {
    width: 36px; height: 36px;
    background: #0f4c81; color: #fff;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 0.85rem; flex-shrink: 0;
}

.sidebar-nav {
    flex: 1; padding: 0 0.75rem;
    display: flex; flex-direction: column; gap: 0.25rem;
}

.nav-section-label {
    font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: rgba(255,255,255,0.3);
    padding: 0 0.75rem; margin-bottom: 0.5rem;
}

.nav-item {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.6rem 0.75rem; border-radius: 8px;
    color: rgba(255,255,255,0.6); text-decoration: none;
    font-size: 0.875rem; font-weight: 500;
    transition: background 0.15s, color 0.15s;
}

.nav-item:hover { background: rgba(255,255,255,0.08); color: #fff; }
.nav-item.active { background: rgba(15,76,129,0.5); color: #fff; }

.sidebar-footer {
    padding: 1rem 1.5rem 0;
    border-top: 1px solid rgba(255,255,255,0.08);
}

.user-info { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }

.user-avatar {
    width: 36px; height: 36px;
    background: #1e3a5f; color: #93c5fd;
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
    border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;
    background: none; color: rgba(255,255,255,0.5);
    font-size: 0.8rem; font-weight: 500;
    cursor: pointer; transition: border-color 0.15s, color 0.15s;
}

.btn-logout:hover { border-color: rgba(239,68,68,0.4); color: #fca5a5; }

.main-content { margin-left: 260px; flex: 1; padding: 2rem 2.5rem; }

.page-header { margin-bottom: 1.75rem; }
.page-header h1 { font-size: 1.75rem; font-weight: 800; color: #0f172a; letter-spacing: -0.02em; }
.page-header p { color: #64748b; font-size: 0.9rem; margin-top: 0.25rem; }

.tab-bar {
    display: flex; gap: 0.25rem;
    background: #fff; border: 1px solid #e2e8f0;
    border-radius: 12px; padding: 0.5rem;
    margin-bottom: 1.5rem; width: fit-content;
}

.tab-btn {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.5rem 1rem; border: none; border-radius: 8px;
    background: transparent; color: #64748b;
    font-size: 0.85rem; font-weight: 500;
    cursor: pointer; transition: background 0.15s, color 0.15s;
}

.tab-btn:hover { background: #f1f5f9; }
.tab-btn.active { background: #0f4c81; color: #fff; }

.tab-count {
    background: rgba(0,0,0,0.1);
    border-radius: 999px;
    padding: 0.1rem 0.5rem;
    font-size: 0.75rem; font-weight: 700;
    min-width: 20px; text-align: center;
}

.tab-btn.active .tab-count { background: rgba(255,255,255,0.2); }

.user-table-wrapper {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: auto;
}

.user-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 860px;
}

.user-table th {
    background: #f8fafc;
    padding: 0.75rem 1.25rem;
    text-align: left;
    font-size: 0.75rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.05em;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.user-table td {
    padding: 1rem 1.25rem;
    font-size: 0.875rem; color: #374151;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.user-table tr:last-child td { border-bottom: none; }
.user-table tr:hover td { background: #fafbfc; }

.user-cell { display: flex; align-items: center; gap: 0.75rem; }

.user-avatar-sm {
    width: 36px; height: 36px;
    background: #eff6ff; color: #2563eb;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.9rem; flex-shrink: 0;
}

.user-full-name { font-weight: 600; color: #0f172a; font-size: 0.875rem; }
.user-email { font-size: 0.78rem; color: #64748b; }
.col-date { white-space: nowrap; color: #64748b; }

.badge {
    display: inline-flex; align-items: center;
    padding: 0.28rem 0.65rem; border-radius: 999px;
    font-size: 0.72rem; font-weight: 600; white-space: nowrap;
}

.badge-pending { background: #fef9c3; color: #854d0e; }
.badge-verified, .badge-approved { background: #dcfce7; color: #15803d; }
.badge-rejected { background: #fee2e2; color: #991b1b; }

.btn-file {
    display: inline-flex; align-items: center; gap: 0.35rem;
    color: #2563eb; text-decoration: none;
    font-size: 0.8rem; font-weight: 600;
    padding: 0.3rem 0.75rem;
    border: 1.5px solid #bfdbfe;
    border-radius: 6px; background: #eff6ff;
    transition: all 0.15s;
}

.btn-file:hover { background: #dbeafe; border-color: #93c5fd; }
.no-file { font-size: 0.78rem; color: #94a3b8; }

.action-btns { display: flex; gap: 0.5rem; flex-wrap: wrap; }

.action-btn {
    padding: 0.4rem 0.85rem;
    border-radius: 6px;
    font-size: 0.78rem; font-weight: 600;
    cursor: pointer; border: 1.5px solid;
    transition: all 0.15s;
}

.action-btn.approve { border-color: #16a34a; color: #16a34a; background: #f0fdf4; }
.action-btn.approve:hover { background: #16a34a; color: #fff; }

.action-btn.reject { border-color: #dc2626; color: #dc2626; background: #fef2f2; }
.action-btn.reject:hover { background: #dc2626; color: #fff; }

.loading-state, .empty-state {
    text-align: center; padding: 4rem 2rem; color: #64748b;
}

.loading-state { display: flex; flex-direction: column; align-items: center; gap: 1rem; }
.empty-state svg { opacity: 0.3; margin: 0 auto 1rem; display: block; }
.empty-state h3 { font-size: 1.1rem; font-weight: 700; color: #374151; margin-bottom: 0.5rem; }

.spinner-lg {
    width: 36px; height: 36px;
    border: 3px solid #e2e8f0; border-top-color: #0f4c81;
    border-radius: 50%; animation: spin 0.7s linear infinite;
}

.spinner-sm {
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff;
    border-radius: 50%; animation: spin 0.7s linear infinite;
    display: inline-block;
}

@keyframes spin { to { transform: rotate(360deg); } }

.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex; align-items: center; justify-content: center;
    z-index: 200; backdrop-filter: blur(4px);
}

.modal {
    background: #fff; border-radius: 16px;
    width: 480px; max-width: 95vw; padding: 2rem;
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
    padding: 0.35rem 1rem; border-radius: 999px;
    font-size: 0.8rem; font-weight: 700;
    margin-bottom: 1.25rem;
    text-transform: uppercase; letter-spacing: 0.05em;
}

.decision-approved { background: #dcfce7; color: #15803d; }
.decision-rejected { background: #fee2e2; color: #991b1b; }

.user-preview {
    display: flex; align-items: center; gap: 1rem;
    background: #f8fafc; border: 1px solid #e2e8f0;
    border-radius: 10px; padding: 1rem;
}

.user-avatar-md {
    width: 44px; height: 44px;
    background: #eff6ff; color: #2563eb;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 1rem; flex-shrink: 0;
}

.user-instansi { font-size: 0.78rem; color: #94a3b8; margin-top: 0.2rem; }

.alert-error {
    background: #fef2f2; border: 1px solid #fecaca;
    color: #dc2626; border-radius: 10px;
    padding: 0.75rem 1rem; font-size: 0.85rem;
    font-weight: 500; margin-top: 1rem;
}

.modal-footer {
    display: flex; gap: 0.75rem; justify-content: flex-end;
    padding-top: 1.5rem; border-top: 1px solid #f1f5f9;
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
    cursor: pointer; color: #fff; transition: filter 0.15s;
}

.btn-decide:disabled { opacity: 0.6; cursor: not-allowed; }
.btn-decide.btn-approved { background: #16a34a; }
.btn-decide.btn-rejected { background: #dc2626; }

@media (max-width: 768px) {
    .sidebar { display: none; }
    .main-content { margin-left: 0; padding: 1.25rem; }
    .tab-bar { width: 100%; flex-wrap: wrap; }
}
</style>
