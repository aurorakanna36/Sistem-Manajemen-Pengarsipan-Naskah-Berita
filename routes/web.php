<?php

use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\Kontributor\KontributorDashboardController;
use App\Models\Naskah;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Manajemen Pengarsipan Naskah (Harian Cakrawala)
|--------------------------------------------------------------------------
*/

// 1. Homepage Portal Berita (Wireframe Atas Kiri)
Route::get('/', function () {
    $beritaTerbaru = Naskah::where('is_published', true)->latest()->take(6)->get();
    return view('home', compact('beritaTerbaru'));
})->name('home');

// 2. Rute Autentikasi (Wireframe Login & Registrasi)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/daftar-kontributor', [AuthController::class, 'showRegisterForm'])->name('register.kontributor');
Route::post('/daftar-kontributor', [AuthController::class, 'register']);
Route::get('/menunggu-verifikasi', [AuthController::class, 'pendingNotice'])->name('kontributor.pending');

// 3. Rute Pengiriman Naskah Guest (Wireframe Alur Tamu)
Route::get('/guest/kirim-naskah', [GuestController::class, 'showKirimForm'])->name('guest.kirim');
Route::post('/guest/kirim-naskah', [GuestController::class, 'storeNaskah'])->name('guest.store');

// 4. Area Kontributor (Wireframe Dashboard Kontributor)
Route::middleware(['auth'])->prefix('kontributor')->name('kontributor.')->group(function () {
    Route::get('/dashboard', [KontributorDashboardController::class, 'index'])->name('dashboard');
    Route::post('/naskah', [KontributorDashboardController::class, 'storeNaskah'])->name('naskah.store');
});

// 5. Area Admin (Wireframe Dashboard Review Naskah)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/review-naskah', [AdminReviewController::class, 'index'])->name('review');
    Route::post('/review-naskah/{id}/decide', [AdminReviewController::class, 'decide'])->name('decide');
});
