<?php

use App\Http\Controllers\Api\AdminReviewController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\KontributorController;
use App\Http\Controllers\Api\PublicBeritaController;
use Illuminate\Support\Facades\Route;

Route::get('/public/berita', [PublicBeritaController::class, 'index']);

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register-kontributor', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::post('/guest/kirim-naskah', [GuestController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('kontributor')->group(function () {
        Route::get('/dashboard', [KontributorController::class, 'dashboard']);
        Route::post('/naskah', [KontributorController::class, 'storeNaskah']);
    });

    Route::prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminReviewController::class, 'dashboard']);

        // Naskah
        Route::get('/naskahs', [AdminReviewController::class, 'index']);
        Route::get('/naskahs/{id}', [AdminReviewController::class, 'show']);
        Route::post('/naskahs/{id}/decide', [AdminReviewController::class, 'decide']);
        Route::post('/naskahs/{id}/publish', [AdminReviewController::class, 'publish']);

        // Kontributor (users)
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::post('/users/{id}/verify', [AdminUserController::class, 'verify']);
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);
        Route::get('/kontributor/{id}', [AdminUserController::class, 'showKontributor']);

        // Admin account management
        Route::get('/admins', [AdminUserController::class, 'listAdmins']);
        Route::post('/admins', [AdminUserController::class, 'storeAdmin']);
        Route::put('/admins/{id}', [AdminUserController::class, 'updateAdmin']);
        Route::delete('/admins/{id}', [AdminUserController::class, 'destroyAdmin']);
    });
});
