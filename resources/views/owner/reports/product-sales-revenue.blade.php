@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Pendapatan Penjualan Produk</h3>
                <p class="text-subtitle text-muted">Analisis pendapatan dari penjualan produk berdasarkan kategori dan periode.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.reports.index') }}">Laporan & Statistik</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pendapatan Penjualan Produk</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Filter Tanggal -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Filter Tanggal</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.reports.product-sales-revenue') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Terapkan Filter</button>
                            <a href="{{ route('owner.reports.product-sales-revenue') }}" class="btn btn-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Statistik Utama -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Ringkasan Pendapatan Produk</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card bg-primary">
                                <div class="card-body text-center py-4">
                                    <h6 class="text-white font-semibold">Total Pendapatan Produk</h6>
                                    <h4 class="font-extrabold text-white mb-0">Rp {{ number_format($totalProductNetRevenue, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card bg-primary">
                                <div class="card-body text-center py-4">
                                    <h6 class="text-white font-semibold">Total Kategori Produk</h6>
                                    <h4 class="font-extrabold text-white mb-0">{{ $productRevenueByCategory ? count($productRevenueByCategory) : 0 }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card bg-primary">
                                <div class="card-body text-center py-4">
                                    <h6 class="text-white font-semibold">Total Penjualan Produk</h6>
                                    <h4 class="font-extrabold text-white mb-0">{{ $productRevenueByItem ? $productRevenueByItem->sum('total_quantity') : 0 }}</h4>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Produk Terlaris -->
                    <div class="row mt-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Produk Terlaris</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Produk</th>
                                                    <th>Kategori</th>
                                                    <th class="text-end">Terjual</th>
                                                    <th class="text-end">Pendapatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($topSellingProducts as $index => $product)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $product->name }}</td>
                                                        <td>
                                                            @if($product->category == 'food')
                                                                <span class="badge bg-success">Makanan</span>
                                                            @elseif($product->category == 'beverage')
                                                                <span class="badge bg-info">Minuman</span>
                                                            @elseif($product->category == 'equipment')
                                                                <span class="badge bg-warning">Peralatan</span>
                                                            @else
                                                                <span class="badge bg-secondary">{{ ucfirst($product->category) }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($product->total_quantity, 0, ',', '.') }}</td>
                                                        <td class="text-end">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">Tidak ada data produk terlaris</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Distribusi Kategori</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="categoryPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tren Pendapatan -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tren Pendapatan Penjualan Produk</h4>
                </div>
                <div class="card-body">
                    <canvas id="productRevenueChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue by Category Table -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>Pendapatan Berdasarkan Kategori Produk</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="categoryTable">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-end">Total Kuantitas</th>
                                    <th class="text-end">Jumlah Penjualan</th>
                                    <th class="text-end">Pendapatan Bersih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productRevenueByCategory as $category)
                                    <tr>
                                        <td>
                                            @if($category->category == 'food')
                                                <span class="badge bg-success me-1"><i class="bi bi-egg-fried"></i></span>
                                                Makanan
                                            @elseif($category->category == 'beverage')
                                                <span class="badge bg-info me-1"><i class="bi bi-cup-straw"></i></span>
                                                Minuman
                                            @elseif($category->category == 'equipment')
                                                <span class="badge bg-warning me-1"><i class="bi bi-tools"></i></span>
                                                Peralatan
                                            @else
                                                <span class="badge bg-secondary me-1"><i class="bi bi-box"></i></span>
                                                {{ ucfirst($category->category) }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($category->total_quantity, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($category->sale_count, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($category->revenue, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data kategori produk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-active fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">{{ number_format($productRevenueByCategory->sum('total_quantity'), 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($productRevenueByCategory->sum('sale_count'), 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($productRevenueByCategory->sum('revenue'), 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Product Table -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Detail Pendapatan Per Produk</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="productTable">
                            <thead>
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th class="text-end">Total Kuantitas</th>
                                    <th class="text-end">Jumlah Penjualan</th>
                                    <th class="text-end">Pendapatan Bersih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productRevenueByItem as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>
                                            @if($product->category == 'food')
                                                <span class="badge bg-success">Makanan</span>
                                            @elseif($product->category == 'beverage')
                                                <span class="badge bg-info">Minuman</span>
                                            @elseif($product->category == 'equipment')
                                                <span class="badge bg-warning">Peralatan</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($product->category) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($product->total_quantity, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($product->sale_count, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format($product->revenue, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data produk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-active fw-bold">
                                    <td colspan="2">Total</td>
                                    <td class="text-end">{{ number_format($productRevenueByItem->sum('total_quantity'), 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($productRevenueByItem->sum('sale_count'), 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($productRevenueByItem->sum('revenue'), 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Perbandingan Pendapatan Per Hari -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Pendapatan Produk Per Hari</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="dailyRevenueTable">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-end">Jumlah Penjualan</th>
                                    <th class="text-end">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productRevenueByDay as $day)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                                    <td class="text-end">{{ number_format($day->sale_count, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($day->revenue, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active fw-bold">
                                    <td>Total</td>
                                    <td class="text-end">{{ number_format($productRevenueByDay->sum('sale_count'), 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($productRevenueByDay->sum('revenue'), 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- Scripts untuk chart -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTables with improved styling
            $('#categoryTable').DataTable({
                paging: false,
                searching: false,
                info: false
            });

            $('#productTable').DataTable({
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                order: [[4, 'desc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                },
                responsive: true
            });

            $('#dailyRevenueTable').DataTable({
                order: [[0, 'desc']], // Sort by date descending by default
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
                },
                responsive: true
            });

            // Modern Doughnut Chart untuk distribusi kategori
            createModernCategoryChart();

            // Modern Line Chart untuk tren pendapatan
            createModernRevenueChart();
        });

        function createModernCategoryChart() {
            // Hapus instance chart lama jika ada
            if (window.categoryChartInstance) {
                window.categoryChartInstance.destroy();
            }

            const ctx = document.getElementById('categoryPieChart').getContext('2d');

            // Siapkan labels dan data
            const categoryLabels = [
                @foreach ($productRevenueByCategory as $category)
                    @if($category->category == 'food')
                        "Makanan",
                    @elseif($category->category == 'beverage')
                        "Minuman",
                    @elseif($category->category == 'equipment')
                        "Peralatan",
                    @else
                        "{{ ucfirst($category->category) }}",
                    @endif
                @endforeach
            ];

            const categoryData = [
                @foreach ($productRevenueByCategory as $category)
                    {{ $category->revenue }},
                @endforeach
            ];

            // Modern color palette
            const colors = {
                backgroundColor: [
                    'rgba(249, 115, 22, 0.85)',  // orange-500
                    'rgba(16, 185, 129, 0.85)',  // emerald-500
                    'rgba(59, 130, 246, 0.85)',  // blue-500
                    'rgba(236, 72, 153, 0.85)',  // pink-500
                    'rgba(139, 92, 246, 0.85)',  // purple-500
                    'rgba(245, 158, 11, 0.85)'   // amber-500
                ],
                borderColor: [
                    'rgba(249, 115, 22, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(59, 130, 246, 1)',
                    'rgba(236, 72, 153, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(245, 158, 11, 1)'
                ],
                hoverBackgroundColor: [
                    'rgba(249, 115, 22, 0.95)',
                    'rgba(16, 185, 129, 0.95)',
                    'rgba(59, 130, 246, 0.95)',
                    'rgba(236, 72, 153, 0.95)',
                    'rgba(139, 92, 246, 0.95)',
                    'rgba(245, 158, 11, 0.95)'
                ]
            };

            // Calculate total revenue for center text
            const totalRevenue = categoryData.reduce((a, b) => a + b, 0);

            // Custom center text plugin
            const centerTextPlugin = {
                id: 'centerText',
                beforeDraw: function(chart) {
                    if (chart.config.type !== 'doughnut') return;

                    const width = chart.width;
                    const height = chart.height;
                    const ctx = chart.ctx;

                    ctx.restore();

                    // Text styles
                    ctx.font = "14px 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
                    ctx.textBaseline = 'middle';
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#6B7280'; // gray-500



                    ctx.save();
                }
            };

            // Create doughnut chart
            window.categoryChartInstance = new Chart(ctx, {
                type: 'doughnut',
                plugins: [centerTextPlugin],
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryData,
                        backgroundColor: colors.backgroundColor.slice(0, categoryLabels.length),
                        borderColor: colors.borderColor.slice(0, categoryLabels.length),
                        hoverBackgroundColor: colors.hoverBackgroundColor.slice(0, categoryLabels.length),
                        borderWidth: 2,
                        hoverOffset: 15,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '60%',
                    layout: {
                        padding: 20
                    },
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 15,
                                color: '#4B5563', // gray-600
                                font: {
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Pendapatan per Kategori',
                            color: '#1F2937', // gray-800
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: {
                                top: 10,
                                bottom: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1F2937', // gray-800
                            bodyColor: '#4B5563', // gray-600
                            borderColor: 'rgba(229, 231, 235, 1)', // gray-200
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: true,
                            boxWidth: 8,
                            boxHeight: 8,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
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
        }

        function createModernRevenueChart() {
            // Hapus instance chart lama jika ada
            if (window.revenueChartInstance) {
                window.revenueChartInstance.destroy();
            }

            const ctx = document.getElementById('productRevenueChart').getContext('2d');

            const days = [
                @foreach ($productRevenueByDay as $day)
                    "{{ \Carbon\Carbon::parse($day->date)->format('d M') }}",
                @endforeach
            ];

            const revenueData = [
                @foreach ($productRevenueByDay as $day)
                    {{ $day->revenue }},
                @endforeach
            ];

            const salesData = [
                @foreach ($productRevenueByDay as $day)
                    {{ $day->sale_count }},
                @endforeach
            ];

            // Create gradients for fills
            const revenueGradient = ctx.createLinearGradient(0, 0, 0, 400);
            revenueGradient.addColorStop(0, 'rgba(249, 115, 22, 0.25)'); // orange-500 with opacity
            revenueGradient.addColorStop(1, 'rgba(249, 115, 22, 0.02)'); // orange-500 very transparent

            window.revenueChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: days,
                    datasets: [
                        {
                            label: 'Pendapatan Produk',
                            data: revenueData,
                            borderColor: 'rgba(249, 115, 22, 1)', // orange-500
                            backgroundColor: revenueGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: 'rgba(249, 115, 22, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 0, // Hide points by default
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: 'rgba(249, 115, 22, 1)',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                            order: 1
                        },
                        {
                            label: 'Jumlah Penjualan',
                            data: salesData,
                            borderColor: 'rgba(59, 130, 246, 1)', // blue-500
                            backgroundColor: 'transparent',
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 0, // Hide points by default
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: 'rgba(59, 130, 246, 1)',
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                            yAxisID: 'y1',
                            order: 0
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    layout: {
                        padding: {
                            top: 10,
                            right: 25,
                            bottom: 10,
                            left: 10
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6B7280', // gray-500
                                font: {
                                    size: 12
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            grid: {
                                borderDash: [3, 3],
                                color: 'rgba(229, 231, 235, 0.7)' // gray-200 with transparency
                            },
                            title: {
                                display: true,
                                text: 'Pendapatan (Rp)',
                                color: '#F97316', // orange-500
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                color: '#6B7280', // gray-500
                                callback: function(value) {
                                    if (value >= 1000000) {
                                        return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + ' jt';
                                    } else if (value >= 1000) {
                                        return 'Rp ' + (value / 1000).toLocaleString('id-ID') + ' rb';
                                    }
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                },
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Penjualan',
                                color: '#3B82F6', // blue-500
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                stepSize: 1,
                                color: '#6B7280', // gray-500
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 20,
                                color: '#4B5563', // gray-600
                                font: {
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Tren Pendapatan Penjualan Produk',
                            color: '#1F2937', // gray-800
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: {
                                top: 10,
                                bottom: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1F2937', // gray-800
                            bodyColor: '#4B5563', // gray-600
                            borderColor: 'rgba(229, 231, 235, 1)', // gray-200
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: true,
                            boxWidth: 8,
                            boxHeight: 8,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.dataset.yAxisID === 'y1') {
                                        label += context.raw;
                                    } else {
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                    return label;
                                }
                            }
                        }
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
                    elements: {
                        line: {
                            tension: 0.4
                        }
                    }
                }
            });
        }
    </script>
@endsection
