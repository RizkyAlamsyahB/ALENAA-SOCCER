@extends('layouts.owner')

@section('title', 'Riwayat Transaksi POS')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Riwayat Transaksi POS</h3>
                <p class="text-subtitle text-muted">Daftar seluruh transaksi dari Point of Sale</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.reports.index') }}">Laporan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Riwayat Transaksi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Filter Panel -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">Filter Transaksi</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('owner.reports.transactions') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="payment_type">Metode Pembayaran</label>
                                <select class="form-select" id="payment_type" name="payment_type">
                                    <option value="">Semua</option>
                                    <option value="cash" {{ $paymentType == 'cash' ? 'selected' : '' }}>Tunai</option>
                                    <option value="transfer" {{ $paymentType == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="points" {{ $paymentType == 'points' ? 'selected' : '' }}>Poin</option>
                                    <option value="other" {{ $paymentType == 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="search">Cari</label>
                                <input type="text" class="form-control" id="search" name="search" placeholder="Order ID / Nama Customer" value="{{ $search }}">
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mb-3">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon blue mb-2">
                                    <i class="bi bi-cash-coin"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Pendapatan</h6>
                                <h6 class="font-extrabold mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon green mb-2">
                                    <i class="bi bi-receipt"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Jumlah Transaksi</h6>
                                <h6 class="font-extrabold mb-0">{{ $transactions->total() }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon purple mb-2">
                                    <i class="bi bi-currency-exchange"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Rata-rata Transaksi</h6>
                                <h6 class="font-extrabold mb-0">
                                    Rp {{ $transactions->total() > 0 ? number_format($totalRevenue / $transactions->total(), 0, ',', '.') : 0 }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">Grafik Transaksi</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <canvas id="transaction-chart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title">Pembayaran berdasarkan Metode</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($revenueByPaymentType as $revenue)
                        <div class="col-md-3 col-sm-6">
                            <div class="card border">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="avatar avatar-lg bg-light-{{ getPaymentTypeColor($revenue->payment_type) }} me-3">
                                            <span class="avatar-content">
                                                <i class="{{ getPaymentTypeIcon($revenue->payment_type) }}"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ getPaymentTypeName($revenue->payment_type) }}</h6>
                                            <small class="text-muted">{{ number_format(($revenue->total / $totalRevenue) * 100, 1) }}%</small>
                                        </div>
                                    </div>
                                    <h4 class="font-weight-bold mb-0">Rp {{ number_format($revenue->total, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Transaction List -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Transaksi</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Metode Pembayaran</th>
                                <th>Total</th>
                                {{-- <th>Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->order_id }}</td>
                                    <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        {{ $transaction->customer->name ?? 'Guest' }}
                                        @if($transaction->customer && $transaction->customer->phone_number)
                                            <br><small>{{ $transaction->customer->phone_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light-{{ getPaymentTypeColor($transaction->payment_type) }}">
                                            {{ getPaymentTypeName($transaction->payment_type) }}
                                        </span>
                                    </td>
                                    <td>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    {{-- <td>
                                        <a href="{{ route('admin.pos.receipt', $transaction->id) }}" class="btn btn-sm btn-info" target="_blank">
                                            <i class="bi bi-eye"></i> Lihat Detail
                                        </a>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="bi bi-receipt-cutoff"></i>
                                            </div>
                                            <h5 class="mt-4">Tidak ada data transaksi</h5>
                                            <p class="text-muted">Tidak ada transaksi yang ditemukan dengan filter yang diterapkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk grafik
        const chartData = @json($chartData);

        // Hapus instance chart lama jika ada
        if (window.transactionChartInstance) {
            window.transactionChartInstance.destroy();
        }

        // Mendapatkan context untuk chart
        const ctx = document.getElementById('transaction-chart').getContext('2d');

        // Membuat gradient untuk latar belakang bar
        const barGradient = ctx.createLinearGradient(0, 0, 0, 400);
        barGradient.addColorStop(0, 'rgba(59, 130, 246, 0.7)'); // blue-500
        barGradient.addColorStop(1, 'rgba(59, 130, 246, 0.2)'); // blue-500 with lower opacity

        // Membuat gradient untuk area di bawah line
        const lineGradient = ctx.createLinearGradient(0, 0, 0, 400);
        lineGradient.addColorStop(0, 'rgba(239, 68, 68, 0.2)'); // red-500 with low opacity
        lineGradient.addColorStop(1, 'rgba(239, 68, 68, 0.02)'); // red-500 with very low opacity

        // Buat grafik transaksi dengan tampilan modern
        window.transactionChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.dates,
                datasets: [
                    {
                        label: 'Jumlah Transaksi',
                        data: chartData.counts,
                        backgroundColor: barGradient,
                        borderColor: 'rgba(59, 130, 246, 1)', // blue-500
                        borderWidth: 1,
                        borderRadius: 6,
                        yAxisID: 'y',
                        order: 2 // Higher order means it's drawn first (behind)
                    },
                    {
                        label: 'Total Pendapatan',
                        data: chartData.totals,
                        type: 'line',
                        backgroundColor: lineGradient,
                        borderColor: 'rgba(239, 68, 68, 1)', // red-500
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 0, // hide points by default
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: 'rgba(239, 68, 68, 1)',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2,
                        yAxisID: 'y1',
                        tension: 0.4,
                        fill: true,
                        order: 1 // Lower order means it's drawn last (in front)
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
                        top: 20,
                        right: 25,
                        bottom: 20,
                        left: 20
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Jumlah Transaksi',
                            color: '#6B7280', // gray-500
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        },
                        grid: {
                            borderDash: [3, 3],
                            color: 'rgba(229, 231, 235, 0.8)' // gray-200
                        },
                        ticks: {
                            color: '#6B7280', // gray-500
                            precision: 0, // ensure whole numbers
                            font: {
                                size: 11
                            }
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Total Pendapatan (Rp)',
                            color: '#6B7280', // gray-500
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            color: '#6B7280', // gray-500
                            font: {
                                size: 11
                            },
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
                            minRotation: 45,
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
                            boxWidth: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Transaksi dan Pendapatan',
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
                                if (context.dataset.yAxisID === 'y') {
                                    // For transaction count
                                    label += context.raw;
                                } else {
                                    // For revenue, format as currency
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
                    },
                    bar: {
                        borderRadius: 4
                    }
                }
            }
        });
    });
</script>

@php
// Helper functions - sebaiknya dipindahkan ke Helper atau BladeServiceProvider
function getPaymentTypeName($type) {
    $types = [
        'cash' => 'Tunai',
        'transfer' => 'Transfer',
        'points' => 'Poin',
        'other' => 'Lainnya'
    ];
    return $types[$type] ?? $type;
}

function getPaymentTypeIcon($type) {
    $icons = [
        'cash' => 'bi bi-cash',
        'transfer' => 'bi bi-bank',
        'points' => 'bi bi-star',
        'other' => 'bi bi-credit-card'
    ];
    return $icons[$type] ?? 'bi bi-question-circle';
}

function getPaymentTypeColor($type) {
    $colors = [
        'cash' => 'success',
        'transfer' => 'primary',
        'points' => 'warning',
        'other' => 'info'
    ];
    return $colors[$type] ?? 'secondary';
}
@endphp
@endsection
