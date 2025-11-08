@extends('layouts.app')
@section('content')
    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Main Bareng</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i> Home</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Main Bareng</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Content -->
    <div class="main-content">

        <div class="container py-4">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <!-- Bootstrap Alert for Session Messages -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <p class="section-desc mx-auto" style="max-width: 700px;">
                    Komunitas Bola Tanpa Batas
                    Ingin bermain tapi tidak memiliki tim lengkap? Bergabunglah dengan program Main Bareng Alena Soccer!
                    Temukan teman baru, tingkatkan kemampuan, dan nikmati serunya bermain bersama dalam suasana yang
                    menyenangkan dan sportif.
                </p>

                <!-- Create Mabar Button -->
                <div class="mt-4">
                    <a href="{{ route('user.mabar.create') }}" class="btn btn-danger rounded-pill px-4 py-2">
                        <i class="fas fa-plus-circle me-2"></i> Buat Open Mabar
                    </a>
                    <a href="{{ route('user.mabar.my') }}" class="btn btn-outline-danger rounded-pill px-4 py-2 ms-2">
                        <i class="fas fa-list me-2"></i> Mabar Saya
                    </a>
                </div>
            </div>

            <!-- Filter & Sort Section -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('user.mabar.index') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="search-input">
                                    <i
                                        class="fas fa-search text-muted position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                                    <input type="text" name="search" class="form-control ps-5"
                                        placeholder="Cari event mabar..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="level">
                                    <option value="all" {{ request('level') == 'all' ? 'selected' : '' }}>Semua Level
                                    </option>
                                    <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>
                                        Beginner
                                    </option>
                                    <option value="intermediate"
                                        {{ request('level') == 'intermediate' ? 'selected' : '' }}>
                                        Intermediate</option>
                                    <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>
                                        Advanced
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="location">
                                    <option value="all">Semua Lapangan</option>
                                    @foreach ($fields as $field)
                                        <option value="{{ $field->id }}"
                                            {{ request('location') == $field->id ? 'selected' : '' }}>
                                            {{ $field->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="sort">
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru
                                    </option>
                                    <option value="price_lowest" {{ request('sort') == 'price_lowest' ? 'selected' : '' }}>
                                        Harga Terendah</option>
                                    <option value="price_highest"
                                        {{ request('sort') == 'price_highest' ? 'selected' : '' }}>Harga Tertinggi</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary rounded-pill px-4">
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
                    <span class="badge bg-primary-subtle text-primary rounded-pill">
                        {{ $openMabars->total() }} event ditemukan
                    </span>
                </div>
            </div>

            <!-- Event Grid -->
            <div class="row g-4">
                @forelse($openMabars as $mabar)
                    <!-- Event Card -->
                    <div class="col-lg-4">
                        <div class="card event-card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">
                                <!-- Header -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <div class="sport-type mb-2">
                                            <span class="badge bg-danger-subtle text-danger rounded-pill">
                                                <i class="fas fa-futbol me-1"></i>
                                                {{ $mabar->fieldBooking->field->type }}
                                            </span>
                                        </div>
                                        <h5 class="card-title mb-1">{{ $mabar->title }}</h5>
                                        <div class="d-flex align-items-center">
                                            <span class="level-badge badge bg-light text-dark">
                                                {{ ucfirst($mabar->level) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="slots-info">
                                        <span class="badge bg-danger-subtle text-danger rounded-pill">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $mabar->filled_slots }}/{{ $mabar->total_slots }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Event Info -->
                                <div class="event-info mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="info-icon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <span>{{ \Carbon\Carbon::parse($mabar->start_time)->format('D, d M Y') }} •
                                            {{ \Carbon\Carbon::parse($mabar->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($mabar->end_time)->format('H:i') }}</span>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <div class="info-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div>
                                            <span class="d-block">{{ $mabar->fieldBooking->field->name }}</span>
                                            <small class="text-muted">Dibuat oleh: {{ $mabar->user->name }}</small>
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-2 border-dashed opacity-50 my-3">

                                <!-- Footer -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price-info">
                                        <span
                                            class="h5 mb-0 text-danger">Rp{{ number_format($mabar->price_per_slot, 0, ',', '.') }}</span>
                                        <small class="text-muted">/orang</small>
                                    </div>
                                    <a href="{{ route('user.mabar.show', $mabar->id) }}"
                                        class="btn btn-outline-danger rounded-pill">
                                        Detail
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <!-- No Results -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 py-5">
                            <div class="card-body text-center">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h4>Tidak ada open mabar ditemukan</h4>
                                <p class="text-muted mb-4">Saat ini belum ada open mabar yang tersedia sesuai filter Anda.
                                </p>
                                <a href="{{ route('user.mabar.create') }}" class="btn btn-danger rounded-pill px-4 py-2">
                                    <i class="fas fa-plus-circle me-2"></i> Buat Open Mabar
                                </a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $openMabars->withQueryString()->links() }}
            </div>
        </div>
    </div>

    <style>
        /* Base Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(to right, #9e0620, #bb2d3b);
            height: 220px;
            position: relative;
            display: flex;
            align-items: center;
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

        /* Event Card Styles */
        .event-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .info-icon {
            width: 24px;
            color: #d00f25;
            margin-right: 10px;
        }

        .border-dashed {
            border-style: dashed !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
