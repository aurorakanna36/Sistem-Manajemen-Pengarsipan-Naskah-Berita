<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login split-screen.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi pengguna secara otomatis berdasarkan email dan password.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Jika peran adalah Admin
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.review'));
            }

            // Jika peran adalah Kontributor
            if ($user->role === 'kontributor') {
                if ($user->status_verifikasi === 'pending') {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Akun kontributor Anda sedang menunggu verifikasi oleh redaksi sebelum dapat masuk.',
                    ])->withInput($request->only('email'));
                }

                if ($user->status_verifikasi === 'rejected') {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Pendaftaran akun kontributor Anda ditolak oleh redaksi.',
                    ])->withInput($request->only('email'));
                }

                return redirect()->intended(route('kontributor.dashboard'));
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Kombinasi email atau kata sandi tidak cocok.',
        ])->withInput($request->only('email'));
    }

    /**
     * Tampilkan halaman registrasi kontributor sesuai wireframe.
     */
    public function showRegisterForm()
    {
        return view('auth.register-kontributor');
    }

    /**
     * Proses pendaftaran kontributor baru.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', 'min:8'],
            'surat_administrasi' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $suratPath = null;
        if ($request->hasFile('surat_administrasi')) {
            $suratPath = $request->file('surat_administrasi')->store('administrasi', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'kontributor',
            'status_verifikasi' => 'pending',
            'phone_number' => $validated['phone_number'],
            'instansi' => $validated['instansi'] ?? null,
            'surat_administrasi' => $suratPath,
        ]);

        Auth::login($user);

        return redirect()->route('kontributor.pending');
    }

    /**
     * Halaman pemberitahuan status verifikasi pending bagi calon kontributor.
     */
    public function pendingNotice()
    {
        return view('auth.pending');
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
