<?php

namespace App\Http\Controllers\Kontributor;

use App\Http\Controllers\Controller;
use App\Models\Naskah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KontributorDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil naskah milik kontributor yang sedang login
        $naskahs = Naskah::where('author_type', get_class($user))
            ->where('author_id', $user->id)
            ->latest()
            ->get();

        // Hitung statistik untuk 4 kartu metrik di wireframe
        $dikirimCount = $naskahs->count();
        $dalamReviewCount = $naskahs->where('tahap', 'verifikasi')->count();
        $terbitCount = $naskahs->where('tahap', 'selesai')->where('keputusan', 'diterima')->count();
        $ditolakCount = $naskahs->where('keputusan', 'ditolak')->count();

        return view('kontributor.dashboard', compact(
            'user',
            'naskahs',
            'dikirimCount',
            'dalamReviewCount',
            'terbitCount',
            'ditolakCount'
        ));
    }

    public function storeNaskah(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string'],
            'abstrak' => ['nullable', 'string'],
            'isi_naskah' => ['nullable', 'string'],
            'berkas_naskah' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
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
            'author_type' => get_class($user),
            'author_id' => $user->id,
            'tahap' => 'pengarsipan',
            'keputusan' => 'menunggu',
            'is_published' => false,
        ]);

        return back()->with('success', 'Naskah berhasil dikirim ke arsip dan akan segera diverifikasi oleh redaksi.');
    }
}
