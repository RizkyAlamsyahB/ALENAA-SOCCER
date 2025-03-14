<!-- resources/views/users/payment/invoice.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $payment->order_id }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
            size: a4 portrait;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #2D3748;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        /* Container */
        .invoice-container {
            max-width: 100%;
            margin: 0;
            padding: 20px;
            position: relative;
        }

        /* Top Border */
        .top-border {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(to right, #9E0620, #E53E3E);
        }

        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            padding-top: 10px;
            position: relative;
        }

        .logo-section {
            text-align: left;
        }

        .logo-section h1 {
            margin: 0;
            font-size: 20pt;
            font-weight: 700;
            color: #9E0620;
            letter-spacing: -0.5px;
            line-height: 1;
        }

        .logo-section p {
            margin: 0;
            color: #4A5568;
            font-size: 8pt;
            font-weight: 400;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            margin: 0 0 3px 0;
            color: #9E0620;
            font-size: 18pt;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .invoice-title p {
            margin: 1px 0;
            font-size: 8pt;
            color: #4A5568;
            font-weight: 400;
        }

        /* Info Boxes */
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            gap: 10px;
        }

        .info-box {
            flex: 1;
            padding: 8px 10px;
            border-radius: 6px;
            border-left: 3px solid #9E0620;
            background-color: #F7FAFC;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .info-title {
            color: #9E0620;
            font-size: 8pt;
            font-weight: 600;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-box p {
            margin: 0 0 2px 0;
            font-size: 8pt;
            line-height: 1.3;
            color: #4A5568;
        }

        .info-box p strong {
            color: #2D3748;
            font-weight: 600;
        }

        /* Payment Status */
        .payment-status {
            display: inline-block;
            background-color: #38A169;
            color: white;
            padding: 2px 6px;
            border-radius: 50px;
            font-size: 7pt;
            font-weight: 500;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Order Details Table */
        .table-wrapper {
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .order-details {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }

        .order-details th {
            background-color: #F7FAFC;
            padding: 6px 8px;
            text-align: left;
            font-weight: 600;
            color: #4A5568;
            border-bottom: 1px solid #E2E8F0;
        }

        .order-details td {
            padding: 5px 8px;
            border-bottom: 1px solid #EDF2F7;
            color: #4A5568;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 150px;
        }

        .order-details .text-right {
            text-align: right;
        }

        /* Item Type Header */
        .item-type-header {
            font-weight: 600;
            background-color: rgba(158, 6, 32, 0.05);
            color: #9E0620;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        /* Handling many items */
        .items-container {
            max-height: 300px; /* Adjust based on your needs */
            overflow: hidden;
        }

        .condensed-item td {
            padding: 4px 8px;
            line-height: 1.2;
        }

        /* Placeholder for more items */
        .more-items {
            text-align: center;
            font-style: italic;
            color: #718096;
            font-size: 7pt;
            background-color: #F7FAFC;
            padding: 4px;
        }

        /* Totals */
        .footer-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .qr-section {
            width: 70px;
            height: 70px;
            border: 1px dashed #CBD5E0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 6pt;
            color: #718096;
            text-align: center;
            margin-top: 5px;
        }

        .totals-section {
            flex-grow: 1;
            display: flex;
            justify-content: flex-end;
        }

        .totals-table {
            width: 40%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 4px 8px;
            font-size: 8pt;
            color: #4A5568;
        }

        .totals-table .label {
            text-align: right;
            font-weight: 500;
        }

        .totals-table .value {
            text-align: right;
            font-weight: 400;
        }

        .totals-table .discount-row td {
            color: #38A169;
            font-weight: 500;
        }

        .totals-table .subtotal-row td {
            border-bottom: 1px solid #E2E8F0;
            padding-bottom: 5px;
        }

        .totals-table .additional-row td {
            padding-top: 5px;
            color: #718096;
            font-size: 7pt;
        }

        .totals-table .total-row td {
            font-weight: 700;
            font-size: 10pt;
            color: #9E0620;
            padding-top: 5px;
        }

        /* Thank You and Footer */
        .thank-you-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            border-top: 1px solid #EDF2F7;
            padding-top: 10px;
        }

        .thank-you {
            font-size: 14pt;
            font-weight: 700;
            color: #9E0620;
            letter-spacing: -0.3px;
        }

        .footer {
            text-align: right;
            font-size: 7pt;
            color: #718096;
            flex-grow: 1;
        }

        .footer p {
            margin: 0 0 2px 0;
            line-height: 1.3;
        }
    </style>
</head>
<body>
    <div class="top-border"></div>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="logo-section">
                <h1>Sport<span style="color: #2D3748">Vue</span></h1>
                <p>Booking Lapangan Online</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p>ORDER #{{ $payment->order_id }}</p>
                <p>{{ Carbon\Carbon::parse($payment->transaction_time ?? $payment->created_at)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Info Boxes -->
        <div class="info-grid">
            <div class="info-box">
                <div class="info-title">Customer</div>
                <p><strong>{{ $payment->user->name }}</strong></p>
                <p>{{ $payment->user->email }}</p>
                <p>{{ $payment->user->phone ?? '-' }}</p>
            </div>

            <div class="info-box">
                <div class="info-title">Payment</div>
                <p><strong>{{ ucwords(str_replace('_', ' ', $payment->payment_type ?? 'Online Payment')) }}</strong></p>
                <p>ID: {{ substr($payment->transaction_id ?? '-', 0, 10) }}...</p>
                <p><span class="payment-status">Paid</span></p>
            </div>

            <div class="info-box">
                <div class="info-title">Address</div>
                <p><strong>SportVue Inc.</strong></p>
                <p>Jl. Contoh No. 123, Jakarta</p>
                <p>info@sportvue.com</p>
            </div>
        </div>

        <!-- Order Details -->
        <div class="table-wrapper">
            <table class="order-details">
                <thead>
                    <tr>
                        <th width="30%">Deskripsi</th>
                        <th width="20%">Tanggal</th>
                        <th width="20%">Waktu</th>
                        <th width="15%">Durasi/Jumlah</th>
                        <th width="15%" class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="items-container">
                    @php
                        $totalItems = 0;
                        if(isset($payment->fieldBookings)) {
                            $totalItems += count($payment->fieldBookings);
                        }
                        if(isset($payment->rentalBookings)) {
                            $totalItems += count($payment->rentalBookings);
                        }

                        // Maximum visible items (adjust as needed)
                        $maxItems = 10;
                        $visibleFieldItems = isset($payment->fieldBookings) ? min(count($payment->fieldBookings), $maxItems) : 0;
                        $remainingSlots = $maxItems - $visibleFieldItems;
                        $visibleRentalItems = isset($payment->rentalBookings) ? min(count($payment->rentalBookings), $remainingSlots) : 0;

                        $hiddenItems = $totalItems - $visibleFieldItems - $visibleRentalItems;
                    @endphp

                    <!-- Field Bookings -->
                    @if(isset($payment->fieldBookings) && count($payment->fieldBookings) > 0)
                        <tr class="item-type-header">
                            <td colspan="5">Lapangan</td>
                        </tr>
                        @foreach($payment->fieldBookings->take($visibleFieldItems) as $booking)
                        <tr class="condensed-item">
                            <td>{{ $booking->field->name ?? 'Lapangan' }}</td>
                            <td>{{ Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</td>
                            <td>
                                {{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </td>
                            <td>
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
                        <tr class="item-type-header">
                            <td colspan="5">Penyewaan Peralatan</td>
                        </tr>
                        @foreach($payment->rentalBookings->take($visibleRentalItems) as $booking)
                        <tr class="condensed-item">
                            <td>{{ $booking->rentalItem->name ?? 'Peralatan' }}</td>
                            <td>{{ Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</td>
                            <td>
                                {{ Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                {{ Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </td>
                            <td>{{ $booking->quantity }} unit</td>
                            <td class="text-right">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    @endif

                    @if($hiddenItems > 0)
                        <tr class="more-items">
                            <td colspan="5">dan {{ $hiddenItems }} item lainnya</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Totals and QR Code -->
        <div class="footer-wrapper">
            <div class="qr-section">
                Scan untuk verifikasi
            </div>

            <div class="totals-section">
                <table class="totals-table">
                    <tr class="subtotal-row">
                        <td class="label">Subtotal</td>
                        <td class="value">Rp {{ number_format($payment->original_amount, 0, ',', '.') }}</td>
                    </tr>

                    @if($payment->discount_amount > 0)
                    <tr class="discount-row">
                        <td class="label">Diskon</td>
                        <td class="value">- Rp {{ number_format($payment->discount_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif

                    <tr class="additional-row">
                        <td class="label">Biaya Admin</td>
                        <td class="value">Rp 0</td>
                    </tr>
                    <tr class="additional-row">
                        <td class="label">PPN (0%)</td>
                        <td class="value">Rp 0</td>
                    </tr>
                    <tr class="total-row">
                        <td class="label">Total</td>
                        <td class="value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Thank You and Footer -->
        <div class="thank-you-footer">
            <div class="thank-you">Terima Kasih!</div>
            <div class="footer">
                <p>Invoice ini dibuat secara elektronik dan sah tanpa tanda tangan.</p>
                <p>Booking Anda telah dikonfirmasi dan siap digunakan sesuai jadwal.</p>
                <p>&copy; {{ date('Y') }} SportVue. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
