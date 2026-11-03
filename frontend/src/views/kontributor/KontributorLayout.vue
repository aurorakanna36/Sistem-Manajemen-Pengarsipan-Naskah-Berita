<template>
    <div class="ktr-layout">
        <aside class="ktr-sidebar">
            <div class="sidebar-brand">
                <h2 class="brand-title">Harian Cakrawala</h2>
                <span class="brand-sub">Kontributor</span>
            </div>

            <nav class="sidebar-nav">
                <router-link
                    to="/kontributor/dashboard"
                    class="nav-item"
                    :class="{ active: isRouteActive('/kontributor/dashboard') }"
                >
                    <span>Dashboard</span>
                </router-link>

                <router-link
                    to="/kontributor/kirim-naskah"
                    class="nav-item"
                    :class="{ active: isRouteActive('/kontributor/kirim-naskah') }"
                >
                    <span>Kirim Naskah</span>
                </router-link>

                <router-link
                    to="/kontributor/naskah-saya"
                    class="nav-item"
                    :class="{ active: isRouteActive('/kontributor/naskah-saya') || isRoutePrefix('/kontributor/naskah/') }"
                >
                    <span>Naskah Saya</span>
                </router-link>

                <router-link
                    to="/kontributor/notifikasi"
                    class="nav-item nav-item-notifikasi"
                    :class="{ active: isRouteActive('/kontributor/notifikasi') }"
                >
                    <span class="nav-label">Notifikasi</span>
                    <span
                        class="notif-floating-badge"
                        :class="{ 'has-unread': displayUnreadCount > 0 }"
                        :title="`${displayUnreadCount} notifikasi belum dibaca`"
                    >
                        {{ displayUnreadCount }}
                    </span>
                </router-link>

                <button type="button" class="nav-item nav-logout" @click="handleLogout">
                    <span>Keluar</span>
                </button>
            </nav>
        </aside>

        <div class="ktr-main-wrap">
            <main class="ktr-content">
                <slot :userName="userName" :userInitial="userInitial" />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/api/index.js'

const props = defineProps({
    unreadCountOverride: {
        type: Number,
        default: null,
    },
})

const route = useRoute()
const router = useRouter()

const userRaw = localStorage.getItem('user_info')
const user = userRaw ? JSON.parse(userRaw) : null

const userName = computed(() => user?.nama_lengkap || user?.name || 'Kontributor')
const userInitial = computed(() => userName.value.charAt(0).toUpperCase() || 'K')

const fetchedUnreadCount = ref(0)

const displayUnreadCount = computed(() => {
    if (props.unreadCountOverride !== null && props.unreadCountOverride !== undefined) {
        return props.unreadCountOverride
    }
    return fetchedUnreadCount.value
})

function isRouteActive(path) {
    return route.path === path
}

function isRoutePrefix(prefix) {
    return route.path.startsWith(prefix)
}

async function fetchUnreadCount() {
    try {
        const res = await api.kontributor.getNotifikasi()
        if (typeof res.data?.unread_count === 'number') {
            fetchedUnreadCount.value = res.data.unread_count
        }
    } catch {
        // Ignore silent fetch errors
    }
}

function handleUnreadEvent(e) {
    if (typeof e.detail === 'number') {
        fetchedUnreadCount.value = e.detail
    } else {
        fetchUnreadCount()
    }
}

watch(
    () => props.unreadCountOverride,
    (val) => {
        if (typeof val === 'number') {
            fetchedUnreadCount.value = val
        }
    }
)

onMounted(() => {
    fetchUnreadCount()
    window.addEventListener('kontributor-unread-updated', handleUnreadEvent)
})

onBeforeUnmount(() => {
    window.removeEventListener('kontributor-unread-updated', handleUnreadEvent)
})

async function handleLogout() {
    try {
        await api.auth.logout()
    } catch {}
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_info')
    router.push({ name: 'login' })
}
</script>

<style scoped>
.ktr-layout {
    display: flex;
    min-height: 100vh;
    background-color: var(--ktr-bg-page);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--ktr-text-main);
}

.ktr-sidebar {
    width: 245px;
    background-color: var(--ktr-navy);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    min-height: 100vh;
    position: sticky;
    top: 0;
    height: 100vh;
}

.sidebar-brand {
    padding: 2rem 1.75rem 1.75rem;
}

.brand-title {
    color: var(--ktr-white);
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.25;
}

.brand-sub {
    color: var(--ktr-border-soft);
    font-size: 0.85rem;
    margin-top: 0.25rem;
    display: inline-block;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    padding: 0.25rem 1rem;
    gap: 0.35rem;
}

.nav-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.78rem 1rem;
    color: var(--ktr-border-soft);
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    border-radius: 8px;
    transition: background 0.2s, color 0.2s;
    background: transparent;
    border: none;
    text-align: left;
    width: 100%;
    cursor: pointer;
}

.nav-item:hover {
    background-color: var(--ktr-navy-hover);
    color: var(--ktr-white);
}

.nav-item.active {
    background-color: var(--ktr-teal);
    color: var(--ktr-white);
    font-weight: 600;
}

.nav-item-notifikasi {
    position: relative;
}

.notif-floating-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 1;
    background-color: var(--ktr-navy-muted);
    color: var(--ktr-border-soft);
    flex-shrink: 0;
    transition: background-color 0.2s, color 0.2s, transform 0.2s;
}

.notif-floating-badge.has-unread {
    background-color: #EF4444;
    color: #FFFFFF;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.35);
}

.nav-item.active .notif-floating-badge.has-unread {
    background-color: #FFFFFF;
    color: var(--ktr-teal);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.nav-logout {
    margin-top: 0.5rem;
    color: var(--ktr-border-soft);
}

.nav-logout:hover {
    background-color: rgba(239, 68, 68, 0.15);
    color: #FCA5A5;
}

.ktr-main-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.ktr-content {
    flex: 1;
    padding: 2.25rem 2.75rem 3.5rem;
}

@media (max-width: 960px) {
    .ktr-layout {
        flex-direction: column;
    }
    .ktr-sidebar {
        width: 100%;
        min-height: auto;
        height: auto;
        position: static;
    }
    .ktr-content {
        padding: 1.5rem;
    }
}
</style>
