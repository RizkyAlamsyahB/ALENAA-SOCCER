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
            <div class="col-lg-10 mx-auto">
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
            <div class="col-lg-10 mx-auto">
                <!-- Schedule Card -->
                <div class="card card-schedule">
                    <div class="card-header">
                        <h4 class="mb-0">Pilih Jadwal Membership</h4>
                    </div>
                    <div class="card-body">
                        <!-- Information Card -->
<!-- Di file view membership/schedule.blade.php -->
<div class="info-card mb-4">
    <div class="info-card-icon">
        <i class="fas fa-info-circle"></i>
    </div>
    <div class="info-card-content">
        <h5>Paket {{ ucfirst($membership->type) }}</h5>
        <p>Untuk paket {{ ucfirst($membership->type) }}, Anda perlu memilih
            <strong>{{ $requiredHours }} jam</strong> slot waktu dalam seminggu. Anda bebas memilih
            kapan saja dalam rentang 7 hari ke depan.</p>

        @if($membership->includes_photographer || $membership->includes_rental_item)
            <div class="package-includes mt-2">
                <h6>Paket ini sudah termasuk:</h6>
                <ul>
                    @if($membership->includes_photographer)
                        @php $photographer = App\Models\Photographer::find($membership->photographer_id); @endphp
                        @if($photographer)
                            <li>
                                <i class="fas fa-camera text-success"></i>
                                Fotografer {{ $photographer->name }} ({{ $membership->photographer_duration }} jam)
                            </li>
                        @endif
                    @endif

                    @if($membership->includes_rental_item)
                        @php $rentalItem = App\Models\RentalItem::find($membership->rental_item_id); @endphp
                        @if($rentalItem)
                            <li>
                                <i class="fas fa-futbol text-success"></i>
                                {{ $rentalItem->name }} ({{ $membership->rental_item_quantity }} pcs)
                            </li>
                        @endif
                    @endif
                </ul>
                <p class="text-muted small">Fotografer dan perlengkapan akan tersedia sesuai dengan jadwal yang Anda pilih.</p>
            </div>
        @endif
    </div>
</div>

                        <form action="{{ route('user.membership.save.schedule', $membership->id) }}" method="POST"
                            id="scheduleForm">
                            @csrf
                            <!-- Tambahkan bagian ini sebelum form di Schedule.blade.php -->
                            <div class="info-card mb-4">
                                <div class="info-card-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="info-card-content">
                                    <h5>Pilih Periode Pembayaran</h5>
                                    <p>Anda dapat memilih untuk membayar keanggotaan secara mingguan atau langsung bayar
                                        bulanan.</p>
                                </div>
                            </div>

                            <div class="payment-options mb-4">
                                <div class="form-check form-check-inline payment-option-card">
                                    <input class="form-check-input" type="radio" name="payment_period" id="weekly-payment"
                                        value="weekly" checked>
                                    <label class="form-check-label payment-label" for="weekly-payment">
                                        <div class="payment-option-content">
                                            <div class="payment-option-icon">
                                                <i class="fas fa-calendar-week"></i>
                                            </div>
                                            <div class="payment-option-details">
                                                <h6>Pembayaran Mingguan</h6>
                                                <p class="mb-0">Rp {{ number_format($membership->price, 0, ',', '.') }}
                                                </p>
                                                <small class="text-muted">Perpanjangan otomatis setiap minggu</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div class="form-check form-check-inline payment-option-card">
                                    <input class="form-check-input" type="radio" name="payment_period"
                                        id="monthly-payment" value="monthly">
                                    <label class="form-check-label payment-label" for="monthly-payment">
                                        <div class="payment-option-content">
                                            <div class="payment-option-icon">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                            <div class="payment-option-details">
                                                <h6>Pembayaran Bulanan</h6>
                                                <p class="mb-0">Rp
                                                    {{ number_format($membership->price * 4, 0, ',', '.') }}</p>
                                                <small class="text-muted">Hemat waktu dengan pembayaran bulanan</small>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="schedule-steps">
                                <!-- Step 1: Pick Week -->
                                <div class="schedule-step active" id="step-1">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h5>Pilih Minggu</h5>4
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

                                <!-- Step 2: Pick Time Slots -->
                                <div class="schedule-step" id="step-2">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h5>Pilih Slot Waktu ({{ $requiredHours }} jam)</h5>
                                        <div class="day-tabs-container mb-4">
                                            <ul class="nav nav-tabs day-tabs" id="dayTabs" role="tablist"></ul>
                                        </div>

                                        <div class="tab-content" id="dayTabsContent">
                                            <!-- Tab panes akan dibuat dinamis dengan JavaScript -->
                                        </div>

                                        <div class="time-slot-counter mt-3">
                                            <div
                                                class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                                <span>Slot Dipilih: <span id="selected-slots-count">0</span> dari
                                                    {{ $requiredHours }} jam</span>
                                                <div class="progress flex-grow-1 mx-3" style="height: 10px;">
                                                    <div id="slots-progress-bar" class="progress-bar bg-success"
                                                        role="progressbar" style="width: 0%"></div>
                                                </div>
                                                <span id="slots-percentage">0%</span>
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
                                                <div class="selected-slots-list mt-3">
                                                    <h6>Slot Waktu Terpilih:</h6>
                                                    <ul id="selected-slots-summary" class="list-group"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden field to store selected slots -->
                            <div id="selected-slots-container"></div>
                            <input type="hidden" name="today_date" id="today-date" value="{{ date('Y-m-d') }}">

                            <div class="form-actions mt-4">
                                <a href="{{ route('user.membership.show', $membership->id) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                                <button type="button" class="btn btn-outline-secondary" id="prev-btn"
                                    style="display: none;">
                                    <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                                </button>
                                <button type="button" class="btn btn-primary" id="next-btn" disabled>
                                    Selanjutnya<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                <button type="submit" class="btn btn-primary" id="submit-btn" style="display: none;">
                                    <i class="fas fa-cart-plus me-2"></i>Tambahkan ke Keranjang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi variabel
            const requiredHours = {{ $requiredHours }};
            let selectedWeekStart = null;
            let selectedWeekEnd = null;
            let selectedSlots = new Set();
            let currentStep = 1;
            const fieldId = {{ $field->id }};
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const availableDays = [];
            let availableSlotsByDay = {};

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

                        // Aktifkan step berikutnya
                        document.getElementById('next-btn').disabled = false;

                        // Add event listener to change week button
                        document.getElementById('change-week-btn').addEventListener('click',
                    function() {
                            resetSelection();
                        });

                        // Dapatkan rentang tanggal untuk seminggu
                        const weekDates = getWeekDates(selectedWeekStart);

                        // Simpan ke variabel global
                        availableDays.length = 0;
                        weekDates.forEach(date => {
                            availableDays.push({
                                dateObj: date,
                                dateStr: formatDateToYMD(date)
                            });
                        });
                    }
                }
            });

            // Generate tab hari dan konten
            function generateDayTabs() {
                const dayTabsContainer = document.getElementById('dayTabs');
                const dayTabsContent = document.getElementById('dayTabsContent');

                // Reset containers
                dayTabsContainer.innerHTML = '';
                dayTabsContent.innerHTML = '';

                // Buat tab untuk setiap hari
                availableDays.forEach((day, index) => {
                    const isActive = index === 0;
                    const dayName = day.dateObj.toLocaleDateString('id-ID', {
                        weekday: 'long'
                    });
                    const dateStr = day.dateObj.toLocaleDateString('id-ID', {
                        day: 'numeric',
                        month: 'long'
                    });
                    const tabId = `day-tab-${index}`;
                    const contentId = `day-content-${index}`;

                    // Buat tab
                    const tabItem = document.createElement('li');
                    tabItem.className = 'nav-item';
                    tabItem.innerHTML = `
                        <a class="nav-link ${isActive ? 'active' : ''}" id="${tabId}" data-bs-toggle="tab"
                           href="#${contentId}" role="tab" aria-controls="${contentId}"
                           aria-selected="${isActive ? 'true' : 'false'}" data-date="${day.dateStr}">
                            <div class="day-tab-content">
                                <div class="day-name">${dayName}</div>
                                <div class="day-date">${dateStr}</div>
                            </div>
                        </a>
                    `;
                    dayTabsContainer.appendChild(tabItem);

                    // Buat konten tab
                    const tabContent = document.createElement('div');
                    tabContent.className = `tab-pane fade ${isActive ? 'show active' : ''}`;
                    tabContent.id = contentId;
                    tabContent.setAttribute('role', 'tabpanel');
                    tabContent.setAttribute('aria-labelledby', tabId);

                    // Tambahkan placeholder loading
                    tabContent.innerHTML = `
                        <div class="time-slots-loading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Memuat slot tersedia...</p>
                        </div>
                    `;

                    dayTabsContent.appendChild(tabContent);

                    // Load available slots for this date
                    loadAvailableSlots(day.dateStr, contentId);
                });

                // Tambahkan event listener untuk tab switch
                document.querySelectorAll('#dayTabs .nav-link').forEach(tabLink => {
                    tabLink.addEventListener('click', function(e) {
                        const date = this.getAttribute('data-date');
                        const tabId = this.getAttribute('aria-controls');

                        // Jika belum ada data untuk hari ini, muat slot yang tersedia
                        if (!availableSlotsByDay[date]) {
                            loadAvailableSlots(date, tabId);
                        }
                    });
                });
            }

            // Fungsi untuk memuat slot tersedia dari server
            function loadAvailableSlots(date, contentId) {
                const tabContent = document.getElementById(contentId);

                // Jika sudah ada data di cache, gunakan itu
                if (availableSlotsByDay[date]) {
                    renderTimeSlots(availableSlotsByDay[date], tabContent);
                    return;
                }

                // Tampilkan loader
                tabContent.innerHTML = `
                    <div class="time-slots-loading">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Memuat slot tersedia...</p>
                    </div>
                `;

                // Fetch slot dari server
                fetch(`/membership/fields/${fieldId}/available-slots-membership?date=${date}`, {
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Simpan ke cache
                        availableSlotsByDay[date] = data;

                        // Render slot
                        renderTimeSlots(data, tabContent);
                    })
                    .catch(error => {
                        console.error('Error loading slots:', error);
                        tabContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            Gagal memuat slot waktu. Silakan coba lagi.
                        </div>
                    `;
                    });
            }

            // Render slot waktu
            function renderTimeSlots(slots, container) {
                if (!slots || slots.length === 0) {
                    container.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Tidak ada slot waktu tersedia untuk tanggal ini.
                        </div>
                    `;
                    return;
                }

                // Buat grid slots
                const timeSlotsGrid = document.createElement('div');
                timeSlotsGrid.className = 'time-slots-grid';

                // Tambahkan setiap slot
                slots.forEach(slot => {
                    const slotElement = document.createElement('div');
                    const slotValue = `${slot.date}|${slot.display}|${fieldId}`;
                    const isSelected = selectedSlots.has(slotValue);

                    slotElement.className = `time-slot ${isSelected ? 'selected' : ''}`;
                    slotElement.dataset.value = slotValue;
                    slotElement.innerHTML = `
                        <div class="slot-time">
                            <i class="fas ${isSelected ? 'fa-check' : 'fa-clock'}"></i>
                            <span>${slot.display}</span>
                        </div>
                        <div class="slot-price">
                            <span>1 jam</span>
                        </div>
                    `;

                    // Tambahkan event listener
                    slotElement.addEventListener('click', function() {
                        toggleSlotSelection(this);
                    });

                    timeSlotsGrid.appendChild(slotElement);
                });

                // Tambahkan ke container
                container.innerHTML = '';
                container.appendChild(timeSlotsGrid);
            }

            // Toggle pemilihan slot
            function toggleSlotSelection(slotElement) {
                const slotValue = slotElement.dataset.value;

                if (selectedSlots.has(slotValue)) {
                    // Hapus dari pilihan
                    selectedSlots.delete(slotValue);
                    slotElement.classList.remove('selected');
                    slotElement.querySelector('i').className = 'fas fa-clock';
                } else {
                    // Tambahkan ke pilihan jika belum cukup
                    if (selectedSlots.size < requiredHours) {
                        selectedSlots.add(slotValue);
                        slotElement.classList.add('selected');
                        slotElement.querySelector('i').className = 'fas fa-check';
                    } else {
                        // Tampilkan pesan batas maksimum
                        alert(`Anda hanya dapat memilih maksimal ${requiredHours} jam untuk paket ini.`);
                        return;
                    }
                }

                // Update UI
                updateSelectedSlotsCount();
                updateHiddenFields();
            }

            // Update counter dan progress bar
            function updateSelectedSlotsCount() {
                const countElement = document.getElementById('selected-slots-count');
                const progressBar = document.getElementById('slots-progress-bar');
                const percentageElement = document.getElementById('slots-percentage');
                const count = selectedSlots.size;
                const percentage = Math.round((count / requiredHours) * 100);

                countElement.textContent = count;
                progressBar.style.width = `${percentage}%`;
                percentageElement.textContent = `${percentage}%`;

                // Enable/disable next button
                document.getElementById('next-btn').disabled = count !== requiredHours;
            }

            // Update hidden fields untuk form submission
            function updateHiddenFields() {
                const container = document.getElementById('selected-slots-container');
                container.innerHTML = '';

                selectedSlots.forEach(slot => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_slots[]';
                    input.value = slot;
                    container.appendChild(input);
                });
            }

            // Perbarui ringkasan slot terpilih
            function updateSelectedSlotsSummary() {
                const summaryList = document.getElementById('selected-slots-summary');
                summaryList.innerHTML = '';

                // Kelompokkan slot berdasarkan tanggal
                const slotsByDate = {};

                selectedSlots.forEach(slotValue => {
                    const [date, timeRange] = slotValue.split('|');
                    if (!slotsByDate[date]) {
                        slotsByDate[date] = [];
                    }
                    slotsByDate[date].push(timeRange);
                });

                // Urutkan berdasarkan tanggal
                const sortedDates = Object.keys(slotsByDate).sort();

                // Tambahkan ke summary
                sortedDates.forEach(date => {
                    const formattedDate = new Date(date).toLocaleDateString('id-ID', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long'
                    });

                    const listItem = document.createElement('li');
                    listItem.className = 'list-group-item';

                    let slotsHtml = '';
                    slotsByDate[date].sort().forEach(timeRange => {
                        slotsHtml += `
                            <div class="slot-summary-item">
                                <i class="fas fa-clock text-success"></i>
                                <span>${timeRange}</span>
                            </div>
                        `;
                    });

                    listItem.innerHTML = `
                        <div class="slot-date-header">
                            <i class="fas fa-calendar-day"></i>
                            <strong>${formattedDate}</strong>
                        </div>
                        <div class="slot-time-list">
                            ${slotsHtml}
                        </div>
                    `;

                    summaryList.appendChild(listItem);
                });
            }

            // Fungsi untuk mendapatkan tanggal dalam seminggu
            function getWeekDates(startDate) {
                const dates = [];
                for (let i = 0; i < 7; i++) {
                    const date = new Date(startDate);
                    date.setDate(date.getDate() + i);
                    dates.push(date);
                }
                return dates;
            }

            // Format tanggal untuk tampilan
            function formatDate(date) {
                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            }

            // Format tanggal ke YYYY-MM-DD
            function formatDateToYMD(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            // Pindah ke langkah berikutnya
            function goToStep(step) {
                // Sembunyikan semua langkah
                document.querySelectorAll('.schedule-step').forEach(el => {
                    el.classList.remove('active', 'completed');
                });

                // Tandai langkah sebelumnya sebagai selesai
                for (let i = 1; i < step; i++) {
                    document.getElementById(`step-${i}`).classList.add('completed');
                }

                // Aktifkan langkah saat ini
                document.getElementById(`step-${step}`).classList.add('active');

                // Update tombol navigasi
                updateNavigationButtons(step);

                // Tindakan khusus berdasarkan langkah
                if (step === 2 && selectedWeekStart) {
                    // Generate tab hari dan muat slot
                    generateDayTabs();
                } else if (step === 3) {
                    // Update ringkasan
                    updateSelectedSlotsSummary();
                    document.getElementById('summary-empty').style.display = 'none';
                    document.getElementById('summary-content').style.display = 'block';
                }

                currentStep = step;
            }

            // Update tombol navigasi
            function updateNavigationButtons(step) {
                const prevBtn = document.getElementById('prev-btn');
                const nextBtn = document.getElementById('next-btn');
                const submitBtn = document.getElementById('submit-btn');

                // Reset tampilan
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'none';

                if (step === 1) {
                    // Langkah pertama: hanya tampilkan next
                    nextBtn.style.display = 'inline-block';
                    nextBtn.disabled = !selectedWeekStart;
                } else if (step === 2) {
                    // Langkah kedua: tampilkan prev dan next
                    prevBtn.style.display = 'inline-block';
                    nextBtn.style.display = 'inline-block';
                    nextBtn.disabled = selectedSlots.size !== requiredHours;
                } else if (step === 3) {
                    // Langkah terakhir: tampilkan prev dan submit
                    prevBtn.style.display = 'inline-block';
                    submitBtn.style.display = 'inline-block';
                }
            }

            // Reset pilihan
            function resetSelection() {
                // Reset variabel
                selectedWeekStart = null;
                selectedWeekEnd = null;
                selectedSlots.clear();
                availableDays.length = 0;
                availableSlotsByDay = {};

                // Reset UI
                document.getElementById('selected-week-display').innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-calendar-alt"></i>
                        <p>Belum ada minggu yang dipilih</p>
                    </div>
                `;

                document.getElementById('dayTabs').innerHTML = '';
                document.getElementById('dayTabsContent').innerHTML = '';

                // Reset step dan tombol
                goToStep(1);

                // Show calendar
                weekPicker._input.style.display = 'block';
            }

            // Event Listeners
            document.getElementById('next-btn').addEventListener('click', function() {
                goToStep(currentStep + 1);
            });

            document.getElementById('prev-btn').addEventListener('click', function() {
                goToStep(currentStep - 1);
            });

            document.getElementById('scheduleForm').addEventListener('submit', function(e) {
                // Validasi sebelum submit
                if (selectedSlots.size !== requiredHours) {
                    e.preventDefault();
                    alert(`Anda harus memilih tepat ${requiredHours} jam slot waktu.`);
                    return false;
                }

                // Disable tombol untuk mencegah double submission
                document.getElementById('submit-btn').disabled = true;
                document.getElementById('submit-btn').innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="ms-2">Memproses...</span>
                `;

                return true;
            });

            // Inisialisasi
            goToStep(1);
        });
    </script>
    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <style>
        /* Modern Design System */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }

        .hero-section {
            background: linear-gradient(to right, #9e0620, #bb2d3b);
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
  width: 100%;
  max-width: 500px;  /* misal batas maksimal di desktop */
  margin: 0 auto 1rem;
  padding: 0 1rem;
  box-sizing: border-box;
}
.flatpickr-calendar {
  width: 100% !important;
  font-size: 0.9rem;
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

        /* Day Tabs */
        .day-tabs-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .day-tabs {
  display: flex;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  padding: 0 1rem;
}
.day-tabs .nav-link {
  flex: 0 0 auto;
}


        .day-tabs .nav-item {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .day-tabs .nav-link {
            border: none;
            padding: 0.75rem 1.25rem;
            color: #6c757d;
            border-radius: 0;
            transition: all 0.2s ease;
            position: relative;
        }

        .day-tabs .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background-color: transparent;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .day-tabs .nav-link.active {
            color: #d00f25;
            background-color: transparent;
            font-weight: 600;
        }

        .day-tabs .nav-link.active::after {
            background-color: #d00f25;
            transform: scaleX(1);
        }

        .day-tab-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .day-name {
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .day-date {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Time Slots Grid */
        .time-slots-grid {
  display: grid;
  /* auto-fit akan menyesuaikan jumlah kolom sesuai lebar tersedia */
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 12px;
  margin-top: 1rem;
  width: 100%;
  box-sizing: border-box;
  padding: 0 0.5rem; /* beri padding agar tidak mepet tepi layar */
}


        .time-slot {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: white;
        }

        .time-slot:hover {
            border-color: #d00f25;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .time-slot.selected {
            border-color: #d00f25;
            background-color: rgba(208, 15, 37, 0.05);
        }

        .slot-time {
            padding: 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .slot-time i {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #6c757d;
        }

        .slot-time span {
            font-weight: 500;
        }

        .time-slot.selected .slot-time i {
            color: #d00f25;
        }

        .slot-price {
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
        }

        /* Loading State */
        .time-slots-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            color: #6c757d;
        }

        .time-slots-loading p {
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        /* Selected Slots Summary */
        .selected-slots-list {
            margin-top: 1rem;
        }

        .list-group-item {
            border-radius: 8px !important;
            margin-bottom: 0.5rem;
            border: 1px solid #e9ecef;
        }

        .slot-date-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            color: #495057;
        }

        .slot-date-header i {
            margin-right: 0.5rem;
            color: #d00f25;
        }

        .slot-time-list {
            padding-left: 1.5rem;
        }

        .slot-summary-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .slot-summary-item i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }

        /* Time Slot Counter */
        .time-slot-counter {
            background-color: white;
            border-radius: 10px;
            border: 1px solid #e9ecef;
        }

        /* Buttons */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            justify-content: space-between;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            font-weight: 500;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #d00f25;
            border-color: #d00f25;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #b00d1f;
            border-color: #b00d1f;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(208, 15, 37, 0.25);
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.7;
            transform: none !important;
            box-shadow: none !important;
        }

        /* Payment Options */
        .payment-options {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 1.5rem;
        }

        .payment-option-card {
            flex: 1;
            min-width: 220px;
            margin: 0;
        }

        .payment-label {
            display: block;
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check-input:checked+.payment-label {
            border-color: #d00f25;
            background-color: rgba(208, 15, 37, 0.05);
            box-shadow: 0 0 0 3px rgba(208, 15, 37, 0.1);
        }

        .payment-label:hover {
            border-color: #d00f25;
        }

        .payment-label img {
            max-height: 28px;
            margin-right: 10px;
        }

        .payment-label span {
            font-weight: 500;
            color: #212529;
        }

        .payment-option-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Confirmation Section */
        .confirmation-message {
            text-align: center;
            padding: 2rem 1rem;
        }

        .confirmation-message h4 {
            color: #212529;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .confirmation-message p {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        .confirmation-icon {
            font-size: 2.5rem;
            color: #28a745;
            margin-bottom: 1rem;
        }

        /* Responsive Tweaks */
        @media (max-width: 576px) {
  .time-slots-grid {
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 8px;
  }
  .time-slot {
    font-size: 0.85rem;
  }
}


            .form-actions {
                flex-direction: column;
                gap: 0.75rem;
            }

            .time-slots-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
        }

    </style>
@endsection
