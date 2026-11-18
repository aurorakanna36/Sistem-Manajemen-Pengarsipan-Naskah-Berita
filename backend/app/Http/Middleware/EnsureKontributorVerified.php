<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKontributorVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if ($user->role !== 'kontributor') {
            return response()->json([
                'message' => 'Akses ditolak. Rute ini hanya untuk kontributor.',
            ], 403);
        }

        if ($user->status_verifikasi === 'pending') {
            return response()->json([
                'message' => 'Akun kontributor Anda sedang menunggu verifikasi oleh redaksi.',
                'status_verifikasi' => 'pending',
            ], 403);
        }

        if ($user->status_verifikasi === 'rejected' || $user->status_verifikasi === 'ditolak') {
            return response()->json([
                'message' => 'Pendaftaran akun kontributor Anda telah ditolak oleh redaksi.',
                'status_verifikasi' => 'rejected',
            ], 403);
        }

        if ($user->status_verifikasi !== 'diverifikasi' && $user->status_verifikasi !== 'verified') {
            return response()->json([
                'message' => 'Akun kontributor Anda belum aktif atau belum diverifikasi.',
                'status_verifikasi' => $user->status_verifikasi,
            ], 403);
        }

        return $next($request);
    }
}
