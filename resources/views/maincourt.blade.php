    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Field A - SportVue</title>

        <!-- CSS & Font imports -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <style>
            :root {
                --primary-color: #9E0620;
                --secondary-color: #2A2A2A;
                --danger-color: #9E0620;
                /* Menambahkan variabel danger color */
            }

            body {
                font-family: 'Inter', sans-serif;
                background: #f8f9fa;
            }

            .breadcrumb-custom {
                background: linear-gradient(45deg, var(--primary-color), #9E0620);
                padding: 1rem 0;

            }

            .breadcrumb-item a {
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
                transition: color 0.3s ease;
            }

            .breadcrumb-item a:hover {
                color: white;
            }

            .breadcrumb-item.active {
                color: white;
            }

            .gallery-img {
                border-radius: 15px;
                overflow: hidden;
                transition: transform 0.3s ease;
            }

            .gallery-img:hover {
                transform: scale(1.02);
            }

            .facility-item {
                background: #f8f9fa;
                padding: 1rem;
                border-radius: 10px;
                transition: all 0.3s ease;
            }

            .facility-item:hover {
                background: #fff;
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            .time-slot {
                transition: all 0.3s ease;
            }

            .time-slot:hover {
                background-color: #f8f9fa;
            }

            .price-card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            .contact-item {
                transition: all 0.3s ease;
            }

            .contact-item:hover {
                transform: translateX(5px);
            }

            .related-venue-card {
                border: none;
                border-radius: 15px;
                transition: all 0.3s ease;
                overflow: hidden;
            }

            .related-venue-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }

            .btn-book {
                padding: 12px 24px;
                border-radius: 10px;
                transition: all 0.3s ease;
            }

            .btn-book:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(158, 6, 32, 0.3);
            }

            /* Override Bootstrap danger color */
            .btn-danger {
                background-color: var(--danger-color) !important;
                border-color: var(--danger-color) !important;
            }
        </style>
    </head>

    <body>
        @include('partials.navbar')

        <!-- Breadcrumb -->
        <nav class="breadcrumb-custom" style="margin-top: 56px;">
            <div class="container">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/venues">Venues</a></li>
                    <li class="breadcrumb-item active">Field A</li>
                </ol>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container py-5">
            <!-- Gallery Section -->
            <div class="row g-3 mb-5">
                <div class="col-lg-8">
                    <div class="gallery-img position-relative">
                        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                            class="img-fluid w-100" style="height: 450px; object-fit: cover;" alt="Main court">
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-success px-3 py-2 rounded-pill">
                                <i class="fas fa-check-circle me-1"></i>Available Now
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="gallery-img">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                    class="img-fluid w-100" style="height: 215px; object-fit: cover;"
                                    alt="Court view 2">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="gallery-img">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                    class="img-fluid w-100" style="height: 215px; object-fit: cover;"
                                    alt="Court view 3">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Information -->
            <div class="row">
                <!-- Field Details -->
                <div class="col-lg-8">
                    <!-- Basic Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h1 class="h3 mb-2">Maincourt - Field A</h1>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                            <span>Jakarta, Indonesia</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-star text-warning me-2"></i>
                                            <span>4.8 (128 reviews)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="h4 text-danger mb-1">Rp 50.000</div>
                                    <small class="text-muted">/hour</small>
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-3">
                                    <div class="facility-item">
                                        <i class="fas fa-ruler text-danger mb-2"></i>
                                        <h6 class="mb-1">Size</h6>
                                        <small class="text-muted">25 x 15m</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="facility-item">
                                        <i class="fas fa-users text-danger mb-2"></i>
                                        <h6 class="mb-1">Capacity</h6>
                                        <small class="text-muted">5v5 Players</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="facility-item">
                                        <i class="fas fa-clock text-danger mb-2"></i>
                                        <h6 class="mb-1">Duration</h6>
                                        <small class="text-muted">1 Hour/Session</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="facility-item">
                                        <i class="fas fa-volleyball-ball text-danger mb-2"></i>
                                        <h6 class="mb-1">Type</h6>
                                        <small class="text-muted">Indoor Field</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Facilities -->
                            <h5 class="mb-3">Facilities</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-parking text-danger me-2"></i>
                                        <span>Free Parking</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-wifi text-danger me-2"></i>
                                        <span>Free WiFi</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-shower text-danger me-2"></i>
                                        <span>Shower Room</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-tshirt text-danger me-2"></i>
                                        <span>Changing Room</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-store text-danger me-2"></i>
                                        <span>Mini Store</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-first-aid text-danger me-2"></i>
                                        <span>First Aid</span>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-calendar mb-4">
                                <h5 class="mb-4">Pilih Jadwal Booking</h5>

                                <!-- Calendar Navigation -->
                                <div
                                    class="calendar-navigation d-flex justify-content-between align-items-center mb-3">
                                    <button class="btn btn-link text-dark text-decoration-none btn-prev">
                                        <i class="fas fa-chevron-left me-1"></i>Previous
                                    </button>
                                    <h6 class="mb-0">January 2024</h6>
                                    <button class="btn btn-link text-dark text-decoration-none btn-next">
                                        Next<i class="fas fa-chevron-right ms-1"></i>
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
                                    <h6 class="mb-3">Available Time Slots</h6>
                                    <div class="time-slots-grid">
                                        <div class="time-slot" data-time="08:00 - 10:00">
                                            <i class="far fa-clock"></i>
                                            <span>08:00 - 10:00</span>
                                            <small class="text-success">Available</small>
                                        </div>
                                        <div class="time-slot active" data-time="10:00 - 12:00">
                                            <i class="far fa-clock"></i>
                                            <span>10:00 - 12:00</span>
                                            <small class="text-success">Available</small>
                                        </div>
                                        <div class="time-slot disabled" data-time="13:00 - 15:00">
                                            <i class="far fa-clock"></i>
                                            <span>13:00 - 15:00</span>
                                            <small class="text-danger">Booked</small>
                                        </div>
                                        <div class="time-slot" data-time="15:00 - 17:00">
                                            <i class="far fa-clock"></i>
                                            <span>15:00 - 17:00</span>
                                            <small class="text-success">Available</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden Input -->
                                <input type="date" id="selectedDate" class="d-none">
                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-outline-secondary rounded-3 px-4">
                                        <i class="fas fa-arrow-left me-2"></i>Back
                                    </button>
                                    <button class="btn btn-danger rounded-3 px-4">
                                        Continue to Payment<i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>

                            <style>
                                .calendar-grid {
                                    background: white;
                                    border-radius: 10px;
                                    overflow: hidden;
                                }

                                .calendar-header {
                                    display: grid;
                                    grid-template-columns: repeat(7, 1fr);
                                    text-align: center;
                                    padding: 10px 0;
                                    background: #f8f9fa;
                                    font-size: 0.9rem;
                                    color: #6c757d;
                                }

                                .calendar-dates {
                                    display: grid;
                                    grid-template-columns: repeat(7, 1fr);
                                    gap: 5px;
                                    padding: 10px;
                                }

                                .calendar-date {
                                    aspect-ratio: 1;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    cursor: pointer;
                                    border-radius: 8px;
                                    transition: all 0.2s;
                                    font-weight: 500;
                                }

                                .calendar-date:hover:not(.disabled) {
                                    background: rgba(158, 6, 32, 0.1);
                                }

                                .calendar-date.active {
                                    background: var(--primary-color);
                                    color: white;
                                }

                                .calendar-date.disabled {
                                    opacity: 0.5;
                                    cursor: not-allowed;
                                }

                                .time-slots-grid {
                                    display: grid;
                                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                                    gap: 15px;
                                }

                                .time-slot {
                                    background: #f8f9fa;
                                    padding: 15px;
                                    border-radius: 10px;
                                    text-align: center;
                                    cursor: pointer;
                                    transition: all 0.2s;
                                }

                                .time-slot:hover:not(.disabled) {
                                    background: rgba(158, 6, 32, 0.1);
                                }

                                .time-slot.active {
                                    background: var(--primary-color);
                                    color: white;
                                }

                                .time-slot.active small {
                                    color: rgba(255, 255, 255, 0.8) !important;
                                }

                                .time-slot.disabled {
                                    opacity: 0.7;
                                    cursor: not-allowed;
                                }

                                .time-slot i {
                                    display: block;
                                    margin-bottom: 5px;
                                }

                                .time-slot span {
                                    display: block;
                                    margin-bottom: 5px;
                                    font-weight: 500;
                                }
                            </style>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const calendarDates = document.querySelector('.calendar-dates');
                                    const selectedDateInput = document.getElementById('selectedDate');
                                    const monthDisplay = document.querySelector('.calendar-navigation h6');
                                    let currentDate = new Date();

                                    // Format bulan dan tahun
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

                                        // Update display bulan dan tahun
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
                                            });
                                        });
                                    }

                                    // Navigate calendar
                                    document.querySelector('.btn-prev').addEventListener('click', function() {
                                        currentDate.setMonth(currentDate.getMonth() - 1);
                                        generateCalendar(currentDate);
                                    });

                                    document.querySelector('.btn-next').addEventListener('click', function() {
                                        currentDate.setMonth(currentDate.getMonth() + 1);
                                        generateCalendar(currentDate);
                                    });

                                    // Initialize calendar
                                    generateCalendar(currentDate);
                                });
                            </script>


                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
<!-- Membership Packages -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="mb-4">Membership Packages</h5>

        <!-- Bronze Package -->
        <div class="membership-package mb-3 p-3 border rounded-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="mb-1 text-warning">Bronze Member</h6>
                    <p class="mb-2 text-muted small">Perfect for casual players</p>
                </div>
                <span class="badge bg-warning">Save 10%</span>
            </div>
            <ul class="list-unstyled mb-3 small">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>10 hours of playtime</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Valid for 1 month</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Basic equipment rental</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="h5 mb-0">Rp 450.000</span>
                    <small class="text-muted">/month</small>
                </div>
                <button class="btn btn-outline-warning btn-sm px-4">Select</button>
            </div>
        </div>

        <!-- Silver Package -->
        <div class="membership-package mb-3 p-3 border rounded-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="mb-1 text-secondary">Silver Member</h6>
                    <p class="mb-2 text-muted small">Great for regular players</p>
                </div>
                <span class="badge bg-secondary">Save 20%</span>
            </div>
            <ul class="list-unstyled mb-3 small">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>20 hours of playtime</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Valid for 2 months</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Premium equipment rental</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>1 free drink per visit</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="h5 mb-0">Rp 850.000</span>
                    <small class="text-muted">/2 months</small>
                </div>
                <button class="btn btn-outline-secondary btn-sm px-4">Select</button>
            </div>
        </div>

        <!-- Gold Package -->
        <div class="membership-package p-3 border rounded-3 bg-light">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="mb-1 text-danger">Gold Member</h6>
                    <p class="mb-2 text-muted small">Best value for enthusiasts</p>
                </div>
                <span class="badge bg-danger">Save 30%</span>
            </div>
            <ul class="list-unstyled mb-3 small">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>40 hours of playtime</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Valid for 3 months</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Premium equipment rental</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>2 free drinks per visit</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority booking</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Free locker access</li>
            </ul>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="h5 mb-0">Rp 1.200.000</span>
                    <small class="text-muted">/3 months</small>
                </div>
                <button class="btn btn-danger btn-sm px-4">Select</button>
            </div>
        </div>
    </div>
</div>

<style>
.membership-package {
    transition: all 0.3s ease;
}

.membership-package:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>
                </div>
            </div>

        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
