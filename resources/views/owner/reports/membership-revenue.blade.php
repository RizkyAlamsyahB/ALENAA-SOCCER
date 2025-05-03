@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Laporan Pendapatan Bersih Membership</h3>
                <p class="text-subtitle text-muted">Analisis pendapatan bersih dari penjualan paket membership.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.reports.index') }}">Laporan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pendapatan Bersih Membership</li>
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
                    <form action="{{ route('owner.reports.membership-revenue') }}" method="GET" class="row g-3">
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

        <!-- Ringkasan Pendapatan Membership -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Ringkasan Pendapatan Bersih Membership</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card shadow border">
                                <div class="card-body text-center py-4">
                                    <h5 class="mb-2">Total Pendapatan Bersih Membership</h5>
                                    <h2 class="text-success mb-0">Rp {{ number_format($totalMembershipNetRevenue, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card shadow border">
                                <div class="card-body text-center py-4">
                                    <h5 class="mb-2">Jumlah Membership Aktif</h5>
                                    <h2 class="text-info mb-0">{{ number_format($activeMemberships, 0) }}</h2>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Pendapatan Per Tipe Membership -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Pendapatan Bersih Per Tipe Membership</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="membershipTable">
                            <thead>
                                <tr>
                                    <th>Tipe Membership</th>
                                    <th>Durasi</th>
                                    <th class="text-center">Jumlah Pembelian</th>
                                    <th class="text-center">Member Aktif</th>
                                    <th class="text-end">Pendapatan Bersih</th>
                                    <th class="text-end">Persentase Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($membershipRevenueByType as $membership)
                                <tr>
                                    <td>{{ $membership->name }}</td>
                                    <td>{{ $membership->duration }} Hari</td>
                                    <td class="text-center">{{ $membership->purchase_count }}</td>
                                    <td class="text-center">{{ $membership->active_count }}</td>
                                    <td class="text-end">Rp {{ number_format($membership->revenue, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        @if($totalMembershipNetRevenue > 0)
                                            {{ number_format(($membership->revenue / $totalMembershipNetRevenue) * 100, 1) }}%
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

        <!-- Grafik Per Tipe Membership -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Pendapatan Bersih per Tipe Membership</h4>
                </div>
                <div class="card-body">
                    <canvas id="membershipTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Penggunaan Membership -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Penggunaan Membership per Kategori</h4>
                </div>
                <div class="card-body">
                    <canvas id="membershipUsageChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tren Pendapatan Membership -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tren Pendapatan Bersih Membership</h4>
                </div>
                <div class="card-body">
                    <canvas id="membershipRevenueTrendChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable with improved styling
            $('#membershipTable').DataTable({
                order: [[4, 'desc']], // Sort by revenue descending by default
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
                },
                responsive: true,
                pagingType: 'simple_numbers',
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]]
            });

            // Membership Type Revenue Chart - Modern Doughnut
            const membershipTypeData = @json($membershipRevenueByType);

            createModernDoughnutChart('membershipTypeChart',
                membershipTypeData.map(item => item.name),
                membershipTypeData.map(item => item.revenue),
                'Pendapatan Bersih per Tipe Membership'
            );

            // Membership Usage Chart - Modern Bar Chart
            const membershipUsageData = @json($membershipUsageByCategory);
            createModernBarChart('membershipUsageChart',
                membershipUsageData.map(item => item.category),
                membershipUsageData.map(item => item.usage_count),
                'Penggunaan Membership per Kategori',
                'Jumlah Penggunaan'
            );

            // Membership Revenue Trend Chart - Modern Line Chart
            createModernLineChart('membershipRevenueTrendChart', @json($membershipRevenueByDay));
        });

        function createModernDoughnutChart(canvasId, labels, data, title) {
            // Hapus instance chart lama jika ada
            if (window[canvasId + 'Instance']) {
                window[canvasId + 'Instance'].destroy();
            }

            const ctx = document.getElementById(canvasId).getContext('2d');

            // Modern color palette - blue-purple theme for membership
            const colors = {
                backgroundColor: [
                    'rgba(79, 70, 229, 0.85)',   // indigo-600
                    'rgba(59, 130, 246, 0.85)',  // blue-500
                    'rgba(14, 165, 233, 0.85)',  // sky-500
                    'rgba(6, 182, 212, 0.85)',   // cyan-500
                    'rgba(124, 58, 237, 0.85)',  // purple-600
                    'rgba(139, 92, 246, 0.85)',  // purple-500
                    'rgba(147, 51, 234, 0.85)',  // violet-600
                    'rgba(79, 70, 229, 0.85)'    // indigo-600
                ],
                borderColor: [
                    'rgba(79, 70, 229, 1)',
                    'rgba(59, 130, 246, 1)',
                    'rgba(14, 165, 233, 1)',
                    'rgba(6, 182, 212, 1)',
                    'rgba(124, 58, 237, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(147, 51, 234, 1)',
                    'rgba(79, 70, 229, 1)'
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

        function createModernBarChart(canvasId, labels, data, title, labelText = 'Pendapatan Bersih') {
            // Hapus instance chart lama jika ada
            if (window[canvasId + 'Instance']) {
                window[canvasId + 'Instance'].destroy();
            }

            const ctx = document.getElementById(canvasId).getContext('2d');

            // Create gradient for bars
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(124, 58, 237, 0.85)'); // purple-600
            gradient.addColorStop(1, 'rgba(124, 58, 237, 0.3)');  // purple-600 with lower opacity

            window[canvasId + 'Instance'] = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: labelText,
                        data: data,
                        backgroundColor: gradient,
                        borderColor: 'rgba(124, 58, 237, 1)', // purple-600
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
                                    if (labelText.includes('Pendapatan')) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + ' jt';
                                        } else if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toLocaleString('id-ID') + ' rb';
                                        }
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                    return value;
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
                                    if (labelText.includes('Pendapatan')) {
                                        return labelText + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                    return labelText + ': ' + context.raw;
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

        function createModernLineChart(canvasId, membershipData) {
            // Hapus instance chart lama jika ada
            if (window[canvasId + 'Instance']) {
                window[canvasId + 'Instance'].destroy();
            }

            const ctx = document.getElementById(canvasId).getContext('2d');

            // Create array of dates from startDate to endDate
            const startDate = new Date('{{ $startDate }}');
            const endDate = new Date('{{ $endDate }}');
            const dateRange = [];

            // Fill all dates in range
            for (let dt = new Date(startDate); dt <= endDate; dt.setDate(dt.getDate() + 1)) {
                dateRange.push(new Date(dt).toISOString().split('T')[0]);
            }

            // Create dataset with zero values for dates with no transactions
            const completeData = dateRange.map(dateString => {
                const existingData = membershipData.find(item => item.date === dateString);
                return {
                    date: dateString,
                    revenue: existingData ? parseFloat(existingData.revenue) : 0
                };
            });

            // Format dates for display
            const labels = completeData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short'
                });
            });

            // Membership revenue data
            const revenueData = completeData.map(item => item.revenue);

            // Create gradient fill
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.25)'); // indigo-600 with transparency
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.02)'); // indigo-600 nearly transparent

            window[canvasId + 'Instance'] = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan Bersih Membership',
                        data: revenueData,
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: 'rgba(79, 70, 229, 1)',  // indigo-600
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 0, // Hide points by default
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: 'rgba(79, 70, 229, 1)',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    layout: {
                        padding: {
                            top: 20,
                            right: 20,
                            bottom: 20,
                            left: 20
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(229, 231, 235, 0.8)', // gray-200
                                borderDash: [3, 3]
                            },
                            ticks: {
                                color: '#6B7280', // gray-500
                                padding: 10,
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
                                padding: 10,
                                maxRotation: 30,
                                minRotation: 30
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Tren Pendapatan Bersih Membership Harian',
                            color: '#1F2937', // gray-800
                            font: {
                                size: 16,
                                weight: 'bold'
                            },
                            padding: {
                                bottom: 20
                            }
                        },
                        tooltip: {
                            enabled: true,
                            mode: 'index',
                            intersect: false,
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
                                    return 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    elements: {
                        line: {
                            borderJoinStyle: 'round'
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
