@extends('layouts.app')
@section('content')
    <!-- CSS Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSRF Token untuk AJAX Requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Hero Section -->
    <div class="hero-section">
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
    <div class="container main-container">
        <!-- Alerts -->
        <div class="alerts-container">
            @if (session('success'))
                <div class="alert-card success">
                    <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="alert-message">{{ session('success') }}</div>
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()"><i
                            class="fas fa-times"></i></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert-card error">
                    <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="alert-message">{{ session('error') }}</div>
                    <button type="button" class="alert-close" onclick="this.parentElement.remove()"><i
                            class="fas fa-times"></i></button>
                </div>
            @endif
        </div>

        <!-- Schedule Card -->
        <div class="card schedule-card">
            <div class="card-header">
                <h4>Pilih Jadwal Membership</h4>
            </div>
            <div class="card-body">
                <!-- Information Card -->
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="info-card-content">
                        <h5>Paket {{ ucfirst($membership->type) }}</h5>
                        <p>Untuk paket {{ ucfirst($membership->type) }}, Anda perlu memilih <strong>{{ $requiredHours }}
                                jam</strong> slot waktu dalam seminggu. Anda bebas memilih kapan saja dalam rentang 7 hari
                            ke depan.</p>

                        @if ($membership->includes_photographer || $membership->includes_rental_item)
                            <div class="package-includes">
                                <h6>Paket ini sudah termasuk:</h6>
                                <ul>
                                    @if ($membership->includes_photographer)
                                        @php $photographer = App\Models\Photographer::find($membership->photographer_id); @endphp
                                        @if ($photographer)
                                            <li>
                                                <i class="fas fa-camera text-success"></i>
                                                Fotografer {{ $photographer->name }}
                                                ({{ $membership->photographer_duration }} jam)
                                            </li>
                                        @endif
                                    @endif

                                    @if ($membership->includes_rental_item)
                                        @php $rentalItem = App\Models\RentalItem::find($membership->rental_item_id); @endphp
                                        @if ($rentalItem)
                                            <li>
                                                <i class="fas fa-futbol text-success"></i>
                                                {{ $rentalItem->name }} ({{ $membership->rental_item_quantity }} pcs)
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                                <p class="text-muted small">Fotografer dan perlengkapan akan tersedia sesuai dengan jadwal
                                    yang Anda pilih.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <form action="{{ route('user.membership.save.schedule', $membership->id) }}" method="POST"
                    id="scheduleForm">
                    @csrf

                    <!-- Payment Options -->
                    <div class="info-card">
                        <div class="info-card-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="info-card-content">
                            <h5>Pilih Periode Pembayaran</h5>
                            <p>Anda dapat memilih untuk membayar keanggotaan secara mingguan atau langsung bayar bulanan.
                            </p>
                        </div>
                    </div>

                    <div class="payment-options">
                        <div class="payment-option">
                            <input class="form-check-input" type="radio" name="payment_period" id="weekly-payment"
                                value="weekly" checked>
                            <label class="payment-label" for="weekly-payment">
                                <div class="payment-content">
                                    <div class="payment-icon">
                                        <i class="fas fa-calendar-week"></i>
                                    </div>
                                    <div class="payment-details">
                                        <h6>Pembayaran Mingguan</h6>
                                        <p>Rp {{ number_format($membership->price, 0, ',', '.') }}</p>
                                        <small>Perpanjangan otomatis setiap minggu</small>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="payment-option">
                            <input class="form-check-input" type="radio" name="payment_period" id="monthly-payment"
                                value="monthly">
                            <label class="payment-label" for="monthly-payment">
                                <div class="payment-content">
                                    <div class="payment-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="payment-details">
                                        <h6>Pembayaran Bulanan</h6>
                                        <p>Rp {{ number_format($membership->price * 4, 0, ',', '.') }}</p>
                                        <small>Hemat waktu dengan pembayaran bulanan</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Booking Process Steps -->
                    <div class="booking-wizard">
                        <!-- Progress Bar -->
                        <div class="wizard-progress">
                            <div class="wizard-progress-bar" id="wizard-progress-bar"></div>

                            <!-- Step 1 -->
                            <div class="wizard-step active" id="wizard-step-1">
                                <div class="step-circle"><span>1</span><i class="fas fa-check"></i></div>
                                <div class="step-label">Pilih Minggu</div>
                                <div class="step-desc">Tentukan minggu untuk jadwal Anda</div>
                            </div>

                            <!-- Step 2 -->
                            <div class="wizard-step" id="wizard-step-2">
                                <div class="step-circle"><span>2</span><i class="fas fa-check"></i></div>
                                <div class="step-label">Pilih Waktu</div>
                                <div class="step-desc">Pilih {{ $requiredHours }} jam waktu bermain</div>
                            </div>

                            <!-- Step 3 -->
                            <div class="wizard-step" id="wizard-step-3">
                                <div class="step-circle"><span>3</span><i class="fas fa-check"></i></div>
                                <div class="step-label">Konfirmasi</div>
                                <div class="step-desc">Konfirmasi jadwal Anda</div>
                            </div>
                        </div>

                        <!-- Wizard Content -->
                        <div class="wizard-content">
                            <!-- Step 1 Panel: Pick Week -->
                            <div class="wizard-panel active" id="step-panel-1">
                                <h5 class="panel-title">Pilih Minggu</h5>

                                <div class="week-picker-wrapper">
                                    <div id="week-picker" class="calendar-picker"></div>
                                    <div class="selected-week-display" id="selected-week-display">
                                        <div class="empty-state">
                                            <i class="fas fa-calendar-alt"></i>
                                            <p>Belum ada minggu yang dipilih</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2 Panel: Pick Time Slots -->
                            <div class="wizard-panel" id="step-panel-2">
                                <h5 class="panel-title">Pilih Slot Waktu ({{ $requiredHours }} jam)</h5>

                                <div class="day-tabs-container">
                                    <div class="day-tabs" id="dayTabs"></div>
                                </div>

                                <div class="tab-content mt-4" id="dayTabsContent">
                                    <!-- Tab panes akan dibuat dinamis dengan JavaScript -->
                                </div>

                                <div class="time-slot-counter">
                                    <div class="counter-content">
                                        <span>Slot Dipilih: <span id="selected-slots-count">0</span> dari
                                            {{ $requiredHours }} jam</span>
                                        <div class="progress-container">
                                            <div id="slots-progress-bar" class="progress-bar"></div>
                                        </div>
                                        <span id="slots-percentage">0%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3 Panel: Confirmation -->
                            <div class="wizard-panel" id="step-panel-3">
                                <h5 class="panel-title">Konfirmasi Jadwal</h5>

                                <div class="warning-card">
                                    <div class="warning-card-icon">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="warning-card-content">
                                        <h5>Perhatian</h5>
                                        <p>Jadwal yang dipilih tidak dapat diubah selama masa membership. Pastikan Anda
                                            memilih jadwal yang sesuai dengan ketersediaan Anda.</p>
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
                                        <div class="selected-slots-list">
                                            <h6>Slot Waktu Terpilih:</h6>
                                            <ul id="selected-slots-summary" class="list-group"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden field to store selected slots -->
                        <div id="selected-slots-container"></div>
                        <input type="hidden" name="today_date" id="today-date" value="{{ date('Y-m-d') }}">

                        <!-- Navigation Buttons -->
                        <div class="wizard-buttons">
                            <a href="{{ route('user.membership.show', $membership->id) }}"
                                class="wizard-btn wizard-btn-prev">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="button" class="wizard-btn wizard-btn-prev" id="prev-btn"
                                style="display: none;">
                                <i class="fas fa-arrow-left"></i> Sebelumnya
                            </button>
                            <button type="button" class="wizard-btn wizard-btn-next" id="next-btn" disabled>
                                Selanjutnya <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="submit" class="wizard-btn wizard-btn-submit" id="submit-btn"
                                style="display: none;">
                                <i class="fas fa-cart-plus"></i> Tambahkan ke Keranjang
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

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

            // Memperbarui lebar progress bar
            function updateProgressBar() {
                const progressBar = document.getElementById('wizard-progress-bar');
                const progressWidth = ((currentStep - 1) / 2) * 100;
                progressBar.style.width = progressWidth + '%';
            }

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
                                <button type="button" class="btn-change-week" id="change-week-btn">
                                    <i class="fas fa-exchange-alt"></i> Ubah
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
                    const tabItem = document.createElement('div');
                    tabItem.className = `day-tab ${isActive ? 'active' : ''}`;
                    tabItem.id = tabId;
                    tabItem.setAttribute('data-tab', contentId);
                    tabItem.setAttribute('data-date', day.dateStr);
                    tabItem.innerHTML = `
                        <div class="day-name">${dayName}</div>
                        <div class="day-date">${dateStr}</div>
                    `;
                    dayTabsContainer.appendChild(tabItem);

                    // Tambahkan event listener untuk tab
                    tabItem.addEventListener('click', function() {
                        // Hapus kelas active dari semua tab
                        document.querySelectorAll('.day-tab').forEach(tab => {
                            tab.classList.remove('active');
                        });
                        // Tambahkan kelas active ke tab yang diklik
                        this.classList.add('active');

                        // Sembunyikan semua konten tab
                        document.querySelectorAll('.day-content').forEach(content => {
                            content.classList.remove('active');
                        });
                        // Tampilkan konten tab yang sesuai
                        document.getElementById(this.getAttribute('data-tab')).classList.add(
                            'active');

                        // Load available slots jika belum ada data
                        const date = this.getAttribute('data-date');
                        const contentId = this.getAttribute('data-tab');
                        if (!availableSlotsByDay[date]) {
                            loadAvailableSlots(date, contentId);
                        }
                    });

                    // Buat konten tab
                    const tabContent = document.createElement('div');
                    tabContent.className = `day-content ${isActive ? 'active' : ''}`;
                    tabContent.id = contentId;

                    // Tambahkan placeholder loading
                    tabContent.innerHTML = `
                        <div class="time-slots-loading">
                            <div class="spinner-border">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Memuat slot tersedia...</p>
                        </div>
                    `;

                    dayTabsContent.appendChild(tabContent);

                    // Load available slots for this date
                    loadAvailableSlots(day.dateStr, contentId);
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
                        <div class="spinner-border">
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

                    slotElement.className = `time-slot ${isSelected ? 'slot-selected' : 'slot-available'}`;
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
                    slotElement.classList.remove('slot-selected');
                    slotElement.classList.add('slot-available');
                    slotElement.querySelector('i').className = 'fas fa-clock';
                } else {
                    // Tambahkan ke pilihan jika belum cukup
                    if (selectedSlots.size < requiredHours) {
                        selectedSlots.add(slotValue);
                        slotElement.classList.remove('slot-available');
                        slotElement.classList.add('slot-selected');
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
                currentStep = step;

                // Update progress bar
                updateProgressBar();

                // Update wizard steps
                document.querySelectorAll('.wizard-step').forEach((el, index) => {
                    const stepNum = index + 1;
                    el.classList.remove('active', 'completed');

                    if (stepNum < step) {
                        el.classList.add('completed');
                    } else if (stepNum === step) {
                        el.classList.add('active');
                    }
                });

                // Update panels
                document.querySelectorAll('.wizard-panel').forEach((panel, index) => {
                    const panelNum = index + 1;
                    panel.classList.remove('active');

                    if (panelNum === step) {
                        panel.classList.add('active');
                    }
                });

                // Update navigation buttons
                const prevBtn = document.getElementById('prev-btn');
                const nextBtn = document.getElementById('next-btn');
                const submitBtn = document.getElementById('submit-btn');

                // Reset visibility
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'none';

                if (step === 1) {
                    // Step 1: Show next button only
                    nextBtn.style.display = 'flex';
                    nextBtn.disabled = !selectedWeekStart;
                } else if (step === 2) {
                    // Step 2: Show prev and next
                    prevBtn.style.display = 'flex';
                    nextBtn.style.display = 'flex';
                    nextBtn.disabled = selectedSlots.size !== requiredHours;

                    // Generate day tabs if coming from step 1
                    if (selectedWeekStart) {
                        generateDayTabs();
                    }
                } else if (step === 3) {
                    // Step 3: Show prev and submit
                    prevBtn.style.display = 'flex';
                    submitBtn.style.display = 'flex';

                    // Update summary
                    updateSelectedSlotsSummary();
                    document.getElementById('summary-empty').style.display = 'none';
                    document.getElementById('summary-content').style.display = 'block';
                }
            }

            // Reset selection for week picker
            function resetSelection() {
                // Reset variables
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

                // Reset progress and steps
                goToStep(1);
            }

            // Event Listeners
            document.getElementById('next-btn').addEventListener('click', function() {
                goToStep(currentStep + 1);
            });

            document.getElementById('prev-btn').addEventListener('click', function() {
                goToStep(currentStep - 1);
            });

            document.getElementById('scheduleForm').addEventListener('submit', function(e) {
                // Validate before submit
                if (selectedSlots.size !== requiredHours) {
                    e.preventDefault();
                    alert(`Anda harus memilih tepat ${requiredHours} jam slot waktu.`);
                    return false;
                }

                // Disable submit button to prevent double submissions
                document.getElementById('submit-btn').disabled = true;
                document.getElementById('submit-btn').innerHTML = `
                    <span class="spinner-border spinner-border-sm"></span>
                    <span>Memproses...</span>
                `;

                return true;
            });

            // Initialize wizard
            updateProgressBar();
            goToStep(1);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/rangePlugin/rangePlugin.js"></script>



    <style>
        /* Base styling */
        :root {
            --primary-color: #9e0620;
            --primary-hover: #bb2d3b;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #2196f3;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }



        /* Hero Section */
        .hero-section {
            background: linear-gradient(to right, var(--primary-color), var(--primary-hover));
            padding: 3rem 0;
            margin-top: 50px;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .hero-content {
            text-align: center;
        }

        .hero-title {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1rem;
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
            list-style: none;
        }

        .breadcrumb-item {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "/";
            margin: 0 0.5rem;
            color: rgba(255, 255, 255, 0.6);
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

        .breadcrumb-item i {
            margin-right: 5px;
        }

        /* Main Container */
        .main-container {
            margin-top: 2rem;
            margin-bottom: 4rem;
        }

        /* Alert Cards */
        .alerts-container {
            margin-bottom: 1.5rem;
        }

        .alert-card {
            display: flex;
            align-items: center;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .alert-card.success {
            background-color: #e8f5e9;
            border-left: 4px solid var(--success-color);
        }

        .alert-card.error {
            background-color: #ffebee;
            border-left: 4px solid var(--danger-color);
        }

        .alert-icon {
            font-size: 1.2rem;
            margin-right: 1rem;
            display: flex;
            align-items: center;
        }

        .alert-card.success .alert-icon {
            color: var(--success-color);
        }

        .alert-card.error .alert-icon {
            color: var(--danger-color);
        }

        .alert-message {
            flex: 1;
        }

        .alert-close {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 0.9rem;
            padding: 0.3rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .alert-close:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Card Design */
        .card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem;
        }

        .card-header h4 {
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Info and Warning Cards */
        .info-card,
        .warning-card {
            display: flex;
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
        }

        .info-card {
            background-color: #e8f4ff;
            border-left: 4px solid var(--info-color);
        }

        .warning-card {
            background-color: #fff8e1;
            border-left: 4px solid var(--warning-color);
        }

        .info-card-icon,
        .warning-card-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            display: flex;
            align-items: center;
        }

        .info-card-icon {
            color: var(--info-color);
        }

        .warning-card-icon {
            color: var(--warning-color);
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
            margin-bottom: 0.5rem;
            color: var(--text-muted);
        }

        .package-includes {
            margin-top: 1rem;
        }

        .package-includes h6 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .package-includes ul {
            list-style: none;
            padding-left: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .package-includes li {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .package-includes li i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }

        /* Payment Options */
        .payment-options {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .payment-option {
            flex: 1;
            min-width: 220px;
            position: relative;
        }

        .form-check-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .payment-label {
            display: block;
            width: 100%;
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-check-input:checked+.payment-label {
            border-color: var(--primary-color);
            background-color: rgba(158, 6, 32, 0.05);
            box-shadow: 0 0 0 3px rgba(158, 6, 32, 0.1);
        }

        .payment-label:hover {
            border-color: var(--primary-color);
        }

        .payment-content {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .payment-icon {
            font-size: 1.2rem;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(158, 6, 32, 0.1);
            border-radius: 50%;
            flex-shrink: 0;
        }

        .payment-details h6 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .payment-details p {
            font-size: 0.95rem;
            font-weight: 500;
            margin-bottom: 0.3rem;
        }

        .payment-details small {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Wizard Booking Process */
        .booking-wizard {
            position: relative;
            margin-bottom: 2.5rem;
        }

        /* Progress Bar */
        .wizard-progress {
            display: flex;
            position: relative;
            margin-bottom: 2rem;
            padding: 0 10px;
        }

        .wizard-progress::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 4px;
            background-color: var(--border-color);
            z-index: 1;
        }

        .wizard-progress-bar {
            position: absolute;
            top: 20px;
            left: 0;
            height: 4px;
            background-color: var(--primary-color);
            transition: width 0.5s ease;
            z-index: 2;
        }

        /* Step Items */
        .wizard-step {
            flex: 1;
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid var(--border-color);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
            position: relative;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .step-circle i {
            font-size: 1rem;
            display: none;
        }

        .wizard-step.active .step-circle {
            border-color: var(--primary-color);
            background-color: var(--primary-color);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(158, 6, 32, 0.3);
        }

        .wizard-step.completed .step-circle {
            border-color: var(--primary-color);
            background-color: var(--primary-color);
            color: white;
        }

        .wizard-step.completed .step-circle span {
            display: none;
        }

        .wizard-step.completed .step-circle i {
            display: inline;
        }

        .step-label {
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            transition: color 0.3s ease;
        }

        .wizard-step.active .step-label,
        .wizard-step.completed .step-label {
            color: var(--text-dark);
        }

        .step-desc {
            color: var(--text-muted);
            font-size: 0.8rem;
            display: none;
        }

        .wizard-step.active .step-desc {
            color: var(--primary-color);
            display: block;
        }

        /* Wizard Content */
        .wizard-content {
            position: relative;
            overflow: hidden;
            min-height: 300px;
        }

        .wizard-panel {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .wizard-panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .panel-title {
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
        }

        /* Week Picker */
        .week-picker-wrapper {
            width: 100%;
            max-width: 500px;
            margin: 0 auto 1.5rem;
            position: relative;
        }

        .calendar-picker {
            width: 100%;
            margin-bottom: 1rem;
        }

        .selected-week-display {
            background-color: var(--light-bg);
            border-radius: 10px;
            padding: 1rem;
            border: 1px solid var(--border-color);
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            color: var(--text-muted);
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
            color: var(--text-muted);
            margin-bottom: 0.3rem;
        }

        .week-value {
            font-weight: 600;
            color: var(--text-dark);
        }

        .btn-change-week {
            background-color: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-change-week:hover {
            background-color: var(--light-bg);
        }

        /* Perbaikan styling untuk tab hari */
        .day-tabs-container {
            margin-top: 30px;
            /* Tambahkan margin top yang lebih besar */
            margin-bottom: 20px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 10px;
            /* Tambahkan padding bawah */
        }

        .day-tabs {
            display: flex;
            gap: 10px;
            /* Memperbesar jarak antar tab */
            padding-bottom: 10px;
        }

        .day-tab {
            flex: 0 0 auto;
            padding: 12px 20px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            /* Perbesar ketebalan border */
            background-color: #ffffff;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            /* Tambahkan shadow untuk lebih menonjol */
        }

        .day-tab.active {
            border-color: #9e0620;
            background-color: #9e0620;
            color: white;
            box-shadow: 0 4px 8px rgba(158, 6, 32, 0.25);
        }

        .day-name {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 6px;
            /* Tambahkan margin bawah */
        }

        .day-date {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .day-tab.active .day-date {
            color: rgba(255, 255, 255, 0.9);
        }

        /* Time Slots Section - Tambahkan margin top */
        .time-slots-section {
            margin-top: 30px;
        }

        /* Time-slots grid */
        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        /* Time slot style */
        .time-slot {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            background-color: white;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .time-slot:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-color: #9e0620;
        }

        /* Slot Counter */
        .slot-counter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 30px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        /* Navigation Buttons */
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #9e0620;
            color: white;
            border: none;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid #e9ecef;
            color: #495057;
        }

        .btn-primary:hover {
            background-color: #8a051c;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(158, 6, 32, 0.2);
        }

        .btn-outline:hover {
            background-color: #f8f9fa;
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .day-tabs-container {
                margin-top: 25px;
            }

            .day-tab {
                padding: 10px 16px;
            }

            .day-name {
                font-size: 0.9rem;
            }

            .day-date {
                font-size: 0.8rem;
            }

            .time-slots-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .nav-buttons {
                flex-wrap: wrap;
                gap: 10px;
            }
        }

        @media (max-width: 576px) {
            .day-tabs {
                gap: 8px;
            }

            .day-tab {
                padding: 8px 12px;
                min-width: 80px;
            }

            .day-name {
                font-size: 0.85rem;
                margin-bottom: 4px;
            }

            .day-date {
                font-size: 0.75rem;
            }
        }

        /* Time Slots */
        .day-content {
            display: none;
        }

        .day-content.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }

        .time-slots-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 0;
            color: var(--text-muted);
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border: 0.25rem solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner 0.75s linear infinite;
            margin-bottom: 1rem;
        }

        @keyframes spinner {
            to {
                transform: rotate(360deg);
            }
        }

        .time-slots-loading p {
            margin-top: 1rem;
            font-size: 0.95rem;
        }

        .time-slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 1.5rem;
        }

        .time-slot {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: white;
        }

        .time-slot:not(.slot-booked):hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-color);
        }

        .slot-available:hover {
            border-color: var(--success-color);
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
            color: var(--text-muted);
        }

        .slot-time span {
            font-weight: 500;
        }

        .slot-price {
            padding: 0.5rem;
            background-color: var(--light-bg);
            border-top: 1px solid var(--border-color);
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .slot-selected {
            border-color: var(--primary-color);
            background-color: rgba(158, 6, 32, 0.05);
        }

        .slot-selected .slot-time i {
            color: var(--primary-color);
        }

        .slot-booked {
            border-color: var(--text-muted);
            background-color: var(--light-bg);
            opacity: 0.7;
            cursor: not-allowed;
        }

        .slot-in-cart {
            border-color: #fd7e14;
            background-color: #fff8f1;
        }

        .slot-in-cart .slot-time i {
            color: #fd7e14;
        }

        /* Time Slot Counter */
        .time-slot-counter {
            background-color: var(--light-bg);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1.5rem;
        }

        .counter-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
        }

        .progress-container {
            flex: 1;
            height: 8px;
            background-color: white;
            border-radius: 50px;
            margin: 0 1rem;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .progress-bar {
            height: 100%;
            background-color: var(--primary-color);
            border-radius: 50px;
            width: 0%;
            transition: width 0.3s ease;
        }

        /* Selected Slots Summary */
        .selected-schedule-summary {
            margin-top: 1.5rem;
            background-color: var(--light-bg);
            border-radius: 10px;
            padding: 1.5rem;
        }

        .selected-schedule-summary h6 {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .selected-slots-list {
            margin-top: 1.5rem;
        }

        .list-group {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .list-group-item {
            background-color: white;
            border-radius: 8px !important;
            margin-bottom: 0.75rem;
            padding: 1rem;
            border: 1px solid var(--border-color);
        }

        .slot-date-header {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            color: var(--text-dark);
        }

        .slot-date-header i {
            margin-right: 0.5rem;
            color: var(--primary-color);
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

        /* Navigation Buttons */
        .wizard-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .wizard-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .wizard-btn-prev {
            background-color: var(--light-bg);
            color: var(--text-dark);
            text-decoration: none;
        }

        .wizard-btn-prev:hover {
            background-color: var(--border-color);
            transform: translateX(-5px);
        }

        .wizard-btn-next {
            background-color: var(--primary-color);
            color: white;
        }

        .wizard-btn-next:hover {
            background-color: var(--primary-hover);
            transform: translateX(5px);
        }

        .wizard-btn-submit {
            background-color: var(--primary-color);
            color: white;
        }

        .wizard-btn-submit:hover {
            background-color: var(--primary-hover);
            transform: scale(1.05);
        }

        .wizard-btn i {
            transition: transform 0.3s ease;
        }

        .wizard-btn-prev:hover i {
            transform: translateX(-3px);
        }

        .wizard-btn-next:hover i {
            transform: translateX(3px);
        }

        .wizard-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Custom Flatpickr Theme - brand color #9e0620 */
        /* Flatpickr Calendar Styling - Improved for Mobile */
        .flatpickr-calendar {
            width: 100% !important;
            max-width: 320px !important;
            box-sizing: border-box !important;
            padding: 0 !important;
            margin: 0 auto !important;
            touch-action: manipulation;
        }


        .flatpickr-days {
            width: 100% !important;
            /* Ensure days container is full width */
        }

        .dayContainer {
            width: 100% !important;
            /* Ensure day container is full width */
            min-width: 100% !important;
            max-width: 100% !important;
            display: flex;
            flex-wrap: wrap;
        }

        .flatpickr-day {
            width: 14.2857% !important;
            /* Equal width for all 7 days (100% ÷ 7) */
            max-width: 14.2857% !important;
            flex-basis: 14.2857% !important;
            height: 40px !important;
            /* Consistent height */
            line-height: 40px !important;
            margin: 0 !important;
            border-radius: 24 !important;
        }

        /* Make sure headers align with days */
        span.flatpickr-weekday {
            width: 14.2857% !important;
            max-width: 14.2857% !important;
            flex-basis: 14.2857% !important;
        }

        /* Mobile adjustments */
        @media (max-width: 576px) {
            .flatpickr-calendar {
                max-width: 100%;
            }

            .flatpickr-day {
                height: 35px !important;
                line-height: 35px !important;
            }
        }

        .flatpickr-months {
            background-color: #ffffff;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .flatpickr-month {
            color: #fff;
        }

        .flatpickr-current-month {
            font-weight: 600;
        }

        .flatpickr-monthDropdown-months,
        .numInputWrapper span.arrowUp,
        .numInputWrapper span.arrowDown {
            color: #fff;
        }

        span.flatpickr-weekday {
            color: #9e0620;
            font-weight: 600;
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
            background: #9e0620;
            border-color: #9e0620;
        }

        .flatpickr-day.today {
            border-color: #9e0620;
        }

        .flatpickr-day.today:hover {
            background: #fff8f8;
            color: #9e0620;
        }

        .flatpickr-day:hover {
            background: #fff8f8;
            border-color: #fff8f8;
        }

        .flatpickr-day.selected.startRange+.endRange:not(:nth-child(7n+1)),
        .flatpickr-day.startRange.startRange+.endRange:not(:nth-child(7n+1)),
        .flatpickr-day.endRange.startRange+.endRange:not(:nth-child(7n+1)) {
            box-shadow: -10px 0 0 #9e0620;
        }

        .flatpickr-prev-month,
        .flatpickr-next-month {
            fill: #fff;
        }

        .flatpickr-prev-month:hover svg,
        .flatpickr-next-month:hover svg {
            fill: #e9ecef;
        }



        /* Responsive Media Queries */
        @media (max-width: 992px) {
            .payment-options {
                flex-direction: column;
            }

            .payment-option {
                width: 100%;
                min-width: 100%;
            }

            .selected-week-content {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .btn-change-week {
                align-self: flex-start;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 0;
            }

            .hero-title {
                font-size: 1.6rem;
            }

            .breadcrumb {
                padding: 0.6rem 1rem;
            }

            .card-header,
            .card-body {
                padding: 1.25rem;
            }

            .info-card,
            .warning-card {
                flex-direction: column;
            }

            .info-card-icon,
            .warning-card-icon {
                margin-right: 0;
                margin-bottom: 1rem;
                align-self: center;
            }

            .step-desc {
                display: none !important;
            }

            .wizard-progress::before {
                top: 15px;
            }

            .wizard-progress-bar {
                top: 15px;
            }

            .step-circle {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }

            .step-label {
                font-size: 0.8rem;
            }

            .time-slots-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .wizard-buttons {
                flex-direction: column;
                gap: 1rem;
            }

            .wizard-btn {
                width: 100%;
                justify-content: center;
            }

            .wizard-btn-prev {
                order: 2;
            }

            .wizard-btn-next,
            .wizard-btn-submit {
                order: 1;
            }

            .counter-content {
                flex-direction: column;
                gap: 0.75rem;
            }

            .progress-container {
                width: 100%;
                margin: 0.5rem 0;
            }
        }

        @media (max-width: 576px) {
            .hero-section {
                padding: 1.5rem 0;
            }

            .hero-title {
                font-size: 1.3rem;
            }

            .breadcrumb {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }

            .card-header h4 {
                font-size: 1.1rem;
            }

            .info-card-content h5,
            .warning-card-content h5 {
                font-size: 0.95rem;
            }

            .info-card-content p,
            .warning-card-content p {
                font-size: 0.85rem;
            }

            .time-slots-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }

            .slot-time {
                padding: 0.6rem;
            }

            .slot-time i {
                font-size: 1rem;
                margin-bottom: 0.3rem;
            }

            .slot-time span {
                font-size: 0.85rem;
            }

            .slot-price {
                padding: 0.4rem;
                font-size: 0.75rem;
            }

            .day-tab {
                padding: 0.6rem 1rem;
                min-width: 90px;
            }

            .day-name {
                font-size: 0.85rem;
            }

            .day-date {
                font-size: 0.75rem;
            }

            .flatpickr-calendar {
                max-width: 100% !important;
            }

            .flatpickr-day {
                height: 32px !important;
                line-height: 32px !important;
                font-size: 0.8rem !important;
            }
        }

        @media (max-width: 375px) {
            .time-slots-grid {
                grid-template-columns: repeat(1, 1fr);
            }

            .day-tab {
                min-width: 80px;
                padding: 0.5rem 0.75rem;
            }

            .wizard-step {
                padding: 0 2px;
            }

            .step-circle {
                width: 28px;
                height: 28px;
                font-size: 0.8rem;
            }

            .step-label {
                font-size: 0.7rem;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

@endsection
