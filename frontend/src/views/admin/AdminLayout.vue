<template>
    <div class="admin-layout">

        <!-- MOBILE SIDEBAR OVERLAY -->
        <button v-if="isMobileSidebarOpen" class="sidebar-overlay" aria-label="Tutup menu"
            @click="closeMobileSidebar"></button>

        <!-- SIDEBAR -->
        <aside :class="[
            'admin-sidebar',
            {
                collapsed: isSidebarCollapsed,
                'mobile-open': isMobileSidebarOpen
            }
        ]">
            <!-- BRAND -->
            <div class="sidebar-brand">
                <router-link to="/" v-if="!isSidebarCollapsed" class="brand-info brand-link-home"
                    title="Kembali ke Beranda">
                    <h2 class="brand-title">Harian Cakrawala</h2>
                    <span class="brand-tagline">Berita Peristiwa Indonesia Terkini</span>
                    <span class="brand-sub">Admin</span>
                </router-link>

                <button class="toggle-sidebar" @click="toggleSidebar"
                    :title="isSidebarCollapsed ? 'Tampilkan Sidebar' : 'Sembunyikan Sidebar'">
                    <svg v-if="isSidebarCollapsed" width="21" height="21" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>

                    <svg v-else width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m15 18-6-6 6-6" />
                    </svg>
                </button>
            </div>

            <!-- NAVIGATION -->
            <nav class="sidebar-nav">
                <router-link to="/admin/dashboard" class="nav-item" active-class="active" title="Dashboard"
                    @click="closeMobileSidebar">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1z" />
                    </svg>
                    <span v-if="!isSidebarCollapsed">Dashboard</span>
                </router-link>

                <router-link to="/admin/kontributor" class="nav-item" active-class="active" title="Kelola Kontributor"
                    @click="closeMobileSidebar">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="10" cy="7" r="4" />
                        <path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    <span v-if="!isSidebarCollapsed">Kelola Kontributor</span>
                </router-link>

                <router-link to="/admin/kelola-naskah" class="nav-item" active-class="active" title="Kelola Naskah"
                    @click="closeMobileSidebar">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4z" />
                    </svg>
                    <span v-if="!isSidebarCollapsed">Kelola Naskah</span>
                </router-link>
            </nav>
        </aside>

        <!-- MAIN WRAPPER -->
        <div class="admin-main-wrap" @click="closeProfileMenu">

            <!-- TOPBAR -->
            <header class="admin-topbar">

                <div class="topbar-left">
                    <button class="mobile-menu-button" @click="openMobileSidebar" aria-label="Buka menu">
                        <svg viewBox="0 0 24 24">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- BACK BUTTON -->
                <button class="back-link" @click="router.back()">
                    <svg viewBox="0 0 24 24">
                        <path d="M19 12H5" />
                        <path d="m12 19-7-7 7-7" />
                    </svg>
                    <span>Kembali</span>
                </button>

                <!-- USER PROFILE -->
                <div class="topbar-user-wrapper" @click.stop>

                    <button class="topbar-user" @click="toggleProfileMenu" :aria-expanded="isProfileMenuOpen"
                        aria-label="Buka menu profil">
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
            </header>

            <!-- PAGE CONTENT -->
            <main class="admin-content">
                <slot />
            </main>

        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/index.js'

const router = useRouter()

// USER INFORMATION
const userRaw = localStorage.getItem('user_info')
const user = userRaw ? JSON.parse(userRaw) : null

const userName = computed(
    () => user?.nama_lengkap || user?.name || 'Admin'
)

const userInitial = computed(
    () => userName.value.charAt(0).toUpperCase() || 'A'
)

// SIDEBAR STATE
const isSidebarCollapsed = ref(false)
const isMobileSidebarOpen = ref(false)

// PROFILE DROPDOWN STATE
const isProfileMenuOpen = ref(false)

// SIDEBAR FUNCTIONS
function toggleSidebar() {
    if (window.innerWidth <= 900) {
        closeMobileSidebar()
        return
    }

    isSidebarCollapsed.value = !isSidebarCollapsed.value
}

function openMobileSidebar() {
    isMobileSidebarOpen.value = true
}

function closeMobileSidebar() {
    isMobileSidebarOpen.value = false
}

// PROFILE FUNCTIONS
function toggleProfileMenu() {
    isProfileMenuOpen.value = !isProfileMenuOpen.value
}

function closeProfileMenu() {
    isProfileMenuOpen.value = false
}

// LOGOUT
async function handleLogout() {
    closeMobileSidebar()
    closeProfileMenu()

    try {
        await api.auth.logout()
    } catch (error) {
        console.error('Logout API gagal:', error)
    }

    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_info')

    router.push({ name: 'login' })
}
</script>

<style scoped>
/* ==============================
   MAIN LAYOUT
============================== */
.admin-layout {
    display: flex;
    min-height: 100vh;
    background-color: var(--adm-bg-page);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont,
        'Segoe UI', Roboto, sans-serif;
    color: var(--adm-text-main);
}

/* ==============================
   SIDEBAR
============================== */
.admin-sidebar {
    width: 280px;
    flex: 0 0 280px;
    height: 100vh;
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    overflow-x: hidden;
    box-sizing: border-box;
    background: var(--adm-green);
    transition: width 0.3s ease, flex-basis 0.3s ease;
}

.admin-sidebar.collapsed {
    width: 80px;
    flex-basis: 80px;
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
    min-width: 0;
    flex: 1;
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
    color: #c6ded6;
    font-size: 13px;
}

/* SIDEBAR TOGGLE */
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
    color: #fff;
    cursor: pointer;
}

.toggle-sidebar:hover {
    background: var(--adm-teal);
}

/* SIDEBAR NAVIGATION */
.sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 12px;
}

.nav-item {
    width: 100%;
    min-height: 48px;
    padding: 0 14px;
    display: flex;
    align-items: center;
    gap: 13px;
    box-sizing: border-box;
    border: none;
    border-radius: 9px;
    background: transparent;
    color: #d5e6df;
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
    background: var(--adm-green-hover);
    color: #fff;
}

.nav-item.active {
    background: var(--adm-teal);
    color: #fff;
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

/* COLLAPSED SIDEBAR */
.admin-sidebar.collapsed .sidebar-brand {
    padding: 20px 0;
    justify-content: center;
}

.admin-sidebar.collapsed .sidebar-nav {
    padding: 12px 10px;
}

.admin-sidebar.collapsed .nav-item {
    justify-content: center;
    padding: 0;
    gap: 0;
}

/* ==============================
   MAIN WRAPPER
============================== */
.admin-main-wrap {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

/* ==============================
   TOPBAR
============================== */
.admin-topbar {
    position: sticky;
    top: 0;
    z-index: 999;
    height: 72px;
    min-height: 72px;
    padding: 0 2.5rem;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 30px;
    flex-shrink: 0;
    box-sizing: border-box;
    background: var(--adm-bg-card);
    border-bottom: 1px solid var(--adm-border);
}

.topbar-left {
    display: flex;
    align-items: center;
}

/* BACK BUTTON */
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #087d77;
    font-family: inherit;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transform: translateX(-30px);
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

/* MOBILE MENU BUTTON */
.mobile-menu-button {
    display: none;
    width: 42px;
    height: 42px;
    align-items: center;
    justify-content: center;
    border: none;
    border-radius: 9px;
    background: transparent;
    color: var(--adm-text-main);
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

/* ==============================
   USER PROFILE
============================== */
.topbar-user-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    margin-left: auto;
}

.topbar-user {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 0;
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
    background-color: var(--adm-green);
    color: #fff;
    font-size: 0.95rem;
    font-weight: 700;
}

.user-name {
    max-width: 180px;
    overflow: hidden;
    color: var(--adm-text-main);
    font-size: 0.95rem;
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

/* PROFILE DROPDOWN */
.profile-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    z-index: 1100;
    width: 120px;
    padding: 20px 22px;
    box-sizing: border-box;
    border-radius: 24px;
    background: #d9d9d9;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.10);
    animation: profileDropdownIn 0.2s ease;
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
    text-align: left;
    cursor: pointer;
    transition: color 0.2s ease;
}

.profile-logout svg {
    width: 19px;
    height: 19px;
    fill: none;
    stroke: #222;
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

/* DROPDOWN ANIMATION */
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

/* ==============================
   PAGE CONTENT
============================== */
.admin-content {
    flex: 1;
    min-width: 0;
    padding: 1.75rem 2.5rem 3rem;
    box-sizing: border-box;
}

/* ==============================
   OVERLAY
============================== */
.sidebar-overlay {
    display: none;
}

/* ==============================
   TABLET & MOBILE
============================== */
@media (max-width: 900px) {
    .admin-sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 1001;
        width: min(280px, 85vw);
        height: 100dvh;
        flex-basis: auto;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        box-shadow: 5px 0 25px rgba(0, 0, 0, 0.15);
    }

    .admin-sidebar.collapsed {
        width: min(280px, 85vw);
        flex-basis: auto;
    }

    .admin-sidebar.mobile-open {
        transform: translateX(0);
    }

    .admin-sidebar.collapsed .sidebar-brand {
        padding: 25px 20px;
        justify-content: space-between;
    }

    .admin-sidebar.collapsed .sidebar-nav {
        padding: 12px;
    }

    .admin-sidebar.collapsed .nav-item {
        justify-content: flex-start;
        padding: 0 14px;
        gap: 13px;
    }

    .admin-sidebar.collapsed .brand-info {
        display: block;
    }

    .admin-sidebar.collapsed .nav-item span {
        display: inline;
    }

    .mobile-menu-button {
        display: flex;
    }

    .sidebar-overlay {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: block;
        width: 100%;
        height: 100%;
        border: none;
        background: rgba(0, 0, 0, 0.45);
        cursor: pointer;
    }

    .admin-topbar {
        padding: 0 1.5rem;
    }

    .admin-content {
        padding: 1.5rem 1.5rem 2rem;
    }
}

/* ==============================
   SMALL MOBILE
============================== */
@media (max-width: 600px) {
    .admin-topbar {
        min-height: 62px;
        height: 62px;
        padding: 0 1rem;
    }

    .admin-content {
        padding: 1.15rem 1rem 1.5rem;
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
        font-size: 0.85rem;
    }

    .profile-dropdown {
        width: 246px;
        max-width: calc(100vw - 24px);
    }
}

/* ==============================
   VERY SMALL MOBILE
============================== */
@media (max-width: 380px) {
    .admin-content {
        padding-right: 0.75rem;
        padding-left: 0.75rem;
    }
}
</style>
