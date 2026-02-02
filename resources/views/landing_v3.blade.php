<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mangga Muda - BPS Kabupaten Demak</title>
    <meta name="description" content="Manajemen Magang Terintegrasi dan Mudah di BPS Kabupaten Demak">
    <link rel="icon" href="assets/img/logoBPS.png" type="image/x-icon">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .hero-pattern {
            background-color: #f3f4f6;
            background-image: radial-gradient(#3b82f6 0.5px, transparent 0.5px), radial-gradient(#3b82f6 0.5px, #f3f4f6 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.1;
        }
    </style>
</head>

<body class="text-gray-800 bg-gray-50 antialiased">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav border-b border-gray-200 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <img class="h-10 w-auto" src="assets/img/logoBPS.png" alt="BPS Demak">
                    <div class="hidden md:block">
                        <span
                            class="block text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">MANGGA
                            MUDA</span>
                    </div>
                </div>
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#home" class="text-gray-600 hover:text-blue-600 font-medium transition">Beranda</a>
                    <a href="#features" class="text-gray-600 hover:text-blue-600 font-medium transition">Fitur</a>
                    <a href="#team" class="text-gray-600 hover:text-blue-600 font-medium transition">Tim</a>
                    <a href="#clients" class="text-gray-600 hover:text-blue-600 font-medium transition">Mitra</a>
                    <a href="{{ route('login') }}"
                        class="px-6 py-2.5 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition shadow-lg hover:shadow-blue-500/30">
                        Login / Daftar
                    </a>
                </div>
                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button class="text-gray-600 focus:outline-none">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative pt-40 pb-40 lg:pt-56 lg:pb-56 overflow-hidden">
        <div class="absolute inset-0 hero-pattern z-0"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <div
                    class="inline-block px-4 py-1.5 mb-6 bg-blue-50 text-blue-700 font-semibold rounded-full text-sm border border-blue-100">
                    🚀 Sistem Manajemen Magang Modern
                </div>
                <h1
                    class="text-5xl md:text-6xl lg:text-7xl font-extrabold tracking-tight text-gray-900 mb-8 leading-tight">
                    Magang Lebih <span
                        class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">Mudah</span>
                    & <span
                        class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">Terintegrasi</span>
                </h1>
                <p class="text-xl md:text-2xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Mangga Muda membantu BPS Kabupaten Demak mengelola program magang mulai dari registrasi, presensi,
                    hingga monitoring kegiatan secara digital.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}"
                        class="px-8 py-4 bg-blue-600 text-white font-bold rounded-full text-lg hover:bg-blue-700 transition shadow-xl hover:shadow-blue-500/30 transform hover:-translate-y-1">
                        Mulai Sekarang
                    </a>
                    <a href="#features"
                        class="px-8 py-4 bg-white text-gray-700 font-bold rounded-full text-lg border border-gray-200 hover:border-gray-300 hover:bg-gray-50 transition shadow-lg transform hover:-translate-y-1">
                        Pelajari Fitur
                    </a>
                </div>
            </div>


        </div>
    </section>

    <!-- Trusted By Section -->
    <section id="clients" class="py-10 border-y border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm font-semibold text-gray-500 uppercase tracking-widest mb-8">Dipercaya oleh
                Universitas & Sekolah Ternama</p>
            <div
                class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8 items-center opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
                <!-- Replace with actual logos -->
                <img src="assets/img/clients/c_logo01.png" alt="Partner 1" class="h-12 object-contain mx-auto">
                <img src="assets/img/clients/c_logo02.png" alt="Partner 2" class="h-12 object-contain mx-auto">
                <img src="assets/img/clients/c_logo03.png" alt="Partner 3" class="h-12 object-contain mx-auto">
                <img src="assets/img/clients/c_logo04.png" alt="Partner 4" class="h-12 object-contain mx-auto">
                <img src="assets/img/clients/c_logo05.png" alt="Partner 5" class="h-12 object-contain mx-auto">
                <img src="assets/img/clients/c_logo06.png" alt="Partner 6" class="h-12 object-contain mx-auto">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Fitur Unggulan</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Semua yang Anda butuhkan dalam satu platform
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    Optimalkan pengalaman magang dengan fitur-fitur canggih yang dirancang khusus.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div
                    class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition duration-300 border border-gray-100 relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 rounded-full group-hover:bg-blue-100 transition">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-blue-600 rounded-xl flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-blue-600/30">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Registrasi Magang</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Pendaftaran peserta magang secara daring, cepat, dan dilengkapi notifikasi otomatis untuk
                            kemudahan administrasi.
                        </p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div
                    class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition duration-300 border border-gray-100 relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-indigo-50 rounded-full group-hover:bg-indigo-100 transition">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-indigo-600 rounded-xl flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-indigo-600/30">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Presensi Digital</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Sistem presensi berbasis lokasi dan waktu realtime untuk memantau kehadiran peserta magang
                            secara akurat.
                        </p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div
                    class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition duration-300 border border-gray-100 relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-teal-50 rounded-full group-hover:bg-teal-100 transition">
                    </div>
                    <div class="relative z-10">
                        <div
                            class="w-14 h-14 bg-teal-600 rounded-xl flex items-center justify-center text-white text-2xl mb-6 shadow-lg shadow-teal-600/30">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Monitoring Kegiatan</h3>
                        <p class="text-gray-600 leading-relaxed">
                            Laporan harian dan monitoring progress kegiatan peserta magang yang dapat diakses oleh
                            pembimbing kapan saja.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Tim Pembimbing</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Mentor Berpengalaman
                </p>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    Siap membimbing peserta magang untuk mendapatkan pengalaman kerja terbaik.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Team Member 1 -->
                <div class="group relative">
                    <div class="relative overflow-hidden rounded-2xl shadow-lg aspect-[4/5]">
                        <img src="assets/img/team-1.jpg" alt="Aji"
                            class="object-cover w-full h-full group-hover:scale-110 transition duration-500">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent opacity-90">
                        </div>
                        <div class="absolute bottom-0 left-0 p-6 w-full">
                            <h3 class="text-xl font-bold text-white">Aji</h3>
                            <p class="text-blue-300 font-medium">Ketua Pembimbing</p>
                            <div
                                class="mt-4 flex space-x-4 opacity-0 group-hover:opacity-100 transition transform translate-y-4 group-hover:translate-y-0">
                                <a href="#" class="text-white hover:text-blue-400"><i
                                        class="fa-brands fa-linkedin text-xl"></i></a>
                                <a href="#" class="text-white hover:text-blue-400"><i
                                        class="fa-brands fa-twitter text-xl"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="group relative">
                    <div class="relative overflow-hidden rounded-2xl shadow-lg aspect-[4/5]">
                        <img src="assets/img/team-2.jpg" alt="Rini Astuti"
                            class="object-cover w-full h-full group-hover:scale-110 transition duration-500">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent opacity-90">
                        </div>
                        <div class="absolute bottom-0 left-0 p-6 w-full">
                            <h3 class="text-xl font-bold text-white">Rini Astuti</h3>
                            <p class="text-blue-300 font-medium">Web Designer</p>
                            <div
                                class="mt-4 flex space-x-4 opacity-0 group-hover:opacity-100 transition transform translate-y-4 group-hover:translate-y-0">
                                <a href="#" class="text-white hover:text-blue-400"><i
                                        class="fa-brands fa-linkedin text-xl"></i></a>
                                <a href="#" class="text-white hover:text-blue-400"><i
                                        class="fa-brands fa-dribbble text-xl"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Member 3 -->
                <div class="group relative">
                    <div class="relative overflow-hidden rounded-2xl shadow-lg aspect-[4/5]">
                        <img src="assets/img/team-3.jpg" alt="M. Aziz"
                            class="object-cover w-full h-full group-hover:scale-110 transition duration-500">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent opacity-90">
                        </div>
                        <div class="absolute bottom-0 left-0 p-6 w-full">
                            <h3 class="text-xl font-bold text-white">M. Aziz</h3>
                            <p class="text-blue-300 font-medium">Developer</p>
                            <div
                                class="mt-4 flex space-x-4 opacity-0 group-hover:opacity-100 transition transform translate-y-4 group-hover:translate-y-0">
                                <a href="#" class="text-white hover:text-blue-400"><i
                                        class="fa-brands fa-github text-xl"></i></a>
                                <a href="#" class="text-white hover:text-blue-400"><i
                                        class="fa-brands fa-twitter text-xl"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Member 4 -->
                <div class="group relative">
                    <div class="relative overflow-hidden rounded-2xl shadow-lg aspect-[4/5]">
                        <img src="assets/img/team-4.jpg" alt="M Abdul Muhshi"
                            class="object-cover w-full h-full group-hover:scale-110 transition duration-500">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent opacity-90">
                        </div>
                        <div class="absolute bottom-0 left-0 p-6 w-full">
                            <h3 class="text-xl font-bold text-white">M Abdul Muhshi</h3>
                            <p class="text-blue-300 font-medium">Photographer</p>
                            <div
                                class="mt-4 flex space-x-4 opacity-0 group-hover:opacity-100 transition transform translate-y-4 group-hover:translate-y-0">
                                <a href="#" class="text-white hover:text-blue-400"><i
                                        class="fa-brands fa-instagram text-xl"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-1">
                    <img src="assets/img/logoBPS.png" alt="BPS Logo" class="h-12 mb-4">
                    <h3 class="text-xl font-bold mb-4">MANGGA MUDA</h3>
                    <p class="text-gray-400 text-sm">
                        Manajemen Magang Terintegrasi dan Mudah. Platform resmi BPS Kabupaten Demak.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-gray-200">Kontak</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li class="flex items-start gap-2"><i class="fa-solid fa-location-dot mt-1 text-blue-500"></i>
                            Jl. Sultan Hadiwijaya No. 23, Demak</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-phone text-blue-500"></i> (0291)
                            685445</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-envelope text-blue-500"></i>
                            bps3321@bps.go.id</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-gray-200">Tautan Cepat</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#home" class="hover:text-blue-400 transition">Beranda</a></li>
                        <li><a href="#features" class="hover:text-blue-400 transition">Fitur</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-blue-400 transition">Login Admin</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-gray-200">Sosial Media</h4>
                    <div class="flex space-x-4">
                        <a href="https://www.facebook.com/bpsdemak3321"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://www.tiktok.com/@bpskabdemak"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-black transition">
                            <i class="fa-brands fa-tiktok"></i>
                        </a>
                        <a href="https://instagram.com/bpskabdemak"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-pink-600 transition">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="https://www.youtube.com/channel/UCUSLymM56wEiJvc5dBWodeA"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-red-600 transition">
                            <i class="fa-brands fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm">&copy; 2025 BPS Kabupaten Demak. All rights reserved.</p>
                <p class="text-gray-500 text-sm flex items-center gap-1">Made with <i
                        class="fa-solid fa-heart text-red-500"></i> for Magang</p>
            </div>
        </div>
    </footer>
</body>

</html>