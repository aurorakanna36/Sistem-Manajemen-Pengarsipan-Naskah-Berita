<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Naskah;
use App\Models\RiwayatStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class KontributorController extends Controller
{
    private function ensureDefaultKategoris(): void
    {
        if (Kategori::count() > 0) {
            return;
        }

        $defaults = [
            'Berita',
            'Nasional',
            'Pendidikan',
            'Teknologi',
            'Bisnis',
            'Daerah',
            'Ekonomi',
            'Kriminal',
            'Olahraga',
            'Opini',
            'Otomotif',
            'Politik',
            'Uncategorized',
        ];

        foreach ($defaults as $name) {
            Kategori::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['nama_kategori' => $name]
            );
        }
    }

    private function formatNaskah(Naskah $n): array
    {
        $statusMap = [
            'diterbitkan' => 'published',
            'ditolak' => 'rejected',
            'direvisi' => 'revision',
            'diterima' => 'approved',
            'perlu_direview' => 'pending',
        ];

        $latestReview = $n->reviews->sortByDesc('waktu_review')->first();

        $kategoriList = $n->kategoris->map(fn($k) => [
            'id' => $k->id,
            'nama_kategori' => $k->nama_kategori,
            'slug' => $k->slug,
        ])->values();

        $kategoriNames = $kategoriList->pluck('nama_kategori')->all();
        $kategoriDisplay = !empty($kategoriNames)
            ? implode(', ', $kategoriNames)
            : ($n->kategori ?: 'Uncategorized');

        return [
            'id' => $n->id,
            'judul' => $n->judul,
            'kata_kunci' => $n->kata_kunci,
            'abstrak' => $n->abstrak,
            'isi' => $n->isi,
            'kategori' => $kategoriDisplay,
            'kategoris' => $kategoriList,
            'gambar_sampul' => $n->gambar_sampul ? asset('storage/' . $n->gambar_sampul) : null,
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
                'is_read' => (bool) $r->is_read,
                'waktu_status' => $r->waktu_status ? $r->waktu_status->toDateTimeString() : null,
            ])->values(),
        ];
    }

    public function kategoris()
    {
        $this->ensureDefaultKategoris();

        $kategoris = Kategori::orderBy('id')->get()->map(fn($k) => [
            'id' => $k->id,
            'nama_kategori' => $k->nama_kategori,
            'slug' => $k->slug,
        ]);

        return response()->json([
            'kategoris' => $kategoris,
            'data' => $kategoris,
        ]);
    }

    public function dashboard()
    {
        $this->ensureDefaultKategoris();
        $user = Auth::user();

        $naskahs = Naskah::where('id_kontributor', $user->id)
            ->with(['kategoris', 'reviews.admin', 'publikasi', 'riwayatStatus'])
            ->latest('waktu_kirim')
            ->get();

        $dikirim = $naskahs->count();
        $dalamReview = $naskahs->where('status_naskah', 'perlu_direview')->count();
        $direvisi = $naskahs->where('status_naskah', 'direvisi')->count();
        $diterima = $naskahs->whereIn('status_naskah', ['diterima', 'diterbitkan'])->count();
        $ditolak = $naskahs->where('status_naskah', 'ditolak')->count();
        $dalamProses = $dalamReview + $direvisi;

        $unreadNotificationsCount = RiwayatStatus::where('id_kontributor', $user->id)
            ->whereIn('status', ['diterima', 'diterbitkan', 'ditolak', 'direvisi'])
            ->where('is_read', false)
            ->count();

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
                'total' => $dikirim,
                'dikirim' => $dikirim,
                'dalam_review' => $dalamReview,
                'draft' => $dalamReview,
                'direvisi' => $direvisi,
                'revisi' => $direvisi,
                'diterima' => $diterima,
                'disetujui' => $diterima,
                'ditolak' => $ditolak,
                'dalam_proses' => $dalamProses,
            ],
            'chart' => [
                'total' => $dikirim,
                'diterima' => $diterima,
                'ditolak' => $ditolak,
                'dalam_proses' => $dalamProses,
            ],
            'unread_notifications_count' => $unreadNotificationsCount,
            'naskahs' => $naskahs->map(fn($n) => $this->formatNaskah($n))->values(),
        ]);
    }

    public function indexNaskah()
    {
        $user = Auth::user();

        $naskahs = Naskah::where('id_kontributor', $user->id)
            ->with(['kategoris', 'reviews.admin', 'publikasi', 'riwayatStatus'])
            ->latest('waktu_kirim')
            ->get();

        return response()->json([
            'naskahs' => $naskahs->map(fn($n) => $this->formatNaskah($n))->values(),
        ]);
    }

    public function showNaskah($id)
    {
        $user = Auth::user();

        $naskah = Naskah::where('id_kontributor', $user->id)
            ->with(['kategoris', 'reviews.admin', 'publikasi', 'riwayatStatus'])
            ->findOrFail($id);

        return response()->json([
            'naskah' => $this->formatNaskah($naskah),
        ]);
    }

    public function storeNaskah(Request $request)
    {
        $this->ensureDefaultKategoris();
        $user = Auth::user();

        $validated = $request->validate([
            'judul' => ['required', 'string'],
            'kategori_ids' => ['nullable', 'array'],
            'kategori_ids.*' => ['integer', 'exists:kategori,id'],
            'kategori' => ['nullable', 'string'],
            'kata_kunci' => ['nullable', 'string', 'max:255'],
            'abstrak' => ['nullable', 'string'],
            'isi' => ['nullable', 'string'],
            'gambar_sampul' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'berkas_naskah' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

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

        $gambarSampulPath = null;
        if ($request->hasFile('gambar_sampul')) {
            $gambarSampulPath = $request->file('gambar_sampul')->store('sampul_naskah', 'public');
        }

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
            'kata_kunci' => $validated['kata_kunci'] ?? null,
            'abstrak' => $validated['abstrak'] ?? null,
            'isi' => $validated['isi'] ?? null,
            'gambar_sampul' => $gambarSampulPath,
            'kategori' => $kategoriString,
            'file_path' => $filePath,
            'status_naskah' => 'perlu_direview',
            'waktu_kirim' => now(),
        ]);

        if (!empty($kategoriIds)) {
            $naskah->kategoris()->sync($kategoriIds);
        }

        RiwayatStatus::create([
            'id_kontributor' => $user->id,
            'id_naskah' => $naskah->id,
            'status' => 'perlu_direview',
            'catatan' => 'Naskah diajukan oleh kontributor.',
            'is_read' => true,
            'waktu_status' => now(),
        ]);

        $naskah->load(['kategoris', 'reviews.admin', 'publikasi', 'riwayatStatus']);

        return response()->json([
            'status' => 'success',
            'message' => 'Naskah berhasil diajukan dan sedang menunggu review redaksi.',
            'naskah' => $this->formatNaskah($naskah),
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

    public function notifikasi()
    {
        $user = Auth::user();

        $riwayats = RiwayatStatus::where('id_kontributor', $user->id)
            ->whereIn('status', ['diterima', 'diterbitkan', 'ditolak', 'direvisi'])
            ->with(['naskah'])
            ->orderByDesc('waktu_status')
            ->get();

        $notifications = $riwayats->map(function ($r) {
            $judul = $r->naskah?->judul ?? 'Naskah';
            $status = $r->status;

            if ($status === 'diterima' || $status === 'diterbitkan') {
                $tipe = 'diterima';
                $title = "Naskah “{$judul}” diterima";
                $message = $r->catatan ?: 'Selamat, naskahmu sudah diterbitkan dan tampil di homepage.';
            } elseif ($status === 'ditolak') {
                $tipe = 'ditolak';
                $title = "Naskah “{$judul}” ditolak";
                $alasan = $r->catatan ?: 'Naskah belum sesuai kriteria redaksi.';
                $message = str_starts_with(strtolower($alasan), 'alasan:') ? $alasan : "Alasan: {$alasan}";
            } else {
                $tipe = 'direvisi';
                $title = "Naskah “{$judul}” perlu direvisi";
                $catatan = $r->catatan ?: 'Mohon perbaiki naskah sesuai arahan redaksi.';
                $message = "Catatan admin: {$catatan} Perbaiki lalu kirim ulang.";
            }

            return [
                'id' => $r->id,
                'id_naskah' => $r->id_naskah,
                'judul_naskah' => $judul,
                'status' => $status,
                'tipe' => $tipe,
                'title' => $title,
                'message' => $message,
                'catatan' => $r->catatan,
                'is_read' => (bool) $r->is_read,
                'waktu_status' => $r->waktu_status ? $r->waktu_status->toDateTimeString() : null,
            ];
        })->values();

        $unreadCount = $notifications->where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markNotifikasiRead($id)
    {
        $user = Auth::user();

        $riwayat = RiwayatStatus::where('id_kontributor', $user->id)->findOrFail($id);
        $riwayat->update(['is_read' => true]);

        $unreadCount = RiwayatStatus::where('id_kontributor', $user->id)
            ->whereIn('status', ['diterima', 'diterbitkan', 'ditolak', 'direvisi'])
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => 'success',
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAllNotifikasiRead()
    {
        $user = Auth::user();

        RiwayatStatus::where('id_kontributor', $user->id)
            ->whereIn('status', ['diterima', 'diterbitkan', 'ditolak', 'direvisi'])
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'unread_count' => 0,
        ]);
    }
}
