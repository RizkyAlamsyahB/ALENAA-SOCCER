@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/users/welcome.css') }}">
    <!-- Promo Banner -->
    <div class="promo-banner text-white" style="background-color: #9E0620; margin-top:60px;">
        <div class="promo-slider">
            <div class="d-flex promo-slide-container mt-3">
                <div class="promo-slide">
                    <i class="fas fa-gift me-2"></i>
                    Member Baru Diskon 20%! Gunakan kode: ALENAFIRST
                </div>
                <div class="promo-slide">
                    <i class="fas fa-trophy me-2"></i>
                    Special Weekend! Booking Sekarang Hemat 15%
                </div>
                <div class="promo-slide">
                    <i class="fas fa-bolt me-2"></i>
                    Flash Deal: Booking 3 Jam Gratis 1 Jam Extra!
                </div>
                <div class="promo-slide">
                    <i class="fas fa-gift me-2"></i>
                    Member Baru Diskon 20%! Gunakan kode: ALENAFIRST
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero position-relative vh-100 d-flex align-items-center">
        <!-- Hero Background -->
        <div class="hero-bg position-absolute w-100 h-100">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/225f207f34ecb422ea74c38dd5016adc852e34aafdae3247df704fa28c8f307d"
                class="w-100 h-100 object-fit-cover lazyload" alt="Sport Facility">
            <div class="overlay"></div>
        </div>
        <!-- Hero Content -->
        <div class="container position-relative z-3">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Brand Badge -->


                    <!-- Main Text -->
                    <h1 class="display-2 fw-bold mb-2 text-shadow fade-in-up delay-1" style="color: #9e0620;">
                        ALENA SPORT CENTER
                    </h1>
                    <h2 class="h4 text-white fw-normal mb-4 fade-in-up delay-2">
                        Main Bareng dan Sewa Lapangan
                    </h2>

                    <p class="lead text-white mb-4 fade-in-up delay-3">
                        Platform sport center untuk sewa lapangan, sparring partner, dan komunitas olahraga.
                        <span class="text-danger fw-bold">Olahraga Jadi Lebih Seru!</span>
                    </p>

                    <!-- CTA Buttons -->
                    <div class="button-group fade-in-up delay-4">
                        <a href="#booking" class="btn btn-danger me-3 mb-3 mb-sm-0">
                            Booking Sekarang
                            <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        <a href="/mabar" class="btn btn-light me-3 mb-3 mb-sm-0">
                            Gabung Main Bareng
                            <i class="fas fa-users ms-2"></i>
                        </a>
                    </div>

                    <!-- Stats Counter -->
                    <div class="stats-wrapper mt-5 fade-in-up delay-5">
                        <div class="stats-container">
                            <div class="stat-item">
                                <h3 class="text-white mb-0">3</h3>
                                <p class="text-white-50 mb-0">Lapangan</p>
                            </div>
                            <div class="stat-divider"></div>
                            <div class="stat-item">
                                <h3 class="text-white mb-0">20+</h3>
                                <p class="text-white-50 mb-0">Member Aktif</p>
                            </div>
                            <div class="stat-divider"></div>
                            <div class="stat-item">
                                <h3 class="text-white mb-0">100+</h3>
                                <p class="text-white-50 mb-0">Event</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const heroSection = document.querySelector('.hero');
                const heroBackground = heroSection.querySelector('.hero-bg img');

                // Parallax effect
                window.addEventListener('scroll', function() {
                    const scrollPosition = window.pageYOffset;
                    heroBackground.style.transform = `translateY(${scrollPosition * 0.5}px)`;
                });
            });
        </script>
    @endpush
    <style>
        .promo-slider .d-flex {
            display: flex !important;
            animation: slideLeft 240s linear infinite;
            width: max-content;
        }
    </style>

    <!-- Easy Booking Section -->
    <section class="booking-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="booking-content pe-lg-4">
                        <span class="badge bg-danger mb-3">Booking Online</span>
                        <h2 class="section-title mb-4">Booking Lapangan Semudah Tendangan Penalti</h2>
                        <p class="section-desc mb-4">
                            Lupakan cara lama booking lapangan yang ribet. Dengan Alena Soccer, kamu bisa booking lapangan
                            favoritmu kapan saja, di mana saja. Cukup pilih jadwal, konfirmasi booking, dan siap bermain!
                        </p>
                        <div class="features-list">
                            <div class="feature-item mb-3">
                                <i class="fas fa-check-circle text-danger me-2"></i>
                                Booking online 24/7
                            </div>
                            <div class="feature-item mb-3">
                                <i class="fas fa-check-circle text-danger me-2"></i>
                                Konfirmasi instan
                            </div>
                            <div class="feature-item mb-3">
                                <i class="fas fa-check-circle text-danger me-2"></i>
                                Jaminan jadwal pasti
                            </div>
                        </div>
                        <a href="/booking" class="btn btn-danger mt-4">
                            Booking Sekarang <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="booking-image">
                        <img src="assets/futsal-field.jpg" alt="Booking Lapangan" class="img-fluid rounded-3 shadow"
                            loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Community Section -->
    <section class="community-section py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2">
                    <div class="community-content ps-lg-4">
                        <span class="badge bg-danger mb-3">Komunitas</span>
                        <h2 class="section-title mb-4">Temukan Tim Impianmu di Alena Soccer</h2>
                        <p class="section-desc mb-4">
                            Bosan main sendiri? Bergabunglah dengan komunitas Alena Soccer. Temukan partner mabar, ikuti
                            turnamen seru, dan tingkatkan skill sepakbolamu bersama pemain lainnya.
                        </p>
                        <div class="community-stats row g-3 mb-4">
                            <div class="col-4">
                                <div class="stat-card text-center p-3 bg-white rounded-3 shadow-sm">
                                    <h3 class="mb-2 text-danger">200+</h3>
                                    <p class="mb-0">Pemain Aktif</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-card text-center p-3 bg-white rounded-3 shadow-sm">
                                    <h3 class="mb-2 text-danger">20+</h3>
                                    <p class="mb-0">Tim Terdaftar</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-card text-center p-3 bg-white rounded-3 shadow-sm">
                                    <h3 class="mb-2 text-danger">3+</h3>
                                    <p class="mb-0">Event/Bulan</p>
                                </div>
                            </div>
                        </div>
                        <a href="/community" class="btn btn-danger">
                            Gabung Komunitas <i class="fas fa-users ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <div class="community-image">
                        <img src="assets/komunitas.jpg" alt="Komunitas Sepakbola" class="img-fluid rounded-3 shadow"
                            loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge bg-danger mb-3">Keunggulan</span>
                <h2 class="section-title">Kenapa Harus Alena Soccer?</h2>
                <p class="section-desc mx-auto" style="max-width: 600px;">
                    Platform olahraga terpercaya dengan fasilitas lengkap dan komunitas aktif untuk pengalaman bermain
                    sepakbola terbaikmu
                </p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card text-center p-4 bg-white rounded-3 shadow-sm h-100">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-futbol fa-2x text-danger"></i>
                        </div>
                        <h4 class="feature-title mb-3">Lapangan Berkualitas</h4>
                        <p class="feature-desc mb-0">
                            Lapangan standar internasional dengan rumput sintetis terbaik dan perawatan rutin
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center p-4 bg-white rounded-3 shadow-sm h-100">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-users fa-2x text-danger"></i>
                        </div>
                        <h4 class="feature-title mb-3">Komunitas Aktif</h4>
                        <p class="feature-desc mb-0">
                            Bergabung dengan ratusan pemain aktif dan ikuti berbagai event seru setiap bulannya
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center p-4 bg-white rounded-3 shadow-sm h-100">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-trophy fa-2x text-danger"></i>
                        </div>
                        <h4 class="feature-title mb-3">Turnamen Reguler</h4>
                        <p class="feature-desc mb-0">
                            Ikuti turnamen rutin dan tingkatkan kemampuanmu bersama tim terbaik
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Testimonials Section -->
<section class="testimonials-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-danger mb-3">Ulasan Member</span>
            <h2 class="section-title">Apa Kata Mereka?</h2>
        </div>
        <div class="row g-4">
            @forelse($testimonials as $testimonial)
                <div class="col-md-4">
                    <div class="testimonial-card p-4 bg-white rounded-3 shadow-sm h-100">
                        <div class="testimonial-header d-flex justify-content-between align-items-center mb-3">
                            <div class="rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                            <span class="text-muted small">
                                {{ $testimonial->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <div class="testimonial-content mb-3">
                            <i class="fas fa-quote-left text-danger mb-3"></i>
                            <p class="mb-0">
                                "{{ $testimonial->comment }}"
                            </p>
                        </div>
                        <div class="testimonial-author d-flex align-items-center">
                            <div class="author-avatar me-3">
                                @if($testimonial->user->profile_photo_path)
                                    <img src="{{ Storage::url($testimonial->user->profile_photo_path) }}"
                                        alt="{{ $testimonial->user->name }}" class="rounded-circle" width="50" height="50">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                        {{ strtoupper(substr($testimonial->user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $testimonial->user->name }}</h6>
                                <small class="text-muted">
                                    Tentang :
                                    @if($testimonial->item_type == 'App\\Models\\Field')
                                         {{ $testimonial->reviewable->name ?? '' }}
                                    @elseif($testimonial->item_type == 'App\\Models\\RentalItem')
                                        {{ $testimonial->reviewable->name ?? 'Penyewaan Peralatan' }}
                                    @elseif($testimonial->item_type == 'App\\Models\\Photographer')
                                         {{ $testimonial->reviewable->name ?? '' }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Tampilkan testimonial default jika belum ada ulasan -->
                <div class="col-md-4">
                    <div class="testimonial-card p-4 bg-white rounded-3 shadow-sm h-100">
                        <div class="testimonial-content mb-3">
                            <i class="fas fa-quote-left text-danger mb-3"></i>
                            <p class="mb-0">
                                "Booking lapangan jadi super gampang, gak perlu ribet telepon atau datang langsung.
                                Lapangannya juga berkualitas!"
                            </p>
                        </div>
                        <div class="testimonial-author d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                style="width: 50px; height: 50px;">A</div>
                            <div>
                                <h6 class="mb-0">Ahmad Fadillah</h6>
                                <small class="text-muted">Member Aktif</small>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Tambahkan testimonial default lain seperti sebelumnya -->
            @endforelse
        </div>
    </div>
</section>

    <style>
        /* New Sections Styling */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2a2a2a;
            margin-bottom: 1rem;
        }

        .section-desc {
            font-size: 1.1rem;
            color: #6c757d;
            line-height: 1.6;
        }

        /* Booking Section */
        .booking-section .feature-item {
            font-size: 1.1rem;
            color: #2a2a2a;
        }

        /* Community Section */
        .community-stats h3 {
            font-size: 2rem;
            font-weight: 700;
        }

        .community-stats p {
            font-size: 0.9rem;
            color: #6c757d;
        }

        /* Features Section */
        .feature-card {
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2a2a2a;
        }

        .feature-desc {
            color: #6c757d;
        }

        /* Testimonials Section */
        .testimonial-card {
            transition: transform 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
        }

        .testimonial-content p {
            font-size: 1.1rem;
            color: #2a2a2a;
            font-style: italic;
        }

        .testimonial-author h6 {
            color: #2a2a2a;
            font-weight: 600;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .section-title {
                font-size: 2rem;
            }

            .section-desc {
                font-size: 1rem;
            }

            .booking-section .feature-item {
                font-size: 1rem;
            }

            .community-stats h3 {
                font-size: 1.5rem;
            }
        }
    </style>


    <!-- Modern Footer -->
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

    <!-- WhatsApp Button -->
    <a href="https://wa.me/628784017803" class="whatsapp-button" title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
