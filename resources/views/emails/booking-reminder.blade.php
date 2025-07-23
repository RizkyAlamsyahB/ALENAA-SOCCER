<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Booking</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
        }
        .reminder-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .reminder-24hours {
            background-color: #e3f2fd;
            color: #1976d2;
        }
        .reminder-1hour {
            background-color: #fff3e0;
            color: #f57c00;
        }
        .reminder-30minutes {
            background-color: #ffebee;
            color: #d32f2f;
        }
        .booking-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .booking-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .booking-details {
            display: grid;
            gap: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #666;
            min-width: 120px;
        }
        .detail-value {
            color: #333;
            text-align: right;
        }
        .time-highlight {
            background-color: #667eea;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            text-align: center;
            margin: 20px 0;
            font-weight: 600;
            font-size: 16px;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .action-button:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            margin: 30px 0;
            border-radius: 1px;
        }

        @media (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 0;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            .detail-value {
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Reminder Booking</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Jangan sampai terlewat!</p>
        </div>

        <div class="content">
            <!-- Reminder Type Badge -->
            @if($reminderType == '24hours')
                <div class="reminder-badge reminder-24hours">
                    📅 Reminder 24 Jam
                </div>
            @elseif($reminderType == '1hour')
                <div class="reminder-badge reminder-1hour">
                    ⏰ Reminder 1 Jam
                </div>
            @elseif($reminderType == '30minutes')
                <div class="reminder-badge reminder-30minutes">
                    🚨 Reminder 30 Menit
                </div>
            @endif

            <!-- Greeting -->
            <p style="font-size: 16px; margin-bottom: 10px;">
                Halo <strong>{{ $booking->user->name }}</strong>,
            </p>

            @if($reminderType == '24hours')
                <p>Ini adalah pengingat bahwa Anda memiliki booking <strong>besok</strong>.</p>
            @elseif($reminderType == '1hour')
                <p>Booking Anda akan dimulai dalam <strong>1 jam lagi</strong>. Bersiap-siaplah!</p>
            @elseif($reminderType == '30minutes')
                <p>⚡ Booking Anda akan dimulai dalam <strong>30 menit lagi</strong>. Segera bersiap!</p>
            @endif

            <!-- Booking Details Card -->
            <div class="booking-card">
                @if(isset($booking->field))
                    <!-- Field Booking -->
                    <div class="booking-title">
                        🏟️ {{ $booking->field->name }}
                    </div>
                @elseif(isset($booking->rentalItem))
                    <!-- Rental Booking -->
                    <div class="booking-title">
                        🛍️ {{ $booking->rentalItem->name }}
                    </div>
                @elseif(isset($booking->photographer))
                    <!-- Photographer Booking -->
                    <div class="booking-title">
                        📸 {{ $booking->photographer->name }}
                    </div>
                @endif

                <div class="booking-details">
                    <div class="detail-row">
                        <span class="detail-label">📅 Tanggal:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('d F Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">🕐 Waktu:</span>
                        <span class="detail-value">
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB
                        </span>
                    </div>
                    @if(isset($booking->quantity))
                    <div class="detail-row">
                        <span class="detail-label">📦 Jumlah:</span>
                        <span class="detail-value">{{ $booking->quantity }} unit</span>
                    </div>
                    @endif
                    @if(isset($booking->field->address))
                    <div class="detail-row">
                        <span class="detail-label">📍 Lokasi:</span>
                        <span class="detail-value">{{ $booking->field->address }}</span>
                    </div>
                    @endif
                    <div class="detail-row">
                        <span class="detail-label">💰 Total:</span>
                        <span class="detail-value">Rp {{ number_format($booking->total_price ?? $booking->price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Time Countdown -->
            @php
                $now = \Carbon\Carbon::now();
                $bookingTime = \Carbon\Carbon::parse($booking->start_time);
                $diffInHours = $now->diffInHours($bookingTime, false);
                $diffInMinutes = $now->diffInMinutes($bookingTime, false);
            @endphp

            <div class="time-highlight">
                @if($diffInHours >= 24)
                    ⏳ {{ floor($diffInHours / 24) }} hari {{ $diffInHours % 24 }} jam lagi
                @elseif($diffInHours >= 1)
                    ⏳ {{ $diffInHours }} jam {{ $diffInMinutes % 60 }} menit lagi
                @else
                    ⏳ {{ $diffInMinutes }} menit lagi
                @endif
            </div>

            <div class="divider"></div>

            <!-- Call to Action -->
            @if($reminderType == '24hours')
                <p><strong>💡 Tips untuk besok:</strong></p>
                <ul style="color: #666; padding-left: 20px;">
                    <li>Pastikan Anda tiba 15 menit sebelum waktu booking</li>
                    <li>Bawa identitas diri yang valid</li>
                    <li>Siapkan perlengkapan yang diperlukan</li>
                    @if(isset($booking->field))
                    <li>Kenakan pakaian olahraga yang nyaman</li>
                    @endif
                </ul>
            @elseif($reminderType == '1hour')
                <p><strong>🎯 Checklist 1 jam sebelum:</strong></p>
                <ul style="color: #666; padding-left: 20px;">
                    <li>✅ Siapkan perlengkapan</li>
                    <li>✅ Berangkat menuju lokasi</li>
                    <li>✅ Pastikan kendaraan dalam kondisi baik</li>
                </ul>
            @elseif($reminderType == '30minutes')
                <p style="color: #d32f2f; font-weight: 600;">
                    🚨 Segera berangkat menuju lokasi! Pastikan Anda tidak terlambat.
                </p>
            @endif

            <!-- Action Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('user.payment.history') }}" class="action-button">
                    📋 Lihat Detail Booking
                </a>
            </div>

            <!-- Additional Info -->
            @if($reminderType == '30minutes')
                <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 15px; margin: 20px 0;">
                    <p style="margin: 0; color: #856404;">
                        <strong>⚠️ Penting:</strong> Jika Anda tidak bisa hadir, silakan hubungi customer service kami segera.
                    </p>
                </div>
            @endif
        </div>

        <div class="footer">
            <p style="margin: 0 0 10px 0;">
                <strong>Need Help?</strong><br>
                Hubungi customer service kami di <a href="mailto:support@example.com">support@example.com</a><br>
                atau WhatsApp: <a href="https://wa.me/6281234567890">0812-3456-7890</a>
            </p>

            <div class="divider" style="margin: 20px 0;"></div>

            <p style="margin: 0; font-size: 12px; color: #999;">
                Email ini dikirim secara otomatis. Jangan balas email ini.<br>
                © {{ date('Y') }} SportBooking. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
