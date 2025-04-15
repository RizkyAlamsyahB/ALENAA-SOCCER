@extends('layouts.app')
@section('content')
    <!-- Link untuk font dan stylesheet tambahan -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Sewa Lapangan</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i>
                                    Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lapangan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Venues Filtering and Search -->
    <div class="container mt-4">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <p class="section-desc mx-auto" style="max-width: 700px;">
                Temukan Lapangan Terbaik
                Alena Soccer menyediakan berbagai pilihan lapangan berkualitas untuk kegiatan olahraga Anda. Dengan
                fasilitas modern dan lokasi strategis, kami memastikan pengalaman bermain yang nyaman dan memuaskan.
            </p>
        </div>


        <!-- Venues Listing -->
        <div class="row g-4 mb-4">
            @forelse($fields as $field)
                <!-- Venue Card -->
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm hover-shadow">
                        <div class="gallery-card">
                            <div class="gallery-img">
                                @if ($field->image)
                                    <img src="{{ Storage::url($field->image) }}" class="img-fluid w-100"
                                        alt="{{ $field->name }}">
                                @else
                                    <img src="{{ asset('images/default-field.jpg') }}" class="img-fluid w-100"
                                        alt="{{ $field->name }}">
                                @endif
                                <div class="gallery-overlay">
                                    <a href="{{ route('user.fields.show', $field->id) }}" class="view-btn">
                                        <i class="fas fa-expand-alt"></i>
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="card-title mb-0 fw-bold">{{ $field->name }}</h5>
                                <div class="rating-badge">
                                    <i class="fas fa-star text-warning me-1"></i>
                                    <span>{{ $field->rating ?? '4.5' }}</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="location-badge">
                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                    <span>{{ 'Sidoarjo, Indonesia' }}</span>
                                </div>
                                <div class="price-tag">
                                    <span class="text-danger fw-bold">Rp
                                        {{ number_format($field->price, 0, ',', '.') }}</span>
                                    <small class="text-muted">/hour</small>
                                </div>
                            </div>
                            <div class="mt-3 d-flex justify-content-between">
                                <div class="badge bg-success bg-opacity-10 text-success p-2">
                                    <i class="fas fa-check-circle me-1"></i>Available
                                </div>
                                <a href="{{ route('user.fields.show', $field->id) }}"
                                    class="btn btn-primary btn-sm rounded-pill">
                                    Book Now
                                    <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No fields found matching your criteria. Please try a
                        different search.
                    </div>
                </div>
            @endforelse


        </div>
    </div>

    <style>
        /* Hero Section - Consolidated Version */
        .hero-section {
            background: linear-gradient(to right, #9e0620, #bb2d3b);

            height: 220px;
            position: relative;
            display: flex;
            align-items: center;
            margin-top: 50px;
            /* Moved from inline style */
            margin-bottom: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .hero-content {
            color: white;
            text-align: center;
            width: 100%;
        }

        .hero-title {
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .breadcrumb-wrapper {
            display: flex;
            justify-content: center;
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            display: inline-flex;
            margin-bottom: 0;
        }

        .breadcrumb-item {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .breadcrumb-item.active {
            color: white;
            font-weight: 500;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: white;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Gallery Card Styling */
        .gallery-card {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .gallery-img {
            position: relative;
            overflow: hidden;
        }

        .gallery-img img {
            height: 250px;
            object-fit: cover;
            transition: all 0.5s ease;
        }

        .gallery-card:hover .gallery-img img {
            transform: scale(1.05);
        }

        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .gallery-card:hover .gallery-overlay {
            opacity: 1;
        }

        .view-btn {
            background: white;
            color: #333;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transform: translateY(20px);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .gallery-card:hover .view-btn {
            transform: translateY(0);
        }

        .view-btn:hover {
            background: #9e0620;
            color: white;
        }

        /* Location and Rating Badges */
        .location-badge,
        .rating-badge {
            padding: 6px 12px;
            background: #f8f9fa;
            border-radius: 50px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .location-badge:hover,
        .rating-badge:hover {
            background: #fff8f8;
        }

        .rating-badge i {
            margin-right: 4px;
        }

        /* Price Tag */
        .price-tag {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        /* Hover Shadow Effect */
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-section {
                height: 180px;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .gallery-img img {
                height: 200px;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.5rem;
            }

            .breadcrumb {
                padding: 0.6rem 1rem;
            }

            .breadcrumb-item {
                font-size: 0.8rem;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
