@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/users/maincourt.css') }}">
    <link rel="stylesheet" href="{{ asset('css/users/field-show.css') }}">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/default.css">

    <!-- CSRF Token untuk AJAX Requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                <li class="breadcrumb-item">
                    <a href="{{ route('user.fields.index') }}" class="breadcrumb-link">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Venues</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-futbol"></i>
                    <span>{{ $field->name }}</span>
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
                        @if ($field->image)
                            <img src="{{ Storage::url($field->image) }}" class="img-fluid w-100" alt="{{ $field->name }}">
                        @else
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                class="img-fluid w-100" alt="{{ $field->name }}">
                        @endif
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
                                @if ($field->image)
                                    <img src="{{ Storage::url($field->image) }}" class="img-fluid w-100"
                                        alt="{{ $field->name }}">
                                @else
                                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                        class="img-fluid w-100" alt="{{ $field->name }}">
                                @endif
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
                                @if ($field->image)
                                    <img src="{{ Storage::url($field->image) }}" class="img-fluid w-100"
                                        alt="{{ $field->name }}">
                                @else
                                    <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                        class="img-fluid w-100" alt="{{ $field->name }}">
                                @endif
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
                            @if ($field->image)
                                <img src="{{ Storage::url($field->image) }}" class="d-block w-100"
                                    alt="{{ $field->name }}">
                            @else
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                    class="d-block w-100" alt="{{ $field->name }}">
                            @endif
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
                    <div class="col">
                        <!-- Basic Information Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3 flex-column flex-md-row">
                                    <div>
                                        <h1 class="h4 mb-2 text-center text-md-start fw-bold">{{ $field->name }}</h1>
                                        <div
                                            class="d-flex flex-column flex-md-row align-items-center gap-3 text-center text-md-start">
                                            <div class="d-flex align-items-center location-badge">
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                <span class="text-secondary">{{ 'Sidoarjo, Indonesia' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center rating-badge">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <span class="text-secondary">{{ number_format($field->rating, 1) }}
                                                    ({{ $field->reviews_count }} reviews)</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-md-end text-center price-tag">
                                        <div class="h3 text-danger fw-bold mb-0">Rp
                                            {{ number_format($field->price, 0, ',', '.') }}</div>
                                        <small class="text-muted">/hour</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Membership Packages Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-header bg-white py-3 border-0 px-4">
                                <h5 class="mb-0 fw-bold">Membership Packages</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-4">
                                    <!-- Bronze Package -->
                                    <div class="col-md-4">
                                        <div class="membership-card bronze">
                                            <div class="package-header">
                                                <div class="package-info">
                                                    <div class="package-icon">
                                                        <i class="fas fa-award"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="package-title">Bronze</h5>
                                                        <p class="package-subtitle">3x main/minggu</p>
                                                    </div>
                                                </div>
                                                <div class="save-badge bronze">
                                                    <i class="fas fa-tag"></i>
                                                    <span>Save 10%</span>
                                                </div>
                                            </div>

                                            <div class="package-footer">
                                                <div class="price-info">
                                                    <span class="price">Rp
                                                        {{ number_format($field->price * 3 * 4 * 0.9, 0, ',', '.') }}</span>
                                                    <span class="duration">/minggu</span>
                                                </div>

                                                @php
                                                    $bronzeMembership = $memberships
                                                        ->where('field_id', $field->id)
                                                        ->where('type', 'bronze')
                                                        ->first();
                                                @endphp
                                                @if ($bronzeMembership)
                                                    <a href="{{ route('user.membership.show', $bronzeMembership->id) }}"
                                                        class="select-btn bronze">
                                                        <span>Pilih Paket</span>
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary" disabled>
                                                        <span>Tidak Tersedia</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Silver Package -->
                                    <div class="col-md-4">
                                        <div class="membership-card silver featured">
                                            <div class="featured-label">Populer</div>
                                            <div class="package-header">
                                                <div class="package-info">
                                                    <div class="package-icon">
                                                        <i class="fas fa-medal"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="package-title">Silver</h5>
                                                        <p class="package-subtitle">3x main/minggu</p>
                                                    </div>
                                                </div>
                                                <div class="save-badge silver">
                                                    <i class="fas fa-tag"></i>
                                                    <span>Save 15%</span>
                                                </div>
                                            </div>



                                            <div class="package-footer">
                                                <div class="price-info">
                                                    <span class="price">Rp
                                                        {{ number_format($field->price * 3 * 4 * 2 * 0.85, 0, ',', '.') }}</span>
                                                    <span class="duration">/minggu</span>
                                                </div>

                                                @php
                                                    $silverMembership = $memberships
                                                        ->where('field_id', $field->id)
                                                        ->where('type', 'silver')
                                                        ->first();
                                                @endphp
                                                @if ($silverMembership)
                                                    <a href="{{ route('user.membership.show', $silverMembership->id) }}"
                                                        class="select-btn silver">
                                                        <span>Pilih Paket</span>
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary" disabled>
                                                        <span>Tidak Tersedia</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Gold Package -->
                                    <div class="col-md-4">
                                        <div class="membership-card gold">
                                            <div class="package-header">
                                                <div class="package-info">
                                                    <div class="package-icon">
                                                        <i class="fas fa-trophy"></i>
                                                    </div>
                                                    <div>
                                                        <h5 class="package-title">Gold</h5>
                                                        <p class="package-subtitle">3x main/minggu</p>
                                                    </div>
                                                </div>
                                                <div class="save-badge gold">
                                                    <i class="fas fa-tag"></i>
                                                    <span>Save 20%</span>
                                                </div>
                                            </div>



                                            <div class="package-footer">
                                                <div class="price-info">
                                                    <span class="price">Rp
                                                        {{ number_format($field->price * 3 * 4 * 3 * 0.8, 0, ',', '.') }}</span>
                                                    <span class="duration">/minggu</span>
                                                </div>

                                                @php
                                                    $goldMembership = $memberships
                                                        ->where('field_id', $field->id)
                                                        ->where('type', 'gold')
                                                        ->first();
                                                @endphp
                                                @if ($goldMembership)
                                                    <a href="{{ route('user.membership.show', $goldMembership->id) }}"
                                                        class="select-btn gold">
                                                        <span>Pilih Paket</span>
                                                        <i class="fas fa-arrow-right"></i>
                                                    </a>
                                                @else
                                                    <button class="btn btn-secondary" disabled>
                                                        <span>Tidak Tersedia</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <!-- Photographer Packages Card -->
                            @if ($photographerPackages->count() > 0)
                                <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                                    <div class="card-header bg-white py-3 border-0 px-4">
                                        <h5 class="mb-0 fw-bold">Paket Fotografer</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="row g-4">
                                            @foreach ($photographerPackages as $photographer)
                                                <div class="col-md-4">
                                                    <div class="photographer-card {{ $photographer->package_type }}">
                                                        <div class="package-header">
                                                            <div class="package-info">
                                                                <div class="package-icon">
                                                                    <i class="fas fa-camera"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="package-title">{{ $photographer->name }}
                                                                    </h5>
                                                                    <p class="package-subtitle">
                                                                        {{ $photographer->duration }} jam</p>
                                                                </div>
                                                            </div>
                                                            <div class="badge {{ $photographer->package_type }}">
                                                                {{ ucfirst($photographer->package_type) }}
                                                            </div>
                                                        </div>

                                                        <div class="package-features">
                                                            <ul class="list-unstyled">
                                                                @if (is_array(json_decode($photographer->features)))
                                                                    @foreach (json_decode($photographer->features) as $feature)
                                                                        <li><i
                                                                                class="fas fa-check-circle text-success me-2"></i>
                                                                            {{ $feature }}</li>
                                                                    @endforeach
                                                                @endif
                                                            </ul>
                                                        </div>

                                                        <div class="package-footer">
                                                            <div class="price-info">
                                                                <span class="price">Rp
                                                                    {{ number_format($photographer->price, 0, ',', '.') }}</span>
                                                                <span class="duration">/sesi</span>
                                                            </div>
                                                            <a href="{{ route('user.photographer.show', $photographer->id) }}"
                                                                class="select-btn {{ $photographer->package_type }}">
                                                                <span>Detail Paket</span>
                                                                <i class="fas fa-arrow-right"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif


                        <!-- Booking Card -->
                        <!-- Booking Wizard Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-header bg-white py-3 border-0 px-4">
                                <h5 class="mb-0 fw-bold">Pilih Jadwal Booking</h5>
                            </div>
                            <div class="card-body p-4">
                                <!-- Hidden field ID -->
                                <input type="hidden" id="fieldId" value="{{ $field->id }}">

                                <!-- Booking Wizard Process -->
                                <div class="booking-wizard">
                                    <!-- Progress Steps -->
                                    <div class="wizard-progress">
                                        <!-- Progress Bar -->
                                        <div class="wizard-progress-bar" id="wizard-progress-bar"></div>

                                        <!-- Step 1: Date Selection -->
                                        <div class="wizard-step" id="wizard-step-1">
                                            <div class="step-circle">
                                                <span>1</span>
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="step-label">Tanggal</div>
                                            <div class="step-desc">Pilih tanggal booking</div>
                                        </div>

                                        <!-- Step 2: Time Selection -->
                                        <div class="wizard-step" id="wizard-step-2">
                                            <div class="step-circle">
                                                <span>2</span>
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="step-label">Waktu</div>
                                            <div class="step-desc">Pilih slot waktu</div>
                                        </div>

                                        <!-- Step 3: Confirmation -->
                                        <div class="wizard-step" id="wizard-step-3">
                                            <div class="step-circle">
                                                <span>3</span>
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="step-label">Konfirmasi</div>
                                            <div class="step-desc">Konfirmasi booking</div>
                                        </div>
                                    </div>

                                    <!-- Wizard Content -->
                                    <div class="wizard-content">
                                        <!-- Panel 1: Date Selection -->
                                        <div class="wizard-panel" id="panel-date">
                                            <h6 class="fw-semibold mb-3">Pilih Tanggal Booking</h6>
                                            <div class="date-picker-container">
                                                <div id="inline-calendar" class="inline-calendar-container"></div>
                                                <input type="hidden" id="selectedDate" name="selected_date">
                                            </div>
                                        </div>

                                        <!-- Panel 2: Time Slot Selection -->
                                        <div class="wizard-panel" id="panel-time">
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="fw-semibold mb-0">
                                                        Slot Waktu Tersedia (<span id="selected-date-display"></span>)
                                                    </h6>
                                                    <span class="badge bg-secondary" id="available-slots-count">0
                                                        slot</span>
                                                </div>
                                                <div id="time-slots-wrapper" class="time-slots-container">
                                                    <div class="text-center py-4 slot-placeholder">
                                                        <div class="spinner-border text-danger" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        <p class="mt-2">Mengambil slot waktu yang tersedia...</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Panel 3: Confirmation -->
                                        <div class="wizard-panel" id="panel-confirm">
                                            <div class="mb-3">
                                                <h6 class="fw-semibold mb-3">Detail Booking</h6>
                                                <div class="confirmation-details mb-3">
                                                    <div class="confirmation-item">
                                                        <span class="label">Lapangan:</span>
                                                        <span class="value">{{ $field->name }}</span>
                                                    </div>
                                                    <div class="confirmation-item">
                                                        <span class="label">Tanggal:</span>
                                                        <span class="value" id="confirm-date"></span>
                                                    </div>
                                                </div>

                                                <h6 class="fw-semibold mb-3">Slot Waktu Terpilih</h6>
                                                <div class="selected-slots-list mb-3">
                                                    <ul id="selected-slots-list" class="list-group list-group-flush">
                                                        <!-- Selected slots will be added here -->
                                                    </ul>
                                                    <div
                                                        class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded">
                                                        <span class="fw-bold">Total:</span>
                                                        <span id="total-price" class="text-danger fw-bold fs-5">Rp
                                                            0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Wizard Navigation Buttons -->
                                    <div class="wizard-buttons">
                                        <button id="wizard-prev-btn" class="wizard-btn wizard-btn-prev">
                                            <i class="fas fa-arrow-left"></i>
                                            Kembali
                                        </button>
                                        <button id="wizard-next-btn" class="wizard-btn wizard-btn-next" disabled>
                                            Lanjutkan
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                        <button id="wizard-submit-btn" class="wizard-btn wizard-btn-submit">
                                            <i class="fas fa-cart-plus me-2"></i>
                                            Tambahkan ke Keranjang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Initialize variables
                                const fieldId = document.getElementById('fieldId').value;
                                let selectedDate = '';
                                const selectedSlots = new Set();
                                let selectedSlotsData = [];
                                let totalPrice = 0;
                                let currentStep = 1;
                                const totalSteps = 3;

                                // DOM Elements
                                const progressBar = document.getElementById('wizard-progress-bar');
                                const wizardSteps = document.querySelectorAll('.wizard-step');
                                const wizardPanels = document.querySelectorAll('.wizard-panel');

                                const prevBtn = document.getElementById('wizard-prev-btn');
                                const nextBtn = document.getElementById('wizard-next-btn');
                                const submitBtn = document.getElementById('wizard-submit-btn');

                                // Initialize Flatpickr inline calendar
                                const calendar = flatpickr("#inline-calendar", {
                                    inline: true,
                                    locale: 'id',
                                    dateFormat: 'Y-m-d',
                                    minDate: 'today',
                                    maxDate: new Date().fp_incr(6), // Maksimal 7 hari ke depan
                                    responsive: true, // Enable responsive mode
                                    onChange: function(selectedDates, dateStr) {
                                        selectedDate = dateStr;
                                        document.getElementById('selectedDate').value = dateStr;

                                        // Enable next button
                                        nextBtn.disabled = false;
                                    }
                                });

                                // Update progress bar
                                function updateProgressBar() {
                                    const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
                                    progressBar.style.width = `${progressPercentage}%`;
                                }

                                // Update step states
                                function updateSteps() {
                                    wizardSteps.forEach((step, index) => {
                                        const stepNumber = index + 1;

                                        // Remove all states
                                        step.classList.remove('active', 'completed');

                                        // Set appropriate state
                                        if (stepNumber === currentStep) {
                                            step.classList.add('active');
                                        } else if (stepNumber < currentStep) {
                                            step.classList.add('completed');
                                        }
                                    });
                                }

                                // Show/hide panels
                                function showPanel() {
                                    wizardPanels.forEach((panel, index) => {
                                        const panelNumber = index + 1;

                                        if (panelNumber === currentStep) {
                                            panel.classList.add('active');
                                        } else {
                                            panel.classList.remove('active');
                                        }
                                    });
                                }

                                // Update buttons
                                function updateButtons() {
                                    prevBtn.style.display = currentStep === 1 ? 'none' : 'flex';
                                    nextBtn.style.display = currentStep === totalSteps ? 'none' : 'flex';
                                    submitBtn.style.display = currentStep === totalSteps ? 'flex' : 'none';

                                    // Disable next button on date selection step if no date is selected
                                    if (currentStep === 1) {
                                        nextBtn.disabled = !selectedDate;
                                    }

                                    // Disable next button on time selection step if no slots are selected
                                    if (currentStep === 2) {
                                        nextBtn.disabled = selectedSlots.size === 0;
                                    }
                                }

                                // Navigate to step
                                function goToStep(step) {
                                    currentStep = step;
                                    updateProgressBar();
                                    updateSteps();
                                    showPanel();
                                    updateButtons();

                                    // Additional actions based on step
                                    if (step === 2 && selectedDate) {
                                        // Format date for display
                                        const formattedDate = new Date(selectedDate).toLocaleDateString('id-ID', {
                                            weekday: 'long',
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric'
                                        });
                                        document.getElementById('selected-date-display').textContent = formattedDate;

                                        // Load available time slots
                                        loadAvailableSlots(selectedDate);
                                    }

                                    if (step === 3) {
                                        // Update confirmation details
                                        const formattedDate = new Date(selectedDate).toLocaleDateString('id-ID', {
                                            weekday: 'long',
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric'
                                        });
                                        document.getElementById('confirm-date').textContent = formattedDate;

                                        // Render selected slots in confirmation
                                        renderSelectedSlots();
                                    }
                                }

                                // Next button click handler
                                nextBtn.addEventListener('click', function() {
                                    if (currentStep < totalSteps) {
                                        goToStep(currentStep + 1);
                                    }
                                });

                                // Previous button click handler
                                prevBtn.addEventListener('click', function() {
                                    if (currentStep > 1) {
                                        goToStep(currentStep - 1);
                                    }
                                });

                                // Submit button click handler (Add to Cart)
                                submitBtn.addEventListener('click', function() {
                                    // Disable button and show loading state
                                    this.disabled = true;
                                    const originalText = this.innerHTML;
                                    this.innerHTML = `
       <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
       <span class="ms-2">Menambahkan...</span>
   `;

                                    // Prepare data for API request
                                    const requestData = {
                                        type: 'field_booking',
                                        item_id: parseInt(fieldId),
                                        field_id: parseInt(fieldId),
                                        date: selectedDate,
                                        slots: Array.from(selectedSlots)
                                    };

                                    // Log data yang akan dikirim (untuk debugging)
                                    console.log('Sending data:', requestData);

                                    // Get CSRF token
                                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                                    // Send request to add to cart
                                    fetch('/cart/add', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': csrfToken,
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify(requestData)
                                        })
                                        .then(response => {
                                            // Log response status (untuk debugging)
                                            console.log('Response status:', response.status);

                                            return response.json().then(data => {
                                                if (!response.ok) {
                                                    throw new Error(data.message || 'Error: ' + response
                                                        .statusText);
                                                }
                                                return data;
                                            });
                                        })
                                        .then(data => {
                                            // Log response data (untuk debugging)
                                            console.log('Response data:', data);

                                            if (data.success) {
                                                // Show success message
                                                showToast('Success', data.message, 'success');

                                                // Update cart count in navbar if exists
                                                const cartCountElement = document.querySelector('.cart-count');
                                                if (cartCountElement) {
                                                    cartCountElement.textContent = data.cart_count;
                                                }

                                                // Redirect to cart page or stay on current page based on preference
                                                setTimeout(() => {
                                                    window.location.href = '/cart';
                                                }, 1500);
                                            } else {
                                                throw new Error(data.message || 'Failed to add to cart');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error adding to cart:', error);
                                            showToast('Error', error.message ||
                                                'Gagal menambahkan ke keranjang. Silakan coba lagi.', 'error');

                                            // Restore button state
                                            this.disabled = false;
                                            this.innerHTML = originalText;
                                        });
                                });

                                // Function to load available slots
                                function loadAvailableSlots(date) {
                                    const slotsWrapper = document.getElementById('time-slots-wrapper');

                                    // Show loading state
                                    slotsWrapper.innerHTML = `
       <div class="text-center py-4 slot-placeholder">
           <div class="spinner-border text-danger" role="status">
               <span class="visually-hidden">Loading...</span>
           </div>
           <p class="mt-2">Mengambil slot waktu yang tersedia...</p>
       </div>
   `;

                                    // Fetch available slots from the server
                                    fetch(`/fields/${fieldId}/available-slots?date=${date}`)
                                        .then(response => {
                                            if (!response.ok) {
                                                throw new Error('Network response was not ok');
                                            }
                                            return response.json();
                                        })
                                        .then(data => {
                                            renderTimeSlots(data);
                                        })
                                        .catch(error => {
                                            console.error('Error fetching available slots:', error);
                                            slotsWrapper.innerHTML = `
               <div class="alert alert-danger" role="alert">
                   <i class="fas fa-exclamation-circle me-2"></i>
                   Gagal memuat slot waktu. Silakan coba lagi nanti.
               </div>
           `;
                                        });
                                }

                                // Function to render time slots
                                function renderTimeSlots(slots) {
                                    const slotsWrapper = document.getElementById('time-slots-wrapper');
                                    const availableSlotsCount = document.getElementById('available-slots-count');

                                    // Clear previous content
                                    slotsWrapper.innerHTML = '';

                                    // Count available slots
                                    const availableCount = slots.filter(slot => slot.is_available).length;
                                    availableSlotsCount.textContent = `${availableCount} slot`;

                                    // If no available slots
                                    if (availableCount === 0) {
                                        slotsWrapper.innerHTML = `
           <div class="alert alert-warning" role="alert">
               <i class="fas fa-exclamation-triangle me-2"></i>
               Tidak ada slot waktu yang tersedia pada tanggal ini. Silakan pilih tanggal lain.
           </div>
       `;
                                        return;
                                    }

                                    // Create grid for slots
                                    const slotGrid = document.createElement('div');
                                    slotGrid.classList.add('time-slots-grid');

                                    // Add slots to grid
                                    slots.forEach(slot => {
                                        const slotDiv = document.createElement('div');

                                        let statusClass = '';
                                        let statusIcon = '';
                                        let isDisabled = false;
                                        let statusText = '';

                                        switch (slot.status) {
                                            case 'booked':
                                                statusClass = 'slot-booked';
                                                statusIcon = '<i class="fas fa-lock"></i>';
                                                isDisabled = true;
                                                break;
                                            case 'membership':
                                                statusClass = 'slot-membership';
                                                statusIcon = '<i class="fas fa-users"></i>';
                                                statusText = 'Member';
                                                isDisabled = true;
                                                break;
                                            case 'in_cart':
                                                statusClass = 'slot-in-cart';
                                                statusIcon = '<i class="fas fa-shopping-cart"></i>';
                                                break;
                                            case 'available':
                                                statusClass = 'slot-available';
                                                statusIcon = '<i class="fas fa-clock"></i>';
                                                break;
                                        }
                                        const isSelected = selectedSlots.has(slot.display);
                                        if (isSelected) {
                                            statusClass = 'slot-selected';
                                            statusIcon = '<i class="fas fa-check"></i>';
                                        }

                                        slotDiv.className = `time-slot ${statusClass} ${isDisabled ? 'disabled' : ''}`;
                                        slotDiv.dataset.slot = slot.display;
                                        slotDiv.dataset.price = slot.price;
                                        slotDiv.dataset.status = slot.status;

                                        slotDiv.innerHTML = `
           <div class="slot-time">
               ${statusIcon}
               <span>${slot.display}</span>
           </div>
           <div class="slot-price">Rp ${slot.price.toLocaleString('id')}</div>
       `;

                                        slotGrid.appendChild(slotDiv);
                                    });

                                    slotsWrapper.appendChild(slotGrid);

                                    // Add slot click event listeners
                                    document.querySelectorAll('.time-slot:not(.disabled)').forEach(slotElement => {
                                        slotElement.addEventListener('click', function() {
                                            const slotTime = this.dataset.slot;
                                            const slotPrice = parseFloat(this.dataset.price);
                                            const slotStatus = this.dataset.status;

                                            // If already in cart, show message and skip
                                            if (slotStatus === 'in_cart') {
                                                showToast('Info', 'Slot waktu ini sudah ada di keranjang Anda', 'info');
                                                return;
                                            }

                                            // Toggle selection
                                            if (selectedSlots.has(slotTime)) {
                                                // Remove from selected slots
                                                selectedSlots.delete(slotTime);
                                                this.classList.remove('slot-selected');

                                                // Update icon
                                                const iconElement = this.querySelector('.slot-time i');
                                                if (slotStatus === 'available') {
                                                    iconElement.className = 'fas fa-clock';
                                                } else if (slotStatus === 'in_cart') {
                                                    iconElement.className = 'fas fa-shopping-cart';
                                                }

                                                // Update selectedSlotsData
                                                selectedSlotsData = selectedSlotsData.filter(s => s.slot !== slotTime);

                                                // Update total price
                                                totalPrice -= slotPrice;
                                            } else {
                                                // Add to selected slots
                                                selectedSlots.add(slotTime);
                                                this.classList.add('slot-selected');

                                                // Update icon
                                                const iconElement = this.querySelector('.slot-time i');
                                                iconElement.className = 'fas fa-check';

                                                // Update selectedSlotsData
                                                selectedSlotsData.push({
                                                    slot: slotTime,
                                                    price: slotPrice
                                                });

                                                // Update total price
                                                totalPrice += slotPrice;
                                            }

                                            // Enable/disable next button based on selection
                                            nextBtn.disabled = selectedSlots.size === 0;
                                        });
                                    });
                                }

                                // Function to render selected slots in confirmation step
                                function renderSelectedSlots() {
                                    const selectedSlotsList = document.getElementById('selected-slots-list');
                                    const totalPriceElement = document.getElementById('total-price');

                                    // Clear previous content
                                    selectedSlotsList.innerHTML = '';

                                    // Add selected slots to list
                                    selectedSlotsData.forEach(slotData => {
                                        const listItem = document.createElement('li');
                                        listItem.classList.add('list-group-item', 'd-flex', 'justify-content-between',
                                            'align-items-center');

                                        listItem.innerHTML = `
           <div>
               <i class="far fa-clock text-danger me-2"></i>
               <span>${slotData.slot}</span>
           </div>
           <span class="text-secondary">Rp ${slotData.price.toLocaleString('id')}</span>
       `;

                                        selectedSlotsList.appendChild(listItem);
                                    });

                                    // Update total price
                                    totalPriceElement.textContent = `Rp ${totalPrice.toLocaleString('id')}`;
                                }

                                // Helper function to show toast notifications
                                function showToast(title, message, type) {
                                    // Check if toastr is available
                                    if (typeof toastr !== 'undefined') {
                                        toastr[type](message, title);
                                    } else {
                                        // Use Bootstrap toast if available
                                        if (typeof bootstrap !== 'undefined') {
                                            // Create toast element
                                            const toastEl = document.createElement('div');
                                            toastEl.className =
                                                `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'info' ? 'info' : 'warning'} border-0`;
                                            toastEl.setAttribute('role', 'alert');
                                            toastEl.setAttribute('aria-live', 'assertive');
                                            toastEl.setAttribute('aria-atomic', 'true');

                                            toastEl.innerHTML = `
               <div class="d-flex">
                   <div class="toast-body">
                       <strong>${title}:</strong> ${message}
                   </div>
                   <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
               </div>
           `;

                                            // Add to container
                                            const toastContainer = document.querySelector('.toast-container');
                                            if (!toastContainer) {
                                                const container = document.createElement('div');
                                                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                                                document.body.appendChild(container);
                                                container.appendChild(toastEl);
                                            } else {
                                                toastContainer.appendChild(toastEl);
                                            }

                                            // Show toast
                                            const toast = new bootstrap.Toast(toastEl);
                                            toast.show();
                                        } else {
                                            // Fallback to alert
                                            alert(`${title}: ${message}`);
                                        }
                                    }
                                }

                                // Initialize the wizard
                                goToStep(1);
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- Include custom field booking JS -->


    <style>
        /* Wizard Booking Process Styling */
        .booking-wizard {
            position: relative;
            margin-bottom: 2.5rem;
        }

        /* Progress Bar Container */
        .wizard-progress {
            display: flex;
            position: relative;
            margin-bottom: 2rem;
            padding: 0 10px;
        }

        .wizard-progress::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #e9ecef;
            z-index: 1;
        }

        /* Progress Bar Active Line */
        .wizard-progress-bar {
            position: absolute;
            top: 20px;
            left: 0;
            height: 4px;
            background-color: #9e0620;
            transition: width 0.5s ease;
            z-index: 2;
        }

        /* Step Item Styling */
        .wizard-step {
            flex: 1;
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        /* Step Circle */
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
            position: relative;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Circle Icon */
        .step-circle i {
            font-size: 1rem;
            display: none;
        }

        /* Step Status Classes */
        .wizard-step.active .step-circle {
            border-color: #9e0620;
            background-color: #9e0620;
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(158, 6, 32, 0.3);
        }

        .wizard-step.completed .step-circle {
            border-color: #9e0620;
            background-color: #9e0620;
            color: white;
        }

        .wizard-step.completed .step-circle span {
            display: none;
        }

        .wizard-step.completed .step-circle i {
            display: inline;
        }

        /* Step Label Text */
        .step-label {
            color: #6c757d;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            transition: color 0.3s ease;
        }

        .wizard-step.active .step-label,
        .wizard-step.completed .step-label {
            color: #212529;
        }

        /* Step Description */
        .step-desc {
            color: #adb5bd;
            font-size: 0.8rem;
            display: none;
        }

        .wizard-step.active .step-desc {
            color: #9e0620;
            display: block;
        }

        /* Wizard Content Container */
        .wizard-content {
            position: relative;
            overflow: hidden;
            min-height: 300px;
        }

        /* Step Panels */
        .wizard-panel {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .wizard-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Navigation Buttons */
        .wizard-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .wizard-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .wizard-btn-prev {
            background-color: #f8f9fa;
            color: #495057;
        }

        .wizard-btn-prev:hover {
            background-color: #e9ecef;
            transform: translateX(-5px);
        }

        .wizard-btn-next {
            background-color: #9e0620;
            color: white;
        }

        .wizard-btn-next:hover {
            background-color: #bb2d3b;
            transform: translateX(5px);
        }

        .wizard-btn-submit {
            background-color: #9e0620;
            color: white;
        }

        .wizard-btn-submit:hover {
            background-color: #bb2d3b;
            transform: scale(1.05);
        }

        .wizard-btn i {
            transition: transform 0.3s ease;
        }

        .wizard-btn-prev:hover i {
            transform: translateX(-3px);
        }

        .wizard-btn-next:hover i {
            transform: translateX(3px);
        }

        .wizard-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Calendar and Time Slot Specific Styling */

        /* Time Slots */
        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 8px;
            width: 100%;
        }

        .time-slot {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid #e9ecef;
        }

        .time-slot:not(.disabled):hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-color: #9e0620;
        }

        .slot-time {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 50px;
            text-align: center;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .slot-time i {
            font-size: 0.85rem;
            color: #6c757d;
            margin-right: 5px;
        }

        .slot-price {
            background-color: #f8f9fa;
            padding: 0.5rem;
            text-align: center;
            font-size: 0.8rem;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }

        /* Time Slot States */
        .time-slot.slot-available:hover {
            border-color: #28a745;
        }

        .time-slot.slot-selected {
            border-color: #9e0620;
            background-color: #fff8f8;
        }

        .time-slot.slot-selected .slot-time {
            color: #9e0620;
        }

        .time-slot.slot-selected .slot-time i {
            color: #9e0620;
        }

        .time-slot.slot-booked {
            border-color: #6c757d;
            background-color: #f8f9fa;
            opacity: 0.7;
            cursor: not-allowed;
        }

        .time-slot.slot-in-cart {
            border-color: #fd7e14;
            background-color: #fff8f1;
        }

        .time-slot.slot-in-cart .slot-time i {
            color: #fd7e14;
        }

        /* Selected Slots List */
        .selected-slots-list {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            width: 100%;
        }

        .selected-slots-list .list-group-item {
            background-color: transparent;
            border-color: #e9ecef;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .selected-slots-list .list-group-item:hover {
            background-color: #fff;
            transform: translateX(5px);
        }

        /* Confirmation Details */
        .confirmation-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
        }

        .confirmation-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .confirmation-item:last-child {
            border-bottom: none;
        }

        .confirmation-item .label {
            font-weight: 600;
            color: #495057;
        }

        .confirmation-item .value {
            color: #212529;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .step-desc {
                display: none !important;
            }

            .wizard-progress::before {
                top: 15px;
            }

            .wizard-progress-bar {
                top: 15px;
            }

            .step-circle {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }

            .step-label {
                font-size: 0.8rem;
            }

            .time-slots-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .wizard-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .wizard-btn {
                width: 100%;
                justify-content: center;
            }

            .wizard-btn-prev {
                order: 2;
            }

            .wizard-btn-next,
            .wizard-btn-submit {
                order: 1;
            }
        }

/* Custom Flatpickr Theme - brand color #9e0620 */
/* Base Calendar Container */
.flatpickr-calendar {
    width: 100% !important;
    max-width: 320px !important;
    box-sizing: border-box !important;
    padding: 0 !important;
    margin: 0 auto !important;
    touch-action: manipulation;
}

/* Month Navigation Section */
.flatpickr-months {
    background-color: #ffffff;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.flatpickr-month {
    color: #fff;
}

.flatpickr-current-month {
    font-weight: 600;
}

.flatpickr-monthDropdown-months,
.numInputWrapper span.arrowUp,
.numInputWrapper span.arrowDown {
    color: #fff;
}

.flatpickr-prev-month,
.flatpickr-next-month {
    fill: #fff;
}

.flatpickr-prev-month:hover svg,
.flatpickr-next-month:hover svg {
    fill: #e9ecef;
}

/* Weekday Headers */
span.flatpickr-weekday {
    color: #9e0620;
    font-weight: 600;
    width: 14.2857% !important;
    max-width: 14.2857% !important;
    flex-basis: 14.2857% !important;
}

/* Days Container */
.flatpickr-days {
    width: 100% !important;
}

.dayContainer {
    width: 100% !important;
    min-width: 100% !important;
    max-width: 100% !important;
    display: flex;
    flex-wrap: wrap;
}

/* Day Cells */
.flatpickr-day {
    width: 14.2857% !important;
    max-width: 14.2857% !important;
    flex-basis: 14.2857% !important;
    height: 40px !important;
    line-height: 40px !important;
    margin: 0 !important;
    border-radius: 24px !important;
}

/* Day States: Hover */
.flatpickr-day:hover {
    background: #fff8f8;
    border-color: #fff8f8;
}

/* Day States: Today */
.flatpickr-day.today {
    border-color: #9e0620;
}

.flatpickr-day.today:hover {
    background: #fff8f8;
    color: #9e0620;
}

/* Day States: Selected */
.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange,
.flatpickr-day.selected.inRange,
.flatpickr-day.startRange.inRange,
.flatpickr-day.endRange.inRange,
.flatpickr-day.selected:focus,
.flatpickr-day.startRange:focus,
.flatpickr-day.endRange:focus,
.flatpickr-day.selected:hover,
.flatpickr-day.startRange:hover,
.flatpickr-day.endRange:hover,
.flatpickr-day.selected.prevMonthDay,
.flatpickr-day.startRange.prevMonthDay,
.flatpickr-day.endRange.prevMonthDay,
.flatpickr-day.selected.nextMonthDay,
.flatpickr-day.startRange.nextMonthDay,
.flatpickr-day.endRange.nextMonthDay {
    background: #9e0620;
    border-color: #9e0620;
    color: #fff;
}

/* Range Selection */
.flatpickr-day.selected.startRange+.endRange:not(:nth-child(7n+1)),
.flatpickr-day.startRange.startRange+.endRange:not(:nth-child(7n+1)),
.flatpickr-day.endRange.startRange+.endRange:not(:nth-child(7n+1)) {
    box-shadow: -10px 0 0 #9e0620;
}

/* Next Month Days (within booking window) */
.flatpickr-day.nextMonthDay:not(.flatpickr-disabled) {
    color: #393939 !important;
    font-weight: normal !important;
    background-color: transparent !important;
    opacity: 1 !important;
}

.flatpickr-day.nextMonthDay:not(.flatpickr-disabled):hover {
    background-color: #fff8f8 !important;
    border-color: #fff8f8 !important;
    color: #9e0620 !important;
}

.flatpickr-day.nextMonthDay.selected {
    background-color: #9e0620 !important;
    border-color: #9e0620 !important;
    color: #fff !important;
}

/* Mobile Adjustments */
@media (max-width: 576px) {
    .flatpickr-calendar {
        max-width: 100%;
    }

    .flatpickr-day {
        height: 35px !important;
        line-height: 35px !important;
    }
}
        .slot-membership {
            background-color: #ffeeba;
            /* Warna kuning lembut */
            border-color: #ffdf7e;
            cursor: not-allowed;
        }

        .slot-membership .slot-time {
            color: #856404;
            /* Warna text kuning gelap */
        }

        /* Photographer Card Styles */
        .photographer-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            background: white;
            border: 1px solid #e9ecef;
        }

        .photographer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        .photographer-card .package-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }

        .photographer-card .package-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .photographer-card .package-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .photographer-card.favorite .package-icon {
            background-color: #fff8f1;
            color: #fd7e14;
        }

        .photographer-card.plus .package-icon {
            background-color: #f1f9fe;
            color: #0d6efd;
        }

        .photographer-card.exclusive .package-icon {
            background-color: #fff1f1;
            color: #9e0620;
        }

        .photographer-card .package-title {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }

        .photographer-card .package-subtitle {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        .photographer-card .badge {
            border-radius: 20px;
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .photographer-card .badge.favorite {
            background-color: #fff8f1;
            color: #fd7e14;
        }

        .photographer-card .badge.plus {
            background-color: #f1f9fe;
            color: #0d6efd;
        }

        .photographer-card .badge.exclusive {
            background-color: #fff1f1;
            color: #9e0620;
        }

        .photographer-card .package-features {
            padding: 1.25rem;
            flex-grow: 1;
        }

        .photographer-card .package-features li {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: flex-start;
        }

        .photographer-card .package-features i {
            margin-top: 0.25rem;
            flex-shrink: 0;
        }

        .photographer-card .package-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .photographer-card .price-info {
            display: flex;
            flex-direction: column;
        }

        .photographer-card .price {
            font-weight: 700;
            font-size: 1.1rem;
            color: #212529;
        }

        .photographer-card .duration {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .photographer-card .select-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .photographer-card .select-btn.favorite {
            background-color: #fff8f1;
            color: #fd7e14;
        }

        .photographer-card .select-btn.plus {
            background-color: #f1f9fe;
            color: #0d6efd;
        }

        .photographer-card .select-btn.exclusive {
            background-color: #fff1f1;
            color: #9e0620;
        }

        .photographer-card .select-btn:hover {
            transform: translateX(5px);
        }


    </style>
@endsection
