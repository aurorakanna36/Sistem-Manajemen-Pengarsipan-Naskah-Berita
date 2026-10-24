<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Naskah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KontributorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $naskahs = Naskah::where('author_type', get_class($user))
            ->where('author_id', $user->id)
            ->latest()
            ->get();

        $dalamReview = $naskahs->where('tahap', '!=', 'selesai')->count();
        $disetujui = $naskahs->where('keputusan', 'diterima')->count();
        $ditolak = $naskahs->where('keputusan', 'ditolak')->count();
        $revisi = $naskahs->where('tahap', 'revisi')->count();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'instansi' => $user->instansi,
            ],
            'stats' => [
                'total' => $naskahs->count(),
                'draft' => $dalamReview,
                'dalam_review' => $dalamReview,
                'disetujui' => $disetujui,
                'terbit' => $disetujui,
                'revisi' => $revisi,
                'ditolak' => $ditolak,
            ],
            'naskahs' => $naskahs->map(function ($n) {
                if ($n->is_published) {
                    $status = 'published';
                } elseif ($n->keputusan === 'ditolak') {
                    $status = 'rejected';
                } elseif ($n->tahap === 'revisi') {
                    $status = 'revision';
                } elseif ($n->keputusan === 'diterima') {
                    $status = 'approved';
                } else {
                    $status = 'pending';
                }

                return [
                    'id' => $n->id,
                    'judul' => $n->judul,
                    'kategori' => $n->kategori,
                    'tahap' => $n->tahap,
                    'keputusan' => $n->keputusan,
                    'status' => $status,
                    'is_published' => (bool) $n->is_published,
                    'catatan_redaksi' => $n->alasan_penolakan,
                    'created_at' => $n->created_at ? $n->created_at->toDateTimeString() : null,
                ];
            }),
        ]);
    }

    public function storeNaskah(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string'],
            'abstrak' => ['nullable', 'string'],
            'isi_naskah' => ['nullable', 'string'],
            'isi' => ['nullable', 'string'],
            'berkas_naskah' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $isiText = $validated['isi_naskah'] ?? $validated['isi'] ?? null;
        $kategori = !empty($validated['kategori']) ? $validated['kategori'] : 'Umum';

        $berkasPath = null;
        if ($request->hasFile('berkas_naskah')) {
            $berkasPath = $request->file('berkas_naskah')->store('naskah_files', 'public');
        } elseif ($request->hasFile('file')) {
            $berkasPath = $request->file('file')->store('naskah_files', 'public');
        }

        $naskah = Naskah::create([
            'judul' => $validated['judul'],
            'kategori' => $kategori,
            'abstrak' => $validated['abstrak'] ?? null,
            'isi_naskah' => $isiText,
            'berkas_naskah' => $berkasPath,
            'author_type' => get_class($user),
            'author_id' => $user->id,
            'tahap' => 'pengarsipan',
            'keputusan' => 'menunggu',
            'is_published' => false,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Naskah berhasil dikirim ke arsip dan akan segera diverifikasi oleh redaksi.',
            'naskah' => [
                'id' => $naskah->id,
                'judul' => $naskah->judul,
                'tahap' => $naskah->tahap,
                'keputusan' => $naskah->keputusan,
                'status' => 'pending',
                'catatan_redaksi' => null,
                'created_at' => $naskah->created_at ? $naskah->created_at->toDateTimeString() : now()->toDateTimeString(),
            ],
        ], 201);
    }
}
