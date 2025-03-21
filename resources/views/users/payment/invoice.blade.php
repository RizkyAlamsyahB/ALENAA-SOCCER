<!-- resources/views/users/payment/invoice.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Faktur #{{ $payment->order_id }}</title>
    <style>
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: normal;
            src: url({{ storage_path('fonts/poppins/Poppins-Regular.ttf') }}) format('truetype');
        }
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: bold;
            src: url({{ storage_path('fonts/poppins/Poppins-Bold.ttf') }}) format('truetype');
        }
        @font-face {
            font-family: 'Poppins';
            font-style: normal;
            font-weight: 600;
            src: url({{ storage_path('fonts/poppins/Poppins-SemiBold.ttf') }}) format('truetype');
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10pt;
            line-height: 1.4;
            color: #333333;
        }

        .page-container {
            width: 100%;
            position: relative;
        }

        /* Header Section */
        .header {
            background-color: #9E0620;
            color: white;
            padding: 30px;
            width: 100%;
        }

        .header-table {
            width: 100%;
        }

        .brand h1 {
            font-size: 24pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }

        .brand p {
            font-size: 10pt;
            margin: 5px 0 0;
            padding: 0;
            opacity: 0.9;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            font-size: 20pt;
            margin: 0;
            padding: 0;
        }

        .invoice-title p {
            font-size: 10pt;
            margin: 5px 0 0;
            padding: 0;
            opacity: 0.9;
        }

        /* Main Content */
        .content {
            padding: 20px 30px;
            background-color: white;
        }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 60pt;
            color: rgba(16, 185, 129, 0.07);
            font-weight: bold;
            transform: rotate(-45deg);
            z-index: 0;
        }

        /* Info Sections */
        .info-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: separate;
            border-spacing: 10px;
        }

        .info-box {
            background-color: #f9fafb;
            border-radius: 5px;
            padding: 15px;
            vertical-align: top;
            width: 33.33%;
        }

        .info-box h3 {
            color: #9E0620;
            font-size: 10pt;
            font-weight: 600;
            margin: 0 0 10px 0;
            padding: 0 0 5px 0;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-label {
            color: #64748b;
            font-size: 8pt;
            display: block;
            margin: 8px 0 2px;
        }

        .info-value {
            font-weight: 600;
            font-size: 9pt;
            color: #334155;
            margin: 0 0 5px 0;
        }

        .status-badge {
            display: inline-block;
            background-color: #10b981;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: 500;
            text-transform: uppercase;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .items-table th {
            background-color: #f1f5f9;
            text-align: left;
            padding: 10px;
            font-size: 9pt;
            font-weight: 600;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
        }

        .items-table td {
            padding: 10px;
            font-size: 9pt;
            border-bottom: 1px solid #f1f5f9;
        }

        .items-table .text-center {
            text-align: center;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .item-type {
            background-color: #f8fafc;
            color: #9E0620;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.5px;
        }

        .items-table .more-items {
            text-align: center;
            font-style: italic;
            color: #94a3b8;
            font-size: 8pt;
            padding: 8px;
            background-color: #f8fafc;
        }

        /* Totals Section */
        .totals-container {
            width: 100%;
        }

        .totals-table {
            width: 350px;
            margin-left: auto;
            background-color: #f9fafb;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 15px;
            font-size: 9pt;
        }

        .totals-table .label {
            text-align: left;
            color: #64748b;
            font-weight: 500;
        }

        .totals-table .value {
            text-align: right;
            font-weight: 500;
            color: #334155;
        }

        .totals-table .discount-row .value {
            color: #10b981;
            font-weight: 600;
        }

        .totals-table .divider td {
            border-bottom: 1px solid #e2e8f0;
            padding: 2px 0;
        }

        .totals-table .total-row {
            background-color: #f1f5f9;
        }

        .totals-table .total-row .label {
            font-weight: 700;
            color: #334155;
            font-size: 11pt;
        }

        .totals-table .total-row .value {
            font-weight: 700;
            color: #9E0620;
            font-size: 11pt;
        }

        /* Thank You */
        .thank-you {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            color: #9E0620;
            margin: 40px 0 30px 0;
        }

        /* Footer */
        .footer {
            background-color: #f8fafc;
            padding: 20px 30px;
            margin-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .footer-table {
            width: 100%;
        }

        .footer-left {
            font-size: 16pt;
            font-weight: bold;
            color: #9E0620;
            width: 30%;
        }

        .footer-right {
            text-align: right;
            font-size: 8pt;
            color: #64748b;
            line-height: 1.5;
            width: 70%;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <!-- Header -->
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="brand">
                        <h1>ALENA<span style="opacity: 0.8">SOCCER</span></h1>
                        <p>Fasilitas Olahraga Premium</p>
                    </td>
                    <td class="invoice-title">
                        <h2>FAKTUR</h2>
                        <p>PESANAN #{{ $payment->order_id }}</p>
                        <p>{{ Carbon\Carbon::parse($payment->transaction_time ?? $payment->created_at)->locale('id')->isoFormat('D MMMM Y') }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Main Content -->
        <div class="content">
            <!-- Watermark -->
            <div class="watermark">LUNAS</div>

            <!-- Info Section -->
            <table class="info-table">
                <tr>
                    <td class="info-box">
                        <h3>Pelanggan</h3>
                        <span class="info-label">Nama</span>
                        <div class="info-value">{{ $payment->user->name }}</div>

                        <span class="info-label">Email</span>
                        <div class="info-value">{{ $payment->user->email }}</div>

                        @if($payment->user->phone)
                        <span class="info-label">Telepon</span>
                        <div class="info-value">{{ $payment->user->phone }}</div>
                        @endif
                    </td>

                    <td class="info-box">
                        <h3>Detail Pembayaran</h3>
                        <span class="info-label">Metode</span>
                        <div class="info-value">{{ ucwords(str_replace('_', ' ', $payment->payment_type ?? 'Pembayaran Online')) }}</div>

                        <span class="info-label">ID Transaksi</span>
                        <div class="info-value">{{ $payment->transaction_id ?? '-' }}</div>

                        <span class="info-label">Status</span>
                        <div class="info-value"><span class="status-badge">Lunas</span></div>
                    </td>

                    <td class="info-box">
                        <h3>Detail Faktur</h3>
                        <span class="info-label">Tanggal Faktur</span>
                        <div class="info-value">{{ Carbon\Carbon::parse($payment->created_at)->locale('id')->isoFormat('D MMM Y') }}</div>

                        <span class="info-label">Jatuh Tempo</span>
                        <div class="info-value">{{ Carbon\Carbon::parse($payment->created_at)->locale('id')->isoFormat('D MMM Y') }}</div>

                        <span class="info-label">Tanggal Pembayaran</span>
                        <div class="info-value">{{ Carbon\Carbon::parse($payment->transaction_time ?? $payment->updated_at)->locale('id')->isoFormat('D MMM Y') }}</div>
                    </td>
                </tr>
            </table>

            <!-- Items Table -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th width="35%">Deskripsi</th>
                        <th width="20%">Tanggal</th>
                        <th width="15%" class="text-center">Waktu</th>
                        <th width="15%" class="text-center">Jumlah/Durasi</th>
                        <th width="15%" class="text-right">Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalItems = 0;
                        if(isset($payment->fieldBookings)) {
                            $totalItems += count($payment->fieldBookings);
                        }
                        if(isset($payment->rentalBookings)) {
                            $totalItems += count($payment->rentalBookings);
                        }
                        if(isset($payment->photographerBookings)) {
                            $totalItems += count($payment->photographerBookings);
                        }

                        // Maximum visible items
                        $maxItems = 8;
                        $visibleFieldItems = isset($payment->fieldBookings) ? min(count($payment->fieldBookings), $maxItems) : 0;
                        $remainingSlots = $maxItems - $visibleFieldItems;
                        $visibleRentalItems = isset($payment->rentalBookings) ? min(count($payment->rentalBookings), $remainingSlots) : 0;
                        $remainingSlotsAfterRental = $remainingSlots - $visibleRentalItems;
                        $visiblePhotographerItems = isset($payment->photographerBookings) ? min(count($payment->photographerBookings), $remainingSlotsAfterRental) : 0;

                        $hiddenItems = $totalItems - $visibleFieldItems - $visibleRentalItems - $visiblePhotographerItems;
                    @endphp

                    <!-- Field Bookings -->
                    @if(isset($payment->fieldBookings) && count($payment->fieldBookings) > 0)
                        <tr>
                            <td colspan="5" class="item-type">Sewa Lapangan</td>
                        </tr>
                        @foreach($payment->fieldBookings->take($visibleFieldItems) as $booking)
                        <tr>
                            <td>{{ $booking->field->name ?? 'Sewa Lapangan' }}</td>
                            <td>{{ Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('D MMM Y') }}</td>
                            <td class="text-center">
                                {{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </td>
                            <td class="text-center">
                                @php
                                    $startTime = Carbon\Carbon::parse($booking->start_time);
                                    $endTime = Carbon\Carbon::parse($booking->end_time);
                                    $durationInHours = $startTime->diffInHours($endTime);
                                @endphp
                                {{ $durationInHours }} jam
                            </td>
                            <td class="text-right">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @endif

                    <!-- Rental Bookings -->
                    @if(isset($payment->rentalBookings) && count($payment->rentalBookings) > 0 && $visibleRentalItems > 0)
                        <tr>
                            <td colspan="5" class="item-type">Sewa Perlengkapan</td>
                        </tr>
                        @foreach($payment->rentalBookings->take($visibleRentalItems) as $booking)
                        <tr>
                            <td>{{ $booking->rentalItem->name ?? 'Sewa Perlengkapan' }}</td>
                            <td>{{ Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('D MMM Y') }}</td>
                            <td class="text-center">
                                {{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </td>
                            <td class="text-center">{{ $booking->quantity }} unit</td>
                            <td class="text-right">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @endif

                    <!-- Photographer Bookings -->
                    @if(isset($payment->photographerBookings) && count($payment->photographerBookings) > 0 && $visiblePhotographerItems > 0)
                        <tr>
                            <td colspan="5" class="item-type">Jasa Fotografi</td>
                        </tr>
                        @foreach($payment->photographerBookings->take($visiblePhotographerItems) as $booking)
                        <tr>
                            <td>{{ $booking->photographer->name ?? 'Jasa Fotografi' }}</td>
                            <td>{{ Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('D MMM Y') }}</td>
                            <td class="text-center">
                                {{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </td>
                            <td class="text-center">
                                @php
                                    $startTime = Carbon\Carbon::parse($booking->start_time);
                                    $endTime = Carbon\Carbon::parse($booking->end_time);
                                    $durationInHours = $startTime->diffInHours($endTime);
                                @endphp
                                {{ $durationInHours }} jam
                            </td>
                            <td class="text-right">Rp {{ number_format($booking->price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @endif

                    @if($hiddenItems > 0)
                        <tr>
                            <td colspan="5" class="more-items">dan {{ $hiddenItems }} item lainnya</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- Totals Section -->
            <div class="totals-container">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">Rp {{ number_format($payment->original_amount, 0, ',', '.') }}</td>
                    </tr>

                    @if($payment->discount_amount > 0)
                    <tr class="discount-row">
                        <td class="label">Diskon</td>
                        <td class="value">- Rp {{ number_format($payment->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    <tr>
                        <td class="label">Biaya Admin</td>
                        <td class="value">Rp 0</td>
                    </tr>

                    <tr>
                        <td class="label">Pajak (0%)</td>
                        <td class="value">Rp 0</td>
                    </tr>

                    <tr class="divider">
                        <td colspan="2"></td>
                    </tr>

                    <tr class="total-row">
                        <td class="label">Total</td>
                        <td class="value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

        </div>

        <!-- Footer -->
        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-left">Terima Kasih!</td>
                    <td class="footer-right">
                        <p>Faktur ini dibuat secara elektronik dan sah tanpa tanda tangan.</p>
                        <p>Pemesanan Anda telah dikonfirmasi dan siap digunakan sesuai jadwal.</p>
                        <p>&copy; {{ date('Y') }} Alena Soccer. Hak Cipta Dilindungi.</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
