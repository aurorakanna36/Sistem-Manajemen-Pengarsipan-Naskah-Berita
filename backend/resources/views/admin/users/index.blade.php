<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kontributor - Admin Harian Cakrawala</title>
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
        .header-section { margin-bottom:1.5rem; }
        .page-title { font-size:1.75rem; font-weight:800; }
        .page-desc { font-size:0.9rem; color:#64748b; margin-top:0.25rem; }

        /* Kartu Metrik */
        .metrics-grid { display:grid; grid-template-columns:repeat(4, 1fr); gap:1.25rem; margin-bottom:2rem; }
        .metric-card { background:#fff; padding:1.25rem; border-radius:10px; border:1px solid #e2e8f0; }
        .metric-card p { font-size:0.85rem; color:#64748b; font-weight:600; margin-bottom:0.25rem; }
        .metric-card h3 { font-size:1.85rem; font-weight:800; color:#0f172a; }

        /* Panel Pencarian & Filter */
        .panel-card { background:#fff; padding:1.75rem; border-radius:10px; border:1px solid #e2e8f0; }
        .filter-bar { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
        .search-form { display:flex; gap:0.5rem; flex:1; max-width:480px; }
        .search-input { width:100%; padding:0.65rem 1rem; border:1px solid #cbd5e1; border-radius:6px; font-size:0.875rem; outline:none; }
        .search-input:focus { border-color:#0d5c4d; }
        .btn-search { background:#0d5c4d; color:#fff; border:none; padding:0.65rem 1.25rem; border-radius:6px; font-weight:600; font-size:0.875rem; cursor:pointer; }
        .filter-tabs { display:flex; gap:0.5rem; }
        .tab-link { text-decoration:none; padding:0.5rem 1rem; border-radius:6px; font-size:0.85rem; font-weight:600; color:#64748b; border:1px solid #e2e8f0; }
        .tab-link.active, .tab-link:hover { background:#0f172a; color:#fff; border-color:#0f172a; }

        /* Tabel Data */
        .table-responsive { width:100%; overflow-x:auto; }
        table { width:100%; border-collapse:collapse; text-align:left; font-size:0.875rem; }
        th { background:#f8fafc; color:#475569; font-weight:700; padding:0.85rem 1rem; border-bottom:1px solid #e2e8f0; }
        td { padding:1rem; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
        tr:hover { background:#fbfcfe; }

        .user-name { font-weight:700; color:#0f172a; }
        .user-email { color:#64748b; font-size:0.8rem; }
        .badge { display:inline-block; font-size:0.75rem; font-weight:700; padding:0.25rem 0.65rem; border-radius:20px; }
        .badge-verified { background:#dcfce7; color:#15803d; }
        .badge-pending { background:#fef9c3; color:#a16207; }
        .badge-rejected { background:#fee2e2; color:#b91c1c; }

        .btn-action-verify { background:#0d5c4d; color:#fff; border:none; padding:0.4rem 0.75rem; border-radius:4px; font-size:0.8rem; font-weight:700; cursor:pointer; }
        .btn-action-reject { background:#b91c1c; color:#fff; border:none; padding:0.4rem 0.75rem; border-radius:4px; font-size:0.8rem; font-weight:700; cursor:pointer; }
        .file-link { color:#0284c7; text-decoration:none; font-weight:600; font-size:0.8rem; }
        .file-link:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">[ADMIN CAKRAWALA]</div>
            <nav class="sidebar-menu">
                <a href="{{ route('admin.review') }}">Review Naskah</a>
                <a href="{{ route('admin.users.index') }}" class="active">Manajemen User</a>
                <a href="{{ route('home') }}" target="_blank">Lihat Portal</a>
            </nav>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">Keluar</button>
        </form>
    </aside>

    <main class="main-content">
        <div class="header-section">
            <h1 class="page-title">Manajemen Akun Kontributor</h1>
            <p class="page-desc">Kelola data pendaftaran kontributor, verifikasi akun baru, dan pantau afiliasi/kontrak penulis.</p>
        </div>

        @if(session('success'))
            <div style="background:#f0fdf4; border-left:4px solid #22c55e; color:#15803d; padding:0.85rem; margin-bottom:1.5rem; border-radius:4px; font-size:0.9rem;">
                {{ session('success') }}
            </div>
        @endif

        <!-- Metrik Ringkas -->
        <div class="metrics-grid">
            <div class="metric-card">
                <p>Total Kontributor</p>
                <h3>{{ $totalKontributor }}</h3>
            </div>
            <div class="metric-card">
                <p>Menunggu Verifikasi</p>
                <h3 style="color:#d97706;">{{ $pendingCount }}</h3>
            </div>
            <div class="metric-card">
                <p>Terverifikasi Aktif</p>
                <h3 style="color:#15803d;">{{ $verifiedCount }}</h3>
            </div>
            <div class="metric-card">
                <p>Ditolak</p>
                <h3 style="color:#b91c1c;">{{ $rejectedCount }}</h3>
            </div>
        </div>

        <!-- Tabel Kontributor -->
        <div class="panel-card">
            <div class="filter-bar">
                <form action="{{ route('admin.users.index') }}" method="GET" class="search-form">
                    <input type="text" name="search" class="search-input" placeholder="Cari nama, email, atau instansi..." value="{{ $search }}">
                    @if($status)
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <button type="submit" class="btn-search">Cari</button>
                    @if($search)
                        <a href="{{ route('admin.users.index', ['status' => $status]) }}" style="font-size:0.8rem; color:#64748b; align-self:center; text-decoration:none;">Reset</a>
                    @endif
                </form>

                <div class="filter-tabs">
                    <a href="{{ route('admin.users.index', ['search' => $search]) }}" class="tab-link {{ empty($status) ? 'active' : '' }}">Semua</a>
                    <a href="{{ route('admin.users.index', ['status' => 'pending', 'search' => $search]) }}" class="tab-link {{ ($status === 'pending') ? 'active' : '' }}">Menunggu ({{ $pendingCount }})</a>
                    <a href="{{ route('admin.users.index', ['status' => 'verified', 'search' => $search]) }}" class="tab-link {{ ($status === 'verified') ? 'active' : '' }}">Terverifikasi</a>
                    <a href="{{ route('admin.users.index', ['status' => 'rejected', 'search' => $search]) }}" class="tab-link {{ ($status === 'rejected') ? 'active' : '' }}">Ditolak</a>
                </div>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama & Email</th>
                            <th>No. WhatsApp</th>
                            <th>Instansi / Afiliasi</th>
                            <th>Berkas Administrasi</th>
                            <th>Status Akun</th>
                            <th>Tanggal Daftar</th>
                            <th style="text-align:right;">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>
                                    <div class="user-name">{{ $u->name }}</div>
                                    <div class="user-email">{{ $u->email }}</div>
                                </td>
                                <td>{{ $u->phone_number ?? '-' }}</td>
                                <td>{{ $u->instansi ?? '-' }}</td>
                                <td>
                                    @if($u->surat_administrasi)
                                        <a href="{{ asset('storage/' . $u->surat_administrasi) }}" target="_blank" class="file-link">Lihat Berkas &rarr;</a>
                                    @else
                                        <span style="color:#94a3b8; font-size:0.8rem;">Tidak ada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($u->status_verifikasi === 'verified')
                                        <span class="badge badge-verified">Terverifikasi</span>
                                    @elseif($u->status_verifikasi === 'rejected')
                                        <span class="badge badge-rejected">Ditolak</span>
                                    @else
                                        <span class="badge badge-pending">Menunggu Verifikasi</span>
                                    @endif
                                </td>
                                <td>{{ $u->created_at->format('d M Y') }}</td>
                                <td style="text-align:right;">
                                    <div style="display:inline-flex; gap:0.4rem;">
                                        @if($u->status_verifikasi !== 'verified')
                                            <form action="{{ route('admin.users.verify', $u->id) }}" method="POST" onsubmit="return confirm('Verifikasi dan aktifkan akun kontributor ini?');">
                                                @csrf
                                                <input type="hidden" name="status" value="verified">
                                                <button type="submit" class="btn-action-verify">Verifikasi</button>
                                            </form>
                                        @endif

                                        @if($u->status_verifikasi !== 'rejected')
                                            <form action="{{ route('admin.users.verify', $u->id) }}" method="POST" onsubmit="return confirm('Tolak akun kontributor ini?');">
                                                @csrf
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn-action-reject">Tolak</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center; color:#94a3b8; padding:2rem;">
                                    Tidak ada data kontributor yang sesuai dengan kriteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top:1.5rem;">
                {{ $users->links() }}
            </div>
        </div>
    </main>
</body>
</html>
