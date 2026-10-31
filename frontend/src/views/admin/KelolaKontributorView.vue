<template>
    <AdminLayout>
        <div class="kontributor-page">
            <div class="page-title-block">
                <h1 class="page-title">Kelola kontributor</h1>
                <p class="page-subtitle">Daftar kontributor dan verifikasi pendaftaran.</p>
            </div>

            <div class="filter-pills-row">
                <button 
                    class="pill-btn" 
                    :class="{ active: currentTab === 'kontributor' }"
                    @click="currentTab = 'kontributor'"
                >
                    Daftar kontributor ({{ totalKontributorCount }})
                </button>
                <button 
                    class="pill-btn" 
                    :class="{ active: currentTab === 'verifikasi' }"
                    @click="currentTab = 'verifikasi'"
                >
                    Perlu verifikasi ({{ pendingCount }})
                </button>
                <button 
                    class="pill-btn" 
                    :class="{ active: currentTab === 'admin' }"
                    @click="currentTab = 'admin'"
                >
                    Kelola Admin
                </button>
            </div>

            <section v-if="currentTab === 'verifikasi' || (currentTab === 'kontributor' && pendingUsers.length > 0)" class="panel-box">
                <h2 class="section-title">Perlu verifikasi pendaftaran</h2>
                
                <div v-if="pendingUsers.length === 0" class="empty-state">
                    Tidak ada pendaftaran kontributor yang membutuhkan verifikasi.
                </div>
                <div v-else class="pending-cards-grid">
                    <div 
                        v-for="user in pendingUsers" 
                        :key="user.id" 
                        class="pending-card"
                    >
                        <div class="pending-card-header">
                            <div class="avatar-circle">
                                {{ (user.nama_lengkap || user.name || 'K').charAt(0).toUpperCase() }}
                            </div>
                            <div class="pending-identity">
                                <h3 class="user-fullname">{{ user.nama_lengkap || user.name }}</h3>
                                <p class="user-email-text">{{ user.email }}</p>
                            </div>
                        </div>
                        <div class="pending-meta-text">
                            {{ user.instansi || 'Umum' }} · Mendaftar {{ formatDate(user.created_at) }}
                        </div>
                        <div class="pending-btn-row">
                            <button 
                                class="btn-verify"
                                @click="handleVerify(user.id, 'verified')"
                            >
                                Verifikasi
                            </button>
                            <button 
                                class="btn-reject"
                                @click="handleVerify(user.id, 'rejected')"
                            >
                                Tolak
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section v-if="currentTab === 'kontributor' || currentTab === 'verifikasi'" class="panel-box">
                <div class="table-header-row">
                    <h2 class="section-title">Daftar kontributor</h2>
                    <div class="search-wrap">
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Cari nama atau email"
                            class="search-input"
                        />
                    </div>
                </div>

                <div v-if="loadingUsers" class="empty-state">Memuat data kontributor...</div>
                <div v-else-if="filteredUsers.length === 0" class="empty-state">
                    Tidak ditemukan data kontributor yang cocok.
                </div>
                <div v-else class="table-responsive">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Kontributor</th>
                                <th>Instansi</th>
                                <th>Naskah</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in filteredUsers" :key="user.id">
                                <td>
                                    <div class="user-cell">
                                        <div class="avatar-small">
                                            {{ (user.nama_lengkap || user.name || 'K').charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="user-cell-meta">
                                            <div class="cell-name">{{ user.nama_lengkap || user.name }}</div>
                                            <div class="cell-email">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ user.instansi || '-' }}</td>
                                <td>{{ user.naskah_count ?? '-' }}</td>
                                <td>
                                    <span 
                                        class="status-badge"
                                        :class="statusClass(user.status_verifikasi)"
                                    >
                                        {{ statusLabel(user.status_verifikasi) }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="actions-cell">
                                        <router-link 
                                            :to="`/admin/kontributor/${user.id}`" 
                                            class="link-view"
                                        >
                                            Lihat
                                        </router-link>
                                        <button 
                                            class="btn-delete-icon" 
                                            title="Hapus Kontributor"
                                            @click="confirmDeleteUser(user)"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section v-if="currentTab === 'admin'" class="panel-box">
                <div class="table-header-row">
                    <div>
                        <h2 class="section-title">Kelola Akun Admin</h2>
                        <p class="section-sub">Daftar pengguna dengan hak akses admin redaksi.</p>
                    </div>
                    <button class="btn-add-admin" @click="openAddAdminModal">
                        + Tambah Admin
                    </button>
                </div>

                <div v-if="loadingAdmins" class="empty-state">Memuat data admin...</div>
                <div v-else-if="adminsList.length === 0" class="empty-state">
                    Belum ada akun admin terdaftar.
                </div>
                <div v-else class="table-responsive">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Admin</th>
                                <th>Email</th>
                                <th>WhatsApp</th>
                                <th>Instansi</th>
                                <th>Terdaftar</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="adm in adminsList" :key="adm.id">
                                <td>
                                    <div class="cell-name">{{ adm.nama_lengkap || adm.name }}</div>
                                </td>
                                <td>{{ adm.email }}</td>
                                <td>{{ adm.nomor_whatsapp || adm.phone_number || '-' }}</td>
                                <td>{{ adm.instansi || '-' }}</td>
                                <td>{{ formatDate(adm.created_at) }}</td>
                                <td class="text-right">
                                    <div class="actions-cell">
                                        <button class="link-edit" @click="openEditAdminModal(adm)">Edit</button>
                                        <button class="btn-delete-icon" @click="confirmDeleteAdmin(adm)">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div v-if="showAdminModal" class="modal-backdrop" @click.self="showAdminModal = false">
            <div class="modal-card">
                <div class="modal-header">
                    <h3 class="modal-title">{{ isEditingAdmin ? 'Edit Data Admin' : 'Tambah Akun Admin Baru' }}</h3>
                    <button class="modal-close-btn" @click="showAdminModal = false">&times;</button>
                </div>

                <form @submit.prevent="submitAdminForm" class="modal-form">
                    <div v-if="adminModalError" class="modal-error">{{ adminModalError }}</div>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input v-model="adminForm.nama_lengkap" type="text" required placeholder="Nama lengkap admin" />
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input v-model="adminForm.email" type="email" required placeholder="email@cakrawala.com" />
                    </div>

                    <div class="form-group">
                        <label>Nomor WhatsApp</label>
                        <input v-model="adminForm.nomor_whatsapp" type="text" placeholder="08xxxxxxxxxx" />
                    </div>

                    <div class="form-group">
                        <label>Instansi / Divisi</label>
                        <input v-model="adminForm.instansi" type="text" placeholder="Redaksi Harian Cakrawala" />
                    </div>

                    <div class="form-group">
                        <label>{{ isEditingAdmin ? 'Password Baru (Kosongkan jika tidak diubah)' : 'Password' }}</label>
                        <input v-model="adminForm.password" type="password" :required="!isEditingAdmin" placeholder="Minimal 8 karakter" />
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" @click="showAdminModal = false">Batal</button>
                        <button type="submit" class="btn-submit" :disabled="adminModalLoading">
                            {{ adminModalLoading ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AdminLayout from './AdminLayout.vue'
import api from '@/api/index.js'

const currentTab = ref('kontributor')
const searchQuery = ref('')
const loadingUsers = ref(true)
const loadingAdmins = ref(false)

const allUsers = ref([])
const adminsList = ref([])

const showAdminModal = ref(false)
const isEditingAdmin = ref(false)
const adminModalLoading = ref(false)
const adminModalError = ref('')
const editingAdminId = ref(null)

const adminForm = ref({
    nama_lengkap: '',
    email: '',
    nomor_whatsapp: '',
    instansi: '',
    password: '',
})

const pendingUsers = computed(() => {
    return allUsers.value.filter(u => u.status_verifikasi === 'pending')
})

const pendingCount = computed(() => pendingUsers.value.length)
const totalKontributorCount = computed(() => allUsers.value.length)

const filteredUsers = computed(() => {
    let list = allUsers.value
    if (currentTab.value === 'verifikasi') {
        list = pendingUsers.value
    }
    if (!searchQuery.value) return list
    const q = searchQuery.value.toLowerCase()
    return list.filter(u => {
        const name = (u.nama_lengkap || u.name || '').toLowerCase()
        const email = (u.email || '').toLowerCase()
        return name.includes(q) || email.includes(q)
    })
})

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
    if (status === 'verified') return 'Terverifikasi'
    if (status === 'rejected') return 'Ditolak'
    return 'Menunggu'
}

function statusClass(status) {
    if (status === 'verified') return 'badge-verified'
    if (status === 'rejected') return 'badge-rejected'
    return 'badge-pending'
}

async function fetchUsers() {
    loadingUsers.value = true
    try {
        const res = await api.admin.getUsers()
        allUsers.value = res.data.users || res.data.data || []
    } catch {
        allUsers.value = []
    } finally {
        loadingUsers.value = false
    }
}

async function fetchAdmins() {
    loadingAdmins.value = true
    try {
        const res = await api.admin.getAdmins()
        adminsList.value = res.data.admins || res.data.data || []
    } catch {
        adminsList.value = []
    } finally {
        loadingAdmins.value = false
    }
}

async function handleVerify(userId, status) {
    try {
        await api.admin.verifyUser(userId, { status })
        await fetchUsers()
    } catch (err) {
        alert(err.response?.data?.message || 'Gagal mengubah status verifikasi.')
    }
}

async function confirmDeleteUser(user) {
    const name = user.nama_lengkap || user.name
    if (!confirm(`Apakah Anda yakin ingin menghapus kontributor "${name}"? Seluruh data naskah terkait akan terdampak.`)) return
    try {
        await api.admin.deleteUser(user.id)
        await fetchUsers()
    } catch (err) {
        alert(err.response?.data?.message || 'Gagal menghapus user.')
    }
}

function openAddAdminModal() {
    isEditingAdmin.value = false
    editingAdminId.value = null
    adminForm.value = {
        nama_lengkap: '',
        email: '',
        nomor_whatsapp: '',
        instansi: '',
        password: '',
    }
    adminModalError.value = ''
    showAdminModal.value = true
}

function openEditAdminModal(adm) {
    isEditingAdmin.value = true
    editingAdminId.value = adm.id
    adminForm.value = {
        nama_lengkap: adm.nama_lengkap || adm.name || '',
        email: adm.email || '',
        nomor_whatsapp: adm.nomor_whatsapp || adm.phone_number || '',
        instansi: adm.instansi || '',
        password: '',
    }
    adminModalError.value = ''
    showAdminModal.value = true
}

async function submitAdminForm() {
    adminModalLoading.value = true
    adminModalError.value = ''
    try {
        if (isEditingAdmin.value) {
            await api.admin.updateAdmin(editingAdminId.value, adminForm.value)
        } else {
            await api.admin.createAdmin(adminForm.value)
        }
        showAdminModal.value = false
        await fetchAdmins()
    } catch (err) {
        adminModalError.value = err.response?.data?.message || 'Gagal menyimpan data admin.'
    } finally {
        adminModalLoading.value = false
    }
}

async function confirmDeleteAdmin(adm) {
    const name = adm.nama_lengkap || adm.name
    if (!confirm(`Hapus akun admin "${name}"?`)) return
    try {
        await api.admin.deleteAdmin(adm.id)
        await fetchAdmins()
    } catch (err) {
        alert(err.response?.data?.message || 'Gagal menghapus admin.')
    }
}

onMounted(() => {
    fetchUsers()
    fetchAdmins()
})
</script>

<style scoped>
.kontributor-page {
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
    transition: background 0.2s, color 0.2s, border-color 0.2s;
}

.pill-btn:hover {
    background: var(--adm-bg-page);
}

.pill-btn.active {
    background: var(--adm-teal);
    color: #ffffff;
    border-color: var(--adm-teal);
}

.panel-box {
    background: var(--adm-bg-card);
    border: 1px solid var(--adm-border);
    border-radius: 14px;
    padding: 1.75rem;
}

.section-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--adm-text-main);
    margin: 0 0 1.25rem 0;
}

.section-sub {
    font-size: 0.85rem;
    color: var(--adm-text-muted);
    margin: -0.85rem 0 1.25rem 0;
}

.pending-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}

.pending-card {
    border: 1px solid var(--adm-border);
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    background: #ffffff;
}

.pending-card-header {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    margin-bottom: 0.75rem;
}

.avatar-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background-color: var(--adm-border-dark);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
}

.pending-identity {
    min-width: 0;
}

.user-fullname {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--adm-text-main);
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-email-text {
    font-size: 0.8rem;
    color: var(--adm-text-muted);
    margin: 0.15rem 0 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pending-meta-text {
    font-size: 0.8rem;
    color: var(--adm-text-muted);
    margin-bottom: 1.25rem;
    flex: 1;
}

.pending-btn-row {
    display: flex;
    gap: 0.75rem;
}

.btn-verify {
    flex: 1;
    padding: 0.5rem;
    background: var(--adm-teal);
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-verify:hover {
    background: var(--adm-teal-hover);
}

.btn-reject {
    flex: 1;
    padding: 0.5rem;
    background: transparent;
    color: var(--adm-danger-text);
    border: 1px solid var(--adm-danger-border);
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-reject:hover {
    background: var(--adm-danger-bg);
}

.table-header-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.search-input {
    width: 280px;
    padding: 0.55rem 0.85rem;
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    font-size: 0.85rem;
    outline: none;
    transition: border-color 0.2s;
}

.search-input:focus {
    border-color: var(--adm-teal);
}

.btn-add-admin {
    padding: 0.55rem 1.1rem;
    background: var(--adm-teal);
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
}

.btn-add-admin:hover {
    background: var(--adm-teal-hover);
}

.table-responsive {
    overflow-x: auto;
}

.adm-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}

.adm-table th {
    text-align: left;
    padding: 0.85rem 1rem;
    color: var(--adm-text-muted);
    font-weight: 600;
    border-bottom: 1px solid var(--adm-border);
    font-size: 0.85rem;
}

.adm-table td {
    padding: 1rem;
    border-bottom: 1px solid var(--adm-border);
    color: var(--adm-text-main);
    vertical-align: middle;
}

.adm-table tbody tr:last-child td {
    border-bottom: none;
}

.text-right {
    text-align: right;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.avatar-small {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: var(--adm-border-dark);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.cell-name {
    font-weight: 600;
    color: var(--adm-text-main);
}

.cell-email {
    font-size: 0.8rem;
    color: var(--adm-text-muted);
}

.status-badge {
    display: inline-block;
    padding: 0.35rem 0.85rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.badge-verified {
    background-color: var(--adm-success-bg);
    color: var(--adm-success-text);
}

.badge-pending {
    background-color: var(--adm-warning-bg);
    color: var(--adm-warning-text);
}

.badge-rejected {
    background-color: var(--adm-danger-bg);
    color: var(--adm-danger-text);
}

.actions-cell {
    display: inline-flex;
    align-items: center;
    gap: 0.85rem;
}

.link-view {
    color: var(--adm-teal);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
}

.link-view:hover {
    text-decoration: underline;
}

.link-edit {
    background: none;
    border: none;
    color: var(--adm-teal);
    font-weight: 600;
    cursor: pointer;
    font-size: 0.85rem;
}

.btn-delete-icon {
    background: none;
    border: none;
    color: var(--adm-danger-text);
    font-weight: 500;
    cursor: pointer;
    font-size: 0.85rem;
}

.btn-delete-icon:hover {
    text-decoration: underline;
}

.empty-state {
    padding: 2.5rem 0;
    text-align: center;
    color: var(--adm-text-muted);
}

.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(18, 38, 58, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}

.modal-card {
    background: #ffffff;
    border-radius: 14px;
    width: 100%;
    max-width: 480px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--adm-border);
}

.modal-title {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--adm-text-main);
}

.modal-close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    line-height: 1;
    color: var(--adm-text-muted);
    cursor: pointer;
}

.modal-form {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.modal-error {
    background: var(--adm-danger-bg);
    color: var(--adm-danger-text);
    padding: 0.75rem;
    border-radius: 8px;
    font-size: 0.85rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.form-group label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--adm-text-main);
}

.form-group input {
    padding: 0.6rem 0.85rem;
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    font-size: 0.9rem;
    outline: none;
}

.form-group input:focus {
    border-color: var(--adm-teal);
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    margin-top: 0.5rem;
}

.btn-cancel {
    padding: 0.55rem 1.1rem;
    background: #ffffff;
    border: 1px solid var(--adm-border);
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.btn-submit {
    padding: 0.55rem 1.25rem;
    background: var(--adm-teal);
    color: #ffffff;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.btn-submit:hover:not(:disabled) {
    background: var(--adm-teal-hover);
}

@media (max-width: 950px) {
    .pending-cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>

