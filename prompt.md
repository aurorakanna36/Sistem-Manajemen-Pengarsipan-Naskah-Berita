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

				card ke 5 . menunjukan informasi naskah  yang dikirim perbulan berupa grafik batang dengan perhitungan naskah yagn dipost setiap bulannya . 
			  card ke 6 (bersifat mapping atua lopping ) : kumpulan card naskah yang dibaut oleh kontributor.

**Halaman Revisi Naskah** :
		-> 3 container /card: 
			  -> 1.menampilakn naskah yang dibuat oleh kontirbutor
			  -> 2.menampilkan informasi singkat  kontributor dan memberikan text link mengarah ke halaman detail kontributor.
			  -> 3. card keputusan yang berisi 3 select option mengugnakab radio button dengan 3 status (terima dan terbitkan, minta revisi, dan tolak naskah) tidak lupa juga dibawahnya ada catatan atau alasna untuk mebmerikan komentar ke naskah tersbeut.  dan button simpan keputusan 
   
		  GUNAKAN LAYOUT UI UX DARI SORUCE ATAU REFRENSI YANG SAYA BERIKAN
        ( source / refrensi :images\Desain Sistem Pengelolaan Naskah_ADMIN/Kelola Kontributor png dan Dashboard Addmin)
        
    saat ini buatkan impelemantion plannya reciew beberapa halaman yang sudah say a buat selbeumnya bandingkan dengan request saya diatas mana yang perlu ditamabhakan dan diupdate. perlu dingat diatas adalah akses halaman atau fitur untuk role admin. 