**PROMP 1 [ Pengubahan database, penyesuaian sistem dengna database yang baru ( hanya update nama tabel )]  **

dapat diperhatikan pad gambar ERD yang saya buat/. seblumnya say sudah membuat database sendiri degann gambar pada fodler images/ERD lama. disitu adlaah gamabr relasi ,t abel, column yang say buat sebleum adanya gambar images/ERDBARU. 

pada task kali ini saya ingin mealkukan penyesuain database lama agar menggunakan ERD BARU yang dibuat. tidak hanya itu tentunay diperlukan penyesuaian di frontendnya yang mengambil dari databse karena ada beberap anama columnd an tabel yang berubah.

dapat anda lihat  pada gambar ERD BARU ada beberap tabel yang ditambahkan tapi disini saya menyorot apda bagian table publikasi, review dan riwayat status karena sisanya kuranglebih sama dengan sebleumnya seprti role user yang bedanya dipisah diseitap rolenya menggunakand tabl;e sendiri sendiri tidaxck sperrti sebelumnya.

dengan 3 hal barus eperti review publikasi dan riwayat status say kaan foksu yang pertama yaitu RIWAYAT STATUS.

pada tabel ini berfungsi sebagai notifikasi naskah yang dipost atau dibuat oleh kontributor. naskah baru akan memilikih 3 status (sisi kontributor)( direvisi, diterima, ditolak )  ini untuk mebmeritau kabar atau status dari naskah yangg dibuat.s edangkan disisi admin ketambahan 1 status yaitu PERLU DIREVIEW agar admin tau naskah yang ebelum di cek oleh admin.

karna flownya naskah dibuat -> masuk ke dalam arsip -> direview admin - > update status ditolak / diterima - > diterbitkan.

ke dua ada table review ini berfungsi sebagai catatan selama proses review  naskah. ini hanay bisa digunakan oleh admin.

publikasi: berufugnsi ketika naskah sudah selesai direvisi atau dicek atau direview admin dapat menerbitkan  atau mempublikasikanny. didalamnya ada columm rurl berita yang nantinay berisi url link dari naskah tersbeut nantinya. admin juga dapat mengatur waktu kapan naskah dipublish  oleh karen aitu kamis ediakan kolumn waktu_terbit menggunakan date .

untuk saat ini buat impelemntaiton plan debngab tujuan penyusian databsae lama dengan erd baru penamaan kolom, tabele tipe data samakan dengan erd baru. 


**PROMPT 2** []
 UI UX. halaman ADMIN. hal yang sebleumnya sudah ada  review naskah, dan managemn user.
 
 eview naskah fitur review naskah sudah berjaland engan baiks cara fotnend dan back endnya mulai mesetuju, minta revisi dan tolak.
manajemen user jgua dikhsuus kan untuk memperlihatkan jumlah user. dan fitur utamanya adallah verifikasi akun kontributor agar akun kontributor baru dapat login. admin jgua bsia menolaks ehinga kaun koitnrtibutor tidak bisa login ektika ditolak.

saya ingin merubah forntendnya layout, pewarnaan, peanmabhan halaman, ffitur, modal pop up dan lainlain.

step ppertama buat envirotnemnt schema collor sesuai dengangambar color palete yang saya sediakan di folder images/color pallete.png. anda juga dapat melihat atua megnambild ari pewarnaan gambar alaman-halman dari folder images/desain sistem pengolaan naskah_admin atau contributor. disitu ada beberapa gambar setiap halaman yang dapat kalian ambil colornya. tapi say asudah emmudahkan dengan menambahkan file gambar collor palete.png didalm fodler iamges.


step ke dua. halaman admin.
 halaman admin yang sudah ada hanya ad 2 review naskah dan  manajemen user.
 kurang lebih fitur yang ada di kedua halaman sudah benar TAPI say ingin mengubah desainnya seperti pada folder images/Desain Sistem Pengolaan Naskah_ADMIN/ dan juga menambahakan bebrapa hlaman yang belum ada seperti card, pembuatan grafik batang.

balaman yang sudah ditambahkan ada kelola user beberap fitur yang sudah ada tapi ada yang harus ditambaaakan atau diubah:

	 halaman kelola user: 
	 yang sudah ada: 
	  -> vervikasi kontributor: sudah dapat mengudpdate status kontirbutor yang baru       mendaftar.
	  -> dapat melakukan delete akun kontributor
		-> tabel neunjukan seluruh akun  kotnributro dengan statusnya.

         Halaman Review naskah 
        yang belum anda: 
         -> halaman detail kontributor ektika admin memecnet link LIHAT pada akun kotriniutor di tabel.
          -> belum ada fitur menampilkan akun admin, menambahknan, update, delete admin.
           
**halaman dashboard** : 
	    menampilkan 5 card: 
		    Total naskah  ( diambil kdari seluruh kontributor)
             Total Kontributor ( akun kontributor yang terdaftar )
             Perlu di review (menampilkan JUMLAHnaskah yang belum direview oleh admin )
             naskah menunggu review (menampilakn naskah-naskah yang perlu direview dan terdapat link BUKA untuk diarahkan ek detail naskah tersebut )
              Kontributor perlu diverfiaksi ( akun-akun yang baru mendaftar sebagai kontributor yang membutuhkan persetujuan. ketika di klik link text verfikasi diarahkan ke detail kotnributor).
            
**Halaman detail kontributor** :
            -> terdapat 8 card:
            card 1: 
              -> informasi identitas kontributor card paling atas. didalamnya ada nama kontributor, email, riwayat bergabung, status akun, dan inforamsi lain sesaui tabel yang sudah dirancang sebleumnya.
              4 card informasi untuk naskah: 
              ->  1. jumlah naskah dikirim
              -> 2. jumlah naskah yang diterima
              -> 3.jumlah naskah yang ditolak
              -> 4. jumlah naskah yang direvisi
              card 5. presentase naskah yang didalamnya ada lingkaran seperti diagram yang warnanya sesuai dengan 3 status naskha milik kotnributor yaitu diterima, ditoalk dan direvisi.

				- [ ] card ke 5 . menunjukan informasi naskah  yang dikirim perbulan berupa grafik batang dengan perhitungan naskah yagn dipost setiap bulannya . 
			  card ke 6 (bersifat mapping atua lopping ) : kumpulan card naskah yang dibaut oleh kontributor.

**Halaman Revisi Naskah** :
		-> 3 container /card: 
			  -> 1.menampilakn naskah yang dibuat oleh kontirbutor
			  -> 2.menampilkan informasi singkat  kontributor dan memberikan text link mengarah ke halaman detail kontributor.
			  -> 3. card keputusan yang berisi 3 select option mengugnakab radio button dengan 3 status (terima dan terbitkan, minta revisi, dan tolak naskah) tidak lupa juga dibawahnya ada catatan atau alasna untuk mebmerikan komentar ke naskah tersbeut.  dan button simpan keputusan 
   
		  GUNAKAN LAYOUT UI UX DARI SORUCE ATAU REFRENSI YANG SAYA BERIKAN
        ( source / refrensi :images\Desain Sistem Pengelolaan Naskah_ADMIN/Kelola Kontributor png dan Dashboard Addmin)
        
    saat ini buatkan impelemantion plannya reciew beberapa halaman yang sudah say a buat selbeumnya bandingkan dengan request saya diatas mana yang perlu ditamabhakan dan diupdate. perlu dingat diatas adalah akses halaman atau fitur untuk role admin. 


prompt [[3]] update halaman kontributor

saya sudah menambahkan folder images/Desain Sistem Pengolahan Naskah_CONTRIBUTOR. yang berisi kumpulan gambar frameware dari halaman yang bisa diakses oleh kontributor.

halaman yang sudah ada pada sistem sebelumnya seperti dahsboard yang berii:
4 card: total naskah, sedang ditinjau, disetujui. terbit, dan perlu direview dan tabel  dari naskah tyang dimiliki oleh kontributor.

saya ingin merubah halaman dashboard diantaranya.

mengupdate 4 card yang sduah ada menjadi 5 card yaitu 
dikirim ( naskah yang sduah dikirim  (degnan status apapun)),  dalam recview, direvisi Diterima, dan ditolak.

kemudian ada card aktiftas terbaru dimana menampilkan naskah yang baru dibuat oleh kontributor degnan statusnay dan nanti dapat diklik fieldn judul naskah atau satu fieldnya mengarah ke detail naskah tersebut.

disampignnya ada card  presentase naskah yang menunjukan  donat chart  menampilkan total naskah dengan 3 indikator yang sebagai inputan chart emnggunakan3 status yaitu Diterima, Ditolak, Dalam proses. 

halaman kirim naskah (sebleumnay sduah ada) taoi masih berupa pop up tidak halaman sendiri sehingga saya ingin membuat halamn terpisah.

isinay seperti form yang ada field judul naskah, kategori, kata kunci, abstrak , isi naskah, dan gambar sampul.

ada bebrapa yang perlu dreivisi dari gambar refrensi frameware nya images/Desain Sistem Pengolahan Naskah_CONTRIBUTOR/kirim naskah.png.

pada pilih kategori jiak apda frame ware berupa select option sehingga hanya bsia memilih satu kategori. saya ingin mengubahnya seperti kumpulan radio button yang bisa dipilih lebih dari satu kategori.

TAPI REVISI DIATAS JUGA MENGUBAH STURKUTUR ERD YANG SUDAH ADA. mengapa? 

kategori saat ini berupa column enum yang ada pada tabel naskab. karena bisa pilih lebih dari 1 kategori harus dipisah atau dibuatkan tabel sendiri agar tabel naskah bisa memilih lebih dari satu kartegori dengan mebuat relasi belongs to many satu naskah puny abanyak kateogri (tolong perhatikan bagian ERD atau DATABASE INI !!) 

pada textfield isi naskah saya ingin membuat bebrapa fitur.  Bold, icon list numbering, list tanpa numbering, kutipan, link (menyisipkan link ), coretan di tengah tulisan, text color, garis mendatar, karakter khusus, dan yang terpenting  pembuatan HEADING 1 ukuran besar,, heading 2 mengengah dan seterunsya, redo dan undo, dan yang terkahir, perlebar dan persempit inden. 

refrensi fitur diatas ada pada images/Desain Sistem Pengolahan Naskah_CONTRIBUTOR/fitur isi.png naskah dan  images/Desain Sistem Pengolahan Naskah_CONTRIBUTOR/fitur heading.png


hapus bagian berkas naskah dan sisakan input atau pilih berkas untuk gambar sampul dan pastikan ada preiew atau nantinya masuk kedalam isi naskah untuk gambarnya. Oh yaa di folder refrensi desain  images/Desain Sistem Pengolahan Naskah_CONTRIBUTOR/popup review image.png sudah sday tambahkan sebagai refrensi. yang diambiol cukup  bagian unggah berkas nanto ada satu button di tengah upload file yang berasla dari  penyimpanan internal. kemudian  disebelah kriinya sam apersih seperti menginputkan text alt, judul, keternagna gambar, deskirpsi, dan url berkas. ketika disisipkan kme dalam pos akan masuk ke bagian isi berita. 

contoh isi naskah setelah gambar disisipkabn??  images/Desain Sistem Pengolahan Naskah_CONTRIBUTOR/popup review image.png.

saat ini buatkan implementation plan nya untuk saya review dan revisi jika ada yang perlu diupdate.