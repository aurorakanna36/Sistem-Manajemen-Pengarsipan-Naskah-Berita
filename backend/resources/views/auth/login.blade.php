<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Harian Cakrawala</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        .split-container {
            display: flex;
            width: 100vw;
            min-height: 100vh;
        }

        /* Sisi Kiri (Navy Branding Cakrawala) */
        .brand-side {
            flex: 1.1;
            background: linear-gradient(135deg, #0b1a2f 0%, #152943 100%);
            color: #ffffff;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .brand-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #38bdf8;
            letter-spacing: -0.5px;
        }

        .brand-header p {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-top: 0.25rem;
        }

        .brand-body {
            max-width: 480px;
        }

        .brand-body h1 {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -1px;
            margin-bottom: 1rem;
        }

        .brand-body p {
            font-size: 1.05rem;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .brand-footer {
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Sisi Kanan (Form Input) */
        .form-side {
            flex: 1;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
        }

        .form-card {
            width: 100%;
            max-width: 420px;
        }

        .form-card h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 1.75rem;
        }

        .alert-error {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #0d5c4d;
            box-shadow: 0 0 0 3px rgba(13, 92, 77, 0.15);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.25rem;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            background-color: #0d5c4d;
            color: #ffffff;
            border: none;
            padding: 0.85rem 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            background-color: #0a483c;
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .form-footer {
            margin-top: 1.75rem;
            text-align: center;
            font-size: 0.875rem;
            color: #64748b;
        }

        .form-footer a {
            color: #0d5c4d;
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .guest-link {
            display: inline-block;
            margin-top: 0.75rem;
            font-weight: 700;
            color: #0284c7;
        }

        /* Responsif Mobile */
        @media (max-width: 900px) {
            .split-container {
                flex-direction: column;
            }
            .brand-side {
                padding: 2.5rem 2rem;
            }
            .brand-body h1 {
                font-size: 2rem;
            }
            .form-side {
                padding: 2.5rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Sisi Kiri: Branding Harian Cakrawala -->
        <div class="brand-side">
            <div class="brand-header">
                <h2>Harian Cakrawala</h2>
                <p>Berita Peristiwa Indonesia Terkini</p>
            </div>

            <div class="brand-body">
                <h1>Selamat datang kembali.</h1>
                <p>Masuk untuk mengirim naskah atau meninjau naskah yang masuk.</p>
            </div>

            <div class="brand-footer">
                <p>Media Online Harian Cakrawala &copy; {{ date('Y') }}</p>
            </div>
        </div>

        <!-- Sisi Kanan: Form Login -->
        <div class="form-side">
            <div class="form-card">
                <h2>Masuk</h2>

                @if($errors->any())
                    <div class="alert-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password">Kata sandi</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Kata sandi" required>
                    </div>

                    <button type="submit" class="btn-submit">Masuk</button>

                    <div class="form-footer">
                        <p>Belum punya akun? <a href="{{ route('register.kontributor') }}">Daftar sebagai kontributor</a></p>
                        <p><a href="{{ route('guest.kirim') }}" class="guest-link">Masuk Sebagai Guest</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
