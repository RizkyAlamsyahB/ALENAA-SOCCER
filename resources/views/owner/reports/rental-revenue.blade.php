@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Laporan Pendapatan Bersih Rental</h3>
                <p class="text-subtitle text-muted">Analisis pendapatan bersih dari penyewaan peralatan.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.reports.index') }}">Laporan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pendapatan Bersih Rental</li>
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
                    <form action="{{ route('owner.reports.rental-revenue') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Ringkasan Pendapatan Rental -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Ringkasan Pendapatan Bersih Rental</h4>
                </div>
                <div class="card-body">
                    <!-- Tambahkan informasi tentang membership di bagian atas -->
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong> Laporan pendapatan ini hanya menampilkan pendapatan bersih (setelah
                        diskon) dari transaksi langsung (non-membership).
                        Pendapatan dari penjualan paket membership dicatat terpisah.
                    </div>
                    <!-- Pada bagian Ringkasan Pendapatan Rental, tambahkan informasi penggunaan membership -->
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card  shadow border">
                                <div class="card-body text-center py-4">
                                    <h5 class="mb-2">Total Pendapatan Bersih Rental</h5>
                                    <h2 class="text-success mb-0">Rp
                                        {{ number_format($totalRentalNetRevenue, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card  shadow border">
                                <div class="card-body text-center py-4">
                                    <h5 class="mb-2">Penggunaan Membership</h5>
                                    <h2 class="text-info mb-0">{{ number_format($membershipRentalCount, 0) }}</h2>
                                    <p class="text-muted small mt-2">*Tidak termasuk dalam perhitungan pendapatan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card  shadow border">
                                <div class="card-body text-center py-4">
                                    <h5 class="mb-2">Total Booking</h5>
                                    <h2 class="text-info mb-0">
                                        {{ number_format($rentalRevenueByItem->sum('booking_count') + $membershipRentalCount, 0) }}
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Tren Pendapatan Harian -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tren Pendapatan Bersih Rental Harian</h4>
                </div>
                <div class="card-body">
                    <canvas id="rentalRevenueByDayChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pendapatan Per Item -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Pendapatan Bersih Per Item Rental</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="rentalItemsTable">
                            <thead>
                                <tr>
                                    <th>Nama Item</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Jumlah Booking</th>
                                    <th class="text-center">Total Kuantitas</th>
                                    <th class="text-end">Pendapatan Bersih</th>
                                    <th class="text-end">Persentase Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rentalRevenueByItem as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->category }}</td>
                                        <td class="text-center">{{ $item->booking_count }}</td>
                                        <td class="text-center">{{ $item->total_quantity }}</td>
                                        <td class="text-end">Rp {{ number_format($item->revenue, 0, ',', '.') }}</td>
                                        <td class="text-end">
                                            @if ($totalRentalNetRevenue > 0)
                                                {{ number_format(($item->revenue / $totalRentalNetRevenue) * 100, 1) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Kategori Item -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Pendapatan Bersih per Kategori Item</h4>
                </div>
                <div class="card-body">
                    <canvas id="itemCategoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Top 5 Items -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Top 5 Item Rental (Pendapatan Bersih)</h4>
                </div>
                <div class="card-body">
                    <canvas id="topItemsChart"></canvas>
                </div>
            </div>
        </div>


    </div>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable with improved styling
            $('#rentalItemsTable').DataTable({
                order: [[4, 'desc']], // Sort by revenue descending by default
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
                },
                responsive: true,
                pagingType: 'simple_numbers',
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
            });

            // Item Category Chart - Modern Doughnut
            const categoryData = @json($rentalRevenueByItem->groupBy('category')->map(function ($group) {
                    return [
                        'category' => $group->first()->category,
                        'revenue' => $group->sum('revenue'),
                    ];
                })->values());

            createModernDoughnutChart('itemCategoryChart',
                categoryData.map(item => item.category),
                categoryData.map(item => item.revenue),
                'Pendapatan Bersih per Kategori Item'
            );

            // Top 5 Items Chart - Modern Bar Chart
            const topItems = @json($rentalRevenueByItem->sortByDesc('revenue')->take(5)->values());
            createModernBarChart('topItemsChart',
                topItems.map(item => item.name),
                topItems.map(item => item.revenue),
                'Top 5 Item Rental (Pendapatan Bersih)'
            );

            // Rental Revenue by Day Chart - Modern Line Chart
            createModernLineChart('rentalRevenueByDayChart', @json($rentalRevenueByDay));
        });

        function createModernDoughnutChart(canvasId, labels, data, title) {
            // Hapus instance chart lama jika ada
            if (window[canvasId + 'Instance']) {
                window[canvasId + 'Instance'].destroy();
            }

            const ctx = document.getElementById(canvasId).getContext('2d');

            // Modern color palette - lebih cerah dan beragam
            const colors = {
                backgroundColor: [
                    'rgba(59, 130, 246, 0.85)',  // blue-500
                    'rgba(16, 185, 129, 0.85)',  // emerald-500
                    'rgba(245, 158, 11, 0.85)',  // amber-500
                    'rgba(236, 72, 153, 0.85)',  // pink-500
                    'rgba(139, 92, 246, 0.85)',  // purple-500
                    'rgba(14, 165, 233, 0.85)',  // sky-500
                    'rgba(249, 115, 22, 0.85)',  // orange-500
                    'rgba(239, 68, 68, 0.85)'    // red-500
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(236, 72, 153, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(14, 165, 233, 1)',
                    'rgba(249, 115, 22, 1)',
                    'rgba(239, 68, 68, 1)'
                ]
            };

            // Calculate total revenue for center text
            const totalRevenue = data.reduce((a, b) => a + b, 0);

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

                    // Title text
                    ctx.fillText('Total', width / 2, height / 2 - 15);

                    // Amount text
                    ctx.font = "bold 16px 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
                    ctx.fillStyle = '#111827'; // gray-900
                    const formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalRevenue);
                    ctx.fillText(formattedTotal, width / 2, height / 2 + 10);

                    ctx.save();
                }
            };

            // Create chart instance
            window[canvasId + 'Instance'] = new Chart(ctx, {
                type: 'doughnut',
                plugins: [centerTextPlugin],
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors.backgroundColor.slice(0, labels.length),
                        borderColor: colors.borderColor.slice(0, labels.length),
                        borderWidth: 2,
                        hoverOffset: 10,
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
                            position: 'bottom',
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
                            text: title,
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

        function createModernBarChart(canvasId, labels, data, title) {
            // Hapus instance chart lama jika ada
            if (window[canvasId + 'Instance']) {
                window[canvasId + 'Instance'].destroy();
            }

            const ctx = document.getElementById(canvasId).getContext('2d');

            // Gradien untuk latar belakang bars
            const gradients = [];

            // Warna utama untuk bars - tealish color palette
            const baseColors = [
                {
                    start: 'rgba(20, 184, 166, 0.9)', // teal-500
                    end: 'rgba(20, 184, 166, 0.3)',   // teal-500 with lower opacity
                    border: 'rgba(20, 184, 166, 1)'   // teal-500 solid
                },
                {
                    start: 'rgba(6, 182, 212, 0.9)',  // cyan-500
                    end: 'rgba(6, 182, 212, 0.3)',    // cyan-500 with lower opacity
                    border: 'rgba(6, 182, 212, 1)'    // cyan-500 solid
                },
                {
                    start: 'rgba(45, 212, 191, 0.9)', // teal-400
                    end: 'rgba(45, 212, 191, 0.3)',   // teal-400 with lower opacity
                    border: 'rgba(45, 212, 191, 1)'   // teal-400 solid
                },
                {
                    start: 'rgba(94, 234, 212, 0.9)', // teal-300
                    end: 'rgba(94, 234, 212, 0.3)',   // teal-300 with lower opacity
                    border: 'rgba(94, 234, 212, 1)'   // teal-300 solid
                },
                {
                    start: 'rgba(8, 145, 178, 0.9)',  // cyan-600
                    end: 'rgba(8, 145, 178, 0.3)',    // cyan-600 with lower opacity
                    border: 'rgba(8, 145, 178, 1)'    // cyan-600 solid
                }
            ];

            // Create gradients for each bar
            for (let i = 0; i < labels.length; i++) {
                const colorSet = baseColors[i % baseColors.length];
                const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, colorSet.start);
                gradient.addColorStop(1, colorSet.end);
                gradients.push(gradient);
            }

            // Create border colors array
            const borderColors = labels.map((_, i) => baseColors[i % baseColors.length].border);

            window[canvasId + 'Instance'] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan Bersih',
                        data: data,
                        backgroundColor: gradients,
                        borderColor: borderColors,
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 40,
                        maxBarThickness: 60
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    layout: {
                        padding: {
                            top: 10,
                            right: 20,
                            bottom: 10,
                            left: 20
                        }
                    },
                    indexAxis: 'y', // Horizontal bar chart for better readability with long item names
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [3, 3],
                                color: 'rgba(229, 231, 235, 0.8)' // gray-200
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
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#4B5563' // gray-600
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: title,
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
                            displayColors: false,
                            callbacks: {
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
        }

        function createModernLineChart(canvasId, rentalData) {
            // Hapus instance chart lama jika ada
            if (window[canvasId + 'Instance']) {
                window[canvasId + 'Instance'].destroy();
            }

            const ctx = document.getElementById(canvasId).getContext('2d');

            // Buat array tanggal lengkap dari startDate hingga endDate
            const startDate = new Date('{{ $startDate }}');
            const endDate = new Date('{{ $endDate }}');
            const dateRange = [];

            // Isi semua tanggal dalam range
            for (let dt = new Date(startDate); dt <= endDate; dt.setDate(dt.getDate() + 1)) {
                dateRange.push(new Date(dt).toISOString().split('T')[0]); // Format 'YYYY-MM-DD'
            }

            // Buat dataset dengan nilai 0 untuk tanggal yang tidak ada transaksi
            const completeData = dateRange.map(dateString => {
                const existingData = rentalData.find(item => item.date === dateString);
                return {
                    date: dateString,
                    revenue: existingData ? parseFloat(existingData.revenue) : 0
                };
            });

            // Format labels (tanggal)
            const labels = completeData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                });
            });

            // Data pendapatan
            const revenueData = completeData.map(item => item.revenue);

            // Membuat gradient untuk area di bawah line
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(5, 150, 105, 0.2)'); // emerald-600 dengan opacity rendah
            gradient.addColorStop(1, 'rgba(5, 150, 105, 0.02)'); // emerald-600 dengan opacity sangat rendah

            window[canvasId + 'Instance'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan Bersih Harian',
                        data: revenueData,
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: 'rgba(5, 150, 105, 1)', // emerald-600
                        borderWidth: 3,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(5, 150, 105, 1)', // emerald-600
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 0, // Hide points by default
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: 'rgba(5, 150, 105, 1)', // emerald-600
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2
                    }]
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
                            right: 20,
                            bottom: 10,
                            left: 10
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [3, 3],
                                color: 'rgba(229, 231, 235, 0.8)' // gray-200
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
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#6B7280', // gray-500
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Tren Pendapatan Bersih Rental Harian',
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
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Pendapatan Bersih: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
