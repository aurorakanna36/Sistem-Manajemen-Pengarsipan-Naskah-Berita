<template>
    <KontributorLayout :unreadCountOverride="unreadCount">
        <div class="notifikasi-page">
            <header class="page-topbar">
                <div class="topbar-titles">
                    <h1 class="page-title">Notifikasi</h1>
                    <p class="page-subtitle">Kabar terbaru tentang naskah yang sudah kamu kirim.</p>
                </div>

                <div class="user-chip">
                    <div class="user-avatar">{{ userInitial }}</div>
                    <span class="user-name">{{ userName }}</span>
                </div>
            </header>

            <div class="notif-toolbar">
                <div class="notif-tabs">
                    <button
                        type="button"
                        class="notif-tab"
                        :class="{ active: filterMode === 'all' }"
                        @click="filterMode = 'all'"
                    >
                        Semua
                    </button>
                    <button
                        type="button"
                        class="notif-tab"
                        :class="{ active: filterMode === 'unread' }"
                        @click="filterMode = 'unread'"
                    >
                        Belum dibaca ({{ unreadCount }})
                    </button>
                </div>

                <button
                    type="button"
                    class="btn-read-all"
                    :disabled="unreadCount === 0"
                    @click="handleMarkAllRead"
                >
                    Tandai semua sudah dibaca
                </button>
            </div>

            <div class="notif-list-card">
                <div v-if="loading" class="state-box">
                    <div class="spinner"></div>
                    <span>Memuat notifikasi...</span>
                </div>

                <div v-else-if="filteredNotifications.length === 0" class="state-box">
                    <p class="empty-text">
                        {{ filterMode === 'unread' ? 'Tidak ada notifikasi yang belum dibaca.' : 'Belum ada notifikasi status naskah.' }}
                    </p>
                </div>

                <div v-else class="notif-items">
                    <div
                        v-for="item in filteredNotifications"
                        :key="item.id"
                        class="notif-row"
                        :class="{ 'is-unread': !item.is_read }"
                        @click="handleClickNotification(item)"
                    >
                        <div class="notif-icon-circle" :class="iconColorClass(item)">
                            <span class="notif-inner-dot"></span>
                        </div>

                        <div class="notif-body">
                            <h3 class="notif-title">{{ item.title }}</h3>
                            <p class="notif-desc">{{ item.message }}</p>
                        </div>

                        <div class="notif-right">
                            <span class="notif-time">{{ formatRelativeTime(item.waktu_status) }}</span>
                            <span v-if="!item.is_read" class="unread-blue-dot"></span>
                        </div>
                    </div>
                </div>
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
const notifications = ref([])
const filterMode = ref('all')

const unreadCount = computed(() => notifications.value.filter((n) => !n.is_read).length)

const filteredNotifications = computed(() => {
    if (filterMode.value === 'unread') {
        return notifications.value.filter((n) => !n.is_read)
    }
    return notifications.value
})

function broadcastUnreadCount() {
    window.dispatchEvent(
        new CustomEvent('kontributor-unread-updated', { detail: unreadCount.value })
    )
}

onMounted(async () => {
    try {
        const res = await api.kontributor.getNotifikasi()
        notifications.value = res.data?.notifications || []
        broadcastUnreadCount()
    } catch {
        notifications.value = []
    } finally {
        loading.value = false
    }
})

function iconColorClass(item) {
    const s = item.tipe || item.status
    if (s === 'diterima' || s === 'diterbitkan') return 'icon-green'
    if (s === 'ditolak') return 'icon-red'
    return 'icon-yellow'
}

async function handleClickNotification(item) {
    if (!item.is_read) {
        item.is_read = true
        broadcastUnreadCount()
        try {
            await api.kontributor.markNotifikasiRead(item.id)
        } catch {}
    }

    if (item.id_naskah) {
        router.push(`/kontributor/naskah/${item.id_naskah}`)
    }
}

async function handleMarkAllRead() {
    notifications.value.forEach((n) => {
        n.is_read = true
    })
    broadcastUnreadCount()

    try {
        await api.kontributor.markAllNotifikasiRead()
    } catch {}
}

function formatRelativeTime(dateStr) {
    if (!dateStr) return '-'
    const date = new Date(dateStr)
    const now = new Date()
    const diffMs = now - date
    const diffMinutes = Math.floor(diffMs / 60000)
    const diffHours = Math.floor(diffMs / 3600000)
    const diffDays = Math.floor(diffMs / 86400000)

    if (diffMinutes < 1) return 'Baru saja'
    if (diffMinutes < 60) return `${diffMinutes} menit lalu`
    if (diffHours < 24 && date.getDate() === now.getDate()) {
        return `${diffHours} jam lalu`
    }
    if (diffDays === 1 || (diffHours < 48 && date.getDate() !== now.getDate())) {
        const timeStr = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
        return `Kemarin, ${timeStr}`
    }
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}
</script>

<style scoped>
.notifikasi-page {
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

.notif-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.notif-tabs {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.notif-tab {
    padding: 0.52rem 1.15rem;
    border-radius: 999px;
    border: 1px solid var(--ktr-border);
    background-color: var(--ktr-bg-card);
    color: var(--ktr-text-main);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s;
}

.notif-tab.active {
    background-color: var(--ktr-teal);
    border-color: var(--ktr-teal);
    color: var(--ktr-white);
    font-weight: 600;
}

.btn-read-all {
    background: none;
    border: none;
    color: var(--ktr-teal);
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
}

.btn-read-all:hover:not(:disabled) {
    text-decoration: underline;
}

.btn-read-all:disabled {
    opacity: 0.45;
    cursor: default;
}

.notif-list-card {
    background-color: var(--ktr-bg-card);
    border: 1px solid var(--ktr-border);
    border-radius: 14px;
    overflow: hidden;
}

.notif-items {
    display: flex;
    flex-direction: column;
}

.notif-row {
    display: flex;
    align-items: center;
    gap: 1.15rem;
    padding: 1.25rem 1.65rem;
    border-bottom: 1px solid #E8EEF4;
    background-color: var(--ktr-bg-card);
    cursor: pointer;
    transition: background-color 0.15s;
}

.notif-row:last-child {
    border-bottom: none;
}

.notif-row.is-unread {
    background-color: var(--ktr-teal-unread);
}

.notif-row:hover {
    background-color: #E7F4F1;
}

.notif-icon-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.notif-inner-dot {
    width: 14px;
    height: 14px;
    border-radius: 50%;
}

/* 3 Indikator Warna: Hijau (diterima), Merah (ditolak), Kuning (perlu direvisi) */
.icon-green {
    background-color: #DCFCE7;
}
.icon-green .notif-inner-dot {
    background-color: #229E59;
}

.icon-red {
    background-color: #FEE2E2;
}
.icon-red .notif-inner-dot {
    background-color: #EF4444;
}

.icon-yellow {
    background-color: #FEF3C7;
}
.icon-yellow .notif-inner-dot {
    background-color: #F59E0B;
}

.notif-body {
    flex: 1;
    min-width: 0;
}

.notif-title {
    font-size: 0.98rem;
    font-weight: 700;
    color: var(--ktr-text-main);
    margin: 0 0 0.25rem;
    line-height: 1.35;
}

.notif-desc {
    font-size: 0.88rem;
    color: var(--ktr-text-muted);
    margin: 0;
    line-height: 1.45;
}

.notif-right {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    flex-shrink: 0;
}

.notif-time {
    font-size: 0.84rem;
    color: var(--ktr-text-muted);
    white-space: nowrap;
}

.unread-blue-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background-color: #2563EB;
    flex-shrink: 0;
}

.state-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3.5rem 1.5rem;
    gap: 0.85rem;
    color: var(--ktr-text-muted);
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
