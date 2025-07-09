<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Magang - {{ $nama }}</title>
    <style>
        /* Mengatur halaman agar tidak ada margin */
        @page {
            margin: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif; /* Font lebih formal */
            margin: 0;
            padding: 0;
        }
        /* Kontainer utama yang ukurannya sama dengan halaman */
        .certificate-container {
            width: 100%;
            height: 100%;
            position: relative;
            /* background-image: url("{{ public_path('images/sertifikat-template.jpg') }}"); */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* =================================================================
         * BAGIAN KUSTOMISASI: SESUAIKAN SEMUA NILAI PIXEL DI BAWAH INI
         * agar pas dengan desain template dan seleramu.
         * ================================================================= */

        .logo {
            position: absolute;
            top: 80px;
            left: 50%; /* Posisikan di tengah secara horizontal */
            transform: translateX(-50%); /* Trik untuk centering sempurna */
            width: 90px; /* Sesuaikan ukuran logo */
        }

        .judul {
            position: absolute;
            width: 100%;
            top: 180px; /* Turunkan di bawah logo */
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            text-decoration: underline;
        }

        .nomor {
            position: absolute;
            width: 100%;
            top: 220px;
            text-align: center;
            font-size: 16px;
        }

        .paragraf-pembuka {
            position: absolute;
            width: 80%;
            top: 280px;
            left: 10%;
            text-align: justify;
            font-size: 16px;
            line-height: 1.5;
        }

        .foto-profil {
            position: absolute;
            top: 340px; /* Sejajarkan dengan blok data peserta */
            left: 120px; /* Posisikan di sebelah kiri */
            width: 120px;  /* Lebar 4cm (sekitar 151px) */
            height: 180px; /* Tinggi 6cm (sekitar 227px) */
            border: 1px solid #ccc;
            object-fit: cover; /* Pastikan gambar mengisi area tanpa distorsi */
        }

        .data-peserta {
            position: absolute;
            top: 350px;
            left: 280px; /* Geser ke kanan untuk memberi ruang bagi foto */
            font-size: 16px;
            line-height: 1.6;
        }

        .paragraf-penutup {
            position: absolute;
            width: 80%;
            top: 450px; /* Sesuaikan posisinya di bawah blok data */
            left: 10%;
            text-align: justify;
            font-size: 16px;
            line-height: 1.5;
        }

        .ttd-block {
            position: absolute;
            top: 550px;
            right: 90px;
            width: 300px; /* Lebar area tanda tangan */
            text-align: center;
            font-size: 16px;
            line-height: 1.5;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 80px; /* Jarak untuk tanda tangan */
        }

    </style>
</head>
<body>
    <div class="certificate-container">

        {{-- 1. LOGO BPS (sekarang di tengah atas) --}}
        <img src="{{ public_path('images/logo-bps.png') }}" class="logo" alt="Logo BPS">

        {{-- 2. FOTO PROFIL PESERTA (sekarang di kiri) --}}
        @if($photoDataUri)
            <img src="{{ $photoDataUri }}" class="foto-profil" alt="Foto Profil">
        @endif

        {{-- 3. JUDUL SERTIFIKAT --}}
        <div class="judul">SERTIFIKAT</div>
        <div class="nomor">Nomor: ... / ... / ...</div>

        {{-- 4. PARAGRAF PEMBUKA --}}
        <div class="paragraf-pembuka">
            Dengan ini menerangkan bahwa:
        </div>

        {{-- 5. DATA PESERTA --}}
        <div class="data-peserta">
            <table>
                <tr>
                    <td style="width: 200px;">Nama</td>
                    <td>:</td>
                    <td style="font-weight: bold;">{{ $nama }}</td>
                </tr>
                <tr>
                    <td>Asal Instansi</td>
                    <td>:</td>
                    <td>{{ $universitas }}</td>
                </tr>
            </table>
        </div>

        {{-- 6. PARAGRAF PENUTUP --}}
        <div class="paragraf-penutup">
            Telah menyelesaikan Praktik Kerja Lapangan (PKL) di Badan Pusat Statistik (BPS) Kabupaten Demak yang dilaksanakan pada periode <strong>{{ $periode }}</strong> dengan hasil yang baik.
        </div>

        {{-- 7. BLOK TANDA TANGAN --}}
        <div class="ttd-block">
            <div class="ttd-kota-tanggal">Demak, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
            <div class="ttd-jabatan">Kepala BPS Kabupaten Demak</div>
            <div class="ttd-nama">Nama Kepala BPS</div>
            <div class="ttd-nip">NIP. 123456789012345678</div>
        </div>

    </div>
</body>
</html>
