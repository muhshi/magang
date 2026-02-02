<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Mangga Muda - BPS Kabupaten Demak</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="{{ asset('landing-assets/img/logo1.png') }}" rel="icon">
    <link href="{{ asset('landing-assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('landing-assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landing-assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('landing-assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('landing-assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('landing-assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Main CSS File -->
    <link href="{{ asset('landing-assets/css/main.css') }}" rel="stylesheet">

</head>

<body class="index-page">

    <header id="header" class="header d-flex align-items-center fixed-top">
        <div class="container-fluid container-xl position-relative d-flex align-items-center">

            <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
                <img src="{{ asset('landing-assets/img/logoBPS.png') }}" alt="">
                <h1 class="sitename">Mangga Muda</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#hero" class="active">Home</a></li>
                    <li><a href="#about">Tentang</a></li>
                    <li><a href="#features">Fitur</a></li>
                    <li><a href="#services">Benefit</a></li>
                    <li><a href="#more-features">Program</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <!-- <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Dropdown 1</a></li>
              <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Deep Dropdown 1</a></li>
                  <li><a href="#">Deep Dropdown 2</a></li>
                  <li><a href="#">Deep Dropdown 3</a></li>
                  <li><a href="#">Deep Dropdown 4</a></li>
                  <li><a href="#">Deep Dropdown 5</a></li>
                </ul>
              </li>
              <li><a href="#">Dropdown 2</a></li>
              <li><a href="#">Dropdown 3</a></li>
              <li><a href="#">Dropdown 4</a></li>
            </ul>
          </li> -->
                    <li><a href="#testimonials">Pembimbing</a></li>
                    <li><a href="#contact">Kontak</a></li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

            <a class="btn-getstarted" href="{{ url('/admin/login') }}">Masuk/Daftar</a>

        </div>
    </header>

    <main class="main">
        <!-- Hero Section -->
        <section id="hero" class="hero section">
            <div class="hero-bg">
                <img src="{{ asset('landing-assets/img/logo1.png') }}" alt="">
            </div>
            <div class="container text-center">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <h1 data-aos="fade-up">Selamat Datang di <span>Mangga Muda</span></h1>
                    <br>
                    <h4 data-aos="fade-up" data-aos-delay="100">Mangga Muda membantu BPS Kabupaten Demak mengelola
                        program magang mulai dari registrasi, presensi, hingga monitoring kegiatan secara digital.</h4>
                    <br>
                    <div class="d-flex" data-aos="fade-up" data-aos-delay="200">
                        <a href="{{ url('/admin/register') }}" class="btn-get-started">Daftar</a>
                        <a href="https://youtu.be/ykPFb0zlJIo?si=BoaCtKx9laW98ZHB"
                            class="glightbox btn-watch-video d-flex align-items-center"><i
                                class="bi bi-play-circle"></i><span>Watch Video</span></a>
                    </div>
                    <img src="{{ asset('landing-assets/img/logo1.png') }}" class="img-fluid hero-img" alt=""
                        data-aos="zoom-out" data-aos-delay="300">
                </div>
            </div>

        </section><!-- /Hero Section -->

        <!-- Featured Services Section -->
        <section id="featured-services" class="featured-services section light-background">

            <div class="container">

                <div class="row gy-4">

                    <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-item d-flex">
                            <div class="icon flex-shrink-0"><i class="bi bi-briefcase"></i></div>
                            <div>
                                <h4 class="title"><a href="#" class="stretched-link">Magang Mandiri</a></h4>
                                <p class="description">Program magang bagi siswa SMK dan mahasiswa untuk memperoleh
                                    pengalaman kerja langsung di lingkungan Badan Pusat Statistik sesuai bidang dan
                                    minat.</p>
                            </div>
                        </div>
                    </div>
                    <!-- End Service Item -->

                    <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-item d-flex">
                            <div class="icon flex-shrink-0"><i class="bi bi-card-checklist"></i></div>
                            <div>
                                <h4 class="title"><a href="#" class="stretched-link">Praktek Kerja Lapangan</a></h4>
                                <p class="description">Kegiatan magang terstruktur sebagai bagian dari pemenuhan
                                    kewajiban akademik, dengan pendampingan pembimbing selama pelaksanaan.</p>
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-xl-4 col-lg-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-item d-flex">
                            <div class="icon flex-shrink-0"><i class="bi bi-bar-chart"></i></div>
                            <div>
                                <h4 class="title"><a href="#" class="stretched-link">Pengambilan Data Penelitian</a>
                                </h4>
                                <p class="description">Fasilitasi kegiatan pengumpulan dan pengolahan data untuk
                                    mendukung kebutuhan penelitian, tugas akhir, maupun laporan akademik.</p>
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                </div>

            </div>

        </section><!-- /Featured Services Section -->

        <!-- About Section -->
        <section id="about" class="about section">

            <div class="container">

                <div class="row gy-4">

                    <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="100">
                        <p class="who-we-are">Apa itu Mangga Muda?</p>
                        <h3>Manajemen Magang Badan Pusat Statistik Kabupaten Demak</h3>
                        <h4 class="fst-italic">
                            Kenapa Mangga Muda?
                        </h4>
                        <ul>
                            <li><i class="bi bi-check-circle"></i> <span>Proses pendaftaran magang lebih mudah, cepat,
                                    dan terpusat.</span></li>
                            <li><i class="bi bi-check-circle"></i> <span>Sistem pengelolaan peserta yang tertata dan
                                    terstruktur.</span></li>
                            <li><i class="bi bi-check-circle"></i> <span>Mendukung pengembangan kompetensi dan kesiapan
                                    kerja peserta magang.</span></li>
                        </ul>
                        <!-- <a href="#" class="read-more"><span>Read More</span><i class="bi bi-arrow-right"></i></a> -->
                    </div>

                    <div class="col-lg-6 about-images" data-aos="fade-up" data-aos-delay="200">
                        <div class="row gy-4">
                            <div class="col-lg-6">
                                <img src="{{ asset('landing-assets/img/doc3.jpg') }}" class=" img-fluid" alt="">
                            </div>
                            <div class="col-lg-6">
                                <div class="row gy-4">
                                    <div class="col-lg-12">
                                        <img src="{{ asset('landing-assets/img/doc2.jpg') }}" class=" img-fluid" alt="">
                                    </div>
                                    <div class="col-lg-12">
                                        <img src="{{ asset('landing-assets/img/doc1.jpeg') }}" class=" img-fluid"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </section><!-- /About Section -->

        <!-- Clients Section -->
        <section id="clients" class="clients section">
            <h3 class="text-center text-sm font-semibold text-gray-500 uppercase tracking-widest mb-8">Dipercaya oleh
                Universitas &amp; Sekolah Ternama</h3>

            <div class="container" data-aos="fade-up">

                <div class="row gy-4">

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('landing-assets/img/clients/logosmk1.png') }}" class=" img-fluid" alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('landing-assets/img/clients/logosmkalmaarif.jpg') }}" class=" img-fluid"
                            alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('landing-assets/img/clients/logoundip.png') }}" class=" img-fluid" alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('landing-assets/img/clients/logounnes.jpg') }}" class=" img-fluid" alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('landing-assets/img/clients/logouns.png') }}" class=" img-fluid" alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('landing-assets/img/clients/logousm.png') }}" class=" img-fluid" alt="">
                    </div><!-- End Client Item -->

                    <div class="col-xl-2 col-md-3 col-6 client-logo">
                        <img src="{{ asset('landing-assets/img/clients/logoic.png') }}" class=" img-fluid" alt="">
                    </div><!-- End Client Item -->

                </div>

            </div>

        </section><!-- /Clients Section -->

        <!-- Features Section -->
        <section id="features" class="features section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Fitur Unggulan Mangga Muda</h2>
                <p>Semua yang Anda butuhkan dalam satu platform. Optimalkan pengalaman magang dengan fitur-fitur canggih
                    yang dirancang khusus.</p>
            </div><!-- End Section Title -->

            <div class="container">
                <div class="row justify-content-between">

                    <div class="col-lg-5 d-flex align-items-center">

                        <ul class="nav nav-tabs" data-aos="fade-up" data-aos-delay="100">
                            <li class="nav-item">
                                <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
                                    <i class="bi bi-binoculars"></i>
                                    <div>
                                        <h4 class="d-none d-lg-block">Registrasi Magang</h4>
                                        <h4>
                                            Pendaftaran peserta magang secara daring, cepat, dan dilengkapi notifikasi
                                            otomatis untuk kemudahan administrasi.
                                        </h4>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
                                    <i class="bi bi-box-seam"></i>
                                    <div>
                                        <h4 class="d-none d-lg-block">Presensi Digital</h4>
                                        <h4>
                                            Sistem presensi berbasis lokasi dan waktu realtime untuk memantau kehadiran
                                            peserta magang secara akurat.
                                        </h4>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
                                    <i class="bi bi-brightness-high"></i>
                                    <div>
                                        <h4 class="d-none d-lg-block">Monitoring Kegiatan</h4>
                                        <h4>
                                            Laporan harian dan monitoring progress kegiatan peserta magang yang dapat
                                            diakses oleh pembimbing kapan saja.
                                        </h4>
                                    </div>
                                </a>
                            </li>
                        </ul><!-- End Tab Nav -->

                    </div>

                    <div class="col-lg-6">

                        <div class="tab-content" data-aos="fade-up" data-aos-delay="200">

                            <div class="tab-pane fade active show" id="features-tab-1">
                                <img src="{{ asset('landing-assets/img/doc4.jpg') }}" alt="" class=" img-fluid">
                            </div><!-- End Tab Content Item -->

                            <div class="tab-pane fade" id="features-tab-2">
                                <img src="{{ asset('landing-assets/img/doc6.jpg') }}" alt="" class=" img-fluid">
                            </div><!-- End Tab Content Item -->

                            <div class="tab-pane fade" id="features-tab-3">
                                <img src="{{ asset('landing-assets/img/doc7.jpg') }}" alt="" class=" img-fluid">
                            </div><!-- End Tab Content Item -->
                        </div>

                    </div>

                </div>

            </div>

        </section><!-- /Features Section -->



        <!-- Services Section -->
        <section id="services" class="services section light-background">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Benefit Peserta Magang</h2>
                <p>Mengikuti program magang di Badan Pusat Statistik memberikan pengalaman belajar yang komprehensif
                    serta relevan dengan kebutuhan dunia akademik dan kerja.</p>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row g-5">

                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="service-item item-cyan position-relative">
                            <i class="bi bi-activity icon"></i>
                            <div>
                                <h3>Pengalaman di Badan Pusat Statistik</h3>
                                <p>Terlibat langsung dalam lingkungan kerja Badan Pusat Statistik dan memahami proses
                                    kerja statistik secara nyata.</p>
                                <!-- <a href="#" class="read-more stretched-link">Learn More <i class="bi bi-arrow-right"></i></a> -->
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-item item-orange position-relative">
                            <i class="bi bi-broadcast icon"></i>
                            <div>
                                <h3>Sertifikat</h3>
                                <p>Peserta yang menyelesaikan program akan memperoleh sertifikat resmi sebagai bukti
                                    pengalaman dan kompetensi.</p>
                                <!-- <a href="#" class="read-more stretched-link">Learn More <i class="bi bi-arrow-right"></i></a> -->
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-item item-teal position-relative">
                            <i class="bi bi-easel icon"></i>
                            <div>
                                <h3>Pendampingan oleh Mentor</h3>
                                <p>Dibimbing langsung oleh pegawai dan fungsional BPS yang berpengalaman di bidang
                                    statistik, data, dan sistem informasi.</p>
                                <!-- <a href="#" class="read-more stretched-link">Learn More <i class="bi bi-arrow-right"></i></a> -->
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="service-item item-red position-relative">
                            <i class="bi bi-bounding-box-circles icon"></i>
                            <div>
                                <h3>Peningkatan Keterampilan Teknis & Non-Teknis</h3>
                                <p>Mengembangkan kemampuan pengolahan data, administrasi, kerja tim, komunikasi, serta
                                    etika kerja profesional.</p>
                                <!-- <a href="#" class="read-more stretched-link">Learn More <i class="bi bi-arrow-right"></i></a> -->
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="service-item item-indigo position-relative">
                            <i class="bi bi-calendar4-week icon"></i>
                            <div>
                                <h3>Portofolio</h3>
                                <p>Mendapatkan hasil kerja dan kegiatan yang dapat digunakan sebagai portofolio akademik
                                    maupun profesional.</p>
                                <!-- <a href="#" class="read-more stretched-link">Learn More <i class="bi bi-arrow-right"></i></a> -->
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="service-item item-pink position-relative">
                            <i class="bi bi-chat-square-text icon"></i>
                            <div>
                                <h3>Konversi SKS</h3>
                                <p>Kegiatan magang dapat digunakan untuk konversi SKS sesuai dengan kebijakan kampus
                                    peserta.</p>
                                <!-- <a href="#" class="read-more stretched-link">Learn More <i class="bi bi-arrow-right"></i></a> -->
                            </div>
                        </div>
                    </div><!-- End Service Item -->

                </div>

            </div>

        </section><!-- /Services Section -->

        <!-- More Features Section -->
        <section id="more-features" class="more-features section">

            <div class="container">

                <div class="row justify-content-around gy-4">

                    <div class="col-lg-6 d-flex flex-column justify-content-center order-2 order-lg-1"
                        data-aos="fade-up" data-aos-delay="100">
                        <h3>Program</h3>
                        <p>Pilih bidang yang sesuai dengan minat dan keahlianmu. Setiap program dirancang untuk
                            memberikan pengalaman kerja nyata, pendampingan langsung, dan pengembangan skill yang
                            relevan dengan kebutuhan industri dan instansi.</p>

                        <div class="row">

                            <div class="col-lg-6 icon-box d-flex">
                                <i class="bi bi-easel flex-shrink-0"></i>
                                <div>
                                    <h4>Full Stack Developer</h4>
                                    <p>Terlibat langsung dalam pengembangan aplikasi internal dan sistem pendukung
                                        berbasis web. Peserta akan mempelajari alur kerja pengembangan aplikasi dari
                                        sisi frontend hingga backend, termasuk pengelolaan database dan implementasi
                                        fitur.</p>
                                </div>
                            </div><!-- End Icon Box -->

                            <div class="col-lg-6 icon-box d-flex">
                                <i class="bi bi-patch-check flex-shrink-0"></i>
                                <div>
                                    <h4>Graphic Designer</h4>
                                    <p>Mengembangkan konten visual yang informatif dan komunikatif untuk kebutuhan
                                        publikasi dan media internal. Peserta akan berlatih menerjemahkan data dan
                                        informasi menjadi desain yang menarik dan mudah dipahami.</p>
                                </div>
                            </div><!-- End Icon Box -->

                            <div class="col-lg-6 icon-box d-flex">
                                <i class="bi bi-brightness-high flex-shrink-0"></i>
                                <div>
                                    <h4>Content Writer</h4>
                                    <p>Berperan dalam penyusunan konten tulisan yang informatif, akurat, dan mudah
                                        dipahami oleh masyarakat. Peserta akan belajar menulis untuk kebutuhan publik,
                                        media sosial, dan dokumentasi.</p>
                                </div>
                            </div><!-- End Icon Box -->

                            <div class="col-lg-6 icon-box d-flex">
                                <i class="bi bi-brightness-high flex-shrink-0"></i>
                                <div>
                                    <h4>Broadcasting</h4>
                                    <p>Terlibat dalam proses produksi dan penyebaran informasi melalui media
                                        audio-visual. Peserta akan belajar teknik dasar broadcasting serta pengelolaan
                                        konten siaran.</p>
                                </div>
                            </div><!-- End Icon Box -->

                        </div>

                    </div>

                    <div class="features-image col-lg-5 order-1 order-lg-2" data-aos="fade-up" data-aos-delay="200">
                        <img src="{{ asset('landing-assets/img/doc5.jpg') }}" alt="">
                    </div>

                </div>

            </div>

        </section><!-- /More Features Section -->

        <!-- Faq Section -->
        <section id="faq" class="faq section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Frequently Asked Questions</h2>
            </div><!-- End Section Title -->

            <div class="container">

                <div class="row justify-content-center">

                    <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">

                        <div class="faq-container">

                            <div class="faq-item faq-active">
                                <h3>Siapa saja yang bisa mendaftar?</h3>
                                <div class="faq-content">
                                    <p>Calon peserta magang dapat berasal dari sekolah maupun Universitas
                                        berbagai
                                        jurusan/bidang.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Berapa lama durasi magang?</h3>
                                <div class="faq-content">
                                    <p>Durasi magang fleksibel sesuai ketentuan dari instansi peserta magang
                                        yang
                                        mendaftar.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Apakah magang dilakukan secara luring atau daring?</h3>
                                <div class="faq-content">
                                    <p>Kegiatan magang dilaksanakan secara luring, berlokasi di Kantor BPS
                                        Kabupaten
                                        Demak.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Apakah mendapatkan sertifikat?</h3>
                                <div class="faq-content">
                                    <p>Ya, peserta akan mendapatkan sertifikat magang setelah menyelesaikan
                                        program
                                        magang sebagai bukti pengalaman dan kompetensi.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <h3>Apakah kegiatan magang dapat digunakan untuk konversi SKS?</h3>
                                <div class="faq-content">
                                    <p>Ya, kegiatan magang dapat dikonversi menjadi SKS sesuai dengan
                                        ketentuan dari
                                        Universitas peserta yang mendaftar.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                        </div>

                    </div><!-- End Faq Column-->

                </div>

            </div>

        </section><!-- /Faq Section -->

        <!-- Testimonials Section -->
        <section id="testimonials" class="testimonials section light-background">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Pembimbing</h2>
                <p>Selama program magang, peserta akan didampingi oleh pembimbing yang berpengalaman di bidangnya dan
                    siap membantu proses belajar secara terarah. Pembimbing tidak hanya memberi arahan teknis, tetapi
                    juga membangun pola kerja profesional, komunikasi yang baik, dan kesiapan menghadapi dunia kerja.
                </p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="swiper init-swiper">
                    <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 1,
                  "spaceBetween": 40
                },
                "1200": {
                  "slidesPerView": 3,
                  "spaceBetween": 1
                }
              }
            }
          </script>
                    <div class="swiper-wrapper">

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    Kami tidak hanya fokus pada hasil akhir, tetapi juga proses belajar peserta. Setiap
                                    peserta didorong untuk memahami alur kerja dan berani bertanya.
                                </p>
                                <div class="profile mt-auto">
                                    <img src="#" class="testimonial-img" alt="">
                                    <h3>Aji</h3>
                                    <h4>Ketua Pembimbing</h4>
                                </div>
                            </div>
                        </div><!-- End testimonial item -->

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    Program magang ini dirancang agar peserta memperoleh pengalaman nyata di lingkungan
                                    Badan Pusat Statistik, sekaligus mengembangkan kedisiplinan, tanggung jawab, dan
                                    kemampuan kerja tim.
                                </p>
                                <div class="profile mt-auto">
                                    <img src="#" class="testimonial-img" alt="">
                                    <h3>Rini Astuti</h3>
                                    <h4>Pembimbing</h4>
                                </div>
                            </div>
                        </div><!-- End testimonial item -->

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    Kami berupaya menciptakan suasana magang yang suportif dan terarah. Peserta
                                    dibimbing secara bertahap agar mampu memahami tugas, meningkatkan keterampilan, dan
                                    siap menghadapi dunia kerja.
                                </p>
                                <div class="profile mt-auto">
                                    <img src="#" class="testimonial-img" alt="">
                                    <h3>M. Aziz</h3>
                                    <h4>Pembimbing</h4>
                                </div>
                            </div>
                        </div><!-- End testimonial item -->

                        <div class="swiper-slide">
                            <div class="testimonial-item">
                                <div class="stars">
                                    <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i
                                        class="bi bi-star-fill"></i>
                                </div>
                                <p>
                                    Program magang ini dirancang untuk memberikan pengalaman belajar yang terarah.
                                    Peserta dibimbing agar mampu memahami alur kerja, meningkatkan keterampilan, serta
                                    membangun sikap profesional selama magang.
                                </p>
                                <div class="profile mt-auto">
                                    <img src="#" class="testimonial-img" alt="">
                                    <h3>M. Abdul Muhshi</h3>
                                    <h4>Pembimbing</h4>
                                </div>
                            </div>
                        </div><!-- End testimonial item -->
                    </div>
                    <div class="swiper-pagination"></div>
                </div>

            </div>

        </section><!-- /Testimonials Section -->

        <!-- Contact Section -->
        <section id="contact" class="contact section">

            <!-- Section Title -->
            <div class="container section-title" data-aos="fade-up">
                <h2>Kontak</h2>
                <p>Apabila Anda memiliki pertanyaan terkait program magang, persyaratan pendaftaran, atau informasi
                    lainnya, silakan menghubungi kami melalui kontak berikut. Tim kami siap membantu dan memberikan
                    informasi yang dibutuhkan.</p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade-up" data-aos-delay="100">

                <div class="row gy-4">

                    <div class="col-lg-8 order-2 order-lg-1" data-aos="fade-up" data-aos-delay="200">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3961.009958159232!2d110.63071009999999!3d-6.889409799999991!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e70ebfac454476d%3A0x2104c8833c6250a7!2sThe%20Central%20Bureau%20of%20Statistics%20Kab.%20Demak!5e0!3m2!1sen!2sid!4v1767840417702!5m2!1sen!2sid"
                            frameborder="0" style="border:0; width: 100%; height: 100%; min-height: 400px;"
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div><!-- End Google Maps -->

                    <div class="col-lg-4 order-1 order-lg-2">
                        <div class="row gy-4">

                            <div class="col-12">
                                <div class="info-item d-flex flex-column justify-content-center align-items-center"
                                    data-aos="fade-up" data-aos-delay="300">
                                    <i class="bi bi-geo-alt"></i>
                                    <h3>Alamat</h3>
                                    <p>Jl. Sultan Hadiwijaya No.23, Krajan, Mangunjiwan, Demak</p>
                                </div>
                            </div><!-- End Info Item -->

                            <div class="col-12">
                                <div class="info-item d-flex flex-column justify-content-center align-items-center"
                                    data-aos="fade-up" data-aos-delay="400">
                                    <i class="bi bi-telephone"></i>
                                    <h3>Telepon</h3>
                                    <p>(0291) 685445</p>
                                </div>
                            </div><!-- End Info Item -->

                            <div class="col-12">
                                <div class="info-item d-flex flex-column justify-content-center align-items-center"
                                    data-aos="fade-up" data-aos-delay="500">
                                    <i class="bi bi-envelope"></i>
                                    <h3>Email</h3>
                                    <p>bps3321@bps.go.id.</p>
                                </div>
                            </div><!-- End Info Item -->

                        </div>
                    </div>

                    <!--
          <div class="col-lg-6">
            <form action="forms/contact.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="400">
              <div class="row gy-4">

                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="Your Name" required="">
                </div>

                <div class="col-md-6 ">
                  <input type="email" class="form-control" name="email" placeholder="Your Email" required="">
                </div>

                <div class="col-md-12">
                  <input type="text" class="form-control" name="subject" placeholder="Subject" required="">
                </div>

                <div class="col-md-12">
                  <textarea class="form-control" name="message" rows="6" placeholder="Message" required=""></textarea>
                </div>

                <div class="col-md-12 text-center">
                  <div class="loading">Loading</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Your message has been sent. Thank you!</div>

                  <button type="submit">Send Message</button>
                </div>

              </div>
            </form>
          </div><-- End Contact Form -->

                </div>

            </div>

        </section><!-- /Contact Section -->

    </main>

    <footer id="footer" class="footer position-relative light-background">

        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-6 footer-about">
                    <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                        <span class="sitename">Mangga Muda</span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p>Jl. Sultan Hadiwijaya No.23</p>
                        <p>Krajan, Mangunjiwan, Demak</p>
                        <p class="mt-3"><strong>Phone:</strong> <span>(0291) 685445</span></p>
                        <p><strong>Email:</strong> <span>bps3321@bps.go.id.</span></p>
                    </div>
                    <div class="social-links d-flex mt-4">
                        <a href="https://x.com/bpskabdemak"><i class="bi bi-twitter-x"></i></a>
                        <a href="https://www.facebook.com/bpsdemak3321"><i class="bi bi-facebook"></i></a>
                        <a href="https://www.instagram.com/bpskabdemak/"><i class="bi bi-instagram"></i></a>
                        <a href="https://www.youtube.com/channel/UCUSLymM56wEiJvc5dBWodeA"><i
                                class="bi bi-youtube"></i></a>
                        <a href="https://www.tiktok.com/@bpskabdemak"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Tautan</h4>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Tentang</a></li>
                        <li><a href="#">Fitur</a></li>
                        <li><a href="#">Benefit</a></li>
                        <li><a href="#">Program</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Pembimbing</a></li>
                        <li><a href="#">Kontak</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Program</h4>
                    <ul>
                        <li><a href="#">Magang Mandiri</a></li>
                        <li><a href="#">Praktek Kerja Lapangan</a></li>
                        <li><a href="#">Pengambilan Data Penelitian</a></li>
                    </ul>
                </div>

                <!-- <div class="col-lg-4 col-md-12 footer-newsletter">
          <h4>Our Newsletter</h4>
          <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
          <form action="forms/newsletter.php" method="post" class="php-email-form">
            <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
            <div class="loading">Loading</div>
            <div class="error-message"></div>
            <div class="sent-message">Your subscription request has been sent. Thank you!</div>
          </form>
        </div> -->

            </div>
        </div>

        <div class="container copyright text-center mt-4">
            <p>© <span>Copyright</span> <strong class="px-1 sitename">Mangga Muda</strong><span>All Rights
                    Reserved</span></p>
            <div class="credits">
                Designed by <a href="#">Magang 2026</a>
            </div>
        </div>

    </footer>

    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('landing-assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('landing-assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('landing-assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('landing-assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('landing-assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('landing-assets/js/main.js') }}"></script>

</body>

</html>