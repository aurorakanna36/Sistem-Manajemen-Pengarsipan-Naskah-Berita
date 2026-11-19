/**
 * Utility untuk memformat dan menormalkan URL gambar sampul naskah.
 * Mendukung berbagai format input:
 * - Objek naskah (mengambil gambar_sampul_url, gambar_sampul, atau <img> dari isi)
 * - Path relatif (misal: "sampul_naskah/xxx.jpg" atau "storage/sampul_naskah/xxx.jpg")
 * - Nama file saja (misal: "xxx.jpg")
 * - Full URL (http://... atau https://...)
 * - Data URL (data:image/...)
 * - Membersihkan duplikasi URL /storage/storage/
 */
export function getCoverImageUrl(imageOrItem, fallbackContent = '') {
  if (!imageOrItem) return ''

  let image = ''
  let content = fallbackContent

  if (typeof imageOrItem === 'object') {
    image =
      imageOrItem.gambar_sampul_url ||
      imageOrItem.gambar_sampul ||
      imageOrItem.cover_url ||
      imageOrItem.cover ||
      imageOrItem.sampul ||
      imageOrItem.image ||
      ''
    content =
      content ||
      imageOrItem.isi ||
      imageOrItem.isi_naskah ||
      imageOrItem.content ||
      ''
  } else if (typeof imageOrItem === 'string') {
    image = imageOrItem
  }

  // Jika belum ada gambar tapi ada konten HTML, coba ekstrak gambar pertama
  if (!image && content) {
    const match = content.match(/<img[^>]+src=["']([^"']+)["']/i)
    if (match && match[1]) {
      image = match[1]
    }
  }

  if (!image) return ''

  // Jika data URL atau blob URL
  if (image.startsWith('data:') || image.startsWith('blob:')) {
    return image
  }

  // Bersihkan duplikasi /storage/storage/ jika ada
  let clean = image.replace(/(\/storage)+\/storage\//g, '/storage/')

  // Jika sudah berupa URL http / https lengkap
  if (clean.startsWith('http://') || clean.startsWith('https://')) {
    return clean
  }

  // Bersihkan leading slash dan awalan storage/ jika ada
  clean = clean.replace(/^\/+/, '')
  if (clean.startsWith('storage/')) {
    clean = clean.substring(8)
  }

  // Jika hanya nama file tunggal tanpa direktori, asumsikan di folder sampul_naskah/
  if (!clean.includes('/')) {
    clean = 'sampul_naskah/' + clean
  }

  const baseUrl = (
    import.meta.env.VITE_APP_URL || 'http://localhost:8000'
  ).replace(/\/$/, '')

  return `${baseUrl}/storage/${clean}`
}

export default getCoverImageUrl
