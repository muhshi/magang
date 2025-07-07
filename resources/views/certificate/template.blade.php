<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Sertifikat Magang</title>
    <style>
        /* Mengatur halaman agar tidak ada margin */
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
        /* Kontainer utama yang ukurannya sama dengan halaman */
        .certificate-container {
            width: 100%;
            height: 100%;
            position: relative;
            /* GANTI DENGAN PATH TEMPLATE GAMBARMU */
            background-image: url("{{ public_path('images/sertifikat-template.jpg') }}");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Base style untuk teks yang akan ditempatkan */
        .text-element {
            position: absolute;
            text-align: center;
            width: 100%;
            color: #333; /* Ganti warna teks jika perlu */
        }

        /* * =================================================================
         * BAGIAN KUSTOMISASI: SESUAIKAN NILAI DI BAWAH INI
         * =================================================================
         * Ubah nilai 'top' (jarak dari atas) dan 'font-size'
         * agar pas dengan desain template gambarmu.
        */

        .nama {
            top: 350px; /* Contoh: 350px dari atas halaman */
            font-size: 48px;
            font-weight: bold;
        }

        .universitas {
            top: 450px; /* Contoh: 450px dari atas halaman */
            font-size: 24px;
        }

        .periode {
            top: 520px; /* Contoh: 520px dari atas halaman */
            font-size: 18px;
        }

        .foto-profil {
            position: absolute;
            top: 150px; /* Contoh: 150px dari atas */
            left: 50%; /* Tengahkan secara horizontal */
            transform: translateX(-50%);
            width: 150px; /* Sesuaikan ukuran foto */
            height: 150px;
            border-radius: 50%; /* Membuat foto menjadi bundar */
            border: 5px solid white; /* Opsional: beri bingkai putih */
            object-fit: cover;
        }

    </style>
</head>
<body>
    <div class="certificate-container">

        {{-- Tampilkan foto jika ada --}}
        @if($photoUrl)
            <img src="{{ $photoUrl }}" class="foto-profil" alt="Foto Profil">
        @endif

        {{-- Tempatkan data dinamis --}}
        <div class="text-element nama">
            {{ $nama }}
        </div>

        <div class="text-element universitas">
            {{ $universitas }}
        </div>

        <div class="text-element periode">
            Telah Melaksanakan Praktik Kerja Lapangan dari tanggal {{ $periode }}
        </div>

    </div>
</body>
</html>
