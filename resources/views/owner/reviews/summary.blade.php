@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Ringkasan Review</h3>
                <p class="text-subtitle text-muted">Analisis dan statistik dari semua review pelanggan.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.reviews.index') }}">Data Review</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ringkasan Review</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
<!-- Statistik Utama -->
<div class="row">
    <div class="col-12 col-md-3">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="stats-icon purple mb-2">
                            <i class="iconly-boldChat"></i>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <h6 class="text-muted font-semibold">Total Review</h6>
                        <h6 class="font-extrabold mb-0">{{ $totalReviews }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="stats-icon green mb-2">
                            <i class="iconly-boldTick-Square"></i>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <h6 class="text-muted font-semibold">Review Aktif</h6>
                        <h6 class="font-extrabold mb-0">{{ $activeReviews }}
                            ({{ $totalReviews > 0 ? round(($activeReviews / $totalReviews) * 100) : 0 }}%)</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="stats-icon blue mb-2">
                            <i class="iconly-boldStar"></i>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <h6 class="text-muted font-semibold">Rating Rata-rata</h6>
                        <h6 class="font-extrabold mb-0">
                            {{ number_format($avgRating, 1) }}
                            <span class="text-warning">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= round($avgRating))
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </span>
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <div class="stats-icon red mb-2">
                            <i class="iconly-boldHeart"></i>
                        </div>
                    </div>
                    <div class="col-12 text-center">
                        <h6 class="text-muted font-semibold">Kepuasan Pelanggan</h6>
                        <h6 class="font-extrabold mb-0">
                            {{ $totalReviews > 0 ? number_format((($ratingDistribution[4] + $ratingDistribution[5]) / $totalReviews) * 100, 1) : 0 }}%
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


        <div class="row">
            <!-- Grafik Distribusi Rating -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Distribusi Rating</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="ratingDistribution"></canvas>
                    </div>
                </div>
            </div>

            <!-- Grafik Rating per Kategori -->
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Rating per Kategori</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryRating"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Terbaru -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Review Terbaru</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Pengguna</th>
                                        <th>Rating</th>
                                        <th>Komentar</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($latestReviews as $review)
                                        <tr>
                                            <td>{{ $review->user ? $review->user->name : 'User tidak ditemukan' }}</td>
                                            <td>
                                                <div class="rating-stars">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= $review->rating)
                                                            <i class="bi bi-star-fill text-warning"></i>
                                                        @else
                                                            <i class="bi bi-star text-secondary"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </td>
                                            <td>{{ \Illuminate\Support\Str::limit($review->comment, 100) ?? 'Tidak ada komentar' }}
                                            </td>
                                            <td>{{ $review->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('owner.reviews.show', $review->id) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada review</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data untuk Distribusi Rating
            var ratingLabels = ['1 Star', '2 Stars', '3 Stars', '4 Stars', '5 Stars'];
            var ratingData = [
                {{ $ratingDistribution[1] ?? 0 }},
                {{ $ratingDistribution[2] ?? 0 }},
                {{ $ratingDistribution[3] ?? 0 }},
                {{ $ratingDistribution[4] ?? 0 }},
                {{ $ratingDistribution[5] ?? 0 }}
            ];

            var ratingDistributionCtx = document.getElementById('ratingDistribution').getContext('2d');
            var ratingDistributionChart = new Chart(ratingDistributionCtx, {
                type: 'bar',
                data: {
                    labels: ratingLabels,
                    datasets: [{
                        label: 'Jumlah Review',
                        data: ratingData,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(255, 205, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(54, 162, 235, 0.7)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(255, 159, 64)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(54, 162, 235)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Rating dari Pelanggan'
                        }
                    }
                }
            });

            // Data untuk Rating per Kategori
            var categoryLabels = ['Lapangan', 'Penyewaan', 'Fotografer'];
            var categoryData = [
                {{ $fieldRatings ?? 0 }},
                {{ $rentalRatings ?? 0 }},
                {{ $photographerRatings ?? 0 }}
            ];

            var categoryRatingCtx = document.getElementById('categoryRating').getContext('2d');
            var categoryRatingChart = new Chart(categoryRatingCtx, {
                type: 'radar',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        label: 'Rating Rata-rata',
                        data: categoryData,
                        fill: true,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgb(54, 162, 235)',
                        pointBackgroundColor: 'rgb(54, 162, 235)',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: 'rgb(54, 162, 235)'
                    }]
                },
                options: {
                    responsive: true,
                    elements: {
                        line: {
                            borderWidth: 3
                        }
                    },
                    scales: {
                        r: {
                            angleLines: {
                                display: true
                            },
                            suggestedMin: 0,
                            suggestedMax: 5
                        }
                    }
                }
            });
        });
    </script>
@endsection
