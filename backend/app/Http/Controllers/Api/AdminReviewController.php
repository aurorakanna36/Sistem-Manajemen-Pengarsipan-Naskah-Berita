<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Naskah;
use App\Models\Publikasi;
use App\Models\Review;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReviewController extends Controller
{
    /**
     * Dashboard stats: total naskah, kontributor, perlu direview,
     * list naskah menunggu review, list kontributor menunggu verifikasi.
     */
    public function dashboard()
    {
        $totalNaskah       = Naskah::count();
        $totalKontributor  = User::where('role', 'kontributor')->count();
        $perluDireview     = Naskah::where('status_naskah', 'perlu_direview')->count();

        $naskahMenunggu = Naskah::where('status_naskah', 'perlu_direview')
            ->latest('waktu_kirim')
            ->limit(5)
            ->get()
            ->map(fn($n) => [
                'id'               => $n->id,
                'judul'            => $n->judul,
                'nama_kontributor' => $n->nama_kontributor ?? $n->nama_guest ?? 'Anonim',
                'waktu_kirim'      => $n->waktu_kirim ? $n->waktu_kirim->toDateTimeString() : null,
                'kategori'         => $n->kategori,
            ]);

        $kontributorMenunggu = User::where('role', 'kontributor')
            ->where('status_verifikasi', 'pending')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($u) => [
                'id'          => $u->id,
                'nama_lengkap'=> $u->nama_lengkap,
                'email'       => $u->email,
                'instansi'    => $u->instansi,
                'created_at'  => $u->created_at ? $u->created_at->toDateTimeString() : null,
            ]);

        return response()->json([
            'stats' => [
                'total_naskah'      => $totalNaskah,
                'total_kontributor' => $totalKontributor,
                'perlu_direview'    => $perluDireview,
            ],
            'naskah_menunggu_review'       => $naskahMenunggu,
            'kontributor_perlu_verifikasi' => $kontributorMenunggu,
        ]);
    }

    /**
     * Detail satu naskah untuk halaman review.
     */
    public function show($id)
    {
        $n = Naskah::with(['kontributor', 'guest', 'reviews.admin', 'publikasi', 'riwayatStatus'])
            ->findOrFail($id);

        $statusMap = [
            'diterbitkan'   => 'published',
            'ditolak'       => 'rejected',
            'direvisi'      => 'revision',
            'diterima'      => 'approved',
            'perlu_direview'=> 'pending',
        ];

        $penulisNama    = $n->nama_kontributor ?? $n->kontributor?->nama_lengkap ?? $n->nama_guest ?? $n->guest?->nama ?? 'Anonim';
        $penulisEmail   = $n->kontributor?->email ?? $n->guest?->email ?? '-';
        $penulisInstansi= $n->kontributor?->instansi ?? $n->guest?->instansi ?? null;
        $penulisType    = $n->id_kontributor ? 'Kontributor' : 'Guest';
        $penulisKontak  = $n->kontributor?->nomor_whatsapp ?? $n->guest?->nomot_Whatsapp ?? null;

        $latestReview   = $n->reviews->sortByDesc('waktu_review')->first();

        // Statistik penulis
        $authorTotalNaskah   = 0;
        $authorDiterimaCount = 0;
        if ($n->id_kontributor) {
            $authorTotalNaskah   = Naskah::where('id_kontributor', $n->id_kontributor)->count();
            $authorDiterimaCount = Naskah::where('id_kontributor', $n->id_kontributor)
                ->whereIn('status_naskah', ['diterima', 'diterbitkan'])
                ->count();
        }

        return response()->json([
            'naskah' => [
                'id'                  => $n->id,
                'judul'               => $n->judul,
                'kategori'            => $n->kategori,
                'file_path'           => $n->file_path ? asset('storage/' . $n->file_path) : null,
                'status_naskah'       => $n->status_naskah,
                'status'              => $statusMap[$n->status_naskah] ?? 'pending',
                'waktu_kirim'         => $n->waktu_kirim ? $n->waktu_kirim->toDateTimeString() : null,
                'created_at'          => $n->waktu_kirim ? $n->waktu_kirim->toDateTimeString() : ($n->created_at ? $n->created_at->toDateTimeString() : null),
                'id_kontributor'      => $n->id_kontributor,
                'nama_kontributor'    => $penulisNama,
                'catatan_redaksi'     => $latestReview?->catatan,
                'alasan_penolakan'    => $latestReview?->catatan,
                'author_total_naskah' => $authorTotalNaskah,
                'author_diterima_naskah' => $authorDiterimaCount,
                'penulis' => [
                    'name'     => $penulisNama,
                    'email'    => $penulisEmail,
                    'instansi' => $penulisInstansi,
                    'kontak'   => $penulisKontak,
                    'type'     => $penulisType,
                ],
                'author' => [
                    'name'     => $penulisNama,
                    'email'    => $penulisEmail,
                    'instansi' => $penulisInstansi,
                    'kontak'   => $penulisKontak,
                    'type'     => $penulisType,
                ],
                'reviews' => $n->reviews->sortByDesc('waktu_review')->map(fn($r) => [
                    'id'           => $r->id,
                    'admin_name'   => $r->admin?->nama_lengkap ?? 'Admin',
                    'keputusan'    => $r->keputusan,
                    'catatan'      => $r->catatan,
                    'waktu_review' => $r->waktu_review ? $r->waktu_review->toDateTimeString() : null,
                ])->values(),
                'publikasi' => $n->publikasi ? [
                    'id'          => $n->publikasi->id,
                    'url_berita'  => $n->publikasi->url_berita,
                    'waktu_terbit'=> $n->publikasi->waktu_terbit ? $n->publikasi->waktu_terbit->toDateTimeString() : null,
                ] : null,
            ],
            'review' => $latestReview ? [
                'id'           => $latestReview->id,
                'catatan'      => $latestReview->catatan,
                'keputusan'    => $latestReview->keputusan,
                'waktu_review' => $latestReview->waktu_review ? $latestReview->waktu_review->toDateTimeString() : null,
            ] : null,
        ]);
    }

    public function index()
    {
        $naskahs = Naskah::with(['kontributor', 'guest', 'reviews.admin', 'publikasi', 'riwayatStatus'])
            ->latest('waktu_kirim')
            ->get()
            ->map(function ($n) {
                $statusMap = [
                    'diterbitkan' => 'published',
                    'ditolak' => 'rejected',
                    'direvisi' => 'revision',
                    'diterima' => 'approved',
                    'perlu_direview' => 'pending',
                ];

                $penulisNama = $n->nama_kontributor
                    ?? $n->kontributor?->nama_lengkap
                    ?? $n->nama_guest
                    ?? $n->guest?->nama
                    ?? 'Anonim';

                $penulisEmail = $n->kontributor?->email ?? $n->guest?->email ?? '-';
                $penulisInstansi = $n->kontributor?->instansi ?? $n->guest?->instansi ?? null;
                $penulisType = $n->id_kontributor ? 'Kontributor' : 'Guest';
                $penulisKontak = $n->kontributor?->nomor_whatsapp ?? $n->guest?->nomot_Whatsapp ?? null;

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
                    'alasan_penolakan' => $latestReview?->catatan,
                    'waktu_kirim' => $n->waktu_kirim ? $n->waktu_kirim->toDateTimeString() : null,
                    'created_at' => $n->waktu_kirim ? $n->waktu_kirim->toDateTimeString() : ($n->created_at ? $n->created_at->toDateTimeString() : null),
                    'author' => [
                        'name' => $penulisNama,
                        'email' => $penulisEmail,
                        'instansi' => $penulisInstansi,
                        'kontak' => $penulisKontak,
                        'type' => $penulisType,
                    ],
                    'penulis' => [
                        'name' => $penulisNama,
                        'email' => $penulisEmail,
                        'instansi' => $penulisInstansi,
                        'kontak' => $penulisKontak,
                        'type' => $penulisType,
                    ],
                    'reviews' => $n->reviews->sortByDesc('waktu_review')->map(fn($r) => [
                        'id' => $r->id,
                        'admin_name' => $r->admin?->nama_lengkap ?? 'Admin',
                        'keputusan' => $r->keputusan,
                        'catatan' => $r->catatan,
                        'waktu_review' => $r->waktu_review ? $r->waktu_review->toDateTimeString() : null,
                    ])->values(),
                    'publikasi' => $n->publikasi ? [
                        'id' => $n->publikasi->id,
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
            });

        return response()->json([
            'data' => $naskahs,
            'naskahs' => $naskahs,
        ]);
    }

    public function decide(Request $request, $id)
    {
        $naskah = Naskah::findOrFail($id);
        $admin = Auth::user();

        $action = $request->input('action') ?? $request->input('status');
        $catatan = $request->input('catatan') ?? $request->input('alasan_penolakan') ?? $request->input('catatan_redaksi');

        if (in_array($action, ['terima', 'approved', 'diterima'])) {
            $naskah->update(['status_naskah' => 'diterima']);

            Review::create([
                'id_admin' => $admin->id,
                'id_naskah' => $naskah->id,
                'keputusan' => 'diterima',
                'catatan' => $catatan,
                'waktu_review' => now(),
            ]);

            RiwayatStatus::create([
                'id_kontributor' => $naskah->id_kontributor,
                'id_naskah' => $naskah->id,
                'status' => 'diterima',
                'catatan' => $catatan ?? 'Naskah disetujui oleh redaksi.',
                'waktu_status' => now(),
            ]);

            $message = "Naskah '{$naskah->judul}' disetujui.";
        } elseif (in_array($action, ['published', 'diterbitkan'])) {
            $urlBerita = $request->input('url_berita');

            $naskah->update(['status_naskah' => 'diterbitkan']);

            Publikasi::updateOrCreate(
                ['id_naskah' => $naskah->id],
                [
                    'id_admin' => $admin->id,
                    'url_berita' => $urlBerita,
                    'waktu_terbit' => now(),
                ]
            );

            RiwayatStatus::create([
                'id_kontributor' => $naskah->id_kontributor,
                'id_naskah' => $naskah->id,
                'status' => 'diterbitkan',
                'catatan' => 'Naskah diterbitkan ke publik.',
                'waktu_status' => now(),
            ]);

            $message = "Naskah '{$naskah->judul}' berhasil diterbitkan.";
        } elseif (in_array($action, ['revision', 'revisi', 'direvisi'])) {
            $naskah->update(['status_naskah' => 'direvisi']);

            Review::create([
                'id_admin' => $admin->id,
                'id_naskah' => $naskah->id,
                'keputusan' => 'direvisi',
                'catatan' => $catatan,
                'waktu_review' => now(),
            ]);

            RiwayatStatus::create([
                'id_kontributor' => $naskah->id_kontributor,
                'id_naskah' => $naskah->id,
                'status' => 'direvisi',
                'catatan' => $catatan ?? 'Naskah memerlukan revisi.',
                'waktu_status' => now(),
            ]);

            $message = "Naskah '{$naskah->judul}' diminta revisi.";
        } else {
            $naskah->update(['status_naskah' => 'ditolak']);

            Review::create([
                'id_admin' => $admin->id,
                'id_naskah' => $naskah->id,
                'keputusan' => 'ditolak',
                'catatan' => $catatan,
                'waktu_review' => now(),
            ]);

            RiwayatStatus::create([
                'id_kontributor' => $naskah->id_kontributor,
                'id_naskah' => $naskah->id,
                'status' => 'ditolak',
                'catatan' => $catatan ?? 'Naskah ditolak oleh redaksi.',
                'waktu_status' => now(),
            ]);

            $message = "Naskah '{$naskah->judul}' telah ditolak.";
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'status_naskah' => $naskah->status_naskah,
        ]);
    }

    public function publish(Request $request, $id)
    {
        $naskah = Naskah::findOrFail($id);
        $admin = Auth::user();

        $request->validate([
            'url_berita' => ['nullable', 'string', 'url'],
            'waktu_terbit' => ['nullable', 'date'],
        ]);

        $naskah->update(['status_naskah' => 'diterbitkan']);

        $publikasi = Publikasi::updateOrCreate(
            ['id_naskah' => $naskah->id],
            [
                'id_admin' => $admin->id,
                'url_berita' => $request->input('url_berita'),
                'waktu_terbit' => $request->input('waktu_terbit') ?? now(),
            ]
        );

        RiwayatStatus::create([
            'id_kontributor' => $naskah->id_kontributor,
            'id_naskah' => $naskah->id,
            'status' => 'diterbitkan',
            'catatan' => 'Naskah resmi diterbitkan: ' . ($request->input('url_berita') ?? 'Portal Berita'),
            'waktu_status' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Naskah '{$naskah->judul}' berhasil diterbitkan.",
            'publikasi' => $publikasi,
        ]);
    }
}
