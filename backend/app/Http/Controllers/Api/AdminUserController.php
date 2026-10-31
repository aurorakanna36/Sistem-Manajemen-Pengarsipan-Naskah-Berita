<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Naskah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * List semua kontributor dengan pagination, search, dan filter status.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $query = User::where('role', 'kontributor')->latest();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('instansi', 'like', "%{$search}%")
                  ->orWhere('nomor_whatsapp', 'like', "%{$search}%");
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
            'id'                => $u->id,
            'nama_lengkap'      => $u->nama_lengkap,
            'name'              => $u->nama_lengkap,
            'email'             => $u->email,
            'nomor_whatsapp'    => $u->nomor_whatsapp,
            'phone_number'      => $u->nomor_whatsapp,
            'instansi'          => $u->instansi,
            'status_verifikasi' => $u->status_verifikasi,
            'file_path'         => $u->file_path ? asset('storage/' . $u->file_path) : null,
            'surat_administrasi'=> $u->file_path ? asset('storage/' . $u->file_path) : null,
            'created_at'        => $u->created_at ? $u->created_at->toDateTimeString() : null,
        ]);

        return response()->json([
            'data'  => $mappedUsers,
            'users' => $mappedUsers,
            'meta'  => [
                'current_page' => $users->currentPage(),
                'last_page'    => $users->lastPage(),
                'total'        => $users->total(),
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Verifikasi atau tolak akun kontributor.
     */
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
            'status'  => 'success',
            'message' => "Akun kontributor '{$user->nama_lengkap}' berhasil {$statusText}.",
        ]);
    }

    /**
     * Hapus akun kontributor.
     * Naskah milik kontributor di-nullkan (naskah tetap tersimpan).
     */
    public function destroy($id)
    {
        $user = User::where('role', 'kontributor')->findOrFail($id);

        Naskah::where('id_kontributor', $id)->update(['id_kontributor' => null]);

        $nama = $user->nama_lengkap;
        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => "Akun kontributor '{$nama}' berhasil dihapus.",
        ]);
    }

    /**
     * Detail satu kontributor beserta statistik naskah & chart bulanan.
     */
    public function showKontributor($id)
    {
        $user = User::where('role', 'kontributor')->findOrFail($id);

        $naskahs = Naskah::where('id_kontributor', $id)->latest('waktu_kirim')->get();

        $stats = [
            'total_dikirim' => $naskahs->count(),
            'diterima'      => $naskahs->whereIn('status_naskah', ['diterima', 'diterbitkan'])->count(),
            'ditolak'       => $naskahs->where('status_naskah', 'ditolak')->count(),
            'direvisi'      => $naskahs->where('status_naskah', 'direvisi')->count(),
        ];

        // Naskah per bulan — 6 bulan terakhir
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date   = Carbon::now()->subMonths($i);
            $key    = $date->format('Y-m');
            $label  = $date->format('M');
            $count  = $naskahs->filter(
                fn($n) => $n->waktu_kirim && $n->waktu_kirim->format('Y-m') === $key
            )->count();
            $months[] = ['month' => $label, 'count' => $count];
        }

        $naskahList = $naskahs->map(fn($n) => [
            'id'            => $n->id,
            'judul'         => $n->judul,
            'kategori'      => $n->kategori,
            'status_naskah' => $n->status_naskah,
            'waktu_kirim'   => $n->waktu_kirim ? $n->waktu_kirim->toDateTimeString() : null,
            'file_path'     => $n->file_path ? asset('storage/' . $n->file_path) : null,
        ])->values();

        return response()->json([
            'kontributor' => [
                'id'                => $user->id,
                'nama_lengkap'      => $user->nama_lengkap,
                'name'              => $user->nama_lengkap,
                'email'             => $user->email,
                'nomor_whatsapp'    => $user->nomor_whatsapp,
                'instansi'          => $user->instansi,
                'status_verifikasi' => $user->status_verifikasi,
                'file_path'         => $user->file_path ? asset('storage/' . $user->file_path) : null,
                'created_at'        => $user->created_at ? $user->created_at->toDateTimeString() : null,
            ],
            'stats'            => $stats,
            'naskah_per_bulan' => $months,
            'naskahs'          => $naskahList,
        ]);
    }

    /* ===================== ADMIN ACCOUNT CRUD ===================== */

    /**
     * List semua akun admin.
     */
    public function listAdmins()
    {
        $admins = User::where('role', 'admin')->latest()->get()->map(fn($u) => [
            'id'             => $u->id,
            'nama_lengkap'   => $u->nama_lengkap,
            'name'           => $u->nama_lengkap,
            'email'          => $u->email,
            'nomor_whatsapp' => $u->nomor_whatsapp,
            'instansi'       => $u->instansi,
            'created_at'     => $u->created_at ? $u->created_at->toDateTimeString() : null,
        ]);

        return response()->json(['data' => $admins, 'admins' => $admins]);
    }

    /**
     * Buat akun admin baru.
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:6'],
            'nomor_whatsapp' => ['nullable', 'string', 'max:20'],
            'instansi'       => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'nama_lengkap'      => $request->nama_lengkap,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'nomor_whatsapp'    => $request->nomor_whatsapp,
            'instansi'          => $request->instansi,
            'role'              => 'admin',
            'status_verifikasi' => 'verified',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => "Akun admin '{$user->nama_lengkap}' berhasil dibuat.",
            'admin'   => [
                'id'             => $user->id,
                'nama_lengkap'   => $user->nama_lengkap,
                'email'          => $user->email,
                'nomor_whatsapp' => $user->nomor_whatsapp,
                'instansi'       => $user->instansi,
            ],
        ], 201);
    }

    /**
     * Update akun admin.
     */
    public function updateAdmin(Request $request, $id)
    {
        $user = User::where('role', 'admin')->findOrFail($id);

        $request->validate([
            'nama_lengkap'   => ['sometimes', 'required', 'string', 'max:255'],
            'email'          => ['sometimes', 'required', 'email', 'unique:users,email,' . $id],
            'password'       => ['nullable', 'string', 'min:6'],
            'nomor_whatsapp' => ['nullable', 'string', 'max:20'],
            'instansi'       => ['nullable', 'string', 'max:255'],
        ]);

        $data = $request->only(['nama_lengkap', 'email', 'nomor_whatsapp', 'instansi']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => "Akun admin '{$user->nama_lengkap}' berhasil diperbarui.",
            'admin'   => [
                'id'             => $user->id,
                'nama_lengkap'   => $user->nama_lengkap,
                'email'          => $user->email,
                'nomor_whatsapp' => $user->nomor_whatsapp,
                'instansi'       => $user->instansi,
            ],
        ]);
    }

    /**
     * Hapus akun admin (tidak bisa hapus diri sendiri).
     */
    public function destroyAdmin($id)
    {
        $currentUser = Auth::user();

        if ($currentUser && (int) $currentUser->id === (int) $id) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 403);
        }

        $user = User::where('role', 'admin')->findOrFail($id);
        $nama = $user->nama_lengkap;
        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => "Akun admin '{$nama}' berhasil dihapus.",
        ]);
    }
}
