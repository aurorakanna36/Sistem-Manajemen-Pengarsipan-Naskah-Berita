<template>
    <div class="login-page">
        <div class="login-left">
            <div class="brand">
                <div class="brand-logo">HC</div>
                <h1 class="brand-name">Harian Cakrawala</h1>
                <p class="brand-tagline">Sistem Manajemen Pengarsipan Naskah Berita</p>
            </div>
            <div class="brand-footer">
                <p>Belum punya akun?</p>
                <router-link to="/daftar-kontributor" class="btn-register">
                    Daftar sebagai Kontributor
                </router-link>
            </div>
        </div>

        <div class="login-right">
            <div class="login-form-container">
                <h2>Masuk ke Sistem</h2>
                <p class="login-subtitle">Gunakan email dan kata sandi akun Anda</p>

                <div v-if="errorMessage" class="alert-error">
                    {{ errorMessage }}
                </div>

                <form @submit.prevent="handleLogin" class="login-form">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="contoh@email.com"
                            required
                            autocomplete="email"
                        />
                    </div>

                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="Masukkan kata sandi"
                            required
                            autocomplete="current-password"
                        />
                    </div>

                    <button type="submit" class="btn-login" :disabled="loading">
                        <span v-if="loading">Memproses...</span>
                        <span v-else>Masuk</span>
                    </button>
                </form>

                <div class="login-guest">
                    <router-link to="/guest/kirim-naskah">
                        Kirim naskah tanpa akun (sebagai Tamu)
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../api/index.js'

const router = useRouter()

const form = ref({ email: '', password: '' })
const loading = ref(false)
const errorMessage = ref('')

async function handleLogin() {
    loading.value = true
    errorMessage.value = ''

    try {
        const response = await api.auth.login(form.value)
        const { token, user } = response.data

        localStorage.setItem('auth_token', token)
        localStorage.setItem('user_info', JSON.stringify(user))

        if (user.role === 'admin') {
            router.push({ name: 'admin.review' })
        } else if (user.role === 'kontributor') {
            router.push({ name: 'kontributor.dashboard' })
        } else {
            router.push({ name: 'home' })
        }
    } catch (error) {
        const errors = error.response?.data?.errors
        if (errors?.email) {
            errorMessage.value = errors.email[0]
        } else {
            errorMessage.value = 'Terjadi kesalahan. Coba lagi.'
        }
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.login-page {
    display: flex;
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
}

.login-left {
    flex: 1;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f4c81 100%);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 3rem;
    color: white;
}

.brand-logo {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(10px);
}

.brand-name {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.5rem;
}

.brand-tagline {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.95rem;
    line-height: 1.5;
}

.brand-footer {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.brand-footer p {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.9rem;
    margin: 0;
}

.btn-register {
    display: inline-block;
    border: 1.5px solid rgba(255, 255, 255, 0.5);
    color: white;
    padding: 0.65rem 1.25rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.9rem;
    text-align: center;
    transition: background 0.2s, border-color 0.2s;
}

.btn-register:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: white;
}

.login-right {
    flex: 1;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
}

.login-form-container {
    width: 100%;
    max-width: 400px;
}

.login-form-container h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.5rem;
}

.login-subtitle {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0 0 2rem;
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    margin-bottom: 1.25rem;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.form-group label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.form-group input {
    padding: 0.7rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #0f172a;
    background: white;
    transition: border-color 0.2s;
    outline: none;
}

.form-group input:focus {
    border-color: #0f4c81;
}

.btn-login {
    margin-top: 0.5rem;
    padding: 0.75rem;
    background: linear-gradient(135deg, #0f4c81, #1e3a5f);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}

.btn-login:hover:not(:disabled) {
    opacity: 0.9;
}

.btn-login:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.login-guest {
    margin-top: 1.5rem;
    text-align: center;
}

.login-guest a {
    color: #64748b;
    font-size: 0.85rem;
    text-decoration: none;
    transition: color 0.2s;
}

.login-guest a:hover {
    color: #0f4c81;
}

@media (max-width: 768px) {
    .login-page {
        flex-direction: column;
    }
    .login-left {
        padding: 2rem;
        min-height: auto;
    }
    .brand-footer {
        display: none;
    }
}
</style>
