@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/users/maincourt.css') }}">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-wrapper " style="margin-top: 50px;">
        <div class="container py-2">
            <ol class="custom-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/" class="breadcrumb-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/venues" class="breadcrumb-link">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Venues</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-futbol"></i>
                    <span>Field A</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-3">

        <!-- Gallery Section -->
        <div class="row g-3 mb-5 d-none d-lg-flex mt-3">
            <!-- Main Image -->
            <div class="col-lg-8">
                <div class="gallery-card main-gallery">
                    <div class="gallery-img">
                        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                            class="img-fluid w-100" alt="Main court">
                        <div class="gallery-overlay">
                            <button class="view-btn">
                                <i class="fas fa-expand-alt"></i>
                                View Full Image
                            </button>
                        </div>
                    </div>
                    <div class="status-badge">
                        <span class="badge-content">
                            <i class="fas fa-check-circle me-1"></i>
                            Available Now
                        </span>
                    </div>
                </div>
            </div>

            <!-- Side Images -->
            <div class="col-lg-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="gallery-card">
                            <div class="gallery-img">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                    class="img-fluid w-100" alt="Court view 2">
                                <div class="gallery-overlay">
                                    <button class="view-btn">
                                        <i class="fas fa-expand-alt"></i>
                                        View Full Image
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="gallery-card">
                            <div class="gallery-img">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                    class="img-fluid w-100" alt="Court view 3">
                                <div class="gallery-overlay">
                                    <button class="view-btn">
                                        <i class="fas fa-expand-alt"></i>
                                        View Full Image
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Mobile Gallery Carousel -->
        <div class="mobile-gallery d-lg-none">
            <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel">
                <!-- Carousel Inner -->
                <div class="carousel-inner rounded-4 overflow-hidden">
                    <div class="carousel-item active">
                        <div class="carousel-img-wrapper">
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                class="d-block w-100" alt="Main court">
                            <div class="image-overlay"></div>
                        </div>
                        <div class="carousel-caption">
                            <span class="caption-badge">
                                <i class="fas fa-image"></i>
                                1/3
                            </span>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-img-wrapper">
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                class="d-block w-100" alt="Court view 2">
                            <div class="image-overlay"></div>
                        </div>
                        <div class="carousel-caption">
                            <span class="caption-badge">
                                <i class="fas fa-image"></i>
                                2/3
                            </span>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-img-wrapper">
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                class="d-block w-100" alt="Court view 3">
                            <div class="image-overlay"></div>
                        </div>
                        <div class="carousel-caption">
                            <span class="caption-badge">
                                <i class="fas fa-image"></i>
                                3/3
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <button class="carousel-control carousel-control-prev" type="button" data-bs-target="#galleryCarousel"
                    data-bs-slide="prev">
                    <span class="control-icon">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                </button>
                <button class="carousel-control carousel-control-next" type="button" data-bs-target="#galleryCarousel"
                    data-bs-slide="next">
                    <span class="control-icon">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </button>

                <!-- Indicators -->
                <div class="carousel-indicators custom-indicators">
                    <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="0" class="active"
                        aria-current="true"></button>
                    <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="2"></button>
                </div>

                <!-- Available Badge -->
                <div class="available-badge">
                    <span class="badge-content">
                        <i class="fas fa-check-circle"></i>
                        Available Now
                    </span>
                </div>
            </div>
        </div>



        <!-- Main Information -->
        <div class="row">
            <!-- Field Details Container -->
            <div class="container py-4">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Basic Information Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3 flex-column flex-md-row">
                                    <div>
                                        <h1 class="h4 mb-2 text-center text-md-start fw-bold">Maincourt - Field A</h1>
                                        <div
                                            class="d-flex flex-column flex-md-row align-items-center gap-3 text-center text-md-start">
                                            <div class="d-flex align-items-center location-badge">
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                <span class="text-secondary">Jakarta, Indonesia</span>
                                            </div>
                                            <div class="d-flex align-items-center rating-badge">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <span class="text-secondary">4.8 (128 reviews)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-md-end text-center price-tag">
                                        <div class="h3 text-danger fw-bold mb-0">Rp 50.000</div>
                                        <small class="text-muted">/hour</small>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Field Overview Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-header bg-white py-3 border-0 px-4">
                                <h5 class="mb-0 fw-bold">Field Overview</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-6 col-md-3">
                                        <div class="overview-item text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-ruler"></i>
                                            </div>
                                            <h6 class="mb-1 fw-semibold">Size</h6>
                                            <small class="text-muted">25 x 15m</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="overview-item text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <h6 class="mb-1 fw-semibold">Capacity</h6>
                                            <small class="text-muted">5v5 Players</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="overview-item text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <h6 class="mb-1 fw-semibold">Duration</h6>
                                            <small class="text-muted">1 Hour/Session</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="overview-item text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-volleyball-ball"></i>
                                            </div>
                                            <h6 class="mb-1 fw-semibold">Type</h6>
                                            <small class="text-muted">Indoor Field</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Available Facilities Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-header bg-white py-3 border-0 px-4">
                                <h5 class="mb-0 fw-bold">Available Facilities</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-6 col-md-4">
                                        <div class="facility-badge">
                                            <i class="fas fa-parking"></i>
                                            <span>Free Parking</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="facility-badge">
                                            <i class="fas fa-wifi"></i>
                                            <span>Free WiFi</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="facility-badge">
                                            <i class="fas fa-shower"></i>
                                            <span>Shower Room</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <div class="facility-badge">
                                            <i class="fas fa-tshirt"></i>
                                            <span>Changing Room</span>
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
                                            <span>First Aid</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Calendar Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-header bg-white py-3 border-0 px-4">
                                <h5 class="mb-0 fw-bold">Pilih Jadwal Booking</h5>
                            </div>
                            <div class="card-body p-4">
                                <!-- Calendar Navigation -->
                                <div class="calendar-navigation d-flex justify-content-between align-items-center mb-4">
                                    <button class="nav-btn btn-prev">
                                        <i class="fas fa-chevron-left"></i>
                                        <span>Previous</span>
                                    </button>
                                    <h6 class="fw-semibold mb-0 month-display">January 2024</h6>
                                    <button class="nav-btn btn-next">
                                        <span>Next</span>
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>

                                <!-- Calendar Grid -->
                                <div class="calendar-grid mb-4">
                                    <!-- Days Header -->
                                    <div class="calendar-header">
                                        <div>Sun</div>
                                        <div>Mon</div>
                                        <div>Tue</div>
                                        <div>Wed</div>
                                        <div>Thu</div>
                                        <div>Fri</div>
                                        <div>Sat</div>
                                    </div>

                                    <!-- Calendar Dates -->
                                    <div class="calendar-dates">
                                        <!-- Dates will be populated by JS -->
                                    </div>
                                </div>

                                <!-- Time Slots -->
                                <div class="mt-4">
                                    <h6 class="fw-semibold mb-3">Available Time Slots</h6>
                                    <div class="time-slots-grid">
                                        <div class="time-slot" data-time="08:00 - 10:00">
                                            <div class="time-slot-content">
                                                <i class="far fa-clock"></i>
                                                <span>08:00 - 10:00</span>
                                                <small class="status available">Available</small>
                                            </div>
                                        </div>
                                        <div class="time-slot active" data-time="10:00 - 12:00">
                                            <div class="time-slot-content">
                                                <i class="far fa-clock"></i>
                                                <span>10:00 - 12:00</span>
                                                <small class="status available">Available</small>
                                            </div>
                                        </div>
                                        <div class="time-slot disabled" data-time="13:00 - 15:00">
                                            <div class="time-slot-content">
                                                <i class="far fa-clock"></i>
                                                <span>13:00 - 15:00</span>
                                                <small class="status booked">Booked</small>
                                            </div>
                                        </div>
                                        <div class="time-slot" data-time="15:00 - 17:00">
                                            <div class="time-slot-content">
                                                <i class="far fa-clock"></i>
                                                <span>15:00 - 17:00</span>
                                                <small class="status available">Available</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden Input -->
                                <input type="date" id="selectedDate" class="d-none">

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between mt-4 gap-3">
                                    <button class="btn-action btn-back">
                                        <i class="fas fa-arrow-left"></i>
                                        <span>Back</span>
                                    </button>
                                    <button class="btn-action btn-continue">
                                        <span>Continue to Payment</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Photographer Services Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-header bg-white py-3 border-0 px-4">
                                <h5 class="mb-0 fw-bold">Jasa Fotografer</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <!-- Basic Package -->
                                    <div class="col-md-4">
                                        <div class="package-card p-4 border rounded-4 h-100">
                                            <h6 class="fw-bold mb-3">Paket Foto Basic</h6>
                                            <div class="price-tag mb-3">
                                                <span class="fs-4 fw-bold">Rp 200.000</span>
                                                <small class="text-muted">/sesi</small>
                                            </div>
                                            <p class="text-muted mb-2">1 jam</p>
                                            <ul class="list-unstyled mb-4">
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    1 fotografer dengan kamera Mirrorless/DSLR
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    1 foto per orang
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    Foto 2 tim
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    File dikirim dalam 1x24 jam via Google Drive
                                                </li>
                                            </ul>
                                            <button class="btn btn-outline-primary w-100 rounded-pill">Pilih Paket</button>
                                        </div>
                                    </div>

                                    <!-- Plus Package -->
                                    <div class="col-md-4">
                                        <div class="package-card p-4 border rounded-4 h-100 bg-primary bg-opacity-5 ">
                                            <div class="position-absolute top-0 end-0 mt-3 me-3">
                                                <span class="badge bg-primary">Popular</span>
                                            </div>
                                            <h6 class="fw-bold mb-3">Paket Foto Plus</h6>
                                            <div class="price-tag mb-3">
                                                <span class="fs-4 fw-bold">Rp 300.000</span>
                                                <small class="text-muted">/sesi</small>
                                            </div>
                                            <p class="text-muted mb-2">2 jam</p>
                                            <ul class="list-unstyled mb-4">
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    1 fotografer dengan kamera Mirrorless/DSLR
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    Unlimited photo
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    Foto 2 tim
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    File dikirim dalam 1x24 jam via Google Drive
                                                </li>
                                            </ul>
                                            <button class="btn btn-primary w-100 rounded-pill">Pilih Paket</button>
                                        </div>
                                    </div>

                                    <!-- Exclusive Package -->
                                    <div class="col-md-4">
                                        <div class="package-card p-4 border rounded-4 h-100">
                                            <h6 class="fw-bold mb-3">Paket Foto Exclusive</h6>
                                            <div class="price-tag mb-3">
                                                <span class="fs-4 fw-bold">Rp 400.000</span>
                                                <small class="text-muted">/sesi</small>
                                            </div>
                                            <p class="text-muted mb-2">3 jam</p>
                                            <ul class="list-unstyled mb-4">
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    1 fotografer dengan kamera Mirrorless/DSLR
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    Unlimited photo
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    Foto 2 tim
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    File dikirim dalam 1x24 jam via Google Drive
                                                </li>
                                            </ul>
                                            <button class="btn btn-outline-primary w-100 rounded-pill">Pilih Paket</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- Membership Packages Sidebar -->
                    <div class="col-lg-4">
                        <div class="card border-0 rounded-4 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-4">Membership Packages</h5>

                                <!-- Bronze Package -->
                                <div class="membership-card bronze mb-4">
                                    <div class="package-header">
                                        <div class="package-info">
                                            <div class="package-icon">
                                                <i class="fas fa-award"></i>
                                            </div>
                                            <div>
                                                <h6 class="package-title">Bronze Member</h6>
                                                <p class="package-subtitle">Perfect for casual players</p>
                                            </div>
                                        </div>
                                        <div class="save-badge bronze">
                                            <i class="fas fa-percentage"></i>
                                            Save 10%
                                        </div>
                                    </div>
                                    <div class="package-features">
                                        <div class="feature-item">
                                            <i class="fas fa-clock"></i>
                                            <span>10 hours of playtime</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Valid for 1 month</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-volleyball-ball"></i>
                                            <span>Basic equipment rental</span>
                                        </div>
                                    </div>
                                    <div class="package-footer">
                                        <div class="price-info">
                                            <span class="price">Rp 450.000</span>
                                            <span class="duration">/month</span>
                                        </div>
                                        <button class="select-btn bronze">
                                            <span>Select Plan</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Silver Package -->
                                <div class="membership-card silver mb-4">
                                    <div class="package-header">
                                        <div class="package-info">
                                            <div class="package-icon">
                                                <i class="fas fa-award"></i>
                                            </div>
                                            <div>
                                                <h6 class="package-title">Silver Member</h6>
                                                <p class="package-subtitle">Great for regular players</p>
                                            </div>
                                        </div>
                                        <div class="save-badge silver">
                                            <i class="fas fa-percentage"></i>
                                            Save 20%
                                        </div>
                                    </div>
                                    <div class="package-features">
                                        <div class="feature-item">
                                            <i class="fas fa-clock"></i>
                                            <span>20 hours of playtime</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Valid for 2 months</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-volleyball-ball"></i>
                                            <span>Premium equipment rental</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-coffee"></i>
                                            <span>1 free drink per visit</span>
                                        </div>
                                    </div>
                                    <div class="package-footer">
                                        <div class="price-info">
                                            <span class="price">Rp 850.000</span>
                                            <span class="duration">/2 months</span>
                                        </div>
                                        <button class="select-btn silver">
                                            <span>Select Plan</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Gold Package -->
                                <div class="membership-card gold featured">
                                    <div class="featured-label">Best Value</div>
                                    <div class="package-header">
                                        <div class="package-info">
                                            <div class="package-icon">
                                                <i class="fas fa-crown"></i>
                                            </div>
                                            <div>
                                                <h6 class="package-title">Gold Member</h6>
                                                <p class="package-subtitle">Best value for enthusiasts</p>
                                            </div>
                                        </div>
                                        <div class="save-badge gold">
                                            <i class="fas fa-percentage"></i>
                                            Save 30%
                                        </div>
                                    </div>
                                    <div class="package-features">
                                        <div class="feature-item">
                                            <i class="fas fa-clock"></i>
                                            <span>40 hours of playtime</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span>Valid for 3 months</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-volleyball-ball"></i>
                                            <span>Premium equipment rental</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-coffee"></i>
                                            <span>2 free drinks per visit</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-star"></i>
                                            <span>Priority booking</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-lock"></i>
                                            <span>Free locker access</span>
                                        </div>
                                    </div>
                                    <div class="package-footer">
                                        <div class="price-info">
                                            <span class="price">Rp 1.200.000</span>
                                            <span class="duration">/3 months</span>
                                        </div>
                                        <button class="select-btn gold">
                                            <span>Select Plan</span>
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>



            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const calendarDates = document.querySelector('.calendar-dates');
                    const selectedDateInput = document.getElementById('selectedDate');
                    const monthDisplay = document.querySelector('.calendar-navigation h6');
                    let currentDate = new Date();

                    // Format month and year
                    function formatMonth(date) {
                        return date.toLocaleDateString('en-US', {
                            month: 'long',
                            year: 'numeric'
                        });
                    }

                    // Generate calendar dates
                    function generateCalendar(date) {
                        const year = date.getFullYear();
                        const month = date.getMonth();

                        // Update month and year display
                        monthDisplay.textContent = formatMonth(date);

                        const firstDay = new Date(year, month, 1).getDay();
                        const lastDate = new Date(year, month + 1, 0).getDate();
                        const lastMonthLastDate = new Date(year, month, 0).getDate();

                        let html = '';

                        // Previous month dates
                        for (let i = firstDay - 1; i >= 0; i--) {
                            html += `<div class="calendar-date disabled">${lastMonthLastDate - i}</div>`;
                        }

                        // Current month dates
                        const today = new Date();
                        for (let i = 1; i <= lastDate; i++) {
                            const currentDate = new Date(year, month, i);
                            const isDisabled = currentDate < today;
                            const isToday = currentDate.toDateString() === today.toDateString();
                            html +=
                                `<div class="calendar-date ${isDisabled ? 'disabled' : ''} ${isToday ? 'active' : ''}">${i}</div>`;
                        }

                        calendarDates.innerHTML = html;

                        // Add click handlers
                        document.querySelectorAll('.calendar-date:not(.disabled)').forEach(date => {
                            date.addEventListener('click', function() {
                                document.querySelectorAll('.calendar-date').forEach(d => d.classList.remove(
                                    'active'));
                                this.classList.add('active');
                                const selectedDate = new Date(year, month, parseInt(this.textContent));
                                selectedDateInput.value = selectedDate.toISOString().split('T')[0];

                                // Update booking summary if exists
                                const bookingSummary = document.querySelector('.booking-summary');
                                if (bookingSummary) {
                                    const formattedDate = selectedDate.toLocaleDateString('en-US', {
                                        weekday: 'long',
                                        year: 'numeric',
                                        month: 'long',
                                        day: 'numeric'
                                    });
                                    bookingSummary.querySelector('.selected-date').textContent =
                                        formattedDate;
                                }
                            });
                        });

                        // Fill in remaining calendar dates if needed
                        const totalDays = document.querySelectorAll('.calendar-date').length;
                        let remainingDays = 42 - totalDays; // 6 rows × 7 days = 42 total grid spaces
                        let nextMonthDay = 1;

                        while (remainingDays > 0) {
                            html += `<div class="calendar-date disabled">${nextMonthDay}</div>`;
                            nextMonthDay++;
                            remainingDays--;
                        }

                        calendarDates.innerHTML = html;
                    }

                    // Calendar navigation
                    document.querySelector('.btn-prev').addEventListener('click', function() {
                        currentDate.setMonth(currentDate.getMonth() - 1);
                        generateCalendar(currentDate);
                    });

                    document.querySelector('.btn-next').addEventListener('click', function() {
                        currentDate.setMonth(currentDate.getMonth() + 1);
                        generateCalendar(currentDate);
                    });

                    // Time slot selection
                    document.querySelectorAll('.time-slot:not(.disabled)').forEach(slot => {
                        slot.addEventListener('click', function() {
                            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove(
                                'active'));
                            this.classList.add('active');

                            // Update booking summary if exists
                            const bookingSummary = document.querySelector('.booking-summary');
                            if (bookingSummary) {
                                const selectedTime = this.getAttribute('data-time');
                                bookingSummary.querySelector('.selected-time').textContent = selectedTime;
                            }
                        });
                    });

                    // Initialize calendar
                    generateCalendar(currentDate);


                    // Add booking summary to sidebar if not exists
                    const sidebarContent = document.querySelector('.col-lg-4 .card-body');
                    if (sidebarContent) {
                        sidebarContent.innerHTML = bookingSummaryTemplate;
                    }
                });
            </script>


        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
