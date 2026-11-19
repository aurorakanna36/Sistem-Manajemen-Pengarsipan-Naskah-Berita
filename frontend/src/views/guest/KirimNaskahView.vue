<template>
    <div class="guest-page">
        <nav class="navbar">
            <div class="navbar-brand">
                <div class="nav-logo">HC</div>
                <span>Harian Cakrawala</span>
            </div>
            <div class="navbar-links">
                <router-link to="/" class="btn-outline">Beranda</router-link>
                <router-link to="/login" class="nav-btn">Masuk</router-link>
            </div>
        </nav>

        <div class="page-hero">
            <div class="hero-content">
                <h1>Kirim Naskah Berita</h1>
                <p>Kamu masuk sebagai guest. Hasil review naskah akan dikirim ke email yang terdaftar.</p>
            </div>
        </div>

        <div v-if="!submitSuccess" class="card-form">
            <h2>Draf tidak bisa disimpan sebagai Guest</h2>
            <p>
                Daftar sebagai kontributor untuk menyimpan draf,
                memantau status naskah, dan menerima notifikasi sistem.
            </p>
        </div>

        <div class="form-container">
            <div v-if="submitSuccess" class="success-card">
                <div class="success-icon">
                    <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                </div>

                <h2>Naskah berhasil dikirim</h2>

                <p class="success-description">
                    Terima kasih. Hasil review akan dikirim ke
                    <strong>{{ form.email_pengirim || 'email pengirim' }}</strong>.
                </p>

                <div class="success-details">
                    <div class="detail-row">
                        <span>Judul</span>
                        <strong>{{ form.judul }}</strong>
                    </div>

                    <div class="detail-row">
                        <span>Kategori</span>
                        <strong>
                            {{kategoriOptions
                                .filter(kat => selectedKategoriIds.includes(kat.id))
                                .map(kat => kat.nama_kategori)
                                .join(', ') || '-'}}
                        </strong>
                    </div>

                    <div class="detail-row">
                        <span>Dikirim pada</span>
                        <strong>{{ tanggalKirim }}</strong>
                    </div>

                    <div class="detail-row">
                        <span>Status</span>
                        <strong class="status-review">Dalam review</strong>
                    </div>
                </div>

                <div class="success-actions">
                    <button class="btn-send-again" @click="resetForm">
                        Kirim naskah lain
                    </button>

                    <router-link to="/daftar-kontributor" class="btn-register">
                        Daftar jadi kontributor
                    </router-link>
                </div>

                <p class="success-note">
                    Sebagai guest, kamu tidak bisa memantau status naskah di sistem.
                    Daftar sebagai kontributor untuk memantau status dan menyimpan draf.
                </p>
            </div>

            <form v-else class="naskah-form" @submit.prevent="handleSubmit">
                <div v-if="error" class="alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    {{ error }}
                </div>

                <div class="form-section">
                    <h3 class="section-title">Data Pengirim</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="g-nama">Nama Lengkap <span class="required">*</span></label>
                            <input id="g-nama" v-model="form.nama_pengirim" type="text" placeholder="Nama Anda"
                                required />
                        </div>
                        <div class="form-group">
                            <label for="g-email">Email <span class="required">*</span></label>
                            <input id="g-email" v-model="form.email_pengirim" type="email"
                                placeholder="email@contoh.com" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="g-phone">Nomor Telepon / WhatsApp <span class="required">*</span></label>
                            <input id="g-phone" v-model="form.phone_pengirim" type="text" placeholder="08xxxxxxxxxx" />
                        </div>
                        <div class="form-group">
                            <label for="g-sosmed">Sosial Media <span class="required">*</span></label>
                            <input id="g-sosmed" v-model="form.sosmed_pengirim" type="text" placeholder="Nama sosmed" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="g-instansi">Instansi <span class="required">*</span></label>
                        <input id="g-instansi" v-model="form.instansi_pengirim" type="text"
                            placeholder="Nama instansi" />
                    </div>

                </div>

                <div class="form-section">
                    <h3 class="section-title">Data Naskah</h3>
                    <div class="form-group">
                        <label for="g-judul">Judul Naskah <span class="required">*</span></label>
                        <input id="g-judul" v-model="form.judul" type="text"
                            placeholder="Tuliskan judul berita yang menarik" required />
                    </div>
                    <div class="form-group">
                        <div class="label-row">
                            <label class="field-label">Kategori <span class="required">*</span></label>
                        </div>

                        <div class="kategori-radio-grid">
                            <button v-for="kat in kategoriOptions" :key="kat.id" type="button" class="radio-multi-btn"
                                :class="{ selected: isKategoriSelected(kat.id) }" @click="toggleKategori(kat.id)">
                                <span class="radio-circle">
                                    <span v-if="isKategoriSelected(kat.id)" class="radio-dot"></span>
                                </span>
                                <span class="radio-label">{{ kat.nama_kategori }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="editor-heading-row">
                            <label class="field-label">
                                Isi Naskah <span class="required">*</span>
                            </label>

                            <button type="button" class="btn-add-media" @click="openMediaModal">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <polyline points="21 15 16 10 5 21" />
                                </svg>
                                Tambahkan Media
                            </button>
                        </div>

                        <div class="rich-editor-shell">

                            <!-- TOOLBAR -->
                            <div class="editor-toolbar">

                                <div class="heading-dropdown-wrap">
                                    <button type="button" class="heading-trigger"
                                        @click="showHeadingMenu = !showHeadingMenu">
                                        <span>{{ currentHeadingLabel }}</span>
                                        <span>&#9662;</span>
                                    </button>

                                    <div v-if="showHeadingMenu" class="heading-menu">
                                        <button v-for="h in headingOptions" :key="h.tag" type="button"
                                            class="heading-option" :class="h.previewClass" @click="applyHeading(h)">
                                            {{ h.label }}
                                        </button>
                                    </div>
                                </div>

                                <span class="toolbar-divider"></span>

                                <button type="button" class="tool-btn" title="Bold" @click="execCmd('bold')">
                                    <strong>B</strong>
                                </button>

                                <button type="button" class="tool-btn" title="Italic" @click="execCmd('italic')">
                                    <em>I</em>
                                </button>

                                <button type="button" class="tool-btn" title="List tanpa numbering"
                                    @click="execCmd('insertUnorderedList')">
                                    &#8226;&#8801;
                                </button>

                                <button type="button" class="tool-btn" title="List numbering"
                                    @click="execCmd('insertOrderedList')">
                                    1&#8801;
                                </button>

                                <button type="button" class="tool-btn" title="Kutipan" @click="toggleBlockquote">
                                    &#10078;
                                </button>

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

                                <button type="button" class="tool-btn" title="Sisipkan Link" @click="promptInsertLink">
                                    &#128279;
                                </button>

                                <span class="toolbar-divider"></span>

                                <button type="button" class="tool-btn" title="Coretan"
                                    @click="execCmd('strikeThrough')">
                                    <s>ABE</s>
                                </button>

                                <button type="button" class="tool-btn" title="Garis mendatar"
                                    @click="execCmd('insertHorizontalRule')">
                                    &#8213;
                                </button>

                                <label class="tool-btn color-btn" title="Warna teks">
                                    <span class="color-letter" :style="{ borderBottomColor: currentTextColor }">
                                        A
                                    </span>
                                    <input v-model="currentTextColor" type="color" class="hidden-color-input"
                                        @input="applyTextColor" />
                                </label>

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

                                <button type="button" class="tool-btn" title="Persempit inden"
                                    @click="execCmd('outdent')">
                                    &#8672;
                                </button>

                                <button type="button" class="tool-btn" title="Perlebar inden"
                                    @click="execCmd('indent')">
                                    &#8674;
                                </button>

                                <button type="button" class="tool-btn" title="Undo" @click="execCmd('undo')">
                                    &#8630;
                                </button>

                                <button type="button" class="tool-btn" title="Redo" @click="execCmd('redo')">
                                    &#8631;
                                </button>
                            </div>

                            <!-- AREA MENULIS -->
                            <div class="editor-canvas-wrapper" ref="canvasWrapperRef">
                                <div ref="editorRef" class="editor-canvas" contenteditable="true"
                                    data-placeholder="Tulis atau tempel isi naskah di sini" @input="onEditorInput"
                                    @click="handleEditorClick"></div>

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

                                <!-- Floating Link Popover -->
                                <div
                                    v-if="linkPopover.visible"
                                    class="floating-link-popover"
                                    :class="{ 'is-below': linkPopover.isBelow }"
                                    :style="{ top: linkPopover.top + 'px', left: linkPopover.left + 'px' }"
                                    @mousedown.stop
                                    @click.stop
                                >
                                    <div class="popover-caret" :style="{ left: linkPopover.arrowLeft + 'px' }"></div>

                                    <!-- Mode Insert / Edit -->
                                    <div v-if="linkPopover.mode === 'insert' || linkPopover.mode === 'edit'" class="popover-edit-body">
                                        <span class="popover-title">Tempelkan URL atau ketik untuk mencari</span>
                                        <div class="popover-input-row">
                                            <input
                                                ref="linkInputRef"
                                                v-model="linkPopover.url"
                                                type="text"
                                                placeholder="https://..."
                                                class="link-url-input"
                                                @keydown.enter.prevent="applyLink"
                                                @keydown.esc="closeLinkPopover"
                                            />
                                            <button type="button" class="btn-link-submit" title="Terapkan link (Enter)" @click="applyLink">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                    <polyline points="9 10 4 15 9 20"></polyline>
                                                    <path d="M20 4v7a4 4 0 0 1-4 4H4"></path>
                                                </svg>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn-link-gear"
                                                :class="{ active: linkPopover.openInNewTab }"
                                                title="Toggle buka di tab baru"
                                                @click="linkPopover.openInNewTab = !linkPopover.openInNewTab"
                                            >
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div v-if="linkPopover.openInNewTab" class="tab-notice">&#10003; Akan terbuka di tab baru</div>
                                    </div>

                                    <!-- Mode Preview (saat link diklik) -->
                                    <div v-else class="popover-preview-body">
                                        <div class="preview-url-row">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                            <span class="preview-url-text" :title="linkPopover.url">{{ linkPopover.url }}</span>
                                        </div>
                                        <div class="preview-actions">
                                            <button type="button" class="btn-action-sm btn-open" @click="openActiveLink">Buka &#8599;</button>
                                            <button type="button" class="btn-action-sm btn-edit" @click="switchToEditMode">Ubah</button>
                                            <button type="button" class="btn-action-sm btn-unlink" @click="unlinkActiveLink">Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="editor-statusbar">
                                Jumlah kata: {{ wordCount }}
                            </div>

                        </div>
                    </div>

                    <div class="form-group">
                        <label for="g-file">Gambar Sampul<span class="required">*</span></label>

                        <div class="file-upload-zone" :class="{ 'has-file': selectedFilePreview }"
                            @click="$refs.coverInput.click()">

                            <template v-if="!selectedFilePreview">
                                <span class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </span>

                                <span>
                                    <strong>Klik untuk upload</strong> atau seret file ke sini
                                    <br />
                                    <small>JPG atau PNG, rasio 16:9</small>
                                </span>
                            </template>

                            <template v-else>
                                <img :src="selectedFilePreview" alt="Pratinjau gambar sampul" class="cover-preview" />

                                <p class="file-name">{{ selectedFileName }}</p>

                                <div class="cover-preview-actions">
                                    <button type="button" class="btn-sampul-insert" @click.stop="insertSampulToEditor">
                                        Sisipkan juga ke isi naskah
                                    </button>

                                    <button type="button" class="btn-remove-cover" @click.stop="removeCover">
                                        Hapus gambar
                                    </button>
                                </div>
                            </template>

                            <input ref="coverInput" id="g-file" type="file" accept="image/png,image/jpeg,image/webp"
                                hidden @change="onSelectCover" />
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <label class="disclaimer-checkbox">
                        <input type="checkbox" v-model="form.persetujuan" required>
                        <span class="disclaimer">
                            Dengan mengirimkan naskah ini, Anda menyatakan bahwa
                            informasi yang diberikan adalah benar dan bukan merupakan
                            plagiarisme. Redaksi berhak mengedit dan menerbitkan naskah
                            sesuai kebijakan editorial.
                        </span>
                    </label>

                    <button type="submit" class="btn-submit" :disabled="loading || !form.persetujuan">
                        <span v-if="loading" class="spinner"></span>
                        <span v-else>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13" />
                                <polygon points="22 2 15 22 11 13 2 9 22 2" />
                            </svg>
                            Kirim Naskah
                        </span>
                    </button>
                </div>
            </form>
        </div>
        <MediaUploadGuest :visible="showMediaModal" @close="showMediaModal = false" @insert="handleMediaInsert" />

    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import api from '@/api/index.js'
import MediaUploadGuest from './MediaUploadGuest.vue'

const form = ref({
    nama_pengirim: '',
    email_pengirim: '',
    phone_pengirim: '',
    sosmed_pengirim: '',
    instansi_pengirim: '',
    judul: '',
    isi: '',
    persetujuan: false,
})

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

const selectedFile = ref(null)
const selectedFileName = ref('')
const selectedFilePreview = ref('')
const showMediaModal = ref(false)

const editorRef = ref(null)
const canvasWrapperRef = ref(null)
const linkInputRef = ref(null)
const showHeadingMenu = ref(false)
const showSpecialChars = ref(false)

const currentBlockTag = ref('P')
const currentHeadingLabel = ref('Paragraf')
const currentTextColor = ref('#12263A')

// ==========================================
// FLOATING LINK POPOVER STATE
// ==========================================
const linkPopover = reactive({
    visible: false,
    mode: 'insert', // 'insert' | 'edit' | 'preview'
    url: '',
    openInNewTab: true,
    top: 0,
    left: 0,
    arrowLeft: 180,
    isBelow: false,
    activeLinkEl: null,
})
let savedLinkRange = null

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

// Menghitung jumlah kata
const wordCount = computed(() => {
    const plain = (form.value.isi || '')
        .replace(/<[^>]*>/g, ' ')
        .replace(/&nbsp;/g, ' ')
        .trim()

    if (!plain) return 0

    return plain.split(/\s+/).length
})


const coverInput = ref(null)

function onSelectCover(event) {
    const file = event.target.files?.[0]

    if (!file) return

    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        alert('Format gambar harus JPG, PNG, atau WEBP.')
        event.target.value = ''
        return
    }

    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran gambar maksimal 2 MB.')
        event.target.value = ''
        return
    }

    if (selectedFilePreview.value && selectedFilePreview.value.startsWith('blob:')) {
        URL.revokeObjectURL(selectedFilePreview.value)
    }

    selectedFile.value = file
    selectedFileName.value = file.name

    const reader = new FileReader()
    reader.onload = (e) => {
        selectedFilePreview.value = e.target?.result || ''
    }
    reader.readAsDataURL(file)

    coverMeta.value = {
        alt: form.value.judul || '',
        judul: form.value.judul || '',
        keterangan: '',
        deskripsi: '',
    }
}

function removeCover() {
    if (selectedFilePreview.value && selectedFilePreview.value.startsWith('blob:')) {
        URL.revokeObjectURL(selectedFilePreview.value)
    }

    selectedFile.value = null
    selectedFileName.value = ''
    selectedFilePreview.value = ''

    if (coverInput.value) {
        coverInput.value.value = ''
    }
}


// Sinkronisasi isi editor dengan form
function onEditorInput() {
    if (editorRef.value) {
        form.value.isi = editorRef.value.innerHTML
    }
}

function closeMenus() {
    showHeadingMenu.value = false
    showSpecialChars.value = false
    closeLinkPopover()
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

    document.execCommand(
        'formatBlock',
        false,
        `<${option.tag}>`
    )

    onEditorInput()
}

function toggleBlockquote() {
    editorRef.value?.focus()

    document.execCommand(
        'formatBlock',
        false,
        '<BLOCKQUOTE>'
    )

    onEditorInput()
}

function promptInsertLink() {
    const sel = window.getSelection()
    if (!sel || sel.rangeCount === 0 || sel.isCollapsed) return

    savedLinkRange = sel.getRangeAt(0).cloneRange()

    // Periksa jika seleksi sudah ada di dalam link
    const range = savedLinkRange
    const container = range.commonAncestorContainer
    const existingLink = (container.nodeType === 3 ? container.parentElement : container).closest('a')

    if (existingLink) {
        linkPopover.url = existingLink.getAttribute('href') || existingLink.href || ''
        linkPopover.activeLinkEl = existingLink
        linkPopover.mode = 'edit'
    } else {
        linkPopover.url = ''
        linkPopover.activeLinkEl = null
        linkPopover.mode = 'insert'
    }

    // Hitung posisi dari rect seleksi
    const rect = range.getBoundingClientRect()
    updateLinkPopoverPosition(rect)

    linkPopover.visible = true
    nextTick(() => {
        linkInputRef.value?.focus()
    })
}

function updateLinkPopoverPosition(rect) {
    if (!canvasWrapperRef.value) return
    const wrapperRect = canvasWrapperRef.value.getBoundingClientRect()
    const popoverWidth = 360
    const popoverHeight = 95

    let top = rect.top - wrapperRect.top - popoverHeight - 12
    let left = rect.left - wrapperRect.left + (rect.width / 2) - (popoverWidth / 2)
    let isBelow = false

    if (top < 8) {
        top = rect.bottom - wrapperRect.top + 12
        isBelow = true
    }

    const maxLeft = wrapperRect.width - popoverWidth - 8
    left = Math.max(8, Math.min(left, maxLeft))

    const rawArrow = (rect.left - wrapperRect.left + (rect.width / 2)) - left
    linkPopover.top = top
    linkPopover.left = left
    linkPopover.arrowLeft = Math.max(16, Math.min(rawArrow, popoverWidth - 28))
    linkPopover.isBelow = isBelow
}

function applyLink() {
    let url = linkPopover.url.trim()
    if (!url) return

    if (!/^https?:\/\//i.test(url) && !url.startsWith('mailto:')) {
        url = 'https://' + url
    }

    const sel = window.getSelection()
    if (savedLinkRange) {
        sel.removeAllRanges()
        sel.addRange(savedLinkRange)
    }

    document.execCommand('createLink', false, url)

    // Set target="_blank" dan rel pada link yang baru dibuat
    const newLink = sel.anchorNode?.parentElement?.closest('a')
    if (newLink) {
        newLink.setAttribute('href', url)
        if (linkPopover.openInNewTab) {
            newLink.setAttribute('target', '_blank')
            newLink.setAttribute('rel', 'noopener noreferrer')
        }
    }

    // Juga update activeLinkEl jika mode edit
    if (linkPopover.activeLinkEl) {
        linkPopover.activeLinkEl.setAttribute('href', url)
        if (linkPopover.openInNewTab) {
            linkPopover.activeLinkEl.setAttribute('target', '_blank')
            linkPopover.activeLinkEl.setAttribute('rel', 'noopener noreferrer')
        }
    }

    onEditorInput()
    closeLinkPopover()
}

function closeLinkPopover() {
    linkPopover.visible = false
    linkPopover.activeLinkEl = null
    savedLinkRange = null
}

function openActiveLink() {
    if (linkPopover.url) {
        window.open(linkPopover.url, '_blank', 'noopener,noreferrer')
    }
}

function switchToEditMode() {
    linkPopover.mode = 'edit'
    nextTick(() => {
        linkInputRef.value?.focus()
    })
}

function unlinkActiveLink() {
    if (linkPopover.activeLinkEl) {
        const sel = window.getSelection()
        const range = document.createRange()
        range.selectNodeContents(linkPopover.activeLinkEl)
        sel.removeAllRanges()
        sel.addRange(range)
    }
    document.execCommand('unlink', false, null)
    onEditorInput()
    closeLinkPopover()
}

function applyTextColor(event) {
    currentTextColor.value = event.target.value

    execCmd('foreColor', currentTextColor.value)
}

function insertSpecialChar(ch) {
    showSpecialChars.value = false

    execCmd('insertText', ch)
}

function openMediaModal() {
    closeMenus()

    showMediaModal.value = true
}


const coverMeta = ref({
    alt: '',
    judul: '',
    keterangan: '',
    deskripsi: '',
})

const loading = ref(false)
const error = ref('')
const submitSuccess = ref(false)
const tanggalKirim = ref('')


async function handleSubmit() {
    onEditorInput()
    if (!form.value.isi && editorRef.value) {
        form.value.isi = editorRef.value.innerHTML
    }

    if (selectedKategoriIds.value.length === 0) {
        error.value = 'Silakan pilih minimal satu kategori naskah.'
        return
    }
    loading.value = true
    error.value = ''

    try {
        const payload = new FormData()

        payload.append('nama', form.value.nama_pengirim)
        payload.append('nama_pengirim', form.value.nama_pengirim)

        payload.append('email', form.value.email_pengirim)
        payload.append('email_pengirim', form.value.email_pengirim)

        payload.append('sosmed', form.value.sosmed_pengirim)
        payload.append('sosmed_pengirim', form.value.sosmed_pengirim)

        payload.append('instansi', form.value.instansi_pengirim)
        payload.append('instansi_pengirim', form.value.instansi_pengirim)

        payload.append('nomot_Whatsapp', form.value.phone_pengirim)
        payload.append('phone_pengirim', form.value.phone_pengirim)

        payload.append('judul', form.value.judul)
        payload.append('isi', form.value.isi)

        // Persetujuan
        payload.append('persetujuan', form.value.persetujuan ? 1 : 0)

        // Kategori
        selectedKategoriIds.value.forEach(id => {
            payload.append('kategori_ids[]', id)
        })

        // File gambar
        if (selectedFile.value) {
            payload.append('gambar_sampul', selectedFile.value)
        }

        // Metadata gambar
        payload.append('alt', coverMeta.value.alt)
        payload.append('judul_gambar', coverMeta.value.judul)
        payload.append('keterangan_gambar', coverMeta.value.keterangan)
        payload.append('deskripsi_gambar', coverMeta.value.deskripsi)

        await api.guest.kirimNaskah(payload)

        tanggalKirim.value = new Date().toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        })

        submitSuccess.value = true

    } catch (err) {
        console.error('Error kirim naskah:', err.response?.data || err)

        error.value =
            err.response?.data?.message ||
            'Gagal mengirim naskah. Silakan coba lagi.'

    } finally {
        loading.value = false
    }
}


function isKategoriSelected(id) {
    return selectedKategoriIds.value.includes(id)
}

function toggleKategori(id) {
    if (isKategoriSelected(id)) {
        selectedKategoriIds.value = selectedKategoriIds.value.filter(
            kategoriId => kategoriId !== id
        )
    } else {
        selectedKategoriIds.value.push(id)
    }
}

function resetForm() {
    if (selectedFilePreview.value) {
        URL.revokeObjectURL(selectedFilePreview.value)
    }

    form.value = {
        nama_pengirim: '',
        email_pengirim: '',
        phone_pengirim: '',
        sosmed_pengirim: '',
        instansi_pengirim: '',
        judul: '',
        isi: '',
        persetujuan: false,
    }

    selectedKategoriIds.value = []

    selectedFile.value = null
    selectedFileName.value = ''
    selectedFilePreview.value = ''

    coverMeta.value = {
        alt: '',
        judul: '',
        keterangan: '',
        deskripsi: '',
    }

    showMediaModal.value = false
    submitSuccess.value = false
    error.value = ''
}


function handleMediaInsert(media) {
    if (!media?.url) return

    showMediaModal.value = false
    editorRef.value?.focus()

    const captionHtml = media.keterangan
        ? `<figcaption>${media.keterangan}</figcaption>`
        : ''

    const html = `<figure class="inserted-media align-center"><img src="${media.url}" alt="${media.alt || ''}" title="${media.judul || ''}" style="width: 320px; max-width: 100%; height: auto; display: block; margin-left: auto; margin-right: auto;" />${captionHtml}</figure><p><br></p>`

    document.execCommand('insertHTML', false, html)
    onEditorInput()
}

function insertSampulToEditor() {
    if (!selectedFilePreview.value) return

    handleMediaInsert({
        url: selectedFilePreview.value,
        alt: form.value.judul || selectedFileName.value,
        judul: form.value.judul || selectedFileName.value,
        keterangan: form.value.judul || '',
    })
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
    // Deteksi klik pada elemen <a> di dalam editor
    const linkEl = e.target.closest('a')
    if (linkEl && editorRef.value?.contains(linkEl)) {
        e.preventDefault()
        linkPopover.url = linkEl.getAttribute('href') || linkEl.href || ''
        linkPopover.activeLinkEl = linkEl
        linkPopover.mode = 'preview'
        const rect = linkEl.getBoundingClientRect()
        updateLinkPopoverPosition(rect)
        linkPopover.visible = true
        return
    }

    // Tutup link popover jika klik di luar area popover
    if (linkPopover.visible) {
        closeLinkPopover()
    }

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

onMounted(() => {
    if (editorRef.value) {
        editorRef.value.addEventListener('scroll', handleEditorScroll)
    }
    window.addEventListener('resize', handleEditorScroll)
})

onBeforeUnmount(() => {
    if (editorRef.value) {
        editorRef.value.removeEventListener('scroll', handleEditorScroll)
    }
    window.removeEventListener('resize', handleEditorScroll)
    window.removeEventListener('mousemove', onImageResizing)
    window.removeEventListener('mouseup', stopImageResize)
})

</script>

<style scoped>
.guest-page {
    min-height: 100vh;
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
    color: #0f2a1e;
}

.nav-logo {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #0f8168, #1e5f51);
    border-radius: 8px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 700;
}

.navbar-links {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.nav-link {
    color: #374151;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.2s;
}

.nav-link:hover {
    color: #1e5f51;
}

.nav-btn {
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, #0f8168, #1e5f51);
    color: white;
    border-radius: 7px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: opacity 0.2s;
}

.nav-btn:hover {
    background: #1e5f51;
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

.btn-outline:hover {
    border-color: #94b8a7;
}

.page-hero {
    background: linear-gradient(135deg, #0f2a22 0%, #1e5f51 100%);
    padding: 4rem 2rem;
    text-align: center;
    color: #fff;
}

.hero-content {
    max-width: 640px;
    margin: 0 auto;
}

.page-hero h1 {
    font-size: 2.25rem;
    font-weight: 800;
    margin-bottom: 1rem;
    letter-spacing: -0.02em;
}

.page-hero p {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.7;
}

.card-form {
    width: 100%;
    max-width: 650px;
    margin: 24px auto 0px;
    padding: 12px 16px;
    background: #dbfefc;
    border: 1px solid #a4dccf;
    border-radius: 8px;
    height: auto;
    box-sizing: border-box;
}

.card-form h2 {
    font-size: 13px;
    font-weight: 700;
    color: #1e5f51;
    margin: 0 0 4px;
}

.card-form p {
    font-size: 11px;
    color: #475569;
    line-height: 1.5;
    margin: 0;
}

.form-container {
    width: 100%;
    max-width: 700px;
    margin: 0 auto;
    padding: 2rem 1.5rem;
    box-sizing: border-box;
}

.success-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 40px 42px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    max-width: 650px;
    margin: 0 auto;
}

.success-icon {
    width: 72px;
    height: 72px;
    background: #dcfce7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    color: #16a34a;
}

.success-card h2 {
    font-size: 26px;
    font-weight: 800;
    color: #172b43;
    margin-bottom: 16px;
}

.success-description {
    color: #475569;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 20px;
}

.success-description strong {
    color: #475569;
    font-weight: 500;
}

.success-details {
    background: #f1f5f9;
    border-radius: 10px;
    padding: 10px 20px;
    margin-bottom: 20px;
}

.success-card p {
    color: #64748b;
    line-height: 1.7;
    margin-bottom: 2rem;
}

.detail-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 13px 0;
    border-bottom: 1px solid #d5dee8;
    font-size: 14px;
    text-align: left;
}

.detail-row:last-child {
    border-bottom: 1px solid #d5dee8;
}

.detail-row span {
    color: #475569;
}

.detail-row strong {
    color: #172b43;
    font-weight: 700;
    text-align: right;
    overflow-wrap: anywhere;
}

.status-review {
    background: #dbeafe;
    color: #1d4ed8 !important;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
}

/* TOMBOL */
.success-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin: 20px 0;
    flex-wrap: wrap;
}

.btn-send-again,
.btn-register {
    padding: 13px 22px;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    transition: 0.2s;
}

.btn-send-again {
    background: white;
    color: #172b43;
    border: 1px solid #172b43;
}

.btn-send-again:hover {
    background: #f1f5f9;
}

.btn-register {
    background: #0f766e;
    color: white;
    border: 1px solid #0f766e;
}

.btn-register:hover {
    background: #115e59;
}

/* CATATAN GUEST */
.success-note {
    font-size: 12px;
    line-height: 1.6;
    color: #475569;
    margin: 18px 0 0;
}

.naskah-form {
    width: 100%;
    box-sizing: border-box;
    background: #fff;
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
    border: 1px solid #e2e8f0;
}

.form-header {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #f1f5f9;
}

.form-header h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e5f51;
    margin-bottom: 0.4rem;
}

.form-header p {
    color: #64748b;
    font-size: 0.9rem;
}

.form-section {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #1e5f51;
    margin-bottom: 1.25rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e0edfa;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.required {
    color: #ef4444;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.9rem;
    color: #1e5f51;
    background: #fafbfc;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: inherit;
    resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1e5f51;
    box-shadow: 0 0 0 3px hsla(167, 52%, 25%, 0.102);
    background: #fff;
}

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

.file-upload-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s, background 0.2s;
    color: #64748b;
    font-size: 0.875rem;
    line-height: 1.6;
}

.file-upload-zone:hover,
.file-upload-zone.has-file {
    border-color: #1e5f51;
    background: #f0f7ff;
    color: #1e5f51;
}

.file-upload-zone svg {
    display: block;
    margin: 0 auto 0.75rem;
    opacity: 0.6;
}

.cover-preview {
    width: 100%;
    height: 220px;
    object-fit: cover;
    border-radius: 10px;
    display: block;
}

.file-upload-zone.has-file {
    padding: 10px;
    overflow: hidden;
}

.file-name {
    font-weight: 600;
    font-size: 0.875rem;
}

.alert-error {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    border-radius: 10px;
    padding: 0.875rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
}

.form-footer {
    padding-top: 1.5rem;
    border-top: 1px solid #f1f5f9;
    margin-top: 25px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.disclaimer-checkbox {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
}

.disclaimer-checkbox input {
    width: 17px;
    height: 17px;
    margin-top: 3px;
    flex-shrink: 0;
    accent-color: #0d5c4d;
    cursor: pointer;
}

.disclaimer {
    font-size: 13px;
    line-height: 1.6;
    color: #64748b;
    margin: 0;
}

.btn-submit {
    width: 100%;
    padding: 0.9rem;
    background: #1e5f51;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: background 0.2s, transform 0.1s;
}

.btn-submit:hover:not(:disabled) {
    background: #1e5f51;
    transform: translateY(-1px);
}

.btn-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    display: inline-block;
    padding: 0.75rem 2rem;
    background: #1e5f51;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 0.75rem;
    margin-right: 0.75rem;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.2s;
}

.btn-primary:hover {
    background: #cbe6e0;
}

.btn-secondary {
    display: inline-block;
    padding: 0.75rem 2rem;
    background: #f1f5f9;
    color: #475569;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background 0.2s;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

.spinner {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

.editor-heading-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 10px;
}

.editor-heading-row .field-label {
    margin-bottom: 0;
}

.btn-add-media {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 18px;
    border: 1px solid #0f766e;
    border-radius: 9px;
    background: #f0fdfa;
    color: #0f766e;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.rich-editor-shell {
    border: 1px solid #dbe3ee;
    border-radius: 12px;
    background: #ffffff;
    overflow: visible;
}

.editor-toolbar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.35rem;
    padding: 0.65rem 0.75rem;
    background: #f4f7fa;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 12px 12px 0 0;
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
    background: white;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    gap: 0.6rem;
}

.heading-menu {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    width: 210px;
    background: white;
    border: 1px solid #cbd5e1;
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
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    color: #172b43;
}

.heading-option:hover {
    background: #eaf5f2;
}

.opt-p {
    font-size: 0.9rem;
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
}

.toolbar-divider {
    width: 1px;
    height: 22px;
    background: #dbe3ee;
    margin: 0 0.25rem;
}

.tool-btn {
    min-width: 32px;
    height: 32px;
    padding: 0 0.45rem;
    border: 1px solid transparent;
    border-radius: 6px;
    background: transparent;
    color: #1e5f51;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.tool-btn:hover {
    background: #e2e8f0;
    color: #172b43;
}

.color-btn {
    position: relative;
}

.color-letter {
    font-weight: 800;
    border-bottom: 3px solid;
    line-height: 1;
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
    background: white;
    border: 1px solid #cbd5e1;
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
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 4px;
    cursor: pointer;
}

.char-cell:hover {
    background: #eaf5f2;
}

.editor-canvas-wrapper {
    position: relative;
    width: 100%;
    overflow: visible;
}

.editor-canvas {
    display: flow-root;
    min-height: 260px;
    padding: 1.15rem 1.25rem;
    font-size: 0.98rem;
    line-height: 1.7;
    color: #172b43;
    outline: none;
    overflow-wrap: anywhere;
}

.editor-canvas::after {
    content: "";
    display: table;
    clear: both;
}

.editor-canvas :deep(figure) {
    margin: 1rem 0;
    display: block;
    max-width: 100%;
    clear: both;
}

.editor-canvas :deep(figure.align-left) {
    float: left;
    width: auto;
    margin: 0.5rem 1.25rem 1rem 0;
    clear: none;
}

.editor-canvas :deep(figure.align-right) {
    float: right;
    width: auto;
    margin: 0.5rem 0 1rem 1.25rem;
    clear: none;
}

.editor-canvas :deep(figure.align-center) {
    display: block;
    width: fit-content;
    max-width: 100%;
    margin: 1.25rem auto;
    float: none;
    clear: both;
    text-align: center;
}

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
    max-width: 100%;          /* sebelumnya 360px, hapus */
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
    color: #64748b;
    margin-top: 0.35rem;
    text-align: center;
}

/* Overlay resize + floating toolbar */
.img-interactive-overlay {
    position: absolute;
    box-sizing: border-box;
    border: 2px solid #1e5f51;
    pointer-events: none;
    z-index: 50;
    border-radius: 4px;
}

.resize-handle {
    position: absolute;
    width: 10px;
    height: 10px;
    background: #fff;
    border: 2px solid #1e5f51;
    border-radius: 2px;
    pointer-events: auto;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
    z-index: 52;
}

.handle-nw { top: -5px; left: -5px; cursor: nwse-resize; }
.handle-ne { top: -5px; right: -5px; cursor: nesw-resize; }
.handle-sw { bottom: -5px; left: -5px; cursor: nesw-resize; }
.handle-se { bottom: -5px; right: -5px; cursor: nwse-resize; }

.img-float-toolbar {
    position: absolute;
    top: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    border: 1px solid #cbd5e1;
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
    border-color: transparent transparent #fff transparent;
}

.img-tb-btn {
    background: transparent;
    border: none;
    color: #172b43;
    width: 30px;
    height: 30px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.15s, color 0.15s;
}

.img-tb-btn:hover { background: #f1f5f9; color: #1e5f51; }
.img-tb-btn.active { background: #e6f5f2; color: #1e5f51; font-weight: 700; }

.img-tb-divider {
    width: 1px;
    height: 18px;
    background: #e2e8f0;
    margin: 0 2px;
}

.btn-delete-img { color: #dc2626; }
.btn-delete-img:hover { background: #fee2e2; color: #991b1b; }

.editor-statusbar {
    clear: both;
    width: 100%;
    display: flex;
    align-items: center;
    position: relative;
    z-index: 10;
    padding: 0.45rem 1rem;
    border-top: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: 0.78rem;
    color: #64748b;
    border-radius: 0 0 12px 12px;
}

/* Tombol hapus sampul */
.btn-remove-cover {
    margin-top: 10px;
    padding: 7px 14px;
    border: 1px solid #fca5a5;
    border-radius: 7px;
    background: #fee2e2;
    color: #991b1b;
    cursor: pointer;
}

/* Tombol sisip sampul ke isi naskah */
.cover-preview-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.btn-sampul-insert {
    margin-top: 10px;
    padding: 7px 14px;
    border: 1px solid #1e5f51;
    border-radius: 7px;
    background: #f0fdfa;
    color: #1e5f51;
    cursor: pointer;
}

.btn-sampul-insert:hover {
    background: #e6f5f2;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 600px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .naskah-form {
        padding: 1.5rem;
    }

    .page-hero h1 {
        font-size: 1.75rem;
    }

    .success-card {
        padding: 30px 18px;
    }

    .success-card h2 {
        font-size: 22px;
    }

    .detail-row {
        font-size: 12px;
    }

    .success-actions {
        flex-direction: column;
    }

    .btn-send-again,
    .btn-register {
        width: 100%;
        box-sizing: border-box;
    }

    .editor-toolbar {
        padding: 0.5rem;
        gap: 0.2rem;
    }

    .heading-trigger {
        min-width: 110px;
    }

    .editor-canvas {
        min-height: 220px;
        padding: 1rem;
    }

    .editor-canvas :deep(img) {
        max-width: 100%;
    }
}

/* ===== LINK STYLING IN EDITOR ===== */
.editor-canvas :deep(a) {
    color: #1a56db;
    text-decoration: underline;
    text-underline-offset: 2px;
    cursor: pointer;
    font-weight: 500;
    transition: color 0.15s;
}
.editor-canvas :deep(a:hover) {
    color: #1e4fc2;
}

/* ===== FLOATING LINK POPOVER ===== */
.floating-link-popover {
    position: absolute;
    z-index: 200;
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.13), 0 2px 8px rgba(0,0,0,0.08);
    width: 360px;
    max-width: calc(100% - 16px);
    padding: 10px 12px;
}

.floating-link-popover .popover-caret {
    position: absolute;
    bottom: -7px;
    width: 14px;
    height: 7px;
    overflow: hidden;
}
.floating-link-popover .popover-caret::before {
    content: '';
    position: absolute;
    top: -7px;
    left: 0;
    width: 14px;
    height: 14px;
    background: #fff;
    border: 1px solid #cbd5e1;
    transform: rotate(45deg);
    transform-origin: center;
}

.floating-link-popover.is-below .popover-caret {
    bottom: auto;
    top: -7px;
    transform: scaleY(-1);
}

.popover-title {
    display: block;
    font-size: 0.75rem;
    color: #64748b;
    margin-bottom: 7px;
    font-weight: 500;
}

.popover-input-row {
    display: flex;
    align-items: center;
    gap: 6px;
}

.link-url-input {
    flex: 1;
    padding: 6px 10px;
    border: 1.5px solid #3b82f6;
    border-radius: 6px;
    font-size: 0.875rem;
    outline: none;
    color: #1e293b;
    background: #f8fafc;
    transition: border-color 0.15s;
}
.link-url-input:focus {
    border-color: #2563eb;
    background: #fff;
}

.btn-link-submit {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    border: none;
    background: #2563eb;
    color: #fff;
    font-size: 1rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background 0.15s;
}
.btn-link-submit:hover { background: #1d4ed8; }

.btn-link-gear {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    color: #64748b;
    font-size: 1rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background 0.15s, color 0.15s;
}
.btn-link-gear:hover, .btn-link-gear.active { background: #dbeafe; color: #2563eb; border-color: #93c5fd; }

.tab-notice {
    font-size: 0.72rem;
    color: #2563eb;
    margin-top: 5px;
}

/* Preview mode */
.popover-preview-body { display: flex; flex-direction: column; gap: 8px; }

.preview-url-row {
    display: flex;
    align-items: center;
    gap: 6px;
    overflow: hidden;
}
.preview-url-text {
    font-size: 0.82rem;
    color: #1a56db;
    text-decoration: underline;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}

.preview-actions {
    display: flex;
    gap: 6px;
}
.btn-action-sm {
    padding: 4px 10px;
    font-size: 0.78rem;
    border-radius: 5px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    cursor: pointer;
    transition: background 0.12s;
}
.btn-open { color: #2563eb; border-color: #bfdbfe; }
.btn-open:hover { background: #dbeafe; }
.btn-edit { color: #374151; }
.btn-edit:hover { background: #f1f5f9; }
.btn-unlink { color: #dc2626; border-color: #fecaca; }
.btn-unlink:hover { background: #fee2e2; }
</style>