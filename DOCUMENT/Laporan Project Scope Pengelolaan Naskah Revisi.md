**LAPORAN PROJECT SCOPE**

**SISTEM MANAJEMEN PENGARSIPAN NASKAH BERITA**

*Disusun sebagai dokumen perencanaan ruang lingkup proyek*

Disusun oleh:  
Surya Aji Saputra			(24081010088)  
Nofa Tarisa Putri	 		(24081010002)  
Rysta Arrizky Khumairoh		(24081010088)  
Nike Devita Tri Jayanti		(24081010002)  
Arya Wahyu Abdi Wicaksono	(24081010043)

# **DAFTAR ISI**

BAB I PENDAHULUAN

1.1 Latar Belakang

1.2 Rumusan Masalah

1.3 Tujuan Proyek

1.4 Manfaat Proyek

1.5 Definisi Istilah

BAB II RUANG LINGKUP PROYEK

2.1 Deskripsi Proyek

2.2 Ruang Lingkup Fungsional

2.3 Pengguna dan Peran

2.4 Alur Proses Bisnis

2.5 Batasan Proyek (Dalam dan Luar Ruang Lingkup)

2.6 Asumsi dan Kendala

2.7 Hasil yang Diharapkan

BAB III PERENCANAAN PROYEK

3.1 Pemangku Kepentingan

3.2 Tahapan dan Jadwal Proyek

3.3 Organisasi Tim

3.4 Risiko dan Mitigasi

3.5 Pengendalian Perubahan Ruang Lingkup

BAB IV KRITERIA KEBERHASILAN

4.1 Kriteria Keberhasilan

4.2 Kriteria Penerimaan per Fitur

4.3 Penutup

# **BAB I PENDAHULUAN**

## **1.1 Latar Belakang**

Perkembangan teknologi informasi telah memberikan kemudahan dalam berbagai proses pengelolaan data dan dokumen, termasuk dalam kegiatan pengiriman dan peninjauan naskah. Dalam lingkungan akademik maupun penerbitan, proses pengiriman naskah, pemeriksaan kelengkapan dokumen, serta peninjauan oleh pihak yang berwenang merupakan tahapan penting untuk memastikan bahwa naskah memenuhi kriteria yang telah ditentukan. Namun, apabila proses tersebut masih dilakukan secara manual atau melalui berbagai media komunikasi yang terpisah, dapat muncul sejumlah kendala, seperti kesulitan dalam memantau status naskah, keterlambatan proses peninjauan, risiko kehilangan data, serta kurangnya keteraturan dalam penyimpanan dokumen.

Permasalahan tersebut menunjukkan perlunya suatu sistem yang mampu mengintegrasikan proses pengiriman, pengelolaan, dan peninjauan naskah dalam satu platform. Sistem ini diharapkan dapat membantu pengguna dalam mengirimkan naskah, mengelola data naskah, memantau perkembangan status pengajuan, serta memudahkan pihak peninjau dalam memberikan hasil evaluasi dan masukan. Selain itu, pengelolaan data secara terpusat dapat meningkatkan keteraturan administrasi dan mempermudah pencarian informasi yang berkaitan dengan setiap naskah.

Berdasarkan permasalahan tersebut, dibutuhkan sistem yang mencatat proses mulai dari pengiriman sampai hasil peninjauan. Alur sistem ini mengacu pada diagram Business Process Model and Notation (BPMN). Pengguna mulai dengan membuka menu homepage, lalu memilih login dan masuk sebagai guest atau mendaftar sebagai kontributor. Pendaftaran kontributor harus diverifikasi terlebih dahulu. Naskah yang dikirim kemudian ditinjau, dan hasil akhirnya  ditetapkan apakah naskah itu diterima atau ditolak.

## **1.2 Rumusan Masalah**

Berdasarkan latar belakang di atas, permasalahan yang ingin diselesaikan melalui proyek ini dirumuskan sebagai berikut:

1. Bagaimana alur login, pendaftaran, dan verifikasi kontributor dapat didefinisikan secara jelas?  
2. Bagaimana alur pengiriman naskah oleh guest maupun kontributor dapat dirancang secara terstruktur?  
3. Bagaimana proses peninjauan dan penentuan keputusan naskah (diterima atau ditolak dengan revisi) dapat dikelola dan dipantau?  
4. Bagaimana batasan proyek ditetapkan agar pengembangan sistem tidak melebar di luar kebutuhan yang disepakati?

## **1.3 Tujuan Proyek**

Tujuan dari proyek ini adalah:

1. Mendefinisikan tahapan proses pengiriman dan peninjauan naskah berdasarkan diagram BPMN.  
2. Menjelaskan fitur dan aktivitas yang termasuk dalam pengembangan sistem.  
3. Memperjelas peran pengguna dalam proses pendaftaran, pengiriman, dan peninjauan naskah.  
4. Menetapkan batasan serta hasil yang diharapkan sebagai acuan pelaksanaan proyek.

## **1.4 Manfaat Proyek**

* Bagi guest, menyediakan alur yang lebih jelas untuk mengakses layanan dan mengirimkan naskah.  
* Bagi kontributor, memberikan proses pendaftaran dan verifikasi sebelum memperoleh status kontributor, dapat mengakses layanan serta mengirimkan naskah dengan jelas.  
* Bagi pengelola, membantu mengelompokkan hasil peninjauan naskah berdasarkan keputusan yang diberikan.  
* Bagi tim pengembang, menjadi acuan agar pengembangan sistem tetap sesuai dengan kebutuhan dan batasan yang ditetapkan.

# **BAB II RUANG LINGKUP PROYEK**

## **2.1 Deskripsi Proyek**

Proyek ini berfokus pada perancangan sistem pengelolaan pengiriman dan peninjauan naskah secara digital. Cakupan proses meliputi akses pengguna, pendaftaran dan verifikasi kontributor, pengiriman naskah, proses review, serta penetapan keputusan terhadap naskah. Dokumen ini disusun berdasarkan aktivitas yang ada pada diagram BPMN dan dapat digunakan sebagai acuan awal dalam tahap analisis serta pengembangan sistem.

## **2.2 Ruang Lingkup Fungsional**

## **Login pengguna**

Pengguna melakukan login sebagai langkah awal untuk mengakses layanan sistem.

## **Pendaftaran kontributor**

Pengguna mengisi formulir pendaftaran untuk menjadi kontributor. Data yang diajukan kemudian diverifikasi.

## **Verifikasi kontributor**

Pihak yang berwenang memeriksa pendaftaran sebelum pengguna memperoleh status kontributor.

## **Akses sebagai guest**

Pengguna yang memilih jalur guest diarahkan ke halaman utama dan dapat melanjutkan ke formulir pengiriman naskah sesuai alur.

## **Pengiriman naskah**

Pengguna mengirimkan naskah melalui formulir yang tersedia untuk diproses lebih lanjut.

## **Peninjauan naskah**

Naskah yang masuk ditinjau oleh pihak yang bertanggung jawab untuk menilai kelayakannya.

## **Penentuan keputusan**

Hasil review ditetapkan menjadi diterima, perlu revisi, atau ditolak.

## **Tindak lanjut hasil review**

Naskah yang diterima masuk ke hasil naskah terbit dan naskah yang tidak diterima masuk ke naskah ditolak.

## **2.3 Pengguna dan Peran**

| Pengguna/Peran | Peran dalam Proses |
| :---- | :---- |
| Guest | Mengakses halaman utama melalui jalur guest dan mengirimkan naskah sesuai fasilitas yang disediakan. |
| Calon kontributor | Mengisi formulir pendaftaran dan menunggu verifikasi. |
| Kontributor | Mengakses layanan sebagai kontributor setelah pendaftaran diverifikasi dan mengirimkan naskah. |
| Admin | Melakukan review dan menentukan apakah naskah diterima, perlu direvisi, atau ditolak. |

## **2.4 Alur Proses Bisnis**

Alur proses berikut merupakan ringkasan tahapan yang tampak pada diagram BPMN acuan. Diagram BPMN lengkap dilampirkan 

![][image1]

Gambar 2.1.

**Tabel 2.1 Ringkasan Tahapan Proses**

| No | Tahapan | Aktor | Deskripsi | Luaran |
| :---: | :---- | :---- | :---- | :---- |
| 1 | Masuk sistem | Pengguna | Pengguna masuk ke sistem sebagai langkah awal. | Akses ke sistem |
| 2 | Pemilihan jalur | Pengguna | Pengguna memilih melanjutkan sebagai guest atau mendaftar sebagai kontributor. | Jalur akses terpilih |
| 3 | Pendaftaran kontributor | Calon kontributor | Mengisi dan mengirim formulir pendaftaran. | Data pendaftaran |
| 4 | Verifikasi kontributor | Pengelola | Memeriksa data pendaftaran calon kontributor. | Hasil verifikasi |
| 5 | Pengiriman naskah | Guest/Kontributor | Mengisi formulir dan mengirim naskah. | Naskah masuk |
| 6 | Peninjauan naskah | Admin | Menilai kelayakan naskah yang masuk. | Hasil penilaian |
| 7 | Penentuan keputusan | Admin | Menetapkan naskah diterima atau ditolak dengan revisi. | Keputusan naskah |
| 8 | Tindak lanjut | Sistem | Mengelompokkan naskah ke naskah terbit, naskah revisi, atau naskah ditolak. | Status akhir naskah |

## **2.5 Batasan Proyek (Dalam dan Luar Ruang Lingkup)**

Proyek dibatasi pada proses yang tercantum dalam diagram BPMN, yaitu login, pendaftaran dan verifikasi kontributor, akses guest, pengiriman naskah, review, serta keputusan akhir naskah. Fitur yang tidak ditunjukkan pada diagram—seperti pembayaran, integrasi dengan platform eksternal, analitik lanjutan, atau tanda tangan elektronik tersertifikasi—tidak termasuk dalam cakupan awal, kecuali ditambahkan melalui kesepakatan kebutuhan proyek.

Agar batasan lebih mudah dipahami, cakupan proyek dirangkum pada tabel berikut.

**Tabel 2.2 Cakupan Proyek**

| Termasuk dalam Ruang Lingkup (In-Scope) | Tidak Termasuk (Out-of-Scope) |
| :---- | :---- |
| Login pengguna | Pembayaran, biaya publikasi, dan royalti |
| Pendaftaran dan verifikasi kontributor | Integrasi dengan platform atau layanan eksternal |
| Akses sebagai guest | Analitik dan pelaporan lanjutan |
| Formulir pengiriman naskah | Tanda tangan elektronik tersertifikasi |
| Peninjauan naskah oleh reviewer | Pemeriksaan kemiripan/plagiarisme otomatis |
| Penentuan keputusan: diterima atau ditolak dengan revisi | Proses percetakan dan distribusi naskah terbit |
| Pengelompokan naskah terbit atau ditolak  | Aplikasi mobile native (di luar akses melalui peramban) |

## **2.6 Asumsi dan Kendala**

Asumsi yang digunakan dalam penyusunan ruang lingkup ini adalah:

* Diagram BPMN yang dilampirkan merupakan sumber acuan utama kebutuhan proyek.  
* Sistem dikembangkan berbasis software/aplikasi dan diakses dengan koneksi internet.  
* Naskah dikirimkan dalam bentuk berkas digital; jenis berkas dan batas ukuran ditetapkan pada tahap analisis kebutuhan.

Kendala yang dihadapi proyek antara lain:

* Waktu pelaksanaan proyek terbatas pada jadwal yang ditetapkan pada subbab 3.2.  
* Beberapa aturan—seperti kewenangan peran, format naskah, dan mekanisme notifikasi—masih perlu dikonfirmasi.

## **2.7 Hasil yang Diharapkan**

1. Kontributor tidak lagi melakukan absensi secara manual.  
2. Dokumen ruang lingkup proyek sebagai acuan pengembangan sistem.  
3. Alur pendaftaran dan verifikasi kontributor yang terdefinisi.  
4. Alur pengiriman dan peninjauan naskah yang terstruktur.  
5. Klasifikasi hasil review berupa diterima atau ditolak dengan revisi.  
6. Dokumentasi proses yang dapat digunakan untuk mendukung analisis dan pengujian sistem.

# **BAB III PERENCANAAN PROYEK**

## **3.1 Pemangku Kepentingan**

| Pemangku Kepentingan | Kepentingan | Keterlibatan |
| :---- | :---- | :---- |
| Guest | Mengirimkan naskah dengan mudah. | Pengguna sistem; memberi masukan kebutuhan. |
| Kontributor | Mendaftar, diverifikasi, dan mengirim naskah. | Pengguna sistem; menguji alur pendaftaran. |
| Admin | Meninjau naskah dan menetapkan keputusan. | Pengguna utama; memvalidasi alur review. |
| Tim perancang | Menghasilkan sistem sesuai kebutuhan. | Menganalisis, merancang, dan membangun sistem. |
| Dosen/pembimbing | Memastikan proyek sesuai ketentuan. | Memberi arahan dan menyetujui dokumen. |

## **3.2 Tahapan dan Jadwal Proyek**

Tahapan pelaksanaan proyek diperkirakan sebagai berikut. Estimasi waktu bersifat awal dan dapat disesuaikan dengan kalender dan ketentuan tugas.

| No | Tahapan | Aktivitas Utama | Estimasi | Keluaran |
| :---: | :---- | :---- | :---- | :---- |
| 1 | Analisis kebutuhan | Menggali dan mengkonfirmasi kebutuhan dari BPMN. | Minggu 1–2 | Spesifikasi kebutuhan |
| 2 | Perancangan | Merancang alur, basis data, dan antarmuka. | Minggu 3–4 | Dokumen rancangan |
| 3 | Pengembangan | Membangun fitur sesuai ruang lingkup. | Minggu 5–7 | Sistem/prototipe |
| 4 | Pengujian | Menguji fitur terhadap kriteria penerimaan. | Minggu 8–9 | Laporan pengujian |
| 5 | Finalisasi | Perbaikan akhir dan penyerahan dokumen. | Minggu 10 | Dokumen dan sistem final |

## **3.3 Organisasi Tim**

| Peran | Tanggung Jawab | Nama |
| :---- | :---- | :---- |
| Ketua proyek | Mengoordinasikan tim, jadwal, dan komunikasi. | Surya Aji Saputra |
| Analis sistem | Menyusun dan memvalidasi kebutuhan. | Nofa Tarisa Putri, Surya Aji Saputra |
| Front end developer | Merancang tampilan dan alur penggunaan. | Rysta Arrizky Khumairoh |
| Back end | Membangun fitur sesuai rancangan. | Arya Wahyu Abdi Wicaksono |
| Testing | Menyusun dan menjalankan skenario pengujian. | Nike Devita Tri Jayanti |

## **3.4 Risiko dan Mitigasi**

| Risiko | Dampak | Mitigasi |
| :---- | :---: | :---- |
| Perubahan kebutuhan di tengah proyek (scope creep) | Tinggi | Gunakan mekanisme pengendalian perubahan pada subbab 3.5. |
| Kewenangan peran belum jelas | Sedang | Konfirmasi peran dan hak akses pada tahap analisis kebutuhan. |
| Salah menafsirkan diagram BPMN | Sedang | Validasi hasil analisis kepada pembimbing/pemilik proses. |
| Jadwal tidak tercapai | Sedang | Prioritaskan fitur inti dan pantau progres secara berkala. |

## **3.5 Pengendalian Perubahan Ruang Lingkup**

Setiap permintaan perubahan terhadap ruang lingkup yang sudah disepakati ditangani dengan langkah berikut agar proyek tetap terkendali:

* Permintaan perubahan dicatat beserta alasan dan pihak yang mengajukan.  
* Tim mengevaluasi dampaknya terhadap waktu, tenaga, dan fitur lain.  
* Perubahan dilaksanakan hanya setelah disetujui oleh ketua proyek dan pembimbing.  
* Dokumen project scope diperbarui sesuai perubahan yang disetujui.

# **BAB IV KRITERIA KEBERHASILAN**

## **4.1 Kriteria Keberhasilan**

Proyek dianggap memenuhi ruang lingkup awal apabila:

* Pengguna dapat menjalankan alur login dan memilih jalur akses sesuai proses yang dirancang.  
* Formulir data guest dapat diisi dengan baik oleh pengguna yang memilih guest.  
* Formulir pendaftaran kontributor dapat diisi dan diproses melalui verifikasi.  
* Pengguna dapat mengirimkan naskah melalui formulir yang tersedia.  
* Naskah yang dikirimkan dapat masuk ke proses review.  
* Hasil review dapat ditetapkan sebagai diterima, perlu direvisi, atau ditolak.

## **4.2 Kriteria Penerimaan per Fitur**

Agar keberhasilan dapat diukur, setiap fitur dilengkapi kriteria penerimaan dan cara verifikasinya.

| No | Fitur | Kriteria Penerimaan | Cara Verifikasi |
| :---: | :---- | :---- | :---- |
| 1 | Login | Pengguna dapat masuk dan memilih jalur guest atau kontributor. | Uji skenario login |
| 2 | Guest | Masuk homepage, mengisi data diri dan mengirim naskah. | Uji skenario guest |
| 3 | Pendaftaran kontributor | Formulir dapat diisi, dikirim, dan masuk ke antrian verifikasi. | Uji skenario pendaftaran |
| 4 | Verifikasi kontributor | Pengelola dapat memeriksa pendaftaran dan status pengguna berubah sesuai hasil. | Uji skenario verifikasi |
| 5 | Pengiriman naskah | Naskah dapat dikirim melalui formulir dan tercatat di sistem. | Uji pengiriman naskah |
| 6 | Peninjauan dan keputusan | Reviewer dapat menetapkan status diterima atau ditolak dengan revisi. | Uji skenario review |
| 7 | Tindak lanjut hasil | Naskah masuk ke kelompok terbit, revisi atau ditolak sesuai keputusan. | Uji pengelompokan naskah |

## **4.3 Penutup**

Project scope ini menjadi acuan awal dalam pengembangan sistem pengelolaan pengiriman dan peninjauan naskah. Dengan adanya ruang lingkup yang terdefinisi, proses pengembangan diharapkan berjalan lebih terarah dan sesuai dengan alur bisnis yang telah dirancang. Detail teknis, aturan akses, penyimpanan dokumen, dan mekanisme notifikasi tetap perlu ditetapkan pada tahap analisis kebutuhan.

[image1]: <data:image/png;base64,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>