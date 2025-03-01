@extends('layouts.app')
@section('content')
    <!-- Breadcrumb -->
    <nav class="breadcrumb-wrapper" style="margin-top: 50px;">
        <div class="container py-2">
            <ol class="custom-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('users.dashboard') }}" class="breadcrumb-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Lapangan</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Venues Filtering and Search -->
    <div class="container mt-4">
        <!-- Header Section -->
        <div class="text-center mb-5">
            <h2 class="section-title fw-bold mb-3">Lapangan</h2>
            <p class="section-desc mx-auto" style="max-width: 700px;">
                Temukan Lapangan Terbaik
                Alena Soccer menyediakan berbagai pilihan lapangan berkualitas untuk kegiatan olahraga Anda. Dengan fasilitas modern dan lokasi strategis, kami memastikan pengalaman bermain yang nyaman dan memuaskan.
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
                                @if($field->image)
                                    <img src="{{ Storage::url($field->image) }}" class="img-fluid w-100" alt="{{ $field->name }}">
                                @else
                                    <img src="{{ asset('images/default-field.jpg') }}" class="img-fluid w-100" alt="{{ $field->name }}">
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
                                    <span>{{'Sidoarjo, Indonesia' }}</span>
                                </div>
                                <div class="price-tag">
                                    <span class="text-danger fw-bold">Rp {{ number_format($field->price, 0, ',', '.') }}</span>
                                    <small class="text-muted">/hour</small>
                                </div>
                            </div>
                            <div class="mt-3 d-flex justify-content-between">
                                <div class="badge bg-success bg-opacity-10 text-success p-2">
                                    <i class="fas fa-check-circle me-1"></i>Available
                                </div>
                                <a href="{{ route('user.fields.show', $field->id) }}" class="btn btn-primary btn-sm rounded-pill">
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
                        <i class="fas fa-info-circle me-2"></i>No fields found matching your criteria. Please try a different search.
                    </div>
                </div>
            @endforelse


        </div>
    </div>

    <style>
        /* Venues Page Specific Styles */

        /* Breadcrumb Wrapper */
        .breadcrumb-wrapper {
            background: linear-gradient(to right, #9e0620, #bb2d3b);
            position: relative;
            overflow: hidden;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-breadcrumb {
            display: flex;
            flex-wrap: wrap;
            padding: 0;
            margin: 0;
            list-style: none;
            align-items: center;
            justify-content: center;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 800;
            font-size: 1.3rem;
        }

        .breadcrumb-link {
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 50px;
            transition: all 0.3s ease;
            font-weight: 800;
            font-size: 1.3rem;
        }

        .breadcrumb-item.active {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            padding: 6px 12px;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.15);
            font-weight: 800;
            font-size: 1.3rem;
        }

        /* Venues Filter Card */
        .venues-filter .input-group-text {
            background: white;
            border-right: none;
        }

        .venues-filter .form-control.border-start-0 {
            border-left: none;
        }

        /* Venue Cards */
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
        }

        /* Gallery Card Styling (Reused from Detail Page) */
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

        /* Pagination */
        .pagination .page-link {
            color: #9e0620;
            background: white;
            border: 1px solid #dee2e6;
            margin: 0 5px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background: #9e0620;
            color: white;
        }

        .pagination .page-item.active .page-link {
            background: #9e0620;
            border-color: #9e0620;
            color: white;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .breadcrumb-wrapper {
                height: 150px;
            }

            .breadcrumb-item,
            .breadcrumb-link {
                font-size: 1.1rem;
            }

            .venues-filter .row>div {
                margin-bottom: 10px;
            }

            .gallery-img img {
                height: 200px;
            }
        }
          /* Section Styles */
          .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #212529;
        }

        .section-desc {
            font-size: 1.1rem;
            color: #6c757d;
            line-height: 1.6;
        }
    </style>

      <!-- Bootstrap JS -->
@endsection
