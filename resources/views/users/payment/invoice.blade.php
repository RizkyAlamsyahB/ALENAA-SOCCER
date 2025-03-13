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
        }

        body {
            font-family: sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        /* Container */
        .invoice-container {
            max-width: 100%;
            margin: 0;
            padding: 30px;
        }

        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 0;
            position: relative;
        }

        .logo-section {
            text-align: left;
        }

        .logo-section h1 {
            margin: 0;
            font-size: 28pt;
            font-weight: 600;
            color: #9E0620;
            letter-spacing: -0.5px;
            line-height: 1;
        }

        .logo-section p {
            margin: 0;
            color: #555;
            font-size: 10pt;
            font-weight: 400;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            margin: 0 0 5px 0;
            color: #9E0620;
            font-size: 28pt;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .invoice-title p {
            margin: 2px 0;
            font-size: 10pt;
            color: #555;
            font-weight: 400;
        }

        /* Info Row */
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .info-column {
            width: 30%;
        }

        .info-title {
            color: #9E0620;
            font-size: 11pt;
            font-weight: 500;
            margin: 0 0 10px 0;
            position: relative;
            display: inline-block;
        }

        .info-column p {
            margin: 0 0 4px 0;
            font-size: 9pt;
            line-height: 1.4;
        }

        /* Order Details Table */
        .order-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .order-details th {
            background-color: #f5f5f5;
            padding: 10px 12px;
            text-align: left;
            font-weight: 500;
            font-size: 9pt;
            color: #555;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-details td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            font-size: 9pt;
        }

        .order-details .text-right {
            text-align: right;
        }

        /* Item Type Header */
        .item-type-header {
            font-weight: 500;
            background-color: rgba(158, 6, 32, 0.05);
            font-size: 9pt;
            color: #9E0620;
            letter-spacing: 0.5px;
        }

        /* Totals */
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .totals-table {
            width: 40%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 12px;
            font-size: 9pt;
        }

        .totals-table .text-right {
            text-align: right;
        }

        .totals-table .total-row td {
            font-weight: 600;
            font-size: 12pt;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            padding-bottom: 5px;
            color: #9E0620;
        }

        /* Payment Status */
        .payment-status {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 9pt;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        /* Footer */
        .footer {
            border-top: 1px solid #eee;
            padding-top: 15px;
            text-align: center;
            font-size: 8pt;
            color: #777;
            margin-top: 15px;
        }

        .footer p {
            margin: 0 0 3px 0;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="logo-section">
                <h1>Sport<span style="color: #333">Vue</span></h1>
                <p>Booking Lapangan Online</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <p>ORDER #{{ $payment->order_id }}</p>
                <p>{{ Carbon\Carbon::parse($payment->transaction_time ?? $payment->created_at)->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Info Row -->
        <div class="info-row">
            <div class="info-column">
                <div class="info-title">Customer Details</div>
                <p><strong>{{ $payment->user->name }}</strong></p>
                <p>{{ $payment->user->email }}</p>
                <p>{{ $payment->user->phone ?? '-' }}</p>
            </div>

            <div class="info-column">
                <div class="info-title">Payment Details</div>
                <p><strong>{{ ucwords(str_replace('_', ' ', $payment->payment_type ?? 'Online Payment')) }}</strong></p>
                <p>Transaction ID: {{ $payment->transaction_id ?? '-' }}</p>
                <p><span class="payment-status">PAID</span></p>
            </div>

            <div class="info-column">
                <div class="info-title">Company Details</div>
                <p><strong>SportVue Inc.</strong></p>
                <p>Jl. Contoh No. 123, Kota</p>
                <p>Indonesia 12345</p>
                <p>info@sportvue.com</p>
            </div>
        </div>

        <!-- Order Details -->
        <table class="order-details">
            <thead>
                <tr>
                    <th width="35%">Deskripsi</th>
                    <th width="20%">Tanggal</th>
                    <th width="20%">Waktu</th>
                    <th width="10%">Durasi/Jumlah</th>
                    <th width="15%" class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <!-- Field Bookings -->
                @if(count($payment->fieldBookings) > 0)
                    <tr class="item-type-header">
                        <td colspan="5">Lapangan</td>
                    </tr>
                    @foreach($payment->fieldBookings as $booking)
                    <tr>
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
                @if(count($payment->rentalBookings) > 0)
                    <tr class="item-type-header">
                        <td colspan="5">Penyewaan Peralatan</td>
                    </tr>
                    @foreach($payment->rentalBookings as $booking)
                    <tr>
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
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td width="60%" class="text-right">Subtotal</td>
                    <td width="40%" class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-right">Biaya Admin</td>
                    <td class="text-right">Rp 0</td>
                </tr>
                <tr>
                    <td class="text-right">PPN (0%)</td>
                    <td class="text-right">Rp 0</td>
                </tr>
                <tr class="total-row">
                    <td class="text-right">Total</td>
                    <td class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima kasih telah melakukan pembayaran. Invoice ini adalah bukti resmi bahwa pembayaran Anda telah diterima.</p>
            <p>Booking lapangan Anda telah dikonfirmasi dan siap digunakan sesuai jadwal yang telah ditentukan.</p>
            <p>Invoice ini dibuat secara elektronik dan sah tanpa tanda tangan.</p>
            <p>&copy; {{ date('Y') }} SportVue. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
