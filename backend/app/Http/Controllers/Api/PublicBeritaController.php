<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Naskah;

class PublicBeritaController extends Controller
{
    public function index()
    {
        $berita = Naskah::with('author')
            ->where('is_published', true)
            ->latest()
            ->take(12)
            ->get()
            ->map(function ($naskah) {
                return [
                    'id'         => $naskah->id,
                    'judul'      => $naskah->judul,
                    'kategori'   => $naskah->kategori,
                    'abstrak'    => $naskah->abstrak,
                    'created_at' => $naskah->created_at->toDateString(),
                    'penulis'    => $naskah->author?->name ?? 'Anonim',
                ];
            });

        return response()->json(['data' => $berita]);
    }
}
