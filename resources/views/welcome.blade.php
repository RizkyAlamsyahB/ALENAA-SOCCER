@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{ asset('css/users/welcome.css') }}">
<!-- Promo Banner -->
<div class="promo-banner text-white" style="background-color: #9E0620; margin-top:60px;">
    <div class="promo-slider">
        <div class="d-flex promo-slide-container mt-3">
            <div class="promo-slide">
                <i class="fas fa-gift me-2"></i>
                New Member Discount 20% Off! Use code: SPORTVUE20
            </div>
            <div class="promo-slide">
                <i class="fas fa-trophy me-2"></i>
                Special Weekend Price! Book Now and Save 15%
            </div>
            <div class="promo-slide">
                <i class="fas fa-bolt me-2"></i>
                Flash Deal: Book 3 Hours Get 1 Hour Free!
            </div>
            <!-- Duplicate first slide for smooth transition -->
            <div class="promo-slide">
                <i class="fas fa-gift me-2"></i>
                New Member Discount 20% Off! Use code: SPORTVUE20
            </div>
        </div>
    </div>
</div>
<style>
    :root {
        --primary-color: #9E0620;
        --secondary-color: #2A2A2A;
        --danger-color: #9E0620;
    }


    .promo-slider .d-flex {
        display: flex !important;
        animation: slideLeft 240s linear infinite;
        width: max-content;
    }

</style>
    {{-- Hero Section --}}
    <section class="hero position-relative vh-100 d-flex align-items-center">
        {{-- Hero Background --}}
        <div class="hero-bg position-absolute w-100 h-100">
            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/225f207f34ecb422ea74c38dd5016adc852e34aafdae3247df704fa28c8f307d"
                class="w-100 h-100 object-fit-cover" alt="Indoor Sport Field">
            <div class="overlay position-absolute top-0 start-0 w-100 h-100"></div>
        </div>

        {{-- Hero Content --}}
        <div class="container position-relative z-3 text-left">
            <div class="col-lg-12 mx-auto">
                {{-- Main Text --}}
                <h1 class="display-1 fw-bold text-white text-shadow">
                    ALENA SOCCER
                </h1>
                <h1 class="display-1 fw-bold text-white mb-3 text-shadow">
                    SUPER SPORT FUTSAL
                </h1>

                <p class="lead text-white mb-4 text-shadow">
                    Platform all-in-one untuk sewa lapangan, cari lawan sparring, atau cari kawan main bareng.
                    Olahraga makin mudah dan menyenangkan!
                </p>

                {{-- CTA Button --}}
                <a href="#booking" class="btn btn-danger btn-lg px-4 animate-btn">
                    Cek Ketersediaan
                </a>
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

    <!-- resources/views/components/booking-section.blade.php -->
    <section class="booking-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">Pesan Lapangan Anda</h2>
                <p class="section-subtitle">Pilih lapangan yang Anda inginkan dan cek ketersediaan secara real-time</p>
            </div>

            <div class="row overflow-auto pb-3">
                @php
                    $courts = [
                        [
                            'id' => 'A',
                            'name' => 'Lapangan A',
                            'type' => 'Indoor',
                            'capacity' => '5v5',
                            'price' => 150000,
                            'status' => 'available',
                            'icon' => 'warehouse',
                        ],
                        [
                            'id' => 'B',
                            'name' => 'Lapangan B',
                            'type' => 'Outdoor',
                            'capacity' => '7v7',
                            'price' => 200000,
                            'status' => 'limited',
                            'icon' => 'sun',
                        ],
                        [
                            'id' => 'C',
                            'name' => 'Lapangan C',
                            'type' => 'Premium',
                            'capacity' => '11v11',
                            'price' => 300000,
                            'status' => 'booked',
                            'icon' => 'star',
                        ],
                    ];
                @endphp

                @foreach ($courts as $court)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0 @if ($court['status'] == 'booked') opacity-75 @endif">
                            <div class="position-relative">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/044664ba4bdf6e751b907ef4f4555d90041b6947df1b73075a20a385d181c41e"
                                    class="card-img-top" alt="{{ $court['name'] }}">

                                @switch($court['status'])
                                    @case('available')
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                            <i class="fas fa-circle me-1"></i> Tersedia Sekarang
                                        </span>
                                    @break

                                    @case('limited')
                                        <span class="badge bg-warning position-absolute top-0 end-0 m-2">
                                            <i class="fas fa-clock me-1"></i> Slot Terbatas
                                        </span>
                                    @break

                                    @case('booked')
                                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">
                                            <i class="fas fa-ban me-1"></i> Penuh Terisi
                                        </span>
                                    @break
                                @endswitch
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">{{ $court['name'] }}</h5>
                                <div class="d-flex justify-content-between mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-{{ $court['icon'] }} me-1"></i>
                                        Lapangan {{ $court['type'] }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $court['capacity'] }}
                                    </small>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="h5 text-danger mb-0">Rp
                                            {{ number_format($court['price'], 0, ',', '.') }}</span>
                                        <small class="text-muted">/jam</small>
                                    </div>

                                    @if ($court['status'] != 'booked')
                                        <a href="/maincourt" class="btn btn-danger rounded-pill">
                                            Pesan Sekarang <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-secondary rounded-pill" disabled>
                                            Tidak Tersedia <i class="fas fa-ban ms-1"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    {{-- End Booking Section --}}

    <!-- resources/views/components/equipment-rental.blade.php -->
    <section class="equipment-section py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">Equipment Rental</h2>
                <p class="section-subtitle">Quality sports equipment for your game</p>
            </div>

            <div class="row overflow-auto pb-3">
                @php
                    $equipments = [
                        [
                            'id' => 'jersey',
                            'name' => 'Jersey Set',
                            'price' => 50000,
                            'status' => 'available',
                            'features' => [
                                ['icon' => 'tshirt', 'text' => 'All Sizes'],
                                ['icon' => 'layer-group', 'text' => 'Full Set'],
                                ['icon' => 'shield-alt', 'text' => 'Clean & Fresh'],
                                ['icon' => 'sync-alt', 'text' => 'Daily Wash'],
                            ],
                            'image' =>
                                'https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b',
                        ],
                        [
                            'id' => 'ball',
                            'name' => 'Soccer Ball',
                            'price' => 30000,
                            'status' => 'available',
                            'features' => [
                                ['icon' => 'certificate', 'text' => 'Official Size'],
                                ['icon' => 'star', 'text' => 'Premium'],
                                ['icon' => 'check-circle', 'text' => 'Match Quality'],
                                ['icon' => 'pump-soap', 'text' => 'Sanitized'],
                            ],
                            'image' => 'assets/ball.avif',
                        ],
                        [
                            'id' => 'shoes',
                            'name' => 'Soccer Shoes',
                            'price' => 40000,
                            'status' => 'limited',
                            'features' => [
                                ['icon' => 'ruler', 'text' => 'Size 39-45'],
                                ['icon' => 'shoe-prints', 'text' => 'Studs/Turf'],
                                ['icon' => 'spray-can', 'text' => 'Deodorized'],
                                ['icon' => 'shield-alt', 'text' => 'Sanitized'],
                            ],
                            'image' => 'assets/shoes.avif',
                        ],
                    ];
                @endphp

                @foreach ($equipments as $equipment)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="position-relative">
                                <img src="{{ $equipment['image'] }}" class="card-img-top" alt="{{ $equipment['name'] }}">

                                @switch($equipment['status'])
                                    @case('available')
                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                            <i class="fas fa-check-circle me-1"></i> In Stock
                                        </span>
                                    @break

                                    @case('limited')
                                        <span class="badge bg-warning position-absolute top-0 end-0 m-2">
                                            <i class="fas fa-clock me-1"></i> Limited Stock
                                        </span>
                                    @break
                                @endswitch
                            </div>

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="card-title mb-0">{{ $equipment['name'] }}</h5>
                                    <div>
                                        <span class="h5 text-danger mb-0">Rp
                                            {{ number_format($equipment['price'], 0, ',', '.') }}</span>
                                        <small class="text-muted">/day</small>
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">
                                    @foreach ($equipment['features'] as $feature)
                                        <div class="col-6 d-flex align-items-center">
                                            <i class="fas fa-{{ $feature['icon'] }} me-2 text-muted"></i>
                                            <small>{{ $feature['text'] }}</small>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($equipment['status'] != 'limited')
                                    <a href="/product-rental" class="btn btn-danger w-100 rounded-pill">
                                        Rent Now <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                @else
                                    <button class="btn btn-secondary w-100 rounded-pill" disabled>
                                        Limited Stock <i class="fas fa-clock ms-2"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    {{-- End Equipment Section --}}

    <!-- Community Section -->
    <section id="community" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">Community</h2>
                <p class="text-muted">Join our growing community of sports enthusiasts</p>
            </div>

            <div class="row g-4">
                <!-- Testimonials Card -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg rounded-4 h-100">
                        <div class="card-body p-4 text-center">
                            <h3 class="h4 mb-4 d-flex align-items-center justify-content-center gap-2">
                                <i class="fas fa-quote-left text-danger fa-2x"></i>
                                <span>What Our Members Say</span>
                            </h3>

                            <!-- Testimonial Carousel -->
                            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <!-- Testimonial 1 -->
                                    <div class="carousel-item active">
                                        <div class="testimonial-item text-center">
                                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/2290ec8fa1d076a31ffece3da471c470b91bfd20a12a271551ae12f28bf93760"
                                                class="rounded-circle mb-3 shadow-sm" width="70" height="70"
                                                alt="John Doe">
                                            <h4 class="h5">John Doe</h4>
                                            <div class="text-warning mb-2">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                            <p class="mb-0 text-muted fst-italic px-3">"Amazing facilities and great
                                                service! The courts are always well-maintained and the staff is incredibly
                                                helpful. Best sports venue in the area!"</p>
                                        </div>
                                    </div>

                                    <!-- Testimonial 2 -->
                                    <div class="carousel-item">
                                        <div class="testimonial-item text-center">
                                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/2290ec8fa1d076a31ffece3da471c470b91bfd20a12a271551ae12f28bf93760"
                                                class="rounded-circle mb-3 shadow-sm" width="70" height="70"
                                                alt="Jane Smith">
                                            <h4 class="h5">Jane Smith</h4>
                                            <div class="text-warning mb-2">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                            <p class="mb-0 text-muted fst-italic px-3">"The membership benefits are
                                                fantastic! Love the community events and tournaments. It's more than just a
                                                sports facility - it's a community."</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Carousel Controls -->
                                <div class="d-flex justify-content-center gap-3 mt-4">
                                    <button
                                        class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center"
                                        data-bs-target="#testimonialCarousel" data-bs-slide="prev"
                                        style="width: 40px; height: 40px;">
                                        <i class="fas fa-arrow-left"></i>
                                    </button>
                                    <button
                                        class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center"
                                        data-bs-target="#testimonialCarousel" data-bs-slide="next"
                                        style="width: 40px; height: 40px;">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Open Mabar Card -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg rounded-4 h-100">
                        <div class="card-body p-4">
                            <h3 class="h4 mb-4">
                                <i class="fas fa-gamepad text-danger me-2"></i>
                                Open Mabar Event
                            </h3>

                            <div class="mabar-card bg-danger bg-opacity-10 rounded-4 p-4 mb-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h4 class="h3 mb-2">SportVue Open Play</h4>
                                        <p class="mb-0 text-muted">
                                            <i class="fas fa-calendar-alt me-2"></i>March 15-20, 2025
                                        </p>
                                    </div>
                                    <span class="badge bg-danger">Join Now</span>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6">
                                        <div class="bg-white rounded-3 p-3 text-center">
                                            <i class="fas fa-users text-danger mb-2"></i>
                                            <h5 class="h6 mb-1">12 Teams</h5>
                                            <small class="text-muted">Available to Join</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-white rounded-3 p-3 text-center">
                                            <i class="fas fa-gamepad text-danger mb-2"></i>
                                            <h5 class="h6 mb-1">5v5 / 7v7</h5>
                                            <small class="text-muted">Game Mode</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <a href="/mabar" class="btn btn-danger rounded-pill">
                                        Join a Team
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Open Mabar Features -->
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-comments text-danger me-2"></i>
                                        <span>Team Matching</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-random text-danger me-2"></i>
                                        <span>Auto Balancing</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-danger me-2"></i>
                                        <span>Flexible Schedule</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-headset text-danger me-2"></i>
                                        <span>Voice Chat Support</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    {{-- End Comunity Section --}}

    <!-- Footer -->
    <footer class="footer position-relative bg-dark text-white">
        <!-- Main Footer Content -->
        <div class="py-5">
            <div class="container">
                <div class="row g-4">
                    <!-- Brand Section -->
                    <div class="col-lg-4 mb-4">
                        <div class="pe-lg-5">
                            <div class="d-flex align-items-center mb-4">
                                <span class="fw-bold fs-4">
                                    ALENA<span class="text-white">
                                        S <img
                                            src="https://cdn.builder.io/api/v1/image/assets/TEMP/3bc3f968d66dd0c368130525f00d42ec550c3ea8f6304c68cbb117fa6eb8dc08"
                                            width="30" height="30" class="" alt="Alena Soccer Logo"> CCER
                                    </span>
                                </span>
                            </div>
                            <p class="text-white-50 mb-4">A vibrant community of sports enthusiasts connecting, playing,
                                and growing together. Join us in celebrating the spirit of sports!</p>
                            <div class="d-flex gap-3">
                                <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="btn btn-outline-light btn-sm rounded-circle">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Community Links -->
                    <div class="col-lg-2 col-md-4">
                        <h5 class="text-white mb-4">Community</h5>
                        <ul class="list-unstyled footer-links">
                            <li class="mb-2">
                                <a href="/events" class="text-white-50 text-decoration-none hover-text-white">Upcoming
                                    Events</a>
                            </li>
                            <li class="mb-2">
                                <a href="/teams" class="text-white-50 text-decoration-none hover-text-white">Team
                                    Matching</a>
                            </li>
                            <li class="mb-2">
                                <a href="/tournaments"
                                    class="text-white-50 text-decoration-none hover-text-white">Tournaments</a>
                            </li>
                            <li class="mb-2">
                                <a href="/forum" class="text-white-50 text-decoration-none hover-text-white">Community
                                    Forum</a>
                            </li>
                            <li class="mb-2">
                                <a href="/support" class="text-white-50 text-decoration-none hover-text-white">Community
                                    Support</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Information -->
                    <div class="col-lg-3 col-md-4">
                        <h5 class="text-white mb-4">Contact Community</h5>
                        <ul class="list-unstyled footer-contact">
                            <li class="d-flex align-items-center mb-3">
                                <div class="p-2 me-3">
                                    <i class="fas fa-headset text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-white-50">Community Support</p>
                                    <a href="tel:+628784017803" class="text-white text-decoration-none">+62 8784 0177
                                        803</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center mb-3">
                                <div class="p-2 rounded-circle me-3">
                                    <i class="fas fa-envelope text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-white-50">Community Email</p>
                                    <a href="mailto:community@sportvue.com"
                                        class="text-white text-decoration-none">community@sportvue.com</a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div class="p-2 rounded-circle me-3">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-white-50">Community Members</p>
                                    <span class="text-white">120+ Active Members</span>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Community Highlight -->
                    <div class="col-lg-3 col-md-4">
                        <h5 class="text-white mb-4">Community Highlights</h5>
                        <div class="rounded-3 overflow-hidden">
                            <div id="communityHighlightCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="assets/community-event-1.jpg" class="d-block w-100"
                                            alt="Community Event 1">
                                        <div class="carousel-caption d-none d-md-block">
                                            <h5>Open Play Tournament</h5>
                                            <p>March 15-20, 2025</p>
                                        </div>
                                    </div>
                                    <div class="carousel-item">
                                        <img src="assets/community-event-2.jpg" class="d-block w-100"
                                            alt="Community Event 2">
                                        <div class="carousel-caption d-none d-md-block">
                                            <h5>Team Matching Night</h5>
                                            <p>Connecting players across Jakarta</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div class="mt-5 pt-4 border-top border-secondary">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="text-white-50 mb-md-0">© 2024 SportVue Community. All rights reserved.</p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-md-end align-items-center">
                                <span class="text-white-50 me-3">Community Partners:</span>
                                <div class="d-flex gap-3">
                                    <i class="fas fa-futbol fs-3 text-white-50"></i>
                                    <i class="fas fa-trophy fs-3 text-white-50"></i>
                                    <i class="fas fa-medal fs-3 text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating WhatsApp Button -->
        <a href="https://wa.me/628784017803"
            class="btn btn-success rounded-circle position-fixed bottom-0 end-0 m-4 shadow-lg d-flex align-items-center justify-content-center"
            style="width: 60px; height: 60px; z-index: 1000;">
            <img src="assets/whatsapp.png" width="70">
        </a>
    </footer>
    <!-- Floating Chat Button -->
    {{-- End Footer Section --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
