import { createRouter, createWebHistory } from 'vue-router'

import HomeView from '../views/HomeView.vue'
import LoginView from '../views/auth/LoginView.vue'
import RegisterKontributorView from '../views/auth/RegisterKontributorView.vue'
import PendingNoticeView from '../views/auth/PendingNoticeView.vue'
import KirimNaskahView from '../views/guest/KirimNaskahView.vue'
import DashboardKontributorView from '../views/kontributor/DashboardKontributorView.vue'

import AdminDashboardView from '../views/admin/AdminDashboardView.vue'
import KelolaKontributorView from '../views/admin/KelolaKontributorView.vue'
import DetailKontributorView from '../views/admin/DetailKontributorView.vue'
import ReviewNaskahView from '../views/admin/ReviewNaskahView.vue'
import DetailReviewNaskahView from '../views/admin/DetailReviewNaskahView.vue'
import KelolaNaskahView from '../views/admin/KelolaNaskahView.vue'

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
        path: '/admin',
        redirect: '/admin/dashboard',
    },
    {
        path: '/admin/dashboard',
        name: 'admin.dashboard',
        component: AdminDashboardView,
        meta: { requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/kontributor',
        name: 'admin.kontributor',
        component: KelolaKontributorView,
        meta: { requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/users',
        redirect: '/admin/kontributor',
    },
    {
        path: '/admin/kontributor/:id',
        name: 'admin.kontributor.detail',
        component: DetailKontributorView,
        meta: { requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/review-naskah',
        name: 'admin.review',
        component: ReviewNaskahView,
        meta: { requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/review-naskah/:id',
        name: 'admin.review.detail',
        component: DetailReviewNaskahView,
        meta: { requiresAuth: true, role: 'admin' },
    },
    {
        path: '/admin/kelola-naskah',
        name: 'admin.kelola.naskah',
        component: KelolaNaskahView,
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
        if (user?.role === 'admin') return next({ name: 'admin.dashboard' })
        if (user?.role === 'kontributor') return next({ name: 'kontributor.dashboard' })
        return next({ name: 'home' })
    }

    if (to.meta.guestOnly && token) {
        if (user?.role === 'admin') return next({ name: 'admin.dashboard' })
        if (user?.role === 'kontributor') return next({ name: 'kontributor.dashboard' })
    }

    next()
})

export default router
