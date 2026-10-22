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
     * Tampilkan halaman login split-screen sesuai wireframe.
     */
    public function showLoginForm(Request $request)
    {
        // default role pilihan jika diarahkan dari homepage
        $selectedRole = $request->query('role', 'kontributor');
        return view('auth.login', compact('selectedRole'));
    }

    /**
     * Proses autentikasi pengguna.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'role' => ['required', 'in:kontributor,admin'],
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Validasi kesesuaian peran
            if ($user->role !== $credentials['role']) {
                Auth::logout();
                return back()->withErrors([
                    'role' => "Akun ini terdaftar sebagai {$user->role}, bukan {$credentials['role']}.",
                ])->withInput($request->only('email', 'role'));
            }

            // Jika admin
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.review'));
            }

            // Jika kontributor masih pending
            if ($user->status_verifikasi === 'pending') {
                return redirect()->route('kontributor.pending');
            }

            if ($user->status_verifikasi === 'rejected') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Pendaftaran kontributor Anda ditolak oleh redaksi.',
                ]);
            }

            return redirect()->intended(route('kontributor.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Kombinasi email atau kata sandi tidak cocok.',
        ])->withInput($request->only('email', 'role'));
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
