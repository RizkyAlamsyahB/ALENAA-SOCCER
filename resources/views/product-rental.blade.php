@extends('layouts.app')
@section('content')
    <style>
        :root {
            --primary-color: #9E0620;
            --secondary-color: #2A2A2A;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
        }


        .product-gallery {
            border-radius: 15px;
            overflow: hidden;
            background: white;
            padding: 1rem;
        }

        .product-thumbnail {
            cursor: pointer;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .product-thumbnail:hover {
            transform: scale(1.05);
        }

        .feature-item {
            padding: 1rem;
            border-radius: 10px;
            background: white;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .size-selector input[type="radio"] {
            display: none;
        }

        .size-selector label {
            padding: 8px 20px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .size-selector input[type="radio"]:checked+label {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .related-product-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        .related-product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-rent {
            padding: 12px 24px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-rent:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(158, 6, 32, 0.3);
        }

        .product-rating {
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 50px;
        }
    </style>


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
                    <a href="/venues" class="breadcrumb-link">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Venues</span>
                    </a>
                </li>
                <li class="breadcrumb-item active">
                    <i class="fas fa-futbol"></i>
                    <span>Field A</span>
                </li>
            </ol>
        </div>
    </nav>

    <style>
        .breadcrumb-wrapper {
            background: linear-gradient(to right, #9E0620, #bb2d3b);
            position: relative;
            overflow: hidden;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            width: 100%;
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
            /* Increased from 700 to 800 */
            font-size: 1.3rem;
            /* Added explicit font size */
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
            /* Increased from 700 to 800 */
            font-size: 1.3rem;
            /* Increased from 1.1rem to 1.3rem */
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
            /* Increased from 700 to 800 */
            font-size: 1.3rem;
            /* Increased from 1.1rem to 1.3rem */
        }

        /* Updated media query for mobile responsiveness */
        @media (max-width: 768px) {

            .breadcrumb-link,
            .breadcrumb-item.active {
                padding: 6px;
                font-size: 1.2rem;
                /* Slightly smaller on mobile but still larger than original */
            }

            .breadcrumb-item i {
                font-size: 1.2rem;
                /* Increased from 1.1rem to 1.2rem */
            }
        }
    </style>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row g-4">
            <!-- Product Gallery -->
            <!-- Product Gallery -->
            <div class="col-lg-6">
                <div class="product-gallery">
                    <!-- Main Carousel -->
                    <div id="productCarousel" class="carousel slide mb-4" data-bs-ride="false">
                        <div class="carousel-inner rounded-4 overflow-hidden position-relative">
                            <div class="carousel-item active">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="d-block w-100" alt="Jersey Set Main">
                            </div>
                            <div class="carousel-item">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="d-block w-100" alt="Jersey Set 1">
                            </div>
                            <div class="carousel-item">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="d-block w-100" alt="Jersey Set 2">
                            </div>
                            <div class="carousel-item">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="d-block w-100" alt="Jersey Set 3">
                            </div>

                            <!-- Image Counter Badge -->
                            <div class="image-counter">
                                <i class="fas fa-camera"></i>
                                <span class="counter-text">1/4</span>
                            </div>
                        </div>

                        <!-- Navigation Arrows -->
                        <button class="carousel-control carousel-control-prev" type="button"
                            data-bs-target="#productCarousel" data-bs-slide="prev">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="carousel-control carousel-control-next" type="button"
                            data-bs-target="#productCarousel" data-bs-slide="next">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>

                    <!-- Thumbnails -->
                    <div class="thumbnails-wrapper">
                        <div class="thumbnails-container">
                            <div class="product-thumbnail active" role="button" data-bs-target="#productCarousel"
                                data-bs-slide-to="0">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="img-fluid" alt="Jersey Set Main">
                            </div>
                            <div class="product-thumbnail" role="button" data-bs-target="#productCarousel"
                                data-bs-slide-to="1">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="img-fluid" alt="Jersey Set 1">
                            </div>
                            <div class="product-thumbnail" role="button" data-bs-target="#productCarousel"
                                data-bs-slide-to="2">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="img-fluid" alt="Jersey Set 2">
                            </div>
                            <div class="product-thumbnail" role="button" data-bs-target="#productCarousel"
                                data-bs-slide-to="3">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="img-fluid" alt="Jersey Set 3">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .product-gallery {
                    background: white;
                    padding: 1.5rem;
                    border-radius: 24px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
                    transition: all 0.3s ease;
                }

                .product-gallery:hover {
                    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
                }

                /* Main Carousel */
                .carousel-inner {
                    border-radius: 20px;
                    overflow: hidden;
                    aspect-ratio: 4/3;
                }

                .carousel-inner img {
                    object-fit: cover;
                    height: 100%;
                    width: 100%;
                }

                /* Image Counter Badge */
                .image-counter {
                    position: absolute;
                    bottom: 20px;
                    right: 20px;
                    background: rgba(0, 0, 0, 0.6);
                    color: white;
                    padding: 8px 16px;
                    border-radius: 20px;
                    font-size: 0.9rem;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    backdrop-filter: blur(4px);
                }

                /* Navigation Controls */
                .carousel-control {
                    width: 48px;
                    height: 48px;
                    background: rgba(255, 255, 255, 0.9);
                    border-radius: 50%;
                    top: 50%;
                    transform: translateY(-50%);
                    border: none;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #1a1a1a;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                    transition: all 0.3s ease;
                }

                .carousel-control:hover {
                    background: white;
                    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
                    transform: translateY(-50%) scale(1.05);
                }

                .carousel-control-prev {
                    left: 20px;
                }

                .carousel-control-next {
                    right: 20px;
                }

                /* Thumbnails */
                .thumbnails-wrapper {
                    margin-top: 1.5rem;
                    position: relative;
                }

                .thumbnails-container {
                    display: grid;
                    grid-template-columns: repeat(4, 1fr);
                    gap: 1rem;
                }

                .product-thumbnail {
                    position: relative;
                    border-radius: 12px;
                    overflow: hidden;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .product-thumbnail::before {
                    content: '';
                    position: absolute;
                    inset: 0;
                    background: rgba(0, 0, 0, 0.2);
                    opacity: 1;
                    transition: all 0.3s ease;
                }

                .product-thumbnail.active::before,
                .product-thumbnail:hover::before {
                    opacity: 0;
                }

                .product-thumbnail img {
                    aspect-ratio: 1;
                    object-fit: cover;
                    width: 100%;
                }

                .product-thumbnail:hover {
                    transform: translateY(-4px);
                }

                .product-thumbnail.active {
                    box-shadow: 0 0 0 3px #9e0620;
                    transform: translateY(-4px);
                }

                /* Responsive Design */
                @media (max-width: 768px) {
                    .product-gallery {
                        padding: 1rem;
                    }

                    .carousel-control {
                        width: 40px;
                        height: 40px;
                    }

                    .image-counter {
                        bottom: 15px;
                        right: 15px;
                        padding: 6px 12px;
                        font-size: 0.8rem;
                    }

                    .thumbnails-container {
                        gap: 0.5rem;
                    }
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const carousel = document.getElementById('productCarousel');
                    const counter = document.querySelector('.counter-text');
                    const thumbnails = document.querySelectorAll('.product-thumbnail');

                    // Update counter and thumbnails when carousel slides
                    carousel.addEventListener('slide.bs.carousel', function(e) {
                        counter.textContent = `${e.to + 1}/4`;
                        thumbnails.forEach((thumb, i) => {
                            thumb.classList.toggle('active', i === e.to);
                        });
                    });

                    // Initialize counter
                    counter.textContent = '1/4';
                });
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Get all thumbnails
                    const thumbnails = document.querySelectorAll('.product-thumbnail');

                    // Add click event to thumbnails
                    thumbnails.forEach(thumb => {
                        thumb.addEventListener('click', function() {
                            // Remove active class from all thumbnails
                            thumbnails.forEach(t => t.classList.remove('active'));
                            // Add active class to clicked thumbnail
                            this.classList.add('active');
                        });
                    });

                    // Listen for carousel slide event to update active thumbnail
                    const carousel = document.getElementById('productCarousel');
                    carousel.addEventListener('slide.bs.carousel', function(e) {
                        thumbnails.forEach(t => t.classList.remove('active'));
                        thumbnails[e.to].classList.add('active');
                    });
                });
            </script>

            <!-- Product Details Card -->
            <div class="col-lg-6">
                <div class="product-card">
                    <div class="card-content">
                        <!-- Header Section -->
                        <div class="product-header">
                            <div class="title-section">
                                <h1 class="product-title">Jersey Set Premium</h1>
                                <div class="product-meta">
                                    <div class="rating-badge">
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                            <span class="rating-value">4.8</span>
                                        </div>
                                        <span class="review-count">(128 reviews)</span>
                                    </div>
                                    <div class="stock-badge">In Stock</div>
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div class="product-description">
                            <h3 class="section-title">Product Description</h3>
                            <p>Experience premium comfort with our high-quality jersey set. Perfect for both professional
                                matches and casual games. Made with moisture-wicking fabric to keep you cool during intense
                                activities.</p>
                        </div>

                        <!-- Price Section -->
                        <div class="price-section">
                            <div class="price-tag">
                                <span class="amount">Rp 50.000</span>
                                <span class="duration">/day</span>
                            </div>
                        </div>

                        <!-- Selection Section -->
                        <div class="selection-group">
                            <div class="size-section">
                                <h3 class="section-title">Select Size</h3>
                                <div class="size-options">
                                    <input type="radio" name="size" id="sizeS" checked>
                                    <label for="sizeS" class="size-chip">All Size</label>
                                </div>
                            </div>

                            <div class="duration-section">
                                <h3 class="section-title">Rental Duration</h3>
                                <select class="duration-select">
                                    <option>1 Day</option>
                                    <option>2 Days</option>
                                    <option>3 Days</option>
                                    <option>1 Week</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button class="btn-primary">
                                <span>Rent Now</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                            <button class="btn-secondary">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Add to Cart</span>
                            </button>
                        </div>

                        <!-- Features Grid -->
                        <div class="features-grid">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <div class="feature-content">
                                    <h6>Premium Material</h6>
                                    <p>High-quality fabric</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-spray-can"></i>
                                </div>
                                <div class="feature-content">
                                    <h6>Fresh & Clean</h6>
                                    <p>Sanitized daily</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <div class="feature-content">
                                    <h6>Easy Return</h6>
                                    <p>Hassle-free process</p>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="feature-content">
                                    <h6>Safe Payment</h6>
                                    <p>Secure transaction</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                /* Modern Product Card Styles */
                .product-card {
                    background: #ffffff;
                    border-radius: 24px;
                    padding: 2rem;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
                    transition: all 0.3s ease;
                }

                .product-card:hover {
                    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
                }

                /* Header Styles */
                .product-header {
                    margin-bottom: 2rem;
                }

                .product-title {
                    font-size: 2rem;
                    font-weight: 700;
                    margin-bottom: 1rem;
                    color: #1a1a1a;
                }

                .product-meta {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                }

                .rating-badge {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    background: #f8f9fa;
                    padding: 0.5rem 1rem;
                    border-radius: 12px;
                }

                .stars {
                    color: #ffc107;
                    display: flex;
                    align-items: center;
                    gap: 0.25rem;
                }

                .rating-value {
                    margin-left: 0.5rem;
                    font-weight: 600;
                    color: #1a1a1a;
                }

                .review-count {
                    color: #6c757d;
                    font-size: 0.9rem;
                }

                .stock-badge {
                    background: #e8f5e9;
                    color: #2e7d32;
                    padding: 0.5rem 1rem;
                    border-radius: 12px;
                    font-weight: 500;
                }

                /* Section Styles */
                .section-title {
                    font-size: 1.1rem;
                    font-weight: 600;
                    color: #1a1a1a;
                    margin-bottom: 1rem;
                }

                .product-description {
                    margin-bottom: 2rem;
                }

                .product-description p {
                    color: #6c757d;
                    line-height: 1.6;
                }

                /* Price Section */
                .price-section {
                    margin-bottom: 2rem;
                }

                .price-tag {
                    display: flex;
                    align-items: baseline;
                    gap: 0.5rem;
                }

                .amount {
                    font-size: 2rem;
                    font-weight: 700;
                    color: #9e0620;
                }

                .duration {
                    color: #6c757d;
                }

                /* Selection Group */
                .selection-group {
                    display: grid;
                    gap: 1.5rem;
                    margin-bottom: 2rem;
                }

                .size-options {
                    display: flex;
                    gap: 1rem;
                }

                .size-chip {
                    background: #f8f9fa;
                    padding: 0.75rem 1.5rem;
                    border-radius: 12px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                input[type="radio"]:checked+.size-chip {
                    background: #9e0620;
                    color: white;
                }

                .duration-select {
                    width: 100%;
                    padding: 0.75rem;
                    border: 1px solid #dee2e6;
                    border-radius: 12px;
                    appearance: none;
                    background: url("data:image/svg+xml,...") no-repeat right 1rem center;
                }

                /* Action Buttons */
                .action-buttons {
                    display: grid;
                    gap: 1rem;
                    margin-bottom: 2rem;
                }

                .btn-primary,
                .btn-secondary {
                    padding: 1rem;
                    border-radius: 12px;
                    border: none;
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.5rem;
                    transition: all 0.3s ease;
                }

                .btn-primary {
                    background: #9e0620;
                    color: white;
                }

                .btn-primary:hover {
                    background: #7d051a;
                    transform: translateY(-2px);
                }

                .btn-secondary {
                    background: #f8f9fa;
                    color: #1a1a1a;
                }

                .btn-secondary:hover {
                    background: #e9ecef;
                    transform: translateY(-2px);
                }

                /* Features Grid */
                .features-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 1.5rem;
                }

                .feature-item {
                    display: flex;
                    align-items: flex-start;
                    gap: 1rem;
                    padding: 1.5rem;
                    background: #f8f9fa;
                    border-radius: 16px;
                    transition: all 0.3s ease;
                }

                .feature-item:hover {
                    transform: translateY(-3px);
                    background: #fff;
                    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
                }

                .feature-icon {
                    background: white;
                    width: 40px;
                    height: 40px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: #9e0620;
                }

                .feature-content h6 {
                    font-weight: 600;
                    margin-bottom: 0.25rem;
                    color: #1a1a1a;
                }

                .feature-content p {
                    font-size: 0.9rem;
                    color: #6c757d;
                    margin: 0;
                }

                /* Responsive Design */
                @media (max-width: 768px) {
                    .product-card {
                        padding: 1.5rem;
                    }

                    .features-grid {
                        grid-template-columns: 1fr;
                    }

                    .product-title {
                        font-size: 1.5rem;
                    }

                    .amount {
                        font-size: 1.5rem;
                    }
                }
            </style>
        </div>

        <!-- Related Products -->
        {{-- <div class="mt-5">
            <h3 class="mb-4">You May Also Like</h3>
            <div class="row g-4">
                <!-- Product 1 -->
                <div class="col-md-4">
                    <div class="related-product-card">
                        <div class="position-relative">
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/2ffcb5e8cb38ed2dd8db25cd0e3dd862204a6b722a476ac7565d282bd80762f6"
                                class="card-img-top" style="height: 250px; object-fit: cover;" alt="Basketball">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success-subtle text-success px-3 py-2">Available</span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-2">Professional Basketball</h5>
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-warning me-2">
                                    <i class="fas fa-star"></i>
                                    <span>4.7</span>
                                </div>
                                <span class="text-muted">(86 reviews)</span>
                            </div>
                            <p class="text-muted small mb-3">Official size basketball with premium grip.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h5 text-danger mb-0">Rp 30.000</span>
                                    <small class="text-muted">/day</small>
                                </div>
                                <a href="#" class="btn btn-outline-danger btn-sm rounded-pill">
                                    View Details
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product 2 -->
                <div class="col-md-4">
                    <div class="related-product-card">
                        <div class="position-relative">
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/bf84d9d7b1c3cc434cdce744c97219185b60171a157cb1f3d6b3d315313fd4c5"
                                class="card-img-top" style="height: 250px; object-fit: cover;" alt="Training Shoes">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning-subtle text-warning px-3 py-2">Limited Stock</span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-2">Training Shoes</h5>
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-warning me-2">
                                    <i class="fas fa-star"></i>
                                    <span>4.8</span>
                                </div>
                                <span class="text-muted">(124 reviews)</span>
                            </div>
                            <p class="text-muted small mb-3">Professional sports shoes for optimal performance.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h5 text-danger mb-0">Rp 70.000</span>
                                    <small class="text-muted">/day</small>
                                </div>
                                <a href="#" class="btn btn-outline-danger btn-sm rounded-pill">
                                    View Details
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product 3 -->
                <div class="col-md-4">
                    <div class="related-product-card">
                        <div class="position-relative">
                            <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                class="card-img-top" style="height: 250px; object-fit: cover;" alt="Jersey Set Alternative">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-success-subtle text-success px-3 py-2">Available</span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title mb-2">Premium Jersey Set</h5>
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-warning me-2">
                                    <i class="fas fa-star"></i>
                                    <span>4.9</span>
                                </div>
                                <span class="text-muted">(156 reviews)</span>
                            </div>
                            <p class="text-muted small mb-3">Alternative design with premium quality material.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="h5 text-danger mb-0">Rp 50.000</span>
                                    <small class="text-muted">/day</small>
                                </div>
                                <a href="#" class="btn btn-outline-danger btn-sm rounded-pill">
                                    View Details
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Product Information Tabs -->
        <div class="mt-5">
            <!-- Modern Tab Navigation -->
            <ul class="nav nav-tabs border-0 gap-2" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-4 py-2 border-0" data-bs-toggle="tab"
                        data-bs-target="#description"
                        style="background: #f8f9fa; color: #1a1a1a; transition: all 0.3s ease;">
                        Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 py-2 border-0" data-bs-toggle="tab"
                        data-bs-target="#specifications"
                        style="background: #f8f9fa; color: #1a1a1a; transition: all 0.3s ease;">
                        Specifications
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-4 py-2 border-0" data-bs-toggle="tab"
                        data-bs-target="#reviews" style="background: #f8f9fa; color: #1a1a1a; transition: all 0.3s ease;">
                        Reviews
                    </button>
                </li>
            </ul>

            <!-- Modern Tab Content -->
            <div class="tab-content p-4 bg-white rounded-3 shadow-sm" style="border-radius: 24px; margin-top: 1rem;">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description">
                    <h4 class="mb-4 fw-bold">Product Description</h4>
                    <p class="text-muted">Experience ultimate comfort and style with our premium jersey set. Made from
                        high-quality moisture-wicking material, this set is perfect for both professional matches and casual
                        games. The breathable fabric ensures you stay cool and comfortable during intense activities.</p>

                    <div class="row mt-4 g-4">
                        <div class="col-md-6">
                            <div class="p-4 rounded-3" style="background: #f8f9fa;">
                                <h5 class="mb-3 fw-semibold">Key Features</h5>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3 d-flex align-items-center">
                                        <span
                                            class="me-2 rounded-circle bg-success p-1 d-flex align-items-center justify-content-center"
                                            style="width: 24px; height: 24px;">
                                            <i class="fas fa-check text-white small"></i>
                                        </span>
                                        Premium quality fabric
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <span
                                            class="me-2 rounded-circle bg-success p-1 d-flex align-items-center justify-content-center"
                                            style="width: 24px; height: 24px;">
                                            <i class="fas fa-check text-white small"></i>
                                        </span>
                                        Moisture-wicking technology
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <span
                                            class="me-2 rounded-circle bg-success p-1 d-flex align-items-center justify-content-center"
                                            style="width: 24px; height: 24px;">
                                            <i class="fas fa-check text-white small"></i>
                                        </span>
                                        Breathable material
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <span
                                            class="me-2 rounded-circle bg-success p-1 d-flex align-items-center justify-content-center"
                                            style="width: 24px; height: 24px;">
                                            <i class="fas fa-check text-white small"></i>
                                        </span>
                                        Comfortable fit
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-4 rounded-3" style="background: #f8f9fa;">
                                <h5 class="mb-3 fw-semibold">Package Includes</h5>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3 d-flex align-items-center">
                                        <span
                                            class="me-2 rounded-circle bg-success p-1 d-flex align-items-center justify-content-center"
                                            style="width: 24px; height: 24px;">
                                            <i class="fas fa-check text-white small"></i>
                                        </span>
                                        Jersey Top
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <span
                                            class="me-2 rounded-circle bg-success p-1 d-flex align-items-center justify-content-center"
                                            style="width: 24px; height: 24px;">
                                            <i class="fas fa-check text-white small"></i>
                                        </span>
                                        Matching Shorts
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <span
                                            class="me-2 rounded-circle bg-success p-1 d-flex align-items-center justify-content-center"
                                            style="width: 24px; height: 24px;">
                                            <i class="fas fa-check text-white small"></i>
                                        </span>
                                        Team Logo
                                    </li>
                                    <li class="d-flex align-items-center">
                                        <span
                                            class="me-2 rounded-circle bg-success p-1 d-flex align-items-center justify-content-center"
                                            style="width: 24px; height: 24px;">
                                            <i class="fas fa-check text-white small"></i>
                                        </span>
                                        Number Customization
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane fade" id="specifications">
                    <h4 class="mb-4 fw-bold">Product Specifications</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr class="border-0">
                                    <th class="ps-0 py-3 border-0 text-muted" style="width: 30%">Material</th>
                                    <td class="pe-0 py-3 border-0">100% Polyester</td>
                                </tr>
                                <tr>
                                    <th class="ps-0 py-3 border-0 text-muted">Available Sizes</th>
                                    <td class="pe-0 py-3 border-0">S, M, L, XL</td>
                                </tr>
                                <tr>
                                    <th class="ps-0 py-3 border-0 text-muted">Care Instructions</th>
                                    <td class="pe-0 py-3 border-0">Machine washable, Tumble dry low</td>
                                </tr>
                                <tr>
                                    <th class="ps-0 py-3 border-0 text-muted">Features</th>
                                    <td class="pe-0 py-3 border-0">Moisture-wicking, Quick-dry, Breathable</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0 fw-bold">Customer Reviews</h4>
                        <button class="btn btn-outline-danger rounded-pill px-4 py-2">
                            <i class="fas fa-pencil-alt me-2"></i>Write a Review
                        </button>
                    </div>

                    <!-- Review Summary -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-4 border-end">
                            <div class="text-center">
                                <h1 class="display-4 fw-bold mb-2">4.8</h1>
                                <div class="text-warning mb-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <p class="text-muted mb-0">Based on 128 reviews</p>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="ps-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="text-muted me-3" style="min-width: 60px">5 stars</span>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar" style="width: 85%; background-color: #9e0620;"></div>
                                    </div>
                                    <span class="text-muted ms-3" style="min-width: 40px">85%</span>
                                </div>
                                <!-- Repeat for other star ratings -->
                            </div>
                        </div>
                    </div>

                    <!-- Individual Reviews -->
                    <div class="review-list">
                        <div class="card border-0 shadow-sm mb-3 rounded-3">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <h6 class="mb-1 fw-semibold">John Doe</h6>
                                        <div class="text-warning">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <small class="text-muted">2 days ago</small>
                                </div>
                                <p class="mb-0 text-muted">Great quality jersey! The material is comfortable and
                                    breathable. Perfect for my soccer matches.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Load More Button -->
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-danger rounded-pill px-4 py-2">
                            Load More Reviews
                            <i class="fas fa-chevron-down ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add this CSS to your stylesheet -->
        <style>
            .nav-tabs .nav-link.active {
                background-color: #9e0620 !important;
                color: white !important;
            }

            .nav-tabs .nav-link:hover:not(.active) {
                background-color: #e9ecef !important;
            }

            .progress {
                border-radius: 10px;
                background-color: #f8f9fa;
            }

            .btn-outline-danger {
                border-color: #9e0620;
                color: #9e0620;
            }

            .btn-outline-danger:hover {
                background-color: #9e0620;
                border-color: #9e0620;
            }

            .card {
                transition: all 0.3s ease;
            }

            .card:hover {
                transform: translateY(-2px);
            }

            @media (max-width: 768px) {
                .nav-tabs {
                    gap: 0.5rem !important;
                }

                .nav-link {
                    padding: 0.5rem 1rem !important;
                }
            }
        </style>




    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
