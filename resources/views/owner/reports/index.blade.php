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
        <div class="row mb-4">
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

        <!-- Row 2: All Other Revenues (Combined) -->
        <div class="row">
            <!-- Pendapatan Lapangan Bersih -->
            <div class="col-12 col-md-4 col-lg-2-4 mb-4">
                <div class="card h-100">
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

            <!-- Pendapatan Rental Bersih -->
            <div class="col-12 col-md-4 col-lg-2-4 mb-4">
                <div class="card h-100">
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

            <!-- Pendapatan Fotografer Bersih -->
            <div class="col-12 col-md-4 col-lg-2-4 mb-4">
                <div class="card h-100">
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

            <!-- Pendapatan Membership Bersih -->
            <div class="col-12 col-md-6 col-lg-2-4 mb-4">
                <div class="card h-100">
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

            <!-- Pendapatan Penjualan Produk Bersih -->
            <div class="col-12 col-md-6 col-lg-2-4 mb-4">
                <div class="card h-100">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-12 d-flex justify-content-center">
                                <div class="stats-icon black mb-2">
                                    <i class="iconly-boldBag"></i>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <h6 class="text-muted font-semibold">Pendapatan Penjualan Produk Bersih</h6>
                                <h6 class="font-extrabold mb-0">Rp {{ number_format($productSalesNetRevenue, 0, ',', '.') }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Loading State -->
        <div class="row mb-3">
            <div class="col-12">
                <div id="chartLoadingState" class="alert alert-info text-center" style="display: none;">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Memuat data grafik...
                </div>
                <div id="chartErrorState" class="alert alert-danger" style="display: none;">
                    <strong>Error:</strong> <span id="chartErrorMessage"></span>
                </div>
            </div>
        </div>

        <!-- Grafik Utama -->
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Tren Pendapatan Bersih (30 Hari Terakhir)</h4>
                    </div>
                    <div class="card-body">
                        <div id="revenueChartContainer" style="position: relative; height: 400px;">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Distribusi Pendapatan Bersih</h4>
                    </div>
                    <div class="card-body">
                        <div id="distributionChartContainer" style="position: relative; height: 400px;">
                            <canvas id="distributionChart"></canvas>
                        </div>
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
                    </div>
                    <div class="card-body">
                        <div id="fieldTypeChartContainer" style="position: relative; height: 300px;">
                            <canvas id="fieldTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Detail Transaksi -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Detail Transaksi</h4>
                    </div>
                    <div class="card-body">
                        <!-- Filter Form -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label for="service_type" class="form-label">Jenis Layanan</label>
                                <select class="form-select" id="service_type">
                                    <option value="all">Semua Layanan</option>
                                    <option value="field">Lapangan</option>
                                    <option value="rental">Rental</option>
                                    <option value="photographer">Fotografer</option>
                                    <option value="membership">Membership</option>
                                    <option value="product">Produk</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date"
                                       value="2024-01-01">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date"
                                       value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="per_page" class="form-label">Tampilkan</label>
                                <select class="form-select" id="per_page">
                                    <option value="15">15 per halaman</option>
                                    <option value="25">25 per halaman</option>
                                    <option value="50">50 per halaman</option>
                                    <option value="100">100 per halaman</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary" id="filterBtn">
                                    <i class="bi bi-search"></i> Filter Data
                                </button>
                                <button type="button" class="btn btn-success" id="exportBtn">
                                    <i class="bi bi-download"></i> Export CSV
                                </button>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div id="loadingTable" class="text-center py-4" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat data...</p>
                        </div>

                        <!-- Error Display -->
                        <div id="errorDisplay" class="alert alert-danger" style="display: none;">
                            <strong>Error:</strong> <span id="errorMessage"></span>
                        </div>

                        <!-- Summary Cards -->
                        <div id="summaryCards" class="row mb-4" style="display: none;">
                            <div class="col-md-3">
                                <div class="card bg-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Transaksi</h6>
                                        <h5 class="text-dark" id="totalTransactions">-</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Pendapatan Kotor</h6>
                                        <h5 class="text-dark" id="totalOriginal">-</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Diskon</h6>
                                        <h5 class="text-dark" id="totalDiscount">-</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-white">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Pendapatan Bersih</h6>
                                        <h5 class="text-success" id="totalNet">-</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="transactionTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jenis Layanan</th>
                                        <th>Deskripsi</th>
                                        <th>Customer</th>
                                        {{-- <th>Tipe Booking</th> --}}
                                        <th>Harga Asli</th>
                                        <th>Diskon</th>
                                        <th>Harga Bersih</th>
                                        <th>Jadwal</th>
                                    </tr>
                                </thead>
                                <tbody id="transactionTableBody">
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <em>Klik "Filter Data" untuk memuat transaksi</em>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3" style="display: none;">
                            <div id="paginationInfo" class="text-muted"></div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination" id="paginationList">
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Global chart instances
        let revenueChartInstance = null;
        let distributionChartInstance = null;
        let fieldTypeChartInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initializing Reports Dashboard');

            // Load charts with error handling
            loadChartsData();

            // Initialize table functionality
            initializeTableFunctionality();
        });

        /**
         * Load and create all charts
         */
        function loadChartsData() {
            $('#chartLoadingState').show();
            $('#chartErrorState').hide();

            console.log('📊 Loading chart data...');

            fetch('{{ route('owner.reports.dashboard-stats') }}')
                .then(response => {
                    console.log('📡 Chart API Response Status:', response.status);

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Chart Data Received:', data);
                    $('#chartLoadingState').hide();

                    // Create all charts
                    createModernRevenueChart(data);
                    createDistributionChart();
                    createFieldTypeChart(data.field_popularity || []);
                })
                .catch(error => {
                    console.error('❌ Error loading chart data:', error);
                    $('#chartLoadingState').hide();
                    $('#chartErrorMessage').text(`Error loading charts: ${error.message}`);
                    $('#chartErrorState').show();

                    // Create empty charts as fallback
                    createEmptyCharts();
                });
        }

        /**
         * Create Modern Revenue Trend Chart
         */
        function createModernRevenueChart(data) {
            console.log('📈 Creating Revenue Trend Chart');

            try {
                const fieldData = data.field_revenue_trend || [];
                const rentalData = data.rental_revenue_trend || [];
                const photographerData = data.photographer_revenue_trend || [];
                const productData = data.product_revenue_trend || [];

                console.log('📊 Revenue Data:', {
                    field: fieldData.length,
                    rental: rentalData.length,
                    photographer: photographerData.length,
                    product: productData.length
                });

                // Get all unique dates
                const allDates = [...new Set([
                    ...fieldData.map(item => item.date),
                    ...rentalData.map(item => item.date),
                    ...photographerData.map(item => item.date),
                    ...productData.map(item => item.date)
                ])].sort();

                console.log('📅 Date Range:', allDates.length, 'days');

                // Modern color palette
                const colors = {
                    field: {
                        primary: '#3B82F6',
                        bg: 'rgba(59, 130, 246, 0.15)',
                        hover: 'rgba(59, 130, 246, 0.3)'
                    },
                    rental: {
                        primary: '#10B981',
                        bg: 'rgba(16, 185, 129, 0.15)',
                        hover: 'rgba(16, 185, 129, 0.3)'
                    },
                    photographer: {
                        primary: '#EC4899',
                        bg: 'rgba(236, 72, 153, 0.15)',
                        hover: 'rgba(236, 72, 153, 0.3)'
                    },
                    product: {
                        primary: '#F59E0B',
                        bg: 'rgba(245, 158, 11, 0.15)',
                        hover: 'rgba(245, 158, 11, 0.3)'
                    }
                };

                // Create datasets
                const fieldDataset = createModernDataset(allDates, fieldData, 'Lapangan', colors.field);
                const rentalDataset = createModernDataset(allDates, rentalData, 'Rental', colors.rental);
                const photographerDataset = createModernDataset(allDates, photographerData, 'Fotografer', colors.photographer);
                const productDataset = createModernDataset(allDates, productData, 'Produk', colors.product);

                // Format dates for display
                const formattedDates = allDates.map(date => {
                    const d = new Date(date);
                    return d.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'short'
                    });
                });

                const ctx = document.getElementById('revenueChart').getContext('2d');

                // Destroy existing chart
                if (revenueChartInstance) {
                    revenueChartInstance.destroy();
                }

                // Create new chart
                revenueChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: formattedDates,
                        datasets: [fieldDataset, rentalDataset, photographerDataset, productDataset]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        animations: {
                            tension: {
                                duration: 1000,
                                easing: 'easeOutQuart',
                                from: 0.2,
                                to: 0.4,
                                loop: false
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 20,
                                    font: {
                                        family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#1F2937',
                                bodyColor: '#4B5563',
                                borderColor: 'rgba(229, 231, 235, 1)',
                                borderWidth: 1,
                                padding: 12,
                                cornerRadius: 8,
                                titleFont: {
                                    family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                    size: 13
                                },
                                displayColors: true,
                                boxWidth: 8,
                                boxHeight: 8,
                                usePointStyle: true,
                                callbacks: {
                                    title: function(tooltipItems) {
                                        return tooltipItems[0].label;
                                    },
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                        size: 12
                                    },
                                    color: '#6B7280'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    borderDash: [3, 3],
                                    color: 'rgba(229, 231, 235, 0.8)'
                                },
                                ticks: {
                                    font: {
                                        family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                        size: 12
                                    },
                                    color: '#6B7280',
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + ' jt';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + ' rb';
                                        }
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        elements: {
                            point: {
                                radius: 0,
                                hitRadius: 10,
                                hoverRadius: 5
                            },
                            line: {
                                tension: 0.4,
                                borderWidth: 3,
                                fill: true
                            }
                        }
                    }
                });

                console.log('✅ Revenue Chart Created Successfully');

            } catch (error) {
                console.error('❌ Error creating revenue chart:', error);
                showChartError('revenueChart', 'Error creating revenue trend chart');
            }
        }

        /**
         * Create Distribution Chart (Doughnut)
         */
        function createDistributionChart() {
            console.log('🍩 Creating Distribution Chart');

            try {
                const ctx = document.getElementById('distributionChart').getContext('2d');

                // Destroy existing chart
                if (distributionChartInstance) {
                    distributionChartInstance.destroy();
                }

                const data = [
                    {{ $fieldNetRevenue }},
                    {{ $rentalNetRevenue }},
                    {{ $photographerNetRevenue }},
                    {{ $membershipNetRevenue }},
                    {{ $productSalesNetRevenue }}
                ];

                console.log('🍩 Distribution Data:', data);

                const colors = {
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',   // Blue
                        'rgba(16, 185, 129, 0.8)',   // Green
                        'rgba(236, 72, 153, 0.8)',   // Pink
                        'rgba(124, 58, 237, 0.8)',   // Purple
                        'rgba(245, 158, 11, 0.8)'    // Amber
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(124, 58, 237, 1)',
                        'rgba(245, 158, 11, 1)'
                    ]
                };

                distributionChartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Lapangan', 'Rental', 'Fotografer', 'Membership', 'Produk'],
                        datasets: [{
                            data: data,
                            backgroundColor: colors.backgroundColor,
                            borderColor: colors.borderColor,
                            borderWidth: 2,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'right',
                                align: 'center',
                                labels: {
                                    boxWidth: 12,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    padding: 15,
                                    font: {
                                        family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                        size: 12
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#1F2937',
                                bodyColor: '#4B5563',
                                borderColor: 'rgba(229, 231, 235, 1)',
                                borderWidth: 1,
                                padding: 12,
                                cornerRadius: 8,
                                titleFont: {
                                    family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                    size: 13
                                },
                                displayColors: true,
                                boxWidth: 8,
                                boxHeight: 8,
                                usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.raw;
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value) +
                                            ' (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 800,
                            easing: 'easeOutQuart'
                        }
                    }
                });

                console.log('✅ Distribution Chart Created Successfully');

            } catch (error) {
                console.error('❌ Error creating distribution chart:', error);
                showChartError('distributionChart', 'Error creating distribution chart');
            }
        }

        /**
         * Create Field Type Chart (Bar)
         */
        function createFieldTypeChart(data) {
            console.log('📊 Creating Field Type Chart');

            try {
                const ctx = document.getElementById('fieldTypeChart').getContext('2d');

                // Destroy existing chart
                if (fieldTypeChartInstance) {
                    fieldTypeChartInstance.destroy();
                }

                if (!data || !Array.isArray(data) || data.length === 0) {
                    console.warn('⚠️ No field type data available');
                    showChartError('fieldTypeChart', 'Tidak ada data tipe lapangan');
                    return;
                }

                console.log('📊 Field Type Data:', data);

                const labels = data.map(item => item.type);
                const values = data.map(item => item.revenue || 0);

                const baseColor = 'rgba(59, 130, 246, 0.85)';
                const borderColor = 'rgba(59, 130, 246, 1)';

                fieldTypeChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pendapatan Bersih per Tipe Lapangan',
                            data: values,
                            backgroundColor: baseColor,
                            borderColor: borderColor,
                            borderWidth: 1,
                            borderRadius: 6,
                            barThickness: 30,
                            maxBarThickness: 45
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 10,
                                right: 20,
                                bottom: 10,
                                left: 20
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                        size: 12
                                    },
                                    color: '#6B7280'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    borderDash: [3, 3],
                                    color: 'rgba(229, 231, 235, 0.8)'
                                },
                                ticks: {
                                    font: {
                                        family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                        size: 12
                                    },
                                    color: '#6B7280',
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + ' jt';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + ' rb';
                                        }
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#1F2937',
                                bodyColor: '#4B5563',
                                borderColor: 'rgba(229, 231, 235, 1)',
                                borderWidth: 1,
                                padding: 12,
                                cornerRadius: 8,
                                titleFont: {
                                    family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    family: "'Nunito', 'Helvetica', 'Arial', sans-serif",
                                    size: 13
                                },
                                displayColors: false,
                                callbacks: {
                                    title: function(tooltipItems) {
                                        return tooltipItems[0].label;
                                    },
                                    label: function(context) {
                                        return 'Pendapatan Bersih: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 1000,
                            easing: 'easeOutQuart'
                        }
                    }
                });

                console.log('✅ Field Type Chart Created Successfully');

            } catch (error) {
                console.error('❌ Error creating field type chart:', error);
                showChartError('fieldTypeChart', 'Error creating field type chart');
            }
        }

        /**
         * Create modern dataset for line chart
         */
        function createModernDataset(allDates, data, label, colors) {
            const values = allDates.map(date => {
                const match = data.find(item => item.date === date);
                return match ? (match.total || 0) : 0;
            });

            return {
                label: label,
                data: values,
                backgroundColor: colors.bg,
                borderColor: colors.primary,
                pointBackgroundColor: colors.primary,
                pointHoverBackgroundColor: colors.primary,
                pointBorderColor: '#fff',
                pointHoverBorderColor: '#fff',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                hoverBackgroundColor: colors.hover
            };
        }

        /**
         * Show chart error
         */
        function showChartError(chartId, message) {
            const container = document.getElementById(chartId + 'Container') || document.getElementById(chartId).parentElement;
            if (container) {
                container.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center text-muted">
                            <i class="bi bi-exclamation-triangle-fill fs-1 mb-2"></i>
                            <p>${message}</p>
                        </div>
                    </div>
                `;
            }
        }

        /**
         * Create empty charts as fallback
         */
        function createEmptyCharts() {
            showChartError('revenueChart', 'Data tren pendapatan tidak tersedia');
            showChartError('distributionChart', 'Data distribusi tidak tersedia');
            showChartError('fieldTypeChart', 'Data tipe lapangan tidak tersedia');
        }

        /**
         * Initialize table functionality
         */
        function initializeTableFunctionality() {
            let currentPage = 1;

            // Filter button event
            $('#filterBtn').on('click', function() {
                currentPage = 1;
                loadTableData();
            });

            // Export button event
            $('#exportBtn').on('click', function() {
                exportToCSV();
            });

            // Load table data function
            function loadTableData(page = 1) {
                const serviceType = $('#service_type').val();
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                const perPage = $('#per_page').val();

                $('#loadingTable').show();
                $('#summaryCards').hide();
                $('#paginationContainer').hide();
                $('#errorDisplay').hide();

                const params = new URLSearchParams({
                    service_type: serviceType,
                    start_date: startDate,
                    end_date: endDate,
                    per_page: perPage,
                    page: page
                });

                console.log('🔍 Loading table data with params:', Object.fromEntries(params));

                fetch(`{{ route('owner.reports.table-data') }}?${params}`)
                    .then(response => {
                        console.log('📡 Table API Response Status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(result => {
                        console.log('✅ Table API Result:', result);
                        $('#loadingTable').hide();

                        if (result.error) {
                            $('#errorMessage').text(result.message || result.error);
                            $('#errorDisplay').show();
                            return;
                        }

                        populateTable(result.data || []);
                        updateSummary(result.summary || {});
                        updatePagination(result.pagination || {});

                        $('#summaryCards').show();
                        if (result.pagination && result.pagination.total > 0) {
                            $('#paginationContainer').show();
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error loading table data:', error);
                        $('#loadingTable').hide();
                        $('#errorMessage').text(error.message);
                        $('#errorDisplay').show();

                        $('#transactionTableBody').html(`
                            <tr>
                                <td colspan="9" class="text-center text-danger py-4">
                                    <i class="bi bi-exclamation-triangle"></i> Error: ${error.message}
                                </td>
                            </tr>
                        `);
                    });
            }

            // Populate table function
            function populateTable(data) {
                let html = '';

                if (data.length === 0) {
                    html = `
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-inbox"></i> Tidak ada data transaksi untuk filter yang dipilih
                            </td>
                        </tr>
                    `;
                } else {
                    data.forEach(transaction => {
                        const date = new Date(transaction.transaction_date);
                        const formattedDate = date.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });

                        let scheduleInfo = '-';
                        if (transaction.booking_date) {
                            const bookingDate = new Date(transaction.booking_date);
                            const formattedBookingDate = bookingDate.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: '2-digit'
                            });

                            if (transaction.start_time && transaction.end_time) {
                                scheduleInfo = `${formattedBookingDate}<br><small class="text-muted">${transaction.start_time} - ${transaction.end_time}</small>`;
                            } else {
                                scheduleInfo = formattedBookingDate;
                            }
                        }

                        const serviceTypeBadge = getServiceTypeBadge(transaction.service_type);
                        const bookingTypeBadge = getBookingTypeBadge(transaction.booking_type);

                        html += `
                            <tr>
                                <td>${formattedDate}</td>
                                <td>${serviceTypeBadge}</td>
                                <td>
                                    <div class="fw-bold">${transaction.description}</div>
                                </td>
                                <td>${transaction.customer_name || '-'}</td>
                                <td class="text-end">Rp ${numberFormat(transaction.original_amount)}</td>
                                <td class="text-end text-dark">Rp ${numberFormat(transaction.discount_amount)}</td>
                                <td class="text-end fw-bold text-dark">Rp ${numberFormat(transaction.net_amount)}</td>
                                <td>${scheduleInfo}</td>
                            </tr>
                        `;
                    });
                }

                $('#transactionTableBody').html(html);
            }

            // Helper functions
            function getServiceTypeBadge(type) {
                const badges = {
                    'Lapangan': '<span class="badge bg-primary">Lapangan</span>',
                    'Rental': '<span class="badge bg-success">Rental</span>',
                    'Fotografer': '<span class="badge bg-danger">Fotografer</span>',
                    'Membership': '<span class="badge bg-warning">Membership</span>',
                    'Produk': '<span class="badge bg-dark">Produk</span>'
                };
                return badges[type] || '<span class="badge bg-secondary">' + type + '</span>';
            }

            function getBookingTypeBadge(type) {
                if (type === 'Membership') {
                    return '<span class="badge bg-warning text-dark">Membership</span>';
                }
                return '<span class="badge bg-outline-primary">Reguler</span>';
            }

            function numberFormat(number) {
                return new Intl.NumberFormat('id-ID').format(number || 0);
            }

            // Update summary cards
            function updateSummary(summary) {
                $('#totalTransactions').text(numberFormat(summary.total_transactions || 0));
                $('#totalOriginal').text('Rp ' + numberFormat(summary.total_original_amount || 0));
                $('#totalDiscount').text('Rp ' + numberFormat(summary.total_discount_amount || 0));
                $('#totalNet').text('Rp ' + numberFormat(summary.total_net_amount || 0));
            }

            // Update pagination
            function updatePagination(pagination) {
                if (!pagination.total || pagination.total === 0) {
                    $('#paginationContainer').hide();
                    return;
                }

                const info = `Menampilkan ${pagination.from} sampai ${pagination.to} dari ${pagination.total} transaksi`;
                $('#paginationInfo').text(info);

                let paginationHtml = '';

                // Previous button
                if (pagination.current_page > 1) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    `;
                }

                // Page numbers
                const startPage = Math.max(1, pagination.current_page - 2);
                const endPage = Math.min(pagination.last_page, pagination.current_page + 2);

                if (startPage > 1) {
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                    if (startPage > 2) {
                        paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    const activeClass = i === pagination.current_page ? 'active' : '';
                    paginationHtml += `
                        <li class="page-item ${activeClass}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>
                    `;
                }

                if (endPage < pagination.last_page) {
                    if (endPage < pagination.last_page - 1) {
                        paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                    }
                    paginationHtml += `<li class="page-item"><a class="page-link" href="#" data-page="${pagination.last_page}">${pagination.last_page}</a></li>`;
                }

                // Next button
                if (pagination.current_page < pagination.last_page) {
                    paginationHtml += `
                        <li class="page-item">
                            <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    `;
                }

                $('#paginationList').html(paginationHtml);

                // Pagination click events
                $('#paginationList a.page-link').on('click', function(e) {
                    e.preventDefault();
                    const page = $(this).data('page');
                    if (page && page !== pagination.current_page) {
                        currentPage = page;
                        loadTableData(page);
                    }
                });
            }

            // Export to CSV function
            function exportToCSV() {
                const serviceType = $('#service_type').val();
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();

                const params = new URLSearchParams({
                    service_type: serviceType,
                    start_date: startDate,
                    end_date: endDate
                });

                window.open(`{{ route('owner.reports.export') }}?${params}`, '_blank');
            }
        }
    </script>
@endsection
