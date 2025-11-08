@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Laporan Pendapatan Bersih Fotografer</h3>
                <p class="text-subtitle text-muted">Analisis pendapatan bersih dari layanan fotografer.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.reports.index') }}">Laporan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pendapatan Bersih Fotografer</li>
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
                    <form action="{{ route('owner.reports.photographer-revenue') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Ringkasan Pendapatan Fotografer -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Ringkasan Pendapatan Bersih Fotografer</h4>
                </div>
                <div class="card-body">
                    <!-- Tambahkan informasi tentang membership di bagian atas -->
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong> Laporan pendapatan ini hanya menampilkan pendapatan bersih (setelah diskon) dari transaksi langsung (non-membership).
                        Pendapatan dari penjualan paket membership dicatat terpisah.
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card  shadow border">
                                <div class="card-body text-center py-4">
                                    <h5 class="mb-2">Total Pendapatan Bersih Fotografer</h5>
                                    <h2 class="text-success mb-0">Rp {{ number_format($totalPhotographerNetRevenue, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card  shadow border">
                                <div class="card-body text-center py-4">
                                    <h5 class="mb-2">Penggunaan Membership</h5>
                                    <h2 class="text-info mb-0">{{ number_format($membershipPhotographerCount, 0) }}</h2>
                                    <p class="text-muted small mt-2">*Tidak termasuk dalam perhitungan pendapatan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card  shadow border">
                                <div class="card-body text-center py-4">
                                    <h5 class="mb-2">Total Booking</h5>
                                    <h2 class="text-info mb-0">{{ number_format($photographerRevenue->sum('booking_count') + $membershipPhotographerCount, 0) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pendapatan Per Fotografer -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>Pendapatan Bersih Per Fotografer</h4>
                    <span class="badge bg-light-info">*Hanya menampilkan pendapatan bersih non-membership</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="photographerTable">
                            <thead>
                                <tr>
                                    <th>Nama Fotografer</th>
                                    <th>Tipe Paket</th>
                                    <th class="text-center">Jumlah Booking</th>
                                    <th class="text-end">Pendapatan Bersih</th>
                                    <th class="text-end">Persentase Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($photographerRevenue as $photographer)
                                <tr>
                                    <td>{{ $photographer->name }}</td>
                                    <td>{{ $photographer->package_type }}</td>
                                    <td class="text-center">{{ $photographer->booking_count }}</td>
                                    <td class="text-end">Rp {{ number_format($photographer->revenue, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        @if($totalPhotographerNetRevenue > 0)
                                            {{ number_format(($photographer->revenue / $totalPhotographerNetRevenue) * 100, 1) }}%
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

        <!-- Grafik Per Fotografer -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Pendapatan Bersih per Fotografer</h4>
                </div>
                <div class="card-body">
                    <canvas id="photographerChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Per Paket -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Pendapatan Bersih per Tipe Paket</h4>
                </div>
                <div class="card-body">
                    <canvas id="packageTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tren Pendapatan Harian -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tren Pendapatan Bersih Fotografer Harian</h4>
                </div>
                <div class="card-body">
                    <canvas id="photographerRevenueByDayChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tren Booking Berdasarkan Hari -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Jumlah Booking Fotografer per Hari</h4>
                </div>
                <div class="card-body">
                    <canvas id="bookingCountChart" style="height: 300px;"></canvas>
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
            $('#photographerTable').DataTable({
                order: [[3, 'desc']], // Sort by revenue descending by default
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
                },
                responsive: true,
                pagingType: 'simple_numbers',
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
            });

            // Photographer Revenue Chart - Modern Doughnut
            const photographerData = @json($photographerRevenue);

            createModernDoughnutChart('photographerChart',
                photographerData.map(item => item.name),
                photographerData.map(item => item.revenue),
                'Pendapatan Bersih per Fotografer'
            );

            // Package Type Chart - Modern Bar Chart
            const packageTypeData = @json($photographerRevenue->groupBy('package_type')
                ->map(function($group) {
                    return [
                        'package_type' => $group->first()->package_type,
                        'revenue' => $group->sum('revenue')
                    ];
                })->values());

            createModernBarChart('packageTypeChart',
                packageTypeData.map(item => item.package_type),
                packageTypeData.map(item => item.revenue),
                'Pendapatan Bersih per Tipe Paket'
            );

            // Photographer Revenue by Day Chart - Modern Line Chart
            createModernLineChart('photographerRevenueByDayChart', @json($photographerRevenueByDay));

            // Booking Count Chart - Modern Bar Chart
            createModernBookingCountChart('bookingCountChart', @json($photographerRevenueByDay));
        });

        function createModernDoughnutChart(canvasId, labels, data, title) {
            // Hapus instance chart lama jika ada
            if (window[canvasId + 'Instance']) {
                window[canvasId + 'Instance'].destroy();
            }

            const ctx = document.getElementById(canvasId).getContext('2d');

            // Modern color palette - vibrant colors that work well together
            const colors = {
                backgroundColor: [
                    'rgba(236, 72, 153, 0.85)',   // pink-500
                    'rgba(139, 92, 246, 0.85)',   // purple-500
                    'rgba(59, 130, 246, 0.85)',   // blue-500
                    'rgba(14, 165, 233, 0.85)',   // sky-500
                    'rgba(20, 184, 166, 0.85)',   // teal-500
                    'rgba(16, 185, 129, 0.85)',   // emerald-500
                    'rgba(234, 88, 12, 0.85)',    // orange-600
                    'rgba(239, 68, 68, 0.85)'     // red-500
                ],
                borderColor: [
                    'rgba(236, 72, 153, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(59, 130, 246, 1)',
                    'rgba(14, 165, 233, 1)',
                    'rgba(20, 184, 166, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(234, 88, 12, 1)',
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

            // Create gradient for bars
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(236, 72, 153, 0.85)'); // pink-500
            gradient.addColorStop(1, 'rgba(236, 72, 153, 0.3)');  // pink-500 with lower opacity

            window[canvasId + 'Instance'] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan Bersih',
                        data: data,
                        backgroundColor: gradient,
                        borderColor: 'rgba(236, 72, 153, 1)', // pink-500
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 30,
                        maxBarThickness: 50
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
                                color: '#6B7280' // gray-500
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

        function createModernLineChart(canvasId, photographerData) {
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
                const existingData = photographerData.find(item => item.date === dateString);
                return {
                    date: dateString,
                    revenue: existingData ? parseFloat(existingData.revenue) : 0,
                    booking_count: existingData ? existingData.booking_count : 0
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
            gradient.addColorStop(0, 'rgba(236, 72, 153, 0.2)'); // pink-500 dengan opacity rendah
            gradient.addColorStop(1, 'rgba(236, 72, 153, 0.02)'); // pink-500 dengan opacity sangat rendah

            window[canvasId + 'Instance'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan Bersih Harian',
                        data: revenueData,
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: 'rgba(236, 72, 153, 1)', // pink-500
                        borderWidth: 3,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(236, 72, 153, 1)', // pink-500
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 0, // Hide points by default
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: 'rgba(236, 72, 153, 1)', // pink-500
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
                            text: 'Tren Pendapatan Bersih Fotografer Harian',
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

        function createModernBookingCountChart(canvasId, photographerData) {
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
                const existingData = photographerData.find(item => item.date === dateString);
                return {
                    date: dateString,
                    booking_count: existingData ? existingData.booking_count : 0
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

            // Data jumlah booking
            const bookingData = completeData.map(item => item.booking_count);

            // Create gradient for bars
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(139, 92, 246, 0.85)'); // purple-500
            gradient.addColorStop(1, 'rgba(139, 92, 246, 0.3)');  // purple-500 with lower opacity

            window[canvasId + 'Instance'] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Booking',
                        data: bookingData,
                        backgroundColor: gradient,
                        borderColor: 'rgba(139, 92, 246, 1)', // purple-500
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false
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
                                stepSize: 1
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
                            text: 'Jumlah Booking Fotografer per Hari',
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
                                    return 'Jumlah Booking: ' + context.raw;
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
    </script>
    <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
