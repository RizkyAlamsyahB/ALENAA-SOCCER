@extends('layouts.owner')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Laporan Ringkasan Pendapatan</h3>
                <p class="text-subtitle text-muted">Analisis ringkasan pendapatan dari semua sumber.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.reports.index') }}">Laporan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ringkasan Pendapatan</li>
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
                    <form action="{{ route('owner.reports.revenue') }}" method="GET" class="row g-3">
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

        <!-- Statistik Utama -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Ringkasan Pendapatan</h4>
                </div>
                <div class="card-body">
                    <!-- Tambahkan informasi tentang membership di bagian atas -->
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i>
                        <strong>Catatan:</strong> Laporan pendapatan ini hanya menampilkan pendapatan dari transaksi langsung (non-membership).
                        Pendapatan dari penjualan paket membership dicatat terpisah.
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card bg-primary">
                                <div class="card-body text-center py-4">
                                    <h6 class="text-white font-semibold">Total Pendapatan Bersih</h6>
                                    <h4 class="font-extrabold text-white mb-0">Rp {{ number_format($totalNetRevenue, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card bg-primary">
                                <div class="card-body text-center py-4">
                                    <h6 class="text-white font-semibold">Pendapatan Lapangan Bersih</h6>
                                    <h4 class="font-extrabold text-white mb-0">Rp {{ number_format($fieldBookingNetRevenue, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card bg-primary">
                                <div class="card-body text-center py-4">
                                    <h6 class="text-white font-semibold">Pendapatan Rental Bersih</h6>
                                    <h4 class="font-extrabold text-white mb-0">Rp {{ number_format($rentalNetRevenue, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card bg-primary">
                                <div class="card-body text-center py-4">
                                    <h6 class="text-white font-semibold">Pendapatan Fotografer Bersih</h6>
                                    <h4 class="font-extrabold text-white mb-0">Rp {{ number_format($photographerNetRevenue, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tambahkan card untuk Product Sales -->
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card bg-primary">
                                <div class="card-body text-center py-4">
                                    <h6 class="text-white font-semibold">Pendapatan Produk Bersih</h6>
                                    <h4 class="font-extrabold text-white mb-0">Rp {{ number_format($productSalesNetRevenue, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card ">
                                <div class="card-body">
                                    <canvas id="revenuePieChart"></canvas>
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
                    <h4>Tren Pendapatan Harian</h4>
                </div>
                <div class="card-body">
                    <canvas id="revenueByDayChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Perbandingan Pendapatan Per Kategori -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>Perbandingan Sumber Pendapatan per Hari</h4>
                    <span class="badge bg-light-info">*Hanya transaksi non-membership</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped" id="revenueByDayTable">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th class="text-end">Lapangan</th>
                                    <th class="text-end">Rental</th>
                                    <th class="text-end">Fotografer</th>
                                    <th class="text-end">Produk</th>
                                    <th class="text-end">Diskon</th>
                                    <th class="text-end">Total Kotor</th>
                                    <th class="text-end">Total Bersih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($revenueByDay as $day)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                                    <td class="text-end">Rp {{ number_format($day->field_revenue ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($day->rental_revenue ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($day->photographer_revenue ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($day->product_revenue ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($day->discount_amount ?? 0, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($day->total_gross, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($day->total_net, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Scripts - Sebaiknya diletakkan di bagian bawah untuk optimasi loading -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable with modern styling
            $('#revenueByDayTable').DataTable({
                order: [[0, 'desc']], // Sort by date descending by default
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
                },
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Semua"]],
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                initComplete: function() {
                    $('.dataTables_wrapper .dataTables_filter input').addClass('form-control form-control-sm');
                    $('.dataTables_wrapper .dataTables_length select').addClass('form-select form-select-sm');
                }
            });

            // Modern Revenue Pie Chart
            createModernPieChart();

            // Modern Revenue by Day Chart
            createModernLineChart();
        });

        function createModernPieChart() {
            // Hapus instance chart lama jika ada
            if (window.revenuePieChartInstance) {
                window.revenuePieChartInstance.destroy();
            }

            const ctx = document.getElementById('revenuePieChart').getContext('2d');

            // Modern color palette
            const colors = {
                field: {
                    primary: '#10B981', // emerald-500
                    hover: '#059669'    // emerald-600
                },
                rental: {
                    primary: '#3B82F6', // blue-500
                    hover: '#2563EB'    // blue-600
                },
                photographer: {
                    primary: '#EC4899', // pink-500
                    hover: '#DB2777'    // pink-600
                },
                product: {
                    primary: '#8B5CF6', // violet-500
                    hover: '#7C3AED'    // violet-600
                }
            };

            // Data untuk chart
            const data = {
                labels: ['Lapangan', 'Rental', 'Fotografer', 'Produk'],
                datasets: [{
                    data: [
                        {{ $fieldBookingRevenue }},
                        {{ $rentalRevenue }},
                        {{ $photographerRevenue }},
                        {{ $productRevenue }}
                    ],
                    backgroundColor: [
                        colors.field.primary,
                        colors.rental.primary,
                        colors.photographer.primary,
                        colors.product.primary
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverBackgroundColor: [
                        colors.field.hover,
                        colors.rental.hover,
                        colors.photographer.hover,
                        colors.product.hover
                    ],
                    hoverBorderColor: '#ffffff',
                    hoverBorderWidth: 3,
                    hoverOffset: 15
                }]
            };

            // Custom center text plugin
            const centerTextPlugin = {
                id: 'centerText',
                beforeDraw: function(chart) {
                    const width = chart.width;
                    const height = chart.height;
                    const ctx = chart.ctx;

                    ctx.restore();

                    // Calculate total revenue
                    const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    const formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);

                    // Text styles
                    ctx.font = "bold 16px 'Poppins', sans-serif";
                    ctx.textBaseline = 'middle';
                    ctx.textAlign = 'center';
                    ctx.fillStyle = '#111827';



                    ctx.save();
                }
            };

            // Chart configuration
            window.revenuePieChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: data,
                plugins: [centerTextPlugin],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    layout: {
                        padding: 20
                    },
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
                                    family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Pendapatan per Kategori',
                            font: {
                                family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                size: 16,
                                weight: '600'
                            },
                            padding: {
                                top: 10,
                                bottom: 20
                            },
                            color: '#111827'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1F2937',
                            bodyColor: '#4B5563',
                            borderColor: 'rgba(229, 231, 235, 1)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                size: 13
                            },
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

        function createModernLineChart() {
            // Hapus instance chart lama jika ada
            if (window.revenueLineChartInstance) {
                window.revenueLineChartInstance.destroy();
            }

            const ctx = document.getElementById('revenueByDayChart').getContext('2d');

            // Mendapatkan data dari Laravel
            const days = @json($revenueByDay->pluck('date'));
            const labels = days.map(day => {
                const date = new Date(day);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            });

            // Palette warna modern
            const colors = {
                total: {
                    primary: '#F43F5E', // rose-500
                    bg: 'rgba(244, 63, 94, 0.1)',
                    hover: 'rgba(244, 63, 94, 0.3)'
                },
                field: {
                    primary: '#10B981', // emerald-500
                    bg: 'rgba(16, 185, 129, 0.05)',
                    hover: 'rgba(16, 185, 129, 0.3)'
                },
                rental: {
                    primary: '#3B82F6', // blue-500
                    bg: 'rgba(59, 130, 246, 0.05)',
                    hover: 'rgba(59, 130, 246, 0.3)'
                },
                photographer: {
                    primary: '#FBBF24', // amber-400
                    bg: 'rgba(251, 191, 36, 0.05)',
                    hover: 'rgba(251, 191, 36, 0.3)'
                },
                product: {
                    primary: '#8B5CF6', // violet-500
                    bg: 'rgba(139, 92, 246, 0.05)',
                    hover: 'rgba(139, 92, 246, 0.3)'
                }
            };

            // Membuat gradient untuk total pendapatan
            const totalGradient = ctx.createLinearGradient(0, 0, 0, 400);
            totalGradient.addColorStop(0, colors.total.bg);
            totalGradient.addColorStop(1, 'rgba(244, 63, 94, 0.01)');

            // Dataset
            window.revenueLineChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Total Pendapatan',
                            data: @json($revenueByDay->pluck('total_gross')),
                            borderColor: colors.total.primary,
                            backgroundColor: totalGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: colors.total.primary,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: colors.total.primary,
                            pointHoverBorderColor: '#fff',
                            pointHoverBorderWidth: 2,
                            order: 0,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Pendapatan Lapangan',
                            data: @json($revenueByDay->pluck('field_revenue')),
                            borderColor: colors.field.primary,
                            backgroundColor: colors.field.bg,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            order: 1,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Pendapatan Rental',
                            data: @json($revenueByDay->pluck('rental_revenue')),
                            borderColor: colors.rental.primary,
                            backgroundColor: colors.rental.bg,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            order: 2,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Pendapatan Fotografer',
                            data: @json($revenueByDay->pluck('photographer_revenue')),
                            borderColor: colors.photographer.primary,
                            backgroundColor: colors.photographer.bg,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            order: 3,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Pendapatan Produk',
                            data: @json($revenueByDay->pluck('product_revenue')),
                            borderColor: colors.product.primary,
                            backgroundColor: colors.product.bg,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            order: 4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                    size: 12
                                },
                                color: '#6B7280' // gray-500
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            beginAtZero: true,
                            grid: {
                                borderDash: [3, 3],
                                color: 'rgba(229, 231, 235, 0.8)' // gray-200
                            },
                            ticks: {
                                font: {
                                    family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                    size: 12
                                },
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
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                font: {
                                    family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                    size: 12
                                },
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
                                    family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                    size: 12
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Tren Pendapatan Harian',
                            font: {
                                family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                size: 16,
                                weight: '600'
                            },
                            padding: {
                                top: 10,
                                bottom: 20
                            },
                            color: '#111827'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1F2937',
                            bodyColor: '#4B5563',
                            borderColor: 'rgba(229, 231, 235, 1)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                size: 13
                            },
                            displayColors: true,
                            boxWidth: 8,
                            boxHeight: 8,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
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
                        point: {
                            radius: 0,
                            hitRadius: 10,
                            hoverRadius: 5
                        },
                        line: {
                            tension: 0.4
                        }
                    }
                }
            });

            // Add hover effect for better UX
            const chartCanvas = document.getElementById('revenueByDayChart');
            chartCanvas.style.cursor = 'pointer';
        }
    </script>
@endsection
