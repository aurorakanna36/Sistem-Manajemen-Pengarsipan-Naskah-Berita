<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Status Naskah - Harian Cakrawala</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #334155;
            line-height: 1.6;
        }
        .wrapper {
            width: 100%;
            background-color: #f1f5f9;
            padding: 40px 16px;
            box-sizing: border-box;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #0d5c4d 0%, #1e5f51 100%);
            padding: 32px 28px;
            text-align: center;
            color: #ffffff;
        }
        .brand-logo {
            display: inline-block;
            background: #ffffff;
            color: #0d5c4d;
            font-weight: 800;
            font-size: 22px;
            width: 46px;
            height: 46px;
            line-height: 46px;
            border-radius: 10px;
            margin-bottom: 12px;
        }
        .header h1 {
            margin: 0 0 6px;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .header p {
            margin: 0;
            font-size: 13px;
            opacity: 0.9;
        }
        .body {
            padding: 32px 28px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .status-card {
            border-radius: 10px;
            padding: 18px 20px;
            margin: 20px 0;
            border-left: 5px solid #0d5c4d;
            background-color: #f8fafc;
        }
        .status-card.diterima,
        .status-card.diterbitkan {
            border-left-color: #10b981;
            background-color: #ecfdf5;
        }
        .status-card.direvisi {
            border-left-color: #f59e0b;
            background-color: #fffbeb;
        }
        .status-card.ditolak {
            border-left-color: #ef4444;
            background-color: #fef2f2;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .status-badge.diterima,
        .status-badge.diterbitkan {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-badge.direvisi {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-badge.ditolak {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .naskah-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 6px 0 2px;
            line-height: 1.4;
        }
        .naskah-meta {
            font-size: 12px;
            color: #64748b;
        }
        .note-box {
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 14px 16px;
            margin-top: 14px;
        }
        .note-title {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            margin-bottom: 4px;
        }
        .note-text {
            font-size: 14px;
            color: #1e293b;
            white-space: pre-line;
            margin: 0;
        }
        .cta-container {
            text-align: center;
            margin: 28px 0 10px;
        }
        .btn-cta {
            display: inline-block;
            background-color: #0d5c4d;
            color: #ffffff !important;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }
        .btn-cta:hover {
            background-color: #094338;
        }
        .footer {
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 22px 28px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
        .footer p {
            margin: 4px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <div class="brand-logo">HC</div>
                <h1>Harian Cakrawala</h1>
                <p>Pemberitahuan Status Editorial Naskah Tamu</p>
            </div>

            <!-- Body -->
            <div class="body">
                <p class="greeting">Halo, {{ $guest->nama ?? 'Pengirim Naskah' }}!</p>
                <p>
                    Terima kasih telah berkontribusi mengirimkan naskah berita ke <strong>Portal Harian Cakrawala</strong>.
                    Tim Redaksi kami telah meninjau naskah yang Anda kirimkan. Berikut adalah status perkembangan terbaru:
                </p>

                <!-- Status Card -->
                <div class="status-card {{ in_array($status, ['diterima', 'diterbitkan']) ? 'diterima' : ($status === 'direvisi' ? 'direvisi' : 'ditolak') }}">
                    @if(in_array($status, ['diterima', 'diterbitkan']))
                        <span class="status-badge {{ $status }}">
                            {{ $status === 'diterbitkan' ? 'Sudah Diterbitkan' : 'Disetujui / Diterima' }}
                        </span>
                        <div class="naskah-title">{{ $naskah->judul }}</div>
                        <div class="naskah-meta">Kategori: {{ $naskah->kategori ?: 'Berita' }} &bull; Tanggal Kirim: {{ $naskah->waktu_kirim ? $naskah->waktu_kirim->format('d M Y') : now()->format('d M Y') }}</div>
                        <p style="margin: 10px 0 0; font-size: 14px; color: #065f46;">
                            Selamat! Naskah Anda telah dinyatakan layak dan disetujui oleh tim editorial kami untuk dipublikasikan.
                        </p>
                    @elseif($status === 'direvisi')
                        <span class="status-badge direvisi">Perlu Revisi</span>
                        <div class="naskah-title">{{ $naskah->judul }}</div>
                        <div class="naskah-meta">Kategori: {{ $naskah->kategori ?: 'Berita' }} &bull; Tanggal Kirim: {{ $naskah->waktu_kirim ? $naskah->waktu_kirim->format('d M Y') : now()->format('d M Y') }}</div>
                        <p style="margin: 10px 0 0; font-size: 14px; color: #92400e;">
                            Naskah Anda memiliki potensi baik namun memerlukan sedikit penyesuaian atau revisi dari tim redaksi.
                        </p>
                    @else
                        <span class="status-badge ditolak">Belum Dapat Diterima</span>
                        <div class="naskah-title">{{ $naskah->judul }}</div>
                        <div class="naskah-meta">Kategori: {{ $naskah->kategori ?: 'Berita' }} &bull; Tanggal Kirim: {{ $naskah->waktu_kirim ? $naskah->waktu_kirim->format('d M Y') : now()->format('d M Y') }}</div>
                        <p style="margin: 10px 0 0; font-size: 14px; color: #991b1b;">
                            Mohon maaf, naskah Anda saat ini belum dapat kami terbitkan karena belum memenuhi standar kriteria editorial kami.
                        </p>
                    @endif

                    @if(!empty($catatan))
                        <div class="note-box">
                            <div class="note-title">Catatan dari Redaksi:</div>
                            <p class="note-text">{{ $catatan }}</p>
                        </div>
                    @endif
                </div>

                @if(!empty($urlBerita))
                    <div class="cta-container">
                        <a href="{{ $urlBerita }}" class="btn-cta" target="_blank" rel="noopener noreferrer">
                            Baca Berita yang Telah Terbit &rarr;
                        </a>
                    </div>
                @endif

                <p style="font-size: 13px; color: #64748b; margin-top: 24px;">
                    Jika Anda memiliki pertanyaan lebih lanjut mengenai naskah Anda atau ingin bergabung sebagai Kontributor tetap, silakan kunjungi website resmi kami atau balas email ini.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p><strong>Redaksi Harian Cakrawala</strong></p>
                <p>Media Informasi & Portal Berita Terpercaya</p>
                <p style="font-size: 11px; margin-top: 8px;">
                    Email ini dikirimkan secara otomatis oleh sistem Harian Cakrawala kepada {{ $guest->email ?? 'Anda' }}.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
