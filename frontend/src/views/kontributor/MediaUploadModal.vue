<template>
    <div v-if="visible" class="media-modal-overlay" @click.self="$emit('close')">
        <div class="media-modal">
            <div class="media-modal-header">
                <div class="header-left">
                    <h2 class="modal-title">Sisipkan Media</h2>
                    <div class="modal-tabs">
                        <span class="modal-tab active">Unggah Berkas</span>
                    </div>
                </div>
                <button type="button" class="btn-close" @click="$emit('close')">&times;</button>
            </div>

            <div class="media-modal-body">
                <div class="upload-preview-pane">
                    <div v-if="!previewUrl" class="upload-center-box">
                        <p class="upload-prompt-title">Unggah berkas dari penyimpanan internal</p>
                        <button
                            type="button"
                            class="btn-choose-file"
                            @click="$refs.mediaFileInput.click()"
                        >
                            Pilih Berkas Gambar
                        </button>
                        <p class="upload-hint">Ukuran berkas unggahan maksimum: 5 MB (PNG, JPG, WEBP).</p>
                    </div>

                    <div v-else class="uploaded-preview-wrap">
                        <div class="selected-thumb-card">
                            <img :src="previewUrl" :alt="meta.alt || meta.judul" class="preview-img" />
                            <span class="check-badge">&#10003;</span>
                        </div>
                        <button
                            type="button"
                            class="btn-change-file"
                            @click="$refs.mediaFileInput.click()"
                        >
                            Ganti Berkas Gambar
                        </button>
                    </div>

                    <input
                        ref="mediaFileInput"
                        type="file"
                        accept="image/*"
                        hidden
                        @change="onSelectFile"
                    />
                </div>

                <div class="attachment-details-pane">
                    <h3 class="details-heading">DETAIL LAMPIRAN</h3>

                    <div v-if="previewUrl" class="attachment-summary">
                        <img :src="previewUrl" alt="thumb" class="summary-thumb" />
                        <div class="summary-info">
                            <div class="summary-filename">{{ fileInfo.name }}</div>
                            <div class="summary-meta">{{ fileInfo.date }}</div>
                            <div class="summary-meta">{{ fileInfo.size }}</div>
                        </div>
                    </div>
                    <p v-else class="empty-detail-note">
                        Pilih berkas gambar di sebelah kiri terlebih dahulu untuk mengisi detail lampiran.
                    </p>

                    <div class="detail-form">
                        <div class="detail-row">
                            <label>Teks Alt</label>
                            <input
                                v-model="meta.alt"
                                type="text"
                                placeholder="Jelaskan tujuan gambar"
                            />
                        </div>

                        <div class="detail-row">
                            <label>Judul</label>
                            <input
                                v-model="meta.judul"
                                type="text"
                                placeholder="Judul gambar"
                            />
                        </div>

                        <div class="detail-row">
                            <label>Keterangan</label>
                            <textarea
                                v-model="meta.keterangan"
                                rows="2"
                                placeholder="Keterangan di bawah gambar (caption)"
                            ></textarea>
                        </div>

                        <div class="detail-row">
                            <label>Deskripsi</label>
                            <textarea
                                v-model="meta.deskripsi"
                                rows="2"
                                placeholder="Deskripsi lampiran"
                            ></textarea>
                        </div>

                        <div class="detail-row">
                            <label>URL Berkas</label>
                            <input
                                :value="uploadedUrl || previewUrl"
                                type="text"
                                readonly
                                class="readonly-url"
                            />
                            <button
                                v-if="uploadedUrl || previewUrl"
                                type="button"
                                class="btn-copy-url"
                                @click="copyUrl"
                            >
                                {{ copied ? 'Tersalin!' : 'Salin URL ke papan klip' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="media-modal-footer">
                <span v-if="uploading" class="uploading-text">Mengunggah gambar...</span>
                <button
                    type="button"
                    class="btn-insert-post"
                    :disabled="!previewUrl || uploading"
                    @click="handleInsert"
                >
                    Sisipkan ke dalam pos
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import api from '@/api/index.js'

defineProps({
    visible: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close', 'insert'])

const previewUrl = ref('')
const uploadedUrl = ref('')
const uploading = ref(false)
const copied = ref(false)

const fileInfo = reactive({
    name: '',
    size: '',
    date: '',
})

const meta = reactive({
    alt: '',
    judul: '',
    keterangan: '',
    deskripsi: '',
})

async function onSelectFile(event) {
    const file = event.target.files?.[0]
    if (!file) return

    fileInfo.name = file.name
    fileInfo.size = `${Math.max(1, Math.round(file.size / 1024))} KB`
    fileInfo.date = new Date().toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })

    const baseName = file.name.replace(/\.[^/.]+$/, '')
    meta.judul = baseName
    if (!meta.keterangan) {
        meta.keterangan = baseName
    }

    const reader = new FileReader()
    reader.onload = (e) => {
        previewUrl.value = e.target?.result || ''
    }
    reader.readAsDataURL(file)

    uploading.value = true
    try {
        const fd = new FormData()
        fd.append('file', file)
        fd.append('alt', meta.alt)
        fd.append('judul', meta.judul)
        fd.append('keterangan', meta.keterangan)
        fd.append('deskripsi', meta.deskripsi)
        const res = await api.kontributor.uploadMedia(fd)
        if (res.data?.media?.url) {
            uploadedUrl.value = res.data.media.url
        }
    } catch {
        // Fallback to Data URL if backend storage is offline
        uploadedUrl.value = ''
    } finally {
        uploading.value = false
    }
}

function copyUrl() {
    const url = uploadedUrl.value || previewUrl.value
    if (!url) return
    navigator.clipboard?.writeText(url)
    copied.value = true
    setTimeout(() => {
        copied.value = false
    }, 1800)
}

function handleInsert() {
    const src = uploadedUrl.value || previewUrl.value
    if (!src) return

    emit('insert', {
        url: src,
        alt: meta.alt || meta.judul || 'Gambar naskah',
        judul: meta.judul,
        keterangan: meta.keterangan,
        deskripsi: meta.deskripsi,
    })

    // Reset state after insert
    previewUrl.value = ''
    uploadedUrl.value = ''
    meta.alt = ''
    meta.judul = ''
    meta.keterangan = ''
    meta.deskripsi = ''
}
</script>

<style scoped>
.media-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(18, 38, 58, 0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 300;
    padding: 1.5rem;
}

.media-modal {
    background: #FFFFFF;
    width: 960px;
    max-width: 96vw;
    height: 600px;
    max-height: 90vh;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 24px 60px rgba(0, 0, 0, 0.25);
}

.media-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem 0;
    border-bottom: 1px solid var(--ktr-border);
}

.header-left {
    display: flex;
    flex-direction: column;
    gap: 0.65rem;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    color: var(--ktr-text-main);
}

.modal-tabs {
    display: flex;
    gap: 1rem;
}

.modal-tab {
    padding: 0.45rem 0.85rem;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--ktr-teal);
    border-bottom: 2px solid var(--ktr-teal);
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.75rem;
    color: var(--ktr-text-muted);
    cursor: pointer;
    line-height: 1;
}

.media-modal-body {
    flex: 1;
    display: grid;
    grid-template-columns: 1fr 330px;
    min-height: 0;
    overflow: hidden;
}

.upload-preview-pane {
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    overflow-y: auto;
    background: #FFFFFF;
}

.upload-center-box {
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.85rem;
}

.upload-prompt-title {
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--ktr-text-main);
    margin: 0;
}

.btn-choose-file {
    padding: 0.75rem 1.6rem;
    background-color: var(--ktr-teal);
    color: #FFFFFF;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
}

.btn-choose-file:hover {
    background-color: var(--ktr-teal-hover);
}

.upload-hint {
    font-size: 0.82rem;
    color: var(--ktr-text-muted);
    margin: 0;
}

.uploaded-preview-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.selected-thumb-card {
    position: relative;
    border: 3px solid #2563EB;
    border-radius: 8px;
    overflow: hidden;
    max-width: 260px;
    max-height: 220px;
}

.preview-img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.check-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 24px;
    height: 24px;
    background: #2563EB;
    color: #FFFFFF;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 700;
}

.btn-change-file {
    padding: 0.45rem 1rem;
    border: 1px solid var(--ktr-border-dark);
    background: #F8FAFC;
    color: var(--ktr-text-main);
    border-radius: 6px;
    font-size: 0.84rem;
    font-weight: 600;
    cursor: pointer;
}

.attachment-details-pane {
    background: #F8FAFC;
    border-left: 1px solid var(--ktr-border);
    padding: 1.25rem;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.details-heading {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--ktr-text-muted);
    margin: 0;
    letter-spacing: 0.04em;
}

.attachment-summary {
    display: flex;
    gap: 0.75rem;
    padding-bottom: 0.9rem;
    border-bottom: 1px solid var(--ktr-border);
}

.summary-thumb {
    width: 64px;
    height: 64px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid var(--ktr-border);
}

.summary-filename {
    font-size: 0.84rem;
    font-weight: 700;
    color: var(--ktr-text-main);
    word-break: break-all;
}

.summary-meta {
    font-size: 0.76rem;
    color: var(--ktr-text-muted);
    margin-top: 0.15rem;
}

.empty-detail-note {
    font-size: 0.82rem;
    color: var(--ktr-text-muted);
    margin: 0;
}

.detail-form {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.detail-row {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.detail-row label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--ktr-text-muted);
}

.detail-row input,
.detail-row textarea {
    width: 100%;
    padding: 0.48rem 0.65rem;
    border: 1px solid var(--ktr-border-dark);
    border-radius: 6px;
    font-size: 0.84rem;
    color: var(--ktr-text-main);
    background: #FFFFFF;
    font-family: inherit;
}

.readonly-url {
    background: #F1F5F9 !important;
    color: var(--ktr-text-muted) !important;
}

.btn-copy-url {
    align-self: flex-start;
    margin-top: 0.25rem;
    padding: 0.35rem 0.7rem;
    font-size: 0.78rem;
    border: 1px solid #2563EB;
    color: #2563EB;
    background: #FFFFFF;
    border-radius: 5px;
    cursor: pointer;
}

.media-modal-footer {
    padding: 0.85rem 1.5rem;
    border-top: 1px solid var(--ktr-border);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 1rem;
    background: #FFFFFF;
}

.uploading-text {
    font-size: 0.85rem;
    color: var(--ktr-text-muted);
}

.btn-insert-post {
    padding: 0.6rem 1.25rem;
    background-color: #2563EB;
    color: #FFFFFF;
    border: none;
    border-radius: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    cursor: pointer;
}

.btn-insert-post:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

@media (max-width: 760px) {
    .media-modal-body {
        grid-template-columns: 1fr;
    }
}
</style>
