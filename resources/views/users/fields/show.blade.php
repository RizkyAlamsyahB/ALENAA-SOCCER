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
                                                <span class="text-secondary">{{ $field->rating ?? '4.8' }}
                                                    ({{ $field->reviews_count ?? '128' }} reviews)</span>
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
                                            <small class="text-muted">{{ $field->type }}</small>
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

                        <!-- 1. Tambahkan CSS Flatpickr di header atau section head -->
                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                        <link rel="stylesheet"
                            href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

                        <!-- 2. Ganti bagian HTML kalender lama dengan ini -->
                        <div class="card border-0 rounded-4 shadow-sm hover-shadow mb-4">
                            <div class="card-header bg-white py-3 border-0 px-4">
                                <h5 class="mb-0 fw-bold">Pilih Jadwal Booking</h5>
                            </div>
                            <div class="card-body p-4">
                                <!-- Flatpickr Calendar -->
                                <div class="mb-4">
                                    <label for="flatpickr-calendar" class="form-label fw-semibold">Pilih Tanggal</label>
                                    <input type="text" id="flatpickr-calendar" class="form-control"
                                        placeholder="Pilih tanggal" readonly>
                                    <input type="hidden" id="selectedDate" name="selected_date">
                                    <input type="hidden" id="fieldId" value="{{ $field->id }}">
                                </div>

                                <!-- Time Slots Container -->
                                <div id="time-slots-container" class="mt-4">
                                    <div class="time-slot-loading text-center py-4">
                                        <p>Silakan pilih tanggal untuk melihat slot waktu yang tersedia</p>
                                    </div>
                                </div>

                                <!-- Selected Slots -->
                                <div id="selected-slots-container" class="mt-4 d-none">
                                    <h6 class="fw-semibold mb-3">Slot Waktu Terpilih</h6>
                                    <div class="selected-slots-list">
                                        <ul id="selected-slots-list" class="list-group">
                                            <!-- Selected slots will be added here -->
                                        </ul>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <span class="fw-bold">Total:</span>
                                            <span id="total-price" class="text-danger fw-bold fs-5">Rp 0</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between mt-4 gap-3">
                                    <a href="{{ route('user.fields.index') }}" class="btn-action btn-back">
                                        <i class="fas fa-arrow-left"></i>
                                        <span>Kembali</span>
                                    </a>
                                    <button id="add-to-cart-btn" class="btn-action btn-continue d-flex align-items-center"
                                        disabled>
                                        <span class="d-none d-md-inline me-2">Tambah ke Keranjang</span>
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Tambahkan script Flatpickr di bagian script -->
                        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
                        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

                        <!-- 4. Script untuk implementasi Flatpickr dan slot waktu -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const timeSlotContainer = document.getElementById('time-slots-container');
                                const selectedSlotsContainer = document.getElementById('selected-slots-container');
                                const selectedSlotsList = document.getElementById('selected-slots-list');
                                const totalPriceElement = document.getElementById('total-price');
                                const addToCartBtn = document.getElementById('add-to-cart-btn');
                                const fieldId = document.getElementById('fieldId').value;
                                const selectedDateInput = document.getElementById('selectedDate');

                                let selectedSlots = [];
                                let pricePerHour = {{ $field->price }};

                                // Inisialisasi Flatpickr
                                const flatpickrCalendar = flatpickr("#flatpickr-calendar", {
                                    locale: "id",
                                    dateFormat: "Y-m-d",
                                    minDate: "today",
                                    maxDate: new Date().getFullYear() + "-12-31", // Hingga akhir tahun berjalan
                                    disable: [
                                        function(date) {
                                            return false;
                                        }
                                    ],
                                    onChange: function(selectedDates, dateStr) {
                                        selectedDateInput.value = dateStr;
                                        selectedSlots = [];
                                        updateSelectedSlots();
                                        loadAvailableSlots(dateStr);
                                    },
                                    // Tambahkan konfigurasi tema
                                    theme: 'custom',
                                    // Anda juga bisa menambahkan inline CSS
                                    onReady: function(selectedDates, dateStr, instance) {
                                        instance.calendarContainer.style.backgroundColor = '#9e0620';
                                        instance.calendarContainer.style.color = 'white';
                                    }
                                });

                                // Load available slots for the selected date
                                function loadAvailableSlots(date) {
                                    timeSlotContainer.innerHTML = `
            <div class="time-slot-loading text-center py-4">
                <div class="spinner-border text-danger" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat slot waktu tersedia...</p>
            </div>
        `;

                                    fetch(`/user/fields/${fieldId}/available-slots?date=${date}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            displayTimeSlots(data);
                                        })
                                        .catch(error => {
                                            console.error('Error loading time slots:', error);
                                            timeSlotContainer.innerHTML = `
                    <div class="alert alert-danger">
                        Gagal memuat slot waktu. Silakan coba lagi.
                    </div>
                `;
                                        });
                                }

                                // Display time slots
                                function displayTimeSlots(slots) {
                                    if (!slots || slots.length === 0) {
                                        timeSlotContainer.innerHTML = `
                <div class="alert alert-info">
                    Tidak ada slot waktu tersedia untuk tanggal ini.
                </div>
            `;
                                        return;
                                    }

                                    let html = '<div class="row g-2">';

                                    slots.forEach((slot) => {
                                        let slotClass = '';
                                        let statusText = '';
                                        let statusClass = '';

                                        if (slot.in_cart) {
                                            slotClass = 'disabled in-cart';
                                            statusText = 'Sudah di keranjang';
                                            statusClass = 'in-cart';
                                        } else if (!slot.is_available) {
                                            slotClass = 'disabled';
                                            statusText = 'Terpesan';
                                            statusClass = 'booked';
                                        } else {
                                            statusText = 'Tersedia';
                                            statusClass = 'available';
                                        }

                                        html += `
                <div class="col-md-4 col-sm-6">
                    <div class="time-slot ${slotClass}" data-slot="${slot.display}" data-price="${slot.price}">
                        <div class="time-slot-content">
                            <i class="far fa-clock"></i>
                            <span>${slot.display}</span>
                            <small class="status ${statusClass}">${statusText}</small>
                        </div>
                    </div>
                </div>
            `;
                                    });

                                    html += '</div>';
                                    timeSlotContainer.innerHTML = html;

                                    // Tambahkan event listener untuk slot yang tersedia
                                    document.querySelectorAll('.time-slot:not(.disabled):not(.in-cart)').forEach(slot => {
                                        slot.addEventListener('click', function() {
                                            const slotTime = this.getAttribute('data-slot');
                                            const slotPrice = parseInt(this.getAttribute('data-price'));

                                            if (this.classList.contains('active')) {
                                                // Batalkan pilihan slot
                                                this.classList.remove('active');
                                                selectedSlots = selectedSlots.filter(s => s.time !== slotTime);
                                            } else {
                                                // Pilih slot
                                                this.classList.add('active');
                                                selectedSlots.push({
                                                    time: slotTime,
                                                    price: slotPrice
                                                });
                                            }

                                            updateSelectedSlots();
                                        });
                                    });
                                }

                                // Update selected slots display
                                function updateSelectedSlots() {
                                    if (selectedSlots.length > 0) {
                                        selectedSlotsContainer.classList.remove('d-none');
                                        selectedSlotsList.innerHTML = '';

                                        let totalPrice = 0;

                                        selectedSlots.forEach(slot => {
                                            const li = document.createElement('li');
                                            li.className = 'list-group-item d-flex justify-content-between align-items-center';
                                            li.innerHTML = `
                    <div>
                        <i class="far fa-clock me-2"></i>
                        <span>${slot.time}</span>
                    </div>
                    <span class="text-danger">Rp ${formatNumber(slot.price)}</span>
                `;
                                            selectedSlotsList.appendChild(li);

                                            totalPrice += slot.price;
                                        });

                                        totalPriceElement.textContent = `Rp ${formatNumber(totalPrice)}`;
                                        addToCartBtn.disabled = false;
                                    } else {
                                        selectedSlotsContainer.classList.add('d-none');
                                        addToCartBtn.disabled = true;
                                    }
                                }

                                // Fungsi untuk refresh cart sidebar
                                function refreshCartSidebar() {
                                    fetch('{{ route('user.fields.cart-sidebar') }}')
                                        .then(response => response.text())
                                        .then(html => {
                                            // Ganti konten cart sidebar
                                            const cartContainer = document.getElementById('cartSidebar');
                                            if (cartContainer) {
                                                cartContainer.outerHTML = html;

                                                // Tambahkan event listener untuk tombol hapus
                                                document.querySelectorAll('.remove-item-btn').forEach(button => {
                                                    button.addEventListener('click', function(e) {
                                                        e.preventDefault();
                                                        const form = this.closest('form');
                                                        const url = form.getAttribute('action');
                                                        const itemElement = this.closest('.cart-item');

                                                        fetch(url, {
                                                                method: 'DELETE',
                                                                headers: {
                                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                    'Content-Type': 'application/json',
                                                                    'Accept': 'application/json'
                                                                }
                                                            })
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                if (data.success) {
                                                                    itemElement.remove();

                                                                    // Update jumlah item di keranjang
                                                                    const cartCountBadge = document
                                                                        .querySelector('.cart-count');
                                                                    if (cartCountBadge) {
                                                                        cartCountBadge.textContent = data
                                                                            .cart_count;
                                                                    }

                                                                    // Refresh slot waktu jika ada tanggal yang dipilih
                                                                    if (selectedDateInput && selectedDateInput
                                                                        .value) {
                                                                        loadAvailableSlots(selectedDateInput
                                                                            .value);
                                                                    }
                                                                }
                                                            })
                                                            .catch(error => {
                                                                console.error('Error removing item:', error);
                                                            });
                                                    });
                                                });
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error refreshing cart sidebar:', error);
                                        });
                                }

                                // Panggil fungsi refreshCartSidebar di dalam blok addToCartBtn
                                addToCartBtn.addEventListener('click', function() {
                                    if (selectedSlots.length === 0 || !selectedDateInput.value) {
                                        return;
                                    }

                                    const date = selectedDateInput.value;
                                    const slots = selectedSlots.map(s => s.time);

                                    fetch('{{ route('user.fields.add-to-cart') }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                field_id: fieldId,
                                                date: date,
                                                slots: slots
                                            })
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                const cartCountBadge = document.querySelector('.cart-count');
                                                if (cartCountBadge) {
                                                    cartCountBadge.textContent = data.cart_count;
                                                }

                                                // Tambahkan refresh cart sidebar
                                                refreshCartSidebar();

                                                alert('Booking berhasil ditambahkan ke keranjang');

                                                selectedSlots = [];
                                                document.querySelectorAll('.time-slot.active').forEach(slot => {
                                                    slot.classList.remove('active');
                                                });
                                                updateSelectedSlots();

                                                loadAvailableSlots(date);
                                            } else {
                                                alert('Gagal menambahkan booking ke keranjang: ' + data.message);
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error adding to cart:', error);
                                            alert('Terjadi kesalahan. Silakan coba lagi.');
                                        });
                                });
                                // Format number with thousand separator
                                function formatNumber(number) {
                                    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                }

                                // Muat jumlah item keranjang saat halaman dimuat
                                fetch('{{ route('user.fields.cart-count') }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        const cartCountBadges = document.querySelectorAll('.cart-count');
                                        cartCountBadges.forEach(badge => {
                                            badge.textContent = data.count;
                                        });
                                    })
                                    .catch(error => {
                                        console.error('Error loading cart count:', error);
                                    });
                            });
                        </script>
                        <style>
                            .flatpickr-calendar {
                                background-color: #9e0620 !important;
                            }

                            .flatpickr-weekdays {
                                background-color: #9e0620 !important;
                            }

                            .flatpickr-weekday {
                                color: white !important;
                                background-color: #9e0620 !important;
                            }

                            .flatpickr-months .flatpickr-month {
                                background-color: #9e0620 !important;
                                color: white !important;
                            }

                            .flatpickr-current-month .flatpickr-monthDropdown-months {
                                background-color: #9e0620 !important;
                                color: white !important;
                            }

                            .flatpickr-weekdays {
                                background-color: #9e0620 !important;
                                color: white !important;
                            }

                            .flatpickr-weekday {
                                color: white !important;
                            }

                            .flatpickr-day:nth-of-type(7n) {
                                color: #9e0620 !important;
                                /* Warna teks tanggal untuk hari Minggu */
                            }

                            .flatpickr-day.selected,
                            .flatpickr-day.selected:hover,
                            .flatpickr-day.startRange,
                            .flatpickr-day.endRange {
                                background-color: white !important;
                                color: #9e0620 !important;
                                border-color: white !important;
                            }

                            .flatpickr-day:hover {
                                background-color: rgba(255, 255, 255, 0.2) !important;
                            }

                            .flatpickr-day.prevMonthDay,
                            .flatpickr-day.nextMonthDay {
                                color: rgba(255, 255, 255, 0.5) !important;
                                /* Warna hari di bulan sebelum/sesudahnya */
                            }
                        </style>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
