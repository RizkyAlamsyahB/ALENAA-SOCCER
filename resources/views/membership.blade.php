<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Membership - SportVue</title>

    <!-- CSS & Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <style>
        :root {
            --primary-color: #9E0620;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
        }

        .breadcrumb-custom {
            background: linear-gradient(45deg, var(--primary-color), #c51b32);
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

        .form-control {
            border: 1.5px solid #e5e9f2;
            padding: 12px 16px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(158, 6, 32, 0.1);
        }

        .membership-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            background: white;
        }

        .benefit-item {
            padding: 1rem;
            border-radius: 10px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .benefit-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-submit {
            padding: 12px 24px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(158, 6, 32, 0.3);
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .step {
            text-align: center;
            flex: 1;
            position: relative;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e5e9f2;
        }

        .step-number {
            width: 30px;
            height: 30px;
            background: #e5e9f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            position: relative;
            z-index: 1;
        }

        .step.active .step-number {
            background: var(--primary-color);
            color: white;
        }

        .step.completed .step-number {
            background: #28a745;
            color: white;
        }
    </style>
</head>

<body>
    @include('partials.navbar')

    <!-- Breadcrumb -->
    <nav class="breadcrumb-custom">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/membership">Membership</a></li>
                <li class="breadcrumb-item active text-white">Join Gold Membership</li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Progress Steps -->
        <div class="progress-steps mb-5">
            <div class="step completed">
                <div class="step-number">
                    <i class="fas fa-check"></i>
                </div>
                <div class="step-label">Select Plan</div>
            </div>
            <div class="step active">
                <div class="step-number">2</div>
                <div class="step-label">Fill Details</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Payment</div>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <div class="step-label">Confirmation</div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Membership Details -->
            <div class="col-lg-5">
                <div class="membership-card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fas fa-crown text-danger fa-2x"></i>
                            </div>
                            <h3 class="h4">Gold Membership</h3>
                            <div class="d-flex justify-content-center align-items-baseline mb-3">
                                <span class="h2 mb-0 fw-bold">Rp 100,000</span>
                                <span class="text-muted ms-1">/month</span>
                            </div>
                        </div>

                        <h5 class="mb-3">Membership Benefits</h5>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="benefit-item">
                                    <i class="fas fa-percent text-danger mb-2"></i>
                                    <h6 class="mb-1">20% Off</h6>
                                    <small class="text-muted">On all bookings</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="benefit-item">
                                    <i class="fas fa-cookie text-danger mb-2"></i>
                                    <h6 class="mb-1">Free Snacks</h6>
                                    <small class="text-muted">Every visit</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="benefit-item">
                                    <i class="fas fa-calendar-check text-danger mb-2"></i>
                                    <h6 class="mb-1">Priority</h6>
                                    <small class="text-muted">Booking access</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="benefit-item">
                                    <i class="fas fa-clock text-danger mb-2"></i>
                                    <h6 class="mb-1">6 Hours</h6>
                                    <small class="text-muted">Weekly play time</small>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="bg-light rounded-3 p-3 mb-4">
                            <h6 class="mb-3">What's Included:</h6>
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    3 sessions per week
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    2 hours per session
                                </li>
                                <li class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Access to all facilities
                                </li>
                                <li class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Member-only events
                                </li>
                            </ul>
                        </div>

                        <!-- Need Help -->
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-headset text-danger fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Need Help?</h6>
                                    <p class="mb-0 small">Contact our support team at
                                        <a href="tel:+6287840177803" class="text-danger">+62 8784 0177 803</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="col-lg-7">
                <div class="membership-card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="mb-4">Personal Information</h4>
                        <form>
                            <div class="row g-3">
                                <!-- Full Name -->
                                <div class="col-12">
                                    <label class="form-label">Full Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="Enter your full name"
                                            required>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="email" class="form-control" placeholder="Enter your email"
                                            required>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-phone text-muted"></i>
                                        </span>
                                        <input type="tel" class="form-control"
                                            placeholder="Enter your phone number" required>
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="col-12">
                                    <label class="form-label">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-map-marker-alt text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="Enter your address"
                                            required>
                                    </div>
                                </div>

                                <!-- Preferred Schedule -->
                                <div class="col-12">
                                    <label class="form-label">Preferred Playing Schedule</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-calendar-alt text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control datepicker"
                                            placeholder="Select up to 3 days per week" required>
                                    </div>
                                    <small class="text-muted mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        You can choose up to 3 days per week
                                    </small>
                                </div>

                                <!-- Emergency Contact -->
                                <div class="col-12">
                                    <label class="form-label">Emergency Contact</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light">
                                            <i class="fas fa-phone-alt text-muted"></i>
                                        </span>
                                        <input type="tel" class="form-control"
                                            placeholder="Emergency contact number" required>
                                    </div>
                                </div>

                                <!-- Terms -->
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#" class="text-danger">Terms &
                                                Conditions</a> and <a href="#" class="text-danger">Privacy
                                                Policy</a>
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger btn-submit w-100">
                                        Proceed to Payment
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Schedule Selection Section -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <h5 class="mb-4">Select Your Schedule</h5>

                    <!-- Date Navigation -->
                    <div class="date-navigation d-flex align-items-center justify-content-between mb-4">
                        <button class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-chevron-left me-2"></i>Previous
                        </button>
                        <h6 class="mb-0">January 2024</h6>
                        <button class="btn btn-outline-secondary rounded-pill px-4">
                            Next<i class="fas fa-chevron-right ms-2"></i>
                        </button>
                    </div>

                    <!-- Week Days -->
                    <div class="schedule-grid mb-4">
                        <div class="row g-3">
                            <!-- Monday -->
                            <div class="col">
                                <div class="date-card text-center p-2 rounded-3">
                                    <small class="text-muted d-block">Mon</small>
                                    <span class="fw-bold">15</span>
                                </div>
                            </div>
                            <!-- Tuesday -->
                            <div class="col">
                                <div class="date-card text-center p-2 rounded-3 active">
                                    <small class="text-muted d-block">Tue</small>
                                    <span class="fw-bold">16</span>
                                </div>
                            </div>
                            <!-- Wednesday -->
                            <div class="col">
                                <div class="date-card text-center p-2 rounded-3">
                                    <small class="text-muted d-block">Wed</small>
                                    <span class="fw-bold">17</span>
                                </div>
                            </div>
                            <!-- Thursday -->
                            <div class="col">
                                <div class="date-card text-center p-2 rounded-3">
                                    <small class="text-muted d-block">Thu</small>
                                    <span class="fw-bold">18</span>
                                </div>
                            </div>
                            <!-- Friday -->
                            <div class="col">
                                <div class="date-card text-center p-2 rounded-3">
                                    <small class="text-muted d-block">Fri</small>
                                    <span class="fw-bold">19</span>
                                </div>
                            </div>
                            <!-- Saturday -->
                            <div class="col">
                                <div class="date-card text-center p-2 rounded-3">
                                    <small class="text-muted d-block">Sat</small>
                                    <span class="fw-bold">20</span>
                                </div>
                            </div>
                            <!-- Sunday -->
                            <div class="col">
                                <div class="date-card text-center p-2 rounded-3 disabled">
                                    <small class="text-muted d-block">Sun</small>
                                    <span class="fw-bold">21</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Time Slots -->
                    <div class="time-slots mb-4">
                        <h6 class="mb-3">Available Time Slots</h6>
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <div class="time-slot-card p-3 rounded-3 text-center">
                                    <i class="far fa-clock mb-2"></i>
                                    <span class="d-block">08:00 - 10:00</span>
                                    <small class="text-success">Available</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="time-slot-card p-3 rounded-3 text-center active">
                                    <i class="far fa-clock mb-2"></i>
                                    <span class="d-block">10:00 - 12:00</span>
                                    <small class="text-success">Available</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="time-slot-card p-3 rounded-3 text-center disabled">
                                    <i class="far fa-clock mb-2"></i>
                                    <span class="d-block">13:00 - 15:00</span>
                                    <small class="text-danger">Booked</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="time-slot-card p-3 rounded-3 text-center">
                                    <i class="far fa-clock mb-2"></i>
                                    <span class="d-block">15:00 - 17:00</span>
                                    <small class="text-success">Available</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Schedule Summary -->
                    <div class="selected-schedule bg-light p-3 rounded-3 mb-4">
                        <h6 class="mb-3">Selected Schedule</h6>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="mb-1">Tuesday, 16 January 2024</p>
                                <p class="mb-0 text-muted">10:00 - 12:00 (2 hours)</p>
                            </div>
                            <button class="btn btn-outline-danger btn-sm rounded-pill">
                                Change
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </button>
                        <button class="btn btn-danger rounded-pill px-4">
                            Continue to Payment<i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <style>
                .date-card {
                    background: #f8f9fa;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .date-card:hover:not(.disabled) {
                    background: #e9ecef;
                }

                .date-card.active {
                    background: var(--primary-color);
                    color: white;
                }

                .date-card.active small {
                    color: rgba(255, 255, 255, 0.8) !important;
                }

                .date-card.disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }

                .time-slot-card {
                    background: #f8f9fa;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .time-slot-card:hover:not(.disabled) {
                    background: #e9ecef;
                }

                .time-slot-card.active {
                    background: var(--primary-color);
                    color: white;
                }

                .time-slot-card.active small {
                    color: rgba(255, 255, 255, 0.8) !important;
                }

                .time-slot-card.disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }

                .btn {
                    transition: all 0.3s ease;
                }

                .btn:hover {
                    transform: translateY(-2px);
                }
            </style>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
            daysOfWeekDisabled: [0], // Disable Sundays
            multidate: 3, // Allow selection of 3 dates
            multidateSeparator: ', ',
            clearBtn: true,
            templates: {
                leftArrow: '<i class="fas fa-chevron-left"></i>',
                rightArrow: '<i class="fas fa-chevron-right"></i>'
            }
        });

        // Validate multidate selection
        $('.datepicker').on('changeDate', function(e) {
            if (e.dates.length > 3) {
                alert('You can only select up to 3 days');
                $(this).datepicker('clearDates');
            }
        });

        // Form validation
        (function() {
            'use strict'

            // Fetch all forms that need validation
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Smooth scroll for progress steps
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Update progress steps
        function updateProgressSteps(currentStep) {
            document.querySelectorAll('.step').forEach((step, index) => {
                if (index < currentStep) {
                    step.classList.add('completed');
                    step.classList.remove('active');
                } else if (index === currentStep) {
                    step.classList.add('active');
                    step.classList.remove('completed');
                } else {
                    step.classList.remove('completed', 'active');
                }
            });
        }

        // Example: Update progress steps when form sections are completed
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            updateProgressSteps(2); // Move to payment step
        });
    </script>
</body>

</html>
