@extends('layouts.app')
@section('content')
    <!-- Breadcrumb -->
    <nav class="breadcrumb-wrapper" style="margin-top: 50px;">
        <div class="container py-2">
            <ol class="custom-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/" class="breadcrumb-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="breadcrumb-item active text-white">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Venues</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container py-4">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h2 class="section-title fw-bold mb-3">Rental Equipment</h2>
                <p class="section-desc mx-auto" style="max-width: 700px;">
                    Lupa bawa perlengkapan olahraga? Jangan khawatir! Alena Soccer menyediakan layanan penyewaan
                    perlengkapan olahraga berkualitas untuk kebutuhan bermain Anda.
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
                                <input type="text" class="form-control ps-5" placeholder="Cari equipment...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option>Semua Kategori</option>
                                <option>Jersey</option>
                                <option>Sepatu</option>
                                <option>Bola</option>
                                <option>Aksesoris</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select">
                                <option>Semua Ukuran</option>
                                <option>S</option>
                                <option>M</option>
                                <option>L</option>
                                <option>XL</option>
                                <option>XXL</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select">
                                <option>Terbaru</option>
                                <option>Harga Terendah</option>
                                <option>Harga Tertinggi</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="result-badge">
                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                        56 item ditemukan
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

            <!-- Category Navigation -->
            <div class="category-nav mb-4">
                <div class="d-flex flex-wrap gap-2">
                    <a href="#" class="category-pill active">
                        <i class="fas fa-futbol me-2"></i>
                        Semua
                    </a>
                    <a href="#" class="category-pill">
                        <i class="fas fa-tshirt me-2"></i>
                        Jersey
                    </a>
                    <a href="#" class="category-pill">
                        <i class="fas fa-shoe-prints me-2"></i>
                        Sepatu
                    </a>
                    <a href="#" class="category-pill">
                        <i class="fas fa-futbol me-2"></i>
                        Bola
                    </a>
                    <a href="#" class="category-pill">
                        <i class="fas fa-mitten me-2"></i>
                        Sarung Tangan
                    </a>
                    <a href="#" class="category-pill">
                        <i class="fas fa-socks me-2"></i>
                        Kaos Kaki
                    </a>
                </div>
            </div>

            <!-- Equipment Grid -->
            <div class="row g-4">
                <!-- Equipment Card 1 -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card equipment-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-img-wrapper position-relative">
                            <span class="badge bg-danger position-absolute top-0 end-0 m-2">Populer</span>
                            <img src="/api/placeholder/300/300" class="card-img-top rounded-top-4" alt="Jersey Home Alena">
                        </div>
                        <div class="card-body p-3">
                            <!-- Header -->
                            <div class="mb-2">
                                <div class="category-badge mb-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                                        <i class="fas fa-tshirt me-1"></i>
                                        Jersey
                                    </span>
                                </div>
                                <h5 class="card-title mb-1">Jersey Home Alena 2024</h5>
                                <div class="d-flex align-items-center">
                                    <div class="rating me-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-1">4.8</span>
                                    </div>
                                    <span class="rental-count text-muted small">
                                        (48 disewa)
                                    </span>
                                </div>
                            </div>

                            <!-- Availability -->
                            <div class="availability mb-3">
                                <div class="d-flex">
                                    <span class="size-badge me-1">S</span>
                                    <span class="size-badge me-1">M</span>
                                    <span class="size-badge me-1 active">L</span>
                                    <span class="size-badge me-1">XL</span>
                                    <span class="size-badge disabled">XXL</span>
                                </div>
                            </div>

                            <hr class="border-1 border-dashed opacity-50 my-2">

                            <!-- Footer -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-info">
                                    <span class="h5 mb-0 text-danger">Rp25.000</span>
                                    <small class="text-muted">/hari</small>
                                </div>
                                <a href="/product-rental" class="btn btn-sm btn-outline-danger rounded-pill">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Card 2 -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card equipment-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-img-wrapper position-relative">
                            <img src="/api/placeholder/300/300" class="card-img-top rounded-top-4" alt="Sepatu Futsal">
                        </div>
                        <div class="card-body p-3">
                            <!-- Header -->
                            <div class="mb-2">
                                <div class="category-badge mb-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                                        <i class="fas fa-shoe-prints me-1"></i>
                                        Sepatu
                                    </span>
                                </div>
                                <h5 class="card-title mb-1">Sepatu Futsal Pro 2024</h5>
                                <div class="d-flex align-items-center">
                                    <div class="rating me-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-1">4.7</span>
                                    </div>
                                    <span class="rental-count text-muted small">
                                        (36 disewa)
                                    </span>
                                </div>
                            </div>

                            <!-- Availability -->
                            <div class="availability mb-3">
                                <div class="d-flex">
                                    <span class="size-badge me-1">39</span>
                                    <span class="size-badge me-1">40</span>
                                    <span class="size-badge me-1 active">41</span>
                                    <span class="size-badge me-1">42</span>
                                    <span class="size-badge me-1">43</span>
                                </div>
                            </div>

                            <hr class="border-1 border-dashed opacity-50 my-2">

                            <!-- Footer -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-info">
                                    <span class="h5 mb-0 text-danger">Rp35.000</span>
                                    <small class="text-muted">/hari</small>
                                </div>
                                <a href="/product-rental" class="btn btn-sm btn-outline-danger rounded-pill">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Card 3 -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card equipment-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-img-wrapper position-relative">
                            <img src="/api/placeholder/300/300" class="card-img-top rounded-top-4" alt="Bola Soccer">
                        </div>
                        <div class="card-body p-3">
                            <!-- Header -->
                            <div class="mb-2">
                                <div class="category-badge mb-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                                        <i class="fas fa-futbol me-1"></i>
                                        Bola
                                    </span>
                                </div>
                                <h5 class="card-title mb-1">Bola Soccer Profesional</h5>
                                <div class="d-flex align-items-center">
                                    <div class="rating me-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-1">4.9</span>
                                    </div>
                                    <span class="rental-count text-muted small">
                                        (72 disewa)
                                    </span>
                                </div>
                            </div>

                            <!-- Availability -->
                            <div class="availability mb-3">
                                <div class="stock-info text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Tersedia (12 stok)
                                </div>
                            </div>

                            <hr class="border-1 border-dashed opacity-50 my-2">

                            <!-- Footer -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-info">
                                    <span class="h5 mb-0 text-danger">Rp20.000</span>
                                    <small class="text-muted">/hari</small>
                                </div>
                                <a href="/product-rental" class="btn btn-sm btn-outline-danger rounded-pill">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Card 4 -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card equipment-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-img-wrapper position-relative">
                            <span class="badge bg-secondary position-absolute top-0 end-0 m-2">Baru</span>
                            <img src="/api/placeholder/300/300" class="card-img-top rounded-top-4"
                                alt="Sarung Tangan Kiper">
                        </div>
                        <div class="card-body p-3">
                            <!-- Header -->
                            <div class="mb-2">
                                <div class="category-badge mb-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                                        <i class="fas fa-mitten me-1"></i>
                                        Sarung Tangan
                                    </span>
                                </div>
                                <h5 class="card-title mb-1">Sarung Tangan Kiper Pro</h5>
                                <div class="d-flex align-items-center">
                                    <div class="rating me-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-1">4.6</span>
                                    </div>
                                    <span class="rental-count text-muted small">
                                        (18 disewa)
                                    </span>
                                </div>
                            </div>

                            <!-- Availability -->
                            <div class="availability mb-3">
                                <div class="d-flex">
                                    <span class="size-badge me-1 disabled">S</span>
                                    <span class="size-badge me-1">M</span>
                                    <span class="size-badge me-1 active">L</span>
                                    <span class="size-badge me-1">XL</span>
                                </div>
                            </div>

                            <hr class="border-1 border-dashed opacity-50 my-2">

                            <!-- Footer -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-info">
                                    <span class="h5 mb-0 text-danger">Rp15.000</span>
                                    <small class="text-muted">/hari</small>
                                </div>
                                <a href="/product-rental" class="btn btn-sm btn-outline-danger rounded-pill">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Card 5 -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card equipment-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-img-wrapper position-relative">
                            <img src="/api/placeholder/300/300" class="card-img-top rounded-top-4"
                                alt="Jersey Away Alena">
                        </div>
                        <div class="card-body p-3">
                            <!-- Header -->
                            <div class="mb-2">
                                <div class="category-badge mb-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                                        <i class="fas fa-tshirt me-1"></i>
                                        Jersey
                                    </span>
                                </div>
                                <h5 class="card-title mb-1">Jersey Away Alena 2024</h5>
                                <div class="d-flex align-items-center">
                                    <div class="rating me-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-1">4.7</span>
                                    </div>
                                    <span class="rental-count text-muted small">
                                        (42 disewa)
                                    </span>
                                </div>
                            </div>

                            <!-- Availability -->
                            <div class="availability mb-3">
                                <div class="d-flex">
                                    <span class="size-badge me-1">S</span>
                                    <span class="size-badge me-1 active">M</span>
                                    <span class="size-badge me-1">L</span>
                                    <span class="size-badge me-1">XL</span>
                                    <span class="size-badge disabled">XXL</span>
                                </div>
                            </div>

                            <hr class="border-1 border-dashed opacity-50 my-2">

                            <!-- Footer -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-info">
                                    <span class="h5 mb-0 text-danger">Rp25.000</span>
                                    <small class="text-muted">/hari</small>
                                </div>
                                <a href="/product-rental" class="btn btn-sm btn-outline-danger rounded-pill">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Card 6 -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card equipment-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-img-wrapper position-relative">
                            <img src="/api/placeholder/300/300" class="card-img-top rounded-top-4"
                                alt="Sepatu Futsal Indoor">
                        </div>
                        <div class="card-body p-3">
                            <!-- Header -->
                            <div class="mb-2">
                                <div class="category-badge mb-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                                        <i class="fas fa-shoe-prints me-1"></i>
                                        Sepatu
                                    </span>
                                </div>
                                <h5 class="card-title mb-1">Sepatu Futsal Indoor</h5>
                                <div class="d-flex align-items-center">
                                    <div class="rating me-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-1">4.5</span>
                                    </div>
                                    <span class="rental-count text-muted small">
                                        (32 disewa)
                                    </span>
                                </div>
                            </div>

                            <!-- Availability -->
                            <div class="availability mb-3">
                                <div class="d-flex">
                                    <span class="size-badge me-1 disabled">39</span>
                                    <span class="size-badge me-1">40</span>
                                    <span class="size-badge me-1 active">41</span>
                                    <span class="size-badge me-1">42</span>
                                    <span class="size-badge disabled">43</span>
                                </div>
                            </div>

                            <hr class="border-1 border-dashed opacity-50 my-2">

                            <!-- Footer -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-info">
                                    <span class="h5 mb-0 text-danger">Rp30.000</span>
                                    <small class="text-muted">/hari</small>
                                </div>
                                <a href="/product-rental" class="btn btn-sm btn-outline-danger rounded-pill">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Card 7 -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card equipment-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-img-wrapper position-relative">
                            <img src="/api/placeholder/300/300" class="card-img-top rounded-top-4" alt="Bola Futsal">
                        </div>
                        <div class="card-body p-3">
                            <!-- Header -->
                            <div class="mb-2">
                                <div class="category-badge mb-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                                        <i class="fas fa-futbol me-1"></i>
                                        Bola
                                    </span>
                                </div>
                                <h5 class="card-title mb-1">Bola Futsal Standar</h5>
                                <div class="d-flex align-items-center">
                                    <div class="rating me-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-1">4.6</span>
                                    </div>
                                    <span class="rental-count text-muted small">
                                        (56 disewa)
                                    </span>
                                </div>
                            </div>

                            <!-- Availability -->
                            <div class="availability mb-3">
                                <div class="stock-info text-success">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Tersedia (8 stok)
                                </div>
                            </div>

                            <hr class="border-1 border-dashed opacity-50 my-2">

                            <!-- Footer -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-info">
                                    <span class="h5 mb-0 text-danger">Rp15.000</span>
                                    <small class="text-muted">/hari</small>
                                </div>
                                <a href="/product-rental" class="btn btn-sm btn-outline-danger rounded-pill">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Card 8 -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card equipment-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-img-wrapper position-relative">
                            <img src="/api/placeholder/300/300" class="card-img-top rounded-top-4"
                                alt="Kaos Kaki Olahraga">
                        </div>
                        <div class="card-body p-3">
                            <!-- Header -->
                            <div class="mb-2">
                                <div class="category-badge mb-2">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                                        <i class="fas fa-socks me-1"></i>
                                        Kaos Kaki
                                    </span>
                                </div>
                                <h5 class="card-title mb-1">Kaos Kaki Olahraga</h5>
                                <div class="d-flex align-items-center">
                                    <div class="rating me-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-1">4.4</span>
                                    </div>
                                    <span class="rental-count text-muted small">
                                        (22 disewa)
                                    </span>
                                </div>
                            </div>

                            <!-- Availability -->
                            <div class="availability mb-3">
                                <div class="d-flex">
                                    <span class="size-badge me-1">S</span>
                                    <span class="size-badge me-1 active">M</span>
                                    <span class="size-badge me-1">L</span>
                                </div>
                            </div>

                            <hr class="border-1 border-dashed opacity-50 my-2">

                            <!-- Footer -->
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="price-info">
                                    <span class="h5 mb-0 text-danger">Rp10.000</span>
                                    <small class="text-muted">/hari</small>
                                </div>
                                <a href="/product-rental" class="btn btn-sm btn-outline-danger rounded-pill">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load More -->
            <div class="text-center mt-5">
                <button class="btn btn-outline-danger rounded-pill px-4 py-2">
                    Tampilkan Lebih Banyak
                    <i class="fas fa-chevron-down ms-2"></i>
                </button>
            </div>

            <!-- Info Section -->
            <div class="info-section mt-5 p-4 bg-light rounded-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6">
                        <h3 class="mb-3">Cara Menyewa Equipment</h3>
                        <ol class="rental-steps">
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">1</div>
                                    <div>
                                        <h5 class="mb-1">Pilih Equipment</h5>
                                        <p class="text-muted mb-0">Pilih equipment yang ingin Anda sewa sesuai dengan
                                            ukuran dan kebutuhan.</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">2</div>
                                    <div>
                                        <h5 class="mb-1">Tentukan Durasi</h5>
                                        <p class="text-muted mb-0">Pilih tanggal mulai dan berakhirnya masa sewa.</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">3</div>
                                    <div>
                                        <h5 class="mb-1">Konfirmasi Pembayaran</h5>
                                        <p class="text-muted mb-0">Lakukan pembayaran melalui metode yang tersedia.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="step-number me-3">4</div>
                                    <div>
                                        <h5 class="mb-1">Ambil Equipment</h5>
                                        <p class="text-muted mb-0">Ambil equipment di lokasi lapangan Alena Soccer.</p>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="assets/rental.jpg" alt="Rental Guide" class="img-fluid rounded-4 shadow">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Breadcrumb Wrapper */
        .breadcrumb-wrapper {
            background: linear-gradient(to right, #9e0620, #bb2d3b);
            position: relative;
            overflow: hidden;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-breadcrumb {
            display: flex;
            flex-wrap: wrap;
            padding: 0;
            margin: 0;
            list-style: none;
            align-items: center;
            justify-content: center;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 800;
            font-size: 1.3rem;
        }

        .breadcrumb-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-weight: 800;
            font-size: 1.3rem;
        }

        .breadcrumb-item.active {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            padding: 6px 12px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.15);
            font-weight: 800;
            font-size: 1.3rem;
        }


        /* Section Styles */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #212529;
        }

        .section-desc {
            font-size: 1.1rem;
            color: #6c757d;
            line-height: 1.6;
        }

        /* Category Pills */
        .category-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            background: #fff;
            color: #6c757d;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            border: 1.5px solid var(--border-light);
            transition: all 0.3s ease;
        }

        .category-pill:hover {
            background: #f8f9fa;
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .category-pill.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Equipment Card Styles */
        .equipment-card .card-img-wrapper {
            height: 200px;
            overflow: hidden;
        }

        .equipment-card .card-img-top {
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .equipment-card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* Size Badges */
        .size-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f8f9fa;
            border: 1px solid var(--border-light);
            color: #495057;
            font-weight: 500;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .size-badge:hover {
            background: #e9ecef;
        }

        .size-badge.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .size-badge.disabled {
            background: #e9ecef;
            color: #adb5bd;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Stock Info */
        .stock-info {
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Rental Steps */
        .rental-steps {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* Card Hover Effects */
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

        /* Info Section */
        .info-section {
            border: 1px solid var(--border-light);
        }

        .info-section h3 {
            font-weight: 700;
            color: #212529;
        }
    </style>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
