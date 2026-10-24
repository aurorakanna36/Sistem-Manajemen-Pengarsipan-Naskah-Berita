<template>
    <div class="register-page">
        <div class="register-left">
            <div class="brand">
                <div class="brand-logo">HC</div>
                <h1 class="brand-name">Harian Cakrawala</h1>
                <p class="brand-tagline">Bergabunglah sebagai kontributor tetap dan kirimkan naskah berita Anda ke redaksi kami.</p>
            </div>
            <router-link to="/login" class="btn-back">Sudah punya akun? Masuk</router-link>
        </div>

        <div class="register-right">
            <div class="register-form-container">
                <h2>Daftar sebagai Kontributor</h2>
                <p class="form-subtitle">Isi data dengan benar. Akun Anda akan diverifikasi oleh redaksi sebelum aktif.</p>

                <div v-if="errorMessage" class="alert-error">{{ errorMessage }}</div>

                <form @submit.prevent="handleRegister" class="register-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nama Lengkap</label>
                            <input id="name" v-model="form.name" type="text" placeholder="Nama lengkap Anda" required />
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Nomor HP</label>
                            <input id="phone_number" v-model="form.phone_number" type="text" placeholder="08xx-xxxx-xxxx" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" v-model="form.email" type="email" placeholder="contoh@email.com" required />
                    </div>

                    <div class="form-group">
                        <label for="instansi">Instansi / Lembaga <span class="optional">(Opsional)</span></label>
                        <input id="instansi" v-model="form.instansi" type="text" placeholder="Nama instansi atau lembaga Anda" />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Kata Sandi</label>
                            <input id="password" v-model="form.password" type="password" placeholder="Minimal 8 karakter" required />
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                            <input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder="Ulangi kata sandi" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="surat_administrasi">Surat Administrasi <span class="optional">(PDF / Gambar, maks 5MB)</span></label>
                        <input id="surat_administrasi" type="file" accept=".pdf,.jpg,.jpeg,.png" @change="handleFileChange" />
                    </div>

                    <button type="submit" class="btn-submit" :disabled="loading">
                        <span v-if="loading">Mendaftarkan...</span>
                        <span v-else>Daftar Sekarang</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../api/index.js'

const router = useRouter()

const form = ref({
    name: '',
    email: '',
    phone_number: '',
    instansi: '',
    password: '',
    password_confirmation: '',
})
const suratFile = ref(null)
const loading = ref(false)
const errorMessage = ref('')

function handleFileChange(event) {
    suratFile.value = event.target.files[0] || null
}

async function handleRegister() {
    loading.value = true
    errorMessage.value = ''

    const formData = new FormData()
    Object.entries(form.value).forEach(([key, value]) => {
        if (value) formData.append(key, value)
    })
    if (suratFile.value) {
        formData.append('surat_administrasi', suratFile.value)
    }

    try {
        const response = await api.auth.register(formData)
        const { token, user } = response.data

        localStorage.setItem('auth_token', token)
        localStorage.setItem('user_info', JSON.stringify(user))

        router.push({ name: 'kontributor.pending' })
    } catch (error) {
        const errors = error.response?.data?.errors
        if (errors) {
            const firstKey = Object.keys(errors)[0]
            errorMessage.value = errors[firstKey][0]
        } else {
            errorMessage.value = 'Terjadi kesalahan. Coba lagi.'
        }
    } finally {
        loading.value = false
    }
}
</script>

<style scoped>
.register-page {
    display: flex;
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
}

.register-left {
    width: 340px;
    flex-shrink: 0;
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f4c81 100%);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 3rem;
    color: white;
}

.brand-logo {
    width: 56px;
    height: 56px;
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 1.25rem;
}

.brand-name {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0 0 0.75rem;
}

.brand-tagline {
    color: rgba(255, 255, 255, 0.65);
    font-size: 0.9rem;
    line-height: 1.6;
}

.btn-back {
    display: inline-block;
    border: 1.5px solid rgba(255, 255, 255, 0.45);
    color: white;
    padding: 0.6rem 1.1rem;
    border-radius: 8px;
    font-size: 0.875rem;
    text-align: center;
    transition: background 0.2s;
}

.btn-back:hover {
    background: rgba(255, 255, 255, 0.1);
}

.register-right {
    flex: 1;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    overflow-y: auto;
}

.register-form-container {
    width: 100%;
    max-width: 560px;
}

.register-form-container h2 {
    font-size: 1.6rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 0.4rem;
}

.form-subtitle {
    color: #64748b;
    font-size: 0.875rem;
    margin: 0 0 1.75rem;
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

.register-form {
    display: flex;
    flex-direction: column;
    gap: 1.1rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.form-group label {
    font-size: 0.85rem;
    font-weight: 500;
    color: #374151;
}

.optional {
    color: #94a3b8;
    font-weight: 400;
    font-size: 0.78rem;
}

.form-group input {
    padding: 0.65rem 0.9rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.875rem;
    color: #0f172a;
    background: white;
    transition: border-color 0.2s;
    outline: none;
}

.form-group input:focus {
    border-color: #0f4c81;
}

.btn-submit {
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

.btn-submit:hover:not(:disabled) { opacity: 0.9; }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }

@media (max-width: 768px) {
    .register-page { flex-direction: column; }
    .register-left { width: 100%; padding: 2rem; }
    .form-row { grid-template-columns: 1fr; }
}
</style>
