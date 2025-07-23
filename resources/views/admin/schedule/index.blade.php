@extends('layouts.admin')

@section('page-title')
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Manajemen Jadwal</h3>
                <p class="text-subtitle text-muted">Kelola jadwal booking lapangan dan membership</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Jadwal</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.schedule.index') }}" class="btn btn-primary active">
                        <i class="bi bi-calendar3"></i> Kalender Jadwal
                    </a>
                    <a href="{{ route('admin.schedule.all-bookings') }}" class="btn btn-outline-primary">
                        <i class="bi bi-table"></i> Tabel Booking
                    </a>
                    <a href="{{ route('admin.schedule.membership') }}" class="btn btn-outline-primary">
                        <i class="bi bi-card-list"></i> Jadwal Membership
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Filter</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-4">
                            <label for="field-filter" class="form-label">Pilih Lapangan</label>
                            <select id="field-filter" class="form-select">
                                <option value="">Semua Lapangan</option>
                                @foreach($fields as $field)
                                    <option value="{{ $field->id }}">{{ $field->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Keterangan Warna</label>
                            <div class="d-flex align-items-center mb-2">
                                <div class="color-box me-2" style="background-color: #198754;"></div>
                                <span>Booking Reguler</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="color-box me-2" style="background-color: #FFC107;"></div>
                                <span>Booking Pending</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <div class="color-box me-2" style="background-color: #435EBE;"></div>
                                <span>Booking Membership</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Booking -->
    <div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingDetailModalLabel">Detail Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Lapangan</th>
                                    <td id="booking-field"></td>
                                </tr>
                                <tr>
                                    <th>Tanggal</th>
                                    <td id="booking-date"></td>
                                </tr>
                                <tr>
                                    <th>Waktu</th>
                                    <td id="booking-time"></td>
                                </tr>
                                <tr>
                                    <th>Pemesan</th>
                                    <td id="booking-user"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="booking-status"></td>
                                </tr>
                                <tr>
                                    <th>Tipe Booking</th>
                                    <td id="booking-type"></td>
                                </tr>
                                <tr class="membership-info" style="display: none;">
                                    <th>Paket Membership</th>
                                    <td id="booking-membership"></td>
                                </tr>
                                <tr class="membership-info" style="display: none;">
                                    <th>Sesi</th>
                                    <td id="booking-session"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-3 photographer-info" style="display: none;">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Fotografer</h5>
                                </div>
                                <div class="card-body">
                                    <p id="photographer-name"></p>
                                </div>
                            </div>

                            <div class="card rental-info" style="display: none;">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">Item Rental</h5>
                                </div>
                                <div class="card-body">
                                    <div id="rental-items-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    {{-- <a href="#" class="btn btn-warning" id="edit-booking-btn">Edit Booking</a> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- CSS for fullcalendar -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="{{ asset('assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">

    <style>
        .color-box {
            width: 20px;
            height: 20px;
            border-radius: 3px;
        }
        .fc-event {
            cursor: pointer;
        }
    </style>

    <!-- Fullcalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const fieldFilter = document.getElementById('field-filter');

            const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'timeGridWeek',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    buttonText: {
        today:    'Hari Ini',
        month:    'Bulan',
        week:     'Minggu',
        day:      'Hari',
        list:     'Agenda'
    },
    slotMinTime: '08:00:00',
    slotMaxTime: '23:00:00',
    allDaySlot: false,
    height: 'auto',
    locale: 'id',
    nowIndicator: true,
    eventTimeFormat: {
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    },
    events: function(fetchInfo, successCallback, failureCallback) {
        fetch(`{{ route('admin.schedule.events') }}?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}&field_id=${fieldFilter.value}`)
            .then(response => response.json())
            .then(data => {
                successCallback(data);
            })
            .catch(error => {
                console.error('Error fetching events:', error);
                failureCallback(error);
            });
    },
    eventClick: function(info) {
        const bookingId = info.event.id;
        showBookingDetails(bookingId);
    }
});

            calendar.render();

            // Apply field filter
            fieldFilter.addEventListener('change', function() {
                calendar.refetchEvents();
            });

            // Function to show booking details
// Update pada fungsi JavaScript untuk menampilkan detail booking

function showBookingDetails(bookingId) {
    fetch(`{{ url('admin/schedule/booking') }}/${bookingId}`)
        .then(response => response.json())
        .then(data => {
            const booking = data.booking;

            // Fill modal with booking details
            document.getElementById('booking-field').textContent = booking.field.name;
            document.getElementById('booking-date').textContent = new Date(booking.start_time).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            document.getElementById('booking-time').textContent = new Date(booking.start_time).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            }) + ' - ' + new Date(booking.end_time).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            document.getElementById('booking-user').textContent = booking.user.name;

            // Status with badge
            let statusBadge = '';
            switch(booking.status) {
                case 'confirmed':
                    statusBadge = '<span class="badge bg-success">Confirmed</span>';
                    break;
                case 'pending':
                    statusBadge = '<span class="badge bg-warning">Pending</span>';
                    break;
                case 'cancelled':
                    statusBadge = '<span class="badge bg-danger">Cancelled</span>';
                    break;
                default:
                    statusBadge = '<span class="badge bg-secondary">' + booking.status + '</span>';
            }
            document.getElementById('booking-status').innerHTML = statusBadge;

            // Type of booking
            document.getElementById('booking-type').textContent = booking.is_membership ? 'Membership' : 'Regular';

            // Show/hide membership info
            const membershipInfoElements = document.querySelectorAll('.membership-info');
            if (booking.is_membership && booking.membership_session) {
                membershipInfoElements.forEach(el => el.style.display = 'table-row');

                // Fill membership info
                if (booking.membership_session.subscription &&
                    booking.membership_session.subscription.membership) {
                    document.getElementById('booking-membership').textContent =
                        booking.membership_session.subscription.membership.name;
                    document.getElementById('booking-session').textContent =
                        'Sesi ' + booking.membership_session.session_number;
                }
            } else {
                membershipInfoElements.forEach(el => el.style.display = 'none');
            }

            // Show/hide photographer info
            const photographerInfo = document.querySelector('.photographer-info');
            if (booking.photographer_bookings && booking.photographer_bookings.length > 0) {
                photographerInfo.style.display = 'block';
                const photographerName = booking.photographer_bookings[0].photographer ?
                    booking.photographer_bookings[0].photographer.name : 'Nama tidak tersedia';
                document.getElementById('photographer-name').textContent = photographerName;
            } else {
                photographerInfo.style.display = 'none';
            }

            // Show/hide rental info
            const rentalInfo = document.querySelector('.rental-info');
            const rentalItemsList = document.getElementById('rental-items-list');

            // Improved code to check rental bookings
            console.log('Rental bookings:', booking.rental_bookings); // Debug log

            if (booking.rental_bookings && booking.rental_bookings.length > 0) {
                rentalInfo.style.display = 'block';
                rentalItemsList.innerHTML = '';

                booking.rental_bookings.forEach(rentalBooking => {
                    if (rentalBooking.rental_item) {
                        const itemHtml = `
                            <div class="d-flex justify-content-between mb-2">
                                <div>${rentalBooking.rental_item.name}</div>
                                <div><span class="badge bg-dark">${rentalBooking.quantity} item</span></div>
                            </div>
                        `;
                        rentalItemsList.innerHTML += itemHtml;
                    } else {
                        console.log('Missing rental item data for booking:', rentalBooking.id);
                    }
                });
            } else {
                rentalInfo.style.display = 'none';
                console.log('No rental bookings found for this field booking');
            }

            // Set edit button URL
            // document.getElementById('edit-booking-btn').href = `{{ url('admin/schedule/booking') }}/${booking.id}/edit`;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error fetching booking details:', error);
            Swal.fire({
                title: 'Error',
                text: 'Terjadi kesalahan saat mengambil detail booking',
                icon: 'error'
            });
        });
}
        });
    </script>
    <style>
        .fc-prev-button, .fc-next-button, .fc-today-button {
          margin-right: 10px !important;
        }
        .fc-dayGridMonth-button,
.fc-timeGridWeek-button {
  margin-right: 8px !important; /* Tombol timeGridDay tidak perlu margin kanan */
}
      </style>
@endsection
