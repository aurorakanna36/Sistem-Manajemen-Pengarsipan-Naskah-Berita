<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Kategori;
use App\Models\Naskah;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    private function ensureDefaultKategoris(): void
    {
        $defaultKategoris = [
            'Berita', 'Nasional', 'Pendidikan', 'Teknologi', 'Bisnis',
            'Daerah', 'Ekonomi', 'Kriminal', 'Olahraga', 'Opini',
            'Otomotif', 'Politik', 'Uncategorized',
        ];

        foreach ($defaultKategoris as $name) {
            Kategori::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['nama_kategori' => $name]
            );
        }
    }

    public function store(Request $request)
    {
        $this->ensureDefaultKategoris();

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
            'kategori' => ['nullable', 'string'],
            'kategori_ids' => ['nullable'],
            'kata_kunci' => ['nullable', 'string', 'max:255'],
            'isi' => ['nullable', 'string'],
            'isi_naskah' => ['nullable', 'string'],
            'gambar_sampul' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'berkas_naskah' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $nama = $validated['nama'] ?? $validated['nama_pengirim'] ?? $validated['name'] ?? 'Tamu';
        $email = $validated['email'] ?? $validated['email_pengirim'] ?? 'tamu@cakrawala.com';
        $whatsapp = $validated['nomot_Whatsapp'] ?? $validated['nomor_whatsapp'] ?? $validated['phone_pengirim'] ?? $validated['phone_number'] ?? null;

        $kategoriIds = $request->input('kategori_ids', []);
        if (is_string($kategoriIds)) {
            $decoded = json_decode($kategoriIds, true);
            $kategoriIds = is_array($decoded) ? $decoded : array_filter(explode(',', $kategoriIds));
        }

        if (empty($kategoriIds) && !empty($validated['kategori'])) {
            $names = array_map('trim', explode(',', $validated['kategori']));
            $kategoriIds = Kategori::whereIn('nama_kategori', $names)->pluck('id')->all();
        }

        if (empty($kategoriIds)) {
            $defaultKat = Kategori::where('nama_kategori', 'Uncategorized')->first()
                ?? Kategori::first();
            if ($defaultKat) {
                $kategoriIds = [$defaultKat->id];
            }
        }

        $selectedKategoris = Kategori::whereIn('id', $kategoriIds)->get();
        $kategoriString = $selectedKategoris->isNotEmpty()
            ? $selectedKategoris->pluck('nama_kategori')->implode(', ')
            : 'Uncategorized';

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

        $gambarSampulPath = null;
        if ($request->hasFile('gambar_sampul')) {
            $gambarSampulPath = $request->file('gambar_sampul')->store('sampul_naskah', 'public');
        }

        $isi = $validated['isi'] ?? $validated['isi_naskah'] ?? $request->input('isi') ?? $request->input('isi_naskah') ?? null;

        $naskah = Naskah::create([
            'id_guest' => $guest->id,
            'nama_guest' => $guest->nama,
            'judul' => $validated['judul'],
            'kata_kunci' => $validated['kata_kunci'] ?? null,
            'isi' => $isi,
            'kategori' => $kategoriString,
            'gambar_sampul' => $gambarSampulPath,
            'file_path' => $filePath,
            'status_naskah' => 'perlu_direview',
            'waktu_kirim' => now(),
        ]);

        if (!empty($kategoriIds)) {
            $naskah->kategoris()->sync($kategoriIds);
        }

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

    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'alt' => ['nullable', 'string', 'max:255'],
            'judul' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $file = $request->file('file');
        $path = $file->store('media_naskah', 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'status' => 'success',
            'media' => [
                'url' => $url,
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'alt' => $request->input('alt', ''),
                'judul' => $request->input('judul', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
                'keterangan' => $request->input('keterangan', ''),
                'deskripsi' => $request->input('deskripsi', ''),
            ],
        ], 201);
    }
}
