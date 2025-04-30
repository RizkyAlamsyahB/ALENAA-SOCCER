@extends('layouts.photographers')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Jadwal Saya</h3>
                <p class="text-subtitle text-muted">Lihat dan kelola semua jadwal booking Anda.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('photographers.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Jadwal Saya</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Jadwal Booking</h4>
                <div class="card-tools">
                    <button class="btn btn-primary btn-sm rounded-3" id="calendarViewBtn">
                        <i class="bi bi-calendar3"></i> Tampilan Kalender
                    </button>
                    <button class="btn btn-outline-primary btn-sm rounded-3" id="listViewBtn">
                        <i class="bi bi-list-ul"></i> Tampilan List
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterMonth">Filter Bulan</label>
                            <select class="form-select" id="filterMonth">
                                <option value="">Semua Bulan</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterType">Tipe Booking</label>
                            <select class="form-select" id="filterType">
                                <option value="">Semua Tipe</option>
                                <option value="photographer">Fotografer</option>
                                <option value="field">Lapangan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterStatus">Status</label>
                            <select class="form-select" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="pending">Menunggu</option>
                                <option value="confirmed">Dikonfirmasi</option>
                                <option value="completed">Selesai</option>
                                <option value="cancelled">Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100" id="applyFilter">
                            <i class="bi bi-funnel-fill"></i> Terapkan Filter
                        </button>
                    </div>
                </div>

                <!-- List View (Default) -->
                <div id="listView">
                    <div class="table-responsive">
                        <table class="table table-striped" id="bookingsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tipe</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Pelanggan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allBookings as $booking)
                                    <tr>
                                        <td>{{ $booking['id'] }}</td>
                                        <td>
                                            @if ($booking['type'] == 'photographer')
                                                <span class="badge bg-primary">Fotografer</span>
                                            @else
                                                <span class="badge bg-success">Lapangan</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($booking['start_time'])->format('d M Y') }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($booking['start_time'])->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($booking['end_time'])->format('H:i') }}
                                        </td>
                                        <td>{{ $booking['user'] }}</td>
                                        <td>
                                            @if ($booking['status'] == 'pending')
                                                <span class="badge bg-warning">Menunggu</span>
                                            @elseif ($booking['status'] == 'confirmed')
                                                <span class="badge bg-success">Dikonfirmasi</span>
                                            @elseif ($booking['status'] == 'completed')
                                                <span class="badge bg-info">Selesai</span>
                                            @elseif ($booking['status'] == 'cancelled')
                                                <span class="badge bg-danger">Dibatalkan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info view-booking"
                                                    data-id="{{ $booking['id'] }}"
                                                    data-type="{{ $booking['type'] }}">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                @if ($booking['status'] == 'pending')
                                                    <button type="button" class="btn btn-sm btn-success confirm-booking"
                                                        data-id="{{ $booking['id'] }}"
                                                        data-type="{{ $booking['type'] }}">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Calendar View (Hidden by default) -->
                <div id="calendarView" style="display: none;">
                    <div id="bookingCalendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Booking Modal -->
    <div class="modal fade" id="viewBookingModal" tabindex="-1" aria-labelledby="viewBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewBookingModalLabel">Detail Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="bookingDetails">
                        <!-- Detail booking akan diisi melalui JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Booking Modal -->
    <div class="modal fade" id="confirmBookingModal" tabindex="-1" aria-labelledby="confirmBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmBookingModalLabel">Konfirmasi Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin mengkonfirmasi booking ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" id="confirmBookingBtn">Ya, Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Dependencies -->
    <script src="{{ asset('assets/extensions/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">

    <!-- FullCalendar -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>

    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configure Toastr options
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right",
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Flash messages
            @if (session('success'))
                toastr.success('{{ session('success') }}', 'Berhasil');
            @endif

            @if (session('error'))
                toastr.error('{{ session('error') }}', 'Error');
            @endif

            // Initialize DataTable
            var table = $('#bookingsTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                }
            });

            // Toggle between List and Calendar views
            $('#listViewBtn').click(function() {
                $('#calendarView').hide();
                $('#listView').show();
                $('#calendarViewBtn').removeClass('btn-outline-primary').addClass('btn-primary');
                $('#listViewBtn').removeClass('btn-primary').addClass('btn-outline-primary');
            });

            $('#calendarViewBtn').click(function() {
                $('#listView').hide();
                $('#calendarView').show();
                $('#listViewBtn').removeClass('btn-outline-primary').addClass('btn-primary');
                $('#calendarViewBtn').removeClass('btn-primary').addClass('btn-outline-primary');

                // Initialize calendar if not already initialized
                if (!calendarInitialized) {
                    initializeCalendar();
                }
            });

            // Filter functionality
            $('#applyFilter').click(function() {
                var month = $('#filterMonth').val();
                var type = $('#filterType').val();
                var status = $('#filterStatus').val();

                // Reset the filter first
                table.search('').columns().search('').draw();

                // Apply filter to DataTable
                if (type) {
                    table.column(1).search(type === 'photographer' ? 'Fotografer' : 'Lapangan').draw();
                }

                if (status) {
                    let statusText = '';
                    switch(status) {
                        case 'pending': statusText = 'Menunggu'; break;
                        case 'confirmed': statusText = 'Dikonfirmasi'; break;
                        case 'completed': statusText = 'Selesai'; break;
                        case 'cancelled': statusText = 'Dibatalkan'; break;
                    }
                    table.column(5).search(statusText).draw();
                }

                // Filter by month (this is more complex and might need custom filtering)
                if (month) {
                    // Create a custom filter function
                    $.fn.dataTable.ext.search.push(
                        function(settings, data, dataIndex) {
                            var dateStr = data[2]; // column index of date
                            if (!dateStr) return true;

                            // Parse date from format "dd MMM YYYY"
                            var dateParts = dateStr.split(' ');
                            var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                            var monthIndex = monthNames.indexOf(dateParts[1]) + 1;

                            return monthIndex == month;
                        }
                    );
                    table.draw();
                    // Remove our custom filter function
                    $.fn.dataTable.ext.search.pop();
                }

                // Update calendar view if it's visible
                if ($('#calendarView').is(':visible') && calendarInitialized) {
                    calendar.refetchEvents();
                }
            });

            // Initialize the calendar
            var calendar;
            var calendarInitialized = false;

            function initializeCalendar() {
                var calendarEl = document.getElementById('bookingCalendar');

                // Menambahkan CSS kustom untuk tombol
                var customCSS = document.createElement('style');
                customCSS.innerHTML = `
                    .fc-button {
                        margin: 0 5px !important;
                        padding: 6px 12px !important;
                    }
                    .fc-toolbar-chunk {
                        display: flex;
                        gap: 8px;
                    }
                `;
                document.head.appendChild(customCSS);

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    buttonText: {
                        today: 'Hari Ini',
                        month: 'Bulan',
                        week: 'Minggu',
                        day: 'Hari'
                    },
                    events: function(info, successCallback, failureCallback) {
                        // Convert all bookings to calendar events
                        var events = [];
                        @foreach ($allBookings as $booking)
                            var color = '{{ $booking['type'] }}' === 'photographer' ? '#3788d8' : '#28a745';

                            // Change color based on status
                            @if ($booking['status'] == 'pending')
                                color = '#ffc107';
                            @elseif ($booking['status'] == 'confirmed')
                                color = '{{ $booking['type'] }}' === 'photographer' ? '#3788d8' : '#28a745';
                            @elseif ($booking['status'] == 'completed')
                                color = '#17a2b8';
                            @elseif ($booking['status'] == 'cancelled')
                                color = '#dc3545';
                            @endif

                            events.push({
                                id: '{{ $booking['id'] }}',
                                title: '{{ $booking['type'] }}' === 'photographer' ? 'Foto: {{ $booking['user'] }}' : 'Lap: {{ $booking['user'] }}',
                                start: '{{ $booking['start_time'] }}',
                                end: '{{ $booking['end_time'] }}',
                                color: color,
                                extendedProps: {
                                    type: '{{ $booking['type'] }}',
                                    status: '{{ $booking['status'] }}',
                                    user: '{{ $booking['user'] }}'
                                }
                            });
                        @endforeach

                        // Apply filters
                        var month = $('#filterMonth').val();
                        var type = $('#filterType').val();
                        var status = $('#filterStatus').val();

                        if (month || type || status) {
                            events = events.filter(function(event) {
                                let monthMatch = true, typeMatch = true, statusMatch = true;

                                // Month filter
                                if (month) {
                                    var eventMonth = new Date(event.start).getMonth() + 1;
                                    monthMatch = (eventMonth == month);
                                }

                                // Type filter
                                if (type) {
                                    typeMatch = (event.extendedProps.type === type);
                                }

                                // Status filter
                                if (status) {
                                    statusMatch = (event.extendedProps.status === status);
                                }

                                return monthMatch && typeMatch && statusMatch;
                            });
                        }

                        successCallback(events);
                    },
                    eventClick: function(info) {
                        // Show booking details modal
                        showBookingDetails(info.event.id, info.event.extendedProps.type);
                    }
                });

                calendar.render();
                calendarInitialized = true;
            }

            // View Booking Details
            $(document).on('click', '.view-booking', function() {
                var bookingId = $(this).data('id');
                var bookingType = $(this).data('type');
                showBookingDetails(bookingId, bookingType);
            });

            function showBookingDetails(bookingId, bookingType) {
                // Fetch booking details via AJAX
                $.ajax({
                    url: '{{ url("photographers/booking-details") }}/' + bookingId + '/' + bookingType,
                    type: 'GET',
                    success: function(response) {
                        // Populate modal with booking details
                        var html = '';
                        if (response.success) {
                            var booking = response.data;
                            html += '<div class="row">';
                            html += '<div class="col-md-6"><strong>ID:</strong></div>';
                            html += '<div class="col-md-6">' + booking.id + '</div>';
                            html += '</div><hr>';

                            html += '<div class="row">';
                            html += '<div class="col-md-6"><strong>Tipe:</strong></div>';
                            html += '<div class="col-md-6">' + (booking.type === 'photographer' ? 'Fotografer' : 'Lapangan') + '</div>';
                            html += '</div><hr>';

                            html += '<div class="row">';
                            html += '<div class="col-md-6"><strong>Tanggal:</strong></div>';
                            html += '<div class="col-md-6">' + booking.date + '</div>';
                            html += '</div><hr>';

                            html += '<div class="row">';
                            html += '<div class="col-md-6"><strong>Waktu:</strong></div>';
                            html += '<div class="col-md-6">' + booking.time_range + '</div>';
                            html += '</div><hr>';

                            html += '<div class="row">';
                            html += '<div class="col-md-6"><strong>Pelanggan:</strong></div>';
                            html += '<div class="col-md-6">' + booking.user_name + '</div>';
                            html += '</div><hr>';

                            html += '<div class="row">';
                            html += '<div class="col-md-6"><strong>Status:</strong></div>';
                            html += '<div class="col-md-6">';
                            switch(booking.status) {
                                case 'pending':
                                    html += '<span class="badge bg-warning">Menunggu</span>';
                                    break;
                                case 'confirmed':
                                    html += '<span class="badge bg-success">Dikonfirmasi</span>';
                                    break;
                                case 'completed':
                                    html += '<span class="badge bg-info">Selesai</span>';
                                    break;
                                case 'cancelled':
                                    html += '<span class="badge bg-danger">Dibatalkan</span>';
                                    break;
                            }
                            html += '</div>';
                            html += '</div><hr>';

                            if (booking.notes) {
                                html += '<div class="row">';
                                html += '<div class="col-md-6"><strong>Catatan:</strong></div>';
                                html += '<div class="col-md-6">' + booking.notes + '</div>';
                                html += '</div><hr>';
                            }

                            if (booking.cancellation_reason) {
                                html += '<div class="row">';
                                html += '<div class="col-md-6"><strong>Alasan Pembatalan:</strong></div>';
                                html += '<div class="col-md-6">' + booking.cancellation_reason + '</div>';
                                html += '</div><hr>';
                            }
                        } else {
                            html = '<div class="alert alert-danger">Error: ' + response.message + '</div>';
                        }

                        $('#bookingDetails').html(html);
                        $('#viewBookingModal').modal('show');
                    },
                    error: function(xhr) {
                        toastr.error('Terjadi kesalahan saat mengambil detail booking.', 'Error');
                    }
                });
            }

            // Confirm Booking
            $(document).on('click', '.confirm-booking', function() {
                var bookingId = $(this).data('id');
                var bookingType = $(this).data('type');

                // Set confirm button data attributes
                $('#confirmBookingBtn').data('id', bookingId);
                $('#confirmBookingBtn').data('type', bookingType);

                // Show confirmation modal
                $('#confirmBookingModal').modal('show');
            });

            $('#confirmBookingBtn').click(function() {
                var bookingId = $(this).data('id');
                var bookingType = $(this).data('type');

                // Confirm booking via AJAX
                $.ajax({
                    url: '{{ url("photographers/confirm-booking") }}/' + bookingId + '/' + bookingType,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message, 'Berhasil');

                            // Close modal
                            $('#confirmBookingModal').modal('hide');

                            // Reload page after short delay
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            toastr.error(response.message, 'Error');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Terjadi kesalahan saat mengkonfirmasi booking.', 'Error');
                    }
                });
            });
        });
    </script>
@endsection
