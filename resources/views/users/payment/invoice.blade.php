<!-- resources/views/users/payment/invoice.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $payment->order_id }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }

        body {
            font-family: 'Inter', 'Roboto', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333333;
            margin: 0;
            padding: 0;
        }

        /* Container */
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eaeaea;
        }

        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #9E0620;
            padding-bottom: 15px;
        }

        .logo-section h1 {
            margin: 0;
            color: #9E0620;
            font-size: 24pt;
            font-weight: 600;
        }

        .logo-section p {
            margin: 0;
            color: #555;
            font-size: 10pt;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            margin: 0;
            color: #9E0620;
            font-size: 24pt;
            font-weight: 700;
        }

        /* Customer Intro */
        .customer-intro {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .greeting p {
            margin: 0 0 5px 0;
            font-size: 10pt;
        }

        .order-info {
            text-align: right;
        }

        .order-info p {
            margin: 0 0 5px 0;
            font-size: 10pt;
        }

        .order-number {
            font-weight: 600;
            font-size: 11pt;
        }

        /* Order Details Table */
        .order-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .order-details th {
       background-color: #f5f5f5;
       padding: 8px 10px;
       border-bottom: 1px solid #ddd;
       text-align: left;
       font-weight: 600;
       font-size: 10pt;
       text-transform: uppercase;
   }

   .order-details td {
       padding: 8px 10px;
       border-bottom: 1px solid #eee;
       font-size: 10pt;
   }

   .order-details .text-right {
       text-align: right;
   }

   /* Totals */
   .totals-table {
       width: 100%;
       margin-bottom: 25px;
   }

   .totals-table td {
       padding: 5px 10px;
       font-size: 10pt;
   }

   .totals-table .text-right {
       text-align: right;
   }

   .totals-table .total-row td {
       font-weight: 700;
       font-size: 12pt;
       border-top: 1px solid #ddd;
       padding-top: 10px;
   }

   /* Info Sections */
   .info-sections {
       display: flex;
       justify-content: space-between;
       margin-bottom: 25px;
   }

   .info-section {
       width: 30%;
   }

   .info-section h3 {
       color: #9E0620;
       font-size: 11pt;
       font-weight: 600;
       margin: 0 0 10px 0;
       text-transform: uppercase;
       border-bottom: 1px solid #eee;
       padding-bottom: 5px;
   }

   .info-section p {
       margin: 0 0 5px 0;
       font-size: 10pt;
   }

   /* Payment Status */
   .payment-status {
       display: inline-block;
       background-color: #28a745;
       color: white;
       padding: 3px 8px;
       border-radius: 3px;
       font-size: 9pt;
       font-weight: 600;
   }

   /* Footer */
   .footer {
       border-top: 1px solid #eee;
       padding-top: 15px;
       text-align: center;
       font-size: 9pt;
       color: #777;
   }

   .footer p {
       margin: 0 0 5px 0;
   }

   /* Item Type Header */
   .item-type-header {
       font-weight: 700;
       background-color: #f8f8f8;
       text-transform: uppercase;
       font-size: 10pt;
       color: #666;
   }
   </style>
</head>
<body>
   <div class="invoice-container">
       <!-- Header -->
       <div class="invoice-header">
           <div class="logo-section">
               <h1>SportVue</h1>
               <p>Booking Lapangan Online</p>
           </div>
           <div class="invoice-title">
               <h2>INVOICE</h2>
           </div>
       </div>

       <!-- Customer Intro -->
       <div class="customer-intro">
           <div class="greeting">
               <p>Hello, {{ $payment->user->name }}.</p>
               <p>Thank you for your booking.</p>
           </div>
           <div class="order-info">
               <p class="order-number">ORDER #{{ $payment->order_id }}</p>
               <p>{{ Carbon\Carbon::parse($payment->transaction_time ?? $payment->created_at)->format('d M Y') }}</p>
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

               <!-- Membership Subscriptions -->
               {{-- @if(count($payment->membershipSubscriptions) > 0)
                   <tr class="item-type-header">
                       <td colspan="5">Keanggotaan</td>
                   </tr>
                   @foreach($payment->membershipSubscriptions as $subscription)
                   <tr>
                       <td>{{ $subscription->membership->name ?? 'Keanggotaan' }}</td>
                       <td>{{ Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }}</td>
                       <td>s/d {{ Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}</td>
                       <td>{{ $subscription->membership->duration ?? '1' }} bulan</td>
                       <td class="text-right">Rp {{ number_format($subscription->price, 0, ',', '.') }}</td>
                   </tr>
                   @endforeach
               @endif --}}

               <!-- Photographer Bookings -->
               {{-- @if(count($payment->photographerBookings) > 0)
                   <tr class="item-type-header">
                       <td colspan="5">Jasa Fotografer</td>
                   </tr>
                   @foreach($payment->photographerBookings as $booking)
                   <tr>
                       <td>{{ $booking->photographer->name ?? 'Fotografer' }}</td>
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
                       <td class="text-right">Rp {{ number_format($booking->price, 0, ',', '.') }}</td>
                   </tr>
                   @endforeach
               @endif --}}
           </tbody>
       </table>

       <!-- Totals -->
       <table class="totals-table">
           <tr>
               <td width="85%" class="text-right">Subtotal</td>
               <td width="15%" class="text-right">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
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

       <!-- Info Sections -->
       <div class="info-sections">
           <div class="info-section">
               <h3>Informasi Pelanggan</h3>
               <p>{{ $payment->user->name }}</p>
               <p>{{ $payment->user->email }}</p>
               <p>{{ $payment->user->phone ?? '-' }}</p>
           </div>

           <div class="info-section">
               <h3>Informasi Pembayaran</h3>
               <p>{{ ucwords(str_replace('_', ' ', $payment->payment_type ?? 'Online Payment')) }}</p>
               <p>Transaction ID:<br>{{ $payment->transaction_id ?? '-' }}</p>
               <p><span class="payment-status">PAID</span></p>
           </div>

           <div class="info-section">
               <h3>Informasi Perusahaan</h3>
               <p>SportVue Inc.</p>
               <p>Jl. Contoh No. 123</p>
               <p>Kota, Indonesia 12345</p>
               <p>info@sportvue.com</p>
           </div>
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
