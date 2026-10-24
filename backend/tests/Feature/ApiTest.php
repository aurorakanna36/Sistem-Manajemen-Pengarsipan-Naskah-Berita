<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class ApiTest extends TestCase
{
    public function test_public_berita_returns_success(): void
    {
        $response = $this->getJson('/api/public/berita');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_admin_can_login_and_receive_token(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@cakrawala.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'token',
            'user' => ['id', 'name', 'email', 'role'],
        ]);
    }

    public function test_guest_can_submit_naskah(): void
    {
        $response = $this->postJson('/api/guest/kirim-naskah', [
            'nama_pengirim' => 'Tamu Penguji',
            'email_pengirim' => 'tamupenguji@cakrawala.com',
            'judul' => 'Judul Naskah Pengujian Tamu',
            'isi' => 'Konten teks lengkap naskah berita dari tamu.',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['status', 'message']);
    }

    public function test_kontributor_dashboard_requires_auth(): void
    {
        $response = $this->getJson('/api/kontributor/dashboard');
        $response->assertStatus(401);
    }

    public function test_kontributor_can_access_dashboard_with_token(): void
    {
        $kontributor = User::where('role', 'kontributor')->where('status_verifikasi', 'verified')->first();
        if (!$kontributor) {
            $this->markTestSkipped('Kontributor user not found in database');
        }

        $token = $kontributor->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/kontributor/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user',
            'stats' => ['total', 'draft', 'disetujui'],
            'naskahs',
        ]);
    }

    public function test_admin_can_fetch_users_and_naskahs_with_token(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('Admin user not found in database');
        }

        $token = $admin->createToken('admin_test_token')->plainTextToken;

        $usersRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/users');
        $usersRes->assertStatus(200);
        $usersRes->assertJsonStructure(['data', 'users', 'stats']);

        $naskahsRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/naskahs');
        $naskahsRes->assertStatus(200);
        $naskahsRes->assertJsonStructure(['data', 'naskahs']);
    }
}
