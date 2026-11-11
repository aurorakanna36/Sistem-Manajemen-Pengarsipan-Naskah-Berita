<template>
    <div class="ktr-layout">
        <button v-if="isMobileSidebarOpen" type="button" class="sidebar-overlay" aria-label="Tutup menu"
            @click="closeMobileSidebar"></button>

        <aside :class="[
            'ktr-sidebar',
            {
                collapsed: isSidebarCollapsed,
                'mobile-open': isMobileSidebarOpen
            }
        ]">
            <div class="sidebar-brand">
                <div v-if="!isSidebarCollapsed || isMobileSidebarOpen" class="brand-info">
                    <h2 class="brand-title">Harian Cakrawala</h2>
                    <span class="brand-sub">Kontributor</span>
                </div>

                <button type="button" class="toggle-sidebar" @click="toggleSidebar"
                    :title="isMobileSidebarOpen ? 'Tutup menu' : 'Toggle sidebar'">
                    <svg v-if="isSidebarCollapsed && !isMobileSidebarOpen" viewBox="0 0 24 24" width="21" height="21"
                        fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>

                    <svg v-else viewBox="0 0 24 24" width="21" height="21" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </button>
            </div>

            <nav class="sidebar-nav">
                <!-- Dashboard -->
                <router-link to="/kontributor/dashboard" class="nav-item"
                    :class="{ active: isRouteActive('/kontributor/dashboard') }" title="Dashboard"
                    @click="closeMobileSidebar">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1z" />
                    </svg>
                    <span v-if="!isSidebarCollapsed || isMobileSidebarOpen">
                        Dashboard
                    </span>
                </router-link>

                <router-link to="/kontributor/kirim-naskah" class="nav-item"
                    :class="{ active: isRouteActive('/kontributor/kirim-naskah') }" title="Kirim Naskah"
                    @click="closeMobileSidebar">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4z" />
                    </svg>
                    <span v-if="!isSidebarCollapsed || isMobileSidebarOpen">
                        Kirim Naskah
                    </span>
                </router-link>

                <router-link to="/kontributor/naskah-saya" class="nav-item" :class="{
                    active:
                        isRouteActive('/kontributor/naskah-saya') ||
                        isRoutePrefix('/kontributor/naskah/')
                }" title="Naskah Saya" @click="closeMobileSidebar">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <path d="M14 2v6h6M8 13h8M8 17h8" />
                    </svg>
                    <span v-if="!isSidebarCollapsed || isMobileSidebarOpen">
                        Naskah Saya
                    </span>
                </router-link>

                <router-link to="/kontributor/notifikasi" class="nav-item nav-item-notifikasi" :class="{
                    active: isRouteActive('/kontributor/notifikasi')
                }" title="Notifikasi" @click="closeMobileSidebar">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                        <path d="M10 21h4" />
                    </svg>

                    <span v-if="!isSidebarCollapsed || isMobileSidebarOpen" class="nav-label">
                        Notifikasi
                    </span>

                    <span class="notif-floating-badge" :class="{
                        'has-unread': displayUnreadCount > 0,
                        'collapsed-badge':
                            isSidebarCollapsed && !isMobileSidebarOpen
                    }" :title="`${displayUnreadCount} notifikasi belum dibaca`">
                        {{ displayUnreadCount }}
                    </span>
                </router-link>

                <button type="button" class="nav-item nav-logout" @click="handleLogout" title="Keluar">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <path d="m16 17 5-5-5-5" />
                        <path d="M21 12H9" />
                    </svg>
                    <span v-if="!isSidebarCollapsed || isMobileSidebarOpen">
                        Keluar
                    </span>
                </button>
            </nav>
        </aside>

        <div class="ktr-main-wrap">
            <header class="ktr-topbar">
                <button type="button" class="mobile-menu-button" aria-label="Buka menu" @click="openMobileSidebar">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </header>

            <main class="ktr-content">
                <slot :userName="userName" :userInitial="userInitial" />
            </main>
        </div>
    </div>
</template>



<script setup>
import {
    ref,
    computed,
    onMounted,
    onBeforeUnmount,
    watch
} from 'vue'

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

const userName = computed(
    () => user?.nama_lengkap || user?.name || 'Kontributor'
)

const userInitial = computed(
    () => userName.value.charAt(0).toUpperCase() || 'K'
)

const isSidebarCollapsed = ref(false)

const isMobileSidebarOpen = ref(false)

function openMobileSidebar() {
    isMobileSidebarOpen.value = true
}

function closeMobileSidebar() {
    isMobileSidebarOpen.value = false
}

function toggleSidebar() {
    if (window.matchMedia('(max-width: 960px)').matches) {
        closeMobileSidebar()
        return
    }

    isSidebarCollapsed.value = !isSidebarCollapsed.value
}

const fetchedUnreadCount = ref(0)

const displayUnreadCount = computed(() => {
    if (
        props.unreadCountOverride !== null &&
        props.unreadCountOverride !== undefined
    ) {
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

    window.addEventListener(
        'kontributor-unread-updated',
        handleUnreadEvent
    )
})

onBeforeUnmount(() => {
    window.removeEventListener(
        'kontributor-unread-updated',
        handleUnreadEvent
    )
})

// Logout
async function handleLogout() {
    closeMobileSidebar()

    try {
        await api.auth.logout()
    } catch {
        // Tetap lanjutkan proses logout lokal
    }

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
    width: 260px;
    flex: 0 0 260px;
    min-height: 100vh;
    height: 100vh;
    background: var(--ktr-green);
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
    overflow: hidden;
    box-sizing: border-box;
    transition: width 0.3s ease, flex-basis 0.3s ease;
}

.ktr-sidebar.collapsed {
    width: 80px;
    flex-basis: 80px;
}

.sidebar-brand {
    min-height: 112px;
    padding: 25px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    box-sizing: border-box;
}

.brand-info {
    min-width: 0;
    white-space: nowrap;
}

.brand-title {
    color: var(--ktr-white);
    font-size: 19px;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.3px;
}

.brand-sub {
    display: block;
    color: var(--ktr-border-soft);
    font-size: 13px;
    margin-top: 5px;
}

.toggle-sidebar {
    width: 36px;
    height: 36px;
    min-width: 36px;
    border: none;
    border-radius: 9px;
    background: rgba(255, 255, 255, 0.12);
    color: var(--ktr-white);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s ease;
}

.toggle-sidebar:hover {
    background: var(--ktr-teal);
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    padding: 12px;
    gap: 8px;
}

.nav-item {
    width: 100%;
    min-height: 48px;
    padding: 0 14px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 13px;
    box-sizing: border-box;

    color: var(--ktr-border-soft);
    background: transparent;
    border: none;
    border-radius: 9px;

    font-family: inherit;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    text-align: left;
    white-space: nowrap;
    cursor: pointer;

    transition: background 0.2s ease, color 0.2s ease;
}

.nav-item:hover {
    background: var(--ktr-green-hover);
    color: var(--ktr-white);
}

.nav-item.active {
    background: var(--ktr-teal);
    color: var(--ktr-white);
    font-weight: 600;
}

.menu-icon {
    width: 21px;
    height: 21px;
    min-width: 21px;
    fill: none;
    stroke: currentColor;
    stroke-width: 1.8;
    stroke-linecap: round;
    stroke-linejoin: round;
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
    margin-left: auto;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 1;
    background-color: var(--ktr-green-muted);
    color: var(--ktr-border-soft);
    flex-shrink: 0;
    transition: background-color 0.2s, color 0.2s;
}

.notif-floating-badge.has-unread {
    background-color: #EF4444;
    color: #FFFFFF;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.35);
}

.nav-item.active .notif-floating-badge.has-unread {
    background-color: #FFFFFF;
    color: var(--ktr-teal);
}

.nav-logout {
    margin-top: 22px;
    color: var(--ktr-border-soft);
}

.nav-logout:hover {
    background: rgba(239, 68, 68, 0.15);
    color: #FCA5A5;
}

.ktr-sidebar.collapsed .sidebar-brand {
    padding: 20px 0;
    justify-content: center;
}

.ktr-sidebar.collapsed .sidebar-nav {
    padding: 12px 10px;
}

.ktr-sidebar.collapsed .nav-item {
    justify-content: center;
    padding: 0;
    gap: 0;
}

.ktr-sidebar.collapsed .nav-logout {
    margin-top: 22px;
}

.ktr-sidebar.collapsed .notif-floating-badge {
    position: absolute;
    top: 3px;
    right: 5px;
    min-width: 16px;
    height: 16px;
    padding: 0 3px;
    font-size: 10px;
    border: 2px solid var(--ktr-green);
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

.ktr-topbar {
    display: none;
    align-items: center;
    min-height: 62px;
    padding: 0 20px;
    background: var(--ktr-bg-page);
    border-bottom: 1px solid var(--ktr-border-soft);
    box-sizing: border-box;
}

.mobile-menu-button {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 9px;
    background: var(--ktr-green);
    color: var(--ktr-white);
    cursor: pointer;
}

.mobile-menu-button:hover {
    background: var(--ktr-green-hover);
}

.sidebar-overlay {
    display: none;
}

@media (max-width: 960px) {
    .ktr-layout {
        flex-direction: row;
        min-height: 100vh;
    }

    .ktr-sidebar,
    .ktr-sidebar.collapsed {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;

        width: min(280px, 85vw);
        flex: 0 0 auto;

        height: 100vh;
        height: 100dvh;
        min-height: 0;

        overflow-y: auto;
        overflow-x: hidden;

        z-index: 1001;

        transform: translateX(-100%);
        transition: transform 0.3s ease;

        box-shadow: none;
    }

    .ktr-sidebar.mobile-open {
        transform: translateX(0);
        box-shadow: 8px 0 30px rgba(0, 0, 0, 0.2);
    }

    .ktr-sidebar.collapsed .sidebar-brand {
        padding: 25px 20px;
        justify-content: space-between;
    }

    .ktr-sidebar.collapsed .sidebar-nav {
        padding: 12px;
    }

    .ktr-sidebar.collapsed .nav-item {
        justify-content: flex-start;
        padding: 0 14px;
        gap: 13px;
    }

    .ktr-sidebar.collapsed .notif-floating-badge {
        position: static;
        min-width: 22px;
        height: 22px;
        padding: 0 6px;
        margin-left: auto;
        font-size: 0.75rem;
        border: none;
    }

    .sidebar-overlay {
        display: block;
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;
        padding: 0;
        border: none;
        background: rgba(0, 0, 0, 0.45);
        z-index: 1000;
        cursor: pointer;
    }

    .ktr-topbar {
        display: flex;
    }

    .ktr-main-wrap {
        width: 100%;
        min-width: 0;
    }

    .ktr-content {
        padding: 1.5rem;
    }
}

@media (max-width: 600px) {
    .ktr-topbar {
        min-height: 58px;
        padding: 0 15px;
    }

    .ktr-content {
        padding: 1.15rem 1rem 1.5rem;
    }
}

</style>
