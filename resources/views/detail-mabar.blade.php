@extends('layouts.app')
@section('content')
    <style>
        :root {
            --primary-color: #9E0620;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            padding-top: 72px;
        }


        .sport-badge {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
        }

      /* Breadcrumb Styles */
.breadcrumb-wrapper {
    background: linear-gradient(to right, var(--primary-color), #bb2d3b);
    padding: 1rem 0;
    color: white;
}

.custom-breadcrumb {
    display: flex;
    flex-wrap: wrap;
    padding: 0;
    margin: 0;
    list-style: none;
    gap: 1rem;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
}

.breadcrumb-link:hover {
    color: white;
}

.breadcrumb-item.active {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    background: rgba(255, 255, 255, 0.1);
    padding: 0.25rem 1rem;
    border-radius: 50px;
}

/* Header Section Styles */
.header-section {
    background: var(--primary-color);
    color: white;
    padding: 2.5rem 0;
}

.sport-badge {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.875rem;
}

.header-title {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
}

.member-list {
    display: flex;
    align-items: center;
    margin-right: 1rem;
}

.member-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid white;
    margin-right: -8px;
    object-fit: cover;
    background: #fff;
}

.more-members {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
}

.slots-badge {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.875rem;
}

.header-info {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.9rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .header-title {
        font-size: 1.5rem;
    }

    .member-avatar {
        width: 32px;
        height: 32px;
    }

    .breadcrumb-item span {
        font-size: 0.9rem;
    }
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

    <style>
        .facility-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .facility-badge:hover {
            background: rgba(158, 6, 32, 0.05);
            transform: translateX(5px);
        }

        .facility-badge i {
            color: var(--primary-color);
            font-size: 1.25rem;
            transition: all 0.3s ease;
        }

        .facility-badge:hover i {
            transform: scale(1.2);
        }

        .facility-badge span {
            color: #495057;
            font-weight: 500;
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

        .payment-card {
            background: white;
            border-radius: 16px;
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

        .btn-outline-danger {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-danger:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .map-container {
            height: 300px;
            border-radius: 12px;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .facility-badge {
                padding: 0.75rem;
            }

            .payment-card {
                margin-top: 1rem;
            }

            .info-icon {
                width: 28px;
                height: 28px;
                font-size: 0.9rem;
            }
        }
    </style>


<!-- Breadcrumb -->
<div class="breadcrumb-wrapper">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="custom-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/" class="breadcrumb-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/main-bareng" class="breadcrumb-link">
                        <i class="fas fa-users"></i>
                        <span>Main Bareng</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-volleyball-ball"></i>
                    <span>OTAKU BADMINTON : HANJOY SPORT</span>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Header Section -->
<div class="header-section">
    <div class="container">
        <div class="sport-badge mb-3">
            <i class="fas fa-volleyball-ball me-2"></i>
            <span>Badminton • Newbie - Intermediate</span>
        </div>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h1 class="header-title mb-3">Otaku Badminton : Hanjoy Sport</h1>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <div class="member-list d-flex align-items-center">
                        <img src="/api/placeholder/40/40" class="member-avatar" alt="AD">
                        <img src="/api/placeholder/40/40" class="member-avatar" alt="E">
                        <img src="/api/placeholder/40/40" class="member-avatar" alt="AP">
                        <div class="member-avatar more-members d-flex align-items-center justify-content-center">
                            <span>+2</span>
                        </div>
                    </div>
                    <div class="slots-badge">
                        <i class="fas fa-users me-2"></i>
                        <span>6/8 Bergabung!</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="header-info">
                    <div class="info-item mb-2">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span>Sel, 14 Jan 2025 • 19:00 - 21:00</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <span>Wins 63 Badminton • Jakarta Selatan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Main Content -->
    <div class="container py-5">
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
                                    <i class="fas fa-users"></i>
                                </div>
                                <span>8 slot tersedia</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="info-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <span>Level: Newbie - Intermediate</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="info-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <span>Durasi: 2 jam</span>
                            </div>
                        </div>

                        <p class="mb-3">Fun game untuk semua level! Mari bergabung dan bersenang-senang bersama.</p>
                        <p class="text-danger mb-0">*Pembatalan H-1 tidak dapat direfund</p>
                    </div>
                </div>

                <!-- Jadwal Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Jadwal Main</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Tanggal</small>
                                        <span>Sel, 14 Jan 2025</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Waktu</small>
                                        <span>19:00 - 21:00 WIB</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fasilitas Section -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Fasilitas</h5>
                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <div class="facility-badge">
                                    <i class="fas fa-parking"></i>
                                    <span>Parkir Gratis</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="facility-badge">
                                    <i class="fas fa-wifi"></i>
                                    <span>WiFi Gratis</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="facility-badge">
                                    <i class="fas fa-shower"></i>
                                    <span>Kamar Mandi</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="facility-badge">
                                    <i class="fas fa-tshirt"></i>
                                    <span>Ruang Ganti</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="facility-badge">
                                    <i class="fas fa-store"></i>
                                    <span>Mini Store</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="facility-badge">
                                    <i class="fas fa-first-aid"></i>
                                    <span>P3K</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Section -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="mb-4">Lokasi</h5>
                        <p class="mb-3">Jl. Sma 63 No. 6 Petukangan Utara, Pesanggrahan Jakarta Selatan</p>
                        <div class="map-container mb-3">
                            <iframe src="https://www.google.com/maps/embed?..." width="100%" height="100%"
                                style="border:0;" allowfullscreen="" loading="lazy">
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
                <div class="payment-card shadow-sm sticky-top" style="top: 90px;">
                    <h5 class="mb-4">Detail Pembayaran</h5>

                    <!-- Price Info -->
                    <div class="mb-4">
                        <div class="payment-option active mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentOption" id="online"
                                    checked>
                                <label class="form-check-label" for="online">Pembayaran Online</label>
                            </div>
                        </div>
                        <div class="payment-option mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentOption" id="cash">
                                <label class="form-check-label" for="cash">Pembayaran Tunai</label>
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Biaya Main</span>
                            <span class="fw-bold">Rp30.000</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Biaya Admin</span>
                            <span class="fw-bold">Rp1.000</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold text-danger">Rp31.000</span>
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
@endsection
