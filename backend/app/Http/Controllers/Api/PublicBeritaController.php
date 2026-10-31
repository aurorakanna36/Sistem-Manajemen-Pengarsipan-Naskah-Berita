<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Naskah;

class PublicBeritaController extends Controller
{
    public function index()
    {
        $berita = Naskah::with(['kontributor', 'guest', 'publikasi'])
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
                    'kategori' => $naskah->kategori,
                    'url_berita' => $naskah->publikasi?->url_berita,
                    'waktu_terbit' => $naskah->publikasi?->waktu_terbit ? $naskah->publikasi->waktu_terbit->toDateString() : ($naskah->waktu_kirim ? $naskah->waktu_kirim->toDateString() : null),
                    'created_at' => $naskah->waktu_kirim ? $naskah->waktu_kirim->toDateString() : null,
                    'penulis' => $penulis,
                ];
            });

        return response()->json(['data' => $berita]);
    }
}
