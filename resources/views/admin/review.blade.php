<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Naskah - Admin Redaksi Harian Cakrawala</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background-color:#f8fafc; min-height:100vh; display:flex; color:#0f172a; }

        .sidebar { width:240px; background:#0f172a; color:#fff; padding:2rem 1.5rem; display:flex; flex-direction:column; justify-content:space-between; flex-shrink:0; }
        .sidebar-brand { font-size:1.1rem; font-weight:800; color:#38bdf8; margin-bottom:2.5rem; }
        .sidebar-menu a { display:flex; align-items:center; color:#94a3b8; text-decoration:none; padding:0.75rem 1rem; border-radius:6px; font-weight:600; font-size:0.9rem; margin-bottom:0.5rem; }
        .sidebar-menu a.active, .sidebar-menu a:hover { background:#1e293b; color:#fff; }
        .btn-logout { background:transparent; border:none; color:#ef4444; font-weight:600; cursor:pointer; padding:0.75rem 1rem; width:100%; text-align:left; font-size:0.9rem; }

        .main-content { flex:1; padding:2.5rem 3rem; overflow-y:auto; }
        .page-title { font-size:1.75rem; font-weight:800; margin-bottom:1.5rem; }

        .review-layout { display:grid; grid-template-columns:1fr 1.6fr; gap:1.75rem; }
        .panel-card { background:#fff; padding:1.75rem; border-radius:10px; border:1px solid #e2e8f0; }
        .panel-header { font-size:1.15rem; font-weight:800; margin-bottom:1rem; }

        /* List Naskah Masuk */
        .naskah-item { display:flex; justify-content:space-between; align-items:center; padding:0.85rem 0.5rem; border-bottom:1px solid #f1f5f9; cursor:pointer; }
        .naskah-item:hover, .naskah-item.selected { background:#f8fafc; }
        .naskah-name { font-size:0.9rem; font-weight:700; color:#0f172a; }
        .naskah-author { font-size:0.8rem; color:#64748b; margin-top:2px; }
        .btn-link-action { font-size:0.825rem; font-weight:700; text-decoration:none; padding:0.35rem 0.75rem; border-radius:4px; }
        .btn-selected { background:#0d5c4d; color:#fff; }
        .btn-open { background:#e2e8f0; color:#475569; }

        /* Detail Tinjauan */
        .preview-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:1.25rem; min-height:160px; margin-bottom:1.25rem; font-size:0.925rem; line-height:1.6; color:#334155; }
        .reason-group { margin-bottom:1.25rem; }
        .reason-group label { display:block; font-size:0.85rem; font-weight:700; margin-bottom:0.4rem; }
        .reason-control { width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:0.9rem; outline:none; }

        .action-buttons { display:flex; gap:1rem; }
        .btn-accept { flex:1; background:#0d5c4d; color:#fff; border:none; padding:0.85rem; border-radius:6px; font-weight:700; cursor:pointer; }
        .btn-reject { flex:1; background:#b91c1c; color:#fff; border:none; padding:0.85rem; border-radius:6px; font-weight:700; cursor:pointer; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">[ADMIN CAKRAWALA]</div>
            <nav class="sidebar-menu">
                <a href="#">Dashboard Admin</a>
                <a href="{{ route('admin.review') }}" class="active">Review Naskah</a>
                <a href="{{ route('home') }}" target="_blank">Lihat Portal</a>
            </nav>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">Keluar</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Review naskah</h1>

        @if(session('success'))
            <div style="background:#f0fdf4; border-left:4px solid #22c55e; color:#15803d; padding:0.85rem; margin-bottom:1.5rem; border-radius:4px; font-size:0.9rem;">
                {{ session('success') }}
            </div>
        @endif

        <div class="review-layout">
            <!-- Kolom Kiri: Naskah Masuk -->
            <div class="panel-card">
                <h2 class="panel-header">Naskah masuk</h2>
                @forelse($naskahs as $item)
                    <div class="naskah-item {{ ($selectedNaskah && $selectedNaskah->id == $item->id) ? 'selected' : '' }}">
                        <div>
                            <div class="naskah-name">{{ $item->judul }}</div>
                            <div class="naskah-author">[{{ $item->author?->name ?? 'Guest/Tamu' }}] &bull; Status: {{ ucfirst($item->tahap) }}</div>
                        </div>
                        <div>
                            @if($selectedNaskah && $selectedNaskah->id == $item->id)
                                <span class="btn-link-action btn-selected">Dipilih</span>
                            @else
                                <a href="{{ route('admin.review', ['id' => $item->id]) }}" class="btn-link-action btn-open">Buka</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:0.9rem;">Tidak ada naskah dalam antrean.</p>
                @endforelse
            </div>

            <!-- Kolom Kanan: Detail & Aksi Redaksi -->
            <div class="panel-card">
                @if($selectedNaskah)
                    <h2 class="panel-header">[{{ $selectedNaskah->judul }}]</h2>
                    <p style="font-size:0.85rem; color:#64748b; margin-bottom:1rem;">
                        Penulis: <strong>{{ $selectedNaskah->author?->name ?? 'Penulis Tamu' }}</strong> &bull; Kategori: <strong>{{ $selectedNaskah->kategori }}</strong>
                    </p>

                    <div class="preview-box">
                        <strong>Abstrak dan Pratinjau Naskah:</strong><br>
                        {{ $selectedNaskah->abstrak ?? 'Tidak ada abstrak.' }}
                        <br><br>
                        <strong>Isi Naskah:</strong><br>
                        {{ $selectedNaskah->isi_naskah ?? 'Isi naskah terlampir dalam dokumen berkas.' }}
                    </div>

                    <form action="{{ route('admin.decide', $selectedNaskah->id) }}" method="POST">
                        @csrf
                        <div class="reason-group">
                            <label>Alasan penolakan (wajib jika ditolak)</label>
                            <textarea name="alasan_penolakan" rows="3" class="reason-control" placeholder="Tuliskan catatan revisi atau alasan naskah ditolak..."></textarea>
                        </div>

                        <div class="action-buttons">
                            <button type="submit" name="action" value="terima" class="btn-accept">Terima dan terbitkan</button>
                            <button type="submit" name="action" value="tolak" class="btn-reject">Tolak naskah</button>
                        </div>
                    </form>
                @else
                    <p style="color:#94a3b8; font-size:0.9rem;">Silakan pilih naskah di sebelah kiri untuk mulai meninjau.</p>
                @endif
            </div>
        </div>
    </main>
</body>
</html>
