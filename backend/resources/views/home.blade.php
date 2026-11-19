<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harian Cakrawala - Berita Peristiwa Indonesia Terkini</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background-color:#fff; color:#0f172a; }
        
        /* Navbar */
        .navbar { display:flex; align-items:center; justify-content:space-between; padding:1.25rem 4rem; border-bottom:1px solid #f1f5f9; }
        .logo { font-size:1.4rem; font-weight:800; color:#0d5c4d; }
        .logo span { font-size:0.75rem; display:block; color:#64748b; font-weight:500; margin-top:-3px; }
        .nav-search { display:flex; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:30px; padding:0.5rem 1.25rem; width:360px; }
        .nav-search input { border:none; background:transparent; outline:none; width:100%; font-size:0.9rem; }
        .nav-actions { display:flex; gap:0.75rem; }
        .btn-outline { padding:0.55rem 1.25rem; border:1px solid #0d5c4d; color:#0d5c4d; border-radius:6px; text-decoration:none; font-weight:600; font-size:0.9rem; }
        .btn-fill { padding:0.55rem 1.25rem; background:#0d5c4d; color:#fff; border-radius:6px; text-decoration:none; font-weight:600; font-size:0.9rem; }

        /* Kategori Bar */
        .categories { display:flex; justify-content:center; gap:2.5rem; padding:0.85rem; border-bottom:1px solid #f8fafc; font-size:0.825rem; font-weight:700; text-transform:uppercase; color:#475569; }

        /* Hero */
        .hero { background:linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%); text-align:center; padding:5rem 2rem 4rem; }
        .hero h1 { font-size:3rem; font-weight:800; color:#0f172a; letter-spacing:-1px; margin-bottom:1rem; }
        .hero p { font-size:1.15rem; color:#64748b; margin-bottom:2rem; }
        .hero-buttons { display:flex; justify-content:center; gap:1rem; }
        .btn-hero-primary { background:#0d5c4d; color:#fff; padding:0.85rem 1.75rem; border-radius:8px; text-decoration:none; font-weight:700; }
        .btn-hero-guest { background:#ffffff; color:#0f172a; border:1px solid #cbd5e1; padding:0.85rem 1.75rem; border-radius:8px; text-decoration:none; font-weight:700; }

        /* Berita Terbaru */
        .section-news { max-width:1200px; margin:0 auto; padding:4rem 2rem; }
        .section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem; }
        .section-header h2 { font-size:1.75rem; font-weight:800; }
        .section-header a { color:#0d5c4d; text-decoration:none; font-weight:700; font-size:0.95rem; }

        .news-grid { display:grid; grid-template-columns:1.5fr 1fr 1fr; gap:1.5rem; }
        .news-card-big { background:#f8fafc; border-radius:12px; height:340px; display:flex; flex-direction:column; justify-content:flex-end; padding:2rem; border:1px solid #e2e8f0; }
        .news-card-small { background:#f8fafc; border-radius:12px; height:160px; padding:1.25rem; border:1px solid #e2e8f0; display:flex; flex-direction:column; justify-content:flex-end; }
        .news-tag { font-size:0.75rem; font-weight:800; text-transform:uppercase; color:#0d5c4d; margin-bottom:0.4rem; }
        .news-title { font-size:1.1rem; font-weight:700; color:#0f172a; line-height:1.4; }

        /* Edukasi Peran */
        .roles-section { background:#f8fafc; padding:4rem 2rem; border-top:1px solid #f1f5f9; }
        .roles-container { max-width:1200px; margin:0 auto; }
        .roles-title { font-size:1.5rem; font-weight:800; margin-bottom:1.5rem; }
        .roles-grid { display:grid; grid-template-columns:repeat(3, 1fr); gap:1.5rem; }
        .role-box { background:#fff; padding:1.75rem; border-radius:10px; border:1px solid #e2e8f0; }
        .role-box h3 { font-size:1.15rem; font-weight:700; margin-bottom:0.5rem; }
        .role-box p { font-size:0.875rem; color:#64748b; line-height:1.5; }

        /* Banner CTA Bawah */
        .cta-bottom { background:#0f172a; color:#fff; padding:3.5rem 2rem; }
        .cta-container { max-width:1200px; margin:0 auto; display:flex; justify-content:space-between; align-items:center; }
        .cta-container h2 { font-size:1.75rem; font-weight:800; }
        .btn-cta { background:#fff; color:#0f172a; padding:0.75rem 1.5rem; border-radius:6px; text-decoration:none; font-weight:700; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">
            Harian Cakrawala
            <span>Berita Peristiwa Indonesia Terkini</span>
        </div>
        <div class="nav-search">
            <input type="text" placeholder="Search Text...">
        </div>
        <div class="nav-actions">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.review') }}" class="btn-fill">Dashboard Admin</a>
                @else
                    <a href="{{ route('kontributor.dashboard') }}" class="btn-fill">Dashboard Kontributor</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn-outline">Masuk</a>
                <a href="{{ route('register.kontributor') }}" class="btn-fill">Daftar</a>
            @endauth
        </div>
    </header>

    <!-- Kategori -->
    <nav class="categories">
        <span>Nasional</span>
        <span>Daerah</span>
        <span>Pendidikan</span>
        <span>Bisnis</span>
        <span>Ekonomi</span>
        <span>Teknologi</span>
        <span>Opini</span>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <h1>Selamat Datang Di Portal Harian Cakrawala</h1>
        <p>Kirimkan naskah anda dan jadi bagian dari kami</p>
        <div class="hero-buttons">
            <a href="{{ route('register.kontributor') }}" class="btn-hero-primary">Daftar sebagai Kontributor</a>
            <a href="{{ route('guest.kirim') }}" class="btn-hero-guest">Masuk sebagai Guest</a>
        </div>
    </section>

    <!-- Berita Terbaru -->
    <section class="section-news">
        <div class="section-header">
            <h2>Berita Terbaru</h2>
            <a href="#">Berita Lainnya &rarr;</a>
        </div>

        <div class="news-grid">
            @forelse($beritaTerbaru as $index => $item)
                @if($index === 0)
                    <div class="news-card-big">
                        <div class="news-tag">{{ $item->kategori }}</div>
                        <div class="news-title" style="font-size:1.5rem;">{{ $item->judul }}</div>
                        <p style="color:#64748b; font-size:0.9rem; margin-top:0.5rem;">{{ Str::limit($item->abstrak, 120) }}</p>
                    </div>
                @else
                    <div class="news-card-small">
                        <div class="news-tag">{{ $item->kategori }}</div>
                        <div class="news-title">{{ $item->judul }}</div>
                    </div>
                @endif
            @empty
                <div class="news-card-big">
                    <div class="news-tag">Redaksi</div>
                    <div class="news-title">Portal Manajemen Pengarsipan Naskah Resmi Diluncurkan</div>
                </div>
                <div class="news-card-small">
                    <div class="news-tag">Teknologi</div>
                    <div class="news-title">Digitalisasi Media Harian Cakrawala</div>
                </div>
                <div class="news-card-small">
                    <div class="news-tag">Edukasi</div>
                    <div class="news-title">Panduan Menulis Naskah Berkualitas</div>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Masuk sesuai peranmu -->
    <section class="roles-section">
        <div class="roles-container">
            <h2 class="roles-title">Masuk sesuai peranmu</h2>
            <div class="roles-grid">
                <div class="role-box">
                    <h3>Guest</h3>
                    <p>Melihat dashboard, naskah yang sudah terbit, dan mengirim naskah tanpa perlu mendaftar.</p>
                </div>
                <div class="role-box">
                    <h3>Kontributor</h3>
                    <p>Mengirim naskah masuk dan memantau status peninjauannya secara transparan.</p>
                </div>
                <div class="role-box">
                    <h3>Admin</h3>
                    <p>Meninjau naskah masuk, menerbitkan naskah layak, atau menolak dengan alasan catatan perbaikan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Bawah -->
    <div class="cta-bottom">
        <div class="cta-container">
            <h2>Siap mengirim naskah pertamamu?</h2>
            <a href="{{ route('register.kontributor') }}" class="btn-cta">Daftar Sekarang</a>
        </div>
    </div>
</body>
</html>
