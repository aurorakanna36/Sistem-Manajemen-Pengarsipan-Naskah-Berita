<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Status Akun Kontributor - Harian Cakrawala</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f5f8;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif;
            color: #1f2937;
            -webkit-text-size-adjust: 100%;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f3f5f8;
            padding: 30px 15px;
        }
        .email-card {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }
        .email-header {
            background-color: #0F6B63;
            padding: 28px 32px;
            text-align: center;
        }
        .brand-badge {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
            margin-bottom: 10px;
        }
        .header-title {
            color: #ffffff;
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .email-body {
            padding: 32px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 12px;
        }
        .lead-text {
            font-size: 15px;
            line-height: 1.6;
            color: #4b5563;
            margin-bottom: 24px;
        }
        /* Status Banner */
        .status-box {
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .status-box.accepted {
            background-color: #ECFDF5;
            border: 1px solid #A7F3D0;
            color: #065F46;
        }
        .status-box.rejected {
            background-color: #FEF2F2;
            border: 1px solid #FECACA;
            color: #991B1B;
        }
        .status-box-title {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
        }
        .status-box-desc {
            font-size: 14px;
            margin: 0;
            line-height: 1.5;
        }
        /* Credentials Box */
        .creds-container {
            background-color: #F8FAFC;
            border: 1px dashed #CBD5E1;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 24px;
        }
        .creds-row {
            margin-bottom: 10px;
            font-size: 14px;
        }
        .creds-row:last-child {
            margin-bottom: 0;
        }
        .creds-label {
            color: #64748B;
            font-weight: 500;
            display: inline-block;
            width: 130px;
        }
        .creds-val {
            color: #0F172A;
            font-weight: 700;
            font-family: monospace;
            background-color: #E2E8F0;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 13px;
        }
        .creds-note {
            color: #64748B;
            font-size: 13px;
            margin-top: 10px;
            line-height: 1.4;
        }
        /* Admin Note Box */
        .admin-note-box {
            background-color: #FFFBEB;
            border-left: 4px solid #F59E0B;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 24px;
        }
        .admin-note-title {
            font-size: 13px;
            font-weight: 700;
            color: #92400E;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .admin-note-text {
            font-size: 14px;
            color: #78350F;
            margin: 0;
            line-height: 1.5;
            white-space: pre-line;
        }
        /* CTA Button */
        .btn-wrapper {
            text-align: center;
            margin: 28px 0 10px;
        }
        .btn-primary {
            display: inline-block;
            background-color: #0F6B63;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.2px;
        }
        /* Footer */
        .email-footer {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            padding: 20px 32px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-card">
            <!-- Header -->
            <div class="email-header">
                <div class="brand-badge">Harian Cakrawala</div>
                <h1 class="header-title">Status Pendaftaran Kontributor</h1>
            </div>

            <!-- Body Content -->
            <div class="email-body">
                <div class="greeting">Halo, {{ $user->nama_lengkap }}!</div>
                
                @if($status === 'verified')
                    <div class="status-box accepted">
                        <div class="status-box-title">✓ Selamat, Pendaftaran Anda Telah Diterima!</div>
                        <p class="status-box-desc">
                            Setelah melalui proses peninjauan berkas identitas dan administrasi, Redaksi Harian Cakrawala memutuskan untuk <strong>menerima</strong> Anda sebagai Kontributor Resmi.
                        </p>
                    </div>

                    <p class="lead-text">
                        Untuk mulai mengirimkan naskah berita dan artikel ke redaksi, akun login kontributor Anda telah disiapkan dengan domain internal resmi kami:
                    </p>

                    <!-- Akun Login Baru -->
                    <div class="creds-container">
                        <div class="creds-row">
                            <span class="creds-label">Email Login:</span>
                            <span class="creds-val">{{ $emailCakrawala ?? $user->email }}</span>
                        </div>
                        <div class="creds-row" style="margin-top: 8px;">
                            <span class="creds-label">Kata Sandi:</span>
                            <span style="font-size: 13px; color: #1e293b; font-weight: 500;">
                                <em>Gunakan kata sandi yang Anda buat saat pendaftaran</em>
                            </span>
                        </div>
                        <p class="creds-note">
                            * Simpan alamat email di atas. Alamat email inilah yang Anda gunakan untuk masuk ke portal Harian Cakrawala.
                        </p>
                    </div>

                    @if(!empty($catatanAdmin))
                    <div class="admin-note-box">
                        <div class="admin-note-title">Pesan Tambahan dari Redaksi:</div>
                        <p class="admin-note-text">{{ $catatanAdmin }}</p>
                    </div>
                    @endif

                    <div class="btn-wrapper">
                        <a href="{{ config('app.url', 'http://localhost:5173') }}/login" class="btn-primary" target="_blank">
                            Masuk ke Dashboard Kontributor
                        </a>
                    </div>
                @else
                    <div class="status-box rejected">
                        <div class="status-box-title">✕ Pendaftaran Belum Dapat Diterima</div>
                        <p class="status-box-desc">
                            Terima kasih atas ketertarikan Anda untuk bergabung sebagai kontributor di Harian Cakrawala. Mohon maaf, saat ini pendaftaran akun Anda belum dapat disetujui oleh tim redaksi.
                        </p>
                    </div>

                    @if(!empty($catatanAdmin))
                    <div class="admin-note-box" style="border-left-color: #EF4444; background-color: #FEF2F2;">
                        <div class="admin-note-title" style="color: #991B1B;">Alasan / Catatan Penolakan:</div>
                        <p class="admin-note-text" style="color: #7F1D1D;">{{ $catatanAdmin }}</p>
                    </div>
                    @endif

                    <p class="lead-text" style="margin-top: 15px;">
                        Anda dapat melengkapi kembali berkas atau mendaftar ulang di masa mendatang apabila seluruh persyaratan berkas telah terpenuhi.
                    </p>
                @endif
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p style="margin: 0 0 4px 0;"><strong>Redaksi Harian Cakrawala</strong></p>
                <p style="margin: 0 0 6px 0;">Portal Media & Pengelolaan Naskah Berita Berintegritas</p>
                <p style="margin: 0; font-size: 11px; color: #9ca3af;">
                    Email ini dikirimkan secara otomatis dari sistem verifikasi pendaftaran kontributor.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
