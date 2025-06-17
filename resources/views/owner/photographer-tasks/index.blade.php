@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Monitoring Tugas Fotografer</h3>
                <p class="text-subtitle text-muted">Pantau progress dan kinerja fotografer dalam menyelesaikan tugas.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Tugas Fotografer</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">

        <!-- Main Content Card -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Tugas Fotografer</h4>
                    <div class="card-tools">
                        <button class="btn btn-primary btn-sm" onclick="refreshTasks()">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                        {{-- <a href="{{ route('owner.photographer-tasks.performance') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-graph-up"></i> Performa
                        </a> --}}
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Filter Section -->
                {{-- <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterStatus">Status Tugas</label>
                            <select class="form-select" id="filterStatus" name="completion_status">
                                <option value="">Semua Status</option>
                                <option value="confirmed" {{ request('completion_status') == 'confirmed' ? 'selected' : '' }}>Siap Shooting</option>
                                <option value="shooting_completed" {{ request('completion_status') == 'shooting_completed' ? 'selected' : '' }}>Editing Foto</option>
                                <option value="delivered" {{ request('completion_status') == 'delivered' ? 'selected' : '' }}>Foto Dikirim</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterPhotographer">Fotografer</label>
                            <select class="form-select" id="filterPhotographer" name="photographer_id">
                                <option value="">Semua Fotografer</option>
                                @foreach($photographers as $photographer)
                                    <option value="{{ $photographer->id }}" {{ request('photographer_id') == $photographer->id ? 'selected' : '' }}>
                                        {{ $photographer->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filterDateFrom">Dari Tanggal</label>
                            <input type="date" class="form-control" id="filterDateFrom" name="date_from" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filterDateTo">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="filterDateTo" name="date_to" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="urgentOnly" name="urgent_only" value="1" {{ request('urgent_only') ? 'checked' : '' }}>
                                <label class="form-check-label" for="urgentOnly">
                                    Hanya Urgent
                                </label>
                            </div>
                            <button class="btn btn-primary w-100" onclick="applyFilters()">
                                <i class="bi bi-funnel-fill"></i> Filter
                            </button>
                        </div>
                    </div>
                </div> --}}

                <!-- Tasks Table -->
                <div class="table-responsive">
                    <table class="table table-striped" id="tasksTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fotografer</th>
                                <th>Client</th>
                                <th>Tanggal & Waktu</th>
                                <th>Status Progress</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($photographerTasks as $task)
<tr>
                                    <td>#{{ $task->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <img src="{{ $task->photographer->user->profile_photo_url ?? asset('assets/images/faces/1.jpg') }}" alt="Avatar">
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $task->photographer->user->name }}</h6>
                                                <small class="text-muted">{{ $task->photographer->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0">{{ $task->user->name }}</h6>
                                        <small class="text-muted">{{ $task->user->email }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ Carbon\Carbon::parse($task->start_time)->format('d M Y') }}</strong><br>
                                            <small class="text-muted">
                                                {{ Carbon\Carbon::parse($task->start_time)->format('H:i') }} -
                                                {{ Carbon\Carbon::parse($task->end_time)->format('H:i') }}
                                            </small>
                                        </div>
                                    </td>
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
                                        @if($task->completion_status == 'shooting_completed' && $task->completed_at)
                                            <br><small class="text-muted">
                                                {{ Carbon\Carbon::parse($task->completed_at)->diffForHumans() }}
                                            </small>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="btn-group">
                                            {{-- <a href="{{ route('owner.photographer-tasks.show', $task->id) }}"
                                               class="btn btn-sm btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a> --}}
                                            @if(in_array($task->completion_status, ['shooting_completed']) && $task->completed_at && Carbon\Carbon::parse($task->completed_at)->addDays(2)->isPast())
                                                <button class="btn btn-sm btn-warning send-reminder"
                                                        data-task-id="{{ $task->id }}"
                                                        title="Kirim Reminder">
                                                    <i class="bi bi-bell"></i>
                                                </button>
                                            @endif
                                            @if($task->photo_gallery_link)
                                                <a href="{{ $task->photo_gallery_link }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-success"
                                                   title="Lihat Galeri">
                                                    <i class="bi bi-images"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox" style="font-size: 3rem; color: #ddd;"></i>
                                            <h5 class="mt-3 text-muted">Tidak ada tugas fotografer</h5>
                                            <p class="text-muted">Belum ada tugas fotografer yang perlu dipantau.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($photographerTasks->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $photographerTasks->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

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

        // Apply filters
        function applyFilters() {
            const status = document.getElementById('filterStatus').value;
            const photographer = document.getElementById('filterPhotographer').value;
            const dateFrom = document.getElementById('filterDateFrom').value;
            const dateTo = document.getElementById('filterDateTo').value;
            const urgentOnly = document.getElementById('urgentOnly').checked ? '1' : '';

            const params = new URLSearchParams();
            if (status) params.append('completion_status', status);
            if (photographer) params.append('photographer_id', photographer);
            if (dateFrom) params.append('date_from', dateFrom);
            if (dateTo) params.append('date_to', dateTo);
            if (urgentOnly) params.append('urgent_only', urgentOnly);

            window.location.href = `{{ route('owner.photographer-tasks.index') }}?${params.toString()}`;
        }

        // Refresh tasks
        function refreshTasks() {
            location.reload();
        }

        // Send reminder
        $(document).on('click', '.send-reminder', function() {
            const taskId = $(this).data('task-id');

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
        });

        // Auto refresh setiap 5 menit
        setInterval(function() {
            // Optional: auto refresh data tanpa reload page
            // updateTasksData();
        }, 300000); // 5 minutes

        // Flash messages
        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif

        @if(session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    </script>
@endsection
