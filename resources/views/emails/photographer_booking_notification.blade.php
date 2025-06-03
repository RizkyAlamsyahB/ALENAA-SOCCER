<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Jadwal Foto Baru - Alena Soccer</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #2A2A2A;
            margin: 0;
            padding: 0;
            background-color: #f6f8fd;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .email-container {
            background: white;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .email-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .logo {
            display: inline-block;
            margin-bottom: 15px;
        }

        .logo img {
            height: 60px;
        }

        .brand-name {
            font-size: 26px;
            font-weight: bold;
            color: #9E0620;
            letter-spacing: 0.5px;
        }

        .brand-name span {
            color: #2A2A2A;
        }

        .notification-badge {
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;
            background-color: #eff6ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-badge svg {
            width: 45px;
            height: 45px;
            color: #2563eb;
        }

        .greeting {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
            color: #2563eb;
        }

        .content {
            margin-bottom: 35px;
            text-align: center;
        }

        .content p {
            margin-bottom: 15px;
            font-size: 16px;
        }

        .details-button {
            display: inline-block;
            background-color: #9E0620;
            color: white !important;
            text-decoration: none;
            padding: 14px 34px;
            border-radius: 50px;
            font-weight: bold;
            margin: 25px 0;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .details-button:hover {
            background-color: #870518;
            transform: translateY(-2px);
        }

        .booking-details {
            background-color: #fcfcfc;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border: 1px solid #f0f0f0;
            text-align: left;
        }

        .detail-row {
            margin-bottom: 15px;
            display: flex;
        }

        .detail-label {
            font-weight: 500;
            color: #64748b;
            width: 130px;
        }

        .detail-value {
            font-weight: 600;
            color: #1e293b;
            flex: 1;
        }

        .booking-box {
            background-color: #eff6ff;
            border-left: 4px solid #2563eb;
            padding: 18px 20px;
            margin: 25px 0;
            border-radius: 6px;
            text-align: left;
        }

        .booking-title {
            color: #2563eb;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .divider {
            height: 1px;
            background-color: #eee;
            margin: 30px 0;
        }

        .section-title {
            text-align: center;
            font-size: 22px;
            color: #333;
            margin: 35px 0 20px;
            position: relative;
        }

        .section-title:after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background-color: #9E0620;
            margin: 12px auto 0;
            border-radius: 2px;
        }

        .equipment-list {
            list-style-type: none;
            padding: 0;
            margin: 25px 0;
            text-align: left;
        }

        .equipment-list li {
            padding: 8px 0;
            display: flex;
            align-items: center;
        }

        .equipment-list li svg {
            color: #2563eb;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .tips-box {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
            border: 1px solid #f0f0f0;
        }

        .tips-title {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .help-text {
            margin-top: 25px;
            font-size: 15px;
            color: #64748b;
            text-align: center;
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }

        .signature {
            text-align: center;
            margin-top: 40px;
            color: #555;
            font-weight: 500;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #888;
        }

        .social-links {
            margin: 15px 0;
        }

        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #9E0620;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <div class="logo">
                    <div class="brand-name">ALENA<span>SOCCER</span></div>
                </div>

                <div class="notification-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>

            <!-- Content -->
            <div class="greeting">Jadwal Foto Baru!</div>

            <div class="content">
                <p>Halo <strong>{{ $photographer->name ?? 'Fotografer' }}</strong>,</p>
                <p>Anda memiliki jadwal foto baru dengan pelanggan kami. Berikut adalah detail sesi foto yang telah
                    dijadwalkan:</p>

                <div class="booking-box">
                    <div class="booking-title">Informasi Sesi Foto</div>
                    <p>Mohon untuk hadir tepat waktu dan mempersiapkan peralatan yang diperlukan untuk sesi foto ini.
                    </p>
                </div>

                <!-- Booking Details -->
                <div class="booking-details">
                    <div class="detail-row">
                        <div class="detail-label">Pelanggan:</div>
                        <div class="detail-value">{{ $user->name ?? 'Pelanggan' }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Tanggal:</div>
                        <div class="detail-value">
                            {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->dayName }},
                            {{ \Carbon\Carbon::parse($booking->start_time)->locale('id')->isoFormat('D MMMM Y') }}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Waktu:</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }} WIB</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Lokasi:</div>
                        <div class="detail-value">
                            @php
                                // Get the photographer's assigned field based on userID from seeder
// The photographer's user_id (not photographer ID) is what's stored in the field table
$assignedField = null;
$photographerUserId = $photographer->id; // This is the user ID

try {
    $assignedField = \App\Models\Field::where(
        'photographer_id',
        $photographerUserId,
    )->first();
} catch (\Exception $e) {
    // Handle error silently
}

$fieldName = $assignedField ? $assignedField->name : null;
$fieldNumber = null;

// Try to extract field number from photographer name if available
if (preg_match('/Lapangan (\d+)/', $photographer->name, $matches)) {
                                    $fieldNumber = $matches[1];
                                }
                            @endphp

                            @if (isset($booking->location) && $booking->location)
                                {{ $booking->location }}
                            @elseif(isset($booking->field) && $booking->field)
                                Lapangan {{ $booking->field->name }}
                            @elseif($fieldName)
                                {{ $fieldName }}
                            @elseif($fieldNumber)
                                Lapangan {{ $fieldNumber }}
                            @else
                                Lokasi tidak ditentukan
                            @endif
                        </div>
                    </div>

                </div>

                <div class="divider"></div>


                <div class="help-text">
                    <p>Jika Anda memiliki pertanyaan, silakan hubungi pihak Alena Soccer segera melalui WhatsApp di
                        <strong>+62 xxx-xxxx-xxxx</strong>.</p>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Signature -->
            <div class="signature">
                <p>Salam Olahraga,<br><strong>Tim Alena Soccer</strong></p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="social-links">
                    <a href="#"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2.04C6.5 2.04 2 6.53 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.85C10.44 7.34 11.93 5.96 14.22 5.96C15.31 5.96 16.45 6.15 16.45 6.15V8.62H15.19C13.95 8.62 13.56 9.39 13.56 10.18V12.06H16.34L15.89 14.96H13.56V21.96C15.9 21.59 18.03 20.39 19.62 18.61C21.2 16.83 22.13 14.56 22.13 12.21C22.13 7.05 17.63 2.54 12.13 2.54L12 2.04Z" />
                        </svg></a>
                    <a href="#"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z" />
                        </svg></a>
                    <a href="#"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M19.05 4.91A9.816 9.816 0 0 0 12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01zm-7.01 15.24c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.264 8.264 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.183 8.183 0 0 1 2.41 5.83c.02 4.54-3.68 8.23-8.22 8.23zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43s.17-.25.25-.41c.08-.17.04-.31-.02-.43s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.07-.11-.22-.16-.47-.28z" />
                        </svg></a>
                </div>
                <p>© {{ date('Y') }} Alena Soccer. Seluruh hak cipta dilindungi.</p>
                <p style="font-size: 12px; color: #999;">Alamat: Jl. Lapangan Futsal No. 123, Jakarta • Telp: +62
                    812-3456-7890</p>
            </div>
        </div>
    </div>
</body>

</html>
