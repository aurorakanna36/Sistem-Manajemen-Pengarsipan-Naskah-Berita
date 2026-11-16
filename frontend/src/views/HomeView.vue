<template>
    <div class="home-page">
        <!-- TOP NAVBAR -->
        <header class="top-header">
            <div class="header-container">
                <!-- BRAND -->
                <router-link to="/" class="brand-link">
                    <div class="brand-text">
                        <span class="brand-title">Harian Cakrawala</span>
                        <span class="brand-tagline">Berita Peristiwa Indonesia Terkini</span>
                    </div>
                </router-link>

                <!-- SEARCH PILL BAR -->
                <div class="search-wrapper">
                    <div class="search-pill">
                        <button type="button" class="btn-search-icon" aria-label="Cari">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </button>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search Text"
                            class="search-input"
                        />
                        <button
                            v-if="searchQuery"
                            type="button"
                            class="btn-clear-search"
                            @click="searchQuery = ''"
                            title="Hapus pencarian"
                        >
                            &times;
                        </button>
                    </div>
                </div>

                <!-- AUTH / ACTION BUTTONS -->
                <div class="header-actions">
                    <template v-if="!isLoggedIn">
                        <router-link to="/login" class="btn-action-outline">Masuk</router-link>
                        <router-link to="/daftar-kontributor" class="btn-action-solid">Daftar</router-link>
                    </template>
                    <template v-else>
                        <router-link :to="dashboardRoute" class="btn-action-solid">
                            Dashboard
                        </router-link>
                    </template>
                </div>
            </div>
        </header>

        <!-- SUB-NAVBAR KATEGORI -->
        <nav class="category-nav">
            <div class="category-container">
                <button
                    type="button"
                    class="category-link"
                    :class="{ active: selectedCategory === '' }"
                    @click="selectedCategory = ''"
                >
                    SEMUA
                </button>
                <button
                    v-for="cat in availableCategories"
                    :key="cat"
                    type="button"
                    class="category-link"
                    :class="{ active: selectedCategory.toLowerCase() === cat.toLowerCase() }"
                    @click="setCategory(cat)"
                >
                    {{ cat.toUpperCase() }}
                </button>
            </div>
        </nav>

        <!-- HERO WELCOME SECTION -->
        <section class="hero-welcome">
            <div class="hero-content">
                <h1 class="hero-title">Selamat Datang Di Portal Harian Cakrawala</h1>
                <p class="hero-subtitle">Kirimkan naskah anda dan jadi bagian dari kami</p>
            </div>
        </section>

        <!-- MAIN MEDIA CONTENT WRAPPER -->
        <main class="main-body">
            <!-- SECTION 1: BERITA TERBARU (1 HERO + 2x2 GRID) -->
            <section class="section-block">
                <div class="section-header-row">
                    <h2 class="section-heading">Berita Terbaru</h2>
                    <a
                        href="https://hariancakrawala.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="section-link-more"
                    >
                        Berita Lainnya <span class="arrow-right">&rarr;</span>
                    </a>
                </div>

                <!-- LOADING STATE -->
                <div v-if="loading" class="media-skeleton-grid">
                    <div class="skeleton-featured"></div>
                    <div class="skeleton-subgrid">
                        <div v-for="i in 4" :key="i" class="skeleton-card"></div>
                    </div>
                </div>

                <!-- EMPTY STATE -->
                <div v-else-if="filteredPosts.length === 0" class="empty-media-box">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                    <p>Tidak ada berita yang sesuai dengan kategori atau kata kunci pencarian.</p>
                </div>

                <!-- BERITA TERBARU GRID (LAYOUT WIREFRAME GAMBAR 3) -->
                <div v-else class="berita-terbaru-grid">
                    <!-- KIRI: 1 KARTU BESAR UTAMA -->
                    <article v-if="featuredPost" class="card-featured-large">
                        <a :href="featuredPost.link" target="_blank" rel="noopener noreferrer" class="featured-media-link">
                            <div class="featured-thumb-box">
                                <img
                                    v-if="featuredPost.image"
                                    :src="featuredPost.image"
                                    :alt="featuredPost.title"
                                    class="featured-thumb-img"
                                    loading="lazy"
                                />
                                <div v-else class="placeholder-thumb">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                            </div>
                        </a>
                        <div class="featured-body">
                            <span class="news-date">{{ featuredPost.dateFormatted }}</span>
                            <h3 class="featured-title">
                                <a :href="featuredPost.link" target="_blank" rel="noopener noreferrer">
                                    {{ featuredPost.title }}
                                </a>
                            </h3>
                            <p class="featured-excerpt">
                                {{ featuredPost.excerpt }}
                            </p>
                        </div>
                    </article>

                    <!-- KANAN: GRID 2x2 (4 KARTU KECIL) -->
                    <div class="sub-grid-2x2">
                        <article
                            v-for="post in gridPosts"
                            :key="post.id"
                            class="sub-card"
                        >
                            <a :href="post.link" target="_blank" rel="noopener noreferrer" class="sub-card-thumb-link">
                                <div class="sub-card-thumb">
                                    <img
                                        v-if="post.image"
                                        :src="post.image"
                                        :alt="post.title"
                                        loading="lazy"
                                    />
                                    <div v-else class="placeholder-thumb">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                            <div class="sub-card-body">
                                <div class="sub-card-meta">
                                    <span v-if="post.category" class="news-cat-badge">{{ post.category }}</span>
                                    <span class="news-date-sm">{{ post.dateFormatted }}</span>
                                </div>
                                <h4 class="sub-card-title">
                                    <a :href="post.link" target="_blank" rel="noopener noreferrer">
                                        {{ post.title }}
                                    </a>
                                </h4>
                            </div>
                        </article>
                    </div>
                </div>
            </section>

            <!-- SECTION 2: PORTAL MEDIA LANJUTAN (LAYOUT GAMBAR 1 & GAMBAR 2) -->
            <section class="media-two-columns" v-if="!loading && filteredPosts.length > 0">
                <!-- KOLOM KIRI (HEADLINES & DAFTAR ARTIKEL) -->
                <div class="media-left-col">
                    <!-- HEADLINES SLIDER / CAROUSEL -->
                    <div class="headlines-section" v-if="headlinePosts.length > 0">
                        <div class="headlines-header-row">
                            <h3 class="headlines-title">HEADLINES</h3>
                            <div class="carousel-nav-arrows">
                                <button
                                    type="button"
                                    class="btn-carousel-nav"
                                    :disabled="headlineIndex <= 0"
                                    @click="headlineIndex = Math.max(0, headlineIndex - 1)"
                                    title="Sebelumnya"
                                >
                                    &laquo;
                                </button>
                                <button
                                    type="button"
                                    class="btn-carousel-nav"
                                    :disabled="headlineIndex + 3 >= headlinePosts.length"
                                    @click="headlineIndex = Math.min(headlinePosts.length - 3, headlineIndex + 1)"
                                    title="Selanjutnya"
                                >
                                    &raquo;
                                </button>
                            </div>
                        </div>

                        <!-- HEADLINES ROW OF 3 CARDS -->
                        <div class="headlines-cards-row">
                            <article
                                v-for="hPost in visibleHeadlinePosts"
                                :key="hPost.id"
                                class="headline-card"
                            >
                                <a :href="hPost.link" target="_blank" rel="noopener noreferrer" class="headline-thumb-link">
                                    <div class="headline-thumb">
                                        <img v-if="hPost.image" :src="hPost.image" :alt="hPost.title" loading="lazy" />
                                        <div v-else class="placeholder-thumb">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                                <div class="headline-body">
                                    <span class="news-date-sm">{{ hPost.dateFormatted }}</span>
                                    <h4 class="headline-item-title">
                                        <a :href="hPost.link" target="_blank" rel="noopener noreferrer">
                                            {{ hPost.title }}
                                        </a>
                                    </h4>
                                </div>
                            </article>
                        </div>
                    </div>

                    <!-- BERITA TERKAIT / LIST FEED -->
                    <div class="related-news-section" v-if="moreArticles.length > 0">
                        <h3 class="related-news-heading">BERITA TERKAIT</h3>
                        <div class="related-news-list">
                            <article
                                v-for="article in moreArticles"
                                :key="article.id"
                                class="related-news-item"
                            >
                                <a :href="article.link" target="_blank" rel="noopener noreferrer" class="related-thumb-link">
                                    <div class="related-thumb">
                                        <img v-if="article.image" :src="article.image" :alt="article.title" loading="lazy" />
                                        <div v-else class="placeholder-thumb"></div>
                                    </div>
                                </a>
                                <div class="related-content">
                                    <div class="related-meta">
                                        <span v-if="article.category" class="news-cat-badge">{{ article.category }}</span>
                                        <span class="news-date-sm">{{ article.dateFormatted }}</span>
                                    </div>
                                    <h4 class="related-title">
                                        <a :href="article.link" target="_blank" rel="noopener noreferrer">
                                            {{ article.title }}
                                        </a>
                                    </h4>
                                    <p class="related-excerpt" v-if="article.excerpt">
                                        {{ article.excerpt }}
                                    </p>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>

                <!-- KOLOM KANAN (SIDEBAR: TOPIK POPULER & BERITA NASIONAL) -->
                <aside class="media-right-sidebar">
                    <!-- WIDGET 1: TOPIK POPULER -->
                    <div class="sidebar-widget widget-popular-topics">
                        <h3 class="sidebar-widget-title">TOPIK POPULER</h3>
                        <div class="popular-topics-list">
                            <button
                                v-for="tag in popularTopics"
                                :key="tag"
                                type="button"
                                class="topic-tag-btn"
                                :class="{ active: searchQuery.toLowerCase() === tag.replace('#', '').trim().toLowerCase() }"
                                @click="filterByTopic(tag)"
                            >
                                <span class="hash-symbol">#</span> {{ tag.replace('#', '').trim() }}
                            </button>
                        </div>
                    </div>

                    <!-- WIDGET 2: BERITA NASIONAL -->
                    <div class="sidebar-widget widget-nasional">
                        <h3 class="sidebar-widget-title">BERITA NASIONAL</h3>

                        <!-- HERO CARD NASIONAL -->
                        <div v-if="nasionalLead" class="nasional-lead-card">
                            <a :href="nasionalLead.link" target="_blank" rel="noopener noreferrer" class="nasional-lead-thumb-link">
                                <div class="nasional-lead-thumb">
                                    <img v-if="nasionalLead.image" :src="nasionalLead.image" :alt="nasionalLead.title" loading="lazy" />
                                </div>
                            </a>
                            <div class="nasional-lead-body">
                                <div class="nasional-tags">
                                    <span class="tag-red">NASIONAL</span>
                                    <span v-if="nasionalLead.category && nasionalLead.category.toLowerCase() !== 'nasional'" class="tag-red">
                                        , {{ nasionalLead.category.toUpperCase() }}
                                    </span>
                                    <span class="news-date-sm ml-2">{{ nasionalLead.dateFormatted }}</span>
                                </div>
                                <h4 class="nasional-lead-title">
                                    <a :href="nasionalLead.link" target="_blank" rel="noopener noreferrer">
                                        {{ nasionalLead.title }}
                                    </a>
                                </h4>
                            </div>
                        </div>

                        <!-- LIST MINI BERITA NASIONAL -->
                        <div class="nasional-mini-list">
                            <div
                                v-for="item in nasionalMiniList"
                                :key="item.id"
                                class="nasional-mini-item"
                            >
                                <a :href="item.link" target="_blank" rel="noopener noreferrer" class="mini-thumb-link">
                                    <div class="mini-thumb">
                                        <img v-if="item.image" :src="item.image" :alt="item.title" loading="lazy" />
                                    </div>
                                </a>
                                <div class="mini-content">
                                    <div class="mini-meta">
                                        <span class="tag-red">NASIONAL</span>
                                        <span class="news-date-sm ml-1">{{ item.dateFormatted }}</span>
                                    </div>
                                    <h5 class="mini-title">
                                        <a :href="item.link" target="_blank" rel="noopener noreferrer">
                                            {{ item.title }}
                                        </a>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </section>

            <!-- SECTION 3: MASUK SESUAI PERANMU (WIREFRAME GAMBAR 3) -->
            <section class="section-roles">
                <h2 class="roles-heading">Masuk sesuai peranmu</h2>
                <div class="roles-grid">
                    <!-- GUEST CARD -->
                    <div class="role-card">
                        <div class="role-card-inner">
                            <h3 class="role-card-title">Guest</h3>
                            <p class="role-card-desc">
                                Melihat dashboard, naskah yang sudah terbit, dan mengirim naskah tanpa perlu mendaftar.
                            </p>
                            <router-link to="/guest/kirim-naskah" class="role-action-link">
                                Mulai sebagai Guest &rarr;
                            </router-link>
                        </div>
                    </div>

                    <!-- KONTRIBUTOR CARD -->
                    <div class="role-card">
                        <div class="role-card-inner">
                            <h3 class="role-card-title">Kontributor</h3>
                            <p class="role-card-desc">
                                Mengirim naskah dan memantau status peninjauannya.
                            </p>
                            <router-link to="/daftar-kontributor" class="role-action-link">
                                Daftar Kontributor &rarr;
                            </router-link>
                        </div>
                    </div>

                    <!-- ADMIN CARD -->
                    <div class="role-card">
                        <div class="role-card-inner">
                            <h3 class="role-card-title">Admin</h3>
                            <p class="role-card-desc">
                                Meninjau naskah masuk, menerbitkan, atau menolak dengan alasan.
                            </p>
                            <router-link to="/login" class="role-action-link">
                                Masuk Admin &rarr;
                            </router-link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 4: CALL TO ACTION BANNER (WIREFRAME GAMBAR 3) -->
            <section class="cta-banner">
                <div class="cta-banner-content">
                    <h2 class="cta-title">Siap mengirim naskah pertamamu?</h2>
                    <router-link to="/daftar-kontributor" class="btn-cta-white">
                        Daftar Sekarang
                    </router-link>
                </div>
            </section>
        </main>

        <!-- FOOTER (WIREFRAME GAMBAR 3) -->
        <footer class="bottom-footer">
            <div class="footer-container">
                <span class="footer-left">Media Online Harian Cakrawala</span>
                <a href="mailto:redaksi@hariancakrawala.com" class="footer-right">
                    redaksi@hariancakrawala.com
                </a>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../api/index.js'

// State
const posts = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedCategory = ref('')
const headlineIndex = ref(0)

// Auth status
const isLoggedIn = computed(() => !!localStorage.getItem('auth_token'))
const userInfo = computed(() => {
    const raw = localStorage.getItem('user_info')
    return raw ? JSON.parse(raw) : null
})
const dashboardRoute = computed(() => {
    if (userInfo.value?.role === 'admin') return '/admin/dashboard'
    return '/kontributor/dashboard'
})

// Categories
const availableCategories = [
    'Nasional',
    'Daerah',
    'Pendidikan',
    'Bisnis',
    'Ekonomi',
    'Teknologi',
    'Opini',
]

// Popular Topics
const popularTopics = [
    'TAG BERITA',
    'BERITA OLAHRAGA',
    'BERITA OTOMOTIF',
    'NEW ZEALAND',
    'KEJAHATAN',
    'PENDIDIKAN',
]

function setCategory(cat) {
    if (selectedCategory.value.toLowerCase() === cat.toLowerCase()) {
        selectedCategory.value = ''
    } else {
        selectedCategory.value = cat
    }
}

function filterByTopic(topic) {
    const clean = topic.replace('#', '').trim()
    searchQuery.value = clean
}

// Helpers
function stripHtml(html) {
    if (!html) return ''
    const doc = new DOMParser().parseFromString(html, 'text/html')
    return doc.body.textContent || ''
}

function formatDateIndo(dateStr) {
    if (!dateStr) return '-'
    const d = new Date(dateStr)
    if (isNaN(d.getTime())) return dateStr
    return d.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
}

// Normalizer
function normalizeWpPost(item) {
    const media = item._embedded?.['wp:featuredmedia']?.[0]?.source_url || ''
    const terms = item._embedded?.['wp:term']?.[0] || []
    const catName = terms.length > 0 ? terms[0].name : 'Berita'
    const cleanExcerpt = stripHtml(item.excerpt?.rendered || item.content?.rendered || '').slice(0, 150)

    return {
        id: item.id,
        title: stripHtml(item.title?.rendered || 'Tanpa Judul'),
        image: media,
        dateFormatted: formatDateIndo(item.date),
        rawDate: item.date,
        excerpt: cleanExcerpt ? `${cleanExcerpt}...` : '',
        category: catName,
        link: item.link || 'https://hariancakrawala.com',
    }
}

function normalizeLocalNaskah(item) {
    let img = ''
    if (item.gambar_sampul) {
        img = item.gambar_sampul.startsWith('http')
            ? item.gambar_sampul
            : `http://localhost:8000/storage/${item.gambar_sampul.replace(/^\/+/, '')}`
    }

    return {
        id: item.id,
        title: item.judul || 'Tanpa Judul',
        image: img,
        dateFormatted: formatDateIndo(item.waktu_kirim || item.created_at),
        rawDate: item.created_at,
        excerpt: item.isi ? stripHtml(item.isi).slice(0, 150) + '...' : '',
        category: item.kategori || 'Berita',
        link: '#',
    }
}

// Fetching
async function fetchNews() {
    loading.value = true
    try {
        const wpRes = await fetch(
            'https://hariancakrawala.com/wp-json/wp/v2/posts?_embed=true&per_page=15',
            { headers: { Accept: 'application/json' } }
        )
        if (!wpRes.ok) throw new Error('WP API response not OK')
        const rawPosts = await wpRes.json()
        if (Array.isArray(rawPosts) && rawPosts.length > 0) {
            posts.value = rawPosts.map(normalizeWpPost)
            return
        }
    } catch (err) {
        console.warn('Gagal memuat berita dari WP API, menggunakan data naskah lokal:', err)
    }

    // Fallback: Local API
    try {
        const localRes = await api.public.getBerita()
        const localData = localRes.data?.data || []
        posts.value = localData.map(normalizeLocalNaskah)
    } catch {
        posts.value = []
    } finally {
        loading.value = false
    }
}

// Filtered Posts
const filteredPosts = computed(() => {
    let list = posts.value

    if (selectedCategory.value) {
        const target = selectedCategory.value.toLowerCase()
        list = list.filter(p => p.category && p.category.toLowerCase().includes(target))
    }

    if (searchQuery.value.trim()) {
        const q = searchQuery.value.trim().toLowerCase()
        list = list.filter(p =>
            p.title.toLowerCase().includes(q) ||
            p.excerpt.toLowerCase().includes(q) ||
            p.category.toLowerCase().includes(q)
        )
    }

    return list
})

// Sub-slices for layout
const featuredPost = computed(() => filteredPosts.value[0] || null)
const gridPosts = computed(() => filteredPosts.value.slice(1, 5))
const headlinePosts = computed(() => filteredPosts.value.slice(5, 11))
const visibleHeadlinePosts = computed(() => {
    return headlinePosts.value.slice(headlineIndex.value, headlineIndex.value + 3)
})
const moreArticles = computed(() => filteredPosts.value.slice(11))

// Sidebar Nasional widget
const nasionalPosts = computed(() => {
    return posts.value.filter(p =>
        p.category && (
            p.category.toLowerCase().includes('nasional') ||
            p.category.toLowerCase().includes('politik')
        )
    )
})
const nasionalLead = computed(() => nasionalPosts.value[0] || posts.value[1] || null)
const nasionalMiniList = computed(() => {
    const list = nasionalPosts.value.length > 1 ? nasionalPosts.value.slice(1, 4) : posts.value.slice(2, 5)
    return list
})

onMounted(() => {
    fetchNews().finally(() => {
        loading.value = false
    })
})
</script>

<style scoped>
/* =====================================================
   MAIN CONTAINER & BRAND THEME
   ===================================================== */
.home-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: #f8fafc;
    color: #0f172a;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* =====================================================
   TOP HEADER NAVBAR (WIREFRAME GAMBAR 3)
   ===================================================== */
.top-header {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.header-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0.85rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
}

/* BRAND */
.brand-link {
    text-decoration: none;
    display: flex;
    align-items: center;
}

.brand-text {
    display: flex;
    flex-direction: column;
}

.brand-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #1e5f51;
    letter-spacing: -0.02em;
    line-height: 1.15;
}

.brand-tagline {
    font-size: 0.725rem;
    color: #64748b;
    font-weight: 500;
    margin-top: 2px;
}

/* SEARCH PILL */
.search-wrapper {
    flex: 1;
    max-width: 480px;
}

.search-pill {
    display: flex;
    align-items: center;
    background: #1e5f51;
    border-radius: 9999px;
    padding: 0.35rem 0.85rem;
    gap: 0.5rem;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.15);
    transition: background-color 0.2s, box-shadow 0.2s;
}

.search-pill:focus-within {
    background: #14463b;
    box-shadow: 0 0 0 3px rgba(30, 95, 81, 0.25);
}

.btn-search-icon {
    background: none;
    border: none;
    color: #ffffff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
}

.search-input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    color: #ffffff;
    font-size: 0.875rem;
    font-weight: 400;
}

.search-input::placeholder {
    color: rgba(255, 255, 255, 0.75);
}

.btn-clear-search {
    background: none;
    border: none;
    color: #ffffff;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0 0.25rem;
    line-height: 1;
}

/* ACTIONS */
.header-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.btn-action-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.45rem 1.25rem;
    border: 1.5px solid #cbd5e1;
    border-radius: 6px;
    color: #0f172a;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    background: #ffffff;
}

.btn-action-outline:hover {
    border-color: #1e5f51;
    color: #1e5f51;
}

.btn-action-solid {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1.35rem;
    background: #1e5f51;
    border: 1px solid transparent;
    border-radius: 6px;
    color: #ffffff;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.2s;
}

.btn-action-solid:hover {
    background: #14463b;
}

/* =====================================================
   SUB-NAVBAR KATEGORI (WIREFRAME GAMBAR 3)
   ===================================================== */
.category-nav {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
}

.category-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 0 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 2rem;
    overflow-x: auto;
    white-space: nowrap;
    scrollbar-width: none;
}

.category-container::-webkit-scrollbar {
    display: none;
}

.category-link {
    background: none;
    border: none;
    padding: 0.85rem 0.25rem;
    font-size: 0.8rem;
    font-weight: 700;
    color: #334155;
    letter-spacing: 0.04em;
    cursor: pointer;
    border-bottom: 2.5px solid transparent;
    transition: all 0.2s;
}

.category-link:hover {
    color: #1e5f51;
}

.category-link.active {
    color: #1e5f51;
    border-bottom-color: #1e5f51;
}

/* =====================================================
   HERO WELCOME SECTION (WIREFRAME GAMBAR 3)
   ===================================================== */
.hero-welcome {
    background: #ffffff;
    padding: 3.5rem 1.5rem 2.5rem;
    text-align: center;
    border-bottom: 1px solid #f1f5f9;
}

.hero-content {
    max-width: 800px;
    margin: 0 auto;
}

.hero-title {
    font-size: 2.25rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.025em;
    margin-bottom: 0.75rem;
}

.hero-subtitle {
    font-size: 1rem;
    color: #64748b;
    font-weight: 400;
}

/* =====================================================
   MAIN BODY & SECTION BLOCKS
   ===================================================== */
.main-body {
    max-width: 1240px;
    margin: 0 auto;
    padding: 2.5rem 1.5rem 4rem;
    width: 100%;
}

.section-block {
    margin-bottom: 3.5rem;
}

.section-header-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid #0f172a;
    padding-bottom: 0.5rem;
}

.section-heading {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.01em;
}

.section-link-more {
    font-size: 0.9rem;
    font-weight: 700;
    color: #0f172a;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: color 0.2s;
}

.section-link-more:hover {
    color: #1e5f51;
}

.arrow-right {
    font-size: 1.1rem;
}

/* =====================================================
   SECTION 1: BERITA TERBARU (1 HERO + 2x2 GRID)
   ===================================================== */
.berita-terbaru-grid {
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    gap: 2rem;
}

/* KARTU FEATURED BESAR */
.card-featured-large {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card-featured-large:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.featured-media-link {
    text-decoration: none;
}

.featured-thumb-box {
    width: 100%;
    height: 320px;
    background: #e2e8f0;
    overflow: hidden;
    position: relative;
}

.featured-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.35s ease;
}

.card-featured-large:hover .featured-thumb-img {
    transform: scale(1.03);
}

.featured-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.news-date {
    font-size: 0.775rem;
    color: #94a3b8;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.featured-title {
    font-size: 1.4rem;
    font-weight: 800;
    line-height: 1.35;
    margin-bottom: 0.75rem;
}

.featured-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s;
}

.featured-title a:hover {
    color: #1e5f51;
}

.featured-excerpt {
    font-size: 0.885rem;
    color: #475569;
    line-height: 1.6;
    margin-top: auto;
}

/* KANAN: SUB-GRID 2x2 */
.sub-grid-2x2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: repeat(2, 1fr);
    gap: 1.25rem;
}

.sub-card {
    background: #ffffff;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    transition: transform 0.2s, box-shadow 0.2s;
}

.sub-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
}

.sub-card-thumb-link {
    text-decoration: none;
}

.sub-card-thumb {
    width: 100%;
    height: 140px;
    background: #e2e8f0;
    overflow: hidden;
}

.sub-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.35s ease;
}

.sub-card:hover .sub-card-thumb img {
    transform: scale(1.04);
}

.sub-card-body {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.sub-card-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.4rem;
}

.news-cat-badge {
    font-size: 0.675rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #1e5f51;
    background: #e8f3f0;
    padding: 2px 6px;
    border-radius: 4px;
}

.news-date-sm {
    font-size: 0.72rem;
    color: #94a3b8;
    font-weight: 500;
}

.sub-card-title {
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.sub-card-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s;
}

.sub-card-title a:hover {
    color: #1e5f51;
}

/* =====================================================
   SECTION 2: 2 KOLOM MEDIA (GAMBAR 1 & 2)
   ===================================================== */
.media-two-columns {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2.5rem;
    margin-bottom: 4rem;
}

/* HEADLINES SLIDER */
.headlines-section {
    margin-bottom: 2.5rem;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
}

.headlines-header-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    border-bottom: 2px solid #0f172a;
    padding-bottom: 0.5rem;
}

.headlines-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.02em;
}

.carousel-nav-arrows {
    display: flex;
    gap: 0.35rem;
}

.btn-carousel-nav {
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-carousel-nav:hover:not(:disabled) {
    background: #1e5f51;
    border-color: #1e5f51;
    color: #ffffff;
}

.btn-carousel-nav:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.headlines-cards-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.headline-card {
    display: flex;
    flex-direction: column;
}

.headline-thumb {
    width: 100%;
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
    background: #e2e8f0;
    margin-bottom: 0.5rem;
}

.headline-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.headline-card:hover .headline-thumb img {
    transform: scale(1.05);
}

.headline-item-title {
    font-size: 0.875rem;
    font-weight: 700;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-top: 0.25rem;
}

.headline-item-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s;
}

.headline-item-title a:hover {
    color: #1e5f51;
}

/* BERITA TERKAIT LIST */
.related-news-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
}

.related-news-heading {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.02em;
    margin-bottom: 1.25rem;
    border-bottom: 2px solid #0f172a;
    padding-bottom: 0.5rem;
}

.related-news-list {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.related-news-item {
    display: flex;
    gap: 1rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid #f1f5f9;
}

.related-news-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.related-thumb {
    width: 140px;
    height: 95px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
    background: #e2e8f0;
}

.related-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.related-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.related-title {
    font-size: 1rem;
    font-weight: 700;
    line-height: 1.35;
    margin-bottom: 0.35rem;
}

.related-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s;
}

.related-title a:hover {
    color: #1e5f51;
}

.related-excerpt {
    font-size: 0.825rem;
    color: #64748b;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* =====================================================
   SIDEBAR KANAN (TOPIK POPULER & BERITA NASIONAL)
   ===================================================== */
.media-right-sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.sidebar-widget {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
}

.sidebar-widget-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.02em;
    margin-bottom: 1rem;
    border-bottom: 2px solid #0f172a;
    padding-bottom: 0.4rem;
}

/* TOPIK POPULER LIST */
.popular-topics-list {
    display: flex;
    flex-direction: column;
}

.topic-tag-btn {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    padding: 0.65rem 0.25rem;
    background: none;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
    cursor: pointer;
    text-align: left;
    transition: color 0.2s, padding-left 0.2s;
}

.topic-tag-btn:hover {
    color: #1e5f51;
    padding-left: 0.35rem;
}

.topic-tag-btn.active {
    color: #1e5f51;
    font-weight: 800;
}

.hash-symbol {
    color: #dc2626;
    font-weight: 800;
}

/* BERITA NASIONAL WIDGET */
.nasional-lead-card {
    margin-bottom: 1.25rem;
    padding-bottom: 1.25rem;
    border-bottom: 1px solid #e2e8f0;
}

.nasional-lead-thumb {
    width: 100%;
    height: 160px;
    border-radius: 8px;
    overflow: hidden;
    background: #e2e8f0;
    margin-bottom: 0.6rem;
}

.nasional-lead-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.tag-red {
    color: #dc2626;
    font-size: 0.725rem;
    font-weight: 800;
    text-transform: uppercase;
}

.nasional-lead-title {
    font-size: 0.95rem;
    font-weight: 700;
    line-height: 1.35;
    margin-top: 0.3rem;
}

.nasional-lead-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s;
}

.nasional-lead-title a:hover {
    color: #1e5f51;
}

.nasional-mini-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.nasional-mini-item {
    display: flex;
    gap: 0.75rem;
}

.mini-thumb {
    width: 75px;
    height: 60px;
    flex-shrink: 0;
    border-radius: 6px;
    overflow: hidden;
    background: #e2e8f0;
}

.mini-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.mini-content {
    flex: 1;
}

.mini-meta {
    display: flex;
    align-items: center;
    margin-bottom: 0.2rem;
}

.mini-title {
    font-size: 0.825rem;
    font-weight: 700;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.mini-title a {
    color: #0f172a;
    text-decoration: none;
    transition: color 0.2s;
}

.mini-title a:hover {
    color: #1e5f51;
}

.ml-1 { margin-left: 0.25rem; }
.ml-2 { margin-left: 0.5rem; }

/* =====================================================
   SECTION 3: MASUK SESUAI PERANMU (WIREFRAME GAMBAR 3)
   ===================================================== */
.section-roles {
    margin-bottom: 3.5rem;
}

.roles-heading {
    font-size: 1.5rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 1.5rem;
    letter-spacing: -0.015em;
}

.roles-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.role-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    transition: all 0.2s ease;
}

.role-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(30, 95, 81, 0.08);
    border-color: #1e5f51;
}

.role-card-inner {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.role-card-title {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 0.65rem;
}

.role-card-desc {
    font-size: 0.875rem;
    color: #475569;
    line-height: 1.55;
    margin-bottom: 1.25rem;
    flex: 1;
}

.role-action-link {
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e5f51;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: gap 0.2s;
}

.role-action-link:hover {
    gap: 0.6rem;
}

/* =====================================================
   SECTION 4: CALL TO ACTION BANNER (WIREFRAME GAMBAR 3)
   ===================================================== */
.cta-banner {
    background: #112822;
    border-radius: 12px;
    padding: 2.5rem 3rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 16px rgba(15, 42, 34, 0.12);
}

.cta-banner-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
}

.cta-title {
    font-size: 1.65rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.015em;
    margin: 0;
}

.btn-cta-white {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.65rem 1.75rem;
    background: #ffffff;
    color: #112822;
    font-size: 0.95rem;
    font-weight: 700;
    border-radius: 8px;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.btn-cta-white:hover {
    background: #f1f5f9;
    transform: translateY(-1px);
}

/* =====================================================
   FOOTER (WIREFRAME GAMBAR 3)
   ===================================================== */
.bottom-footer {
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    margin-top: auto;
}

.footer-container {
    max-width: 1240px;
    margin: 0 auto;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.85rem;
    color: #64748b;
}

.footer-right {
    color: #1e5f51;
    text-decoration: none;
    font-weight: 600;
}

.footer-right:hover {
    text-decoration: underline;
}

/* =====================================================
   SKELETON & PLACEHOLDERS
   ===================================================== */
.media-skeleton-grid {
    display: grid;
    grid-template-columns: 1.1fr 1fr;
    gap: 2rem;
}

.skeleton-featured {
    height: 440px;
    background: #e2e8f0;
    border-radius: 12px;
    animation: pulse 1.5s infinite;
}

.skeleton-subgrid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: repeat(2, 1fr);
    gap: 1.25rem;
}

.skeleton-card {
    height: 210px;
    background: #e2e8f0;
    border-radius: 10px;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 0.3; }
}

.placeholder-thumb {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e2e8f0;
    color: #94a3b8;
}

.empty-media-box {
    text-align: center;
    padding: 4rem 1.5rem;
    background: #ffffff;
    border-radius: 12px;
    border: 1px dashed #cbd5e1;
    color: #64748b;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
}

/* =====================================================
   RESPONSIVE MEDIA QUERIES
   ===================================================== */
@media (max-width: 1024px) {
    .berita-terbaru-grid {
        grid-template-columns: 1fr;
    }

    .featured-thumb-box {
        height: 280px;
    }

    .media-two-columns {
        grid-template-columns: 1fr;
    }

    .roles-grid {
        grid-template-columns: 1fr;
    }

    .cta-banner-content {
        flex-direction: column;
        text-align: center;
    }
}

@media (max-width: 640px) {
    .header-container {
        flex-wrap: wrap;
        gap: 1rem;
    }

    .search-wrapper {
        order: 3;
        max-width: 100%;
        width: 100%;
    }

    .hero-title {
        font-size: 1.65rem;
    }

    .sub-grid-2x2 {
        grid-template-columns: 1fr;
    }

    .headlines-cards-row {
        grid-template-columns: 1fr;
    }

    .related-news-item {
        flex-direction: column;
    }

    .related-thumb {
        width: 100%;
        height: 160px;
    }

    .footer-container {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
}
</style>
