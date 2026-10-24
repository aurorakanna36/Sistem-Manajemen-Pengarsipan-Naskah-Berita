<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = User::where('role', 'kontributor')->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if (!empty($status) && in_array($status, ['pending', 'verified', 'rejected'])) {
            $query->where('status_verifikasi', $status);
        }

        $users = $query->paginate(15);

        $stats = [
            'total'    => User::where('role', 'kontributor')->count(),
            'pending'  => User::where('role', 'kontributor')->where('status_verifikasi', 'pending')->count(),
            'verified' => User::where('role', 'kontributor')->where('status_verifikasi', 'verified')->count(),
            'rejected' => User::where('role', 'kontributor')->where('status_verifikasi', 'rejected')->count(),
        ];

        $mappedUsers = $users->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'phone_number' => $u->phone_number,
            'instansi' => $u->instansi,
            'status_verifikasi' => $u->status_verifikasi,
            'surat_administrasi' => $u->surat_administrasi
                ? asset('storage/' . $u->surat_administrasi)
                : null,
            'created_at' => $u->created_at ? $u->created_at->toDateTimeString() : null,
        ]);

        return response()->json([
            'data' => $mappedUsers,
            'users' => $mappedUsers,
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
            ],
            'stats' => $stats,
        ]);
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:verified,approved,rejected'],
        ]);

        $status = ($request->status === 'approved') ? 'verified' : $request->status;

        $user = User::where('role', 'kontributor')->findOrFail($id);
        $user->update(['status_verifikasi' => $status]);

        $statusText = ($status === 'verified') ? 'diverifikasi' : 'ditolak';

        return response()->json([
            'status' => 'success',
            'message' => "Akun kontributor '{$user->name}' berhasil {$statusText}.",
        ]);
    }
}
