<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Naskah;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        // Ambil daftar naskah masuk yang belum selesai / perlu ditinjau
        $naskahs = Naskah::with('author')->latest()->get();

        // Naskah yang sedang dipilih untuk ditinjau (default naskah pertama)
        $selectedId = $request->query('id', $naskahs->first()?->id);
        $selectedNaskah = $naskahs->where('id', $selectedId)->first();

        return view('admin.review', compact('naskahs', 'selectedNaskah'));
    }

    public function decide(Request $request, $id)
    {
        $naskah = Naskah::findOrFail($id);

        $request->validate([
            'action' => ['required', 'in:terima,tolak'],
            'alasan_penolakan' => ['nullable', 'string', 'required_if:action,tolak'],
        ]);

        if ($request->action === 'terima') {
            $naskah->update([
                'tahap' => 'selesai',
                'keputusan' => 'diterima',
                'is_published' => true,
                'alasan_penolakan' => null,
            ]);
            $msg = "Naskah '{$naskah->judul}' berhasil diterima dan diterbitkan!";
        } else {
            $naskah->update([
                'tahap' => 'selesai',
                'keputusan' => 'ditolak',
                'is_published' => false,
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);
            $msg = "Naskah '{$naskah->judul}' telah ditolak dengan catatan alasan penolakan.";
        }

        return redirect()->route('admin.review')->with('success', $msg);
    }
}
