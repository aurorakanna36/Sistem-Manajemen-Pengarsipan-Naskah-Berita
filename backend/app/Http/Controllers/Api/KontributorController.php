<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Naskah;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KontributorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $naskahs = Naskah::where('id_kontributor', $user->id)
            ->with(['reviews.admin', 'publikasi', 'riwayatStatus'])
            ->latest('waktu_kirim')
            ->get();

        $dalamReview = $naskahs->where('status_naskah', 'perlu_direview')->count();
        $disetujui = $naskahs->where('status_naskah', 'diterima')->count();
        $terbit = $naskahs->where('status_naskah', 'diterbitkan')->count();
        $ditolak = $naskahs->where('status_naskah', 'ditolak')->count();
        $revisi = $naskahs->where('status_naskah', 'direvisi')->count();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'nama_lengkap' => $user->nama_lengkap,
                'name' => $user->nama_lengkap,
                'email' => $user->email,
                'instansi' => $user->instansi,
                'nomor_whatsapp' => $user->nomor_whatsapp,
            ],
            'stats' => [
                'total' => $naskahs->count(),
                'draft' => $dalamReview,
                'dalam_review' => $dalamReview,
                'disetujui' => $disetujui,
                'terbit' => $terbit,
                'revisi' => $revisi,
                'ditolak' => $ditolak,
            ],
            'naskahs' => $naskahs->map(function ($n) {
                $statusMap = [
                    'diterbitkan' => 'published',
                    'ditolak' => 'rejected',
                    'direvisi' => 'revision',
                    'diterima' => 'approved',
                    'perlu_direview' => 'pending',
                ];

                $latestReview = $n->reviews->sortByDesc('waktu_review')->first();

                return [
                    'id' => $n->id,
                    'judul' => $n->judul,
                    'kategori' => $n->kategori,
                    'file_path' => $n->file_path ? asset('storage/' . $n->file_path) : null,
                    'status_naskah' => $n->status_naskah,
                    'status' => $statusMap[$n->status_naskah] ?? 'pending',
                    'tahap' => ($n->status_naskah === 'diterbitkan' || $n->status_naskah === 'ditolak') ? 'selesai' : (($n->status_naskah === 'direvisi') ? 'revisi' : (($n->status_naskah === 'diterima') ? 'verifikasi' : 'pengarsipan')),
                    'keputusan' => ($n->status_naskah === 'diterima' || $n->status_naskah === 'diterbitkan') ? 'diterima' : (($n->status_naskah === 'ditolak') ? 'ditolak' : (($n->status_naskah === 'direvisi') ? 'revisi' : 'menunggu')),
                    'is_published' => $n->status_naskah === 'diterbitkan',
                    'catatan_redaksi' => $latestReview?->catatan,
                    'waktu_kirim' => $n->waktu_kirim ? $n->waktu_kirim->toDateTimeString() : null,
                    'created_at' => $n->waktu_kirim ? $n->waktu_kirim->toDateTimeString() : ($n->created_at ? $n->created_at->toDateTimeString() : null),
                    'publikasi' => $n->publikasi ? [
                        'url_berita' => $n->publikasi->url_berita,
                        'waktu_terbit' => $n->publikasi->waktu_terbit ? $n->publikasi->waktu_terbit->toDateTimeString() : null,
                    ] : null,
                    'riwayat_status' => $n->riwayatStatus->sortByDesc('waktu_status')->map(fn($r) => [
                        'id' => $r->id,
                        'status' => $r->status,
                        'catatan' => $r->catatan,
                        'waktu_status' => $r->waktu_status ? $r->waktu_status->toDateTimeString() : null,
                    ])->values(),
                ];
            }),
        ]);
    }

    public function storeNaskah(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'judul' => ['required', 'string'],
            'kategori' => ['nullable', 'string', 'max:30'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'berkas_naskah' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $kategori = !empty($validated['kategori']) ? $validated['kategori'] : 'Berita';

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('naskah_files', 'public');
        } elseif ($request->hasFile('berkas_naskah')) {
            $filePath = $request->file('berkas_naskah')->store('naskah_files', 'public');
        }

        $naskah = Naskah::create([
            'id_kontributor' => $user->id,
            'nama_kontributor' => $user->nama_lengkap,
            'judul' => $validated['judul'],
            'kategori' => $kategori,
            'file_path' => $filePath,
            'status_naskah' => 'perlu_direview',
            'waktu_kirim' => now(),
        ]);

        RiwayatStatus::create([
            'id_kontributor' => $user->id,
            'id_naskah' => $naskah->id,
            'status' => 'perlu_direview',
            'catatan' => 'Naskah diajukan oleh kontributor.',
            'waktu_status' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Naskah berhasil diajukan dan sedang menunggu review redaksi.',
            'naskah' => [
                'id' => $naskah->id,
                'judul' => $naskah->judul,
                'status_naskah' => $naskah->status_naskah,
                'status' => 'pending',
                'created_at' => $naskah->waktu_kirim->toDateTimeString(),
            ],
        ], 201);
    }
}
