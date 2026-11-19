<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar seluruh kontributor dengan pencarian dan filter status verifikasi.
     */
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

        $users = $query->paginate(15)->withQueryString();

        // Hitung statistik kontributor
        $totalKontributor = User::where('role', 'kontributor')->count();
        $pendingCount = User::where('role', 'kontributor')->where('status_verifikasi', 'pending')->count();
        $verifiedCount = User::where('role', 'kontributor')->where('status_verifikasi', 'verified')->count();
        $rejectedCount = User::where('role', 'kontributor')->where('status_verifikasi', 'rejected')->count();

        return view('admin.users.index', compact(
            'users',
            'search',
            'status',
            'totalKontributor',
            'pendingCount',
            'verifiedCount',
            'rejectedCount'
        ));
    }

    /**
     * Verifikasi atau tolak akun pendaftaran kontributor.
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', 'in:verified,rejected'],
        ]);

        $user = User::where('role', 'kontributor')->findOrFail($id);

        $newStatus = $request->status;
        $user->update([
            'status_verifikasi' => $newStatus,
        ]);

        $statusText = ($newStatus === 'verified') ? 'diverifikasi dan disetujui untuk login' : 'ditolak';

        return back()->with('success', "Akun kontributor '{$user->name}' berhasil {$statusText}.");
    }
}
