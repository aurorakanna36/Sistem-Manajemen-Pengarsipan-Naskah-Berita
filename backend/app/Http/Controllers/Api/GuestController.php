<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Naskah;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['nullable', 'string', 'max:50'],
            'nama_pengirim' => ['nullable', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:50'],
            'email_pengirim' => ['nullable', 'email', 'max:50'],
            'nomot_Whatsapp' => ['nullable', 'string', 'max:20'],
            'nomor_whatsapp' => ['nullable', 'string', 'max:20'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'phone_pengirim' => ['nullable', 'string', 'max:20'],
            'instansi' => ['nullable', 'string', 'max:50'],
            'sosial_media' => ['nullable', 'string', 'max:50'],
            'judul' => ['required', 'string'],
            'kategori' => ['nullable', 'string', 'max:30'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'berkas_naskah' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $nama = $validated['nama'] ?? $validated['nama_pengirim'] ?? $validated['name'] ?? 'Tamu';
        $email = $validated['email'] ?? $validated['email_pengirim'] ?? 'tamu@cakrawala.com';
        $whatsapp = $validated['nomot_Whatsapp'] ?? $validated['nomor_whatsapp'] ?? $validated['phone_pengirim'] ?? $validated['phone_number'] ?? null;
        $kategori = !empty($validated['kategori']) ? $validated['kategori'] : 'Berita';

        $guest = Guest::create([
            'nama' => $nama,
            'email' => $email,
            'nomot_Whatsapp' => $whatsapp,
            'instansi' => $validated['instansi'] ?? null,
            'sosial_media' => $validated['sosial_media'] ?? null,
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('naskah_files', 'public');
        } elseif ($request->hasFile('berkas_naskah')) {
            $filePath = $request->file('berkas_naskah')->store('naskah_files', 'public');
        }

        $naskah = Naskah::create([
            'id_guest' => $guest->id,
            'nama_guest' => $guest->nama,
            'judul' => $validated['judul'],
            'kategori' => $kategori,
            'file_path' => $filePath,
            'status_naskah' => 'perlu_direview',
            'waktu_kirim' => now(),
        ]);

        RiwayatStatus::create([
            'id_naskah' => $naskah->id,
            'status' => 'perlu_direview',
            'catatan' => 'Naskah tamu berhasil dikirim.',
            'waktu_status' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Naskah berhasil dikirim. Perkembangan verifikasi akan diinformasikan melalui kontak Anda.',
            'data' => [
                'id' => $naskah->id,
                'judul' => $naskah->judul,
                'status_naskah' => $naskah->status_naskah,
            ],
        ], 201);
    }
}
