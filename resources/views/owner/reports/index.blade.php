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
                        <h6 class="font-extrabold mb-0">Rp {{ number_format($photographerNetRevenue, 0, ',', '.') }}</h6>
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
                        <h6 class="font-extrabold mb-0">Rp {{ number_format($membershipNetRevenue, 0, ',', '.') }}</h6>
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
                        <h6 class="font-extrabold mb-0">Rp {{ number_format($productSalesNetRevenue, 0, ',', '.') }}</h6>
                    </div>
                </div>
            </div>
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
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Distribusi Pendapatan Bersih</h4>
                    </div>
                    <div class="card-body">
                        <canvas id="distributionChart"></canvas>
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
                        <canvas id="fieldTypeChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Fetch data
    fetch('{{ route("owner.reports.dashboard-stats") }}')
        .then(response => response.json())
        .then(data => {
            createModernRevenueChart(data);
            createDistributionChart();
            createFieldTypeChart(data.field_popularity);
        })
        .catch(error => console.error('Error loading chart data:', error));

    // Create Modern Revenue Chart
    function createModernRevenueChart(data) {
        const fieldData = data.field_revenue_trend || [];
        const rentalData = data.rental_revenue_trend || [];
        const photographerData = data.photographer_revenue_trend || [];
        const productData = data.product_revenue_trend || [];

        // Get all unique dates
        const allDates = [...new Set([
            ...fieldData.map(item => item.date),
            ...rentalData.map(item => item.date),
            ...photographerData.map(item => item.date),
            ...productData.map(item => item.date)
        ])].sort();

        // Modern color palette
        const colors = {
            field: {
                primary: '#3B82F6', // blue-500
                bg: 'rgba(59, 130, 246, 0.15)',
                hover: 'rgba(59, 130, 246, 0.3)'
            },
            rental: {
                primary: '#10B981', // emerald-500
                bg: 'rgba(16, 185, 129, 0.15)',
                hover: 'rgba(16, 185, 129, 0.3)'
            },
            photographer: {
                primary: '#EC4899', // pink-500
                bg: 'rgba(236, 72, 153, 0.15)',
                hover: 'rgba(236, 72, 153, 0.3)'
            },
            product: {
                primary: '#F59E0B', // amber-500
                bg: 'rgba(245, 158, 11, 0.15)',
                hover: 'rgba(245, 158, 11, 0.3)'
            }
        };

        // Create modern datasets
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

        // Destroy existing chart instance if exists
        if (window.revenueChartInstance) {
            window.revenueChartInstance.destroy();
        }

        // Create new chart instance
        window.revenueChartInstance = new Chart(ctx, {
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
                                family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                size: 12
                            }
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
                                family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                size: 12
                            },
                            color: '#6B7280' // gray-500
                        }
                    },
                    y: {
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

        // Add hover effect for better UX
        const chartCanvas = document.getElementById('revenueChart');
        chartCanvas.style.cursor = 'pointer';
    }

// Fungsi untuk membuat chart distribusi pendapatan
function createDistributionChart() {
    // Hapus instance chart lama jika ada
    if (window.distributionChartInstance) {
        window.distributionChartInstance.destroy();
    }

    const ctx = document.getElementById('distributionChart').getContext('2d');

    // Color palette yang lebih modern
    const colors = {
        backgroundColor: [
            'rgba(59, 130, 246, 0.8)',  // Blue
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

    window.distributionChartInstance = new Chart(ctx, {
        type: 'doughnut', // Menggunakan doughnut untuk tampilan yang lebih modern
        data: {
            labels: ['Lapangan', 'Rental', 'Fotografer', 'Membership', 'Produk'],
            datasets: [{
                data: [
                    {{ $fieldNetRevenue }},
                    {{ $rentalNetRevenue }},
                    {{ $photographerNetRevenue }},
                    {{ $membershipNetRevenue }},
                    {{ $productSalesNetRevenue }}
                ],
                backgroundColor: colors.backgroundColor,
                borderColor: colors.borderColor,
                borderWidth: 2,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%', // Membuat doughnut lebih elegan
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
                            size: 12
                        }
                    }
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
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.raw;
                            const percentage = ((value / total) * 100).toFixed(1);
                            return context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(value) +
                                ' (' + percentage + '%)';
                        }
                    }
                }
            },
            // Animasi yang lebih menarik
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 800,
                easing: 'easeOutQuart'
            }
        }
    });
}

// Fungsi untuk membuat chart tipe lapangan yang diperbaiki
function createFieldTypeChart(data) {
    // Pastikan data valid
    if (!data || !Array.isArray(data) || data.length === 0) {
        console.error('Data tipe lapangan tidak valid:', data);
        // Tampilkan pesan error di chart jika data tidak valid
        const errorCtx = document.getElementById('fieldTypeChart').getContext('2d');
        errorCtx.font = "14px 'Poppins', sans-serif";
        errorCtx.fillStyle = "#EF4444";
        errorCtx.textAlign = "center";
        errorCtx.fillText("Data tipe lapangan tidak tersedia", errorCtx.canvas.width/2, errorCtx.canvas.height/2);
        return;
    }

    try {
        // Hapus instance chart lama jika ada
        if (window.fieldTypeChartInstance) {
            window.fieldTypeChartInstance.destroy();
        }

        const labels = data.map(item => item.type);
        const values = data.map(item => item.revenue);

        // Menggunakan warna yang lebih konsisten dan modern
        const baseColor = 'rgba(59, 130, 246, 0.85)'; // Biru modern
        const borderColor = 'rgba(59, 130, 246, 1)';

        // Generate gradient untuk tiap bar
        const ctx = document.getElementById('fieldTypeChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, baseColor);
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.3)');

        // Membuat array warna sesuai jumlah data
        const backgroundColors = Array(labels.length).fill(gradient);
        const borderColors = Array(labels.length).fill(borderColor);

        window.fieldTypeChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan Bersih per Tipe Lapangan',
                    data: values,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1,
                    borderRadius: 6,
                    barThickness: 30, // Ukuran bar yang lebih baik
                    maxBarThickness: 45 // Maksimal lebar bar
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
                                family: "'Poppins', 'Helvetica', 'Arial', sans-serif",
                                size: 12
                            },
                            color: '#6B7280' // gray-500
                        }
                    },
                    y: {
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
                    }
                },
                plugins: {
                    legend: {
                        display: false // Sembunyikan legend karena tidak perlu
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
    } catch (error) {
        console.error('Error creating field type chart:', error);
        // Tampilkan pesan error di chart
        const errorCtx = document.getElementById('fieldTypeChart').getContext('2d');
        errorCtx.font = "14px 'Poppins', sans-serif";
        errorCtx.fillStyle = "#EF4444";
        errorCtx.textAlign = "center";
        errorCtx.fillText("Terjadi kesalahan saat memuat grafik", errorCtx.canvas.width/2, errorCtx.canvas.height/2);
    }
}


    function createModernDataset(allDates, data, label, colors) {
        const values = allDates.map(date => {
            const match = data.find(item => item.date === date);
            return match ? match.total : 0;
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
});
</script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
