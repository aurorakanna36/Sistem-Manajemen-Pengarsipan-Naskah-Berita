<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Naskah;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'nama_pengirim' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'email_pengirim' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'phone_pengirim' => ['nullable', 'string', 'max:20'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string'],
            'abstrak' => ['nullable', 'string'],
            'isi_naskah' => ['nullable', 'string'],
            'isi' => ['nullable', 'string'],
            'berkas_naskah' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $name = $validated['nama_pengirim'] ?? $validated['name'] ?? 'Tamu';
        $email = $validated['email_pengirim'] ?? $validated['email'] ?? 'tamu@cakrawala.com';
        $phone = $validated['phone_pengirim'] ?? $validated['phone_number'] ?? null;
        $isiText = $validated['isi_naskah'] ?? $validated['isi'] ?? null;
        $kategori = !empty($validated['kategori']) ? $validated['kategori'] : 'Umum';

        $guest = Guest::create([
            'name' => $name,
            'email' => $email,
            'phone_number' => $phone,
            'instansi' => $validated['instansi'] ?? null,
        ]);

        $berkasPath = null;
        if ($request->hasFile('berkas_naskah')) {
            $berkasPath = $request->file('berkas_naskah')->store('naskah_files', 'public');
        } elseif ($request->hasFile('file')) {
            $berkasPath = $request->file('file')->store('naskah_files', 'public');
        }

        Naskah::create([
            'judul' => $validated['judul'],
            'kategori' => $kategori,
            'abstrak' => $validated['abstrak'] ?? null,
            'isi_naskah' => $isiText,
            'berkas_naskah' => $berkasPath,
            'author_type' => Guest::class,
            'author_id' => $guest->id,
            'tahap' => 'pengarsipan',
            'keputusan' => 'menunggu',
            'is_published' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Naskah berhasil dikirim. Perkembangan verifikasi akan diinformasikan melalui email Anda.',
        ], 201);
    }
}
