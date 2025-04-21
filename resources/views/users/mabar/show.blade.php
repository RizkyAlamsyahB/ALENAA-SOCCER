@extends('layouts.app')
@section('content')
    <!-- Hero Section with Image -->
    <div class="hero-section-detail" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">{{ $openMabar->title }}</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i> Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.mabar.index') }}">Main Bareng</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content bg-light">
        <div class="container py-5">
            <div class="row g-4">
                <!-- Left Column: Mabar Details -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
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

                            <!-- Status Bar -->
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="status-badge me-3">
                                        @if ($openMabar->status == 'open')
                                            <span class="badge bg-success rounded-pill px-3 py-2">Open</span>
                                        @elseif($openMabar->status == 'full')
                                            <span class="badge bg-danger rounded-pill px-3 py-2">Full</span>
                                        @elseif($openMabar->status == 'cancelled')
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">Cancelled</span>
                                        @elseif($openMabar->status == 'completed')
                                            <span class="badge bg-info rounded-pill px-3 py-2">Completed</span>
                                        @endif
                                    </div>
                                    <div class="level-info">
                                        <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                            Level: {{ ucfirst($openMabar->level) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="participants-info">
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $openMabar->filled_slots }}/{{ $openMabar->total_slots }} Peserta
                                    </span>
                                </div>
                            </div>

                            <!-- Details Card -->
                            <div class="mabar-details">
                                <h4 class="mb-4">Informasi Event</h4>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="detail-item d-flex mb-3">
                                            <div class="icon me-3">
                                                <i class="fas fa-calendar-alt text-danger"></i>
                                            </div>
                                            <div class="content">
                                                <h6 class="mb-1">Tanggal & Waktu</h6>
                                                <p class="mb-0">
                                                    {{ \Carbon\Carbon::parse($openMabar->start_time)->format('D, d M Y') }}
                                                </p>
                                                <p class="mb-0">
                                                    {{ \Carbon\Carbon::parse($openMabar->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($openMabar->end_time)->format('H:i') }} WIB
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item d-flex mb-3">
                                            <div class="icon me-3">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                            </div>
                                            <div class="content">
                                                <h6 class="mb-1">Lokasi</h6>
                                                <p class="mb-0">{{ $openMabar->fieldBooking->field->name }}</p>
                                                <p class="mb-0 text-muted">{{ $openMabar->fieldBooking->field->type }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="detail-item d-flex mb-3">
                                            <div class="icon me-3">
                                                <i class="fas fa-user text-danger"></i>
                                            </div>
                                            <div class="content">
                                                <h6 class="mb-1">Dibuat Oleh</h6>
                                                <p class="mb-0">{{ $openMabar->user->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item d-flex mb-3">
                                            <div class="icon me-3">
                                                <i class="fas fa-money-bill-wave text-danger"></i>
                                            </div>
                                            <div class="content">
                                                <h6 class="mb-1">Harga per Orang</h6>
                                                <p class="mb-0 fw-bold">
                                                    Rp{{ number_format($openMabar->price_per_slot, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="description-section mb-4">
                                    <h5 class="mb-3">Deskripsi</h5>
                                    <div class="description-content p-3 bg-light rounded-3">
                                        {{ $openMabar->description ?? 'Tidak ada deskripsi' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Participants List Section -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <!-- Di show.blade.php -->
                            @php
                                $activeParticipants = $openMabar->participants->where('status', '!=', 'cancelled');
                                $participantCount = $activeParticipants->count();
                            @endphp

                            <h4 class="mb-4">Daftar Peserta ({{ $participantCount }}/{{ $openMabar->total_slots }})</h4>
                            @if (Auth::id() == $openMabar->user_id)
                                <div class="mt-3">
                                    <a href="{{ route('user.mabar.broadcast.form', $openMabar->id) }}"
                                        class="btn btn-primary w-100">
                                        <i class="fas fa-bullhorn me-2"></i> Kirim Pesan ke Semua Peserta
                                    </a>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th>Nama</th>
                                            <th width="20%">Status</th>
                                            <th width="20%">Pembayaran</th>
                                            @if (Auth::id() == $openMabar->user_id)
                                                <th width="15%">Kontak</th>
                                                <th width="15%">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($openMabar->participants->where('status', '!=', 'cancelled') as $index => $participant)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar me-2">
                                                            @if ($participant->user->profile_picture)
                                                                <img src="{{ Storage::url($participant->user->profile_picture) }}"
                                                                    alt="{{ $participant->user->name }}" width="32"
                                                                    height="32" class="rounded-circle">
                                                            @else
                                                                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center"
                                                                    style="width: 32px; height: 32px; font-size: 14px;">
                                                                    {{ substr($participant->user->name, 0, 1) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            {{ $participant->user->name }}
                                                            @if ($participant->user_id == $openMabar->user_id)
                                                                <span class="badge bg-danger text-white ms-1">Host</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($participant->status == 'joined')
                                                        <span class="badge bg-primary">Terdaftar</span>
                                                    @elseif($participant->status == 'attended')
                                                        <span class="badge bg-success">Hadir</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($participant->payment_status == 'pending')
                                                        <span class="badge bg-warning text-dark">Belum Dibayar</span>
                                                    @elseif($participant->payment_status == 'paid')
                                                        <span class="badge bg-success">Lunas</span>
                                                    @endif
                                                </td>
                                                @if (Auth::id() == $openMabar->user_id && $participant->user_id != $openMabar->user_id)
                                                    <td>
                                                        @if ($participant->user->phone_number)
                                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $participant->user->phone_number) }}"
                                                                class="btn btn-sm btn-success" target="_blank">
                                                                <i class="fab fa-whatsapp"></i> WhatsApp
                                                            </a>
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if ($participant->status == 'joined' && $participant->payment_status == 'pending')
                                                            <form
                                                                action="{{ route('user.mabar.mark.attended', ['mabarId' => $openMabar->id, 'participantId' => $participant->id]) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success">
                                                                    <i class="fas fa-check me-1"></i> Hadir & Bayar
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="{{ Auth::id() == $openMabar->user_id ? '5' : '4' }}"
                                                    class="text-center py-4">
                                                    <div class="empty-state">
                                                        <i class="fas fa-users text-muted fa-2x mb-3"></i>
                                                        <p class="mb-0">Belum ada peserta yang bergabung</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Action Card -->
                <div class="col-lg-4">
                    <!-- Join/Cancel Button Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 sticky-md-top" style="top: 100px;">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Informasi Bergabung</h5>

                            <div
                                class="price-section d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3">
                                <div>
                                    <span class="text-muted">Harga per orang</span>
                                    <h4 class="mb-0 text-danger">
                                        Rp{{ number_format($openMabar->price_per_slot, 0, ',', '.') }}</h4>
                                </div>
                                <div>
                                    <span class="text-muted">Slot tersisa</span>
                                    <h4 class="mb-0">{{ $openMabar->total_slots - $openMabar->filled_slots }}</h4>
                                </div>
                            </div>

                            <div class="progress mb-4" style="height: 10px;">
                                <div class="progress-bar bg-danger" role="progressbar"
                                    style="width: {{ $openMabar->filledPercentage() }}%;"
                                    aria-valuenow="{{ $openMabar->filledPercentage() }}" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>

                            <div class="action-button mb-3">
                                @if (now() > $openMabar->end_time)
                                    <button class="btn btn-secondary w-100 rounded-pill py-3" disabled>
                                        <i class="fas fa-hourglass-end me-2"></i> Event Telah Berakhir
                                    </button>
                                @elseif(Auth::id() == $openMabar->user_id)
                                    <p class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle me-2"></i> Anda adalah pembuat event ini
                                    </p>
                                    <!-- Tambahkan tombol untuk manage participant jika diperlukan -->
                                @else
                                    @php
                                        // Cek status keikutsertaan saat ini
                                        $participant = $openMabar->participants
                                            ->where('user_id', Auth::id())
                                            ->where('status', '!=', 'cancelled')
                                            ->first();
                                        $isActivelyJoined = !is_null($participant);
                                    @endphp

                                    @if ($isActivelyJoined)
                                        <p class="alert alert-success mb-3">
                                            <i class="fas fa-check-circle me-2"></i> Anda telah bergabung dengan event ini
                                        </p>

                                        <form action="{{ route('user.mabar.cancel', $openMabar->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-3">
                                                <i class="fas fa-times-circle me-2"></i> Batalkan Keikutsertaan
                                            </button>
                                        </form>
                                    @elseif($openMabar->status == 'full')
                                        <button class="btn btn-secondary w-100 rounded-pill py-3" disabled>
                                            <i class="fas fa-users-slash me-2"></i> Slot Sudah Penuh
                                        </button>
                                    @else
                                        <form action="{{ route('user.mabar.join', $openMabar->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger w-100 rounded-pill py-3">
                                                <i class="fas fa-user-plus me-2"></i> Gabung Sekarang
                                            </button>
                                        </form>

                                        <div class="mt-3 text-center">
                                            <small class="text-muted">Pembayaran dilakukan secara langsung (cash)</small>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="notes mt-4">
                                <h6 class="mb-2">Catatan Penting:</h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="d-flex mb-2">
                                        <i class="fas fa-info-circle text-primary me-2 mt-1"></i>
                                        <span>Pembayaran dilakukan secara langsung di lokasi</span>
                                    </li>
                                    <li class="d-flex mb-2">
                                        <i class="fas fa-info-circle text-primary me-2 mt-1"></i>
                                        <span>Pastikan hadir tepat waktu sesuai jadwal</span>
                                    </li>
                                    <li class="d-flex mb-2">
                                        <i class="fas fa-info-circle text-primary me-2 mt-1"></i>
                                        <span>Bawa perlengkapan olahraga seperlunya</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Hero Section */
        .hero-section-detail {
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

        /* Icon Styling */
        .detail-item .icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(208, 15, 37, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        /* Progress Bar */
        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
        }

        /* Empty State */
        .empty-state {
            padding: 20px;
            text-align: center;
            color: #6c757d;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
