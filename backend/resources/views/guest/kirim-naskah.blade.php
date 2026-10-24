<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Naskah Tamu (Guest) - Harian Cakrawala</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Plus Jakarta Sans', sans-serif; }
        body { background-color:#f1f5f9; min-height:100vh; padding:3rem 1rem; display:flex; justify-content:center; }
        .card { background:#fff; width:100%; max-width:680px; padding:2.5rem; border-radius:12px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.05); }
        .card h2 { font-size:1.6rem; font-weight:800; color:#0f172a; margin-bottom:0.4rem; }
        .card p { color:#64748b; font-size:0.9rem; margin-bottom:1.75rem; }
        .section-sub { font-size:1rem; font-weight:700; color:#0d5c4d; border-bottom:2px solid #e2e8f0; padding-bottom:0.5rem; margin:1.5rem 0 1rem; }
        .form-group { margin-bottom:1.1rem; }
        .form-group label { display:block; font-size:0.85rem; font-weight:600; color:#334155; margin-bottom:0.35rem; }
        .form-control { width:100%; padding:0.75rem; border:1px solid #cbd5e1; border-radius:6px; font-size:0.9rem; outline:none; }
        .form-control:focus { border-color:#0d5c4d; }
        .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .btn-submit { width:100%; background:#0d5c4d; color:#fff; border:none; padding:0.85rem; border-radius:6px; font-size:1rem; font-weight:700; cursor:pointer; margin-top:1rem; }
        .btn-submit:hover { background:#0a483c; }
        .btn-back { display:inline-block; margin-bottom:1.5rem; color:#64748b; text-decoration:none; font-size:0.85rem; font-weight:600; }
    </style>
</head>
<body>
    <div class="card">
        <a href="{{ route('home') }}" class="btn-back">&larr; Kembali ke Beranda</a>
        <h2>Kirim Naskah sebagai Guest</h2>
        <p>Anda dapat mengirimkan naskah tanpa mendaftar akun. Notifikasi status verifikasi naskah akan dikirimkan otomatis melalui Gmail Anda.</p>

        @if($errors->any())
            <div style="background:#fef2f2; border-left:4px solid #ef4444; color:#991b1b; padding:0.75rem; margin-bottom:1rem; font-size:0.85rem; border-radius:4px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('guest.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="section-sub">1. Data Diri Penulis Tamu</div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label>Alamat Email (Gmail)</label>
                    <input type="email" name="email" class="form-control" placeholder="nama@gmail.com" value="{{ old('email') }}" required>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}">
                </div>
                <div class="form-group">
                    <label>Instansi / Umum</label>
                    <input type="text" name="instansi" class="form-control" placeholder="Universitas / Umum" value="{{ old('instansi') }}">
                </div>
            </div>

            <div class="section-sub">2. Konten Naskah Berita</div>
            <div class="form-group">
                <label>Judul Naskah</label>
                <input type="text" name="judul" class="form-control" value="{{ old('judul') }}" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" class="form-control" required>
                    <option value="Berita">Berita</option>
                    <option value="Opini">Opini</option>
                    <option value="Pendidikan">Pendidikan</option>
                    <option value="Teknologi">Teknologi</option>
                    <option value="Ekonomi">Ekonomi</option>
                </select>
            </div>
            <div class="form-group">
                <label>Abstrak / Ringkasan Singkat</label>
                <textarea name="abstrak" rows="3" class="form-control">{{ old('abstrak') }}</textarea>
            </div>
            <div class="form-group">
                <label>Isi Naskah Lengkap</label>
                <textarea name="isi_naskah" rows="6" class="form-control">{{ old('isi_naskah') }}</textarea>
            </div>
            <div class="form-group">
                <label>Lampiran Berkas Naskah (PDF/DOCX - Opsional)</label>
                <input type="file" name="berkas_naskah" class="form-control" accept=".pdf,.doc,.docx">
            </div>

            <button type="submit" class="btn-submit">Kirim Naskah ke Arsip</button>
        </form>
    </div>
</body>
</html>
