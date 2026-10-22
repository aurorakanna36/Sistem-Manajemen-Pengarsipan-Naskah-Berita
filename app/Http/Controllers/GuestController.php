<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Naskah;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function showKirimForm()
    {
        return view('guest.kirim-naskah');
    }

    public function storeNaskah(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string'],
            'abstrak' => ['nullable', 'string'],
            'isi_naskah' => ['nullable', 'string'],
            'berkas_naskah' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $guest = Guest::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'instansi' => $validated['instansi'] ?? null,
        ]);

        $berkasPath = null;
        if ($request->hasFile('berkas_naskah')) {
            $berkasPath = $request->file('berkas_naskah')->store('naskah_files', 'public');
        }

        Naskah::create([
            'judul' => $validated['judul'],
            'kategori' => $validated['kategori'],
            'abstrak' => $validated['abstrak'] ?? null,
            'isi_naskah' => $validated['isi_naskah'] ?? null,
            'berkas_naskah' => $berkasPath,
            'author_type' => Guest::class,
            'author_id' => $guest->id,
            'tahap' => 'pengarsipan',
            'keputusan' => 'menunggu',
            'is_published' => false,
        ]);

        return redirect()->route('home')->with('success', 'Naskah tamu berhasil dikirim ke arsip Harian Cakrawala! Perkembangan verifikasi akan diinformasikan melalui Gmail Anda.');
    }
}
