@extends('layouts.app')
@section('content')
    <!-- Breadcrumb -->
    <nav class="breadcrumb-wrapper" style="margin-top: 50px;">
        <div class="container py-2">
            <ol class="custom-breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/" class="breadcrumb-link">
                        <i class="fas fa-home"></i>
                        <span>Home</span>
                    </a>
                </li>
                <li class="breadcrumb-item active text-white">
                    <i class="fas fa-camera"></i>
                    <span>Fotografer</span>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container py-4">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h2 class="section-title fw-bold mb-3">Jasa Fotografer</h2>
                <p class="section-desc mx-auto" style="max-width: 700px;">
                    Abadikan momen bermain sepak bola Anda dengan jasa fotografer profesional kami. Dapatkan foto berkualitas tinggi untuk kenangan tak terlupakan.
                </p>
            </div>

            <!-- Filter & Sort Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('user.photographer.index') }}" method="GET" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="search-input">
                                    <i class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                    <input type="text" name="search" class="form-control ps-5" placeholder="Cari jasa fotografer..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select name="sort" class="form-select" onchange="this.form.submit()">
                                    <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-danger rounded-pill w-100">
                                    <i class="fas fa-filter me-2"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="result-badge">
                    <span class="badge bg-danger-subtle text-danger rounded-pill">
                        {{ $photographers->count() }} paket ditemukan
                    </span>
                </div>
            </div>

            <!-- Package Grid -->
            <div class="row g-4">
                @forelse($photographers as $photographer)
                    <!-- Photographer Package Card -->
                    <div class="col-lg-4 col-md-6">
                        <div class="card photographer-card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-img-wrapper position-relative">
                                @if($photographer->package_type == 'exclusive')
                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">Exclusive</span>
                                @elseif($photographer->package_type == 'plus')
                                    <span class="badge bg-primary position-absolute top-0 end-0 m-2">Plus</span>
                                @elseif($photographer->package_type == 'favorite')
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2">Favorite</span>
                                @endif

                                @if ($photographer->image)
                                    <img src="{{ asset('storage/' . $photographer->image) }}" class="card-img-top rounded-top-4" alt="{{ $photographer->name }}">
                                @else
                                    <img src="/api/placeholder/300/300" class="card-img-top rounded-top-4" alt="{{ $photographer->name }}">
                                @endif
                            </div>
                            <div class="card-body p-3">
                                <!-- Header -->
                                <div class="mb-3">
                                    <div class="category-badge mb-2">
                                        <span class="badge bg-danger-subtle text-danger rounded-pill">
                                            <i class="fas fa-camera me-1"></i> {{ ucfirst($photographer->package_type) }}
                                        </span>
                                    </div>
                                    <h5 class="card-title mb-1">{{ $photographer->name }}</h5>
                                    <p class="card-text text-muted small">{{ $photographer->description }}</p>
                                </div>

                                <!-- Features -->
                                <div class="features-list mb-3">
                                    <ul class="list-unstyled">
                                        @if(is_array($photographer->features))
                                            @foreach(array_slice($photographer->features, 0, 3) as $feature)
                                                <li class="mb-1">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    <span>{{ $feature }}</span>
                                                </li>
                                            @endforeach
                                            @if(count($photographer->features) > 3)
                                                <li class="text-center mt-2">
                                                    <small class="text-danger">+{{ count($photographer->features) - 3 }} fitur lainnya</small>
                                                </li>
                                            @endif
                                        @endif
                                    </ul>
                                </div>

                                <hr class="border-1 border-dashed opacity-50 my-2">

                                <!-- Footer -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price-info">
                                        <span class="h5 mb-0 text-danger">Rp{{ number_format($photographer->price, 0, ',', '.') }}</span>
                                        <small class="text-muted">/sesi</small>
                                    </div>
                                    <a href="{{ route('user.photographer.show', $photographer->id) }}" class="btn btn-sm btn-outline-danger rounded-pill">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                            <h4>Tidak Ada Paket Fotografer Ditemukan</h4>
                            <p class="text-muted">Maaf, tidak ada paket fotografer yang sesuai dengan kriteria pencarian Anda.</p>
                            <a href="{{ route('user.photographer.index') }}" class="btn btn-outline-danger rounded-pill mt-3">
                                <i class="fas fa-sync-alt me-2"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->


            <!-- Info Section -->
            <div class="info-section mt-5 p-4 bg-light rounded-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6">
                        <h3 class="mb-3">Cara Booking Fotografer</h3>
                        <ol class="photographer-steps">
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">1</div>
                                    <div>
                                        <h5 class="mb-1">Pilih Paket</h5>
                                        <p class="text-muted mb-0">Pilih paket fotografer yang sesuai dengan kebutuhan Anda.</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">2</div>
                                    <div>
                                        <h5 class="mb-1">Tentukan Jadwal</h5>
                                        <p class="text-muted mb-0">Pilih tanggal dan jam yang Anda inginkan.</p>
                                    </div>
                                </div>
                            </li>
                            <li class="mb-3">
                                <div class="d-flex">
                                    <div class="step-number me-3">3</div>
                                    <div>
                                        <h5 class="mb-1">Konfirmasi Pembayaran</h5>
                                        <p class="text-muted mb-0">Lakukan pembayaran melalui metode yang tersedia.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="step-number me-3">4</div>
                                    <div>
                                        <h5 class="mb-1">Dapatkan Foto</h5>
                                        <p class="text-muted mb-0">Fotografer akan hadir sesuai jadwal dan hasil foto akan dikirim melalui Google Drive.</p>
                                    </div>
                                </div>
                            </li>
                        </ol>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="{{ asset('assets/photographer.jpg') }}" alt="Photographer Service" class="img-fluid rounded-4 shadow">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
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

        /* Photographer Card Styles */
        .photographer-card .card-img-wrapper {
            height: 200px;
            overflow: hidden;
        }

        .photographer-card .card-img-top {
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .photographer-card:hover .card-img-top {
            transform: scale(1.05);
        }

        /* Features List */
        .features-list li {
            font-size: 0.9rem;
        }

        /* Photographer Steps */
        .photographer-steps {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #9e0620;
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* Card Hover Effects */
        .card {
            background: #ffffff;
            transition: all 0.3s ease;
            border: none !important;
        }

        .card.border-0.shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        /* Form Controls */
        .search-input {
            position: relative;
        }

        .form-control,
        .form-select {
            border: 1.5px solid #dee2e6;
            padding: 0.75rem 1rem;
            border-radius: 50px;
        }
    </style>
@endsection
