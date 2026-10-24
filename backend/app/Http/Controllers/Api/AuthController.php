<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
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
            'token'  => $token,
            'user'   => [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'role'              => $user->role,
                'status_verifikasi' => $user->status_verifikasi,
                'instansi'          => $user->instansi,
            ],
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number'        => ['required', 'string', 'max:20'],
            'instansi'            => ['nullable', 'string', 'max:255'],
            'password'            => ['required', 'confirmed', 'min:8'],
            'surat_administrasi'  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $suratPath = null;
        if ($request->hasFile('surat_administrasi')) {
            $suratPath = $request->file('surat_administrasi')->store('administrasi', 'public');
        }

        $user = User::create([
            'name'               => $validated['name'],
            'email'              => $validated['email'],
            'password'           => Hash::make($validated['password']),
            'role'               => 'kontributor',
            'status_verifikasi'  => 'pending',
            'phone_number'       => $validated['phone_number'],
            'instansi'           => $validated['instansi'] ?? null,
            'surat_administrasi' => $suratPath,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'token'  => $token,
            'user'   => [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'role'              => $user->role,
                'status_verifikasi' => $user->status_verifikasi,
            ],
        ], 201);
    }

    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => 'success']);
    }
}
