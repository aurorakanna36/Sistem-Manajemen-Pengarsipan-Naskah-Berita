<template>
    <KontributorLayout>
        <div class="revisi-naskah-page">
            <header class="page-topbar">
                <div class="topbar-titles">
                    <h1 class="page-title">Kirim ulang naskah</h1>
                    <p class="page-subtitle">Perbaiki naskah sesuai catatan admin, lalu kirim ulang untuk direview.</p>
                </div>
            </header>

            <div class="revisi-layout">
                <form class="revisi-card" @submit.prevent="submitNaskah">
                    <div v-if="errorMessage" class="alert-box alert-error">
                        {{ errorMessage }}
                    </div>
                    <div v-if="successMessage" class="alert-box alert-success">
                        {{ successMessage }}
                    </div>

                    <article class="naskah-main-card">
                        <div class="document-card">
                            <div class="document-icon"></div>

                            <div class="document-info">
                                <h3>{{ naskah.judul || form.judul || 'Judul naskah' }}</h3>

                                <p class="naskah-meta">
                                    Dikirim {{ formatDate(naskah.waktu_kirim || naskah.created_at) }}
                                </p>
                            </div>

                           <span class="status-pill" :class="badgeClass(currentStatus)">
                                {{ labelStatus(currentStatus) }}
                            </span>

                        </div>

                        <div class="form-field">
                            <label class="field-label">
                                Judul naskah <span class="req">*</span>
                            </label>
                            <input v-model="form.judul" type="text" class="text-input"
                                placeholder="Masukkan judul naskah yang jelas dan informatif" required />
                        </div>

                        <!-- Kategori (Multi-Select Radio Style Buttons) -->
                        <div class="form-field">
                            <div class="label-row">
                                <label class="field-label">
                                    Kategori <span class="req">*</span>
                                </label>
                                <span class="field-hint">
                                    Pilih satu atau lebih kategori yang sesuai dengan isi naskah ({{
                                        selectedKategoriIds.length
                                    }} dipilih)
                                </span>
                            </div>

                            <div class="kategori-radio-grid">
                                <button v-for="kat in kategoriOptions" :key="kat.id" type="button"
                                    class="radio-multi-btn" :class="{ selected: isKategoriSelected(kat.id) }"
                                    @click="toggleKategori(kat.id)">
                                    <span class="radio-circle">
                                        <span v-if="isKategoriSelected(kat.id)" class="radio-dot"></span>
                                    </span>
                                    <span class="radio-label">{{ kat.nama_kategori }}</span>
                                </button>
                            </div>
                        </div>

                        <!-- Isi Naskah (Rich Text Editor sesuai fitur isi naskah.png & fitur heading.png) -->
                        <div class="form-field">
                            <div class="editor-top-row">
                                <label class="field-label">
                                    Isi naskah <span class="req">*</span>
                                </label>
                                <button type="button" class="btn-add-media" @click="openMediaModal">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                        <circle cx="8.5" cy="8.5" r="1.5" />
                                        <polyline points="21 15 16 10 5 21" />
                                    </svg>
                                    Tambahkan Media
                                </button>
                            </div>

                            <div class="rich-editor-shell">
                                <!-- Baris Toolbar 1 -->
                                <div class="editor-toolbar">
                                    <!-- Dropdown Heading / Format Blok -->
                                    <div class="heading-dropdown-wrap">
                                        <button type="button" class="heading-trigger"
                                            @click="showHeadingMenu = !showHeadingMenu">
                                            <span>{{ currentHeadingLabel }}</span>
                                            <span class="caret">&#9662;</span>
                                        </button>
                                        <div v-if="showHeadingMenu" class="heading-menu">
                                            <button v-for="h in headingOptions" :key="h.tag" type="button"
                                                class="heading-option"
                                                :class="[h.previewClass, { active: currentBlockTag === h.tag }]"
                                                @click="applyHeading(h)">
                                                {{ h.label }}
                                            </button>
                                        </div>
                                    </div>

                                    <span class="toolbar-divider"></span>

                                    <!-- Bold & Italic -->
                                    <button type="button" class="tool-btn" title="Bold (Ctrl+B)"
                                        @click="execCmd('bold')">
                                        <strong>B</strong>
                                    </button>
                                    <button type="button" class="tool-btn" title="Italic (Ctrl+I)"
                                        @click="execCmd('italic')">
                                        <em>I</em>
                                    </button>

                                    <!-- List tanpa numbering & List numbering -->
                                    <button type="button" class="tool-btn" title="List tanpa numbering"
                                        @click="execCmd('insertUnorderedList')">
                                        &#8226;&#8801;
                                    </button>
                                    <button type="button" class="tool-btn" title="List numbering"
                                        @click="execCmd('insertOrderedList')">
                                        1&#8801;
                                    </button>

                                    <!-- Kutipan (Blockquote) -->
                                    <button type="button" class="tool-btn" title="Kutipan" @click="toggleBlockquote">
                                        &#10078;
                                    </button>

                                    <!-- Alignment -->
                                    <button type="button" class="tool-btn" title="Rata Kiri"
                                        @click="execCmd('justifyLeft')">
                                        &#8676;
                                    </button>
                                    <button type="button" class="tool-btn" title="Rata Tengah"
                                        @click="execCmd('justifyCenter')">
                                        &#8596;
                                    </button>
                                    <button type="button" class="tool-btn" title="Rata Kanan"
                                        @click="execCmd('justifyRight')">
                                        &#8677;
                                    </button>
                                    <button type="button" class="tool-btn" title="Rata Kanan Kiri (Justify)"
                                        @click="execCmd('justifyFull')">
                                        &#8801;
                                    </button>

                                    <!-- Sisipkan Link -->
                                    <button type="button" class="tool-btn" title="Sisipkan Link"
                                        @click="promptInsertLink">
                                        &#128279;
                                    </button>

                                    <span class="toolbar-divider"></span>

                                    <!-- Coretan di tengah tulisan (Strikethrough) -->
                                    <button type="button" class="tool-btn" title="Coretan di tengah tulisan"
                                        @click="execCmd('strikeThrough')">
                                        <s>ABE</s>
                                    </button>

                                    <!-- Garis mendatar (Horizontal rule) -->
                                    <button type="button" class="tool-btn" title="Garis mendatar"
                                        @click="execCmd('insertHorizontalRule')">
                                        &#8213;
                                    </button>

                                    <!-- Text Color -->
                                    <div class="color-picker-wrap" title="Warna Teks">
                                        <label class="tool-btn color-btn">
                                            <span class="color-letter"
                                                :style="{ borderBottomColor: currentTextColor }">A</span>
                                            <input v-model="currentTextColor" type="color" class="hidden-color-input"
                                                @input="applyTextColor" />
                                        </label>
                                    </div>

                                    <!-- Karakter Khusus -->
                                    <div class="special-char-wrap">
                                        <button type="button" class="tool-btn" title="Karakter khusus"
                                            @click="showSpecialChars = !showSpecialChars">
                                            &Omega;
                                        </button>
                                        <div v-if="showSpecialChars" class="special-char-popover">
                                            <button v-for="ch in specialCharacters" :key="ch" type="button"
                                                class="char-cell" @click="insertSpecialChar(ch)">
                                                {{ ch }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Persempit & Perlebar Inden -->
                                    <button type="button" class="tool-btn" title="Persempit inden (Outdent)"
                                        @click="execCmd('outdent')">
                                        &#8672;
                                    </button>
                                    <button type="button" class="tool-btn" title="Perlebar inden (Indent)"
                                        @click="execCmd('indent')">
                                        &#8674;
                                    </button>

                                    <!-- Undo & Redo -->
                                    <button type="button" class="tool-btn" title="Undo (Ctrl+Z)"
                                        @click="execCmd('undo')">
                                        &#8630;
                                    </button>
                                    <button type="button" class="tool-btn" title="Redo (Ctrl+Y)"
                                        @click="execCmd('redo')">
                                        &#8631;
                                    </button>
                                </div>

                                <!-- Area Isi Naskah ContentEditable -->
                                <div ref="editorRef" class="editor-canvas" contenteditable="true"
                                    data-placeholder="Tulis isi naskah lengkap di sini..." @input="onEditorInput"
                                    @click="closeMenus"></div>

                                <div class="editor-statusbar">
                                    <span>Jumlah kata: {{ wordCount }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Gambar Sampul (Berkas Naskah PDF/DOCX Dihapus) -->
                        <div class="form-field">
                            <label class="field-label">Gambar sampul</label>

                            <div class="sampul-dropzone" :class="{ 'has-preview': sampulPreviewUrl }"
                                @click="$refs.sampulInput.click()">
                                <template v-if="!sampulPreviewUrl">
                                    <span class="sampul-title">Klik untuk unggah gambar sampul</span>
                                    <span class="sampul-sub">PNG, JPG maksimal 2 MB</span>
                                </template>

                                <div v-else class="sampul-preview-container" @click.stop>
                                    <img :src="sampulPreviewUrl" alt="Preview Gambar Sampul"
                                        class="sampul-preview-img" />
                                    <div class="sampul-preview-actions">
                                        <span class="sampul-filename">{{ sampulFileName }}</span>
                                        <div class="sampul-btns">
                                            <button type="button" class="btn-sampul-action"
                                                @click="insertSampulToEditor">
                                                Sisipkan juga ke isi naskah
                                            </button>
                                            <button type="button" class="btn-sampul-action"
                                                @click="$refs.sampulInput.click()">
                                                Ganti gambar
                                            </button>
                                            <button type="button" class="btn-sampul-remove" @click="removeSampul">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <input ref="sampulInput" type="file" accept="image/png,image/jpeg,image/webp" hidden
                                    @change="onSelectSampul" />
                            </div>
                        </div>

                        <!-- Footer Tombol Aksi -->
                        <div class="form-field">
                            <label class="field-label">Catatan untuk admin (Opsional)</label>
                            <textarea v-model="form.catatan_admin" class="textarea-input" rows="3"
                                placeholder="Jelaskan perubahan yang sudah kamu lakukan"></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn-draft" :disabled="submitting"
                                @click="router.push('/kontributor/dashboard')">
                                Batal
                            </button>
                            <button type="button" class="btn-save" :disabled="submitting" @click="saveDraft">
                                Simpan draf
                            </button>
                            <button type="submit" class="btn-submit" :disabled="submitting">
                                {{ submitting ? 'Mengirim...' : 'Kirim ulang naskah' }}
                            </button>
                        </div>
                    </article>
                </form>

                <aside class="naskah-side-col">
                    <section class="side-card revision-note">
                        <h2 class="side-title">Catatan revisi dari admin</h2>
                        <ul class="revision-list">
                            <li>Perbaiki judul agar lebih jelas dan menarik.</li>
                            <li>Pastikan isi naskah sesuai dengan kategori yang dipilih.</li>
                            <li>Periksa kembali ejaan, tanda baca, dan kelengkapan informasi.</li>
                        </ul>
                    </section>
                    <section class="side-card reminder-card">
                        <h2 class="side-title">Sebelum mengirim ulang</h2>
                        <ul class="reminder-list">
                            <li>Pastikan semua catatan revisi sudah diperbaiki.</li>
                            <li>Naskah akan masuk antrean review setelah dikirim.</li>
                        </ul>
                    </section>
                </aside>
            </div>

            <!-- Popup Sisipkan Media ke Isi Naskah -->
            <MediaUploadModal :visible="showMediaModal" @close="showMediaModal = false"
                @insert="handleInsertMediaToEditor" />
        </div>
    </KontributorLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import KontributorLayout from './KontributorLayout.vue'
import MediaUploadModal from './MediaUploadModal.vue'
import api from '@/api/index.js'
import Swal from 'sweetalert2'

const router = useRouter()
const userRaw = localStorage.getItem('user_info')
const user = userRaw ? JSON.parse(userRaw) : null

const userName = computed(() => user?.nama_lengkap || user?.name || 'Kontributor')
const userInitial = computed(() => userName.value.charAt(0).toUpperCase() || 'K')

const defaultKategoriList = [
    { id: 1, nama_kategori: 'Berita', slug: 'berita' },
    { id: 2, nama_kategori: 'Nasional', slug: 'nasional' },
    { id: 3, nama_kategori: 'Pendidikan', slug: 'pendidikan' },
    { id: 4, nama_kategori: 'Teknologi', slug: 'teknologi' },
    { id: 5, nama_kategori: 'Bisnis', slug: 'bisnis' },
    { id: 6, nama_kategori: 'Daerah', slug: 'daerah' },
    { id: 7, nama_kategori: 'Ekonomi', slug: 'ekonomi' },
    { id: 8, nama_kategori: 'Kriminal', slug: 'kriminal' },
    { id: 9, nama_kategori: 'Olahraga', slug: 'olahraga' },
    { id: 10, nama_kategori: 'Opini', slug: 'opini' },
    { id: 11, nama_kategori: 'Otomotif', slug: 'otomotif' },
    { id: 12, nama_kategori: 'Politik', slug: 'politik' },
    { id: 13, nama_kategori: 'Uncategorized', slug: 'uncategorized' },
]

const kategoriOptions = ref([...defaultKategoriList])
const selectedKategoriIds = ref([])

const form = reactive({
    judul: '',
    isi: '',
    catatan_admin: '',
})

const naskah = ref({
    judul: '',
    waktu_kirim: null,
    created_at: null
})

const currentStatus = ref('revisi')

const editorRef = ref(null)
const showHeadingMenu = ref(false)
const showSpecialChars = ref(false)
const currentBlockTag = ref('P')
const currentHeadingLabel = ref('Paragraf')
const currentTextColor = ref('#12263A')

const headingOptions = [
    { label: 'Paragraf', tag: 'P', previewClass: 'opt-p' },
    { label: 'Heading 1', tag: 'H1', previewClass: 'opt-h1' },
    { label: 'Heading 2', tag: 'H2', previewClass: 'opt-h2' },
    { label: 'Heading 3', tag: 'H3', previewClass: 'opt-h3' },
    { label: 'Heading 4', tag: 'H4', previewClass: 'opt-h4' },
    { label: 'Heading 5', tag: 'H5', previewClass: 'opt-h5' },
    { label: 'Heading 6', tag: 'H6', previewClass: 'opt-h6' },
    { label: 'Preformatted', tag: 'PRE', previewClass: 'opt-pre' },
]

const specialCharacters = [
    '©', '®', '™', '€', '£', '¥', '§', '¶',
    '•', '—', '–', '“', '”', '‘', '’', '«',
    '»', '±', '×', '÷', '≠', '≤', '≥', '∞',
    'α', 'β', 'Δ', 'Ω', '←', '→', '↔', '✓',
]

const showMediaModal = ref(false)
const sampulFile = ref(null)
const sampulFileName = ref('')
const sampulPreviewUrl = ref('')

const submitting = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const wordCount = computed(() => {
    const plain = (form.isi || '')
        .replace(/<[^>]*>/g, ' ')
        .replace(/&nbsp;/g, ' ')
        .trim()
    if (!plain) return 0
    return plain.split(/\s+/).length
})

onMounted(async () => {
    try {
        const res = await api.kontributor.getKategoris()
        const list = res.data?.kategoris || res.data?.data || []
        if (list.length > 0) {
            kategoriOptions.value = list
        }
    } catch {
        // Use fallback 13 categories
    }
})

function formatDate(date) {
    if (!date) return '-'

    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    })
}

function badgeClass(status) {
    return 'status-' + String(status || '').toLowerCase()
}

function labelStatus(status) {
    const labels = {
        revisi: 'Perlu Revisi',
        pending: 'Menunggu Review',
        approved: 'Disetujui',
        rejected: 'Ditolak'
    }

    return labels[status] || status || '-'
}

function isKategoriSelected(id) {
    return selectedKategoriIds.value.includes(id)
}

function toggleKategori(id) {
    const idx = selectedKategoriIds.value.indexOf(id)
    if (idx >= 0) {
        selectedKategoriIds.value.splice(idx, 1)
    } else {
        selectedKategoriIds.value.push(id)
    }
}

function closeMenus() {
    showHeadingMenu.value = false
    showSpecialChars.value = false
}

function onEditorInput() {
    if (editorRef.value) {
        form.isi = editorRef.value.innerHTML
    }
}

function execCmd(command, value = null) {
    editorRef.value?.focus()
    document.execCommand(command, false, value)
    onEditorInput()
}

function applyHeading(option) {
    currentBlockTag.value = option.tag
    currentHeadingLabel.value = option.label
    showHeadingMenu.value = false
    editorRef.value?.focus()
    document.execCommand('formatBlock', false, `<${option.tag}>`)
    onEditorInput()
}

function toggleBlockquote() {
    editorRef.value?.focus()
    document.execCommand('formatBlock', false, '<BLOCKQUOTE>')
    onEditorInput()
}

function promptInsertLink() {
    const url = window.prompt('Masukkan URL link (contoh: https://cakrawala.com):', 'https://')
    if (url && url !== 'https://') {
        execCmd('createLink', url)
    }
}

function applyTextColor(e) {
    const color = e.target.value
    currentTextColor.value = color
    execCmd('foreColor', color)
}

function insertSpecialChar(ch) {
    showSpecialChars.value = false
    execCmd('insertText', ch)
}

function openMediaModal() {
    closeMenus()
    showMediaModal.value = true
}

function handleInsertMediaToEditor(media) {
    showMediaModal.value = false
    editorRef.value?.focus()

    const captionHtml = media.keterangan
        ? `<figcaption>${media.keterangan}</figcaption>`
        : ''
    const html = `<figure class="inserted-media"><img src="${media.url}" alt="${media.alt || ''}" title="${media.judul || ''}" />${captionHtml}</figure><p><br></p>`

    document.execCommand('insertHTML', false, html)
    onEditorInput()
}

function onSelectSampul(event) {
    const file = event.target.files?.[0]
    if (!file) return
    sampulFile.value = file
    sampulFileName.value = file.name

    const reader = new FileReader()
    reader.onload = (e) => {
        sampulPreviewUrl.value = e.target?.result || ''
    }
    reader.readAsDataURL(file)
}

function removeSampul() {
    sampulFile.value = null
    sampulFileName.value = ''
    sampulPreviewUrl.value = ''
}

function insertSampulToEditor() {
    if (!sampulPreviewUrl.value) return
    handleInsertMediaToEditor({
        url: sampulPreviewUrl.value,
        alt: form.judul || sampulFileName.value,
        judul: form.judul || sampulFileName.value,
        keterangan: form.judul || '',
    })
}


async function saveDraft() {
    onEditorInput()
    if (!form.judul.trim()) {
        await Swal.fire('Judul belum diisi', 'Isi judul naskah terlebih dahulu.', 'warning')
        return
    }
    await Swal.fire({
        icon: 'info',
        title: 'Draf siap disimpan',
        text: 'Fitur penyimpanan draf ke server perlu disambungkan ke endpoint backend.',
        confirmButtonColor: '#0d5c4d'
    })
}

async function submitNaskah() {
    errorMessage.value = ''
    successMessage.value = ''

    onEditorInput()

    // Validasi kategori
    if (selectedKategoriIds.value.length === 0) {
        errorMessage.value = 'Silakan pilih minimal satu kategori untuk naskah Anda.'

        Swal.fire({
            icon: 'warning',
            title: 'Kategori Belum Dipilih',
            text: errorMessage.value,
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#0d5c4d'
        })

        return
    }

    // Validasi isi naskah
    const plainText = (form.isi || '').replace(/<[^>]*>/g, '').trim()
    const hasImage = /<img\s/i.test(form.isi || '')

    if (!plainText && !hasImage) {
        errorMessage.value = 'Isi naskah wajib diisi.'

        Swal.fire({
            icon: 'warning',
            title: 'Isi Naskah Belum Diisi',
            text: errorMessage.value,
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#0d5c4d'
        })

        return
    }

    // Pop-up konfirmasi sebelum mengirim naskah
    const result = await Swal.fire({
        icon: 'question',
        title: 'Kirim Naskah?',
        text: 'Apakah kamu yakin ingin mengirim naskah ini ke redaksi Harian Cakrawala? Setelah dikirim, naskah akan masuk ke tahap review oleh redaksi.',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim Naskah',
        cancelButtonText: 'Periksa Kembali',
        confirmButtonColor: '#0d5c4d',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        allowOutsideClick: false
    })

    // Jika kontributor memilih batal
    if (!result.isConfirmed) return

    submitting.value = true

    try {
        const fd = new FormData()

        fd.append('judul', form.judul)
        fd.append('isi', form.isi)
        fd.append('catatan_admin', form.catatan_admin || '')

        selectedKategoriIds.value.forEach((id) => {
            fd.append('kategori_ids[]', id)
        })

        const selectedNames = kategoriOptions.value
            .filter((k) => selectedKategoriIds.value.includes(k.id))
            .map((k) => k.nama_kategori)
            .join(', ')

        fd.append('kategori', selectedNames)

        if (sampulFile.value) {
            fd.append('gambar_sampul', sampulFile.value)
        }

        await api.kontributor.storeNaskah(fd)

        successMessage.value = 'Naskah berhasil dikirim!'

        // Pop-up berhasil
        await Swal.fire({
            icon: 'success',
            title: 'Naskah Berhasil Dikirim!',
            text: 'Naskah kamu telah berhasil dikirim dan sedang menunggu review dari redaksi Harian Cakrawala.',
            confirmButtonText: 'Kembali ke Dashboard',
            confirmButtonColor: '#0d5c4d',
            allowOutsideClick: false
        })

        router.push('/kontributor/dashboard')

    } catch (err) {
        errorMessage.value = err.response?.data?.message ||
            'Gagal mengirim naskah. Periksa kembali isian Anda.'

        // Pop-up gagal
        await Swal.fire({
            icon: 'error',
            title: 'Gagal Mengirim Naskah',
            text: errorMessage.value,
            confirmButtonText: 'Mengerti',
            confirmButtonColor: '#0d5c4d'
        })

    } finally {
        submitting.value = false
    }
}

</script>

<style scoped>
.revisi-naskah-page {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.page-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}

.page-title {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--ktr-text-main);
    margin: 0;
}

.page-subtitle {
    color: var(--ktr-text-muted);
    font-size: 0.95rem;
    margin: 0.3rem 0 0;
}

.user-chip {
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

.user-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background-color: var(--ktr-teal);
    color: var(--ktr-white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
}

.user-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--ktr-text-main);
}

.revisi-card {
    background-color: var(--ktr-bg-card);
    border: 1px solid var(--ktr-border);
    border-radius: 14px;
    padding: 2rem 2.25rem;
    display: flex;
    flex-direction: column;
    gap: 1.45rem;
}

.alert-box {
    padding: 0.85rem 1.15rem;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
}

.alert-error {
    background-color: var(--ktr-danger-bg);
    color: var(--ktr-danger-text);
}

.alert-success {
    background-color: var(--ktr-success-bg);
    color: var(--ktr-success-text);
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}

.label-row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.field-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--ktr-text-main);
}

.field-hint {
    font-size: 0.8rem;
    color: var(--ktr-text-muted);
}

.req {
    color: #EF4444;
}

.text-input,
.textarea-input {
    width: 100%;
    padding: 0.78rem 1rem;
    border: 1px solid var(--ktr-border);
    border-radius: 10px;
    background-color: #FAFCFF;
    font-size: 0.92rem;
    color: var(--ktr-text-main);
    font-family: inherit;
    transition: border-color 0.15s, background-color 0.15s;
}

.text-input:focus,
.textarea-input:focus {
    outline: none;
    border-color: var(--ktr-teal);
    background-color: #FFFFFF;
}

/* Kumpulan Radio Button Multi-Kategori */
.kategori-radio-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
}

.radio-multi-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.55rem 1rem;
    border-radius: 999px;
    border: 1.5px solid var(--ktr-border);
    background-color: #FAFCFF;
    color: var(--ktr-text-main);
    font-size: 0.88rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s;
}

.radio-multi-btn:hover {
    border-color: var(--ktr-teal);
    background-color: #F0F9F6;
}

.radio-multi-btn.selected {
    border-color: var(--ktr-teal);
    background-color: var(--ktr-teal-light);
    color: var(--ktr-teal);
    font-weight: 700;
}

.radio-circle {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid var(--ktr-border-dark);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #FFFFFF;
    flex-shrink: 0;
}

.radio-multi-btn.selected .radio-circle {
    border-color: var(--ktr-teal);
}

.radio-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: var(--ktr-teal);
}

/* Rich Text Editor */
.editor-top-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.btn-add-media {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.45rem 0.9rem;
    border: 1.5px solid var(--ktr-teal);
    background-color: #F0F9F6;
    color: var(--ktr-teal);
    border-radius: 7px;
    font-size: 0.84rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.15s;
}

.btn-add-media:hover {
    background-color: #DDF2EE;
}

.rich-editor-shell {
    border: 1px solid var(--ktr-border);
    border-radius: 10px;
    background-color: #FFFFFF;
    overflow: visible;
}

.editor-toolbar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem;
    padding: 0.55rem 0.75rem;
    background-color: #F4F7FA;
    border-bottom: 1px solid var(--ktr-border);
    border-radius: 10px 10px 0 0;
}

.heading-dropdown-wrap {
    position: relative;
}

.heading-trigger {
    display: inline-flex;
    align-items: center;
    justify-content: space-between;
    min-width: 135px;
    padding: 0.36rem 0.7rem;
    background: #FFFFFF;
    border: 1px solid var(--ktr-border-dark);
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--ktr-text-main);
    cursor: pointer;
}

.heading-menu {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    width: 210px;
    background: #FFFFFF;
    border: 1px solid var(--ktr-border-dark);
    border-radius: 8px;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.14);
    z-index: 60;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.heading-option {
    text-align: left;
    padding: 0.5rem 0.85rem;
    background: none;
    border: none;
    border-bottom: 1px solid #F1F5F9;
    cursor: pointer;
    color: var(--ktr-text-main);
}

.heading-option:hover,
.heading-option.active {
    background-color: var(--ktr-teal-light);
}

.opt-p {
    font-size: 0.9rem;
    font-weight: 400;
}

.opt-h1 {
    font-size: 1.55rem;
    font-weight: 800;
}

.opt-h2 {
    font-size: 1.3rem;
    font-weight: 700;
}

.opt-h3 {
    font-size: 1.12rem;
    font-weight: 700;
}

.opt-h4 {
    font-size: 1rem;
    font-weight: 700;
}

.opt-h5 {
    font-size: 0.88rem;
    font-weight: 700;
}

.opt-h6 {
    font-size: 0.78rem;
    font-weight: 700;
}

.opt-pre {
    font-family: monospace;
    font-size: 0.85rem;
}

.toolbar-divider {
    width: 1px;
    height: 22px;
    background-color: var(--ktr-border);
    margin: 0 0.25rem;
}

.tool-btn {
    min-width: 32px;
    height: 32px;
    padding: 0 0.45rem;
    border: 1px solid transparent;
    border-radius: 6px;
    background: transparent;
    color: var(--ktr-green-muted);
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.tool-btn:hover {
    background-color: #E2E8F0;
    color: var(--ktr-text-main);
}

.color-picker-wrap {
    position: relative;
}

.color-btn {
    cursor: pointer;
}

.color-letter {
    font-weight: 800;
    border-bottom: 3px solid #12343a;
    line-height: 1;
    padding-bottom: 1px;
}

.hidden-color-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.special-char-wrap {
    position: relative;
}

.special-char-popover {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    width: 240px;
    background: #FFFFFF;
    border: 1px solid var(--ktr-border-dark);
    border-radius: 8px;
    padding: 0.5rem;
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 0.2rem;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.14);
    z-index: 60;
}

.char-cell {
    height: 26px;
    border: 1px solid #E2E8F0;
    background: #FFFFFF;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
}

.char-cell:hover {
    background: var(--ktr-teal-light);
    border-color: var(--ktr-teal);
}

.editor-canvas {
    min-height: 260px;
    padding: 1.15rem 1.25rem;
    font-size: 0.98rem;
    line-height: 1.7;
    color: var(--ktr-text-main);
    outline: none;
}

.editor-canvas:empty::before {
    content: attr(data-placeholder);
    color: var(--ktr-text-light);
    pointer-events: none;
}

.editor-canvas :deep(h1) {
    font-size: 1.85rem;
    font-weight: 800;
    margin: 0.9rem 0 0.45rem;
}

.editor-canvas :deep(h2) {
    font-size: 1.45rem;
    font-weight: 700;
    margin: 0.8rem 0 0.4rem;
}

.editor-canvas :deep(h3) {
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0.7rem 0 0.35rem;
}

.editor-canvas :deep(blockquote) {
    border-left: 4px solid var(--ktr-teal);
    padding-left: 1rem;
    margin: 0.85rem 0;
    color: var(--ktr-text-muted);
    font-style: italic;
}

.editor-canvas :deep(figure) {
    margin: 1rem 0;
    display: inline-block;
    max-width: 100%;
}

.editor-canvas :deep(img) {
    max-width: 360px;
    height: auto;
    border-radius: 8px;
    display: block;
}

.editor-canvas :deep(figcaption) {
    font-size: 0.84rem;
    color: var(--ktr-text-muted);
    margin-top: 0.35rem;
}

.editor-statusbar {
    padding: 0.45rem 1rem;
    border-top: 1px solid var(--ktr-border);
    background-color: #F8FAFC;
    font-size: 0.78rem;
    color: var(--ktr-text-muted);
    border-radius: 0 0 10px 10px;
}

/* Gambar Sampul */
.sampul-dropzone {
    border: 1.5px dashed var(--ktr-border-dark);
    border-radius: 12px;
    background-color: #FAFCFF;
    padding: 1.75rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    cursor: pointer;
    transition: border-color 0.15s, background-color 0.15s;
}

.sampul-dropzone:hover {
    border-color: var(--ktr-teal);
    background-color: #F0F9F6;
}

.sampul-title {
    font-size: 0.92rem;
    font-weight: 600;
    color: var(--ktr-text-muted);
}

.sampul-sub {
    font-size: 0.8rem;
    color: var(--ktr-text-light);
}

.sampul-preview-container {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: wrap;
}

.sampul-preview-img {
    width: 180px;
    height: 115px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid var(--ktr-border);
}

.sampul-preview-actions {
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}

.sampul-filename {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--ktr-text-main);
}

.sampul-btns {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.btn-sampul-action {
    padding: 0.42rem 0.85rem;
    border: 1px solid var(--ktr-border-dark);
    background: #FFFFFF;
    color: var(--ktr-text-main);
    border-radius: 7px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
}

.btn-sampul-remove {
    padding: 0.42rem 0.85rem;
    border: 1px solid #FCA5A5;
    background: #FEE2E2;
    color: #991B1B;
    border-radius: 7px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
}

.form-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.85rem;
    margin-top: 0.5rem;
}

.btn-draft {
    padding: 0.72rem 1.35rem;
    border: 1px solid var(--ktr-border-dark);
    background-color: #FFFFFF;
    color: var(--ktr-text-main);
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
}

.btn-submit {
    padding: 0.72rem 1.65rem;
    border: none;
    background-color: var(--ktr-teal);
    color: #FFFFFF;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
}

.btn-submit:hover:not(:disabled) {
    background-color: var(--ktr-teal-hover);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Tata letak halaman mengikuti referensi desain */
.revisi-naskah-page {
    width: 100%;
    max-width: 1180px;
    margin: 0 auto;
    gap: 1rem;
}

.page-topbar {
    align-items: flex-start;
    margin-bottom: .1rem;
}

.topbar-titles {
    display: flex;
    flex-direction: column;
    gap: .28rem;
}

.back-link {
    width: fit-content;
    border: 0;
    padding: 0;
    margin: 0 0 .3rem;
    background: transparent;
    color: var(--ktr-teal);
    font-size: .78rem;
    font-weight: 700;
    cursor: pointer;
}

.page-title {
    font-size: 1.85rem;
    font-weight: 800;
    color: var(--ktr-text-main);
    margin: 0;
}

.page-subtitle {
    color: var(--ktr-text-muted);
    font-size: 0.95rem;
    margin: 0.3rem 0 0;
}

.revisi-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 340px;
    gap: 1.75rem;
    align-items: start;
}

.revisi-card {
    width: 100%;
    min-width: 0;
    box-sizing: border-box;
    padding: 2rem 2.25rem;
    border: 1px solid var(--ktr-border);
    border-radius: 14px;
    background-color: var(--ktr-bg-card);
    display: flex;
    flex-direction: column;
    gap: 1.45rem;
}

.naskah-main-card {
    display: flex;
    flex-direction: column;
    gap: .85rem;
}

.document-card {
    display: flex;
    align-items: center;
    gap: .7rem;
    padding: .65rem .75rem;
    border: 1px solid var(--ktr-border);
    border-radius: 9px;
    background: #f8fafc;
}

.document-icon {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    border-radius: 6px;
    background: #dceaf4;
    position: relative;
}

.document-info {
    min-width: 0;
    flex: 1;
}

.document-info h3 {
    margin: 0;
    color: var(--ktr-text-main);
    font-size: 0.9rem;
    font-weight: 600;
}

.naskah-meta {
    margin: .18rem 0 0;
    color: var(--ktr-text-muted);
    font-size: .68rem;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.34rem 0.85rem;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
}

.form-field {
    gap: .38rem;
}

.field-label {
    font-size: .76rem;
}

.field-hint {
    font-size: .65rem;
}

.text-input,
.textarea-input {
    padding: .58rem .72rem;
    border-radius: 7px;
    font-size: .76rem;
}

.kategori-radio-grid {
    gap: .38rem;
}

.radio-multi-btn {
    padding: .36rem .65rem;
    gap: .35rem;
    font-size: .68rem;
}

.radio-circle {
    width: 13px;
    height: 13px;
}

.radio-dot {
    width: 6px;
    height: 6px;
}

.editor-top-row {
    align-items: center;
}

.btn-add-media {
    padding: .32rem .55rem;
    font-size: .68rem;
}

.rich-editor-shell {
    border-radius: 8px;
}

.editor-toolbar {
    padding: .35rem .45rem;
    gap: .1rem;
}

.heading-trigger {
    min-width: 90px;
    padding: .28rem .45rem;
    font-size: .68rem;
}

.tool-btn {
    min-width: 24px;
    height: 25px;
    padding: 0 .25rem;
    font-size: .72rem;
}

.editor-canvas {
    min-height: 150px;
    padding: .75rem .8rem;
    font-size: .75rem;
    line-height: 1.55;
}

.editor-statusbar {
    padding: .32rem .7rem;
    font-size: .65rem;
}

.sampul-dropzone {
    padding: .8rem;
    border-radius: 8px;
}

.sampul-title {
    font-size: .72rem;
}

.sampul-sub {
    font-size: .65rem;
}

.sampul-preview-img {
    width: 70px;
    height: 48px;
}

.sampul-filename {
    font-size: .7rem;
}

.sampul-btns {
    gap: .3rem;
}

.btn-sampul-action,
.btn-sampul-remove {
    padding: .28rem .45rem;
    font-size: .64rem;
}

.form-actions {
    justify-content: flex-end;
    gap: .45rem;
    margin-top: .1rem;
}

.btn-draft,
.btn-save,
.btn-submit {
    padding: .55rem .8rem;
    border-radius: 7px;
    font-size: .7rem;
}

.btn-save {
    border: 1px solid var(--ktr-teal);
    background: #fff;
    color: var(--ktr-teal);
    font-weight: 700;
    cursor: pointer;
}

.btn-save:disabled {
    opacity: .6;
    cursor: not-allowed;
}

.naskah-side-col {
    display: flex;
    flex-direction: column;
    gap: .8rem;
}

.side-card {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1.5rem;
    border: 1px solid var(--ktr-border);
    border-radius: 14px;
    background-color: var(--ktr-bg-card);
}

.revision-note {
    background: #fff7dc;
    border-color: #f1d47a;
}

.side-title {
    margin: 0;
    color: var(--ktr-text-main);
    font-size: 1.05rem;
    font-weight: 700;
}

.revision-list,
.reminder-list {
    margin: 0;
    padding-left: 1rem;
    color: var(--ktr-text-muted);
    font-size: 0.84rem;
    line-height: 1.55;
    font-weight: 600;
}

.revision-list li,
.reminder-list li {
    margin: .25rem 0;
}

.reminder-card {
    background: #fff;
}

@media (max-width: 980px) {
    .revisi-layout {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .revisi-card {
        padding: 1.25rem;
    }

    .side-card {
        padding: 1.2rem;
    }
}

@media (max-width: 760px) {
    .revisi-layout {
        grid-template-columns: 1fr;
    }

    .naskah-side-col {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        width: 100%;
        min-width: 0;
    }

    .revisi-card {
        padding: 1rem;
    }

    .page-topbar {
        flex-direction: column;
    }

    .user-chip {
        align-self: flex-end;
    }
}
</style>