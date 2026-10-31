<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Guest;
use App\Models\Kontributor;
use App\Models\Naskah;
use App\Models\Publikasi;
use App\Models\Review;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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
            'kategori' => 'Teknologi',
            'status_naskah' => 'diterbitkan',
            'waktu_kirim' => now()->subDays(3),
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
            'status' => 'perlu_direview',
            'catatan' => 'Naskah berhasil diajukan.',
            'waktu_status' => now()->subDays(3),
        ]);

        RiwayatStatus::create([
            'id_kontributor' => $kontributor->id,
            'id_naskah' => $naskah1->id,
            'status' => 'diterima',
            'catatan' => 'Naskah disetujui untuk terbit.',
            'waktu_status' => now()->subDays(2),
        ]);

        RiwayatStatus::create([
            'id_kontributor' => $kontributor->id,
            'id_naskah' => $naskah1->id,
            'status' => 'diterbitkan',
            'catatan' => 'Naskah telah terbit di portal berita.',
            'waktu_status' => now()->subDays(1),
        ]);

        $naskah2 = Naskah::create([
            'id_kontributor' => $kontributor->id,
            'nama_kontributor' => $kontributor->nama_lengkap,
            'judul' => 'Strategi Pertumbuhan Ekonomi Kreatif Jawa Timur',
            'kategori' => 'Ekonomi',
            'status_naskah' => 'perlu_direview',
            'waktu_kirim' => now()->subDay(),
        ]);

        RiwayatStatus::create([
            'id_kontributor' => $kontributor->id,
            'id_naskah' => $naskah2->id,
            'status' => 'perlu_direview',
            'catatan' => 'Naskah baru diajukan, menunggu review admin.',
            'waktu_status' => now()->subDay(),
        ]);

        $naskah3 = Naskah::create([
            'id_kontributor' => $kontributor->id,
            'nama_kontributor' => $kontributor->nama_lengkap,
            'judul' => 'Kajian Kebudayaan Tradisional Nusantara',
            'kategori' => 'Pendidikan',
            'status_naskah' => 'ditolak',
            'waktu_kirim' => now()->subDays(4),
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
            'waktu_status' => now()->subDays(3),
        ]);

        $naskah4 = Naskah::create([
            'id_guest' => $guest->id,
            'nama_guest' => $guest->nama,
            'judul' => 'Suara Warga: Pentingnya Penghijauan Jalur Pedestrian',
            'kategori' => 'Opini',
            'status_naskah' => 'perlu_direview',
            'waktu_kirim' => now(),
        ]);

        RiwayatStatus::create([
            'id_naskah' => $naskah4->id,
            'status' => 'perlu_direview',
            'catatan' => 'Naskah tamu masuk ke sistem.',
            'waktu_status' => now(),
        ]);
    }
}
