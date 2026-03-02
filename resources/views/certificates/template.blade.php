<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Magang - {{ $nama }}</title>
    <style>
        @font-face {
            font-family: 'Anton';
            src: url("{{ public_path('fonts/Anton-Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url("{{ public_path('fonts/Montserrat-Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Montserrat';
            src: url("{{ public_path('fonts/Montserrat-Bold.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'OleoScript';
            src: url("{{ public_path('fonts/OleoScript-Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page {
            margin: 0;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .certificate-container {
            width: 100%;
            height: 100%;
            background-image: url("{{ public_path('images/TEMPLATE.png') }}");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        .content {
            padding-top: 60px;
            padding-left: 80px;
            padding-right: 80px;
            text-align: center;
        }

        .judul {
            font-size: 54px;
            font-family: 'Anton', sans-serif;
            font-weight: normal;
            color: #1a1a1a;
            letter-spacing: 4px;
            margin-bottom: 2px;
        }

        .nomor {
            font-size: 17px;
            font-weight: bold;
            color: #c8902e;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }

        .pembuka {
            font-size: 19px;
            color: #333;
            margin-bottom: 5px;
        }

        .nama-wrapper {
            margin-bottom: 5px;
        }
        .nama-garis {
            width: 400px;
            margin: 0 auto;
            border-bottom: 1px solid #999;
        }
        .nama-peserta {
            font-size: 38px;
            font-family: 'OleoScript', cursive;
            color: #1a1a1a;
            margin-top: -5px;
        }

        .info-peserta {
            font-size: 17px;
            line-height: 1.4;
            color: #333;
            margin-bottom: 10px;
        }

        .body-text {
            font-size: 17px;
            line-height: 1.5;
            color: #333;
            margin-bottom: 5px;
            padding-left: 60px;
            padding-right: 60px;
        }
        .predikat {
            font-weight: bold;
            color: #c8902e;
            font-size: 17px;
        }

        .ttd-table {
            width: 100%;
        }
        .ttd-cell {
            text-align: center;
            font-size: 17px;
            line-height: 1.4;
            vertical-align: top;
            padding-top: 5px;
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }
        .ttd-nip {
            font-size: 14px;
        }

        .qr-code {
            width: 70px;
            height: 70px;
            margin: 5px auto;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="content">

            {{-- 1. JUDUL --}}
            <div class="judul">SERTIFIKAT MAGANG</div>

            {{-- 2. NOMOR --}}
            <div class="nomor">NOMOR : {{ $nomor }}</div>

            {{-- 3. PEMBUKA --}}
            <div class="pembuka">Dengan bangga diberikan kepada :</div>

            {{-- 4. NAMA PESERTA --}}
            <div class="nama-wrapper">
                <div class="nama-peserta">{{ $nama }}</div>
                <div class="nama-garis"></div>
            </div>

            {{-- 5. INFO PESERTA --}}
            <div class="info-peserta">
                Mahasiswa Program Studi {{ $programStudi }}, {{ $fakultas }},<br>
                {{ $universitas }}<br>
                NIM : &nbsp;{{ $nim }}
            </div>

            {{-- 6. BODY TEXT --}}
            <div class="body-text">
                atas terselesaikannya Praktik Kerja Lapangan (PKL) di Badan Pusat Statistik Kabupaten
                Demak dari tanggal <strong>{{ $periodeMulai }} - {{ $periodeSelesai }}</strong>
                dengan hasil <span class="predikat">{{ $predikat }}</span>
            </div>

            {{-- 7. TTD --}}
            <table class="ttd-table">
                <tr>
                    <td width="50%"></td>
                    <td width="50%" class="ttd-cell">
                        Demak, {{ $tanggalSertifikat }}<br>
                        Kepala<br>
                        BPS Kabupaten Demak,<br>
                        <img src="{{ $qrCodeDataUri }}" class="qr-code" alt="QR Code"><br>
                        <span class="ttd-nama">{{ $kepalaBpsName }}</span><br>
                        @if(!empty($kepalaBpsNip))
                            <span class="ttd-nip">NIP. {{ $kepalaBpsNip }}</span>
                        @endif
                    </td>
                </tr>
            </table>

        </div>
    </div>
</body>
</html>
