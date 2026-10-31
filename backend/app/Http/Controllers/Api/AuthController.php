<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kontributor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kombinasi email atau kata sandi tidak cocok.'],
            ]);
        }

        if ($user->role === 'kontributor') {
            if ($user->status_verifikasi === 'pending') {
                throw ValidationException::withMessages([
                    'email' => ['Akun kontributor Anda sedang menunggu verifikasi oleh redaksi.'],
                ]);
            }

            if ($user->status_verifikasi === 'rejected') {
                throw ValidationException::withMessages([
                    'email' => ['Pendaftaran akun kontributor Anda ditolak oleh redaksi.'],
                ]);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'name' => $user->nama_lengkap,
                'email' => $user->email,
                'nomor_whatsapp' => $user->nomor_whatsapp,
                'phone_number' => $user->nomor_whatsapp,
                'role' => $user->role,
                'status_verifikasi' => $user->status_verifikasi,
                'instansi' => $user->instansi,
                'file_path' => $user->file_path ? asset('storage/' . $user->file_path) : null,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => ['nullable', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:50', 'unique:users,email'],
            'nomor_whatsapp' => ['nullable', 'string', 'max:20'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'instansi' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'confirmed', 'min:8'],
            'file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'surat_administrasi' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $namaLengkap = $validated['nama_lengkap'] ?? $validated['name'] ?? '';
        if (empty($namaLengkap)) {
            throw ValidationException::withMessages([
                'nama_lengkap' => ['Nama lengkap wajib diisi.'],
            ]);
        }

        $nomorWhatsapp = $validated['nomor_whatsapp'] ?? $validated['phone_number'] ?? null;

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('administrasi', 'public');
        } elseif ($request->hasFile('surat_administrasi')) {
            $filePath = $request->file('surat_administrasi')->store('administrasi', 'public');
        }

        $user = User::create([
            'nama_lengkap' => $namaLengkap,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'kontributor',
            'status_verifikasi' => 'pending',
            'nomor_whatsapp' => $nomorWhatsapp,
            'instansi' => $validated['instansi'] ?? null,
            'file_path' => $filePath,
        ]);

        Kontributor::create([
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'name' => $user->nama_lengkap,
                'email' => $user->email,
                'role' => $user->role,
                'status_verifikasi' => $user->status_verifikasi,
            ],
        ], 201);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'name' => $user->nama_lengkap,
                'email' => $user->email,
                'nomor_whatsapp' => $user->nomor_whatsapp,
                'phone_number' => $user->nomor_whatsapp,
                'role' => $user->role,
                'status_verifikasi' => $user->status_verifikasi,
                'instansi' => $user->instansi,
                'file_path' => $user->file_path ? asset('storage/' . $user->file_path) : null,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => 'success']);
    }
}
