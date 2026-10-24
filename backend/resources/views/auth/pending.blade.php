<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Verifikasi - Harian Cakrawala</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background-color:#f8fafc; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1.5rem; }
        .card { background:#fff; border-radius:12px; padding:3rem 2rem; max-width:480px; text-align:center; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); }
        .icon { width:64px; height:64px; background:#fef3c7; color:#d97706; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; font-size:1.75rem; font-weight:800; }
        h2 { font-size:1.5rem; color:#0f172a; margin-bottom:0.75rem; font-weight:800; }
        p { color:#64748b; font-size:0.95rem; line-height:1.6; margin-bottom:1.75rem; }
        .btn-home { display:inline-block; background:#0d5c4d; color:#fff; text-decoration:none; padding:0.75rem 1.5rem; border-radius:6px; font-weight:600; font-size:0.9rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⏳</div>
        <h2>Pendaftaran Sedang Diverifikasi</h2>
        <p>Terima kasih telah mendaftar sebagai kontributor Harian Cakrawala. Berkas dan akun Anda saat ini sedang diperiksa oleh tim redaksi. Anda akan menerima notifikasi begitu verifikasi disetujui.</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-home" style="border:none; cursor:pointer;">Keluar / Kembali ke Beranda</button>
        </form>
    </div>
</body>
</html>
