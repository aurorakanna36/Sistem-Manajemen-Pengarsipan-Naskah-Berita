<template>
    <div class="ktr-layout">
        <!-- MOBILE SIDEBAR OVERLAY -->
        <button v-if="isMobileSidebarOpen" type="button" class="sidebar-overlay" aria-label="Tutup menu"
            @click="closeMobileSidebar"></button>

        <!-- SIDEBAR -->
        <aside :class="[
            'ktr-sidebar',
            {
                collapsed: isSidebarCollapsed,
                'mobile-open': isMobileSidebarOpen
            }
        ]">
            <!-- SIDEBAR BRAND -->
            <div class="sidebar-brand">
                <router-link to="/" v-if="!isSidebarCollapsed || isMobileSidebarOpen" class="brand-info brand-link-home" title="Kembali ke Beranda">
                    <h2 class="brand-title">Harian Cakrawala</h2>
                    <span class="brand-tagline">Berita Peristiwa Indonesia Terkini</span>
                    <span class="brand-sub">Kontributor</span>
                </router-link>

                <button type="button" class="toggle-sidebar"
                    :title="isMobileSidebarOpen ? 'Tutup menu' : 'Toggle sidebar'" @click="toggleSidebar">
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

            <!-- SIDEBAR NAVIGATION -->
            <nav class="sidebar-nav">
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
            </nav>
        </aside>

        <!-- MAIN CONTENT WRAPPER -->
        <div class="ktr-main-wrap" @click="closeProfileMenu">

            <!-- TOPBAR -->
            <header class="ktr-topbar">

                <div class="topbar-left">
                    <button type="button" class="mobile-menu-button" aria-label="Buka menu" @click="openMobileSidebar">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- BACK BUTTON -->
                <button @click="router.back()" class="back-link">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 12H5" />
                        <path d="m12 19-7-7 7-7" />
                    </svg>
                    <span>Kembali</span>
                </button>

                <!-- PROFILE -->
                <div class="topbar-user-wrapper" @click.stop>
                    <button type="button" class="topbar-user" :aria-expanded="isProfileMenuOpen"
                        aria-label="Buka menu profil" @click="toggleProfileMenu">
                        <div class="user-avatar">
                            {{ userInitial }}
                        </div>

                        <span class="user-name">
                            {{ userName }}
                        </span>

                        <svg class="profile-chevron" :class="{ rotated: isProfileMenuOpen }" viewBox="0 0 24 24">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </button>

                    <!-- PROFILE DROPDOWN -->
                    <div v-if="isProfileMenuOpen" class="profile-dropdown">
                        <div class="profile-dropdown-info">
                            <div class="profile-avatar">
                                {{ userInitial }}
                            </div>

                            <div class="profile-details">
                                <router-link :to="{
                                    name: 'kontributor.profil',
                                    params: { id: user.id }
                                }" class="profile-name-link" @click="closeProfileMenu">
                                    {{ userName }}
                                </router-link>

                                <button type="button" class="profile-logout" @click="handleLogout">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <path d="m16 17 5-5-5-5" />
                                        <path d="M21 12H9" />
                                    </svg>

                                    <span>Logout</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- PAGE CONTENT -->
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

// =====================================================
// PROPS
// =====================================================

const props = defineProps({
    unreadCountOverride: {
        type: Number,
        default: null
    }
})

// =====================================================
// ROUTER
// =====================================================

const route = useRoute()
const router = useRouter()

// =====================================================
// USER INFORMATION
// =====================================================

const userRaw = localStorage.getItem('user_info')
const user = userRaw ? JSON.parse(userRaw) : null

const userName = computed(
    () => user?.nama_lengkap || user?.name || 'Kontributor'
)

const userInitial = computed(
    () => userName.value.charAt(0).toUpperCase() || 'K'
)

// =====================================================
// SIDEBAR STATE
// =====================================================

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

// =====================================================
// PROFILE DROPDOWN
// =====================================================

const isProfileMenuOpen = ref(false)

function toggleProfileMenu() {
    isProfileMenuOpen.value = !isProfileMenuOpen.value
}

function closeProfileMenu() {
    isProfileMenuOpen.value = false
}

// =====================================================
// ROUTE ACTIVE STATE
// =====================================================

function isRouteActive(path) {
    return route.path === path
}

function isRoutePrefix(prefix) {
    return route.path.startsWith(prefix)
}

// =====================================================
// NOTIFICATION
// =====================================================

const fetchedUnreadCount = ref(0)

const displayUnreadCount = computed(() => {
    if (props.unreadCountOverride !== null) {
        return props.unreadCountOverride
    }

    return fetchedUnreadCount.value
})

async function fetchUnreadCount() {
    try {
        const res = await api.kontributor.getNotifikasi()

        if (typeof res.data?.unread_count === 'number') {
            fetchedUnreadCount.value = res.data.unread_count
        }
    } catch {
        // Mengabaikan error pengambilan notifikasi
    }
}

function handleUnreadEvent(event) {
    if (typeof event.detail === 'number') {
        fetchedUnreadCount.value = event.detail
    } else {
        fetchUnreadCount()
    }
}

// Sinkronisasi jumlah notifikasi dari parent
watch(
    () => props.unreadCountOverride,
    (value) => {
        if (typeof value === 'number') {
            fetchedUnreadCount.value = value
        }
    }
)

// =====================================================
// AUTHENTICATION
// =====================================================

async function handleLogout() {
    closeMobileSidebar()
    closeProfileMenu()

    try {
        await api.auth.logout()
    } catch {
        // Tetap lanjutkan logout lokal
    }

    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_info')

    router.push({ name: 'login' })
}

// =====================================================
// LIFECYCLE
// =====================================================

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
</script>

<style scoped>
/* =====================================================
   LAYOUT
===================================================== */

.ktr-layout {
    display: flex;
    min-height: 100vh;
    background-color: var(--ktr-bg-page);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--ktr-text-main);
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

/* =====================================================
   SIDEBAR
===================================================== */

.ktr-sidebar {
    width: 280px;
    flex: 0 0 280px;
    height: 100vh;
    min-height: 100vh;
    position: sticky;
    top: 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-sizing: border-box;
    background: var(--ktr-green);
    transition: width 0.3s ease, flex-basis 0.3s ease;
}

.ktr-sidebar.collapsed {
    width: 80px;
    flex-basis: 80px;
}

/* Sidebar Brand */

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

.brand-link-home {
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.2s ease, transform 0.2s ease;
    display: block;
}

.brand-link-home:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.brand-title {
    margin: 0;
    color: var(--ktr-orange);
    font-size: 19px;
    font-weight: 700;
    letter-spacing: -0.3px;
}

/* SIDEBAR BRAND */

.sidebar-brand {
    min-height: 125px;
    padding: 20px 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    box-sizing: border-box;
}

.brand-info {
    flex: 1;
    min-width: 0;
    white-space: nowrap;
}

.brand-link-home {
    display: block;
    flex: 1;
    min-width: 0;
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.2s ease, transform 0.2s ease;
}

.brand-link-home:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.brand-title {
    margin: 0;
    color: #FF5722;
    font-size: 19px;
    font-weight: 700;
    letter-spacing: -0.3px;
}

.brand-tagline {
    display: block;
    color: #999999;
    font-size: 13px;
    font-style: italic;
    margin-top: -3px;
    margin-bottom: 5px;
}

.brand-sub {
    display: block;
    margin-top: 5px;
    color: var(--ktr-border-soft);
    font-size: 13px;
}

.toggle-sidebar {
    width: 36px;
    height: 36px;
    min-width: 36px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 9px;
    background: rgba(255, 255, 255, 0.12);
    color: var(--ktr-white);
    cursor: pointer;
    transition: background 0.2s ease;
}

.toggle-sidebar:hover {
    background: var(--ktr-teal);
}

/* Sidebar Navigation */

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
    border: none;
    border-radius: 9px;
    background: transparent;
    color: var(--ktr-border-soft);
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

/* Collapsed Sidebar */

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

/* =====================================================
   NOTIFICATION BADGE
===================================================== */

.nav-item-notifikasi {
    position: relative;
}

.notif-floating-badge {
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    margin-left: auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border-radius: 999px;
    background-color: var(--ktr-green-muted);
    color: var(--ktr-border-soft);
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 1;
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

.ktr-sidebar.collapsed .notif-floating-badge {
    position: absolute;
    top: 3px;
    right: 5px;
    min-width: 16px;
    height: 16px;
    padding: 0 3px;
    border: 2px solid var(--ktr-green);
    font-size: 10px;
}

/* =====================================================
   TOPBAR
===================================================== */

.ktr-topbar {
    position: sticky;
    top: 0;
    z-index: 999;

    min-height: 72px;
    height: 72px;
    padding: 0 1rem;

    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 30px;

    flex-shrink: 0;
    box-sizing: border-box;

    background: var(--ktr-bg-card, #ffffff);
    border-bottom: 1px solid var(--ktr-border-soft);
}

.topbar-left {
    display: flex;
    align-items: center;
}

/* Back Button */

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    padding: 8px 12px;
    margin-left: -10px;

    border: none;
    border-radius: 8px;
    background: transparent;

    color: #087d77;
    font-family: inherit;
    font-size: 0.9rem;
    font-weight: 600;

    cursor: pointer;
    transform: none;
    flex-shrink: 0;

    transition: all 0.2s ease;
}

.back-link svg {
    width: 19px;
    height: 19px;
    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.back-link:hover {
    background: #e6f4f1;
    color: #065f59;
}

/* Mobile Menu Button */

.mobile-menu-button {
    display: none;
    width: 42px;
    height: 42px;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 9px;
    background: var(--ktr-green);
    color: var(--ktr-white);
    cursor: pointer;
}

.mobile-menu-button svg {
    width: 24px;
    height: 24px;
    fill: none;
    stroke: currentColor;
    stroke-width: 2;
    stroke-linecap: round;
}

.mobile-menu-button:hover {
    background: var(--ktr-green-hover);
}

/* =====================================================
   USER PROFILE
===================================================== */

.topbar-user-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    margin-left: auto;
}

.topbar-user {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 7px 10px;
    border: none;
    border-radius: 10px;
    background: transparent;
    cursor: pointer;
    transition: background 0.2s ease;
}

.topbar-user:hover {
    background: #f1f5f9;
}

.user-avatar {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: var(--ktr-green);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
}

.user-name {
    max-width: 180px;
    overflow: hidden;
    color: var(--ktr-text-main);
    font-size: 14px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.profile-chevron {
    width: 16px;
    height: 16px;
    fill: none;
    stroke: #64748b;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
    transition: transform 0.2s ease;
}

.profile-chevron.rotated {
    transform: rotate(180deg);
}

/* =====================================================
   PROFILE DROPDOWN
===================================================== */

.profile-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    z-index: 1100;
    width: 260px;
    padding: 20px 22px;
    box-sizing: border-box;
    border: none;
    border-radius: 24px;
    background: #d9d9d9;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.10);
    animation: profileDropdownIn 0.2s ease;
}

.profile-dropdown-info {
    display: flex;
    align-items: center;
    gap: 11px;
}

.profile-avatar {
    width: 56px;
    height: 60px;
    min-width: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 18px;
    background: #14283b;
    color: #ffffff;
    font-size: 15px;
    font-weight: 700;
}

.profile-details {
    min-width: 0;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
}

.profile-name {
    max-width: 150px;
    overflow: hidden;
    color: #14283b;
    font-size: 15px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Profile Name Router Link */
.profile-name-link {
    display: inline-block;
    max-width: 170px;
    overflow: hidden;

    color: #14283b;
    font-family: inherit;
    font-size: 15px;
    font-weight: 700;

    text-decoration: none;
    text-overflow: ellipsis;
    white-space: nowrap;

    cursor: pointer;
    transition: color 0.2s ease;
}

.profile-name-link:hover {
    color: #087f78;
    text-decoration: underline;
}

.profile-name-link:focus-visible {
    outline: 2px solid #087f78;
    outline-offset: 3px;
    border-radius: 3px;
}

.profile-logout {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 0;
    border: none;
    background: transparent;
    color: #087f78;
    font-family: inherit;
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: color 0.2s ease;
}

.profile-logout svg {
    width: 19px;
    height: 19px;
    fill: none;
    stroke: #222222;
    stroke-width: 2.5;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.profile-logout:hover {
    color: #065f59;
}

.profile-logout:hover svg {
    stroke: #065f59;
}

/* =====================================================
   MOBILE OVERLAY
===================================================== */

.sidebar-overlay {
    display: none;
}

/* =====================================================
   RESPONSIVE - TABLET
===================================================== */

@media (max-width: 960px) {
    .ktr-layout {
        flex-direction: row;
        min-height: 100vh;
    }

    .ktr-topbar {
        min-height: 62px;
        height: 62px;
        padding: 0 1.5rem;
    }

    .mobile-menu-button {
        display: flex;
    }

    .ktr-sidebar,
    .ktr-sidebar.collapsed {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1001;

        width: min(280px, 85vw);
        flex: 0 0 auto;

        height: 100vh;
        height: 100dvh;
        min-height: 0;

        overflow-x: hidden;
        overflow-y: auto;

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
        border: none;
        font-size: 0.75rem;
    }

    .sidebar-overlay {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: block;
        width: 100%;
        height: 100%;
        padding: 0;
        border: none;
        background: rgba(0, 0, 0, 0.45);
        cursor: pointer;
    }

    .ktr-main-wrap {
        width: 100%;
        min-width: 0;
    }

    .ktr-content {
        padding: 1.5rem;
    }
}

/* =====================================================
   RESPONSIVE - MOBILE
===================================================== */

@media (max-width: 600px) {
    .ktr-topbar {
        min-height: 58px;
        height: 58px;
        padding: 0 1rem;
    }

    .mobile-menu-button {
        width: 38px;
        height: 38px;
    }

    .topbar-user {
        gap: 6px;
        padding: 5px;
    }

    .user-avatar {
        width: 34px;
        height: 34px;
    }

    .user-name {
        max-width: 100px;
        font-size: 12px;
    }

    .profile-dropdown {
        width: 250px;
        max-width: calc(100vw - 24px);
    }

    .profile-name {
        max-width: 145px;
    }

    .profile-name-link {
        max-width: 145px;
        font-size: 14px;
    }

    .ktr-content {
        padding: 1.15rem 1rem 1.5rem;
    }
}

/* =====================================================
   ANIMATION
===================================================== */

@keyframes profileDropdownIn {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>