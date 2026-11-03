<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Naskah;

class PublicBeritaController extends Controller
{
    public function index()
    {
        $berita = Naskah::with(['kategoris', 'kontributor', 'guest', 'publikasi'])
            ->where('status_naskah', 'diterbitkan')
            ->latest('waktu_kirim')
            ->take(12)
            ->get()
            ->map(function ($naskah) {
                $penulis = $naskah->nama_kontributor
                    ?? $naskah->kontributor?->nama_lengkap
                    ?? $naskah->nama_guest
                    ?? $naskah->guest?->nama
                    ?? 'Redaksi';

                return [
                    'id' => $naskah->id,
                    'judul' => $naskah->judul,
                    'abstrak' => $naskah->abstrak,
                    'isi' => $naskah->isi,
                    'gambar_sampul' => $naskah->gambar_sampul ? asset('storage/' . $naskah->gambar_sampul) : null,
                    'kategori' => $naskah->kategoris->isNotEmpty() ? $naskah->kategoris->pluck('nama_kategori')->implode(', ') : $naskah->kategori,
                    'kategoris' => $naskah->kategoris->map(fn($k) => ['id' => $k->id, 'nama_kategori' => $k->nama_kategori, 'slug' => $k->slug])->values(),
                    'url_berita' => $naskah->publikasi?->url_berita,
                    'waktu_terbit' => $naskah->publikasi?->waktu_terbit ? $naskah->publikasi->waktu_terbit->toDateString() : ($naskah->waktu_kirim ? $naskah->waktu_kirim->toDateString() : null),
                    'created_at' => $naskah->waktu_kirim ? $naskah->waktu_kirim->toDateString() : null,
                    'penulis' => $penulis,
                ];
            });

        return response()->json(['data' => $berita]);
    }
}
