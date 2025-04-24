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
            // Initialize DataTable
            $('#rentalItemsTable').DataTable({
                order: [
                    [4, 'desc']
                ], // Sort by revenue descending by default
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
                }
            });

            // Item Category Chart
            const categoryData = @json(
                $rentalRevenueByItem->groupBy('category')->map(function ($group) {
                        return [
                            'category' => $group->first()->category,
                            'revenue' => $group->sum('revenue'),
                        ];
                    })->values());

            createPieChart('itemCategoryChart',
                categoryData.map(item => item.category),
                categoryData.map(item => item.revenue),
                'Pendapatan Bersih per Kategori Item'
            );

            // Top 5 Items Chart
            const topItems = @json($rentalRevenueByItem->sortByDesc('revenue')->take(5)->values());
            createBarChart('topItemsChart',
                topItems.map(item => item.name),
                topItems.map(item => item.revenue),
                'Top 5 Item Rental (Pendapatan Bersih)'
            );

            // Rental Revenue by Day Chart (Line Chart)
            createLineChart('rentalRevenueByDayChart', @json($rentalRevenueByDay));
        });

        function createPieChart(canvasId, labels, data, title) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
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
                        title: {
                            display: true,
                            text: title
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(
                                            value) +
                                        ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        function createBarChart(canvasId, labels, data, title) {
            const ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan Bersih',
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
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
                        title: {
                            display: true,
                            text: title
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Pendapatan Bersih: Rp ' + new Intl.NumberFormat('id-ID').format(
                                        context.raw);
                                }
                            }
                        }
                    }
                }
            });
        }

        function createLineChart(canvasId, rentalData) {
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

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: completeData.map(item => {
                        const date = new Date(item.date);
                        return date.toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short'
                        });
                    }),
                    datasets: [{
                        label: 'Pendapatan Bersih Harian',
                        data: completeData.map(item => item.revenue),
                        fill: false,
                        borderColor: 'rgba(40, 167, 69, 1)', // Warna hijau
                        tension: 0.4
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
                        },
                        x: {
                            ticks: {
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tren Pendapatan Bersih Rental Harian'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Pendapatan Bersih: Rp ' + new Intl.NumberFormat('id-ID').format(
                                        context.raw);
                                }
                            }
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
