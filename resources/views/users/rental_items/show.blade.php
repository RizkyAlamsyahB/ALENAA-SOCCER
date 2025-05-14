@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/users/maincourt.css') }}">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/default.css">

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
                    <a href="{{ route('user.rental_items.index') }}" class="breadcrumb-link">
                        <i class="fas fa-shopping-bag"></i>
                        <span>Rental Equipment</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-futbol"></i>
                    <span>{{ $rentalItem->name }}</span>
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
                        @if ($rentalItem->image)
                            <img src="{{ Storage::url($rentalItem->image) }}" class="img-fluid w-100"
                                alt="{{ $rentalItem->name }}">
                        @else
                            <img src="{{ asset('assets/placeholder.jpg') }}" class="img-fluid w-100"
                                alt="{{ $rentalItem->name }}">
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
                            @if ($rentalItem->stock_available > 0)
                                <i class="fas fa-check-circle me-1"></i>
                                Available Now
                            @else
                                <i class="fas fa-times-circle me-1"></i>
                                Out of Stock
                            @endif
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
                                @if ($rentalItem->image)
                                    <img src="{{ Storage::url($rentalItem->image) }}" class="img-fluid w-100"
                                        alt="{{ $rentalItem->name }}">
                                @else
                                    <img src="{{ asset('assets/placeholder.jpg') }}" class="img-fluid w-100"
                                        alt="{{ $rentalItem->name }}">
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
                                @if ($rentalItem->image)
                                    <img src="{{ Storage::url($rentalItem->image) }}" class="img-fluid w-100"
                                        alt="{{ $rentalItem->name }}">
                                @else
                                    <img src="{{ asset('assets/placeholder.jpg') }}" class="img-fluid w-100"
                                        alt="{{ $rentalItem->name }}">
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
                            @if ($rentalItem->image)
                                <img src="{{ Storage::url($rentalItem->image) }}" class="d-block w-100"
                                    alt="{{ $rentalItem->name }}">
                            @else
                                <img src="{{ asset('assets/placeholder.jpg') }}" class="d-block w-100"
                                    alt="{{ $rentalItem->name }}">
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
                            @if ($rentalItem->image)
                                <img src="{{ Storage::url($rentalItem->image) }}" class="d-block w-100"
                                    alt="{{ $rentalItem->name }}">
                            @else
                                <img src="{{ asset('assets/placeholder.jpg') }}" class="d-block w-100"
                                    alt="{{ $rentalItem->name }}">
                            @endif
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
                            @if ($rentalItem->image)
                                <img src="{{ Storage::url($rentalItem->image) }}" class="d-block w-100"
                                    alt="{{ $rentalItem->name }}">
                            @else
                                <img src="{{ asset('assets/placeholder.jpg') }}" class="d-block w-100"
                                    alt="{{ $rentalItem->name }}">
                            @endif
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
                        @if ($rentalItem->stock_available > 0)
                            <i class="fas fa-check-circle"></i>
                            Available Now
                        @else
                            <i class="fas fa-times-circle"></i>
                            Out of Stock
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <!-- Main Information -->
        <div class="row">
            <!-- Item Details Container -->
            <div class="container py-4">
                <div class="row">
                    <div class="col">
                        <!-- Basic Information Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3 flex-column flex-md-row">
                                    <div>
                                        <h1 class="h4 mb-2 text-center text-md-start fw-bold">{{ $rentalItem->name }}</h1>
                                        <div
                                            class="d-flex flex-column flex-md-row align-items-center gap-3 text-center text-md-start">
                                            <div class="d-flex align-items-center category-badge">
                                                @if ($rentalItem->category == 'ball')
                                                    <i class="fas fa-futbol text-danger me-2"></i>
                                                    <span class="text-secondary">Bola</span>
                                                @elseif($rentalItem->category == 'jersey')
                                                    <i class="fas fa-tshirt text-danger me-2"></i>
                                                    <span class="text-secondary">Jersey</span>
                                                @elseif($rentalItem->category == 'shoes')
                                                    <i class="fas fa-shoe-prints text-danger me-2"></i>
                                                    <span class="text-secondary">Sepatu</span>
                                                @else
                                                    <i class="fas fa-mitten text-danger me-2"></i>
                                                    <span class="text-secondary">Aksesoris</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center stock-badge">
                                                @if ($rentalItem->stock_available > 0)
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    <span class="text-secondary">Tersedia
                                                        ({{ $rentalItem->stock_available }} stok)</span>
                                                @else
                                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                                    <span class="text-secondary">Stok Habis</span>
                                                @endif
                                            </div>
                                            {{-- Badge Rating --}}
                                            <div class="d-flex align-items-center rating-badge ms-0 ms-md-3 mt-2 mt-md-0">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                <span class="text-secondary">
                                                    {{ number_format($rentalItem->rating ?? 0, 1) }}
                                                    ({{ $rentalItem->reviews_count ?? 0 }} reviews)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Wizard Card -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-header bg-white py-3 border-0 px-4">
                                <h5 class="mb-0 fw-bold">Pilih Jadwal Rental</h5>
                            </div>
                            <div class="card-body p-4">
                                <!-- Hidden rental item ID -->
                                <input type="hidden" id="rentalItemId" value="{{ $rentalItem->id }}">

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
                                            <div class="step-desc">Pilih tanggal rental</div>
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

                                        <!-- Step 3: Quantity Selection -->
                                        <div class="wizard-step" id="wizard-step-3">
                                            <div class="step-circle">
                                                <span>3</span>
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="step-label">Jumlah</div>
                                            <div class="step-desc">Konfirmasi jumlah</div>
                                        </div>
                                    </div>

                                    <!-- Wizard Content -->
                                    <div class="wizard-content">
                                        <!-- Panel 1: Date Selection -->
                                        <div class="wizard-panel" id="panel-date">
                                            <h6 class="fw-semibold mb-3">Pilih Tanggal Rental</h6>
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
                                                <div class="alert alert-info mb-3">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Pilih slot waktu dan tentukan jumlah item untuk setiap slot. Klik
                                                    slot untuk memilih.
                                                </div>
                                                <div id="time-slots-wrapper" class="time-slots-container">
                                                    <!-- Konten slot waktu akan ditambahkan melalui JavaScript -->
                                                </div>

                                                <!-- Tambahkan bagian untuk slot yang dipilih -->
                                                <div id="selected-slots-section" class="selected-slots-section mt-4"
                                                    style="display: none;">
                                                    <h6 class="fw-semibold mb-3">Slot Waktu yang Dipilih</h6>
                                                    <div id="selected-slots-container" class="selected-slots-container">
                                                        <!-- Slot terpilih dengan kontrol kuantitas akan ditampilkan di sini -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Panel 3: Quantity Selection -->
                                        <div class="wizard-panel" id="panel-quantity">
                                            <div class="mb-3">
                                                <h6 class="fw-semibold mb-3">Konfirmasi Pemesanan</h6>

                                                <div class="selected-time-info mb-4 p-3 bg-light rounded">
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <span class="fw-semibold">Durasi Rental:</span>
                                                        <span id="selected-duration-display"
                                                            class="text-danger fw-bold"></span>
                                                    </div>

                                                    <div class="selected-slots-summary my-3">
                                                        <h6 class="fw-semibold mb-2">Rincian Slot Waktu:</h6>
                                                        <div id="selected-slots-detail" class="selected-slots-detail">
                                                            <!-- Slot details with quantities will be added here -->
                                                        </div>
                                                    </div>

                                                    <div
                                                        class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                                        <span class="fw-bold">Total:</span>
                                                        <span id="subtotal-price" class="text-danger fw-bold">Rp
                                                            0</span>
                                                    </div>
                                                </div>

                                                <div class="mt-4 text-center">
                                                    <button id="confirm-quantity-btn"
                                                        class="btn btn-danger px-4 py-2 rounded-pill">
                                                        <i class="fas fa-cart-plus me-2"></i>
                                                        Tambahkan ke Keranjang
                                                    </button>
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
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Initialize variables
                                const rentalItemId = document.getElementById('rentalItemId').value;
                                let selectedDate = '';
                                let selectedSlots = new Set(); // Menggunakan Set untuk menyimpan waktu yang dipilih
                                let selectedSlotsData = []; // Untuk menyimpan data slot yang dipilih (termasuk harga)
                                let selectedQuantity = 1;
                                let availableStock = 0;
                                let itemPrice = {{ $rentalItem->rental_price }};
                                let totalPrice = 0;
                                let currentStep = 1;
                                const totalSteps = 3;

                                // DOM Elements
                                const progressBar = document.getElementById('wizard-progress-bar');
                                const wizardSteps = document.querySelectorAll('.wizard-step');
                                const wizardPanels = document.querySelectorAll('.wizard-panel');

                                const prevBtn = document.getElementById('wizard-prev-btn');
                                const nextBtn = document.getElementById('wizard-next-btn');
                                const confirmQuantityBtn = document.getElementById('confirm-quantity-btn');

                                // Quantity selector elements
                                const decreaseBtn = document.getElementById('decrease-quantity');
                                const increaseBtn = document.getElementById('increase-quantity');
                                const quantityInput = document.getElementById('quantity-input');

                                // Initialize Flatpickr inline calendar
                                const calendar = flatpickr("#inline-calendar", {
                                    inline: true,
                                    locale: 'id',
                                    dateFormat: 'Y-m-d',
                                    minDate: 'today',
                                    maxDate: new Date().fp_incr(6), // Maksimal 7 hari ke depan
                                    disableMobile: true,
                                    onChange: function(selectedDates, dateStr) {
                                        selectedDate = dateStr;
                                        document.getElementById('selectedDate').value = dateStr;

                                        // Reset selections when date changes
                                        selectedSlots.clear();
                                        selectedSlotsData = [];
                                        totalPrice = 0;

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

                                    if (step === 3 && selectedSlots.size > 0) {
                                        // Display duration
                                        const hoursCount = selectedSlots.size;
                                        document.getElementById('selected-duration-display').textContent = `${hoursCount} jam`;

                                        // Pastikan elemen 'duration-hours' ada sebelum mencoba memperbarui
                                        const durationHours = document.getElementById('duration-hours');
                                        if (durationHours) {
                                            durationHours.textContent = `${hoursCount} jam`;
                                        }

                                        // Perbarui tampilan slot yang dipilih
                                        updateSelectedSlotsDetail();

                                        // Update price calculation
                                        updateTotalPrice();
                                    }
                                }

                                // Function untuk memperbarui detail slot yang dipilih di panel 3
                                function updateSelectedSlotsDetail() {
                                    const slotsDetailElement = document.getElementById('selected-slots-detail');
                                    if (!slotsDetailElement) return;

                                    // Clear previous content
                                    slotsDetailElement.innerHTML = '';

                                    // Sort slots by time before displaying
                                    const sortedSlots = [...selectedSlotsData].sort((a, b) => {
                                        const timeA = a.slot.split(' - ')[0];
                                        const timeB = b.slot.split(' - ')[0];
                                        return timeA.localeCompare(timeB);
                                    });

                                    // Display each slot with quantity
                                    sortedSlots.forEach(slotData => {
                                        const slotDiv = document.createElement('div');
                                        slotDiv.className =
                                            'selected-slot-item d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom';
                                        slotDiv.innerHTML = `
                <div>
                    <i class="far fa-clock text-danger me-2"></i>
                    <span>${slotData.slot}</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary me-2">${slotData.quantity} unit</span>
                    <span class="text-danger">Rp ${(slotData.price * slotData.quantity).toLocaleString('id')}</span>
                </div>
            `;
                                        slotsDetailElement.appendChild(slotDiv);
                                    });
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

                                // Confirmation button click handler
                                confirmQuantityBtn.addEventListener('click', function() {
                                    // Disable button dan tampilkan loading state
                                    this.disabled = true;
                                    const originalText = this.innerHTML;
                                    this.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="ms-2">Menambahkan...</span>
        `;

                                    // Persiapkan array slot waktu
                                    const slots = [];

                                    // Untuk setiap slot yang dipilih, tambahkan ke array
                                    selectedSlotsData.forEach(slotData => {
                                        slots.push({
                                            start_time: slotData.slot.split(' - ')[0],
                                            end_time: slotData.slot.split(' - ')[1],
                                            quantity: slotData.quantity ||
                                                1 // Gunakan quantity per slot jika ada, atau default 1
                                        });
                                    });

                                    // Persiapkan data untuk API request
                                    const requestData = {
                                        type: 'rental_item',
                                        rental_item_id: parseInt(rentalItemId),
                                        date: selectedDate,
                                        slots: slots
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
                                    fetch(`/rental/items/${rentalItemId}/available-slots?date=${date}`)
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

                                        switch (slot.status) {
                                            case 'fully_booked':
                                                statusClass = 'slot-booked';
                                                statusIcon = '<i class="fas fa-lock"></i>';
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
                                        slotDiv.dataset.availableQuantity = slot.available_quantity;

                                        // Persiapkan tampilan stok
                                        let availabilityText = `${slot.available_quantity} tersedia`;
                                        let availabilityClass = slot.available_quantity > 0 ? 'success' : 'danger';

                                        // Jika ada item di keranjang pengguna lain, tambahkan informasi (opsional)
                                        if (slot.other_cart_quantity && slot.other_cart_quantity > 0) {
                                            availabilityText += ` (${slot.other_cart_quantity} di keranjang lain)`;
                                        }

                                        slotDiv.innerHTML = `
                <div class="slot-time">
                    ${statusIcon}
                    <span>${slot.display}</span>
                </div>
                <div class="slot-price">
                    <div>Rp ${slot.price.toLocaleString('id')}</div>
                    <small class="text-${availabilityClass}">
                        ${availabilityText}
                    </small>
                </div>
            `;

                                        slotGrid.appendChild(slotDiv);
                                    });

                                    slotsWrapper.appendChild(slotGrid);

                                    // Pastikan bagian untuk slot yang dipilih ada di posisi yang benar
                                    let selectedSlotsSection = document.getElementById('selected-slots-section');
                                    if (!selectedSlotsSection) {
                                        selectedSlotsSection = document.createElement('div');
                                        selectedSlotsSection.id = 'selected-slots-section';
                                        selectedSlotsSection.className = 'selected-slots-section mt-4';
                                        selectedSlotsSection.style.display = 'none';

                                        selectedSlotsSection.innerHTML = `
                <h6 class="fw-semibold mb-3">Slot Waktu yang Dipilih</h6>
                <div id="selected-slots-container" class="selected-slots-container">
                    <!-- Slot terpilih dengan kontrol kuantitas akan ditampilkan di sini -->
                </div>
            `;

                                        // Pastikan ditempatkan di posisi yang benar (setelah time-slots-wrapper)
                                        slotsWrapper.parentNode.insertBefore(selectedSlotsSection, slotsWrapper.nextSibling);
                                    }

                                    // Tampilkan bagian slot terpilih jika ada slot yang sudah dipilih
                                    if (selectedSlots.size > 0) {
                                        selectedSlotsSection.style.display = 'block';
                                        const selectedSlotsContainer = document.getElementById('selected-slots-container');
                                        selectedSlotsContainer.innerHTML = '';

                                        // Tambahkan kembali slot yang sudah dipilih sebelumnya
                                        selectedSlotsData.forEach(slotData => {
                                            addSlotToSelectedView(slotData.slot, slotData.price, slotData.availableQuantity,
                                                slotData.quantity || 1);
                                        });

                                        // Tambahkan total di bagian bawah
                                        updateTotalPrice();
                                    }

                                    // Tambahkan event listener ke slot
                                    document.querySelectorAll('.time-slot:not(.disabled)').forEach(slotElement => {
                                        slotElement.addEventListener('click', function() {
                                            const slotTime = this.dataset.slot;
                                            const slotPrice = parseFloat(this.dataset.price);
                                            const slotStatus = this.dataset.status;
                                            const availableQty = parseInt(this.dataset.availableQuantity);

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

                                                // Hapus slot dari tampilan slot terpilih
                                                const selectedSlotElement = document.getElementById(
                                                    `selected-slot-${slotTime.replace(/\s+/g, '-').replace(/:/g, '')}`
                                                );
                                                if (selectedSlotElement) {
                                                    selectedSlotElement.remove();
                                                }
                                            } else {
                                                // Add to selected slots
                                                selectedSlots.add(slotTime);
                                                this.classList.add('slot-selected');

                                                // Update icon
                                                const iconElement = this.querySelector('.slot-time i');
                                                iconElement.className = 'fas fa-check';

                                                // Update selectedSlotsData dengan quantity default = 1
                                                selectedSlotsData.push({
                                                    slot: slotTime,
                                                    price: slotPrice,
                                                    availableQuantity: availableQty,
                                                    quantity: 1 // Set default quantity
                                                });

                                                // Tambahkan slot ke tampilan slot terpilih dengan kontrol kuantitas
                                                addSlotToSelectedView(slotTime, slotPrice, availableQty, 1);
                                            }

                                            // Tampilkan atau sembunyikan bagian slot terpilih
                                            const selectedSlotsSection = document.getElementById(
                                                'selected-slots-section');
                                            selectedSlotsSection.style.display = selectedSlots.size > 0 ? 'block' :
                                                'none';

                                            // Enable/disable next button based on selection
                                            nextBtn.disabled = selectedSlots.size === 0;

                                            // Perbarui total harga
                                            updateTotalPrice();
                                        });
                                    });
                                }

                                // Function untuk menambahkan slot ke tampilan slot terpilih
                                function addSlotToSelectedView(slotTime, slotPrice, availableQty, quantity) {
                                    const slotId = `selected-slot-${slotTime.replace(/\s+/g, '-').replace(/:/g, '')}`;
                                    const selectedSlotsContainer = document.getElementById('selected-slots-container');

                                    // Pastikan container ada
                                    if (!selectedSlotsContainer) return;

                                    const slotDiv = document.createElement('div');
                                    slotDiv.id = slotId;
                                    slotDiv.className = 'selected-slot-item p-3 bg-light rounded mb-2';
                                    slotDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="far fa-clock text-danger me-2"></i>
                    <span class="fw-semibold">${slotTime}</span>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-sm btn-outline-secondary decrease-slot-qty" data-slot="${slotTime}" ${quantity <= 1 ? 'disabled' : ''}>
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" class="form-control form-control-sm mx-2 slot-qty-input"
                           style="width: 60px; text-align: center;" value="${quantity}" min="1" max="${availableQty}"
                           data-slot="${slotTime}" readonly>
                    <button class="btn btn-sm btn-outline-secondary increase-slot-qty" data-slot="${slotTime}"
                           ${quantity >= availableQty ? 'disabled' : ''}>
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <small class="text-muted">Tersedia: ${availableQty} unit</small>
                <div class="slot-subtotal text-danger">Rp ${(slotPrice * quantity).toLocaleString('id')}</div>
            </div>
        `;

                                    selectedSlotsContainer.appendChild(slotDiv);

                                    // Tambahkan event listener untuk tombol kuantitas
                                    const decreaseBtn = slotDiv.querySelector('.decrease-slot-qty');
                                    const increaseBtn = slotDiv.querySelector('.increase-slot-qty');
                                    const qtyInput = slotDiv.querySelector('.slot-qty-input');
                                    const subtotalElement = slotDiv.querySelector('.slot-subtotal');

                                    decreaseBtn.addEventListener('click', function() {
                                        const slotTime = this.dataset.slot;
                                        const slotData = selectedSlotsData.find(s => s.slot === slotTime);

                                        if (slotData && slotData.quantity > 1) {
                                            slotData.quantity--;
                                            qtyInput.value = slotData.quantity;

                                            // Update subtotal untuk slot ini
                                            subtotalElement.textContent =
                                                `Rp ${(slotData.price * slotData.quantity).toLocaleString('id')}`;

                                            // Update tombol
                                            decreaseBtn.disabled = slotData.quantity <= 1;
                                            increaseBtn.disabled = false;

                                            // Update total harga
                                            updateTotalPrice();
                                        }
                                    });

                                    increaseBtn.addEventListener('click', function() {
                                        const slotTime = this.dataset.slot;
                                        const slotData = selectedSlotsData.find(s => s.slot === slotTime);

                                        if (slotData && slotData.quantity < slotData.availableQuantity) {
                                            slotData.quantity++;
                                            qtyInput.value = slotData.quantity;

                                            // Update subtotal untuk slot ini
                                            subtotalElement.textContent =
                                                `Rp ${(slotData.price * slotData.quantity).toLocaleString('id')}`;

                                            // Update tombol
                                            decreaseBtn.disabled = false;
                                            increaseBtn.disabled = slotData.quantity >= slotData.availableQuantity;

                                            // Update total harga
                                            updateTotalPrice();
                                        }
                                    });
                                }

                                // Update total price
                                function updateTotalPrice() {
                                    const subtotal = selectedSlotsData.reduce((total, slot) => {
                                        return total + (slot.price * (slot.quantity || 1));
                                    }, 0);

                                    // Jika berada di step 2, perbarui total yang ditampilkan di section slot terpilih
                                    if (currentStep === 2) {
                                        const selectedSlotsContainer = document.getElementById('selected-slots-container');
                                        if (selectedSlotsContainer && selectedSlots.size > 0) {
                                            // Cari atau buat elemen total
                                            let totalElement = document.getElementById('selected-slots-total');
                                            let totalContainer = document.getElementById('selected-slots-total-container');

                                            if (!totalContainer) {
                                                totalContainer = document.createElement('div');
                                                totalContainer.id = 'selected-slots-total-container';
                                                totalContainer.className =
                                                    'mt-3 pt-2 border-top d-flex justify-content-between align-items-center';
                                                totalContainer.innerHTML = `
                        <span class="fw-bold">Total:</span>
                        <span id="selected-slots-total" class="fw-bold text-danger">Rp ${subtotal.toLocaleString('id')}</span>
                    `;
                                                selectedSlotsContainer.appendChild(totalContainer);
                                            } else if (totalElement) {
                                                totalElement.textContent = `Rp ${subtotal.toLocaleString('id')}`;
                                            }
                                        }
                                    }

                                    // Perbarui juga tampilan di step 3 jika sedang di step tersebut
                                    if (currentStep === 3) {
                                        const subtotalPriceElement = document.getElementById('subtotal-price');
                                        if (subtotalPriceElement) {
                                            subtotalPriceElement.textContent = `Rp ${subtotal.toLocaleString('id')}`;
                                        }
                                    }
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
    </style>
    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
