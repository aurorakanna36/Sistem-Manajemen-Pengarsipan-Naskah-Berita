<?php


namespace Database\Seeders;

use App\Models\Guest;
use App\Models\Naskah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Redaksi
        $admin = User::create([
            'name' => 'Admin Redaksi Cakrawala',
            'email' => 'admin@cakrawala.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status_verifikasi' => 'verified',
            'phone_number' => '081234567890',
            'instansi' => 'Harian Cakrawala',
        ]);

        // 2. Akun Kontributor Aktif (Terverifikasi)
        $kontributor = User::create([
            'name' => 'Kontributor Utama',
            'email' => 'kontributor@cakrawala.com',
            'password' => Hash::make('password123'),
            'role' => 'kontributor',
            'status_verifikasi' => 'verified',
            'phone_number' => '089876543210',
            'instansi' => 'Universitas Surabaya',
        ]);

        // 3. Akun Calon Kontributor (Masih Pending)
        User::create([
            'name' => 'Calon Kontributor Baru',
            'email' => 'calon@cakrawala.com',
            'password' => Hash::make('password123'),
            'role' => 'kontributor',
            'status_verifikasi' => 'pending',
            'phone_number' => '081122334455',
            'instansi' => 'Komunitas Penulis',
        ]);

        // 4. Guest Contoh
        $guest = Guest::create([
            'name' => 'Budi Penulis Tamu',
            'email' => 'budi.guest@gmail.com',
            'phone_number' => '085711223344',
            'instansi' => 'Umum',
        ]);

        // 5. Naskah Contoh Milik Kontributor (Status Terbit)
        Naskah::create([
            'judul' => 'Inovasi Teknologi Terkini di Era Digital 2026',
            'kategori' => 'Teknologi',
            'abstrak' => 'Ulasan mengenai percepatan kecerdasan buatan dalam transformasi media dan penerbitan pers.',
            'isi_naskah' => 'Perkembangan kecerdasan buatan kini memberikan dampak nyata pada dunia publikasi dan efisiensi redaksi...',
            'author_type' => User::class,
            'author_id' => $kontributor->id,
            'tahap' => 'selesai',
            'keputusan' => 'diterima',
            'is_published' => true,
        ]);

        // 6. Naskah Contoh Milik Kontributor (Status Dalam Verifikasi / Review)
        Naskah::create([
            'judul' => 'Strategi Pertumbuhan Ekonomi Kreatif Jawa Timur',
            'kategori' => 'Ekonomi',
            'abstrak' => 'Analisis komprehensif terhadap sektor UMKM dan ekonomi kreatif lokal pasca pandemi.',
            'isi_naskah' => 'Sektor ekonomi kreatif terus mencatatkan pertumbuhan positif di berbagai daerah...',
            'author_type' => User::class,
            'author_id' => $kontributor->id,
            'tahap' => 'verifikasi',
            'keputusan' => 'menunggu',
            'is_published' => false,
        ]);

        // 7. Naskah Contoh Milik Kontributor (Status Ditolak)
        Naskah::create([
            'judul' => 'Kajian Kebudayaan Tradisional Nusantara',
            'kategori' => 'Pendidikan',
            'abstrak' => 'Studi literatur tentang pelestarian seni tradisional di era global.',
            'isi_naskah' => 'Melestarikan warisan leluhur menjadi tanggung jawab bersama...',
            'author_type' => User::class,
            'author_id' => $kontributor->id,
            'tahap' => 'selesai',
            'keputusan' => 'ditolak',
            'is_published' => false,
            'alasan_penolakan' => 'Topik naskah belum memenuhi kriteria aktualitas berita minggu ini, mohon perbarui data sumber.',
        ]);

        // 8. Naskah Contoh dari Guest (Status Pengarsipan Baru Masuk)
        Naskah::create([
            'judul' => 'Suara Warga: Pentingnya Penghijauan Jalur pedestrian',
            'kategori' => 'Opini',
            'abstrak' => 'Pandangan masyarakat terhadap kenyamanan tata ruang kota.',
            'isi_naskah' => 'Sebagai pengguna jalan kaki setiap hari, pohon peneduh sangat krusial...',
            'author_type' => Guest::class,
            'author_id' => $guest->id,
            'tahap' => 'pengarsipan',
            'keputusan' => 'menunggu',
            'is_published' => false,
        ]);
    }
}
