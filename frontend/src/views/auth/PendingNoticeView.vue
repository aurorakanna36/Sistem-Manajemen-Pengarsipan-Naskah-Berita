<template>
    <div class="pending-page">
        <div class="pending-card">
            <div class="pending-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <h1>Akun Sedang Menunggu Verifikasi</h1>
            <p>
                Terima kasih telah mendaftar sebagai kontributor Harian Cakrawala.
                Tim redaksi kami akan meninjau data pendaftaran Anda dan memberikan konfirmasi
                melalui email dalam 1–3 hari kerja.
            </p>
            <div class="pending-steps">
                <div class="step done">
                    <div class="step-dot"></div>
                    <span>Pendaftaran dikirim</span>
                </div>
                <div class="step active">
                    <div class="step-dot"></div>
                    <span>Menunggu verifikasi redaksi</span>
                </div>
                <div class="step">
                    <div class="step-dot"></div>
                    <span>Akun aktif & dapat login</span>
                </div>
            </div>
            <button class="btn-logout" @click="handleLogout">Keluar</button>
        </div>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import api from '../../api/index.js'

const router = useRouter()

async function handleLogout() {
    try {
        await api.auth.logout()
    } catch {
        // token mungkin sudah tidak valid
    } finally {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('user_info')
        router.push({ name: 'login' })
    }
}
</script>

<style scoped>
.pending-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.pending-card {
    background: white;
    border-radius: 16px;
    padding: 3rem;
    max-width: 500px;
    width: 100%;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
}

.pending-icon {
    width: 80px;
    height: 80px;
    background: #fffbeb;
    border: 2px solid #fde68a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: #d97706;
}

.pending-card h1 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 1rem;
}

.pending-card p {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.7;
    margin: 0 0 2rem;
}

.pending-steps {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    text-align: left;
    margin-bottom: 2rem;
    padding: 1.25rem;
    background: #f8fafc;
    border-radius: 10px;
}

.step {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
    color: #94a3b8;
}

.step.done { color: #166534; }
.step.active { color: #d97706; font-weight: 500; }

.step-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: currentColor;
    flex-shrink: 0;
}

.btn-logout {
    padding: 0.65rem 1.5rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    background: white;
    color: #64748b;
    font-size: 0.875rem;
    cursor: pointer;
    transition: border-color 0.2s, color 0.2s;
}

.btn-logout:hover {
    border-color: #94a3b8;
    color: #374151;
}
</style>
