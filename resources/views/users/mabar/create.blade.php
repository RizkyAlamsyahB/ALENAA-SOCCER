@extends('layouts.app')
@section('content')
    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Buat Open Mabar</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i> Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.mabar.index') }}">Main Bareng</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Buat Open Mabar</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <!-- Bootstrap Alert for Session Messages -->
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('info'))
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    {{ session('info') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ session('warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <h4 class="card-title mb-4">Form Pembuatan Open Mabar</h4>

                            <form action="{{ route('user.mabar.store') }}" method="POST">
                                @csrf

                                <!-- Pilih Booking Lapangan -->
                                <div class="mb-4">
                                    <label for="field_booking_id" class="form-label">Pilih Booking Lapangan</label>
                                    <select id="field_booking_id" name="field_booking_id"
                                        class="form-select @error('field_booking_id') is-invalid @enderror" required>
                                        <option value="" disabled selected>-- Pilih Booking Lapangan --</option>
                                        @foreach ($availableBookings as $booking)
                                            <option value="{{ $booking->id }}"
                                                {{ old('field_booking_id') == $booking->id ? 'selected' : '' }}>
                                                {{ $booking->field->name }} -
                                                {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y H:i') }} -
                                                {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('field_booking_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle text-primary"></i>
                                        Pilih booking lapangan yang sudah Anda miliki untuk digunakan sebagai Open Mabar.
                                    </div>
                                </div>

                                <!-- Judul Open Mabar -->
                                <div class="mb-4">
                                    <label for="title" class="form-label">Judul Open Mabar</label>
                                    <input type="text" id="title" name="title"
                                        class="form-control @error('title') is-invalid @enderror"
                                        value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div class="form-text">
                                        Contoh: "Fun Futsal Jumat Malam" atau "Latihan Futsal untuk Pemula"
                                    </div>
                                </div>

                                <!-- Deskripsi -->
                                <div class="mb-4">
                                    <label for="description" class="form-label">Deskripsi (Opsional)</label>
                                    <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div class="form-text">
                                        Jelaskan tentang event Anda, aturan main, dan informasi tambahan lainnya.
                                    </div>
                                </div>

                                <!-- Level Pemain -->
                                <div class="mb-4">
                                    <label for="level" class="form-label">Level Pemain</label>
                                    <select id="level" name="level"
                                        class="form-select @error('level') is-invalid @enderror" required>
                                        <option value="" disabled selected>-- Pilih Level --</option>
                                        @foreach ($levels as $level)
                                            <option value="{{ $level }}"
                                                {{ old('level') == $level ? 'selected' : '' }}>
                                                {{ ucfirst($level) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Harga per Slot -->
                                <div class="mb-4">
                                    <label for="price_per_slot" class="form-label">Harga per Orang</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" id="price_per_slot" name="price_per_slot"
                                            class="form-control @error('price_per_slot') is-invalid @enderror"
                                            value="{{ old('price_per_slot') }}" min="0" required>
                                    </div>
                                    @error('price_per_slot')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div class="form-text">
                                        Tentukan harga yang harus dibayar per orang untuk bergabung.
                                    </div>
                                </div>

                                <!-- Jumlah Slot -->
                                <div class="mb-4">
                                    <label for="total_slots" class="form-label">Jumlah Slot Pemain</label>
                                    <input type="number" id="total_slots" name="total_slots"
                                        class="form-control @error('total_slots') is-invalid @enderror"
                                        value="{{ old('total_slots') }}" min="1" max="30" required>
                                    @error('total_slots')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div class="form-text">
                                        Tentukan berapa banyak pemain yang Anda butuhkan (maksimal 30 orang).
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="alert alert-info mb-4">
                                    <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Informasi Penting:</h6>
                                    <ul class="mb-0 ps-3">
                                        <li>Open Mabar akan secara otomatis menggunakan jadwal dan lapangan dari booking
                                            yang Anda pilih.</li>
                                        <li>Status pembayaran peserta dapat Anda konfirmasi saat mereka hadir di lapangan.
                                        </li>
                                        <li>Pembayaran dilakukan secara langsung (cash) di lokasi.</li>
                                    </ul>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-danger rounded-pill py-3">
                                        <i class="fas fa-plus-circle me-2"></i> Buat Open Mabar
                                    </button>
                                    <a href="{{ route('user.mabar.index') }}"
                                        class="btn btn-outline-secondary rounded-pill py-3">
                                        <i class="fas fa-arrow-left me-2"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
