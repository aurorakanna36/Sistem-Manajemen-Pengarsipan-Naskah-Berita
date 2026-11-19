import { createRouter, createWebHistory } from "vue-router";

import HomeView from "../views/HomeView.vue";
import LoginView from "../views/auth/LoginView.vue";
import RegisterKontributorView from "../views/auth/RegisterKontributorView.vue";
import KirimNaskahView from "../views/guest/KirimNaskahView.vue";
import DashboardKontributorView from "../views/kontributor/DashboardKontributorView.vue";
import KirimNaskahKontributorView from "../views/kontributor/KirimNaskahKontributorView.vue";
import NaskahSayaView from "../views/kontributor/NaskahSayaView.vue";
import NotifikasiKontributorView from "../views/kontributor/NotifikasiKontributorView.vue";
import DetailNaskahKontributorView from "../views/kontributor/DetailNaskahKontributorView.vue";

import AdminDashboardView from "../views/admin/AdminDashboardView.vue";
import KelolaKontributorView from "../views/admin/KelolaKontributorView.vue";
import DetailKontributorView from "../views/admin/DetailKontributorView.vue";
import DetailReviewNaskahView from "../views/admin/DetailReviewNaskahView.vue";
import KelolaNaskahView from "../views/admin/KelolaNaskahView.vue";
import RevisiNaskahKontributorView from '../views/kontributor/RevisiNaskahKontributorView.vue'

const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView,
  },
  {
    path: "/login",
    name: "login",
    component: LoginView,
    meta: { guestOnly: true },
  },
  {
    path: "/daftar-kontributor",
    name: "register.kontributor",
    component: RegisterKontributorView,
    meta: { guestOnly: true },
  },
  {
    path: "/menunggu-verifikasi",
    redirect: "/login",
  },
  {
    path: "/guest/kirim-naskah",
    name: "guest.kirim",
    component: KirimNaskahView,
  },
  {
    path: "/kontributor",
    redirect: "/kontributor/dashboard",
  },
  {
    path: "/kontributor/dashboard",
    name: "kontributor.dashboard",
    component: DashboardKontributorView,
    meta: { requiresAuth: true, role: "kontributor" },
  },
  {
    path: "/kontributor/kirim-naskah",
    name: "kontributor.kirim",
    component: KirimNaskahKontributorView,
    meta: { requiresAuth: true, role: "kontributor" },
  },
  {
    path: "/kontributor/naskah-saya",
    name: "kontributor.naskah",
    component: NaskahSayaView,
    meta: { requiresAuth: true, role: "kontributor" },
  },
  {
    path: "/kontributor/notifikasi",
    name: "kontributor.notifikasi",
    component: NotifikasiKontributorView,
    meta: { requiresAuth: true, role: "kontributor" },
  },
  {
    path: "/kontributor/naskah/:id",
    name: "kontributor.naskah.detail",
    component: DetailNaskahKontributorView,
    meta: { requiresAuth: true, role: "kontributor" },
  },
  {
    path: "/admin",
    redirect: "/admin/dashboard",
  },
  {
    path: "/admin/dashboard",
    name: "admin.dashboard",
    component: AdminDashboardView,
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/kontributor",
    name: "admin.kontributor",
    component: KelolaKontributorView,
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/users",
    redirect: "/admin/kontributor",
  },
  {
    path: "/admin/kontributor/:id",
    name: "admin.kontributor.detail",
    component: DetailKontributorView,
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/review-naskah/:id",
    name: "admin.review.detail",
    component: DetailReviewNaskahView,
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/kelola-naskah",
    name: "admin.kelola.naskah",
    component: KelolaNaskahView,
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: '/admin/verifikasi-kontributor/:id',
    name: 'admin.verifikasi.kontributor',
    component: () => import('@/views/admin/VerifikasiKontributor.vue')
  },
  {
    path: "/kontributor/naskah/:id/revisi",
    name: "revisi.naskah.kontributor",
    component: RevisiNaskahKontributorView,
    meta: { requiresAuth: true, role: "kontributor" },
  },
  {
    path: '/kontributor/profil/:id',
    name: 'kontributor.profil',
    component: () => import('@/views/kontributor/ProfileKontributorView.vue'),
    meta: {
      requiresAuth: true,
      role: 'kontributor'
    }
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("auth_token");
  const userRaw = localStorage.getItem("user_info");
  const user = userRaw ? JSON.parse(userRaw) : null;

  // 1. Jika rute khusus tamu (login / register) tetapi user sudah login
  if (to.meta.guestOnly && token) {
    if (user?.role === "admin") {
      return next({ name: "admin.dashboard" });
    }
    if (user?.role === "kontributor") {
      if (user?.status_verifikasi === "diverifikasi" || user?.status_verifikasi === "verified") {
        return next({ name: "kontributor.dashboard" });
      }
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user_info");
      return next({ name: "login" });
    }
    return next({ name: "home" });
  }

  // 2. Jika rute butuh login tetapi belum ada token
  if (to.meta.requiresAuth && !token) {
    return next({ name: "login" });
  }

  // 3. Jika rute memiliki batasan role tertentu
  if (to.meta.role && user?.role !== to.meta.role) {
    if (user?.role === "admin") return next({ name: "admin.dashboard" });
    if (user?.role === "kontributor") {
      if (user?.status_verifikasi === "diverifikasi" || user?.status_verifikasi === "verified") {
        return next({ name: "kontributor.dashboard" });
      }
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user_info");
      return next({ name: "login" });
    }
    return next({ name: "home" });
  }

  // 4. GATE KONTRIBUTOR: Cegah akses dashboard/kirim naskah jika status masih pending/belum terverifikasi
  if (token && user?.role === "kontributor" && to.path.startsWith("/kontributor")) {
    const isVerified = user?.status_verifikasi === "diverifikasi" || user?.status_verifikasi === "verified";
    if (!isVerified) {
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user_info");
      return next({ name: "login" });
    }
  }

  next();
});

export default router;