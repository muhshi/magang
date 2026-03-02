<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat - {{ $internship->full_name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 520px;
            width: 100%;
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #c8902e 0%, #d4a24a 100%);
            color: white;
            padding: 28px 32px;
            text-align: center;
        }
        .card-header .icon {
            font-size: 40px;
            margin-bottom: 8px;
        }
        .card-header h1 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .card-header p {
            font-size: 13px;
            opacity: 0.9;
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 12px;
        }
        .verified-badge::before {
            content: '✓';
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            background: white;
            color: #c8902e;
            border-radius: 50%;
            font-size: 11px;
            font-weight: bold;
        }

        .card-body {
            padding: 28px 32px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            flex-shrink: 0;
            width: 40%;
        }
        .info-value {
            font-size: 14px;
            color: #333;
            font-weight: 500;
            text-align: right;
            width: 58%;
        }
        .info-value.highlight {
            color: #c8902e;
            font-weight: 700;
        }

        .card-footer {
            padding: 0 32px 28px;
        }

        .btn-download {
            display: block;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #c8902e 0%, #d4a24a 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(200, 144, 46, 0.4);
        }

        .footer-note {
            text-align: center;
            margin-top: 16px;
            font-size: 11px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <div class="icon">🎓</div>
            <h1>Sertifikat Magang</h1>
            <p>Badan Pusat Statistik Kabupaten Demak</p>
            <div class="verified-badge">Sertifikat Terverifikasi</div>
        </div>

        <div class="card-body">
            <div class="info-row">
                <div class="info-label">Nomor</div>
                <div class="info-value">{{ $certificate->certificate_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama</div>
                <div class="info-value">{{ $internship->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Program Studi</div>
                <div class="info-value">{{ $certificate->program_studi }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Universitas</div>
                <div class="info-value">{{ $internship->school_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">NIM</div>
                <div class="info-value">{{ $certificate->nim }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Periode</div>
                <div class="info-value">{{ $tanggalMulai }} — {{ $tanggalSelesai }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Predikat</div>
                <div class="info-value highlight">{{ $certificate->predikat }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Terbit</div>
                <div class="info-value">{{ $tanggalSertifikat }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Ditandatangani</div>
                <div class="info-value">{{ $kepalaBpsName }}</div>
            </div>
        </div>

        <div class="card-footer">
            <a href="{{ route('certificate.download', $certificate->uuid) }}" class="btn-download">
                📥 Download Sertifikat (PDF)
            </a>
            <div class="footer-note">
                Sertifikat ini diterbitkan oleh BPS Kabupaten Demak secara resmi.
            </div>
        </div>
    </div>
</body>
</html>
