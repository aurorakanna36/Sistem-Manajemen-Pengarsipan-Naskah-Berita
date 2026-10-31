<template>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                <h2 class="brand-title">Harian Cakrawala</h2>
                <span class="brand-sub">Admin</span>
            </div>

            <nav class="sidebar-nav">
                <router-link to="/admin/dashboard" class="nav-item" active-class="active">
                    Dashboard
                </router-link>
                <router-link to="/admin/kontributor" class="nav-item" active-class="active">
                    Kelola Kontributor
                </router-link>
                <router-link to="/admin/review-naskah" class="nav-item" active-class="active">
                    Review Naskah
                </router-link>
                <router-link to="/admin/kelola-naskah" class="nav-item" active-class="active">
                    Kelola Naskah
                </router-link>
                <button class="nav-item nav-logout" @click="handleLogout">
                    Keluar
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
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api/index.js'

const router = useRouter()
const userRaw = localStorage.getItem('user_info')
const user = userRaw ? JSON.parse(userRaw) : null

const userName = computed(() => user?.nama_lengkap || user?.name || 'Admin')
const userInitial = computed(() => userName.value.charAt(0).toUpperCase() || 'A')

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
.admin-layout {
    display: flex;
    min-height: 100vh;
    background-color: var(--adm-bg-page);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: var(--adm-text-main);
}

.admin-sidebar {
    width: 250px;
    background-color: var(--adm-navy);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    min-height: 100vh;
    position: sticky;
    top: 0;
}

.sidebar-brand {
    padding: 2rem 1.75rem 1.5rem;
}

.brand-title {
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

.brand-sub {
    color: var(--adm-border-soft);
    font-size: 0.85rem;
    margin-top: 0.25rem;
    display: inline-block;
}

.sidebar-nav {
    display: flex;
    flex-direction: column;
    padding: 0.5rem 1rem;
    gap: 0.35rem;
}

.nav-item {
    display: block;
    padding: 0.75rem 1rem;
    color: var(--adm-border-soft);
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
    background-color: var(--adm-navy-hover);
    color: #ffffff;
}

.nav-item.active {
    background-color: var(--adm-teal);
    color: #ffffff;
    font-weight: 600;
}

.nav-logout {
    margin-top: 1.5rem;
    color: var(--adm-border-soft);
}

.nav-logout:hover {
    background-color: rgba(239, 68, 68, 0.15);
    color: #fca5a5;
}

.admin-main-wrap {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.admin-topbar {
    height: 72px;
    background: transparent;
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
    background-color: var(--adm-navy);
    color: #ffffff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.95rem;
}

.user-name {
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

