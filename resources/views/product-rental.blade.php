<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jersey Set - SportVue</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #9E0620;
            --secondary-color: #2A2A2A;
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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

        .size-selector input[type="radio"]:checked + label {
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
</head>

<body>
    @include('partials.navbar')

    <!-- Breadcrumb -->
    <nav class="breadcrumb-custom">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/products">Products</a></li>
                <li class="breadcrumb-item active text-white">Jersey Set</li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row g-4">
            <!-- Product Gallery -->
            <div class="col-lg-6">
                <div class="product-gallery shadow-sm">
                    <!-- Main Image -->
                    <div class="mb-3">
                        <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                            class="img-fluid rounded" alt="Jersey Set Main">
                    </div>
                    <!-- Thumbnail Images -->
                    <div class="row g-2">
                        <div class="col-3">
                            <div class="product-thumbnail">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="img-fluid rounded" alt="Jersey Set 1">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="product-thumbnail">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="img-fluid rounded" alt="Jersey Set 2">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="product-thumbnail">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="img-fluid rounded" alt="Jersey Set 3">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="product-thumbnail">
                                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/5aa5ed7450a6694778d31686a44411c5b806b174bc5c0c366ecd748d4b3dfe9b"
                                    class="img-fluid rounded" alt="Jersey Set 4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h1 class="h2 mb-2">Jersey Set Premium</h1>
                                <div class="product-rating d-inline-flex align-items-center gap-2">
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span>4.8</span>
                                    <span class="text-muted">(128 reviews)</span>
                                </div>
                            </div>
                            <span class="badge bg-success-subtle text-success px-3 py-2">In Stock</span>
                        </div>

                        <div class="mb-4">
                            <h3 class="h5 mb-3">Product Description</h3>
                            <p class="text-muted">Experience premium comfort with our high-quality jersey set. Perfect for both professional matches and casual games. Made with moisture-wicking fabric to keep you cool during intense activities.</p>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h3 mb-0">Rp 50.000</span>
                                <span class="text-muted">/day</span>
                            </div>
                        </div>

                        <!-- Size Selection -->
                        <div class="mb-4">
                            <h3 class="h5 mb-3">Select Size</h3>
                            <div class="size-selector d-flex gap-2">
                                <input type="radio" name="size" id="sizeS">
                                <label for="sizeS">All Size</label>


                            </div>
                        </div>

                        <!-- Rental Duration -->
                        <div class="mb-4">
                            <h3 class="h5 mb-3">Rental Duration</h3>
                            <select class="form-select mb-3">
                                <option>1 Day</option>
                                <option>2 Days</option>
                                <option>3 Days</option>
                                <option>1 Week</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button class="btn btn-danger btn-rent">
                                Rent Now
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-rent">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Add to Cart
                            </button>
                        </div>

                        <!-- Features -->
                        <div class="mt-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="feature-item">
                                        <i class="fas fa-tshirt text-danger mb-2"></i>
                                        <h6 class="mb-1">Premium Material</h6>
                                        <small class="text-muted">High-quality fabric</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="feature-item">
                                        <i class="fas fa-spray-can text-danger mb-2"></i>
                                        <h6 class="mb-1">Fresh & Clean</h6>
                                        <small class="text-muted">Sanitized daily</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="feature-item">
                                        <i class="fas fa-exchange-alt text-danger mb-2"></i>
                                        <h6 class="mb-1">Easy Return</h6>
                                        <small class="text-muted">Hassle-free process</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="feature-item">
                                        <i class="fas fa-shield-alt text-danger mb-2"></i>
                                        <h6 class="mb-1">Safe Payment</h6>
                                        <small class="text-muted">Secure transaction</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-5">
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
        </div>

        <!-- Product Information Tabs -->
        <div class="mt-5">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">Description</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#specifications">Specifications</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">Reviews</button>
                </li>
            </ul>
            <div class="tab-content p-4 bg-white rounded-bottom shadow-sm">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description">
                    <h4 class="mb-4">Product Description</h4>
                    <p>Experience ultimate comfort and style with our premium jersey set. Made from high-quality moisture-wicking material, this set is perfect for both professional matches and casual games. The breathable fabric ensures you stay cool and comfortable during intense activities.</p>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Key Features</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Premium quality fabric</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Moisture-wicking technology</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Breathable material</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Comfortable fit</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Package Includes</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Jersey Top</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Matching Shorts</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Team Logo</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Number Customization</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Specifications Tab -->
                <div class="tab-pane fade" id="specifications">
                    <h4 class="mb-4">Product Specifications</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Material</th>
                                    <td>100% Polyester</td>
                                </tr>
                                <tr>
                                    <th>Available Sizes</th>
                                    <td>S, M, L, XL</td>
                                </tr>
                                <tr>
                                    <th>Care Instructions</th>
                                    <td>Machine washable, Tumble dry low</td>
                                </tr>
                                <tr>
                                    <th>Features</th>
                                    <td>Moisture-wicking, Quick-dry, Breathable</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">Customer Reviews</h4>
                        <button class="btn btn-outline-danger rounded-pill">
                            <i class="fas fa-pencil-alt me-2"></i>Write a Review
                        </button>
                    </div>

                    <!-- Review Summary -->
                    <div class="row mb-4">
                        <div class="col-md-4 border-end">
                            <div class="text-center">
                                <h1 class="display-4 fw-bold">4.8</h1>
                                <div class="text-warning mb-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <p class="text-muted">Based on 128 reviews</p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="ps-4">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted me-2">5 stars</span>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 85%"></div>
                                    </div>
                                    <span class="text-muted ms-2">85%</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted me-2">4 stars</span>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 10%"></div>
                                    </div>
                                    <span class="text-muted ms-2">10%</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted me-2">3 stars</span>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 3%"></div>
                                    </div>
                                    <span class="text-muted ms-2">3%</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-muted me-2">2 stars</span>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 1%"></div>
                                    </div>
                                    <span class="text-muted ms-2">1%</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="text-muted me-2">1 star</span>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-warning" style="width: 1%"></div>
                                    </div>
                                    <span class="text-muted ms-2">1%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Individual Reviews -->
                    <div class="review-list">
                        <!-- Review Item -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <h6 class="mb-0">John Doe</h6>
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
                                <p class="mb-0">Great quality jersey! The material is comfortable and breathable. Perfect for my soccer matches.</p>
                            </div>
                        </div>

                        <!-- More Review Items -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <h6 class="mb-0">Jane Smith</h6>
                                        <div class="text-warning">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                    </div>
                                    <small class="text-muted">1 week ago</small>
                                </div>
                                <p class="mb-0">The jersey fits perfectly and the rental process was very smooth. Would rent again!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Load More Button -->
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-danger rounded-pill px-4">
                            Load More Reviews
                            <i class="fas fa-chevron-down ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
