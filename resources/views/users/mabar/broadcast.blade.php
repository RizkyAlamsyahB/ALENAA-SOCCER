@extends('layouts.app')
@section('content')
    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Kirim Pesan ke Peserta Mabar</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i> Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.mabar.index') }}">Main Bareng</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.mabar.show', $openMabar->id) }}">Detail Mabar</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Kirim Pesan</li>
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
                            <h4 class="card-title mb-4">Form Pengiriman Pesan</h4>

                            <!-- Informasi Event -->
                            <div class="alert alert-info mb-4">
                                <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Informasi Event:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Judul:</strong> {{ $openMabar->title }}</p>
                                        <p class="mb-1"><strong>Tanggal:</strong>
                                            {{ \Carbon\Carbon::parse($openMabar->start_time)->format('d M Y') }}</p>
                                        <p class="mb-0"><strong>Waktu:</strong>
                                            {{ \Carbon\Carbon::parse($openMabar->start_time)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($openMabar->end_time)->format('H:i') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Lapangan:</strong>
                                            {{ $openMabar->fieldBooking->field->name }}</p>
                                        <p class="mb-1"><strong>Jumlah Peserta:</strong>
                                            {{ $openMabar->participants->where('status', '!=', 'cancelled')->count() }}</p>
                                    </div>
                                </div>
                            </div>

                            <p class="text-muted mb-4">
                                Kirim pesan ini kepada semua peserta yang telah bergabung dengan Open Mabar Anda.
                                Pesan akan dikirim melalui email ke masing-masing peserta.
                            </p>

                            <form action="{{ route('user.mabar.broadcast.send', $openMabar->id) }}" method="POST">
                                @csrf

                                <!-- Judul Pesan -->
                                <div class="mb-4">
                                    <label for="subject" class="form-label">Judul Pesan</label>
                                    <input type="text" id="subject" name="subject"
                                        class="form-control @error('subject') is-invalid @enderror"
                                        value="{{ old('subject') }}" required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div class="form-text">
                                        Contoh: "Pengumuman Penting", "Perubahan Jadwal", "Pengingat Event"
                                    </div>
                                </div>

                                <!-- Isi Pesan -->
                                <div class="mb-4">
                                    <label for="message" class="form-label">Isi Pesan</label>
                                    <textarea id="message" name="message" class="form-control @error('message') is-invalid @enderror" rows="6"
                                        required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    <div class="form-text">
                                        Tulis pesan Anda dengan jelas. Informasi tanggal, waktu, dan lokasi event akan
                                        otomatis ditambahkan.
                                    </div>
                                </div>

                                <!-- Penerima Pesan -->
                                <div class="mb-4">
                                    <label class="form-label">Penerima:</label>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>Email</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($openMabar->participants->where('status', '!=', 'cancelled') as $index => $participant)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $participant->user->name }}</td>
                                                        <td>{{ $participant->user->email }}</td>
                                                        <td>
                                                            @if ($participant->status == 'joined')
                                                                <span class="badge bg-primary">Terdaftar</span>
                                                            @elseif($participant->status == 'attended')
                                                                <span class="badge bg-success">Hadir</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">Belum ada peserta untuk event
                                                            ini</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="alert alert-warning mb-4">
                                    <h6 class="mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Perhatian:</h6>
                                    <ul class="mb-0 ps-3">
                                        <li>Pesan akan dikirim ke email semua peserta yang tercantum di atas.</li>
                                        <li>Pastikan pesan Anda informatif dan relevan dengan event.</li>
                                        <li>Peserta dapat membalas email ini langsung ke email Anda.</li>
                                    </ul>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-danger rounded-pill py-3">
                                        <i class="fas fa-paper-plane me-2"></i> Kirim Pesan ke Semua Peserta
                                    </button>
                                    <a href="{{ route('user.mabar.show', $openMabar->id) }}"
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
