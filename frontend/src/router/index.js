import { createRouter, createWebHistory } from 'vue-router'

import HomeView from '../views/HomeView.vue'
import LoginView from '../views/auth/LoginView.vue'
import RegisterKontributorView from '../views/auth/RegisterKontributorView.vue'
import PendingNoticeView from '../views/auth/PendingNoticeView.vue'
import KirimNaskahView from '../views/guest/KirimNaskahView.vue'
import DashboardKontributorView from '../views/kontributor/DashboardKontributorView.vue'
import ReviewNaskahView from '../views/admin/ReviewNaskahView.vue'
import UserManagementView from '../views/admin/UserManagementView.vue'

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeView,
    },
    {
        path: '/login',
        name: 'login',
        component: LoginView,
        meta: { guestOnly: true },
    },
    {
        path: '/daftar-kontributor',
        name: 'register.kontributor',
        component: RegisterKontributorView,
        meta: { guestOnly: true },
    },
    {
        path: '/menunggu-verifikasi',
        name: 'kontributor.pending',
        component: PendingNoticeView,
    },
    {
        path: '/guest/kirim-naskah',
        name: 'guest.kirim',
        component: KirimNaskahView,
    },
    {
        path: '/kontributor/dashboard',
        name: 'kontributor.dashboard',
        component: DashboardKontributorView,
        meta: { requiresAuth: true, role: 'kontributor' },
    },
    {
        path: '/admin/review-naskah',
        name: 'admin.review',
        component: ReviewNaskahView,
        meta: { requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/users',
        name: 'admin.users',
        component: UserManagementView,
        meta: { requiresAuth: true, role: 'admin' },
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token')
    const userRaw = localStorage.getItem('user_info')
    const user = userRaw ? JSON.parse(userRaw) : null

    if (to.meta.requiresAuth && !token) {
        return next({ name: 'login' })
    }

    if (to.meta.role && user?.role !== to.meta.role) {
        if (user?.role === 'admin') return next({ name: 'admin.review' })
        if (user?.role === 'kontributor') return next({ name: 'kontributor.dashboard' })
        return next({ name: 'home' })
    }

    if (to.meta.guestOnly && token) {
        if (user?.role === 'admin') return next({ name: 'admin.review' })
        if (user?.role === 'kontributor') return next({ name: 'kontributor.dashboard' })
    }

    next()
})

export default router
