@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Laporan & Statistik</h3>
                <p class="text-subtitle text-muted">Kelola dan analisa data pendapatan bisnis Anda.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan & Statistik</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Row 1: Total Pendapatan Bersih -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-2 d-flex justify-content-center">
                                <div class="stats-icon purple mb-2">
                                    <i class="iconly-boldWallet"></i>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <h6 class="text-muted font-semibold">Total Pendapatan Bersih</h6>
                                <h6 class="font-extrabold mb-0">Rp {{ number_format($totalNetRevenue, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Semua Pendapatan Lainnya -->
        <div class="row">
            <div class="col-12 col-md-3">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-center">
                                <div class="stats-icon blue mb-2">
                                    <i class="iconly-boldTicket"></i>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <h6 class="text-muted font-semibold">Pendapatan Lapangan Bersih</h6>
                                <h6 class="font-extrabold mb-0">Rp {{ number_format($fieldNetRevenue, 0, ',', '.') }}</h6>
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
                                    <i class="iconly-boldBag"></i>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <h6 class="text-muted font-semibold">Pendapatan Rental Bersih</h6>
                                <h6 class="font-extrabold mb-0">Rp {{ number_format($rentalNetRevenue, 0, ',', '.') }}</h6>
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
                                    <i class="iconly-boldCamera"></i>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <h6 class="text-muted font-semibold">Pendapatan Fotografer Bersih</h6>
                                <h6 class="font-extrabold mb-0">Rp {{ number_format($photographerNetRevenue, 0, ',', '.') }}
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
                                <div class="stats-icon purple mb-2">
                                    <i class="iconly-boldProfile"></i>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <h6 class="text-muted font-semibold">Pendapatan Membership Bersih</h6>
                                <h6 class="font-extrabold mb-0">Rp {{ number_format($membershipNetRevenue, 0, ',', '.') }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Alternatif 1: Desain Card Modern dengan Shadow dan Hover Effect -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Pilih Jenis Laporan</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 col-sm-6 mb-4">
                                <a href="{{ route('owner.reports.revenue') }}" class="text-decoration-none">
                                    <div class="card report-card shadow-sm h-100 transition-hover border-shadow">
                                        <div class="card-body d-flex flex-column align-items-center py-4">
                                            <div class="report-icon  mb-3 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-graph-up text-primary" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="fw-bold">Laporan Pendapatan</h5>
                                            <p class="text-muted text-center">Ringkasan semua pendapatan berdasarkan periode
                                                (lapangan, rental, fotografer).</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-4">
                                <a href="{{ route('owner.reports.field-revenue') }}" class="text-decoration-none">
                                    <div class="card report-card shadow-sm h-100 transition-hover border-shadow">
                                        <div class="card-body d-flex flex-column align-items-center py-4">
                                            <div class="report-icon  mb-3 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-calendar-week text-success" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="fw-bold">Pendapatan Lapangan</h5>
                                            <p class="text-muted text-center">Analisis pendapatan dari penyewaan lapangan
                                                berdasarkan tipe dan periode.</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-4">
                                <a href="{{ route('owner.reports.rental-revenue') }}" class="text-decoration-none">
                                    <div class="card report-card shadow-sm h-100 transition-hover border-shadow">
                                        <div class="card-body d-flex flex-column align-items-center py-4">
                                            <div class="report-icon  mb-3 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-box-seam text-info" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="fw-bold">Pendapatan Rental</h5>
                                            <p class="text-muted text-center">Analisis pendapatan dari penyewaan peralatan
                                                berdasarkan item dan periode.</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-4">
                                <a href="{{ route('owner.reports.photographer-revenue') }}" class="text-decoration-none">
                                    <div class="card report-card shadow-sm h-100 transition-hover border-shadow">
                                        <div class="card-body d-flex flex-column align-items-center py-4">
                                            <div
                                                class="report-icon  mb-3 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-camera text-warning" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="fw-bold">Pendapatan Fotografer</h5>
                                            <p class="text-muted text-center">Analisis pendapatan dari layanan fotografer
                                                berdasarkan paket dan periode.</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-4">
                                <a href="{{ route('owner.reports.membership-revenue') }}" class="text-decoration-none">
                                    <div class="card report-card shadow-sm h-100 transition-hover border-shadow">
                                        <div class="card-body d-flex flex-column align-items-center py-4">
                                            <div
                                                class="report-icon  mb-3 d-flex align-items-center justify-content-center">
                                                <i class="bi bi-person-badge text-danger" style="font-size: 2rem;"></i>
                                            </div>
                                            <h5 class="fw-bold">Pendapatan Membership</h5>
                                            <p class="text-muted text-center">Analisis pendapatan dari layanan membership
                                                berdasarkan tipe dan periode.</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CSS tambahan untuk Alternatif 1 -->
        <style>
            .report-card {
                border-radius: 10px;
                border: 1px solid rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .report-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
                border-color: rgba(0, 0, 0, 0);
            }

            .report-icon {
                width: 80px;
                height: 80px;
            }

            .bg-primary-light {
                background-color: rgba(54, 162, 235, 0.1);
            }

            .bg-success-light {
                background-color: rgba(40, 167, 69, 0.1);
            }

            .bg-info-light {
                background-color: rgba(23, 162, 184, 0.1);
            }

            .bg-warning-light {
                background-color: rgba(255, 193, 7, 0.1);
            }

            .bg-danger-light {
                background-color: rgba(220, 53, 69, 0.1);
            }
        </style>

        <!-- Grafik Utama -->
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Tren Pendapatan Bersih (30 Hari Terakhir)</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Distribusi Pendapatan Bersih</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Field Revenue -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Pendapatan Bersih per Tipe Lapangan</h4>
                        <a href="{{ route('owner.reports.field-revenue') }}" class="btn btn-sm btn-primary">Lihat
                            Detail</a>
                    </div>
                    <div class="card-body">
                        <canvas id="fieldTypeChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch data
            fetch('{{ route('owner.reports.dashboard-stats') }}')
                .then(response => response.json())
                .then(data => {
                    createRevenueChart(data);
                    createDistributionChart();
                    createFieldTypeChart(data.field_popularity);
                })
                .catch(error => console.error('Error loading chart data:', error));

            // Create Revenue Chart
            function createRevenueChart(data) {
                const fieldData = data.field_revenue_trend || [];
                const rentalData = data.rental_revenue_trend || [];
                const photographerData = data.photographer_revenue_trend || [];

                // Get all unique dates
                const allDates = [...new Set([
                    ...fieldData.map(item => item.date),
                    ...rentalData.map(item => item.date),
                    ...photographerData.map(item => item.date)
                ])].sort();

                // Create datasets
                const fieldDataset = createDataset(allDates, fieldData, 'Lapangan', 'rgba(54, 162, 235, 0.7)',
                    'rgba(54, 162, 235, 1)');
                const rentalDataset = createDataset(allDates, rentalData, 'Rental', 'rgba(75, 192, 192, 0.7)',
                    'rgba(75, 192, 192, 1)');
                const photographerDataset = createDataset(allDates, photographerData, 'Fotografer',
                    'rgba(255, 99, 132, 0.7)', 'rgba(255, 99, 132, 1)');

                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: allDates.map(date => {
                            const d = new Date(date);
                            return d.toLocaleDateString('id-ID', {
                                day: 'numeric',
                                month: 'short'
                            });
                        }),
                        datasets: [fieldDataset, rentalDataset, photographerDataset]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': Rp ' + new Intl.NumberFormat(
                                            'id-ID').format(context.raw);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function createDataset(allDates, data, label, backgroundColor, borderColor) {
                const values = allDates.map(date => {
                    const match = data.find(item => item.date === date);
                    return match ? match.total : 0;
                });

                return {
                    label: label,
                    data: values,
                    fill: false,
                    backgroundColor: backgroundColor,
                    borderColor: borderColor,
                    tension: 0.4
                };
            }

            // Create Distribution Chart
            function createDistributionChart() {
                const ctx = document.getElementById('distributionChart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Lapangan', 'Rental', 'Fotografer'],
                        datasets: [{
                            data: [
                                {{ $fieldNetRevenue }},
                                {{ $rentalNetRevenue }},
                                {{ $photographerNetRevenue }}
                            ],
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(255, 99, 132, 0.7)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.raw;
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return context.label + ': Rp ' + new Intl.NumberFormat('id-ID')
                                            .format(value) +
                                            ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Create Field Type Chart
            function createFieldTypeChart(data) {
                if (!data || data.length === 0) return;

                const labels = data.map(item => item.type);
                const values = data.map(item => item.revenue);

                const ctx = document.getElementById('fieldTypeChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pendapatan Bersih per Tipe Lapangan',
                            data: values,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(153, 102, 255, 0.7)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(153, 102, 255, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Pendapatan Bersih: Rp ' + new Intl.NumberFormat('id-ID')
                                            .format(context.raw);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
