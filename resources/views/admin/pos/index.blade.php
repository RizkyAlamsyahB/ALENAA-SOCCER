@extends('layouts.admin')

@section('title', 'Point of Sale')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Point of Sale (POS)</h3>
                    <p class="text-subtitle text-muted">Kelola pemesanan lapangan, fotografer, dan penjualan produk</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Point of Sale</li>
                        </ol>
                    </nav>
                </div>
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
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Data Pelanggan</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="customer-search"
                                        placeholder="Cari pelanggan (nama/telepon)...">
                                    <button class="btn btn-primary" type="button" id="search-customer-btn">
                                        <i class="bi bi-search"></i> Cari
                                    </button>
                                </div>
                                <small class="form-text text-muted">Ketik minimal 3 karakter untuk mencari pelanggan</small>
                            </div>
                        </div>

                        <div id="customer-search-results" class="d-none">
                            <div class="list-group" id="customer-list">
                                <!-- Hasil pencarian akan muncul di sini -->
                            </div>
                        </div>

                        <div class="row mt-3" id="selected-customer-info">
                            <div class="col-md-6">
                                <label for="global_customer_name" class="form-label">Nama Pelanggan <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="global_customer_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="global_customer_phone" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="global_customer_phone">
                                <input type="hidden" id="global_customer_id" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Rest of the content remains the same -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="posTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="fields-tab" data-bs-toggle="tab"
                                        data-bs-target="#fields" type="button" role="tab" aria-controls="fields"
                                        aria-selected="true">Lapangan</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="rentals-tab" data-bs-toggle="tab" data-bs-target="#rentals"
                                        type="button" role="tab" aria-controls="rentals"
                                        aria-selected="false">Penyewaan</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="photographers-tab" data-bs-toggle="tab"
                                        data-bs-target="#photographers" type="button" role="tab"
                                        aria-controls="photographers" aria-selected="false">Fotografer</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="products-tab" data-bs-toggle="tab"
                                        data-bs-target="#products" type="button" role="tab"
                                        aria-controls="products" aria-selected="false">Produk</button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="posTabContent">
                                <!-- Tab Lapangan -->
                                <div class="tab-pane fade show active" id="fields" role="tabpanel"
                                    aria-labelledby="fields-tab">
                                    <form id="fieldBookingForm" method="POST"
                                        action="{{ route('admin.pos.add.field') }}">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="field_id" class="form-label mt-2">Pilih Lapangan</label>
                                                <select class="form-select" id="field_id" name="field_id" required>
                                                    <option value="">-- Pilih Lapangan --</option>
                                                    @foreach ($fields as $field)
                                                        <option value="{{ $field->id }}"
                                                            data-price="{{ $field->price }}">
                                                            {{ $field->name }} - {{ $field->type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="field_date" class="form-label">Tanggal</label>
                                                <input type="date" class="form-control" id="field_date" name="date" min="{{ date('Y-m-d') }}" required>
                                                <small class="form-text text-muted">Anda dapat memilih tanggal hingga 7 hari ke depan</small>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="field_time_slot" class="form-label">Slot Waktu</label>
                                                <select class="form-select" id="field_time_slot" name="time_slot"
                                                    required disabled>
                                                    <option value="">-- Pilih tanggal dan lapangan terlebih dahulu --
                                                    </option>
                                                </select>
                                            </div>
                                        </div>



                                        <button type="submit" class="btn btn-primary">Tambah ke Keranjang</button>
                                    </form>
                                </div>

                                <!-- Tab Penyewaan yang diperbarui -->
                                <div class="tab-pane fade" id="rentals" role="tabpanel" aria-labelledby="rentals-tab">
                                    <form id="rentalBookingForm" method="POST"
                                        action="{{ route('admin.pos.add.rental') }}">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="rental_item_id" class="form-label">Pilih Item</label>
                                                <select class="form-select" id="rental_item_id" name="rental_item_id"
                                                    required>
                                                    <option value="">-- Pilih Item --</option>
                                                    @foreach ($rentalItems as $item)
                                                        <option value="{{ $item->id }}"
                                                            data-price="{{ $item->rental_price }}"
                                                            data-stock="{{ $item->stock_available }}">
                                                            {{ $item->name }} - Rp
                                                            {{ number_format($item->rental_price, 0, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted" id="rental_stock_info"></small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="rental_date" class="form-label">Tanggal</label>
                                                <input type="date" class="form-control" id="rental_date"
                                                    name="date" min="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="rental_time_slot" class="form-label">Slot Waktu</label>
                                                <select class="form-select" id="rental_time_slot" name="time_slot"
                                                    required>
                                                    <option value="">-- Pilih Slot Waktu --</option>
                                                    @for ($hour = 8; $hour < 22; $hour++)
                                                        @php
                                                            $startTime = sprintf('%02d:00', $hour);
                                                            $endTime = sprintf('%02d:00', $hour + 1);
                                                            $timeSlot = $startTime . ' - ' . $endTime;
                                                        @endphp
                                                        <option value="{{ $timeSlot }}">{{ $timeSlot }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="rental_quantity" class="form-label">Jumlah</label>
                                                <input type="number" class="form-control" id="rental_quantity"
                                                    name="quantity" min="1" value="1" required>
                                            </div>
                                        </div>

                                        <!-- Input tersembunyi untuk data customer -->
                                        <input type="hidden" name="customer_name" id="rental_customer_name_hidden">
                                        <input type="hidden" name="customer_phone" id="rental_customer_phone_hidden">

                                        <button type="submit" class="btn btn-primary">Tambah ke Keranjang</button>
                                    </form>
                                </div>

                                <!-- Tab Fotografer -->
                                <!-- Tab Fotografer yang diperbarui -->
                                <div class="tab-pane fade" id="photographers" role="tabpanel"
                                    aria-labelledby="photographers-tab">
                                    <form id="photographerBookingForm" method="POST"
                                        action="{{ route('admin.pos.add.photographer') }}">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="photographer_id" class="form-label mt-2">Pilih
                                                    Fotografer</label>
                                                <select class="form-select" id="photographer_id" name="photographer_id"
                                                    required>
                                                    <option value="">-- Pilih Fotografer --</option>
                                                    @foreach ($photographers as $photographer)
                                                        <option value="{{ $photographer->id }}"
                                                            data-price="{{ $photographer->price }}">
                                                            {{ $photographer->name }} - Rp
                                                            {{ number_format($photographer->price, 0, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="photographer_date" class="form-label">Tanggal</label>
                                                <input type="date" class="form-control" id="photographer_date"
                                                    name="date" min="{{ date('Y-m-d') }}" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="photographer_time_slot" class="form-label">Slot Waktu</label>
                                                <select class="form-select" id="photographer_time_slot" name="time_slot"
                                                    required disabled>
                                                    <option value="">-- Pilih tanggal dan fotografer terlebih dahulu
                                                        --</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Input tersembunyi untuk data customer -->
                                        <input type="hidden" name="customer_name"
                                            id="photographer_customer_name_hidden">
                                        <input type="hidden" name="customer_phone"
                                            id="photographer_customer_phone_hidden">

                                        <button type="submit" class="btn btn-primary">Tambah ke Keranjang</button>
                                    </form>
                                </div>

                                <!-- Tab Produk -->
                                <div class="tab-pane fade" id="products" role="tabpanel"
                                    aria-labelledby="products-tab">
                                    <form id="productForm" method="POST" action="{{ route('admin.pos.add.product') }}">
                                        @csrf
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="product_id" class="form-label mt-2">Pilih Produk</label>
                                                <select class="form-select" id="product_id" name="product_id" required>
                                                    <option value="">-- Pilih Produk --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-price="{{ $product->price }}"
                                                            data-stock="{{ $product->stock }}">
                                                            {{ $product->name }} - Rp
                                                            {{ number_format($product->price, 0, ',', '.') }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="form-text text-muted" id="product_stock_info"></small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="product_quantity" class="form-label">Jumlah</label>
                                                <input type="number" class="form-control" id="product_quantity"
                                                    name="quantity" min="1" value="1" required>
                                            </div>
                                        </div>

                                        <!-- Input tersembunyi untuk data customer -->
                                        <input type="hidden" name="customer_name" id="product_customer_name_hidden">
                                        <input type="hidden" name="customer_phone" id="product_customer_phone_hidden">

                                        <button type="submit" class="btn btn-primary">Tambah ke Keranjang</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keranjang POS -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Keranjang</h4>
                        </div>
                        <div class="card-body" id="cart-container">
                            @include('admin.pos.partials.cart_items')
                        </div>
                        <div class="card-footer">
                            <form id="checkoutForm" method="POST" action="{{ route('admin.pos.checkout') }}">
                                @csrf
                                <input type="hidden" id="customer_id" name="customer_id" value="">

                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="payment_method" class="form-label">Metode Pembayaran</label>
                                        <select class="form-select" id="payment_method" name="payment_method" required>
                                            <option value="cash">Tunai</option>
                                            <option value="transfer">Transfer</option>
                                            <option value="other">Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3" id="cash_amount_container">
                                    <div class="col">
                                        <label for="cash_amount" class="form-label">Jumlah Tunai</label>
                                        <input type="number" class="form-control" id="cash_amount" name="cash_amount"
                                            min="0" step="1000">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="notes" class="form-label">Catatan</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success" id="checkout-btn"
                                        {{ count($cartItems ?? []) == 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-cart-check"></i> Proses Pembayaran
                                    </button>

                                    <a href="{{ route('admin.pos.history') }}" class="btn btn-secondary">
                                        <i class="bi bi-clock-history"></i> Riwayat Transaksi
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk mendapatkan tanggal 7 hari dari sekarang dalam format YYYY-MM-DD
            function getMaxDate() {
                const today = new Date();
                const maxDate = new Date();
                maxDate.setDate(today.getDate() + 6); // 6 karena hari ini sudah terhitung 1 hari

                const year = maxDate.getFullYear();
                const month = String(maxDate.getMonth() + 1).padStart(2, '0');
                const day = String(maxDate.getDate()).padStart(2, '0');

                return `${year}-${month}-${day}`;
            }

            // Set max date untuk semua input tanggal
            const dateInputs = [
                document.getElementById('field_date'),
                document.getElementById('rental_date'),
                document.getElementById('photographer_date')
            ];

            const maxDate = getMaxDate();

            dateInputs.forEach(input => {
                if (input) {
                    input.max = maxDate;
                }
            });

            // Bisa juga menggunakan flatpickr jika sudah diimport
            if (typeof flatpickr === 'function') {
                const flatpickrConfig = {
                    minDate: "today",
                    maxDate: maxDate,
                    locale: "id", // Gunakan bahasa Indonesia jika sudah diload
                    dateFormat: "Y-m-d"
                };

                dateInputs.forEach(input => {
                    if (input) {
                        // Tambahkan atribut placeholder untuk menunjukkan batasan
                        input.placeholder = "Pilih tanggal (maks 7 hari)";
                        flatpickr(input, flatpickrConfig);
                    }
                });
            }

            // Fungsi untuk validasi data pelanggan
            function validateCustomerData() {
                const customerName = document.getElementById('global_customer_name').value.trim();

                if (!customerName) {
                    Toastify({
                        text: "Nama pelanggan harus diisi terlebih dahulu",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                    }).showToast();

                    // Fokus ke input nama pelanggan
                    document.getElementById('global_customer_name').focus();
                    return false;
                }
                return true;
            }

            // Tambahkan tanda * pada label nama pelanggan untuk menunjukkan wajib diisi
            const customerNameLabel = document.querySelector('label[for="global_customer_name"]');
            if (customerNameLabel) {
                customerNameLabel.innerHTML = 'Nama Pelanggan <span class="text-danger">*</span>';
            }

            // Lapangan Form Handling
            const fieldIdSelect = document.getElementById('field_id');
            const fieldDateInput = document.getElementById('field_date');
            const fieldTimeSlotSelect = document.getElementById('field_time_slot');

            // Event listener untuk saat lapangan atau tanggal dipilih
            function updateFieldTimeSlots() {
                const fieldId = fieldIdSelect.value;
                const date = fieldDateInput.value;

                if (fieldId && date) {
                    fieldTimeSlotSelect.disabled = true;
                    fieldTimeSlotSelect.innerHTML = '<option value="">Memuat slot waktu...</option>';

                    // Fetch available time slots dari server
                    fetch(`${routeFieldTimeslots}?field_id=${fieldId}&date=${date}`)
                        .then(response => response.json())
                        .then(data => {
                            fieldTimeSlotSelect.innerHTML = '<option value="">-- Pilih Slot Waktu --</option>';

                            // Tambahkan slot yang tersedia
                            data.available_slots.forEach(slot => {
                                const option = document.createElement('option');
                                option.value = slot.label;
                                option.textContent = slot.label;
                                fieldTimeSlotSelect.appendChild(option);
                            });

                            // Tambahkan informasi slot yang sudah dibooking
                            if (data.booked_slots.length > 0) {
                                const optgroup = document.createElement('optgroup');
                                optgroup.label = "Slot yang sudah dibooking";

                                data.booked_slots.forEach(slot => {
                                    const option = document.createElement('option');
                                    option.value = "";
                                    option.textContent =
                                        `${slot.start} - ${slot.end} (${slot.customer})`;
                                    option.disabled = true;
                                    optgroup.appendChild(option);
                                });

                                fieldTimeSlotSelect.appendChild(optgroup);
                            }

                            fieldTimeSlotSelect.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching time slots:', error);
                            fieldTimeSlotSelect.innerHTML = '<option value="">Error memuat slot waktu</option>';
                            fieldTimeSlotSelect.disabled = false;
                        });
                }
            }

            fieldIdSelect.addEventListener('change', updateFieldTimeSlots);
            fieldDateInput.addEventListener('change', updateFieldTimeSlots);

            // Form submission dengan AJAX untuk Lapangan
            const fieldBookingForm = document.getElementById('fieldBookingForm');
            fieldBookingForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validasi data pelanggan
                if (!validateCustomerData()) {
                    return;
                }

                const formData = new FormData(this);
                formData.append('customer_name', document.getElementById('global_customer_name').value);
                formData.append('customer_phone', document.getElementById('global_customer_phone').value);
                if (document.getElementById('global_customer_id').value) {
                    formData.append('customer_id', document.getElementById('global_customer_id').value);
                }
                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update cart container
                            document.getElementById('cart-container').innerHTML = data.html_content;

                            // Reset form
                            fieldBookingForm.reset();
                            fieldTimeSlotSelect.disabled = true;
                            fieldTimeSlotSelect.innerHTML =
                                '<option value="">-- Pilih tanggal dan lapangan terlebih dahulu --</option>';

                            // Enable checkout button
                            document.getElementById('checkout-btn').disabled = false;

                            // Show success message
                            Toastify({
                                text: data.message,
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#4fbe87",
                            }).showToast();
                        } else {
                            // Show error message
                            Toastify({
                                text: data.message,
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545",
                            }).showToast();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Toastify({
                            text: "Terjadi kesalahan saat memproses permintaan",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545",
                        }).showToast();
                    });
            });

            // Fotografer Form Handling
            const photographerId = document.getElementById('photographer_id');
            const photographerDate = document.getElementById('photographer_date');
            const photographerTimeSlot = document.getElementById('photographer_time_slot');

            // Event listener untuk saat fotografer atau tanggal dipilih
            function updatePhotographerTimeSlots() {
                const pId = photographerId.value;
                const date = photographerDate.value;

                if (pId && date) {
                    photographerTimeSlot.disabled = true;
                    photographerTimeSlot.innerHTML = '<option value="">Memuat slot waktu...</option>';

                    // Fetch available time slots dari server
                    fetch(`${routePhotographerTimeslots}?photographer_id=${pId}&date=${date}`)
                        .then(response => response.json())
                        .then(data => {
                            photographerTimeSlot.innerHTML = '<option value="">-- Pilih Slot Waktu --</option>';

                            // Tambahkan slot yang tersedia
                            data.available_slots.forEach(slot => {
                                const option = document.createElement('option');
                                option.value = slot.label;
                                option.textContent = slot.label;
                                photographerTimeSlot.appendChild(option);
                            });

                            // Tambahkan informasi slot yang sudah dibooking
                            if (data.booked_slots.length > 0) {
                                const optgroup = document.createElement('optgroup');
                                optgroup.label = "Slot yang sudah dibooking";

                                data.booked_slots.forEach(slot => {
                                    const option = document.createElement('option');
                                    option.value = "";
                                    option.textContent =
                                        `${slot.start} - ${slot.end} (${slot.customer})`;
                                    option.disabled = true;
                                    optgroup.appendChild(option);
                                });

                                photographerTimeSlot.appendChild(optgroup);
                            }

                            photographerTimeSlot.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error fetching time slots:', error);
                            photographerTimeSlot.innerHTML =
                                '<option value="">Error memuat slot waktu</option>';
                            photographerTimeSlot.disabled = false;
                        });
                }
            }

            photographerId.addEventListener('change', updatePhotographerTimeSlots);
            photographerDate.addEventListener('change', updatePhotographerTimeSlots);

            // Form submission dengan AJAX untuk Fotografer
            const photographerBookingForm = document.getElementById('photographerBookingForm');
            photographerBookingForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validasi data pelanggan
                if (!validateCustomerData()) {
                    return;
                }

                // Ambil data customer dari form global
                const customerName = document.getElementById('global_customer_name').value;
                const customerPhone = document.getElementById('global_customer_phone').value;

                // Masukkan data customer ke hidden fields
                document.getElementById('photographer_customer_name_hidden').value = customerName;
                document.getElementById('photographer_customer_phone_hidden').value = customerPhone;

                const formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message ||
                                    'Terjadi kesalahan saat memproses permintaan');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update cart container
                            document.getElementById('cart-container').innerHTML = data.html_content;

                            // Reset form
                            photographerBookingForm.reset();
                            photographerTimeSlot.disabled = true;
                            photographerTimeSlot.innerHTML =
                                '<option value="">-- Pilih tanggal dan fotografer terlebih dahulu --</option>';

                            // Enable checkout button
                            document.getElementById('checkout-btn').disabled = false;

                            // Show success message
                            Toastify({
                                text: data.message,
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#4fbe87",
                            }).showToast();
                        } else {
                            // Show error message
                            Toastify({
                                text: data.message,
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545",
                            }).showToast();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Toastify({
                            text: error.message ||
                                "Terjadi kesalahan saat memproses permintaan",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545",
                        }).showToast();
                    });
            });

            // Rental Form Handling
            const rentalItemId = document.getElementById('rental_item_id');
            const rentalQuantity = document.getElementById('rental_quantity');
            const rentalStockInfo = document.getElementById('rental_stock_info');

            // Event listener untuk saat item rental dipilih
            rentalItemId.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const stock = selectedOption.getAttribute('data-stock');

                if (stock) {
                    rentalStockInfo.textContent = `Stok tersedia: ${stock}`;
                    rentalQuantity.max = stock;

                    if (parseInt(rentalQuantity.value) > parseInt(stock)) {
                        rentalQuantity.value = stock;
                    }
                } else {
                    rentalStockInfo.textContent = '';
                    rentalQuantity.removeAttribute('max');
                }
            });

            // Form submission dengan AJAX untuk Rental
            const rentalBookingForm = document.getElementById('rentalBookingForm');
            rentalBookingForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validasi data pelanggan
                if (!validateCustomerData()) {
                    return;
                }

                // Ambil data customer dari form global
                const customerName = document.getElementById('global_customer_name').value;
                const customerPhone = document.getElementById('global_customer_phone').value;

                // Masukkan data customer ke hidden fields
                document.getElementById('rental_customer_name_hidden').value = customerName;
                document.getElementById('rental_customer_phone_hidden').value = customerPhone;

                const formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message ||
                                    'Terjadi kesalahan saat memproses permintaan');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update cart container
                            document.getElementById('cart-container').innerHTML = data.html_content;

                            // Reset form
                            rentalBookingForm.reset();
                            rentalStockInfo.textContent = '';

                            // Enable checkout button
                            document.getElementById('checkout-btn').disabled = false;

                            // Show success message
                            Toastify({
                                text: data.message,
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#4fbe87",
                            }).showToast();
                        } else {
                            // Show error message
                            Toastify({
                                text: data.message,
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545",
                            }).showToast();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Toastify({
                            text: error.message ||
                                "Terjadi kesalahan saat memproses permintaan",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545",
                        }).showToast();
                    });
            });

            // Product Form Handling
            const productId = document.getElementById('product_id');
            const productQuantity = document.getElementById('product_quantity');
            const productStockInfo = document.getElementById('product_stock_info');

            // Event listener untuk saat produk dipilih
            productId.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const stock = selectedOption.getAttribute('data-stock');

                if (stock) {
                    productStockInfo.textContent = `Stok tersedia: ${stock}`;
                    productQuantity.max = stock;

                    if (parseInt(productQuantity.value) > parseInt(stock)) {
                        productQuantity.value = stock;
                    }
                } else {
                    productStockInfo.textContent = '';
                    productQuantity.removeAttribute('max');
                }
            });

            // Form submission dengan AJAX untuk Produk
            const productForm = document.getElementById('productForm');
            productForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validasi data pelanggan
                if (!validateCustomerData()) {
                    return;
                }

                // Ambil data customer dari form global
                const customerName = document.getElementById('global_customer_name').value;
                const customerPhone = document.getElementById('global_customer_phone').value;

                // Validasi jumlah dengan stok yang tersedia
                const productSelect = document.getElementById('product_id');
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                const availableStock = selectedOption.getAttribute('data-stock');
                const requestedQuantity = document.getElementById('product_quantity').value;

                if (parseInt(requestedQuantity) > parseInt(availableStock)) {
                    Toastify({
                        text: `Stok tidak cukup. Tersedia: ${availableStock}, Diminta: ${requestedQuantity}`,
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                    }).showToast();
                    return;
                }

                // Masukkan data customer ke hidden fields
                document.getElementById('product_customer_name_hidden').value = customerName;
                document.getElementById('product_customer_phone_hidden').value = customerPhone;

                const formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.message ||
                                    'Terjadi kesalahan saat memproses permintaan');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update cart container
                            document.getElementById('cart-container').innerHTML = data.html_content;

                            // Reset form
                            productForm.reset();
                            productStockInfo.textContent = '';

                            // Enable checkout button
                            document.getElementById('checkout-btn').disabled = false;

                            // Show success message
                            Toastify({
                                text: data.message,
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#4fbe87",
                            }).showToast();
                        } else {
                            // Show error message
                            Toastify({
                                text: data.message,
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "#dc3545",
                            }).showToast();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Toastify({
                            text: error.message ||
                                "Terjadi kesalahan saat memproses permintaan",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545",
                        }).showToast();
                    });
            });

            // Metode Pembayaran Handling
            const paymentMethod = document.getElementById('payment_method');
            const cashAmountContainer = document.getElementById('cash_amount_container');

            paymentMethod.addEventListener('change', function() {
                if (this.value === 'cash') {
                    cashAmountContainer.style.display = 'block';
                } else {
                    cashAmountContainer.style.display = 'none';
                }
            });

            // Checkout Form Handling
            const checkoutForm = document.getElementById('checkoutForm');
            checkoutForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validasi data pelanggan
                if (!validateCustomerData()) {
                    return;
                }

                // Ambil customer_id dari global atau dari item pertama di cart
                const customerId = document.getElementById('global_customer_id').value;
                if (customerId) {
                    document.getElementById('customer_id').value = customerId;
                } else {
                    const firstCustomerId = document.querySelector('[data-customer-id]');
                    if (firstCustomerId) {
                        document.getElementById('customer_id').value = firstCustomerId.getAttribute(
                            'data-customer-id');
                    } else {
                        // Tidak ada customer_id yang tersedia
                        Toastify({
                            text: "Data pelanggan tidak lengkap. Silakan pilih atau masukkan data pelanggan",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "#dc3545",
                        }).showToast();
                        return;
                    }
                }

                // Konfirmasi checkout
                if (confirm('Anda yakin ingin memproses pembayaran ini?')) {
                    this.submit();
                }
            });

            // Handler untuk menghapus item dari cart
            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-cart-item')) {
                    e.preventDefault();

                    const itemId = e.target.getAttribute('data-item-id');

                    // Konfirmasi penghapusan
                    if (confirm('Anda yakin ingin menghapus item ini?')) {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content');

                        fetch(`${routeRemoveItem}/${itemId}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    // Update cart container
                                    document.getElementById('cart-container').innerHTML = data
                                        .html_content;

                                    // Disable checkout button if cart is empty
                                    if (data.cart_total === 0) {
                                        document.getElementById('checkout-btn').disabled = true;
                                    }

                                    // Show success message
                                    Toastify({
                                        text: data.message,
                                        duration: 3000,
                                        close: true,
                                        gravity: "top",
                                        position: "right",
                                        backgroundColor: "#4fbe87",
                                    }).showToast();
                                } else {
                                    // Show error message
                                    Toastify({
                                        text: data.message,
                                        duration: 3000,
                                        close: true,
                                        gravity: "top",
                                        position: "right",
                                        backgroundColor: "#dc3545",
                                    }).showToast();
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Toastify({
                                    text: "Terjadi kesalahan saat memproses permintaan",
                                    duration: 3000,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#dc3545",
                                }).showToast();
                            });
                    }
                }
            });

            // Customer search and selection handling
            const customerSearch = document.getElementById('customer-search');
            const searchCustomerBtn = document.getElementById('search-customer-btn');
            const customerList = document.getElementById('customer-list');
            const customerSearchResults = document.getElementById('customer-search-results');
            const globalCustomerName = document.getElementById('global_customer_name');
            const globalCustomerPhone = document.getElementById('global_customer_phone');
            const globalCustomerId = document.getElementById('global_customer_id');

            // Function untuk mencari customer
            function searchCustomer() {
                const query = customerSearch.value.trim();

                if (query.length < 3) {
                    Toastify({
                        text: "Masukkan minimal 3 karakter untuk mencari",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#dc3545",
                    }).showToast();
                    return;
                }

                // Tampilkan loading
                customerList.innerHTML =
                    '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
                customerSearchResults.classList.remove('d-none');

                // Fetch customer data
                fetch(`${routeSearchCustomers}?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        customerList.innerHTML = '';

                        if (data.customers.length === 0) {
                            customerList.innerHTML =
                                '<div class="list-group-item">Tidak ada pelanggan ditemukan. Masukkan data baru.</div>';
                            return;
                        }

                        // Tampilkan hasil pencarian
                        data.customers.forEach(customer => {
                            const item = document.createElement('a');
                            item.href = '#';
                            item.className = 'list-group-item list-group-item-action';
                            item.innerHTML =
                                `<strong>${customer.name}</strong> <br> ${customer.phone_number || '<i>Tidak ada nomor telepon</i>'}`;

                            item.dataset.id = customer.id;
                            item.dataset.name = customer.name;
                            item.dataset.phone = customer.phone_number || '';

                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                selectCustomer(this.dataset.id, this.dataset.name, this.dataset
                                    .phone);
                            });

                            customerList.appendChild(item);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        customerList.innerHTML =
                            '<div class="list-group-item">Error saat mencari pelanggan</div>';
                    });
            }

            // Function untuk memilih customer
            function selectCustomer(id, name, phone) {
                globalCustomerId.value = id;
                globalCustomerName.value = name;
                globalCustomerPhone.value = phone;

                // Sembunyikan hasil pencarian
                customerSearchResults.classList.add('d-none');

                // Reset input pencarian
                customerSearch.value = '';

                // Isi form dengan data customer
                document.querySelectorAll('[id$="_customer_name_hidden"]').forEach(input => {
                    input.value = name;
                });

                document.querySelectorAll('[id$="_customer_phone_hidden"]').forEach(input => {
                    input.value = phone;
                });

                // Set customer ID untuk form checkout
                document.getElementById('customer_id').value = id;

                Toastify({
                    text: "Data pelanggan berhasil dipilih",
                    duration: 3000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#4fbe87",
                }).showToast();
            }

            // Event listener untuk pencarian
            searchCustomerBtn.addEventListener('click', searchCustomer);
            customerSearch.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    searchCustomer();
                }
            });

            // Event listener untuk mendeteksi perubahan pada global customer data
            globalCustomerName.addEventListener('input', function() {
                // Isi semua field nama customer
                const name = this.value;
                document.querySelectorAll('[id$="_customer_name_hidden"]').forEach(input => {
                    input.value = name;
                });
            });

            globalCustomerPhone.addEventListener('input', function() {
                // Isi semua field telepon customer
                const phone = this.value;
                document.querySelectorAll('[id$="_customer_phone_hidden"]').forEach(input => {
                    input.value = phone;
                });
            });

            // Set path route untuk HTTP request
            const routeFieldTimeslots = '{{ route('admin.pos.field.timeslots') }}';
            const routePhotographerTimeslots = '{{ route('admin.pos.photographer.timeslots') }}';
            const routeSearchCustomers = '{{ route('admin.pos.customers.search') }}';
            const routeRemoveItem = '{{ route('admin.pos.remove.item', '') }}';
        });
    </script>
    <!-- Toastify CSS dan JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!-- Flatpickr CSS dan JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Tambahkan juga bahasa Indonesia jika diinginkan -->
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
@endsection
