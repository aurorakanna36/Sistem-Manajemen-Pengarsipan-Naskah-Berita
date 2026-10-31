<template>
    <div class="dashboard-page">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-logo">HC</div>
                <span>Harian Cakrawala</span>
            </div>
            <nav class="sidebar-nav">
                <div class="nav-section-label">Menu</div>
                <a href="#" class="nav-item active">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </a>
                <a href="#" class="nav-item" @click.prevent="showModal = true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Kirim Naskah
                </a>
                <router-link to="/" class="nav-item">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Beranda
                </router-link>
            </nav>
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">{{ userInitial }}</div>
                    <div class="user-detail">
                        <div class="user-name">{{ user?.nama_lengkap || user?.name }}</div>
                        <div class="user-role">Kontributor</div>
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
                    <h1>Dashboard Kontributor</h1>
                    <p>Selamat datang kembali, <strong>{{ user?.nama_lengkap || user?.name }}</strong></p>
                </div>
                <button class="btn-primary" @click="showModal = true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    Kirim Naskah Baru
                </button>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ stats.total }}</div>
                        <div class="stat-label">Total Naskah</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon draft">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ stats.draft }}</div>
                        <div class="stat-label">Sedang Ditinjau</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon approved">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ stats.disetujui }}</div>
                        <div class="stat-label">Disetujui / Terbit</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon revisi">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                    </div>
                    <div class="stat-info">
                        <div class="stat-value">{{ stats.revisi }}</div>
                        <div class="stat-label">Perlu Revisi</div>
                    </div>
                </div>
            </div>

            <section class="table-section">
                <div class="section-header">
                    <h2>Riwayat Naskah Saya</h2>
                </div>

                <div v-if="loadingData" class="loading-state">
                    <div class="spinner-lg"></div>
                    <span>Memuat data naskah...</span>
                </div>

                <div v-else-if="naskahs.length === 0" class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <h3>Belum Ada Naskah</h3>
                    <p>Mulai kontribusi Anda dengan mengirimkan naskah pertama.</p>
                    <button class="btn-primary" @click="showModal = true">Kirim Naskah Pertama</button>
                </div>

                <table v-else class="naskah-table">
                    <thead>
                        <tr>
                            <th>Judul Naskah</th>
                            <th>Tanggal Kirim</th>
                            <th>Status</th>
                            <th>Catatan Redaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="naskah in naskahs" :key="naskah.id">
                            <td class="col-judul">{{ naskah.judul }}</td>
                            <td class="col-date">{{ formatDate(naskah.waktu_kirim || naskah.created_at) }}</td>
                            <td>
                                <span class="badge" :class="'badge-' + naskah.status">
                                    {{ labelStatus(naskah.status) }}
                                </span>
                            </td>
                            <td class="col-catatan">{{ naskah.catatan_redaksi || '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </main>

        <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
            <div class="modal">
                <div class="modal-header">
                    <h2>Kirim Naskah Baru</h2>
                    <button class="modal-close" @click="showModal = false">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <form @submit.prevent="handleKirimNaskah">
                    <div v-if="modalError" class="alert-error">{{ modalError }}</div>

                    <div class="form-group">
                        <label for="k-judul">Judul Naskah <span class="required">*</span></label>
                        <input id="k-judul" v-model="naskahForm.judul" type="text" placeholder="Tuliskan judul berita" required />
                    </div>
                    <div class="form-group">
                        <label for="k-isi">Isi Naskah <span class="required">*</span></label>
                        <textarea id="k-isi" v-model="naskahForm.isi" rows="6" placeholder="Tulis isi naskah berita Anda..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="k-file">File Naskah (PDF / DOC)</label>
                        <div class="file-upload-zone" :class="{ 'has-file': naskahFileName }" @click="$refs.naskahFileInput.click()">
                            <svg v-if="!naskahFileName" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <span>{{ naskahFileName || 'Klik untuk pilih file — Maks. 10MB' }}</span>
                            <input ref="naskahFileInput" type="file" accept=".pdf,.doc,.docx" @change="handleNaskahFile" hidden />
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" @click="showModal = false">Batal</button>
                        <button type="submit" class="btn-primary" :disabled="modalLoading">
                            <span v-if="modalLoading" class="spinner-sm"></span>
                            <span v-else>Kirim Naskah</span>
                        </button>
                    </div>
                </form>
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

const userInitial = computed(() => (user?.nama_lengkap || user?.name)?.charAt(0)?.toUpperCase() || 'K')

const naskahs = ref([])
const stats = ref({ total: 0, draft: 0, disetujui: 0, revisi: 0 })
const loadingData = ref(true)

const showModal = ref(false)
const modalLoading = ref(false)
const modalError = ref('')
const naskahFileName = ref('')
const naskahFile = ref(null)
const naskahForm = ref({ judul: '', isi: '' })

onMounted(async () => {
    try {
        const res = await api.kontributor.dashboard()
        naskahs.value = res.data.naskahs || []
        stats.value = res.data.stats || { total: 0, draft: 0, disetujui: 0, revisi: 0 }
    } catch {
        naskahs.value = []
    } finally {
        loadingData.value = false
    }
})

function handleNaskahFile(event) {
    const file = event.target.files[0]
    if (file) {
        naskahFile.value = file
        naskahFileName.value = file.name
    }
}

async function handleKirimNaskah() {
    modalLoading.value = true
    modalError.value = ''
    try {
        const payload = new FormData()
        payload.append('judul', naskahForm.value.judul)
        payload.append('isi', naskahForm.value.isi)
        if (naskahFile.value) {
            payload.append('file', naskahFile.value)
            payload.append('berkas_naskah', naskahFile.value)
        }
        const res = await api.kontributor.storeNaskah(payload)
        naskahs.value.unshift(res.data.naskah)
        stats.value.total++
        stats.value.draft++
        showModal.value = false
        naskahForm.value = { judul: '', isi: '' }
        naskahFile.value = null
        naskahFileName.value = ''
    } catch (err) {
        modalError.value = err.response?.data?.message || 'Gagal mengirim naskah.'
    } finally {
        modalLoading.value = false
    }
}

async function handleLogout() {
    try { await api.auth.logout() } catch {}
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_info')
    router.push({ name: 'login' })
}

function formatDate(dateStr) {
    if (!dateStr) return '-'
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

function labelStatus(status) {
    const map = {
        pending: 'Menunggu Review',
        approved: 'Disetujui',
        rejected: 'Ditolak',
        revision: 'Perlu Revisi',
        published: 'Terbit',
        perlu_direview: 'Menunggu Review',
        diterima: 'Disetujui',
        ditolak: 'Ditolak',
        direvisi: 'Perlu Revisi',
        diterbitkan: 'Terbit',
    }
    return map[status] || status
}
</script>

<style scoped>
.dashboard-page {
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
    top: 0;
    left: 0;
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
    width: 36px;
    height: 36px;
    background: #0f4c81;
    color: #fff;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.sidebar-nav {
    flex: 1;
    padding: 0 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.nav-section-label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(255,255,255,0.3);
    padding: 0 0.75rem;
    margin-bottom: 0.5rem;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem 0.75rem;
    border-radius: 8px;
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: background 0.15s, color 0.15s;
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
}

.nav-item:hover,
.nav-item.active {
    background: rgba(255,255,255,0.08);
    color: #fff;
}

.nav-item.active { background: rgba(15,76,129,0.5); color: #fff; }

.sidebar-footer {
    padding: 1rem 1.5rem 0;
    border-top: 1px solid rgba(255,255,255,0.08);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.user-avatar {
    width: 36px;
    height: 36px;
    background: #1e3a5f;
    color: #93c5fd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.user-name {
    font-size: 0.85rem;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
}

.user-role {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.4);
}

.btn-logout {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    background: none;
    color: rgba(255,255,255,0.5);
    font-size: 0.8rem;
    font-weight: 500;
    cursor: pointer;
    transition: border-color 0.15s, color 0.15s;
}

.btn-logout:hover {
    border-color: rgba(239,68,68,0.4);
    color: #fca5a5;
}

.main-content {
    margin-left: 260px;
    flex: 1;
    padding: 2rem 2.5rem;
}

.page-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.02em;
}

.page-header p {
    color: #64748b;
    font-size: 0.9rem;
    margin-top: 0.25rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: box-shadow 0.2s;
}

.stat-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); }

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon.total { background: #eff6ff; color: #2563eb; }
.stat-icon.draft { background: #fefce8; color: #ca8a04; }
.stat-icon.approved { background: #f0fdf4; color: #16a34a; }
.stat-icon.revisi { background: #fff7ed; color: #ea580c; }

.stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1;
}

.stat-label {
    font-size: 0.78rem;
    color: #64748b;
    margin-top: 0.25rem;
    font-weight: 500;
}

.table-section {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}

.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.section-header h2 {
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
}

.naskah-table {
    width: 100%;
    border-collapse: collapse;
}

.naskah-table th {
    background: #f8fafc;
    padding: 0.75rem 1.5rem;
    text-align: left;
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
}

.naskah-table td {
    padding: 1rem 1.5rem;
    font-size: 0.875rem;
    color: #374151;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: top;
}

.naskah-table tr:last-child td { border-bottom: none; }
.naskah-table tr:hover td { background: #fafbfc; }

.col-judul { font-weight: 600; color: #0f172a; max-width: 280px; }
.col-date { color: #64748b; white-space: nowrap; }
.col-catatan { color: #94a3b8; font-style: italic; max-width: 200px; }

.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.3rem 0.75rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.badge-pending { background: #fef9c3; color: #854d0e; }
.badge-approved, .badge-published { background: #dcfce7; color: #15803d; }
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
.empty-state p { margin-bottom: 1.5rem; font-size: 0.875rem; }

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
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 200;
    backdrop-filter: blur(4px);
}

.modal {
    background: #fff;
    border-radius: 16px;
    width: 560px;
    max-width: 95vw;
    max-height: 90vh;
    overflow-y: auto;
    padding: 2rem;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
}

.modal-header h2 { font-size: 1.25rem; font-weight: 700; color: #0f172a; }

.modal-close {
    width: 32px; height: 32px;
    border: none; background: #f1f5f9;
    border-radius: 8px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: #64748b; transition: background 0.15s;
}

.modal-close:hover { background: #e2e8f0; }

.form-group { margin-bottom: 1.25rem; }

.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.required { color: #ef4444; }

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #0f172a;
    font-family: inherit;
    resize: vertical;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #0f4c81;
    box-shadow: 0 0 0 3px rgba(15,76,129,0.1);
}

.file-upload-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-size: 0.875rem;
    color: #64748b;
    transition: border-color 0.2s, background 0.2s;
}

.file-upload-zone:hover,
.file-upload-zone.has-file {
    border-color: #0f4c81;
    background: #f0f7ff;
    color: #0f4c81;
}

.modal-footer {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #f1f5f9;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.5rem;
    background: #0f4c81;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
}

.btn-primary:hover:not(:disabled) { background: #0a3460; }
.btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.btn-cancel {
    padding: 0.65rem 1.25rem;
    background: #f1f5f9;
    color: #475569;
    border: none;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-cancel:hover { background: #e2e8f0; }

.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 1.25rem;
}

@media (max-width: 768px) {
    .sidebar { display: none; }
    .main-content { margin-left: 0; padding: 1.25rem; }
    .stats-grid { grid-template-columns: 1fr 1fr; }
}
</style>
