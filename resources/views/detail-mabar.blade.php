<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTAKU BADMINTON : HANJOY SPORT - SportVue</title>

    <!-- CSS & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #9E0620;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            padding-top: 72px;
        }

        .breadcrumb-section {
            background: var(--primary-color);
            padding: 1rem 0;
            color: white;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: white;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.8);
        }

        .header-section {
            background: var(--primary-color);
            color: white;
            padding: 2rem 0;
        }

        .sport-badge {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
        }

        .member-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid white;
        }

        .slots-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 1rem;
            border-radius: 50px;
            color: white;
        }

        .payment-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
        }

        .payment-option {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-option:hover,
        .payment-option.active {
            border-color: var(--primary-color);
            background: rgba(158, 6, 32, 0.05);
        }

        .info-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(158, 6, 32, 0.1);
            color: var(--primary-color);
            margin-right: 0.75rem;
        }

        .map-container {
            height: 300px;
            border-radius: 12px;
            overflow: hidden;
        }

        .host-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <!-- Breadcrumb -->
    <div class="breadcrumb-section">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/main-bareng">Main Bareng</a></li>
                    <li class="breadcrumb-item active">OTAKU BADMINTON : HANJOY SPORT</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="sport-badge mb-3">
                <i class="fas fa-volleyball-ball me-2"></i>
                Badminton • Newbie - Intermediate
            </div>

            <!-- Event Title & Members -->
            <div class="row align-items-center mb-4">
                <div class="col-lg-8">
                    <h1 class="h2 mb-3">Otaku Badminton : Hanjoy Sport</h1>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center me-4">
                            <img src="/api/placeholder/40/40" class="member-avatar me-2" alt="AD">
                            <img src="/api/placeholder/40/40" class="member-avatar me-2" alt="E">
                            <img src="/api/placeholder/40/40" class="member-avatar me-2" alt="AP">
                        </div>
                        <div class="slots-badge">
                            6/8 Bergabung!
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="row g-4">
            <!-- Left Content -->
            <div class="col-lg-8">
                <!-- About Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Tentang Mabar</h5>
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="info-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <span>1 lapangan 2 jam ( Wins 63 )</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="info-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <span>Fun play Newbie - intermediate</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="info-icon">
                                    <i class="fas fa-volleyball-ball"></i>
                                </div>
                                <span>Sudah termasuk Shuttlecock</span>
                            </div>
                        </div>

                        <p class="mb-3">
                            Olahraga sambil nyari relasi 🙂
                        </p>
                        <p class="text-danger mb-0">
                            daftar dan tidak datang tampa info apa2 akan di banned.
                        </p>
                        <p class="mb-0">
                            Online payment 30k ( WAJIB REVIEW dan Join Community di AYO)
                        </p>
                    </div>
                </div>

                <!-- Location Section -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Lokasi</h5>
                        <p class="mb-3">Jl. Sma 63 No. 6 Petukangan Utara, Pesanggrahan Jakarta Selatan</p>

                        <!-- Map Container -->
                        <div class="map-container mb-3">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.0269457788045!2d112.76238777575094!3d-7.35087059265795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7e52a24e495d7%3A0x243c2e1056011f20!2sAlena%20Soccer!5e0!3m2!1sen!2sid!4v1735891963050!5m2!1sen!2sid"
                                width="100%"
                                height="100%"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy">
                            </iframe>
                        </div>

                        <a href="#" class="btn btn-outline-danger rounded-pill">
                            <i class="fas fa-location-arrow me-2"></i>
                            Petunjuk Jalan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Payment Card -->
                <div class="payment-card shadow-sm sticky-top" style="top: 90px;">
                    <h5 class="mb-4">Pilih metode pembayaran:</h5>

                    <!-- Payment Options -->
                    <div class="payment-option active mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentOption" id="online" checked>
                            <label class="form-check-label" for="online">
                                Online
                            </label>
                        </div>
                    </div>

                    <div class="payment-option mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="paymentOption" id="tunai">
                            <label class="form-check-label" for="tunai">
                                Tunai
                            </label>
                        </div>
                    </div>

                    <!-- Price Info -->
                    <div class="mb-2">
                        <h4 class="mb-1">Rp30.000 <small class="text-muted">/peserta</small></h4>
                        <small class="text-danger">Hanya tersisa 2 slot!</small>
                    </div>

                    <!-- Date & Location -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-calendar-alt text-muted me-2"></i>
                            <div>
                                <small class="text-muted d-block">Waktu & Tanggal</small>
                                <span>Sel, 14 Jan 2025 • 19:00 - 21:00</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <i class="fas fa-map-marker-alt text-muted me-2 mt-1"></i>
                            <div>
                                <small class="text-muted d-block">Lapangan</small>
                                <span>Lapangan 1 • Wins 63 Badminton</span>
                                <small class="d-block">Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta</small>
                            </div>
                        </div>
                    </div>

                    <!-- Host Info -->
                    <div class="mb-4">
                        <div class="d-flex align-items-center">
                            <img src="/api/placeholder/48/48" alt="Host" class="rounded-circle me-3">
                            <div>
                                <div class="d-flex align-items-center mb-1">
                                    <h6 class="mb-0 me-2">Hanjoy Sport</h6>
                                    <span class="host-badge">Penyelenggara</span>
                                </div>
                                <small class="text-muted">Dibuat oleh Abay</small>
                            </div>
                        </div>
                    </div>

                    <!-- Join Button -->
                    <button class="btn btn-danger w-100 rounded-pill py-2">
                        Gabung Main Bareng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
