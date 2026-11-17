<template>
    <div class="register-page">
        <div class="register-left">
            <div class="brand">
                <div class="brand-logo">HC</div>
                <h1 class="brand-name">Harian Cakrawala</h1>
                <p class="brand-tagline">Bergabunglah sebagai kontributor tetap dan kirimkan naskah berita Anda ke
                    redaksi kami.</p>
            </div>
            <router-link to="/login" class="btn-back">Sudah punya akun? Masuk</router-link>
        </div>

        <div class="register-right">
            <div class="register-form-container">
                <h2>Daftar sebagai Kontributor</h2>
                <p class="form-subtitle">Isi data dengan benar. Akun Anda akan diverifikasi oleh redaksi sebelum aktif.
                </p>

                <div v-if="errorMessage" class="alert-error">{{ errorMessage }}</div>

                <form @submit.prevent="handleRegister" class="register-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap<span class="required">*</span></label>
                            <input id="nama_lengkap" v-model="form.nama_lengkap" type="text"
                                placeholder="Nama lengkap Anda" required />
                        </div>
                        <div class="form-group">
                            <label for="nomor_whatsapp">Nomor WhatsApp<span class="required">*</span></label>
                            <input id="nomor_whatsapp" v-model="form.nomor_whatsapp" type="text"
                                placeholder="08xx-xxxx-xxxx" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email<span class="required">*</span></label>
                        <input id="email" v-model="form.email" type="email" placeholder="contoh@email.com" required />
                    </div>

                    <div class="form-group">
                        <label for="instansi">Instansi / Lembaga <span class="required">*</span></label>
                        <input id="instansi" v-model="form.instansi" type="text"
                            placeholder="Nama instansi atau lembaga Anda" />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Kata Sandi<span class="required">*</span></label>
                            <input id="password" v-model="form.password" type="password"
                                placeholder="Minimal 8 karakter" required />
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Kata Sandi<span class="required">*</span></label>
                            <input id="password_confirmation" v-model="form.password_confirmation" type="password"
                                placeholder="Ulangi kata sandi" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ktp">KTP <span class="required">*</span></label>
                        <input id="ktp" type="file" accept=".pdf,.jpg,.jpeg,.png" @change="handleKtpChange" />
                    </div>

                    <div class="form-group">
                        <label for="surat_administrasi">Surat Administrasi <span class="required">*</span></label>
                        <input id="surat_administrasi" type="file" accept=".pdf,.jpg,.jpeg,.png"
                            @change="handleSuratChange" />
                    </div>


                    <!-- TANDA TANGAN DIGITAL -->
                    <div class="form-group signature-group">
                        <label>
                            Tanda Tangan Digital <span class="required">*</span>
                        </label>

                        <p class="signature-description">
                            Silakan klik tombol di bawah untuk membubuhkan tanda tangan Anda.
                        </p>

                        <!-- Tombol membuka kanvas -->
                        <button v-if="!showSignatureCanvas" type="button" class="btn-open-signature"
                            @click="openSignatureCanvas">
                            ✍️ Bubuhkan Tanda Tangan
                        </button>

                        <!-- Kanvas muncul setelah diklik -->
                        <div v-if="showSignatureCanvas">
                            <div class="signature-wrapper">
                                <canvas ref="signatureCanvas" class="signature-canvas" width="900" height="240"
                                    @pointerdown="startDrawing" @pointermove="drawSignature" @pointerup="stopDrawing"
                                    @pointercancel="stopDrawing"></canvas>

                                <div class="signature-placeholder" v-if="!hasSignature">
                                    Goreskan tanda tangan di sini
                                </div>
                            </div>

                            <div class="signature-actions">
                                <span class="signature-status">
                                    {{ hasSignature
                                        ? 'Tanda tangan sudah dibuat'
                                        : 'Silakan bubuhkan tanda tangan'
                                    }}
                                </span>

                                <button type="button" class="btn-clear-signature" @click="clearSignature">
                                    Hapus Tanda Tangan
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- TOMBOL DAFTAR -->
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
import { ref, nextTick } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../api/index.js'
import Swal from 'sweetalert2'

const router = useRouter()

const form = ref({
    nama_lengkap: '',
    email: '',
    nomor_whatsapp: '',
    instansi: '',
    password: '',
    password_confirmation: '',
})

const ktpFile = ref(null)
const suratFile = ref(null)

const loading = ref(false)
const errorMessage = ref('')

// TANDA TANGAN DIGITAL
const signatureCanvas = ref(null)
const hasSignature = ref(false)
const showSignatureCanvas = ref(false)

let drawing = false
let signatureContext = null

// MEMBUKA KANVAS
async function openSignatureCanvas() {
    showSignatureCanvas.value = true

    await nextTick()

    const canvas = signatureCanvas.value

    if (!canvas) return

    signatureContext = canvas.getContext('2d')

    signatureContext.lineWidth = 3
    signatureContext.lineCap = 'round'
    signatureContext.lineJoin = 'round'
    signatureContext.strokeStyle = '#111827'
}

// POSISI KURSOR
function getCanvasPosition(event) {
    const canvas = signatureCanvas.value
    const rect = canvas.getBoundingClientRect()

    return {
        x: (event.clientX - rect.left) * (canvas.width / rect.width),
        y: (event.clientY - rect.top) * (canvas.height / rect.height)
    }
}

// MULAI MENGGAMBAR
function startDrawing(event) {
    if (!signatureContext) return

    drawing = true

    signatureCanvas.value.setPointerCapture(event.pointerId)

    const position = getCanvasPosition(event)

    signatureContext.beginPath()
    signatureContext.moveTo(position.x, position.y)
    signatureContext.lineTo(position.x + 0.1, position.y + 0.1)
    signatureContext.stroke()

    hasSignature.value = true
}

// MENGGAMBAR TANDA TANGAN
function drawSignature(event) {
    if (!drawing || !signatureContext) return

    const position = getCanvasPosition(event)

    signatureContext.lineTo(position.x, position.y)
    signatureContext.stroke()
}

// BERHENTI MENGGAMBAR
function stopDrawing() {
    drawing = false

    if (signatureContext) {
        signatureContext.closePath()
    }
}

// HAPUS TANDA TANGAN
function clearSignature() {
    if (!signatureContext || !signatureCanvas.value) return

    const canvas = signatureCanvas.value

    signatureContext.clearRect(
        0,
        0,
        canvas.width,
        canvas.height
    )

    hasSignature.value = false
}

// MENGUBAH CANVAS MENJADI FILE PNG
async function getSignatureFile() {
    if (!hasSignature.value || !signatureCanvas.value) {
        return null
    }

    return new Promise((resolve, reject) => {
        signatureCanvas.value.toBlob((blob) => {
            if (!blob) {
                reject(new Error('Gagal membuat gambar tanda tangan.'))
                return
            }

            resolve(
                new File(
                    [blob],
                    'tanda_tangan.png',
                    { type: 'image/png' }
                )
            )
        }, 'image/png')
    })
}

// FILE KTP
function handleKtpChange(event) {
    ktpFile.value = event.target.files[0] || null
}

// FILE SURAT ADMINISTRASI
function handleSuratChange(event) {
    suratFile.value = event.target.files[0] || null
}

// PROSES PENDAFTARAN
async function handleRegister() {

    if (!hasSignature.value) {
        errorMessage.value = 'Silakan bubuhkan tanda tangan terlebih dahulu.'

        Swal.fire({
            icon: 'warning',
            title: 'Tanda Tangan Belum Diisi',
            text: 'Silakan bubuhkan tanda tangan sebelum mendaftar.',
            confirmButtonColor: '#0d5c4d'
        })

        return
    }

    const result = await Swal.fire({
        icon: 'info',
        title: 'Informasi Pendaftaran Kontributor',
        text: 'Status verifikasi pendaftaran akan dikabarkan melalui email. Pastikan alamat email yang kamu masukkan sudah benar.',
        showCancelButton: true,
        confirmButtonText: 'Ya, Daftar',
        cancelButtonText: 'Periksa Kembali',
        confirmButtonColor: '#0d5c4d',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    })

    if (!result.isConfirmed) return

    loading.value = true
    errorMessage.value = ''

    try {
        const formData = new FormData()

        formData.append('nama_lengkap', form.value.nama_lengkap)
        formData.append('name', form.value.nama_lengkap)
        formData.append('email', form.value.email)

        formData.append('nomor_whatsapp', form.value.nomor_whatsapp)
        formData.append('phone_number', form.value.nomor_whatsapp)

        if (form.value.instansi) {
            formData.append('instansi', form.value.instansi)
        }

        formData.append('password', form.value.password)

        formData.append(
            'password_confirmation',
            form.value.password_confirmation
        )

        // TANDA TANGAN PNG
        const signatureFile = await getSignatureFile()

        if (!signatureFile) {
            throw new Error('Tanda tangan gagal diproses.')
        }

        formData.append('tanda_tangan', signatureFile)

        // FILE KTP / IDENTITAS
        if (ktpFile.value) {
            formData.append('foto_identitas', ktpFile.value)
            formData.append('ktp', ktpFile.value)
        }

        // FILE SURAT ADMINISTRASI
        if (suratFile.value) {
            formData.append('surat_administrasi', suratFile.value)
            formData.append('file', suratFile.value)
        }

        // KIRIM KE LARAVEL
        const response = await api.auth.register(formData)

        const { token, user } = response.data

        localStorage.setItem('auth_token', token)
        localStorage.setItem('user_info', JSON.stringify(user))

        await Swal.fire({
            icon: 'success',
            title: 'Pendaftaran Berhasil!',
            text: 'Terima kasih telah mendaftar sebagai kontributor Harian Cakrawala. Status verifikasi akan dikabarkan melalui email.',
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#0d5c4d',
            allowOutsideClick: false
        })

        router.push({ name: 'kontributor.pending' })

    } catch (error) {

        console.error('Error pendaftaran:', error)

        const errors = error.response?.data?.errors

        if (errors) {
            const firstKey = Object.keys(errors)[0]
            errorMessage.value = errors[firstKey][0]
        } else if (error.response?.data?.message) {
            const msg = error.response.data.message
            if (msg.includes('The given data was invalid')) {
                errorMessage.value = 'Data formulir yang Anda masukkan belum lengkap atau tidak valid.'
            } else {
                errorMessage.value = msg
            }
        } else if (error.message && error.message.includes('Network Error')) {
            errorMessage.value = 'Gagal terhubung ke server. Periksa koneksi internet Anda.'
        } else {
            errorMessage.value = 'Terjadi kendala saat memproses pendaftaran. Silakan coba beberapa saat lagi.'
        }

        Swal.fire({
            icon: 'error',
            title: 'Pendaftaran Gagal',
            text: errorMessage.value,
            confirmButtonColor: '#0d5c4d'
        })

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
    background: linear-gradient(135deg, #0f2a22 0%, #0f8168 50%, #1e5f51 100%);
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
    background: linear-gradient(135deg, #0f8168, #1e5f51);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}

.btn-submit:hover:not(:disabled) {
    opacity: 0.9;
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}


.btn-open-signature {
    width: 100%;
    padding: 0.85rem 1rem;
    background: #ffffff;
    color: #0f8168;
    border: 1.5px dashed #0f8168;
    border-radius: 9px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-open-signature:hover {
    background: #ecfdf5;
    border-color: #0d5c4d;
}

.signature-wrapper {
    position: relative;
    width: 100%;
    height: 170px;
    border: 1.5px dashed #cbd5e1;
    border-radius: 10px;
    background: #ffffff;
    overflow: hidden;
}

.signature-canvas {
    display: block;
    width: 100%;
    height: 100%;
    cursor: crosshair;
    touch-action: none;
    position: relative;
    z-index: 2;
}

.signature-placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    color: #94a3b8;
    font-size: 0.85rem;
    pointer-events: none;
    z-index: 1;
}

.signature-group {
    margin-top: 0.3rem;
}

.required {
    color: #dc2626;
}

.signature-description {
    font-size: 0.8rem;
    color: #64748b;
    margin: 0 0 0.6rem;
    line-height: 1.5;
}

.signature-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-top: 0.65rem;
    flex-wrap: wrap;
}

.signature-status {
    font-size: 0.78rem;
    color: #64748b;
}

.btn-clear-signature {
    border: 1px solid #fecaca;
    background: #fff;
    color: #dc2626;
    padding: 0.45rem 0.8rem;
    border-radius: 7px;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.btn-clear-signature:hover {
    background: #fef2f2;
}


@media (max-width: 768px) {
    .register-page {
        flex-direction: column;
    }

    .register-left {
        width: 100%;
        padding: 2rem;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .signature-wrapper {
        height: 150px;
    }

    .signature-actions {
        align-items: flex-start;
    }
}
</style>
