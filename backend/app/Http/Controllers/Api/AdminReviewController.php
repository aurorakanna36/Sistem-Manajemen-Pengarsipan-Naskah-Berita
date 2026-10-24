<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Naskah;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index()
    {
        $naskahs = Naskah::with('author')->latest()->get()->map(function ($n) {
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
                'abstrak' => $n->abstrak,
                'isi' => $n->isi_naskah ?? $n->abstrak ?? '',
                'tahap' => $n->tahap,
                'keputusan' => $n->keputusan,
                'status' => $status,
                'is_published' => (bool) $n->is_published,
                'alasan_penolakan' => $n->alasan_penolakan,
                'catatan_redaksi' => $n->alasan_penolakan,
                'created_at' => $n->created_at ? $n->created_at->toDateTimeString() : null,
                'author' => [
                    'name' => $n->author?->name ?? 'Anonim',
                    'email' => $n->author?->email ?? '-',
                    'instansi' => $n->author?->instansi ?? null,
                    'type' => class_basename($n->author_type),
                ],
                'penulis' => [
                    'name' => $n->author?->name ?? 'Anonim',
                    'email' => $n->author?->email ?? '-',
                    'instansi' => $n->author?->instansi ?? null,
                    'type' => class_basename($n->author_type),
                ],
            ];
        });

        return response()->json([
            'data' => $naskahs,
            'naskahs' => $naskahs,
        ]);
    }

    public function decide(Request $request, $id)
    {
        $naskah = Naskah::findOrFail($id);

        $decision = $request->input('action') ?? $request->input('status');
        $catatan = $request->input('alasan_penolakan') ?? $request->input('catatan_redaksi');

        if (in_array($decision, ['terima', 'approved'])) {
            $naskah->update([
                'tahap' => 'verifikasi',
                'keputusan' => 'diterima',
                'is_published' => false,
                'alasan_penolakan' => $catatan,
            ]);
            $message = "Naskah '{$naskah->judul}' disetujui untuk diterbitkan.";
        } elseif ($decision === 'published') {
            $naskah->update([
                'tahap' => 'selesai',
                'keputusan' => 'diterima',
                'is_published' => true,
                'alasan_penolakan' => null,
            ]);
            $message = "Naskah '{$naskah->judul}' berhasil diterbitkan ke publik.";
        } elseif (in_array($decision, ['revision', 'revisi'])) {
            $naskah->update([
                'tahap' => 'revisi',
                'keputusan' => 'menunggu',
                'is_published' => false,
                'alasan_penolakan' => $catatan,
            ]);
            $message = "Naskah '{$naskah->judul}' diminta revisi.";
        } else {
            $naskah->update([
                'tahap' => 'selesai',
                'keputusan' => 'ditolak',
                'is_published' => false,
                'alasan_penolakan' => $catatan,
            ]);
            $message = "Naskah '{$naskah->judul}' telah ditolak.";
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }
}
