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
            // Initialize DataTable
            $('#revenueByDayTable').DataTable({
                order: [[0, 'desc']], // Sort by date descending by default
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
                }
            });

            // Revenue Pie Chart
            createPieChart();

            // Revenue by Day Chart
            createLineChart();
        });

        function createPieChart() {
            const ctx = document.getElementById('revenuePieChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Lapangan', 'Rental', 'Fotografer'],
                    datasets: [{
                        data: [
                            {{ $fieldBookingRevenue }},
                            {{ $rentalRevenue }},
                            {{ $photographerRevenue }}
                        ],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 99, 132, 0.7)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        title: {
                            display: true,
                            text: 'Distribusi Pendapatan per Kategori'
                        },
                        tooltip: {
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
                    }
                }
            });
        }

        function createLineChart() {
            const ctx = document.getElementById('revenueByDayChart').getContext('2d');

            const days = @json($revenueByDay->pluck('date'));
            const labels = days.map(day => {
                const date = new Date(day);
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Pendapatan',
                        data: @json($revenueByDay->pluck('total_gross')),
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2
                    }, {
                        label: 'Pendapatan Lapangan',
                        data: @json($revenueByDay->pluck('field_revenue')),
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2
                    }, {
                        label: 'Pendapatan Rental',
                        data: @json($revenueByDay->pluck('rental_revenue')),
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2
                    }, {
                        label: 'Pendapatan Fotografer',
                        data: @json($revenueByDay->pluck('photographer_revenue')),
                        borderColor: 'rgba(255, 206, 86, 1)',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2
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
                            text: 'Tren Pendapatan Harian'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection
