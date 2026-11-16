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
                'email_gmail' => $user->email_gmail,
                'nomor_whatsapp' => $user->nomor_whatsapp,
                'phone_number' => $user->nomor_whatsapp,
                'role' => $user->role,
                'status_verifikasi' => $user->status_verifikasi,
                'instansi' => $user->instansi,
                'file_path' => $user->file_path ? asset('storage/' . $user->file_path) : null,
                'foto_identitas' => $user->foto_identitas ? asset('storage/' . $user->foto_identitas) : null,
                'tanda_tangan' => $user->tanda_tangan ? asset('storage/' . $user->tanda_tangan) : null,
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
            'foto_identitas' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'ktp' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'tanda_tangan' => ['nullable'],
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

        $fotoIdentitasPath = null;
        if ($request->hasFile('foto_identitas')) {
            $fotoIdentitasPath = $request->file('foto_identitas')->store('identitas_kontributor', 'public');
        } elseif ($request->hasFile('ktp')) {
            $fotoIdentitasPath = $request->file('ktp')->store('identitas_kontributor', 'public');
        }

        $tandaTanganPath = null;
        if ($request->hasFile('tanda_tangan')) {
            $tandaTanganPath = $request->file('tanda_tangan')->store('tanda_tangan', 'public');
        } elseif ($request->filled('tanda_tangan')) {
            $signatureInput = $request->input('tanda_tangan');
            if (is_string($signatureInput) && str_starts_with($signatureInput, 'data:image')) {
                @list($type, $data) = explode(';', $signatureInput);
                @list(, $data) = explode(',', $data);
                if ($data) {
                    $decodedImage = base64_decode($data);
                    $fileName = 'tanda_tangan/' . \Illuminate\Support\Str::random(40) . '.png';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $decodedImage);
                    $tandaTanganPath = $fileName;
                }
            }
        }

        $user = User::create([
            'nama_lengkap' => $namaLengkap,
            'email' => $validated['email'],
            'email_gmail' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'kontributor',
            'status_verifikasi' => 'pending',
            'nomor_whatsapp' => $nomorWhatsapp,
            'instansi' => $validated['instansi'] ?? null,
            'foto_identitas' => $fotoIdentitasPath,
            'file_path' => $filePath,
            'tanda_tangan' => $tandaTanganPath,
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
                'email_gmail' => $user->email_gmail,
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
                'email_gmail' => $user->email_gmail,
                'nomor_whatsapp' => $user->nomor_whatsapp,
                'phone_number' => $user->nomor_whatsapp,
                'role' => $user->role,
                'status_verifikasi' => $user->status_verifikasi,
                'instansi' => $user->instansi,
                'file_path' => $user->file_path ? asset('storage/' . $user->file_path) : null,
                'foto_identitas' => $user->foto_identitas ? asset('storage/' . $user->foto_identitas) : null,
                'tanda_tangan' => $user->tanda_tangan ? asset('storage/' . $user->tanda_tangan) : null,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => 'success']);
    }
}
