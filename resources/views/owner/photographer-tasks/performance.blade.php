@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Performa Fotografer</h3>
                <p class="text-subtitle text-muted">Analisis kinerja dan produktivitas fotografer dalam {{ $period }} hari terakhir.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.photographer-tasks.index') }}">Tugas Fotografer</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Performa</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Period Filter -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Filter Periode</h5>
                        <form method="GET" action="{{ route('owner.photographer-tasks.performance') }}">
                            <div class="form-group">
                                <label for="period">Periode Analisis</label>
                                <select name="period" id="period" class="form-select" onchange="this.form.submit()">
                                    <option value="7" {{ $period == '7' ? 'selected' : '' }}>7 Hari Terakhir</option>
                                    <option value="30" {{ $period == '30' ? 'selected' : '' }}>30 Hari Terakhir</option>
                                    <option value="60" {{ $period == '60' ? 'selected' : '' }}>60 Hari Terakhir</option>
                                    <option value="90" {{ $period == '90' ? 'selected' : '' }}>90 Hari Terakhir</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card card-stats">
                            <div class="card-body text-center">
                                <i class="bi bi-people text-primary" style="font-size: 2rem;"></i>
                                <h3 class="mt-2">{{ $performance->count() }}</h3>
                                <p class="text-muted">Total Fotografer</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stats">
                            <div class="card-body text-center">
                                <i class="bi bi-camera text-info" style="font-size: 2rem;"></i>
                                <h3 class="mt-2">{{ $performance->sum('total_bookings') }}</h3>
                                <p class="text-muted">Total Booking</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stats">
                            <div class="card-body text-center">
                                <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                                <h3 class="mt-2">{{ $performance->sum('completed_tasks') }}</h3>
                                <p class="text-muted">Selesai</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-stats">
                            <div class="card-body text-center">
                                <i class="bi bi-clock text-danger" style="font-size: 2rem;"></i>
                                <h3 class="mt-2">{{ $performance->sum('overdue_tasks') }}</h3>
                                <p class="text-muted">Terlambat</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Table -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Performa Individual Fotografer</h4>
                    <div>
                        <a href="{{ route('owner.photographer-tasks.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="performanceTable">
                        <thead>
                            <tr>
                                <th>Fotografer</th>
                                <th>Total Booking</th>
                                <th>Selesai</th>
                                <th>Tingkat Penyelesaian</th>
                                <th>Tugas Terlambat</th>
                                <th>Rata-rata Waktu Delivery</th>
                                <th>Rating Performa</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($performance as $photographer)
                                @php
                                    $completionRate = $photographer->total_bookings > 0 ?
                                        ($photographer->completed_tasks / $photographer->total_bookings) * 100 : 0;

                                    $overdueRate = $photographer->completed_tasks > 0 ?
                                        ($photographer->overdue_tasks / $photographer->completed_tasks) * 100 : 0;

                                    // Calculate performance rating
                                    $performanceScore = 0;
                                    if ($completionRate >= 90) $performanceScore += 30;
                                    elseif ($completionRate >= 70) $performanceScore += 20;
                                    elseif ($completionRate >= 50) $performanceScore += 10;

                                    if ($overdueRate <= 5) $performanceScore += 30;
                                    elseif ($overdueRate <= 15) $performanceScore += 20;
                                    elseif ($overdueRate <= 25) $performanceScore += 10;

                                    if ($photographer->avg_delivery_days <= 2) $performanceScore += 25;
                                    elseif ($photographer->avg_delivery_days <= 3) $performanceScore += 20;
                                    elseif ($photographer->avg_delivery_days <= 5) $performanceScore += 15;
                                    elseif ($photographer->avg_delivery_days <= 7) $performanceScore += 10;

                                    if ($photographer->total_bookings >= 10) $performanceScore += 15;
                                    elseif ($photographer->total_bookings >= 5) $performanceScore += 10;
                                    elseif ($photographer->total_bookings >= 1) $performanceScore += 5;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <img src="{{ $photographer->user->profile_photo_url ?? asset('assets/images/faces/1.jpg') }}" alt="Avatar">
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $photographer->user->name }}</h6>
                                                <small class="text-muted">{{ $photographer->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $photographer->total_bookings }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $photographer->completed_tasks }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar
                                                @if($completionRate >= 80) bg-success
                                                @elseif($completionRate >= 60) bg-warning
                                                @else bg-danger
                                                @endif"
                                                style="width: {{ $completionRate }}%">
                                                {{ number_format($completionRate, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($photographer->overdue_tasks > 0)
                                            <span class="badge bg-danger">{{ $photographer->overdue_tasks }}</span>
                                            <small class="text-muted">({{ number_format($overdueRate, 1) }}%)</small>
                                        @else
                                            <span class="badge bg-success">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($photographer->avg_delivery_days)
                                            <span class="badge
                                                @if($photographer->avg_delivery_days <= 2) bg-success
                                                @elseif($photographer->avg_delivery_days <= 5) bg-warning
                                                @else bg-danger
                                                @endif">
                                                {{ number_format($photographer->avg_delivery_days, 1) }} hari
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 8px;">
                                                <div class="progress-bar
                                                    @if($performanceScore >= 80) bg-success
                                                    @elseif($performanceScore >= 60) bg-warning
                                                    @else bg-danger
                                                    @endif"
                                                    style="width: {{ $performanceScore }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $performanceScore }}/100</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($performanceScore >= 80)
                                            <span class="badge bg-success">Excellent</span>
                                        @elseif($performanceScore >= 60)
                                            <span class="badge bg-warning">Good</span>
                                        @elseif($performanceScore >= 40)
                                            <span class="badge bg-info">Average</span>
                                        @else
                                            <span class="badge bg-danger">Needs Improvement</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-camera" style="font-size: 3rem; color: #ddd;"></i>
                                            <h5 class="mt-3 text-muted">Tidak ada data performa</h5>
                                            <p class="text-muted">Belum ada fotografer dengan booking dalam periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Performance Insights -->
        @if($performance->count() > 0)
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">🏆 Top Performers</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $topPerformers = $performance->sortByDesc(function($p) {
                                return ($p->total_bookings > 0 ? ($p->completed_tasks / $p->total_bookings) * 100 : 0);
                            })->take(3);
                        @endphp
                        @foreach($topPerformers as $index => $photographer)
                            @php
                                $rate = $photographer->total_bookings > 0 ? ($photographer->completed_tasks / $photographer->total_bookings) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge bg-warning me-2">#{{ $index + 1 }}</div>
                                <div class="avatar avatar-sm me-2">
                                    <img src="{{ $photographer->user->profile_photo_url ?? asset('assets/images/faces/1.jpg') }}" alt="Avatar">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $photographer->user->name }}</h6>
                                    <small class="text-muted">{{ number_format($rate, 1) }}% completion rate</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">⚠️ Needs Attention</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $needsAttention = $performance->filter(function($p) {
                                $overdueRate = $p->completed_tasks > 0 ? ($p->overdue_tasks / $p->completed_tasks) * 100 : 0;
                                return $overdueRate > 20 || $p->avg_delivery_days > 5;
                            })->take(3);
                        @endphp
                        @if($needsAttention->count() > 0)
                            @foreach($needsAttention as $photographer)
                                @php
                                    $overdueRate = $photographer->completed_tasks > 0 ? ($photographer->overdue_tasks / $photographer->completed_tasks) * 100 : 0;
                                @endphp
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-sm me-2">
                                        <img src="{{ $photographer->user->profile_photo_url ?? asset('assets/images/faces/1.jpg') }}" alt="Avatar">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $photographer->user->name }}</h6>
                                        <small class="text-danger">
                                            @if($overdueRate > 20)
                                                {{ number_format($overdueRate, 1) }}% overdue rate
                                            @endif
                                            @if($photographer->avg_delivery_days > 5)
                                                {{ number_format($photographer->avg_delivery_days, 1) }} days avg delivery
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">Semua fotografer bekerja dengan baik! 🎉</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#performanceTable').DataTable({
                responsive: true,
                order: [[6, 'desc']], // Sort by performance score
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                columnDefs: [
                    { orderable: false, targets: [0] } // Disable sorting for photographer column with avatar
                ]
            });
        });
    </script>
@endsection
