<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar sebagai Kontributor - Harian Cakrawala</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem;
        }

        .register-card {
            background: #ffffff;
            width: 100%;
            max-width: 520px;
            padding: 2.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }

        .header-title {
            margin-bottom: 1.5rem;
        }

        .header-title h4 {
            font-size: 0.95rem;
            color: #0d5c4d;
            font-weight: 700;
        }

        .header-title h2 {
            font-size: 1.65rem;
            font-weight: 800;
            color: #0f172a;
            margin-top: 0.25rem;
        }

        .header-title p {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 0.2rem;
        }

        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.825rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.9rem;
            color: #0f172a;
            outline: none;
        }

        .form-control:focus {
            border-color: #0d5c4d;
            box-shadow: 0 0 0 3px rgba(13, 92, 77, 0.15);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .btn-register {
            width: 100%;
            background-color: #0d5c4d;
            color: #ffffff;
            border: none;
            padding: 0.8rem;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 0.75rem;
        }

        .btn-register:hover {
            background-color: #0a483c;
        }

        .footer-text {
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 1.25rem;
        }

        .footer-text a {
            color: #0d5c4d;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="header-title">
            <h4>Harian Cakrawala</h4>
            <h2>Daftar sebagai kontributor</h2>
            <p>Akunmu akan diverifikasi sebelum bisa mengirim naskah.</p>
        </div>

        @if($errors->any())
            <div style="background: #fef2f2; border-left: 4px solid #ef4444; color: #991b1b; padding: 0.75rem; margin-bottom: 1rem; font-size: 0.85rem; border-radius: 4px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register.kontributor') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Nama lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label>Nomor WhatsApp</label>
                <input type="text" name="phone_number" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('phone_number') }}" required>
            </div>

            <div class="form-group">
                <label>Instansi / Afiliasi</label>
                <input type="text" name="instansi" class="form-control" placeholder="Universitas / Media / Komunitas" value="{{ old('instansi') }}">
            </div>

            <div class="form-group">
                <label>Surat Administrasi / Pakta Integritas (PDF/JPG)</label>
                <input type="file" name="surat_administrasi" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Kata sandi</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Ulangi kata sandi</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <button type="submit" class="btn-register">Daftar</button>

            <div class="footer-text">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
            </div>
        </form>
    </div>
</body>
</html>
