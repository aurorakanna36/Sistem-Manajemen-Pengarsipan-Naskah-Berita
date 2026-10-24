<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kontributor - Harian Cakrawala</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background-color:#f8fafc; min-height:100vh; display:flex; color:#0f172a; }

        /* Sidebar Kiri */
        .sidebar { width:240px; background:#0f172a; color:#fff; padding:2rem 1.5rem; display:flex; flex-direction:column; justify-content:space-between; flex-shrink:0; }
        .sidebar-brand { font-size:1.1rem; font-weight:800; color:#38bdf8; margin-bottom:2.5rem; }
        .sidebar-menu a { display:flex; align-items:center; color:#94a3b8; text-decoration:none; padding:0.75rem 1rem; border-radius:6px; font-weight:600; font-size:0.9rem; margin-bottom:0.5rem; }
        .sidebar-menu a.active, .sidebar-menu a:hover { background:#1e293b; color:#fff; }
        .btn-logout { background:transparent; border:none; color:#ef4444; font-weight:600; cursor:pointer; padding:0.75rem 1rem; width:100%; text-align:left; font-size:0.9rem; }

        /* Konten Utama */
        .main-content { flex:1; padding:2.5rem 3rem; overflow-y:auto; }
        .greeting { font-size:1.75rem; font-weight:800; margin-bottom:1.5rem; }

        /* 4 Kartu Metrik di Wireframe */
        .metrics-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:1.25rem; margin-bottom:2rem; }
        .metric-card { background:#fff; padding:1.25rem; border-radius:10px; border:1px solid #e2e8f0; }
        .metric-card p { font-size:0.85rem; color:#64748b; font-weight:600; margin-bottom:0.25rem; }
        .metric-card h3 { font-size:2rem; font-weight:800; color:#0f172a; }

        /* Layout Form Kirim & List Status Naskah */
        .workspace-grid { display:grid; grid-template-columns:1.4fr 1fr; gap:1.75rem; }
        .panel-card { background:#fff; padding:1.75rem; border-radius:10px; border:1px solid #e2e8f0; }
        .panel-title { font-size:1.15rem; font-weight:800; margin-bottom:1.25rem; }

        .form-group { margin-bottom:1rem; }
        .form-group label { display:block; font-size:0.825rem; font-weight:600; color:#334155; margin-bottom:0.35rem; }
        .form-control { width:100%; padding:0.7rem; border:1px solid #cbd5e1; border-radius:6px; font-size:0.875rem; outline:none; }
        .form-control:focus { border-color:#0d5c4d; }
        .btn-kirim { background:#0d5c4d; color:#fff; border:none; padding:0.75rem 1.5rem; border-radius:6px; font-weight:700; cursor:pointer; }
        .btn-kirim:hover { background:#0a483c; }

        /* Status List */
        .status-item { display:flex; justify-content:space-between; align-items:center; padding:0.85rem 0; border-bottom:1px solid #f1f5f9; }
        .status-item:last-child { border-bottom:none; }
        .status-title { font-size:0.875rem; font-weight:600; color:#0f172a; max-width:240px; }
        .badge { font-size:0.75rem; font-weight:700; padding:0.3rem 0.65rem; border-radius:20px; }
        .badge-terbit { background:#dcfce7; color:#15803d; }
        .badge-review { background:#fef9c3; color:#a16207; }
        .badge-ditolak { background:#fee2e2; color:#b91c1c; }
        .badge-arsip { background:#e0f2fe; color:#0369a1; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">[HARIAN CAKRAWALA]</div>
            <nav class="sidebar-menu">
                <a href="{{ route('kontributor.dashboard') }}" class="active">Dashboard</a>
                <a href="#form-naskah">Kirim Naskah</a>
                <a href="{{ route('home') }}" target="_blank">Lihat Portal</a>
            </nav>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">Keluar</button>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <h1 class="greeting">Halo, {{ $user->name }}</h1>

        @if(session('success'))
            <div style="background:#f0fdf4; border-left:4px solid #22c55e; color:#15803d; padding:0.85rem; margin-bottom:1.5rem; border-radius:4px; font-size:0.9rem;">
                {{ session('success') }}
            </div>
        @endif

        <!-- 4 Kartu Metrik di Wireframe -->
        <div class="metrics-grid">
            <div class="metric-card">
                <p>Dikirim</p>
                <h3>[{{ $dikirimCount }}]</h3>
            </div>
            <div class="metric-card">
                <p>Dalam review</p>
                <h3>[{{ $dalamReviewCount }}]</h3>
            </div>
            <div class="metric-card">
                <p>Terbit</p>
                <h3>[{{ $terbitCount }}]</h3>
            </div>
            <div class="metric-card">
                <p>Ditolak</p>
                <h3>[{{ $ditolakCount }}]</h3>
            </div>
        </div>

        <!-- Ruang Kerja -->
        <div class="workspace-grid">
            <!-- Form Kirim Naskah -->
            <div class="panel-card" id="form-naskah">
                <h2 class="panel-title">Kirim naskah</h2>
                <form action="{{ route('kontributor.naskah.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Judul naskah</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control" required>
                            <option value="Berita">[Berita]</option>
                            <option value="Opini">[Opini]</option>
                            <option value="Pendidikan">[Pendidikan]</option>
                            <option value="Teknologi">[Teknologi]</option>
                            <option value="Ekonomi">[Ekonomi]</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Abstrak</label>
                        <textarea name="abstrak" rows="3" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Isi Naskah</label>
                        <textarea name="isi_naskah" rows="4" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Berkas naskah</label>
                        <input type="file" name="berkas_naskah" class="form-control" accept=".pdf,.doc,.docx">
                    </div>
                    <button type="submit" class="btn-kirim">Kirim naskah</button>
                </form>
            </div>

            <!-- List Status Naskah Saya -->
            <div class="panel-card">
                <h2 class="panel-title">Status naskah saya</h2>
                @forelse($naskahs as $n)
                    <div class="status-item">
                        <div>
                            <div class="status-title">{{ $n->judul }}</div>
                            <small style="color:#64748b;">{{ $n->created_at->format('d M Y') }}</small>
                        </div>
                        <div>
                            @if($n->keputusan === 'diterima')
                                <span class="badge badge-terbit">Terbit</span>
                            @elseif($n->keputusan === 'ditolak')
                                <span class="badge badge-ditolak" title="{{ $n->alasan_penolakan }}">Ditolak</span>
                            @elseif($n->tahap === 'verifikasi')
                                <span class="badge badge-review">Dalam review</span>
                            @else
                                <span class="badge badge-arsip">Pengarsipan</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <p style="color:#94a3b8; font-size:0.9rem;">Belum ada naskah yang dikirimkan.</p>
                @endforelse
            </div>
        </div>
    </main>
</body>
</html>
