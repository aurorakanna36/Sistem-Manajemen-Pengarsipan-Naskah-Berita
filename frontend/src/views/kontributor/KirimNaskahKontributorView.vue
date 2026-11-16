<template>
    <KontributorLayout>
        <div class="kirim-naskah-page">
            <header class="page-topbar">
                <div class="topbar-titles">
                    <h1 class="page-title">Kirim Naskah</h1>
                    <p class="page-subtitle">Isi formulir di bawah untuk mengirim naskah baru ke redaksi.</p>
                </div>
            </header>

            <form class="kirim-card" @submit.prevent="submitNaskah">
                <div v-if="errorMessage" class="alert-box alert-error">
                    {{ errorMessage }}
                </div>
                <div v-if="successMessage" class="alert-box alert-success">
                    {{ successMessage }}
                </div>

                <!-- Judul Naskah -->
                <div class="form-field">
                    <label class="field-label">
                        Judul naskah <span class="req">*</span>
                    </label>
                    <input
                        v-model="form.judul"
                        type="text"
                        class="text-input"
                        placeholder="Masukkan judul naskah yang jelas dan informatif"
                        required
                    />
                </div>

                <!-- Kategori (Multi-Select Radio Style Buttons) -->
                <div class="form-field">
                    <div class="label-row">
                        <label class="field-label">
                            Kategori <span class="req">*</span>
                        </label>
                        <span class="field-hint">
                            Pilih satu atau lebih kategori yang sesuai dengan isi naskah ({{ selectedKategoriIds.length }} dipilih)
                        </span>
                    </div>

                    <div class="kategori-radio-grid">
                        <button
                            v-for="kat in kategoriOptions"
                            :key="kat.id"
                            type="button"
                            class="radio-multi-btn"
                            :class="{ selected: isKategoriSelected(kat.id) }"
                            @click="toggleKategori(kat.id)"
                        >
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
                        <button
                            type="button"
                            class="btn-add-media"
                            @click="openMediaModal"
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                                <button
                                    type="button"
                                    class="heading-trigger"
                                    @click="showHeadingMenu = !showHeadingMenu"
                                >
                                    <span>{{ currentHeadingLabel }}</span>
                                    <span class="caret">&#9662;</span>
                                </button>
                                <div v-if="showHeadingMenu" class="heading-menu">
                                    <button
                                        v-for="h in headingOptions"
                                        :key="h.tag"
                                        type="button"
                                        class="heading-option"
                                        :class="[h.previewClass, { active: currentBlockTag === h.tag }]"
                                        @click="applyHeading(h)"
                                    >
                                        {{ h.label }}
                                    </button>
                                </div>
                            </div>

                            <span class="toolbar-divider"></span>

                            <!-- Bold & Italic -->
                            <button type="button" class="tool-btn" title="Bold (Ctrl+B)" @click="execCmd('bold')">
                                <strong>B</strong>
                            </button>
                            <button type="button" class="tool-btn" title="Italic (Ctrl+I)" @click="execCmd('italic')">
                                <em>I</em>
                            </button>

                            <!-- List tanpa numbering & List numbering -->
                            <button type="button" class="tool-btn" title="List tanpa numbering" @click="execCmd('insertUnorderedList')">
                                &#8226;&#8801;
                            </button>
                            <button type="button" class="tool-btn" title="List numbering" @click="execCmd('insertOrderedList')">
                                1&#8801;
                            </button>

                            <!-- Kutipan (Blockquote) -->
                            <button type="button" class="tool-btn" title="Kutipan" @click="toggleBlockquote">
                                &#10078;
                            </button>

                            <!-- Alignment -->
                            <button type="button" class="tool-btn" title="Rata Kiri" @click="execCmd('justifyLeft')">
                                &#8676;
                            </button>
                            <button type="button" class="tool-btn" title="Rata Tengah" @click="execCmd('justifyCenter')">
                                &#8596;
                            </button>
                            <button type="button" class="tool-btn" title="Rata Kanan" @click="execCmd('justifyRight')">
                                &#8677;
                            </button>

                            <!-- Sisipkan Link -->
                            <button type="button" class="tool-btn" title="Sisipkan Link" @click="promptInsertLink">
                                &#128279;
                            </button>

                            <span class="toolbar-divider"></span>

                            <!-- Coretan di tengah tulisan (Strikethrough) -->
                            <button type="button" class="tool-btn" title="Coretan di tengah tulisan" @click="execCmd('strikeThrough')">
                                <s>ABE</s>
                            </button>

                            <!-- Garis mendatar (Horizontal rule) -->
                            <button type="button" class="tool-btn" title="Garis mendatar" @click="execCmd('insertHorizontalRule')">
                                &#8213;
                            </button>

                            <!-- Text Color -->
                            <div class="color-picker-wrap" title="Warna Teks">
                                <label class="tool-btn color-btn">
                                    <span class="color-letter" :style="{ borderBottomColor: currentTextColor }">A</span>
                                    <input
                                        v-model="currentTextColor"
                                        type="color"
                                        class="hidden-color-input"
                                        @input="applyTextColor"
                                    />
                                </label>
                            </div>

                            <!-- Karakter Khusus -->
                            <div class="special-char-wrap">
                                <button
                                    type="button"
                                    class="tool-btn"
                                    title="Karakter khusus"
                                    @click="showSpecialChars = !showSpecialChars"
                                >
                                    &Omega;
                                </button>
                                <div v-if="showSpecialChars" class="special-char-popover">
                                    <button
                                        v-for="ch in specialCharacters"
                                        :key="ch"
                                        type="button"
                                        class="char-cell"
                                        @click="insertSpecialChar(ch)"
                                    >
                                        {{ ch }}
                                    </button>
                                </div>
                            </div>

                            <!-- Persempit & Perlebar Inden -->
                            <button type="button" class="tool-btn" title="Persempit inden (Outdent)" @click="execCmd('outdent')">
                                &#8672;
                            </button>
                            <button type="button" class="tool-btn" title="Perlebar inden (Indent)" @click="execCmd('indent')">
                                &#8674;
                            </button>

                            <!-- Undo & Redo -->
                            <button type="button" class="tool-btn" title="Undo (Ctrl+Z)" @click="execCmd('undo')">
                                &#8630;
                            </button>
                            <button type="button" class="tool-btn" title="Redo (Ctrl+Y)" @click="execCmd('redo')">
                                &#8631;
                            </button>
                        </div>

                        <!-- Canvas Wrapper: Menampung canvas dan overlay resize agar koordinat tidak tergeser toolbar -->
                        <div class="editor-canvas-wrapper" ref="canvasWrapperRef">
                            <div
                                ref="editorRef"
                                class="editor-canvas"
                                contenteditable="true"
                                data-placeholder="Tulis isi naskah lengkap di sini..."
                                @input="onEditorInput"
                                @click="handleEditorClick"
                            ></div>

                            <!-- Overlay: Resize Handles & Floating Toolbar untuk Gambar Terpilih -->
                            <div
                                v-if="selectedImage && imageOverlay.visible"
                                class="img-interactive-overlay"
                                :style="{
                                    top: imageOverlay.top + 'px',
                                    left: imageOverlay.left + 'px',
                                    width: imageOverlay.width + 'px',
                                    height: imageOverlay.height + 'px'
                                }"
                            >
                                <!-- 4 Handle Sudut -->
                                <div class="resize-handle handle-nw" @mousedown.prevent.stop="startImageResize($event, 'nw')"></div>
                                <div class="resize-handle handle-ne" @mousedown.prevent.stop="startImageResize($event, 'ne')"></div>
                                <div class="resize-handle handle-sw" @mousedown.prevent.stop="startImageResize($event, 'sw')"></div>
                                <div class="resize-handle handle-se" @mousedown.prevent.stop="startImageResize($event, 'se')"></div>

                                <!-- Floating Alignment Toolbar -->
                                <div class="img-float-toolbar" @mousedown.stop @click.stop>
                                    <button
                                        type="button"
                                        class="img-tb-btn"
                                        :class="{ active: currentImageAlignment === 'align-left' }"
                                        title="Rata Kiri (Float Left)"
                                        @click="setImageAlignment('align-left')"
                                    >
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="3" y1="6" x2="21" y2="6"></line>
                                            <line x1="3" y1="12" x2="11" y2="12"></line>
                                            <line x1="3" y1="18" x2="17" y2="18"></line>
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="img-tb-btn"
                                        :class="{ active: currentImageAlignment === 'align-center' }"
                                        title="Rata Tengah"
                                        @click="setImageAlignment('align-center')"
                                    >
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="3" y1="6" x2="21" y2="6"></line>
                                            <line x1="6" y1="12" x2="18" y2="12"></line>
                                            <line x1="4" y1="18" x2="20" y2="18"></line>
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="img-tb-btn"
                                        :class="{ active: currentImageAlignment === 'align-right' }"
                                        title="Rata Kanan (Float Right)"
                                        @click="setImageAlignment('align-right')"
                                    >
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="3" y1="6" x2="21" y2="6"></line>
                                            <line x1="13" y1="12" x2="21" y2="12"></line>
                                            <line x1="7" y1="18" x2="21" y2="18"></line>
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="img-tb-btn"
                                        :class="{ active: currentImageAlignment === 'align-full' }"
                                        title="Lebar Penuh (100%)"
                                        @click="setImageAlignment('align-full')"
                                    >
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="2" y1="6" x2="22" y2="6"></line>
                                            <line x1="2" y1="12" x2="22" y2="12"></line>
                                            <line x1="2" y1="18" x2="22" y2="18"></line>
                                        </svg>
                                    </button>
                                    <span class="img-tb-divider"></span>
                                    <button
                                        type="button"
                                        class="img-tb-btn btn-delete-img"
                                        title="Hapus Gambar"
                                        @click="deleteActiveImage"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="editor-statusbar">
                            <span>Jumlah kata: {{ wordCount }}</span>
                        </div>
                    </div>
                </div>

                <!-- Gambar Sampul (Berkas Naskah PDF/DOCX Dihapus) -->
                <div class="form-field">
                    <label class="field-label">Gambar sampul<span class="req">*</span></label>

                    <div
                        class="sampul-dropzone"
                        :class="{ 'has-preview': sampulPreviewUrl }"
                        @click="$refs.sampulInput.click()"
                    >
                        <template v-if="!sampulPreviewUrl">
                            <span class="sampul-title">Klik untuk unggah gambar sampul</span>
                            <span class="sampul-sub">PNG, JPG maksimal 2 MB</span>
                        </template>

                        <div v-else class="sampul-preview-container" @click.stop>
                            <img :src="sampulPreviewUrl" alt="Preview Gambar Sampul" class="sampul-preview-img" />
                            <div class="sampul-preview-actions">
                                <span class="sampul-filename">{{ sampulFileName }}</span>
                                <div class="sampul-btns">
                                    <button
                                        type="button"
                                        class="btn-sampul-action"
                                        @click="insertSampulToEditor"
                                    >
                                        Sisipkan juga ke isi naskah
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-sampul-action"
                                        @click="$refs.sampulInput.click()"
                                    >
                                        Ganti gambar
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-sampul-remove"
                                        @click="removeSampul"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>

                        <input
                            ref="sampulInput"
                            type="file"
                            accept="image/png,image/jpeg,image/webp"
                            hidden
                            @change="onSelectSampul"
                        />
                    </div>
                </div>

                <!-- Footer Tombol Aksi -->
                <div class="form-actions">
                    <button
                        type="button"
                        class="btn-draft"
                        :disabled="submitting"
                        @click="router.push('/kontributor/dashboard')"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="btn-submit"
                        :disabled="submitting"
                    >
                        {{ submitting ? 'Mengirim naskah...' : 'Kirim naskah' }}
                    </button>
                </div>
            </form>

            <!-- Popup Sisipkan Media ke Isi Naskah -->
            <MediaUploadModal
                :visible="showMediaModal"
                @close="showMediaModal = false"
                @insert="handleInsertMediaToEditor"
            />
        </div>
    </KontributorLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount } from 'vue'
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
    kata_kunci: '',
    isi: '',
})

const editorRef = ref(null)
const canvasWrapperRef = ref(null)
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
    if (editorRef.value) {
        editorRef.value.addEventListener('scroll', handleEditorScroll)
    }
    window.addEventListener('resize', handleEditorScroll)

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

onBeforeUnmount(() => {
    if (editorRef.value) {
        editorRef.value.removeEventListener('scroll', handleEditorScroll)
    }
    window.removeEventListener('resize', handleEditorScroll)
    window.removeEventListener('mousemove', onImageResizing)
    window.removeEventListener('mouseup', stopImageResize)
})

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
    // Default gambar ukuran kecil/kompak (width: 320px) agar rapi dan mudah diatur
    const html = `<figure class="inserted-media align-center"><img src="${media.url}" alt="${media.alt || ''}" title="${media.judul || ''}" style="width: 320px; max-width: 100%; height: auto; display: block; margin-left: auto; margin-right: auto;" />${captionHtml}</figure><p><br></p>`

    document.execCommand('insertHTML', false, html)
    onEditorInput()
}

// ==========================================
// KONTROL GAMBAR INTERAKTIF (RESIZE & ALIGN)
// ==========================================
const selectedImage = ref(null)
const imageOverlay = reactive({
    visible: false,
    top: 0,
    left: 0,
    width: 0,
    height: 0,
})
const currentImageAlignment = ref('align-center')

let isResizingImage = false
let resizeStart = { x: 0, y: 0, width: 0, height: 0, handle: '' }

function handleEditorClick(e) {
    closeMenus()
    const target = e.target
    if (target && target.tagName === 'IMG') {
        selectImage(target)
    } else {
        deselectImage()
    }
}

function selectImage(imgEl) {
    selectedImage.value = imgEl
    detectImageAlignment(imgEl)
    updateOverlayPosition()
}

function deselectImage() {
    selectedImage.value = null
    imageOverlay.visible = false
}

function detectImageAlignment(imgEl) {
    const parentFigure = imgEl.closest('figure')
    const target = parentFigure || imgEl

    if (target.classList.contains('align-left')) {
        currentImageAlignment.value = 'align-left'
    } else if (target.classList.contains('align-right')) {
        currentImageAlignment.value = 'align-right'
    } else if (target.classList.contains('align-full')) {
        currentImageAlignment.value = 'align-full'
    } else {
        currentImageAlignment.value = 'align-center'
    }
}

function updateOverlayPosition() {
    if (!selectedImage.value || !canvasWrapperRef.value) {
        imageOverlay.visible = false
        return
    }

    const wrapperBox = canvasWrapperRef.value.getBoundingClientRect()
    const imgBox = selectedImage.value.getBoundingClientRect()

    imageOverlay.top = imgBox.top - wrapperBox.top
    imageOverlay.left = imgBox.left - wrapperBox.left
    imageOverlay.width = imgBox.width
    imageOverlay.height = imgBox.height
    imageOverlay.visible = true
}

function setImageAlignment(alignment) {
    if (!selectedImage.value) return
    const img = selectedImage.value
    const parentFigure = img.closest('figure')
    const target = parentFigure || img

    // Hapus kelas align sebelumnya
    target.classList.remove('align-left', 'align-center', 'align-right', 'align-full')
    target.classList.add(alignment)
    currentImageAlignment.value = alignment

    const currentWidth = img.style.width || '320px'

    if (alignment === 'align-full') {
        img.style.width = '100%'
        img.style.height = 'auto'
        img.style.display = 'block'
        img.style.marginLeft = ''
        img.style.marginRight = ''
    } else if (alignment === 'align-center') {
        img.style.width = currentWidth
        img.style.height = 'auto'
        img.style.display = 'block'
        img.style.marginLeft = 'auto'
        img.style.marginRight = 'auto'
    } else {
        img.style.width = currentWidth
        img.style.height = 'auto'
        img.style.display = 'block'
        img.style.marginLeft = ''
        img.style.marginRight = ''
    }

    onEditorInput()
    setTimeout(() => {
        updateOverlayPosition()
    }, 30)
}

function deleteActiveImage() {
    if (!selectedImage.value) return
    const img = selectedImage.value
    const parentFigure = img.closest('figure')
    const toRemove = parentFigure || img
    toRemove.remove()
    deselectImage()
    onEditorInput()
}

function startImageResize(e, handle) {
    if (!selectedImage.value) return
    isResizingImage = true
    const img = selectedImage.value

    resizeStart = {
        x: e.clientX,
        y: e.clientY,
        width: img.offsetWidth,
        height: img.offsetHeight,
        aspectRatio: img.offsetWidth / (img.offsetHeight || 1),
        handle,
    }

    window.addEventListener('mousemove', onImageResizing)
    window.addEventListener('mouseup', stopImageResize)
}

function onImageResizing(e) {
    if (!isResizingImage || !selectedImage.value) return

    const dx = e.clientX - resizeStart.x
    const handle = resizeStart.handle
    let newWidth = resizeStart.width

    if (handle === 'se' || handle === 'ne') {
        newWidth = resizeStart.width + dx
    } else if (handle === 'sw' || handle === 'nw') {
        newWidth = resizeStart.width - dx
    }

    const maxEditorWidth = editorRef.value ? editorRef.value.clientWidth : 800
    newWidth = Math.max(100, Math.min(newWidth, maxEditorWidth))

    const img = selectedImage.value
    img.style.width = `${Math.round(newWidth)}px`
    img.style.height = 'auto'

    updateOverlayPosition()
}

function stopImageResize() {
    if (!isResizingImage) return
    isResizingImage = false
    window.removeEventListener('mousemove', onImageResizing)
    window.removeEventListener('mouseup', stopImageResize)
    onEditorInput()
    updateOverlayPosition()
}

function handleEditorScroll() {
    if (selectedImage.value) {
        updateOverlayPosition()
    }
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
        fd.append('kata_kunci', form.kata_kunci)
        fd.append('isi', form.isi)

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
.kirim-naskah-page {
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

.kirim-card {
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

.opt-p { font-size: 0.9rem; font-weight: 400; }
.opt-h1 { font-size: 1.55rem; font-weight: 800; }
.opt-h2 { font-size: 1.3rem; font-weight: 700; }
.opt-h3 { font-size: 1.12rem; font-weight: 700; }
.opt-h4 { font-size: 1rem; font-weight: 700; }
.opt-h5 { font-size: 0.88rem; font-weight: 700; }
.opt-h6 { font-size: 0.78rem; font-weight: 700; }
.opt-pre { font-family: monospace; font-size: 0.85rem; }

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

/* Canvas Wrapper: anchor point presisi untuk overlay resize handle */
.editor-canvas-wrapper {
    position: relative;
    width: 100%;
    overflow: visible;
}

.editor-canvas {
    display: flow-root; /* Block Formatting Context: float gambar tidak bocor keluar */
    min-height: 260px;
    padding: 1.15rem 1.25rem;
    font-size: 0.98rem;
    line-height: 1.7;
    color: var(--ktr-text-main);
    outline: none;
}

/* Clearfix eksplisit sebagai fallback */
.editor-canvas::after {
    content: "";
    display: table;
    clear: both;
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

/* Figure default: block agar konsisten, caption menempel di bawah gambar */
.editor-canvas :deep(figure) {
    margin: 1rem 0;
    display: block;
    max-width: 100%;
    clear: both;
}

/* Rata kiri: figure float kiri, teks lain mengalir di kanan */
.editor-canvas :deep(figure.align-left) {
    float: left;
    width: auto;
    margin: 0.5rem 1.25rem 1rem 0;
    clear: none;
}

/* Rata kanan: figure float kanan, teks lain mengalir di kiri */
.editor-canvas :deep(figure.align-right) {
    float: right;
    width: auto;
    margin: 0.5rem 0 1rem 1.25rem;
    clear: none;
}

/* Rata tengah (horizontal): figure block margin auto, gambar di dalam juga margin auto */
.editor-canvas :deep(figure.align-center) {
    display: block;
    width: fit-content;
    max-width: 100%;
    margin: 1.25rem auto;
    float: none;
    clear: both;
    text-align: center;
}

/* Lebar penuh: figure dan gambar 100% lebar kanvas */
.editor-canvas :deep(figure.align-full) {
    display: block;
    width: 100%;
    margin: 1.25rem 0;
    float: none;
    clear: both;
}

.editor-canvas :deep(figure.align-full img) {
    width: 100% !important;
    max-width: 100% !important;
    height: auto !important;
}

.editor-canvas :deep(img) {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    display: block;
    cursor: pointer;
    transition: outline 0.15s ease;
}

.editor-canvas :deep(img:hover) {
    outline: 2px dashed rgba(15, 107, 99, 0.4);
    outline-offset: 2px;
}

.editor-canvas :deep(figcaption) {
    font-size: 0.84rem;
    color: var(--ktr-text-muted);
    margin-top: 0.35rem;
    text-align: center;
}

/* ======================================================== */
/* Interactive Image Overlay: 4 Resize Handles & Floating Toolbar */
/* ======================================================== */
.img-interactive-overlay {
    position: absolute;
    box-sizing: border-box;
    border: 2px solid var(--ktr-teal);
    pointer-events: none;
    z-index: 50;
    border-radius: 4px;
}

.resize-handle {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #FFFFFF;
    border: 2px solid var(--ktr-teal);
    border-radius: 2px;
    pointer-events: auto;
    box-shadow: 0 1px 3px rgba(0,0,0,0.25);
    z-index: 52;
}

.handle-nw {
    top: -5px;
    left: -5px;
    cursor: nwse-resize;
}

.handle-ne {
    top: -5px;
    right: -5px;
    cursor: nesw-resize;
}

.handle-sw {
    bottom: -5px;
    left: -5px;
    cursor: nesw-resize;
}

.handle-se {
    bottom: -5px;
    right: -5px;
    cursor: nwse-resize;
}

/* Floating Toolbar */
.img-float-toolbar {
    position: absolute;
    top: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    background: #FFFFFF;
    border: 1px solid var(--ktr-border-dark);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    border-radius: 8px;
    padding: 4px 6px;
    display: flex;
    align-items: center;
    gap: 4px;
    pointer-events: auto;
    z-index: 55;
    white-space: nowrap;
}

.img-float-toolbar::before {
    content: "";
    position: absolute;
    top: -6px;
    left: 50%;
    transform: translateX(-50%);
    border-width: 0 6px 6px 6px;
    border-style: solid;
    border-color: transparent transparent #FFFFFF transparent;
}

.img-tb-btn {
    background: transparent;
    border: none;
    color: var(--ktr-text-main);
    width: 30px;
    height: 30px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.15s, color 0.15s;
}

.img-tb-btn:hover {
    background: #F1F5F9;
    color: var(--ktr-teal);
}

.img-tb-btn.active {
    background: #E6F5F2;
    color: var(--ktr-teal);
    font-weight: 700;
}

.img-tb-divider {
    width: 1px;
    height: 18px;
    background: var(--ktr-border);
    margin: 0 2px;
}

.btn-delete-img {
    color: #DC2626;
}

.btn-delete-img:hover {
    background: #FEE2E2;
    color: #991B1B;
}

.editor-statusbar {
    clear: both;
    width: 100%;
    display: flex;
    align-items: center;
    position: relative;
    z-index: 10;
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
</style>