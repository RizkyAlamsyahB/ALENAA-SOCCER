@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Detail Tugas Fotografer #{{ $task->id }}</h3>
                <p class="text-subtitle text-muted">Informasi lengkap tugas fotografer dan progress pengerjaan.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('owner.photographer-tasks.index') }}">Tugas Fotografer</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail #{{ $task->id }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Task Information -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Informasi Tugas</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold" width="40%">ID Booking:</td>
                                        <td>#{{ $task->id }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Tanggal & Waktu:</td>
                                        <td>
                                            {{ Carbon\Carbon::parse($task->start_time)->format('d F Y') }}<br>
                                            <small class="text-muted">
                                                {{ Carbon\Carbon::parse($task->start_time)->format('H:i') }} -
                                                {{ Carbon\Carbon::parse($task->end_time)->format('H:i') }}
                                            </small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Harga:</td>
                                        <td>Rp {{ number_format($task->price, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Status Booking:</td>
                                        <td>
                                            @if($task->status == 'confirmed')
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($task->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Status Progress:</td>
                                        <td>
                                            @php
                                                $statusConfig = [
                                                    'confirmed' => ['class' => 'bg-primary', 'text' => 'Siap Shooting', 'icon' => 'camera'],
                                                    'shooting_completed' => ['class' => 'bg-warning', 'text' => 'Editing Foto', 'icon' => 'image'],
                                                    'delivered' => ['class' => 'bg-success', 'text' => 'Foto Dikirim', 'icon' => 'check-circle']
                                                ];
                                                $status = $statusConfig[$task->completion_status] ?? ['class' => 'bg-secondary', 'text' => $task->completion_status, 'icon' => 'question'];
                                            @endphp
                                            <span class="badge {{ $status['class'] }}">
                                                <i class="bi bi-{{ $status['icon'] }}"></i> {{ $status['text'] }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold" width="40%">Dibuat:</td>
                                        <td>{{ $task->created_at->format('d M Y H:i') }}</td>
                                    </tr>
                                    @if($task->completed_at)
                                    <tr>
                                        <td class="fw-bold">Shooting Selesai:</td>
                                        <td>{{ Carbon\Carbon::parse($task->completed_at)->format('d M Y H:i') }}</td>
                                    </tr>
                                    @endif
                                    @if($task->completion_status == 'delivered')
                                    <tr>
                                        <td class="fw-bold">Foto Dikirim:</td>
                                        <td>{{ $task->updated_at->format('d M Y H:i') }}</td>
                                    </tr>
                                    @endif
                                    @if($task->completed_at)
                                    <tr>
                                        <td class="fw-bold">Waktu Editing:</td>
                                        <td>
                                            @if($task->completion_status == 'delivered')
                                                {{ Carbon\Carbon::parse($task->completed_at)->diffInDays($task->updated_at) }} hari
                                            @else
                                                {{ Carbon\Carbon::parse($task->completed_at)->diffForHumans() }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        @if($task->notes)
                        <div class="mt-3">
                            <h6 class="fw-bold">Catatan Client:</h6>
                            <div class="alert alert-light">
                                {{ $task->notes }}
                            </div>
                        </div>
                        @endif

                        @if($task->photographer_notes)
                        <div class="mt-3">
                            <h6 class="fw-bold">Pesan Fotografer:</h6>
                            <div class="alert alert-info">
                                {{ $task->photographer_notes }}
                            </div>
                        </div>
                        @endif

                        @if($task->photo_gallery_link)
                        <div class="mt-3">
                            <h6 class="fw-bold">Link Galeri Foto:</h6>
                            <div class="d-flex align-items-center">
                                <a href="{{ $task->photo_gallery_link }}" target="_blank" class="btn btn-success me-2">
                                    <i class="bi bi-images"></i> Buka Galeri
                                </a>
                                <small class="text-muted">{{ $task->photo_gallery_link }}</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Progress Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Timeline Progress</h4>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Booking Dikonfirmasi</h6>
                                    <p class="timeline-text">Pembayaran berhasil dan booking dikonfirmasi</p>
                                    <small class="text-muted">{{ $task->created_at->format('d M Y H:i') }}</small>
                                </div>
                            </div>

                            @if($task->completed_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-warning"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Shooting Selesai</h6>
                                    <p class="timeline-text">Fotografer menandai sesi pemotretan selesai</p>
                                    <small class="text-muted">{{ Carbon\Carbon::parse($task->completed_at)->format('d M Y H:i') }}</small>
                                </div>
                            </div>
                            @endif

                            @if($task->completion_status == 'delivered')
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Galeri Foto Dikirim</h6>
                                    <p class="timeline-text">Link galeri foto telah dikirim ke client</p>
                                    <small class="text-muted">{{ $task->updated_at->format('d M Y H:i') }}</small>
                                </div>
                            </div>
                            @endif

                            @if(in_array($task->completion_status, ['confirmed', 'shooting_completed']))
                            <div class="timeline-item">
                                <div class="timeline-marker bg-light"></div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title text-muted">
                                        @if($task->completion_status == 'confirmed')
                                            Menunggu Sesi Pemotretan
                                        @else
                                            Menunggu Pengiriman Galeri
                                        @endif
                                    </h6>
                                    <p class="timeline-text text-muted">Status akan diperbarui oleh fotografer</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photographer & Client Info -->
            <div class="col-lg-4">


                <!-- Client Info -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Informasi Client</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-lg me-3">
                                <img src="{{ $task->user->profile_photo_url ?? asset('assets/images/faces/2.jpg') }}" alt="Client">
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $task->user->name }}</h5>
                                <p class="text-muted mb-0">{{ $task->user->email }}</p>
                            </div>
                        </div>

                        @if($task->user->phone)
                        <div class="mb-2">
                            <i class="bi bi-telephone"></i>
                            <span class="ms-2">{{ $task->user->phone }}</span>
                        </div>
                        @endif


                    </div>
                </div>

                <!-- Payment Info -->
                @if($task->payment)
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Informasi Pembayaran</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($task->payment->status) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Metode:</td>
                                <td>{{ $task->payment->payment_method ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Jumlah:</td>
                                <td>Rp {{ number_format($task->payment->amount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tanggal:</td>
                                <td>{{ $task->payment->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Aksi</h4>
                    </div>
                    <div class="card-body">
                        @if(in_array($task->completion_status, ['shooting_completed']) && $task->completed_at && Carbon\Carbon::parse($task->completed_at)->addDays(2)->isPast())
                            <button class="btn btn-warning w-100 mb-2" onclick="sendReminder({{ $task->id }})">
                                <i class="bi bi-bell"></i> Kirim Reminder ke Fotografer
                            </button>
                        @endif

                        <a href="{{ route('owner.photographer-tasks.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline:before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e9ecef;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 30px;
        }

        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #e9ecef;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .timeline-title {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .timeline-text {
            margin-bottom: 5px;
            color: #6c757d;
        }
    </style>

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // Configure Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "timeOut": "5000"
        };

        function sendReminder(taskId) {
            if (confirm('Kirim reminder ke fotografer untuk tugas ini?')) {
                $.ajax({
                    url: `{{ url('owner/photographer-tasks') }}/${taskId}/reminder`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Terjadi kesalahan saat mengirim reminder.');
                    }
                });
            }
        }
    </script>
@endsection
