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
                            <div class="card bg-light">
                                <div class="card-body text-center py-4">
                                    <h5 class="mb-2">Total Pendapatan Bersih Membership</h5>
                                    <h2 class="text-primary mb-0">Rp {{ number_format($totalMembershipNetRevenue, 0, ',', '.') }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card bg-light">
                                <div class="card-body text-center py-4">
                                    <h5 class="mb-2">Jumlah Membership Aktif</h5>
                                    <h2 class="text-success mb-0">{{ number_format($activeMemberships, 0) }}</h2>
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

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            $('#membershipTable').DataTable({
                order: [[4, 'desc']], // Sort by revenue descending by default
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
                }
            });

            // Membership Type Revenue Chart
            const membershipTypeData = @json($membershipRevenueByType);

            createPieChart('membershipTypeChart',
                membershipTypeData.map(item => item.name),
                membershipTypeData.map(item => item.revenue),
                'Pendapatan Bersih per Tipe Membership'
            );

            // Membership Usage Chart
            const membershipUsageData = @json($membershipUsageByCategory);
            createBarChart('membershipUsageChart',
                membershipUsageData.map(item => item.category),
                membershipUsageData.map(item => item.usage_count),
                'Penggunaan Membership per Kategori',
                'Jumlah Penggunaan'
            );

            // Membership Revenue Trend Chart
            createLineChart('membershipRevenueTrendChart', @json($membershipRevenueByDay));
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
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
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
                                    return context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value) +
                                           ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        function createBarChart(canvasId, labels, data, title, labelText = 'Pendapatan Bersih') {
            const ctx = document.getElementById(canvasId).getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: labelText,
                        data: data,
                        backgroundColor: 'rgba(153, 102, 255, 0.7)',
                        borderColor: 'rgba(153, 102, 255, 1)',
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
                                    if (labelText.includes('Pendapatan')) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                    return value;
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
                                    if (labelText.includes('Pendapatan')) {
                                        return labelText + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                    return labelText + ': ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        }

        function createLineChart(canvasId, membershipData) {
            const ctx = document.getElementById(canvasId).getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: membershipData.map(item => {
                        const date = new Date(item.date);
                        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                    }),
                    datasets: [{
                        label: 'Pendapatan Bersih Harian',
                        data: membershipData.map(item => item.revenue),
                        fill: false,
                        borderColor: 'rgba(75, 192, 192, 1)',
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
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Tren Pendapatan Bersih Membership Harian'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Pendapatan Bersih: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
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
