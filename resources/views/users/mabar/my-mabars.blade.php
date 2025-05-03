@extends('layouts.app')
@section('content')
    <!-- Hero Section -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Mabar Saya</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i> Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('user.mabar.index') }}">Main Bareng</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Mabar Saya</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content bg-light">
        <div class="container py-5">
            <!-- Bootstrap Alert for Session Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end mb-4">
                <a href="{{ route('user.mabar.create') }}" class="btn btn-danger rounded-pill">
                    <i class="fas fa-plus-circle me-2"></i> Buat Open Mabar
                </a>
            </div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="mabarTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="created-tab" data-bs-toggle="tab" data-bs-target="#created"
                        type="button" role="tab" aria-controls="created" aria-selected="true">
                        <i class="fas fa-user-cog me-2"></i> Dibuat Saya
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="joined-tab" data-bs-toggle="tab" data-bs-target="#joined" type="button"
                        role="tab" aria-controls="joined" aria-selected="false">
                        <i class="fas fa-user-plus me-2"></i> Diikuti Saya
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="mabarTabsContent">
                <!-- Created Mabars -->
                <div class="tab-pane fade show active" id="created" role="tabpanel" aria-labelledby="created-tab">
                    @if ($createdMabars->isEmpty())
                        <div class="card border-0 shadow-sm rounded-4 py-5">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h4>Belum Ada Open Mabar</h4>
                                <p class="text-muted mb-4">Anda belum pernah membuat Open Mabar.</p>
                                <a href="{{ route('user.mabar.create') }}" class="btn btn-danger rounded-pill px-4 py-2">
                                    <i class="fas fa-plus-circle me-2"></i> Buat Open Mabar
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover bg-white rounded-4 shadow-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul</th>
                                        <th>Lapangan</th>
                                        <th>Jadwal</th>
                                        <th>Peserta</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($createdMabars as $mabar)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mabar-icon me-3 d-flex align-items-center justify-content-center bg-danger-subtle rounded-circle"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="fas fa-futbol text-danger"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $mabar->title }}</div>
                                                        <small class="text-muted">{{ ucfirst($mabar->level) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $mabar->fieldBooking->field->name }}</td>
                                            <td>
                                                <div>{{ \Carbon\Carbon::parse($mabar->start_time)->format('d M Y') }}</div>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($mabar->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($mabar->end_time)->format('H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="fw-medium">{{ $mabar->filled_slots }}/{{ $mabar->total_slots }}</span>
                                                    <div class="progress ms-2" style="width: 60px; height: 6px;">
                                                        <div class="progress-bar bg-danger" role="progressbar"
                                                            style="width: {{ $mabar->filledPercentage() }}%;"
                                                            aria-valuenow="{{ $mabar->filledPercentage() }}"
                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($mabar->status == 'open')
                                                    <span class="badge bg-success">Open</span>
                                                @elseif($mabar->status == 'full')
                                                    <span class="badge bg-danger">Full</span>
                                                @elseif($mabar->status == 'cancelled')
                                                    <span class="badge bg-secondary">Cancelled</span>
                                                @elseif($mabar->status == 'completed')
                                                    <span class="badge bg-info">Completed</span>
                                                @endif

                                                @if ($mabar->end_time < now())
                                                    <span class="badge bg-secondary ms-1">Selesai</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('user.mabar.show', $mabar->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>

                                                    @if ($mabar->end_time > now())
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal{{ $mabar->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>

                                                        <!-- Modal Konfirmasi Hapus -->
                                                        <div class="modal fade" id="deleteModal{{ $mabar->id }}"
                                                            tabindex="-1"
                                                            aria-labelledby="deleteModalLabel{{ $mabar->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="deleteModalLabel{{ $mabar->id }}">
                                                                            Konfirmasi Hapus</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Apakah Anda yakin ingin menghapus Open Mabar
                                                                            "{{ $mabar->title }}"?</p>
                                                                        <p class="text-danger">Peringatan: Tindakan ini
                                                                            tidak dapat dibatalkan!</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Batal</button>
                                                                        <form
                                                                            action="{{ route('user.mabar.delete', $mabar->id) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                class="btn btn-danger">Hapus</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Joined Mabars -->
                <div class="tab-pane fade" id="joined" role="tabpanel" aria-labelledby="joined-tab">
                    @if ($joinedMabars->isEmpty())
                        <div class="card border-0 shadow-sm rounded-4 py-5">
                            <div class="card-body text-center">
                                <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                                <h4>Belum Bergabung dengan Open Mabar</h4>
                                <p class="text-muted mb-4">Anda belum bergabung dengan Open Mabar manapun.</p>
                                <a href="{{ route('user.mabar.index') }}" class="btn btn-danger rounded-pill px-4 py-2">
                                    <i class="fas fa-search me-2"></i> Cari Open Mabar
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover bg-white rounded-4 shadow-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul</th>
                                        <th>Tuan Rumah</th>
                                        <th>Lapangan</th>
                                        <th>Jadwal</th>
                                        <th>Harga</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($joinedMabars as $mabar)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mabar-icon me-3 d-flex align-items-center justify-content-center bg-danger-subtle rounded-circle"
                                                        style="width: 40px; height: 40px;">
                                                        <i class="fas fa-futbol text-danger"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $mabar->title }}</div>
                                                        <small class="text-muted">{{ ucfirst($mabar->level) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $mabar->user->name }}</td>
                                            <td>{{ $mabar->fieldBooking->field->name }}</td>
                                            <td>
                                                <div>{{ \Carbon\Carbon::parse($mabar->start_time)->format('d M Y') }}</div>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($mabar->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($mabar->end_time)->format('H:i') }}
                                                </small>
                                            </td>
                                            <td>Rp{{ number_format($mabar->price_per_slot, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($mabar->status == 'open')
                                                    <span class="badge bg-success">Open</span>
                                                @elseif($mabar->status == 'full')
                                                    <span class="badge bg-danger">Full</span>
                                                @elseif($mabar->status == 'cancelled')
                                                    <span class="badge bg-secondary">Cancelled</span>
                                                @elseif($mabar->status == 'completed')
                                                    <span class="badge bg-info">Completed</span>
                                                @endif

                                                @if ($mabar->end_time < now())
                                                    <span class="badge bg-secondary ms-1">Selesai</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('user.mabar.show', $mabar->id) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
                    <!-- Modal Force Delete untuk Open Mabar yang Sudah Ada Peserta yang Bayar -->
        @if (session('show_force_delete_modal'))
        <div class="modal fade" id="forceDeleteModal" tabindex="-1" aria-labelledby="forceDeleteModalLabel"
            aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="forceDeleteModalLabel">Konfirmasi Hapus dengan Peserta Berbayar
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('user.mabar.delete', session('mabar_id')) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="force_delete" value="1">

                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Peringatan:</strong> Open Mabar ini memiliki peserta yang sudah membayar. Anda
                                harus memberikan informasi untuk refund kepada peserta.
                            </div>

                            <div class="mb-3">
                                <label for="cancellation_reason" class="form-label">Alasan Pembatalan <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required></textarea>
                                <div class="form-text">Jelaskan alasan mengapa Open Mabar dibatalkan</div>
                            </div>

                            <div class="mb-3">
                                <label for="refund_info" class="form-label">Informasi Refund <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="refund_info" name="refund_info" rows="4" required></textarea>
                                <div class="form-text">Berikan informasi detail bagaimana peserta akan mendapatkan
                                    refund (metode pembayaran, jadwal, proses, dll)</div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash me-2"></i>Hapus dengan Refund
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Script untuk membuka modal force delete otomatis -->
        @if (session('show_force_delete_modal'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var forceDeleteModal = new bootstrap.Modal(document.getElementById('forceDeleteModal'));
                    forceDeleteModal.show();
                });
            </script>
        @endif
    @endif
        </div>

    </div>

    <style>
        /* Hero Section */
        .hero-section {
            background: linear-gradient(to right, #9e0620, #bb2d3b);
            height: 220px;
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .hero-content {
            color: white;
            text-align: center;
            width: 100%;
        }

        .hero-title {
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .breadcrumb-wrapper {
            display: flex;
            justify-content: center;
        }

        .breadcrumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50px;
            padding: 0.8rem 1.5rem;
            display: inline-flex;
            margin-bottom: 0;
        }

        .breadcrumb-item {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .breadcrumb-item.active {
            color: white;
            font-weight: 500;
        }

        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: white;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            color: rgba(255, 255, 255, 0.6);
        }

        /* Tabs */
        .nav-tabs .nav-link {
            padding: 0.75rem 1.25rem;
            border: none;
            border-bottom: 3px solid transparent;
            color: #6c757d;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: #d00f25;
            background-color: transparent;
            border-bottom: 3px solid #d00f25;
        }

        .nav-tabs .nav-link:hover:not(.active) {
            border-bottom: 3px solid #e9ecef;
        }

        /* Table */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            font-weight: 600;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
        }

        /* Progress Bar */
        .progress {
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            border-radius: 10px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
