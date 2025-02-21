<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Main Bareng - SportVue</title>

    <!-- CSS & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


</head>

<body>
    @include('partials.navbar')

    <!-- Event Main Bareng Section -->
    <div class="main-content"style="padding-top: 0;"> <!-- Fixed navbar spacing -->


        <!-- Main Content -->
        <div class="container py-4">
            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="mb-0 text-muted">Menampilkan 507 event mabar</p>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-2">Urutkan:</span>
                    <select class="form-select form-select-sm" style="width: auto;">
                        <option>Waktu dan Tanggal</option>
                        <option>Harga Terendah</option>
                        <option>Rating Tertinggi</option>
                    </select>
                </div>
            </div>

            <!-- Team Grid -->
            <div class="row g-4">
                <!-- Team Card 1 -->
                <div class="col-lg-4">
                    <div class="card team-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1">Otaku Badminton</h5>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span class="me-2">5.00</span>
                                        <span class="badge bg-light text-dark">Newbie - Intermediate</span>
                                    </div>
                                </div>
                                <span class="badge bg-danger-subtle text-danger rounded-pill">
                                    <i class="fas fa-users me-1"></i>9/18
                                </span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span>Min, 12 Jan 2024 • 14:00 - 16:00</span>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-map-marker-alt text-muted me-2 mt-1"></i>
                                    <span>Lapangan 7 • Gor Badminton Kebd, Kota Jakarta Barat</span>
                                </div>
                            </div>

                            <!-- Garis putus-putus -->
                            <hr class="border-2 border-dashed opacity-50 my-3">

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="avatar-group">
                                    <img src="/api/placeholder/32/32" class="avatar" alt="Member 1">
                                    <img src="/api/placeholder/32/32" class="avatar" alt="Member 2">
                                    <img src="/api/placeholder/32/32" class="avatar" alt="Member 3">
                                    <div class="avatar bg-light d-flex align-items-center justify-content-center">
                                        <small class="text-muted">+5</small>
                                    </div>
                                </div>
                                <a href="/detail-mabar" class="btn btn-outline-danger rounded-pill px-3">
                                    Detail
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    /* Style untuk border dashed */
                    .border-dashed {
                        border-style: dashed !important;
                    }
                </style>

                <!-- Team Card 2 -->
                <div class="col-lg-4">
                    <div class="card team-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1">Jujur Badminton</h5>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span class="me-2">5.00</span>
                                        <span class="badge bg-light text-dark">Intermediate - Advanced</span>
                                    </div>
                                </div>
                                <span class="badge bg-danger-subtle text-danger rounded-pill">
                                    <i class="fas fa-users me-1"></i>6/8
                                </span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span>Sen, 13 Jan 2024 • 20:00 - 22:00</span>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-map-marker-alt text-muted me-2 mt-1"></i>
                                    <span>Lapangan 5 • Gor Badminton Cipondoh, Kota Tangerang</span>
                                </div>
                            </div>
                            <!-- Garis putus-putus -->
                            <hr class="border-2 border-dashed opacity-50 my-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="avatar-group">
                                    <img src="/api/placeholder/32/32" class="avatar" alt="Member 1">
                                    <img src="/api/placeholder/32/32" class="avatar" alt="Member 2">
                                    <img src="/api/placeholder/32/32" class="avatar" alt="Member 3">
                                    <div class="avatar bg-light d-flex align-items-center justify-content-center">
                                        <small class="text-muted">+3</small>
                                    </div>
                                </div>
                                <a href="/detail-mabar" class="btn btn-outline-danger rounded-pill px-3">
                                    Detail
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team Card 3 -->
                <div class="col-lg-4">
                    <div class="card team-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1">Main Bareng Fun</h5>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span class="me-2">4.85</span>
                                        <span class="badge bg-light text-dark">Beginner - Pro</span>
                                    </div>
                                </div>
                                <span class="badge bg-danger-subtle text-danger rounded-pill">
                                    <i class="fas fa-users me-1"></i>6/12
                                </span>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span>Min, 12 Jan 2024 • 10:00 - 12:00</span>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="fas fa-map-marker-alt text-muted me-2 mt-1"></i>
                                    <span>Lapangan 5 • Gor Badminton Cipondoh, Kota Tangerang</span>
                                </div>
                            </div>
                            <!-- Garis putus-putus -->
                            <hr class="border-2 border-dashed opacity-50 my-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="avatar-group">
                                    <img src="/api/placeholder/32/32" class="avatar" alt="Member 1">
                                    <img src="/api/placeholder/32/32" class="avatar" alt="Member 2">
                                    <img src="/api/placeholder/32/32" class="avatar" alt="Member 3">
                                    <div class="avatar bg-light d-flex align-items-center justify-content-center">
                                        <small class="text-muted">+4</small>
                                    </div>
                                </div>
                                <a href="/detail-mabar" class="btn btn-outline-danger rounded-pill px-3">
                                    Detail
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More -->
            <div class="text-center mt-5">
                <button class="btn btn-outline-danger rounded-pill px-4">
                    Tampilkan Lebih Banyak
                    <i class="fas fa-chevron-down ms-2"></i>
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Card & Layout Styles */
        .team-card {
            transition: all 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
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

        /* Form Controls */
        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e9ecef;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: none;
            border-color: var(--primary-color);
        }

        /* Buttons */
        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        /* Search Section */
        .search-section {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .avatar-group .avatar {
                width: 28px;
                height: 28px;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
