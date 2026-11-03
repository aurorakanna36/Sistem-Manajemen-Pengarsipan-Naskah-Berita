<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Guest;
use App\Models\Kategori;
use App\Models\Kontributor;
use App\Models\Naskah;
use App\Models\Publikasi;
use App\Models\Review;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $defaultCategories = [
            'Berita',
            'Nasional',
            'Pendidikan',
            'Teknologi',
            'Bisnis',
            'Daerah',
            'Ekonomi',
            'Kriminal',
            'Olahraga',
            'Opini',
            'Otomotif',
            'Politik',
            'Uncategorized',
        ];

        $kategoriModels = [];
        foreach ($defaultCategories as $catName) {
            $kategoriModels[$catName] = Kategori::updateOrCreate(
                ['slug' => Str::slug($catName)],
                ['nama_kategori' => $catName]
            );
        }

        $admin = User::create([
            'nama_lengkap' => 'Admin Redaksi Cakrawala',
            'email' => 'admin@cakrawala.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status_verifikasi' => 'verified',
            'nomor_whatsapp' => '081234567890',
            'instansi' => 'Harian Cakrawala',
        ]);

        Admin::create([
            'user_id' => $admin->id,
            'email' => $admin->email,
        ]);

        $kontributor = User::create([
            'nama_lengkap' => 'Kontributor Utama',
            'email' => 'kontributor@cakrawala.com',
            'password' => Hash::make('password123'),
            'role' => 'kontributor',
            'status_verifikasi' => 'verified',
            'nomor_whatsapp' => '089876543210',
            'instansi' => 'Universitas Surabaya',
        ]);

        Kontributor::create([
            'user_id' => $kontributor->id,
            'email' => $kontributor->email,
        ]);

        $calon = User::create([
            'nama_lengkap' => 'Calon Kontributor Baru',
            'email' => 'calon@cakrawala.com',
            'password' => Hash::make('password123'),
            'role' => 'kontributor',
            'status_verifikasi' => 'pending',
            'nomor_whatsapp' => '081122334455',
            'instansi' => 'Komunitas Penulis',
        ]);

        Kontributor::create([
            'user_id' => $calon->id,
            'email' => $calon->email,
        ]);

        $guest = Guest::create([
            'nama' => 'Budi Penulis Tamu',
            'email' => 'budi.guest@gmail.com',
            'nomot_Whatsapp' => '085711223344',
            'instansi' => 'Umum',
            'sosial_media' => '@budipenulis',
        ]);

        $naskah1 = Naskah::create([
            'id_kontributor' => $kontributor->id,
            'nama_kontributor' => $kontributor->nama_lengkap,
            'judul' => 'Inovasi Teknologi Terkini di Era Digital 2026',
            'kata_kunci' => 'teknologi, digital, inovasi, AI',
            'abstrak' => 'Perkembangan teknologi kecerdasan buatan dan infrastruktur digital mendorong transformasi layanan publik serta industri kreatif di Indonesia pada tahun 2026.',
            'isi' => '<h1>Inovasi Teknologi Terkini di Era Digital 2026</h1><p>Transformasi digital di Indonesia terus mengalami akselerasi pesat. Berbagai sektor mulai dari pendidikan hingga layanan publik kini mengadopsi sistem berbasis kecerdasan buatan.</p><h2>Dampak bagi Industri Kreatif</h2><p>Pelaku industri kreatif memanfaatkan perangkat analitik dan otomatisasi untuk meningkatkan produktivitas serta jangkauan audiens secara nasional.</p>',
            'kategori' => 'Teknologi, Nasional',
            'status_naskah' => 'diterbitkan',
            'waktu_kirim' => now()->subDays(3),
        ]);
        $naskah1->kategoris()->sync([
            $kategoriModels['Teknologi']->id,
            $kategoriModels['Nasional']->id,
        ]);

        Review::create([
            'id_admin' => $admin->id,
            'id_naskah' => $naskah1->id,
            'keputusan' => 'diterima',
            'catatan' => 'Naskah sangat baik dan layak diterbitkan.',
            'waktu_review' => now()->subDays(2),
        ]);

        Publikasi::create([
            'id_admin' => $admin->id,
            'id_naskah' => $naskah1->id,
            'url_berita' => 'https://cakrawala.com/berita/inovasi-teknologi-2026',
            'waktu_terbit' => now()->subDays(1),
        ]);

        RiwayatStatus::create([
            'id_kontributor' => $kontributor->id,
            'id_naskah' => $naskah1->id,
            'status' => 'diterima',
            'catatan' => 'Selamat, naskahmu sudah diterbitkan dan tampil di homepage.',
            'is_read' => false,
            'waktu_status' => now()->subHours(2),
        ]);

        $naskah2 = Naskah::create([
            'id_kontributor' => $kontributor->id,
            'nama_kontributor' => $kontributor->nama_lengkap,
            'judul' => 'Strategi Pertumbuhan Ekonomi Kreatif Jawa Timur',
            'kata_kunci' => 'ekonomi kreatif, UMKM, Jawa Timur',
            'abstrak' => 'Pertumbuhan UMKM dan ekonomi kreatif di Jawa Timur menunjukkan tren positif berkat kolaborasi digital dan dukungan pembiayaan daerah.',
            'isi' => '<h2>Pertumbuhan Ekonomi Kreatif</h2><p>Sektor ekonomi kreatif menjadi tulang punggung baru bagi perekonomian daerah di Jawa Timur dengan melibatkan ribuan pelaku UMKM.</p>',
            'kategori' => 'Ekonomi, Daerah',
            'status_naskah' => 'perlu_direview',
            'waktu_kirim' => now()->subDay(),
        ]);
        $naskah2->kategoris()->sync([
            $kategoriModels['Ekonomi']->id,
            $kategoriModels['Daerah']->id,
        ]);

        RiwayatStatus::create([
            'id_kontributor' => $kontributor->id,
            'id_naskah' => $naskah2->id,
            'status' => 'perlu_direview',
            'catatan' => 'Naskah baru diajukan, menunggu review admin.',
            'is_read' => true,
            'waktu_status' => now()->subDay(),
        ]);

        $naskah3 = Naskah::create([
            'id_kontributor' => $kontributor->id,
            'nama_kontributor' => $kontributor->nama_lengkap,
            'judul' => 'Kajian Kebudayaan Tradisional Nusantara',
            'kata_kunci' => 'budaya, pendidikan, nusantara',
            'abstrak' => 'Pelestarian kebudayaan tradisional melalui kurikulum pendidikan menjadi langkah strategis menjaga identitas generasi muda.',
            'isi' => '<p>Upaya pelestarian kebudayaan lokal memerlukan sinergi antara sekolah, keluarga, dan komunitas seni daerah.</p>',
            'kategori' => 'Pendidikan, Opini',
            'status_naskah' => 'ditolak',
            'waktu_kirim' => now()->subDays(4),
        ]);
        $naskah3->kategoris()->sync([
            $kategoriModels['Pendidikan']->id,
            $kategoriModels['Opini']->id,
        ]);

        Review::create([
            'id_admin' => $admin->id,
            'id_naskah' => $naskah3->id,
            'keputusan' => 'ditolak',
            'catatan' => 'Topik naskah belum memenuhi kriteria aktualitas berita minggu ini.',
            'waktu_review' => now()->subDays(3),
        ]);

        RiwayatStatus::create([
            'id_kontributor' => $kontributor->id,
            'id_naskah' => $naskah3->id,
            'status' => 'ditolak',
            'catatan' => 'Topik naskah belum memenuhi kriteria aktualitas berita minggu ini.',
            'is_read' => false,
            'waktu_status' => now()->subDay(),
        ]);

        $naskahRevisi = Naskah::create([
            'id_kontributor' => $kontributor->id,
            'nama_kontributor' => $kontributor->nama_lengkap,
            'judul' => 'Transformasi Pendidikan Vokasi di Era Industri 5.0',
            'kata_kunci' => 'pendidikan vokasi, industri, teknologi',
            'abstrak' => 'Pendidikan vokasi perlu menyelaraskan kurikulum praktik dengan kebutuhan industri modern agar lulusan siap kerja.',
            'isi' => '<h2>Kurikulum Berbasis Industri</h2><p>Sekolah vokasi dan politeknik mulai memperbarui laboratorium serta program magang terpadu bersama mitra industri.</p>',
            'kategori' => 'Pendidikan, Teknologi',
            'status_naskah' => 'direvisi',
            'waktu_kirim' => now()->subDays(2),
        ]);
        $naskahRevisi->kategoris()->sync([
            $kategoriModels['Pendidikan']->id,
            $kategoriModels['Teknologi']->id,
        ]);

        Review::create([
            'id_admin' => $admin->id,
            'id_naskah' => $naskahRevisi->id,
            'keputusan' => 'direvisi',
            'catatan' => 'Mohon tambahkan data statistik serapan lulusan vokasi tahun terakhir.',
            'waktu_review' => now()->subDay(),
        ]);

        RiwayatStatus::create([
            'id_kontributor' => $kontributor->id,
            'id_naskah' => $naskahRevisi->id,
            'status' => 'direvisi',
            'catatan' => 'Mohon tambahkan data statistik serapan lulusan vokasi tahun terakhir.',
            'is_read' => false,
            'waktu_status' => now()->subDay(),
        ]);

        $naskah4 = Naskah::create([
            'id_guest' => $guest->id,
            'nama_guest' => $guest->nama,
            'judul' => 'Suara Warga: Pentingnya Penghijauan Jalur Pedestrian',
            'kata_kunci' => 'pedestrian, lingkungan, opini warga',
            'abstrak' => 'Penghijauan jalur pejalan kaki di pusat kota sangat dibutuhkan untuk meningkatkan kenyamanan warga.',
            'isi' => '<p>Jalur pedestrian yang teduh akan mendorong masyarakat lebih gemar berjalan kaki dan menggunakan transportasi umum.</p>',
            'kategori' => 'Opini',
            'status_naskah' => 'perlu_direview',
            'waktu_kirim' => now(),
        ]);
        $naskah4->kategoris()->sync([
            $kategoriModels['Opini']->id,
        ]);

        RiwayatStatus::create([
            'id_naskah' => $naskah4->id,
            'status' => 'perlu_direview',
            'catatan' => 'Naskah tamu masuk ke sistem.',
            'is_read' => true,
            'waktu_status' => now(),
        ]);
    }
}
