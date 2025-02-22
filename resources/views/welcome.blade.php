@extends('layouts.app')
@section('content')

    <style>

        @keyframes slideLeft {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-300%);
                /* 3 slides */
            }
        }

        .hero-bg img {
            filter: brightness(0.6);
            /* Membuat gambar sedikit lebih gelap */
        }

        .overlay {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7));
        }

        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .btn-danger {
            background-color: #9E0620;
            border: none;
            border-radius: 4px;
            /* Sudut yang lebih tajam */
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #8a051c;
            transform: translateY(-2px);
        }

        .bg-danger {
            background-color: #8a051c;
        }

        .lead {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .tournament-card {
            transition: transform 0.3s ease;
        }

        .tournament-card:hover {
            transform: translateY(-5px);
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: auto;
            padding: 0 10px;
        }

        .testimonial-item {
            min-height: 200px;
        }

        .footer {
            background-color: #212529;
        }

        .footer-links a {
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: white !important;
            padding-left: 5px;
        }

        .btn-outline-light {
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            transform: translateY(-3px);
        }

        .footer-contact li {
            transition: all 0.3s ease;
        }

        .footer-contact li:hover {
            transform: translateX(5px);
        }

        .hover-text-white {
            transition: color 0.3s ease;
        }

        .hover-text-white:hover {
            color: white !important;
        }
    </style>




    <!-- Hero Section -->
    <section class="hero position-relative vh-100 d-flex align-items-center">
        <!-- Hero Background -->
        <div class="hero-bg position-absolute w-100 h-100">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/225f207f34ecb422ea74c38dd5016adc852e34aafdae3247df704fa28c8f307d"
                class="w-100 h-100 object-fit-cover" alt="Indoor Sport Field">
            <div class="overlay position-absolute top-0 start-0 w-100 h-100"></div>
        </div>

        <!-- Hero Content -->
        <div class="container position-relative z-3">
            <div class="col-lg-12 mx-auto text-left">
                <!-- Main Text -->
                <h1 class="display-1 fw-bold text-white  text-shadow">
                    ALENA SOCCER
                </h1>
                <h1 class="display-1 fw-bold text-white mb-3 text-shadow">
                    SUPER SPORT FUTSAL
                </h1>

                <p class="lead text-white mb-4 text-shadow ">
                    Platform all-in-one untuk sewa lapangan, cari lawan sparring, atau cari kawan main bareng. Olahraga
                    makin mudah dan menyenangkan!
                </p>

                <!-- Tombol CTA Sederhana -->
                <a href="#booking" class="btn btn-danger btn-lg px-4 ">
                    Cek Ketersediaan
                </a>

            </div>
        </div>
    </section>


    <!-- Booking Section -->
    <section id="booking" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">Pesan Lapangan Anda</h2>
                <p class="text-muted">Pilih lapangan yang Anda inginkan dan cek ketersediaan secara real-time</p>

            </div>

            <div class="row g-4">
                <!-- Lapangan A -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-lg rounded-4 hover-scale">
                        <!-- Status Badge -->
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                <i class="fas fa-circle text-success me-1"></i>Tersedia Sekarang
                            </span>
                        </div>

                        <div class="position-relative">
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;"
                                alt="Lapangan A">

                            <!-- Overlay Info -->
                            <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white"
                                style="background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0));">
                                <h3 class="h4 mb-0">Lapangan A</h3>
                                <p class="mb-0"><small>Lapangan Indoor • 5v5</small></p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="mb-3">
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3">
                                            <i class="fas fa-ruler-combined text-muted"></i>
                                            <small class="d-block text-muted">25x15m</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3">
                                            <i class="fas fa-volleyball-ball text-muted"></i>
                                            <small class="d-block text-muted">Indoor</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3">
                                            <i class="fas fa-star text-muted"></i>
                                            <small class="d-block text-muted">4.8/5</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h5 mb-0">Rp 150K</span>
                                    <small class="text-muted">/jam</small>
                                </div>
                                <a href="/maincourt" class="btn btn-danger rounded-pill px-4">
                                    Pesan Sekarang
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lapangan B -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-lg rounded-4 hover-scale">
                        <!-- Status Badge -->
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">
                                <i class="fas fa-clock text-warning me-1"></i>Slot Terbatas
                            </span>
                        </div>

                        <div class="position-relative">
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;"
                                alt="Lapangan B">

                            <!-- Overlay Info -->
                            <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white"
                                style="background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0));">
                                <h3 class="h4 mb-0">Lapangan B</h3>
                                <p class="mb-0"><small>Lapangan Outdoor • 7v7</small></p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="mb-3">
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3">
                                            <i class="fas fa-ruler-combined text-muted"></i>
                                            <small class="d-block text-muted">30x20m</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3">
                                            <i class="fas fa-sun text-muted"></i>
                                            <small class="d-block text-muted">Outdoor</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3">
                                            <i class="fas fa-star text-muted"></i>
                                            <small class="d-block text-muted">4.6/5</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h5 mb-0">Rp 200K</span>
                                    <small class="text-muted">/jam</small>
                                </div>
                                <button class="btn btn-danger rounded-pill px-4">
                                    Pesan Sekarang
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lapangan C -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-lg rounded-4 hover-scale opacity-75">
                        <!-- Status Badge -->
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                                <i class="fas fa-ban text-danger me-1"></i>Penuh Terisi
                            </span>
                        </div>

                        <div class="position-relative">
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;"
                                alt="Lapangan C">

                            <!-- Overlay Info -->
                            <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white"
                                style="background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0));">
                                <h3 class="h4 mb-0">Lapangan C</h3>
                                <p class="mb-0"><small>Lapangan Premium • 11v11</small></p>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="mb-3">
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3">
                                            <i class="fas fa-ruler-combined text-muted"></i>
                                            <small class="d-block text-muted">40x20m</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3">
                                            <i class="fas fa-medal text-muted"></i>
                                            <small class="d-block text-muted">Premium</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-light rounded-3">
                                            <i class="fas fa-star text-muted"></i>
                                            <small class="d-block text-muted">4.9/5</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h5 mb-0">Rp 300K</span>
                                    <small class="text-muted">/jam</small>
                                </div>
                                <button class="btn btn-secondary rounded-pill px-4" disabled>
                                    Tidak Tersedia
                                    <i class="fas fa-ban ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Booking -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 bg-light rounded-4 p-4">
                        <div class="row g-4 text-center">
                            <!-- Jam Operasional -->
                            <div class="col-md-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-clock text-danger fa-2x me-3"></i>
                                    <div class="text-start">
                                        <h5 class="mb-1">Jam Operasional</h5>
                                        <p class="mb-0 text-muted">08:00 - 22:00</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking di Muka -->
                            <div class="col-md-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-calendar-check text-danger fa-2x me-3"></i>
                                    <div class="text-start">
                                        <h5 class="mb-1">Booking di Muka</h5>
                                        <p class="mb-0 text-muted">Hingga 2 minggu sebelumnya</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pembayaran Aman -->
                            <div class="col-md-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-shield-alt text-danger fa-2x me-3"></i>
                                    <div class="text-start">
                                        <h5 class="mb-1">Pembayaran Aman</h5>
                                        <p class="mb-0 text-muted">100% terjamin keamanannya</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Dukungan 24/7 -->
                            <div class="col-md-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-headset text-danger fa-2x me-3"></i>
                                    <div class="text-start">
                                        <h5 class="mb-1">Dukungan 24/7</h5>
                                        <p class="mb-0 text-muted">Selalu siap membantu Anda</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Equipment Section -->
    <section id="equipment" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">Equipment Rental</h2>
                <p class="text-muted">Quality sports equipment for your game</p>
            </div>

            <div class="row g-4">
                <!-- Jersey Set -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg rounded-4 hover-scale h-100">
                        <div class="position-relative">
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                class="card-img-top rounded-top-4" style="height: 200px; object-fit: cover;"
                                alt="Jersey Set">

                            <!-- Stock Badge -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i>In Stock
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="h4 mb-0">Jersey Set</h3>
                                <div class="text-end">
                                    <span class="h5 text-danger mb-0">Rp50K</span>
                                    <small class="text-muted">/day</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-tshirt text-muted me-2"></i>
                                            <small class="text-muted">All Sizes</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-layer-group text-muted me-2"></i>
                                            <small class="text-muted">Full Set</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-shield-alt text-muted me-2"></i>
                                            <small class="text-muted">Clean & Fresh</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-sync-alt text-muted me-2"></i>
                                            <small class="text-muted">Daily Wash</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <a href="/product-rental" class="btn btn-danger rounded-pill w-100 py-2">
                                Rent Now
                                <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Soccer Ball -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg rounded-4 hover-scale h-100">
                        <div class="position-relative">
                            <img src="assets/ball.avif" class="card-img-top rounded-top-4"
                                style="height: 200px; object-fit: cover;" alt="Soccer Ball">

                            <!-- Stock Badge -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i>In Stock
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="h4 mb-0">Soccer Ball</h3>
                                <div class="text-end">
                                    <span class="h5 text-danger mb-0">Rp30K</span>
                                    <small class="text-muted">/day</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-certificate text-muted me-2"></i>
                                            <small class="text-muted">Official Size</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-star text-muted me-2"></i>
                                            <small class="text-muted">Premium</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-muted me-2"></i>
                                            <small class="text-muted">Match Quality</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-pump-soap text-muted me-2"></i>
                                            <small class="text-muted">Sanitized</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-danger rounded-pill w-100 py-2">
                                Rent Now
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Soccer Shoes -->
                <div class="col-md-4">
                    <div class="card border-0 shadow-lg rounded-4 hover-scale h-100">
                        <div class="position-relative">
                            <img src="assets/shoes.avif" class="card-img-top rounded-top-4"
                                style="height: 200px; object-fit: cover;" alt="Soccer Shoes">

                            <!-- Stock Badge -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">
                                    <i class="fas fa-clock me-1"></i>Limited Stock
                                </span>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="h4 mb-0">Soccer Shoes</h3>
                                <div class="text-end">
                                    <span class="h5 text-danger mb-0">Rp40K</span>
                                    <small class="text-muted">/day</small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-ruler text-muted me-2"></i>
                                            <small class="text-muted">Size 39-45</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-shoe-prints text-muted me-2"></i>
                                            <small class="text-muted">Studs/Turf</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-spray-can text-muted me-2"></i>
                                            <small class="text-muted">Deodorized</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-shield-alt text-muted me-2"></i>
                                            <small class="text-muted">Sanitized</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-danger rounded-pill w-100 py-2">
                                Rent Now
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rental Information -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card border-0 bg-light rounded-4 p-4">
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock text-danger fa-2x me-3"></i>
                                    <div>
                                        <h5 class="mb-1">24H Rental</h5>
                                        <p class="mb-0 text-muted">Full day usage</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-shield-alt text-danger fa-2x me-3"></i>
                                    <div>
                                        <h5 class="mb-1">Clean & Safe</h5>
                                        <p class="mb-0 text-muted">Sanitized daily</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-undo text-danger fa-2x me-3"></i>
                                    <div>
                                        <h5 class="mb-1">Easy Return</h5>
                                        <p class="mb-0 text-muted">No hassle return</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-medal text-danger fa-2x me-3"></i>
                                    <div>
                                        <h5 class="mb-1">Quality Items</h5>
                                        <p class="mb-0 text-muted">Premium brands</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container text-center">
            <h2 class="text-primary fw-bold mb-4">Jasa Fotografer Futsal</h2>
            <p class="text-muted mb-5">Abadikan momen seru pertandingan futsal Anda dengan fotografer profesional.</p>

            <div class="row g-4">
                <!-- Dummy Photographer Service 1 -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <img src="https://via.placeholder.com/400" class="card-img-top" alt="Fotografer 1">
                        <div class="card-body">
                            <h5 class="card-title">Paket Standard</h5>
                            <p class="card-text">Dokumentasi pertandingan selama 1 jam.</p>
                            <p class="fw-bold">Rp 500.000</p>
                            <a href="#" class="btn btn-primary btn-modern">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>

                <!-- Dummy Photographer Service 2 -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <img src="https://via.placeholder.com/400" class="card-img-top" alt="Fotografer 2">
                        <div class="card-body">
                            <h5 class="card-title">Paket Premium</h5>
                            <p class="card-text">Dokumentasi full pertandingan + editing profesional.</p>
                            <p class="fw-bold">Rp 800.000</p>
                            <a href="#" class="btn btn-primary btn-modern">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>

                <!-- Dummy Photographer Service 3 -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <img src="https://via.placeholder.com/400" class="card-img-top" alt="Fotografer 3">
                        <div class="card-body">
                            <h5 class="card-title">Paket VIP</h5>
                            <p class="card-text">Dokumentasi eksklusif + album cetak.</p>
                            <p class="fw-bold">Rp 1.200.000</p>
                            <a href="#" class="btn btn-primary btn-modern">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Community Section -->
    <section id="community" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">Community</h2>
                <p class="text-muted">Join our growing community of sports enthusiasts</p>
            </div>

            <div class="row g-4">
                <!-- Testimonials Card -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg rounded-4 h-100">
                        <div class="card-body p-4">
                            <h3 class="h4 mb-4">
                                <i class="fas fa-quote-left text-danger me-2"></i>
                                What Our Members Say
                            </h3>

                            <!-- Testimonial Carousel -->
                            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <!-- Testimonial 1 -->
                                    <div class="carousel-item active">
                                        <div class="testimonial-item">
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/2290ec8fa1d076a31ffece3da471c470b91bfd20a12a271551ae12f28bf93760"
                                                    class="rounded-circle" width="60" height="60"
                                                    alt="John Doe">
                                                <div>
                                                    <h4 class="h5 mb-1">John Doe</h4>
                                                    <div class="text-warning">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="mb-0 text-muted fst-italic">"Amazing facilities and great
                                                service! The courts are always well-maintained and the staff is
                                                incredibly helpful. Best sports venue in the area!"</p>
                                        </div>
                                    </div>

                                    <!-- Testimonial 2 -->
                                    <div class="carousel-item">
                                        <div class="testimonial-item">
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/2290ec8fa1d076a31ffece3da471c470b91bfd20a12a271551ae12f28bf93760"
                                                    class="rounded-circle" width="60" height="60"
                                                    alt="Jane Smith">
                                                <div>
                                                    <h4 class="h5 mb-1">Jane Smith</h4>
                                                    <div class="text-warning">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star-half-alt"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="mb-0 text-muted fst-italic">"The membership benefits are
                                                fantastic! Love the community events and tournaments. It's more than
                                                just a sports facility - it's a community."</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Carousel Controls -->
                                <div class="d-flex justify-content-between mt-4">
                                    <button class="btn btn-sm btn-outline-danger rounded-circle"
                                        data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-circle"
                                        data-bs-target="#testimonialCarousel" data-bs-slide="next">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tournament Card -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg rounded-4 h-100">
                        <div class="card-body p-4">
                            <h3 class="h4 mb-4">
                                <i class="fas fa-trophy text-danger me-2"></i>
                                Upcoming Tournament
                            </h3>

                            <div class="tournament-card bg-danger bg-opacity-10 rounded-4 p-4 mb-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h4 class="h3 mb-2">SportVue Cup 2025</h4>
                                        <p class="mb-0 text-muted">
                                            <i class="fas fa-calendar-alt me-2"></i>March 15-20, 2025
                                        </p>
                                    </div>
                                    <span class="badge bg-danger">Registrations Open</span>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="bg-white rounded-3 p-3 text-center">
                                            <i class="fas fa-users text-danger mb-2"></i>
                                            <h5 class="h6 mb-1">32 Teams</h5>
                                            <small class="text-muted">Participating</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-white rounded-3 p-3 text-center">
                                            <i class="fas fa-award text-danger mb-2"></i>
                                            <h5 class="h6 mb-1">Rp 10M</h5>
                                            <small class="text-muted">Prize Pool</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <a href="/mabar" class="btn btn-danger rounded-pill">
                                        Register Now
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Tournament Features -->
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-medal text-danger me-2"></i>
                                        <span>Professional Referees</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-video text-danger me-2"></i>
                                        <span>Live Streaming</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-first-aid text-danger me-2"></i>
                                        <span>Medical Support</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-camera text-danger me-2"></i>
                                        <span>Professional Coverage</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="footer position-relative bg-dark text-white">

        <!-- Main Footer Content -->
        <div class="py-5">
            <div class="container">
                <div class="row g-4">
                    <!-- Brand Section -->
                    <div class="col-lg-4 mb-4">
                        <div class="pe-lg-5">
                            <div class="d-flex align-items-center mb-4">

                                <span class="fw-bold fs-4">
                                    ALENA<span class="text-white">
                                        S<img
                                            src="https://cdn.builder.io/api/v1/image/assets/TEMP/3bc3f968d66dd0c368130525f00d42ec550c3ea8f6304c68cbb117fa6eb8dc08"
                                            width="30" height="30" class=""
                                            alt="Alena Soccer Logo">CCER
                                    </span>
                                </span>
                            </div>
                            <p class="text-white-50 mb-4">Your premier destination for sports facility bookings and
                                equipment rentals. Experience quality service and premium facilities.</p>
                            <div class="d-flex gap-3">
                                <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>


                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-4">
                        <h5 class="text-white mb-4">Quick Links</h5>
                        <ul class="list-unstyled footer-links">
                            <li class="mb-2">
                                <a href="/about" class="text-white-50 text-decoration-none hover-text-white">About
                                    Us</a>
                            </li>
                            <li class="mb-2">
                                <a href="/fields" class="text-white-50 text-decoration-none hover-text-white">Our
                                    Fields</a>
                            </li>
                            <li class="mb-2">
                                <a href="/membership"
                                    class="text-white-50 text-decoration-none hover-text-white">Membership</a>
                            </li>
                            <li class="mb-2">
                                <a href="/equipment"
                                    class="text-white-50 text-decoration-none hover-text-white">Equipment Rental</a>
                            </li>
                            <li class="mb-2">
                                <a href="/contact" class="text-white-50 text-decoration-none hover-text-white">Contact
                                    Us</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Information -->
                    <div class="col-lg-3 col-md-4">
                        <h5 class="text-white mb-4">Contact Us</h5>
                        <ul class="list-unstyled footer-contact">
                            <li class="d-flex align-items-center mb-3">
                                <div class=" p-2  me-3">
                                    <i class="fas fa-phone-alt text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-white-50">Phone</p>
                                    <a href="https://wa.link/0qvfmn" class="text-white text-decoration-none">+62 8784
                                        0177 803</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class=" p-2 rounded-circle me-3">
                                    <i class="fas fa-envelope text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-white-50">Email</p>
                                    <a href="mailto:info@sportvue.com"
                                        class="text-white text-decoration-none">info@sportvue.com</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div class=" p-2 rounded-circle me-3">
                                    <i class="fas fa-map-marker-alt text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-white-50">Location</p>
                                    <span class="text-white">Jakarta, Indonesia</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Map Section -->
                    <div class="col-lg-3 col-md-4">
                        <h5 class="text-white mb-4">Find Us</h5>
                        <div class="rounded-3 overflow-hidden">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.0269457788045!2d112.76238777575094!3d-7.35087059265795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7e52a24e495d7%3A0x243c2e1056011f20!2sAlena%20Soccer!5e0!3m2!1sen!2sid!4v1735891963050!5m2!1sen!2sid"
                                width="100%" height="200" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div class="mt-5 pt-4 border-top border-secondary">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="text-white-50 mb-md-0">© 2024 SportVue. All rights reserved.</p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-md-end align-items-center">
                                <span class="text-white-50 me-3">Payment Partners:</span>
                                <div class="d-flex gap-3">
                                    <i class="fab fa-cc-visa fs-3 text-white-50"></i>
                                    <i class="fab fa-cc-mastercard fs-3 text-white-50"></i>
                                    <i class="fab fa-cc-paypal fs-3 text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating WhatsApp Button -->
        <a href="https://wa.me/628784017803"
            class="btn btn-success rounded-circle position-fixed bottom-0 end-0 m-4 shadow-lg d-flex align-items-center justify-content-center"
            style="width: 60px; height: 60px; z-index: 1000;">
            <img src="assets/whatsapp.png" width="70">
        </a>
    </footer>

    <!-- Floating Chat Button -->
    <button id="chat-button"
        class="btn   position-fixed bottom-0 end-0 m-4  d-flex align-items-center justify-content-center"
        style="width: 60px; height: 60px;">
        <img src="assets/whatsapp.png" width="70">
    </button>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>

@endsection
