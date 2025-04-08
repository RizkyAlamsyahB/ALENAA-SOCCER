@extends('layouts.app')
@section('content')
    <!-- Modern styling with custom fonts and better UI -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSRF Token untuk AJAX Requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Hero Section with Gradient Overlay -->
    <div class="hero-section" style="margin-top: 50px;">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Atur Jadwal Membership</h1>
                <div class="breadcrumb-wrapper">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('users.dashboard') }}"><i class="fas fa-home"></i>
                                    Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('user.membership.index') }}">Membership</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('user.membership.show', $membership->id) }}">{{ $membership->name }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Pilih Jadwal</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5 mb-5">
        <!-- Alerts -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
                            <div class="alert-message">{{ session('success') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
                            <div class="alert-message">{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Schedule Card -->
                <div class="card card-schedule">
                    <div class="card-header">
                        <h4 class="mb-0">Pilih Jadwal Membership</h4>
                    </div>
                    <div class="card-body">
                        <!-- Information Card -->
                        <div class="info-card">
                            <div class="info-card-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="info-card-content">
                                <h5>Informasi Penting</h5>
                                <p>Pilih 3 slot jadwal permainan dalam rentang 7 hari dari tanggal yang Anda pilih. Jadwal
                                    yang dipilih akan menjadi jadwal tetap selama masa membership.</p>
                            </div>
                        </div>

                        <form action="{{ route('user.membership.save.schedule', $membership->id) }}" method="POST"
                            id="scheduleForm">
                            @csrf
                            <div class="schedule-steps">
                                <!-- Step 1: Pick Week -->
                                <div class="schedule-step active" id="step-1">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h5>Pilih Minggu</h5>
                                        <div class="week-picker-wrapper">
                                            <div id="week-picker" class="mb-3"></div>
                                            <div class="selected-week-display" id="selected-week-display">
                                                <div class="empty-state">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <p>Belum ada minggu yang dipilih</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Pick Sessions -->
                                <div class="schedule-step" id="step-2">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h5>Pilih 3 Jadwal</h5>
                                        <div class="session-cards">
                                            <!-- Session 1 -->
                                            <div class="session-card">
                                                <div class="session-card-header">
                                                    <span class="session-number">1</span>
                                                    <h6>Jadwal Pertama</h6>
                                                </div>
                                                <div class="session-card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Hari</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-calendar-day"></i></span>
                                                                <select class="form-select session-day"
                                                                    name="sessions[0][day]" required disabled>
                                                                    <option value="">Pilih Hari</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Jam</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-clock"></i></span>
                                                                <select class="form-select session-time"
                                                                    name="sessions[0][time]" required disabled>
                                                                    <option value="">Pilih Jam</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Session 2 -->
                                            <div class="session-card">
                                                <div class="session-card-header">
                                                    <span class="session-number">2</span>
                                                    <h6>Jadwal Kedua</h6>
                                                </div>
                                                <div class="session-card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Hari</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-calendar-day"></i></span>
                                                                <select class="form-select session-day"
                                                                    name="sessions[1][day]" required disabled>
                                                                    <option value="">Pilih Hari</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Jam</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-clock"></i></span>
                                                                <select class="form-select session-time"
                                                                    name="sessions[1][time]" required disabled>
                                                                    <option value="">Pilih Jam</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Session 3 -->
                                            <div class="session-card">
                                                <div class="session-card-header">
                                                    <span class="session-number">3</span>
                                                    <h6>Jadwal Ketiga</h6>
                                                </div>
                                                <div class="session-card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Hari</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-calendar-day"></i></span>
                                                                <select class="form-select session-day"
                                                                    name="sessions[2][day]" required disabled>
                                                                    <option value="">Pilih Hari</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Jam</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i
                                                                        class="fas fa-clock"></i></span>
                                                                <select class="form-select session-time"
                                                                    name="sessions[2][time]" required disabled>
                                                                    <option value="">Pilih Jam</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirmation -->
                                <div class="schedule-step" id="step-3">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h5>Konfirmasi Jadwal</h5>
                                        <div class="warning-card">
                                            <div class="warning-card-icon">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                            <div class="warning-card-content">
                                                <h5>Perhatian</h5>
                                                <p>Jadwal yang dipilih tidak dapat diubah selama masa membership. Pastikan
                                                    Anda memilih jadwal yang sesuai dengan ketersediaan Anda.</p>
                                            </div>
                                        </div>

                                        <div class="selected-schedule-summary" id="schedule-summary">
                                            <h6>Ringkasan Jadwal Pilihan Anda</h6>
                                            <div class="summary-empty" id="summary-empty">
                                                <p>Silahkan pilih jadwal terlebih dahulu</p>
                                            </div>
                                            <div class="summary-content" id="summary-content" style="display:none;">
                                                <div class="summary-item" id="summary-week">
                                                    <i class="fas fa-calendar-week"></i>
                                                    <span>Minggu: <span id="selected-week-summary">-</span></span>
                                                </div>
                                                <div class="summary-item" id="summary-session-1">
                                                    <i class="fas fa-calendar-check"></i>
                                                    <span>Jadwal 1: <span id="selected-session-1-summary">-</span></span>
                                                </div>
                                                <div class="summary-item" id="summary-session-2">
                                                    <i class="fas fa-calendar-check"></i>
                                                    <span>Jadwal 2: <span id="selected-session-2-summary">-</span></span>
                                                </div>
                                                <div class="summary-item" id="summary-session-3">
                                                    <i class="fas fa-calendar-check"></i>
                                                    <span>Jadwal 3: <span id="selected-session-3-summary">-</span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions mt-4">
                                <a href="{{ route('user.membership.show', $membership->id) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                    <i class="fas fa-cart-plus me-2"></i>Tambahkan ke Keranjang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    let selectedWeekStart = null;
    let selectedWeekEnd = null;
    let canSubmit = false;
    let selectedSchedules = []; // Untuk menyimpan jadwal yang dipilih
    const fieldId = {{ $field->id }}; // Ambil ID field dari Blade template

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Initialize Flatpickr for week selection
    const weekPicker = flatpickr("#week-picker", {
        inline: true,
        locale: 'id',
        dateFormat: "Y-m-d",
        minDate: "today",
        maxDate: new Date().fp_incr(6), // Maksimal 7 hari ke depan

        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                // Mulai minggu dari tanggal yang dipilih
                selectedWeekStart = new Date(selectedDates[0]);
                selectedWeekEnd = new Date(selectedWeekStart);
                selectedWeekEnd.setDate(selectedWeekStart.getDate() + 6);

                // Format dates for display
                const formattedStart = formatDate(selectedWeekStart);
                const formattedEnd = formatDate(selectedWeekEnd);

                // Update the week display
                document.getElementById('selected-week-display').innerHTML = `
                    <div class="selected-week-content">
                        <div class="selected-week-info">
                            <span class="week-label">Minggu Terpilih:</span>
                            <span class="week-value">${formattedStart} - ${formattedEnd}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="change-week-btn">
                            <i class="fas fa-exchange-alt me-1"></i> Ubah
                        </button>
                    </div>
                `;

                // Update summary
                document.getElementById('selected-week-summary').textContent =
                    `${formattedStart} - ${formattedEnd}`;

                // Activate step 2
                document.getElementById('step-1').classList.add('completed');
                document.getElementById('step-2').classList.add('active');

                // Populate day selectors with days of the selected week
                populateDaySelectors();

                // Add event listener to change week button
                document.getElementById('change-week-btn').addEventListener('click', function() {
                    resetSelection();
                });

                // Hide the calendar
                weekPicker._input.style.display = 'none';
            }
        }
    });

    // Format date for display
    function formatDate(date) {
        return date.toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    // Reset the selection
    function resetSelection() {
        // Reset variables
        selectedWeekStart = null;
        selectedWeekEnd = null;
        canSubmit = false;
        selectedSchedules = []; // Reset pilihan jadwal

        // Reset week display
        document.getElementById('selected-week-display').innerHTML = `
            <div class="empty-state">
                <i class="fas fa-calendar-alt"></i>
                <p>Belum ada minggu yang dipilih</p>
            </div>
        `;

        // Reset steps
        document.getElementById('step-1').classList.remove('completed');
        document.getElementById('step-2').classList.remove('active');
        document.getElementById('step-2').classList.remove('completed');
        document.getElementById('step-3').classList.remove('active');

        // Show calendar
        weekPicker._input.style.display = 'block';

        // Disable day and time selectors
        const daySelectors = document.querySelectorAll('.session-day');
        const timeSelectors = document.querySelectorAll('.session-time');

        daySelectors.forEach(selector => {
            selector.disabled = true;
            selector.innerHTML = '<option value="">Pilih Hari</option>';
        });

        timeSelectors.forEach(selector => {
            selector.disabled = true;
            selector.innerHTML = '<option value="">Pilih Jam</option>';
        });

        // Reset summary
        document.getElementById('summary-content').style.display = 'none';
        document.getElementById('summary-empty').style.display = 'block';
        document.getElementById('selected-week-summary').textContent = '-';
        document.getElementById('selected-session-1-summary').textContent = '-';
        document.getElementById('selected-session-2-summary').textContent = '-';
        document.getElementById('selected-session-3-summary').textContent = '-';

        // Disable submit button
        document.getElementById('submit-btn').disabled = true;
    }

    // Populate day selectors with days of the selected week
    function populateDaySelectors() {
        const daySelectors = document.querySelectorAll('.session-day');

        daySelectors.forEach(selector => {
            // Enable selector
            selector.disabled = false;

            // Clear options
            selector.innerHTML = '<option value="">Pilih Hari</option>';

            // Add days - mulai dari tanggal yang dipilih hingga 6 hari ke depan
            for (let i = 0; i < 7; i++) {
                const day = new Date(selectedWeekStart);
                day.setDate(selectedWeekStart.getDate() + i);

                // FIX: Gunakan format yang konsisten untuk nilai tanggal
                // Buat string dengan format YYYY-MM-DD secara manual
                const year = day.getFullYear();
                const month = String(day.getMonth() + 1).padStart(2, '0');
                const date = String(day.getDate()).padStart(2, '0');
                const isoDate = `${year}-${month}-${date}`;

                const option = document.createElement('option');
                option.value = isoDate; // Format YYYY-MM-DD yang konsisten
                option.textContent = day.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long'
                });

                selector.appendChild(option);
            }

            // Add event listener for change
            selector.addEventListener('change', handleDaySelection);
        });
    }

    // Handle day selection
    function handleDaySelection(event) {
        const daySelector = event.target;
        const sessionIndex = Array.from(document.querySelectorAll('.session-day')).indexOf(daySelector);
        const timeSelector = document.querySelectorAll('.session-time')[sessionIndex];

        if (daySelector.value) {
            // Enable time selector
            timeSelector.disabled = false;

            // Clear options
            timeSelector.innerHTML = '<option value="">Pilih Jam</option>';

            // Tambahkan loading state
            const loadingOption = document.createElement('option');
            loadingOption.value = "";
            loadingOption.textContent = "Memuat slot tersedia...";
            timeSelector.appendChild(loadingOption);

            // Get available time slots based on field's available times and existing bookings
            getAvailableSlotsForDay(daySelector.value, fieldId)
                .then(availableSlots => {
                    // Clear options including loading option
                    timeSelector.innerHTML = '<option value="">Pilih Jam</option>';

                    if (availableSlots.length === 0) {
                        const option = document.createElement('option');
                        option.value = "";
                        option.textContent = "Tidak ada slot tersedia";
                        option.disabled = true;
                        timeSelector.appendChild(option);
                    } else {
                        availableSlots.forEach(slot => {
                            const option = document.createElement('option');
                            option.value = `${slot.start} - ${slot.end}`;
                            option.textContent = `${slot.start} - ${slot.end}`;
                            timeSelector.appendChild(option);
                        });
                    }

                    // Add event listener for change
                    timeSelector.addEventListener('change', function(e) {
                        if (e.target.value) {
                            const dayText = daySelector.options[daySelector.selectedIndex].text;
                            const dayValue = daySelector.value;
                            const timeText = e.target.value;
                            const timeValue = e.target.value;

                            // Cek apakah kombinasi hari dan jam sudah dipilih sebelumnya
                            const isDuplicate = selectedSchedules.some(schedule =>
                                schedule.day === dayValue && schedule.time === timeValue
                            );

                            if (isDuplicate) {
                                // Tampilkan pesan error
                                alert("Jadwal ini sudah dipilih. Silakan pilih hari atau jam yang berbeda.");
                                // Reset pilihan
                                e.target.value = "";
                                return;
                            }

                            // Hapus jadwal lama jika ada perubahan
                            selectedSchedules = selectedSchedules.filter(schedule => schedule.index !== sessionIndex);

                            // Tambahkan jadwal baru
                            selectedSchedules.push({
                                index: sessionIndex,
                                day: dayValue,
                                time: timeValue
                            });

                            // Update summary
                            document.getElementById(`selected-session-${sessionIndex+1}-summary`).textContent =
                                `${dayText}, ${timeText}`;

                            // Show summary content
                            document.getElementById('summary-content').style.display = 'block';
                            document.getElementById('summary-empty').style.display = 'none';
                        }

                        checkFormCompletion();
                    });

                    checkFormCompletion();
                })
                .catch(error => {
                    console.error('Error fetching available slots:', error);
                    // Show error message
                    timeSelector.innerHTML = '<option value="">Error memuat slot</option>';
                });
        } else {
            // Disable time selector
            timeSelector.disabled = true;
            timeSelector.innerHTML = '<option value="">Pilih Jam</option>';
            document.getElementById(`selected-session-${sessionIndex+1}-summary`).textContent = '-';
        }

        // Check if form is complete
        checkFormCompletion();
    }

    // Fetch available slots for a specific day from server
    function getAvailableSlotsForDay(day, fieldId) {
        return new Promise((resolve, reject) => {
            fetch(`/membership/fields/${fieldId}/available-slots-membership?date=${day}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    resolve(data);
                })
                .catch(error => {
                    console.error('Error fetching available slots:', error);
                    reject(error);
                });
        });
    }

    // Check if form is complete
    function checkFormCompletion() {
        const daySelectors = document.querySelectorAll('.session-day');
        const timeSelectors = document.querySelectorAll('.session-time');
        let allSelected = true;

        // Check if all sessions have been selected
        for (let i = 0; i < 3; i++) {
            if (!daySelectors[i].value || !timeSelectors[i].value) {
                allSelected = false;
                break;
            }
        }

        // Enable or disable submit button
        document.getElementById('submit-btn').disabled = !allSelected;

        // Activate step 3 if all selected
        if (allSelected) {
            document.getElementById('step-2').classList.add('completed');
            document.getElementById('step-3').classList.add('active');
        } else {
            document.getElementById('step-2').classList.remove('completed');
            document.getElementById('step-3').classList.remove('active');
        }
    }

    // Tambahkan hidden field untuk tanggal saat ini
    function addHiddenTodayField() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const date = String(today.getDate()).padStart(2, '0');
        const todayDate = `${year}-${month}-${date}`;

        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = 'today_date';
        hiddenField.value = todayDate;

        document.getElementById('scheduleForm').appendChild(hiddenField);
    }

    // Panggil fungsi untuk menambahkan tanggal hari ini
    addHiddenTodayField();

    // Form submission
    document.getElementById('scheduleForm').addEventListener('submit', function(event) {
        // Disable submit button to prevent double submission
        document.getElementById('submit-btn').disabled = true;

        // Show loading state
        const originalButtonText = document.getElementById('submit-btn').innerHTML;
        document.getElementById('submit-btn').innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            <span class="ms-2">Memproses...</span>
        `;

        // Tambahkan debug info
        console.log('Submitting form with selected schedules:', selectedSchedules);

        // Form will be submitted normally
        return true;
    });
});
</script>

    <style>
        /* Modern Design System */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }

        .hero-section {
            background: linear-gradient(135deg, #d00f25 0%, #9e0620 100%);
            height: 220px;
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
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

        /* Card Design */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card-schedule {
            margin-bottom: 2rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem 1.5rem;
        }

        .card-header h4 {
            font-weight: 600;
            color: #212529;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Information Card */
        .info-card,
        .warning-card {
            display: flex;
            background-color: #e8f4ff;
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 2rem;
            border-left: 4px solid #2196f3;
        }

        .warning-card {
            background-color: #fff8e1;
            border-left-color: #ffc107;
        }

        .info-card-icon,
        .warning-card-icon {
            font-size: 1.5rem;
            color: #2196f3;
            margin-right: 1rem;
            display: flex;
            align-items: center;
        }

        .warning-card-icon {
            color: #ffc107;
        }

        .info-card-content h5,
        .warning-card-content h5 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .info-card-content p,
        .warning-card-content p {
            font-size: 0.95rem;
            margin-bottom: 0;
            color: #495057;
        }

        /* Step system */
        .schedule-steps {
            margin-bottom: 2rem;
        }

        .schedule-step {
            display: flex;
            margin-bottom: 2rem;
            opacity: 0.5;
            transition: all 0.3s ease;
        }

        .schedule-step.active {
            opacity: 1;
        }

        .schedule-step.completed .step-number {
            background-color: #28a745;
            color: white;
        }

        .step-number {
            background-color: #e9ecef;
            color: #495057;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
            margin-right: 1rem;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .active .step-number {
            background-color: #d00f25;
            color: white;
        }

        .step-content {
            flex-grow: 1;
        }

        .step-content h5 {
            font-weight: 600;
            margin-bottom: 1.2rem;
            color: #212529;
        }

        /* Week Picker */
        .week-picker-wrapper {
            background-color: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .flatpickr-calendar {
            width: 100% !important;
            max-width: 100%;
            box-shadow: none !important;
            border: none;
            padding: 1rem;
            border-radius: 12px;
        }

        .flatpickr-day {
            border-radius: 8px;
            margin: 2px;
            height: 38px;
            line-height: 38px;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange,
        .flatpickr-day.selected:focus,
        .flatpickr-day.startRange:focus,
        .flatpickr-day.endRange:focus,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover,
        .flatpickr-day.selected.prevMonthDay,
        .flatpickr-day.startRange.prevMonthDay,
        .flatpickr-day.endRange.prevMonthDay,
        .flatpickr-day.selected.nextMonthDay,
        .flatpickr-day.startRange.nextMonthDay,
        .flatpickr-day.endRange.nextMonthDay {
            background: #d00f25;
            border-color: #d00f25;
            color: #fff;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 600;
        }

        .selected-week-display {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 0.8rem;
            opacity: 0.5;
        }

        .empty-state p {
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        .selected-week-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .selected-week-info {
            display: flex;
            flex-direction: column;
        }

        .week-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.3rem;
        }

        .week-value {
            font-weight: 600;
            color: #212529;
        }

        /* Session Cards */
        .session-cards {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .session-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .session-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .session-card-header {
            background-color: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
        }

        .session-number {
            width: 28px;
            height: 28px;
            background-color: #d00f25;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            margin-right: 0.8rem;
        }

        .session-card-header h6 {
            margin-bottom: 0;
            font-weight: 600;
            color: #212529;
        }

        .session-card-body {
            padding: 1rem;
        }

        /* Form Controls */
        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .form-select,
        .form-control {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            border: 1px solid #ced4da;
            font-size: 0.95rem;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #d00f25;
            box-shadow: 0 0 0 0.2rem rgba(208, 15, 37, 0.25);
        }

        .input-group-text {
            border-radius: 8px 0 0 8px;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            border-right: none;
        }

        /* Summary Section */
        .selected-schedule-summary {
            background-color: white;
            border-radius: 12px;
            padding: 1.2rem;
            margin-top: 1.5rem;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .selected-schedule-summary h6 {
            font-weight: 600;
            margin-bottom: 1rem;
            color: #212529;
            font-size: 1rem;
        }

        .summary-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
        }

        .summary-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .summary-item i {
            color: #d00f25;
            margin-right: 0.8rem;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }

        .summary-item span {
            color: #495057;
        }

        .summary-empty {
            padding: 1rem;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #d00f25;
            border-color: #d00f25;
            flex-grow: 1;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #b00d1f;
            border-color: #b00d1f;
            box-shadow: 0 4px 10px rgba(208, 15, 37, 0.25);
        }

        .btn-outline-primary {
            color: #d00f25;
            border-color: #d00f25;
        }

        .btn-outline-primary:hover {
            background-color: #d00f25;
            border-color: #d00f25;
        }

        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
            flex-grow: 1;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
            color: white;
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border: none;
        }

        .alert-success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .alert-danger {
            background-color: #ffebee;
            color: #c62828;
        }

        .alert-icon {
            margin-right: 0.8rem;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
        }

        .alert-message {
            flex-grow: 1;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-section {
                height: 180px;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .breadcrumb {
                padding: 0.6rem 1rem;
            }

            .breadcrumb-item {
                font-size: 0.8rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .selected-week-content {
                flex-direction: column;
                gap: 0.8rem;
                align-items: flex-start;
            }

            .card-body {
                padding: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.5rem;
            }

            .step-number {
                width: 32px;
                height: 32px;
                font-size: 0.9rem;
            }

            .card-header {
                padding: 1.2rem;
            }

            .card-header h4 {
                font-size: 1.2rem;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
@endsection
