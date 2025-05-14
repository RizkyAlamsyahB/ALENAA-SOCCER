@extends('layouts.app')

@section('content')
    <!-- Hero Section with Breadcrumb -->
    <div class="hero-section" style="margin-top:50px">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Detail Membership</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i> Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('user.membership.my-memberships') }}">Membership
                                    Saya</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Detail Membership</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container content-wrapper mt-4">
        <!-- Alerts Section -->
        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                <div class="alert-content">
                    <div class="alert-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="alert-message">
                        {{ session('error') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                <div class="alert-content">
                    <div class="alert-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="alert-message">
                        {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4" style="margin-bottom: 50px">
            <!-- Membership Overview Card -->
            <div class="col-lg-8">
                <div class="card membership-card">
                    <div class="card-body">
                        <div class="membership-header">
                            <div class="membership-info">
                                <h2 class="membership-name">{{ $subscription->membership->name }}</h2>
                                <div class="membership-details">
                                    <div
                                        class="membership-badge
                                        @if ($subscription->status === 'active') badge-active
                                        @elseif($subscription->status === 'expired') badge-expired
                                        @else badge-pending @endif">
                                        <i class="fas fa-circle"></i>
                                        <span>{{ ucfirst($subscription->status) }}</span>
                                    </div>

                                    <div
                                        class="membership-type
                                        @if ($subscription->membership->type === 'bronze') badge-bronze
                                        @elseif($subscription->membership->type === 'silver') badge-silver
                                        @else badge-gold @endif">
                                        <i class="fas fa-crown"></i>
                                        <span>{{ ucfirst($subscription->membership->type ?? 'Standard') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="membership-date">
                                <div class="date-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <div class="date-range">
                                        <span class="date-label">Periode Membership</span>
                                        <span
                                            class="date-value">{{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }}
                                            - {{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="membership-grid">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="info-content">
                                    <h4>Lokasi Lapangan</h4>
                                    <p>{{ $subscription->membership->field->name ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-calendar-week"></i>
                                </div>
                                <div class="info-content">
                                    <h4>Sesi Per Minggu</h4>
                                    <p>{{ $subscription->membership->sessions_per_week ?? 3 }} sesi</p>
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                                <div class="info-content">
                                    <h4>Durasi Sesi</h4>
                                    <p>{{ $subscription->membership->session_duration ?? 1 }} jam</p>
                                </div>
                            </div>

                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div class="info-content">
                                    <h4>Status Perpanjangan</h4>
                                    <p>{{ ucfirst($subscription->renewal_status) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="price-summary-section">
                            <div class="price-label">Biaya Membership</div>
                            <div class="price-amount">Rp {{ number_format($subscription->price, 0, ',', '.') }} <span
                                    class="price-period">per minggu</span></div>
                        </div>
                    </div>
                </div>

                <!-- Session Schedule Card -->
                <div class="card session-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-calendar-check"></i>
                            Jadwal Sesi
                        </h3>
                    </div>
                    <div class="card-body">
                        @if ($subscription->sessions && $subscription->sessions->count() > 0)
                            <div class="table-responsive">
                                <table class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th>Sesi</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            // Sort sessions by date first, then by time
                                            $sortedSessions = $subscription->sessions->sortBy([['start_time', 'asc']]);
                                        @endphp

                                        @foreach ($sortedSessions as $session)
                                            <tr class="session-row">
                                                <td class="session-number">{{ $session->session_number }}</td>
                                                <td>
                                                    <div class="session-date">
                                                        <div class="day-name">
                                                            {{ \Carbon\Carbon::parse($session->start_time)->format('l') }}
                                                        </div>
                                                        <div class="date">
                                                            {{ \Carbon\Carbon::parse($session->start_time)->format('d M Y') }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="session-time">
                                                        <i class="far fa-clock"></i>
                                                        <span>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }}
                                                            -
                                                            {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="status-badge
@if ($session->status === 'completed') badge-completed
    @elseif($session->status === 'cancelled') badge-cancelled
    @elseif($session->status === 'upcoming') badge-upcoming
    @elseif($session->status === 'ongoing') badge-ongoing
    @else badge-scheduled @endif">
                                                        @if ($session->status === 'completed')
                                                            <i class="fas fa-check-circle"></i>
                                                        @elseif($session->status === 'cancelled')
                                                            <i class="fas fa-times-circle"></i>
                                                        @elseif($session->status === 'upcoming')
                                                            <i class="fas fa-hourglass-start"></i>
                                                        @elseif($session->status === 'ongoing')
                                                            <i class="fas fa-play-circle"></i>
                                                        @else
                                                            <i class="far fa-calendar-alt"></i>
                                                        @endif

                                                        <span>{{ ucfirst($session->status) }}</span>
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="far fa-calendar-times"></i>
                                </div>
                                <h4>Belum Ada Jadwal</h4>
                                <p>Belum ada jadwal sesi untuk membership ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <!-- Membership Actions Card -->
                <div class="card action-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-cogs"></i>
                            Manajemen Membership
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="membership-status-info">
                            <div
                                class="status-icon
                                @if ($subscription->status === 'active') active
                                @elseif($subscription->status === 'expired') expired
                                @else pending @endif">
                                @if ($subscription->status === 'active')
                                    <i class="fas fa-check-circle"></i>
                                @elseif($subscription->status === 'expired')
                                    <i class="fas fa-times-circle"></i>
                                @else
                                    <i class="fas fa-clock"></i>
                                @endif
                            </div>
                            <div class="status-details">
                                <h4>Status: {{ ucfirst($subscription->status) }}</h4>
                                <p>
                                    @if ($subscription->status === 'active')
                                        Membership Anda sedang aktif dan akan berakhir pada
                                        {{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}.
                                    @elseif($subscription->status === 'expired')
                                        Membership Anda telah berakhir. Silakan perpanjang untuk terus menikmati fasilitas.
                                    @else
                                        Membership Anda dalam status menunggu.
                                    @endif
                                </p>
                            </div>
                        </div>



                        <div class="support-contact">
                            <div class="support-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="support-text">
                                <p>Butuh bantuan? Hubungi kami di</p>
                                <a href="tel:+6285123456789">085123456789</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Sessions Card -->
                <div class="card upcoming-card">
                    <div class="card-header">
                        <h3>
                            <i class="fas fa-calendar-day"></i>
                            Sesi Mendatang
                        </h3>
                    </div>
                    <div class="card-body">
                        @php
                            // Menggunakan scope upcoming atau filter manual
                            $upcomingSessions = $subscription
                                ->sessions()
                                ->whereIn('status', ['scheduled', 'ongoing'])
                                ->where('end_time', '>', now()) // Menyaring yang masih relevan (belum selesai)
                                ->orderBy('start_time', 'asc')
                                ->take(3)
                                ->get();

                        @endphp

                        @if ($upcomingSessions->count() > 0)
                            @foreach ($upcomingSessions as $session)
                                <div class="upcoming-session">
                                    <div class="session-day">
                                        <div class="day">{{ \Carbon\Carbon::parse($session->start_time)->format('d') }}
                                        </div>
                                        <div class="month">{{ \Carbon\Carbon::parse($session->start_time)->format('M') }}
                                        </div>
                                    </div>
                                    <div class="session-details">
                                        <div class="time">
                                            <i class="far fa-clock"></i>
                                            <span>{{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}</span>
                                        </div>
                                        <div class="weekday">
                                            {{ \Carbon\Carbon::parse($session->start_time)->format('l') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-upcoming">
                                <div class="empty-icon">
                                    <i class="far fa-calendar"></i>
                                </div>
                                <p>Tidak ada sesi mendatang.</p>
                            </div>
                        @endif
                    </div>
                </div>
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
            padding: 3.5rem 0;
            text-align: center;
            color: white;
            position: relative;
            margin-bottom: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
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

        /* Content Wrapper */
        .content-wrapper {
            margin-top: -2rem;
            position: relative;
            z-index: 1;
            padding-bottom: 3rem;
        }

        /* Alert Styling */
        .alert {
            border: none;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .alert-content {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
        }

        .alert-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .alert-message {
            font-weight: 500;
            flex-grow: 1;
        }

        .alert-success {
            background-color: #e8f5e9;
        }

        .alert-success .alert-icon {
            background-color: #4caf50;
            color: white;
        }

        .alert-danger {
            background-color: #ffebee;
        }

        .alert-danger .alert-icon {
            background-color: #f44336;
            color: white;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #6c757d;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-close:hover {
            color: #212529;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: white;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: white;
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #212529;
            display: flex;
            align-items: center;
        }

        .card-header h3 i {
            margin-right: 0.75rem;
            color: #d00f25;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Membership Card */
        .membership-card {
            background: white;
            border-radius: 16px;
        }

        .membership-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .membership-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #212529;
        }

        .membership-details {
            display: flex;
            gap: 0.75rem;
        }

        .membership-badge,
        .membership-type {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .badge-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .badge-expired {
            background-color: #ffebee;
            color: #c62828;
        }

        .badge-pending {
            background-color: #fff8e1;
            color: #f57f17;
        }

        .badge-bronze {
            background-color: rgba(205, 127, 50, 0.1);
            color: #CD7F32;
        }

        .badge-silver {
            background-color: rgba(192, 192, 192, 0.1);
            color: #808080;
        }

        .badge-gold {
            background-color: rgba(255, 215, 0, 0.1);
            color: #DAA520;
        }

        .membership-date {
            margin-top: 0.5rem;
        }

        .date-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0;
        }

        .date-item i {
            color: #d00f25;
            font-size: 1.25rem;
        }

        .date-range {
            display: flex;
            flex-direction: column;
        }

        .date-label {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .date-value {
            font-weight: 600;
            color: #212529;
        }

        /* Membership Grid */
        .membership-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .info-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            border-radius: 12px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-3px);
        }

        .info-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: rgba(208, 15, 37, 0.1);
            color: #d00f25;
            flex-shrink: 0;
        }

        .info-content h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #212529;
        }

        .info-content p {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        /* Price Summary */
        .price-summary-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            background: rgba(208, 15, 37, 0.03);
            border-radius: 12px;
            border-left: 4px solid #d00f25;
        }

        .price-label {
            font-weight: 600;
            color: #212529;
            font-size: 1.1rem;
        }

        .price-amount {
            font-weight: 700;
            color: #d00f25;
            font-size: 1.25rem;
        }

        .price-period {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 400;
        }

        /* Session Table */
        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        .custom-table th {
            border: none;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
        }

        .session-row {
            border-radius: 10px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
        }

        .session-row:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            background-color: white;
        }

        .session-row td {
            padding: 0.75rem 1rem;
            border: none;
            vertical-align: middle;
        }

        .session-row td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        .session-row td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .session-number {
            width: 50px;
            height: 50px;
            background-color: rgba(208, 15, 37, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #d00f25;
            font-size: 1.25rem;
        }

        .session-date {
            display: flex;
            flex-direction: column;
        }

        .day-name {
            font-weight: 600;
            color: #212529;
        }

        .date {
            color: #6c757d;
            font-size: 0.85rem;
        }

        .session-time {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #495057;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .badge-completed {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .badge-cancelled {
            background-color: #ffebee;
            color: #c62828;
        }

        .badge-upcoming {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .badge-scheduled {
            background-color: #f5f5f5;
            color: #616161;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2rem;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            background-color: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: #6c757d;
        }

        .empty-state h4 {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #212529;
        }

        .empty-state p {
            color: #6c757d;
            margin-bottom: 0;
        }

        /* Action Card */
        .action-card {
            background: white;
            border-radius: 16px;
        }

        .membership-status-info {
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
            padding: 1.25rem;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }

        .status-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .status-icon.active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-icon.expired {
            background: #ffebee;
            color: #c62828;
        }

        .status-icon.pending {
            background: #fff8e1;
            color: #f57f17;
        }

        .status-details h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #212529;
        }

        .status-details p {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0;
            line-height: 1.5;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-action.renew {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid rgba(46, 125, 50, 0.2);
        }

        .btn-action.renew:hover {
            background: #c8e6c9;
            box-shadow: 0 5px 15px rgba(46, 125, 50, 0.15);
            transform: translateY(-3px);
        }

        .btn-action.reactive {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid rgba(21, 101, 192, 0.2);
        }

        .btn-action.reactive:hover {
            background: #bbdefb;
            box-shadow: 0 5px 15px rgba(21, 101, 192, 0.15);
            transform: translateY(-3px);
        }

        .btn-action.cancel {
            background: #f5f5f5;
            color: #616161;
            border: 1px solid rgba(97, 97, 97, 0.2);
        }

        .btn-action.cancel:hover {
            background: #e0e0e0;
            color: #d32f2f;
            border-color: rgba(211, 47, 47, 0.2);
            box-shadow: 0 5px 15px rgba(97, 97, 97, 0.15);
            transform: translateY(-3px);
        }

        /* Support Contact */
        .support-contact {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .support-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: #e9ecef;
            color: #d00f25;
            flex-shrink: 0;
        }

        .support-text {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .support-text p {
            margin-bottom: 0.25rem;
        }

        .support-text a {
            color: #d00f25;
            font-weight: 600;
            text-decoration: none;
        }

        /* Upcoming Sessions Card */
        .upcoming-card {
            background: white;
            border-radius: 16px;
        }

        .upcoming-session {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            border-radius: 12px;
            background: #f8f9fa;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .upcoming-session:last-child {
            margin-bottom: 0;
        }

        .upcoming-session:hover {
            background: white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-3px);
        }

        .session-day {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            background: white;
            border: 1px solid rgba(208, 15, 37, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .session-day .day {
            font-size: 1.25rem;
            font-weight: 700;
            color: #d00f25;
            line-height: 1;
        }

        .session-day .month {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .session-details {
            flex-grow: 1;
        }

        .session-details .time {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #212529;
            margin-bottom: 0.25rem;
        }

        .session-details .weekday {
            color: #6c757d;
            font-size: 0.85rem;
        }

        .empty-upcoming {
            text-align: center;
            padding: 1.5rem;
        }

        .empty-upcoming .empty-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .empty-upcoming p {
            color: #6c757d;
            margin-bottom: 0;
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .membership-header {
                flex-direction: column;
                gap: 1.5rem;
            }

            .membership-date {
                width: 100%;
            }

            .membership-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 2.5rem 0;
            }

            .hero-title {
                font-size: 1.75rem;
            }

            .breadcrumb {
                padding: 0.6rem 1rem;
            }

            .breadcrumb-item {
                font-size: 0.8rem;
            }

            .content-wrapper {
                margin-top: -1rem;
            }

            .membership-grid {
                grid-template-columns: 1fr;
            }

            .price-summary-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .session-row td {
                padding: 0.6rem;
            }

            .session-number {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .status-badge {
                padding: 0.35rem 0.75rem;
                font-size: 0.8rem;
            }

            .membership-status-info {
                flex-direction: column;
                align-items: center;
                text-align: center;
                padding: 1rem;
            }

            .custom-table {
                display: block;
                width: 100%;
                overflow-x: auto;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.5rem;
            }

            .card-header {
                padding: 1.25rem;
            }

            .card-body {
                padding: 1.25rem;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

@endsection
