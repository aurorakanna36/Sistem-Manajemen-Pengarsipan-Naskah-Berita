<template>
    <div class="admin-layout">

        <aside :class="['admin-sidebar', { collapsed: isSidebarCollapsed }]">

            <div class="sidebar-brand">

                <div v-if="!isSidebarCollapsed" class="brand-info">
                    <h2 class="brand-title">Harian Cakrawala</h2>
                    <span class="brand-sub">Admin</span>
                </div>

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

            <nav class="sidebar-nav">

                <router-link to="/admin/dashboard" class="nav-item" active-class="active" title="Dashboard">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1z" />
                    </svg>
                    <span v-if="!isSidebarCollapsed">Dashboard</span>
                </router-link>

                <router-link to="/admin/kontributor" class="nav-item" active-class="active" title="Kelola Kontributor">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                        <circle cx="10" cy="7" r="4" />
                        <path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    <span v-if="!isSidebarCollapsed">Kelola Kontributor</span>
                </router-link>

                <router-link to="/admin/review-naskah" class="nav-item" active-class="active" title="Review Naskah">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                        <path d="M14 2v6h6M8 13h8M8 17h8" />
                    </svg>
                    <span v-if="!isSidebarCollapsed">Review Naskah</span>
                </router-link>

                <router-link to="/admin/kelola-naskah" class="nav-item" active-class="active" title="Kelola Naskah">
                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="M12 20h9" />
                        <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4z" />
                    </svg>
                    <span v-if="!isSidebarCollapsed">Kelola Naskah</span>
                </router-link>

                <button class="nav-item nav-logout" @click="handleLogout" title="Keluar">

                    <svg viewBox="0 0 24 24" class="menu-icon">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <path d="m16 17 5-5-5-5" />
                        <path d="M21 12H9" />
                    </svg>

                    <span v-if="!isSidebarCollapsed">Keluar</span>
                </button>

            </nav>
        </aside>


        <div class="admin-main-wrap">
            <header class="admin-topbar">
                <div class="topbar-user">
                    <div class="user-avatar">
                        {{ userInitial }}
                    </div>
                    <span class="user-name">{{ userName }}</span>
                </div>
            </header>

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
const userRaw = localStorage.getItem('user_info')
const user = userRaw ? JSON.parse(userRaw) : null

const userName = computed(() => user?.nama_lengkap || user?.name || 'Admin')
const userInitial = computed(() => userName.value.charAt(0).toUpperCase() || 'A')

const isSidebarCollapsed = ref(false)

async function handleLogout() {
    try {
        await api.auth.logout()
    } catch { }
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user_info')
    router.push({ name: 'login' })
}

function toggleSidebar() {
    isSidebarCollapsed.value = !isSidebarCollapsed.value
}
</script>

<style scoped>
.admin-layout {
    display: flex;
    min-height: 100vh;
    background-color: var(--adm-bg-page);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--adm-text-main);
}

.admin-sidebar {
    width: 260px;
    flex: 0 0 260px;
    min-height: 100vh;
    background: var(--adm-green);
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
    overflow: hidden;
    transition: width 0.3s ease, flex-basis 0.3s ease;
    box-sizing: border-box;
}

.admin-sidebar.collapsed {
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
    color: #fff;
    font-size: 19px;
    font-weight: 700;
    margin: 0;
    letter-spacing: -0.3px;
}

.brand-sub {
    display: block;
    color: #c6ded6;
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
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s ease;
}

.toggle-sidebar:hover {
    background: var(--adm-teal);
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
    gap: 13px;
    box-sizing: border-box;

    color: #d5e6df;
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

.nav-logout {
    margin-top: 22px;
    color: #d5e6df;
}

.nav-logout:hover {
    background: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
}

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

.admin-sidebar.collapsed .nav-logout {
    margin-top: 22px;
}

.admin-main-wrap {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.admin-topbar {
    height: 64px;
    background: var(--adm-bg-card);
    border-bottom: 1px solid var(--adm-border);
    height: 72px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 2.5rem;
}

.topbar-user {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 38px;
    height: 38px;
    background-color: var(--adm-teal);
    background-color: var(--adm-green);
    color: #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.95rem;
}

.user-name {
    font-weight: 600;
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--adm-text-main);
}

.admin-content {
    flex: 1;
    padding: 0 2.5rem 3rem;
}

@media (max-width: 900px) {
    .admin-layout {
        flex-direction: column;
    }

    .admin-sidebar {
        width: 100%;
        min-height: auto;
        position: static;
    }

    .admin-topbar {
        padding: 0 1.5rem;
    }

    .admin-content {
        padding: 0 1.5rem 2rem;
    }
}
</style>
