@extends('layouts.app')
@section('content')
    <!-- Event Main Bareng Section -->
    <!-- Main Content -->
    <div class="main-content">
        <div class="container py-4">
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
        /* Root Variables */
        :root {
            --primary-color: #9E0620;
            --bg-light: #f8f9fa;
            --border-light: #e9ecef;
            --border-lighter: #dee2e6;
            --text-muted: #6c757d;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Layout & Container Styles */
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-light);
            padding-top: 72px;
        }

        .main-content {
            background: var(--bg-light);
            min-height: 100vh;
            padding-top: 0;
        }

        /* Card Base Styles */
        .card {
            background: #ffffff;
            transition: all 0.3s ease;
            border: none !important;
        }

        .card.border-0.shadow-sm {
            box-shadow: var(--shadow-sm) !important;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md) !important;
        }

        /* Form Controls */
        .search-input {
            position: relative;
        }

        .form-control,
        .form-select {
            border: 1.5px solid var(--border-light);
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(158, 6, 32, 0.1);
        }

        /* Button Styles */
        .btn {
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-outline-danger {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-danger:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-light {
            background: var(--bg-light);
            border: 1.5px solid var(--border-light);
        }

        .btn-light.active {
            background: var(--border-light);
            border-color: var(--border-lighter);
        }

        /* Badge Styles */
        .badge {
            font-weight: 500;
        }

        .sport-type .badge {
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
        }

        .level-badge {
            font-size: 0.85rem;
            padding: 0.35rem 0.75rem;
            background: var(--bg-light);
            border: 1px solid var(--border-light);
        }

        .result-badge .badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            background: rgba(158, 6, 32, 0.1);
            color: var(--primary-color);
        }

        /* Icon Styles */
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

        /* Avatar Styles */
        .avatar-group {
            display: flex;
            align-items: center;
        }

        .avatar-group .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid white;
            margin-left: -8px;
        }

        .avatar-group .avatar:first-child {
            margin-left: 0;
        }

        /* Border Utilities */
        .border-dashed {
            border-style: dashed !important;
        }

        /* Price Info */
        .price-info .h5 {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Search Section */
        .search-section {
            box-shadow: var(--shadow-sm);
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {

            .form-control,
            .form-select {
                font-size: 0.9rem;
                padding: 0.5rem 0.75rem;
            }

            .sport-type .badge {
                font-size: 0.8rem;
                padding: 0.4rem 0.75rem;
            }

            .info-icon {
                width: 28px;
                height: 28px;
                font-size: 0.9rem;
            }

            .avatar-group .avatar {
                width: 28px;
                height: 28px;
            }
        }

        /* Custom Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
