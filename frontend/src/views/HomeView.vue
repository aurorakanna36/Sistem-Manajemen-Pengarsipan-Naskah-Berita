<template>
    <div class="home-page">
        <nav class="navbar">
            <div class="navbar-brand">
                <div class="nav-logo">HC</div>
                <span>Harian Cakrawala</span>
            </div>
            <div class="navbar-actions">
                <router-link to="/guest/kirim-naskah" class="btn-outline">Kirim Naskah</router-link>
                <router-link v-if="!isLoggedIn" to="/login" class="btn-primary">Masuk</router-link>
                <router-link v-else :to="dashboardRoute" class="btn-primary">Dashboard</router-link>
            </div>
        </nav>

        <header class="hero">
            <h1>Portal Berita & Pengarsipan Naskah</h1>
            <p>Temukan berita terkini dari kontributor Harian Cakrawala</p>
        </header>

        <main class="main-content">
            <div v-if="loading" class="loading-state">Memuat berita...</div>

            <div v-else-if="beritaList.length === 0" class="empty-state">
                <p>Belum ada berita yang diterbitkan.</p>
            </div>

            <div v-else class="berita-grid">
                <article
                    v-for="berita in beritaList"
                    :key="berita.id"
                    class="berita-card"
                >
                    <div class="card-badge">{{ berita.kategori }}</div>
                    <h2 class="card-title">{{ berita.judul }}</h2>
                    <p v-if="berita.abstrak" class="card-abstrak">{{ berita.abstrak }}</p>
                    <div class="card-meta">
                        <span>{{ berita.penulis }}</span>
                        <span>{{ formatDate(berita.created_at) }}</span>
                    </div>
                </article>
            </div>
        </main>

        <footer class="footer">
            <p>Harian Cakrawala &copy; {{ new Date().getFullYear() }}</p>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../api/index.js'

const beritaList = ref([])
const loading = ref(true)
const isLoggedIn = computed(() => !!localStorage.getItem('auth_token'))
const userInfo = computed(() => {
    const raw = localStorage.getItem('user_info')
    return raw ? JSON.parse(raw) : null
})
const dashboardRoute = computed(() => {
    if (userInfo.value?.role === 'admin') return '/admin/review-naskah'
    return '/kontributor/dashboard'
})

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
}

onMounted(async () => {
    try {
        const response = await api.public.getBerita()
        beritaList.value = response.data.data
    } catch {
        beritaList.value = []
    } finally {
        loading.value = false
    }
})
</script>

<style scoped>
.home-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f8fafc;
    font-family: 'Inter', sans-serif;
}

.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 2rem;
    background: white;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 10;
}

.navbar-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 700;
    font-size: 1rem;
    color: #0f172a;
}

.nav-logo {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #0f4c81, #1e3a5f);
    border-radius: 8px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 700;
}

.navbar-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.btn-outline {
    padding: 0.5rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 7px;
    font-size: 0.875rem;
    color: #374151;
    font-weight: 500;
    transition: border-color 0.2s;
}

.btn-outline:hover { border-color: #94a3b8; }

.btn-primary {
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #0f4c81, #1e3a5f);
    color: white;
    border-radius: 7px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: opacity 0.2s;
}

.btn-primary:hover { opacity: 0.9; }

.hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f4c81 100%);
    color: white;
    text-align: center;
    padding: 4rem 2rem;
}

.hero h1 {
    font-size: 2.25rem;
    font-weight: 700;
    margin: 0 0 0.75rem;
}

.hero p {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1rem;
    margin: 0;
}

.main-content {
    flex: 1;
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
    padding: 2.5rem 2rem;
}

.loading-state, .empty-state {
    text-align: center;
    color: #94a3b8;
    padding: 4rem;
    font-size: 0.95rem;
}

.berita-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.berita-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    transition: box-shadow 0.2s, transform 0.2s;
}

.berita-card:hover {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    transform: translateY(-2px);
}

.card-badge {
    display: inline-block;
    background: #eff6ff;
    color: #0f4c81;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 0.6rem;
    line-height: 1.4;
}

.card-abstrak {
    color: #64748b;
    font-size: 0.875rem;
    line-height: 1.6;
    margin: 0 0 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.78rem;
    color: #94a3b8;
    border-top: 1px solid #f1f5f9;
    padding-top: 0.75rem;
    margin-top: auto;
}

.footer {
    text-align: center;
    padding: 1.5rem;
    color: #94a3b8;
    font-size: 0.825rem;
    border-top: 1px solid #e2e8f0;
}
</style>
