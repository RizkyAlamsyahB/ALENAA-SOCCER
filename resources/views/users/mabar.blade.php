@extends('layouts.app')
@section('content')
<!-- Hero Section -->
<div class="hero-section" style="margin-top: 50px;">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Main Bareng</h1>
            <div class="breadcrumb-wrapper">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="/"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Venues</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
    <!-- Main Content -->
    <div class="main-content">
        <div class="container py-4">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <p class="section-desc mx-auto" style="max-width: 700px;">
                    Komunitas Bola Tanpa Batas
                    Ingin bermain tapi tidak memiliki tim lengkap? Bergabunglah dengan program Main Bareng Alena Soccer!
                    Temukan teman baru, tingkatkan kemampuan, dan nikmati serunya bermain bersama dalam suasana yang
                    menyenangkan dan sportif.
                </p>
            </div>


            <!-- Filter & Sort Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="search-input">
                                <i
                                    class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                <input type="text" class="form-control ps-5" placeholder="Cari event mabar...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option>Semua Level</option>
                                <option>Beginner</option>
                                <option>Intermediate</option>
                                <option>Advanced</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option>Semua Lokasi</option>
                                <option>Jakarta Selatan</option>
                                <option>Jakarta Barat</option>
                                <option>Jakarta Timur</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select">
                                <option>Terbaru</option>
                                <option>Harga Terendah</option>
                                <option>Rating Tertinggi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="result-badge">
                    <span class="badge bg-primary-subtle text-primary rounded-pill">
                        507 event ditemukan
                    </span>
                </div>
                <div class="view-toggle">
                    <button class="btn btn-light rounded-pill active me-2">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="btn btn-light rounded-pill">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Event Grid -->
            <div class="row g-4">
                <!-- Event Card 1 -->
                <div class="col-lg-4">
                    <div class="card event-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <!-- Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <div class="sport-type mb-2">
                                        <span class="badge bg-danger-subtle text-danger rounded-pill">
                                            <i class="fas fa-volleyball-ball me-1"></i>
                                            Badminton
                                        </span>
                                    </div>
                                    <h5 class="card-title mb-1">Otaku Badminton</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="rating me-2">
                                            <i class="fas fa-star text-warning"></i>
                                            <span class="ms-1">5.00</span>
                                        </div>
                                        <span class="level-badge badge bg-light text-dark">
                                            Newbie - Intermediate
                                        </span>
                                    </div>
                                </div>
                                <div class="slots-info">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                                        <i class="fas fa-users me-1"></i>
                                        9/18
                                    </span>
                                </div>
                            </div>

                            <!-- Event Info -->
                            <div class="event-info mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="info-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <span>Min, 12 Jan 2024 • 14:00 - 16:00</span>
                                </div>
                                <div class="d-flex align-items-start">
                                    <div class="info-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <span class="d-block">Lapangan 7 • Gor Badminton Kebd</span>
                                        <small class="text-muted">Kota Jakarta Barat</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-2 border-dashed opacity-50 my-3">

                            <!-- Footer -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-info">
                                    <span class="h5 mb-0 text-danger">Rp30.000</span>
                                    <small class="text-muted">/orang</small>
                                </div>
                                <a href="/detail-mabar" class="btn btn-outline-danger rounded-pill">
                                    Detail
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Event Card 2 & 3 (similar structure) -->
                <!-- ... -->
            </div>

            <!-- Load More -->
            <div class="text-center mt-5">
                <button class="btn btn-outline-danger rounded-pill px-4 py-2">
                    Tampilkan Lebih Banyak
                    <i class="fas fa-chevron-down ms-2"></i>
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Base Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }

        /* Hero Section */
        .hero-section {
    background: linear-gradient(to right, #9e0620, #bb2d3b);
            height: 220px;
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .hero-content {
            color: white;
            text-align: center;
            width: 100%;
        }

        .hero-title {
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .breadcrumb-wrapper {
            display: flex;
            justify-content: center;
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            display: inline-flex;
            margin-bottom: 0;
        }

        .breadcrumb-item {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .breadcrumb-item.active {
            color: white;
            font-weight: 500;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: white;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Intro Section */
        .intro-section {
            background: white;
            padding: 3rem 0;
            text-align: center;
        }

        .intro-content {
            max-width: 800px;
            margin: 0 auto;
        }

        .intro-content h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 1rem;
        }

        .intro-content p {
            font-size: 1.1rem;
            color: #6c757d;
            line-height: 1.6;
        }

        /* Content Wrapper */
        .content-wrapper {
            padding: 3rem 0;
        }

        /* Section Styling */
        section {
            margin-bottom: 4rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .section-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.75rem;
        }

        .section-header p {
            font-size: 1rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Field Cards */
        .fields-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .field-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .field-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .field-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .field-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .field-card:hover .field-image img {
            transform: scale(1.05);
        }

        .field-badges {
            position: absolute;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .badge-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border-radius: 50px;
            padding: 0.35rem 0.75rem;
            font-size: 0.8rem;
        }

        .field-content {
            padding: 1.5rem;
        }

        .field-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #212529;
        }

        .field-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .field-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .field-location i {
            color: #d00f25;
        }

        .field-price {
            text-align: right;
        }

        .field-price .price {
            font-weight: 700;
            color: #d00f25;
            font-size: 1.1rem;
        }

        .field-price .period {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .empty-icon {
            font-size: 3rem;
            color: #d00f25;
            opacity: 0.5;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #212529;
        }

        .empty-state p {
            color: #6c757d;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Membership Plans */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .plan-card {
            position: relative;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .plan-card.bronze .plan-header {
            background: linear-gradient(135deg, #A77044, #CD7F32);
        }

        .plan-card.silver .plan-header {
            background: linear-gradient(135deg, #7B8A8B, #C0C0C0);
        }

        .plan-card.gold .plan-header {
            background: linear-gradient(135deg, #B5903C, #FFD700);
        }

        .popular-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #d00f25;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            z-index: 1;
        }

        .plan-header {
            padding: 2rem;
            color: white;
            text-align: center;
        }

        .plan-type {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .plan-price .price {
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .plan-price .period {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .plan-features {
            padding: 2rem;
        }

        .plan-features ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .plan-features li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
        }

        .plan-features li:last-child {
            border-bottom: none;
        }

        .plan-features i {
            color: #2e7d32;
            font-size: 1.1rem;
            margin-top: 0.1rem;
        }

        .plan-features span {
            font-size: 0.95rem;
            color: #495057;
        }

        .plan-action {
            padding: 0 2rem 2rem;
            text-align: center;
        }

        /* Benefits Section */
        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .benefit-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .benefit-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .benefit-icon {
            width: 80px;
            height: 80px;
            background: rgba(208, 15, 37, 0.1);
            color: #d00f25;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }

        .benefit-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.75rem;
        }

        .benefit-content p {
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        /* FAQ Section */
        .faq-item {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
        }

        .faq-question {
            width: 100%;
            text-align: left;
            padding: 1.5rem;
            background: white;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1rem;
            font-weight: 600;
            color: #212529;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            background: #f8f9fa;
        }

        .faq-question:not(.collapsed) {
            background: #f8f9fa;
            color: #d00f25;
        }

        .faq-question i {
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .faq-question:not(.collapsed) i {
            transform: rotate(180deg);
            color: #d00f25;
        }

        .faq-answer {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .faq-content {
            padding: 1.5rem;
            color: #6c757d;
            font-size: 0.95rem;
        }

        /* Buttons */
        .btn-primary,
        .btn-secondary,
        .btn-disabled {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            font-size: 0.95rem;
            cursor: pointer;
            width: 100%;
        }

        .btn-primary {
            background: #d00f25;
            color: white;
        }

        .btn-primary:hover {
            background: #b00d1f;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(208, 15, 37, 0.3);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #212529;
            border: 1px solid #dee2e6;
        }

        .btn-secondary:hover {
            background: #dee2e6;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-disabled {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
        }

        .btn-disabled:hover {
            transform: none;
            box-shadow: none;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {

            .plans-grid,
            .benefits-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                height: 180px;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .intro-content h2 {
                font-size: 1.8rem;
            }

            .intro-content p {
                font-size: 1rem;
            }

            .section-header h2 {
                font-size: 1.5rem;
            }

            .fields-grid,
            .plans-grid,
            .benefits-grid {
                grid-template-columns: 1fr;
            }

            .plan-header,
            .plan-features,
            .plan-action {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.5rem;
            }

            .breadcrumb {
                padding: 0.6rem 1rem;
            }

            .breadcrumb-item {
                font-size: 0.8rem;
            }

            .field-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .field-price {
                text-align: left;
            }

            .benefit-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
