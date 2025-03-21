<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SportVue</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Animation -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Link untuk font dan stylesheet tambahan -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
  :root {
    --primary-color: #9e0620;
    --danger-color: #9e0620;
}

.btn-primary {
    background-color: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.btn-primary:hover,
.btn-primary:focus {
    background-color: #8a051c !important;
    border-color: #8a051c !important;
}

.btn-danger {
    background-color: var(--danger-color) !important;
    border-color: var(--danger-color) !important;
}

.btn-danger:hover,
.btn-danger:focus {
    background-color: #8a051c !important;
    border-color: #8a051c !important;
}


        .promo-banner {
            position: relative;
            height: 50px;
            width: 100%;

        }

        html,
        body {
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
        }

        .promo-slider {
            position: absolute;
            width: 100%;
        }

        .promo-slider .d-flex {
            display: flex !important;
            animation: slideLeft 120s linear infinite;
            width: max-content;
        }

        .promo-slide {
            min-width: 100vw;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            padding: 0 20px;
        }

        @keyframes slideLeft {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-300%);
                /* 3 slides */
            }
        }

        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .btn-danger {
            background-color: #9E0620;
            border: none;
            border-radius: 4px;
            /* Sudut yang lebih tajam */
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-danger:hover {
            background-color: #8a051c;
            transform: translateY(-2px);
        }

        .bg-danger {
            background-color: #8a051c;
        }

        .lead {
            font-size: 1.1rem;
            opacity: 0.9;
        }


        .btn-outline-light {
            width: 36px;
            height: 36px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            transform: translateY(-3px);
        }


        .hover-text-white {
            transition: color 0.3s ease;
        }

        .hover-text-white:hover {
            color: white !important;
        }

    </style>

</head>


<body>


    @include('layouts.navigation')
    @yield('content')

    <link rel="stylesheet" href="{{ asset('css/users/welcome.css') }}">

    <footer class="footer bg-white py-5">
        <!-- Main Footer -->
        <div class="footer-main">
            <div class="container">
                <div class="row g-4">
                    <!-- Brand & Description -->
                    <div class="col-lg-4 mb-lg-0 mb-4">
                        <div class="pe-lg-4">
                            <!-- Logo -->
                            <div class="footer-brand mb-4">
                                <span class="brand-text">
                                    ALENA <span class="text-dark"> SOCCER</span>

                                </span>
                            </div>
                            <!-- Description -->
                            <p class="text-muted mb-4">
                                Platform olahraga terpercaya untuk sewa lapangan dan komunitas sport di Indonesia.
                                Bergabunglah dengan ribuan member aktif kami!
                            </p>
                            <!-- Social Links -->
                            <div class="social-links">
                                <a href="#" class="social-link" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-link" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-link" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link" title="YouTube">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-4">
                        <h5 class="footer-title">Komunitas</h5>
                        <ul class="footer-links">
                            <li><a href="/events">Event Mendatang</a></li>
                            <li><a href="/teams">Cari Tim</a></li>
                            <li><a href="/tournaments">Turnamen</a></li>
                            <li><a href="/forum">Forum</a></li>
                            <li><a href="/support">Bantuan</a></li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div class="col-lg-3 col-md-4">
                        <h5 class="footer-title">Hubungi Kami</h5>
                        <ul class="footer-contact">
                            <li class="contact-item">
                                <i class="fas fa-headset"></i>
                                <div>
                                    <span class="label">Customer Service</span>
                                    <a href="tel:+628784017803">+62 878 4017 7803</a>
                                </div>
                            </li>
                            <li class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <span class="label">Email</span>
                                    <a href="mailto:info@alenasoccer.com">info@alenasoccer.com</a>
                                </div>
                            </li>
                            <li class="contact-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <span class="label">Member Aktif</span>
                                    <span class="value text-dark">120+ Member</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Latest Events -->
                    <div class="col-lg-3 col-md-4">
                        <h5 class="footer-title">Event Terbaru</h5>
                        <div class="events-wrapper">
                            <div class="event-card">
                                <div class="event-content">
                                    <div class="event-date">15-20 MAR 2024</div>
                                    <h6 class="event-title text-dark">Open Tournament</h6>
                                    <p class="event-desc">Turnamen terbesar tahun ini!</p>
                                </div>
                            </div>
                            <div class="event-card">
                                <div class="event-content">
                                    <div class="event-date">01 APR 2024</div>
                                    <h6 class="event-title text-dark">Team Matching Night</h6>
                                    <p class="event-desc">Temukan tim impianmu!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom mt-5 pt-4 border-top">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="copyright mb-md-0 text-muted">© 2024 Alena Soccer. All rights reserved.</p>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end align-items-center">
                            <span class="partners-label me-3 text-muted">Partner Kami:</span>
                            <div class="partners-icons">
                                <i class="fas fa-futbol"></i>
                                <i class="fas fa-trophy"></i>
                                <i class="fas fa-medal"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>

</html>
