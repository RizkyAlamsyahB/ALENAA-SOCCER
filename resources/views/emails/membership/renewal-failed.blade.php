<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpanjangan Membership Gagal - Alena Soccer</title>
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
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
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
        .alert-badge {
            width: 90px;
            height: 90px;
            margin: 0 auto 20px;
            background-color: #fef2f2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .alert-badge svg {
            width: 45px;
            height: 45px;
            color: #dc2626;
        }
        .greeting {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
            color: #dc2626;
        }
        .content {
            margin-bottom: 35px;
            text-align: left;
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
        .stats-container {
            background-color: #fcfcfc;
            border-radius: 12px;
            padding: 20px;
            margin: 30px 0;
            border: 1px solid #f0f0f0;
        }
        .stats-table {
            width: 100%;
            margin-bottom: 5px;
        }
        .stats-item {
            background: #fff;
            border-radius: 10px;
            padding: 18px 15px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid #f5f5f5;
        }
        .stats-number {
            display: block;
            font-size: 22px;
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 5px;
        }
        .stats-label {
            display: block;
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }
        .error-box {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 18px 20px;
            margin: 25px 0;
            border-radius: 6px;
            text-align: left;
        }
        .error-title {
            color: #dc2626;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 18px;
        }
        .conflict-list {
            list-style-type: none;
            padding: 0;
            margin: 25px 0;
        }
        .conflict-list li {
            background-color: #fff5f5;
            padding: 15px 18px;
            margin-bottom: 10px;
            border-radius: 8px;
            font-weight: 500;
            border-left: 3px solid #dc2626;
            position: relative;
            display: flex;
            align-items: center;
        }
        .conflict-list li svg {
            flex-shrink: 0;
            margin-right: 12px;
            color: #dc2626;
            width: 20px;
            height: 20px;
        }
        .conflict-list li span {
            flex-grow: 1;
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
        .notice-box {
            background-color: #fffbeb;
            border-left: 4px solid #d97706;
            padding: 18px 20px;
            margin: 25px 0;
            border-radius: 6px;
        }
        .notice-title {
            color: #d97706;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 18px;
        }
        .action-needed {
            display: inline-block;
            background-color: #dc2626;
            color: white;
            font-size: 12px;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .center-content {
            text-align: center;
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

                <div class="alert-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Content -->
            <div class="greeting">Perpanjangan Membership Gagal</div>

            <div class="content">
                <div class="center-content">
                    <span class="action-needed">Perhatian Admin</span>
                </div>

                <div class="error-box">
                    <div class="error-title">Perpanjangan Otomatis Gagal</div>
                    <p>Perpanjangan otomatis untuk membership berikut gagal karena terdapat konflik jadwal:</p>
                </div>

                <!-- Stats -->
                <div class="stats-container">
                    <table class="stats-table" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="48%" style="padding-right: 10px;">
                                <div class="stats-item">
                                    <span class="stats-number">{{ $subscription->id }}</span>
                                    <span class="stats-label">ID Subscription</span>
                                </div>
                            </td>
                            <td width="48%" style="padding-left: 10px;">
                                <div class="stats-item">
                                    <span class="stats-number">{{ $subscription->membership->field->name }}</span>
                                    <span class="stats-label">Lapangan</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table class="stats-table" cellpadding="0" cellspacing="0" style="margin-top: 15px;">
                        <tr>
                            <td width="48%" style="padding-right: 10px;">
                                <div class="stats-item">
                                    <span class="stats-number">{{ $subscription->user->name }}</span>
                                    <span class="stats-label">Nama Pengguna</span>
                                </div>
                            </td>
                            <td width="48%" style="padding-left: 10px;">
                                <div class="stats-item">
                                    <span class="stats-number">{{ $subscription->membership->name }}</span>
                                    <span class="stats-label">Paket Membership</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="section-title">Detail Konflik Jadwal</div>

                <p>Berikut adalah detail konflik jadwal yang menyebabkan kegagalan perpanjangan:</p>

                <ul class="conflict-list">
                    @foreach($unavailableSessions as $session)
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ $session }}</span>
                    </li>
                    @endforeach
                </ul>

                <div class="notice-box">
                    <div class="notice-title">Tindakan yang Diperlukan</div>
                    <p>Perpanjangan otomatis telah gagal dan memerlukan tindakan manual dari admin. Silakan:</p>
                    <ol>
                        <li>Hubungi pelanggan untuk menawarkan jadwal alternatif</li>
                        <li>Atur jadwal baru yang tidak bertabrakan dengan booking yang sudah ada</li>
                        <li>Proses perpanjangan secara manual melalui panel admin</li>
                    </ol>
                </div>

                {{-- <div class="center-content">
                    <a href="{{ route('admin.memberships.subscriptions.show', $subscription->id) }}" class="details-button">
                        Lihat Detail Subscription
                    </a>
                </div> --}}

                <div class="help-text">
                    <p>Email ini dikirim secara otomatis oleh sistem. Harap jangan membalas email ini.</p>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Signature -->
            <div class="signature">
                <p>Salam,<br><strong>Tim Sistem Alena Soccer</strong></p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="social-links">
                    <a href="#"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.04C6.5 2.04 2 6.53 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.85C10.44 7.34 11.93 5.96 14.22 5.96C15.31 5.96 16.45 6.15 16.45 6.15V8.62H15.19C13.95 8.62 13.56 9.39 13.56 10.18V12.06H16.34L15.89 14.96H13.56V21.96C15.9 21.59 18.03 20.39 19.62 18.61C21.2 16.83 22.13 14.56 22.13 12.21C22.13 7.05 17.63 2.54 12.13 2.54L12 2.04Z"/></svg></a>
                    <a href="#"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z"/></svg></a>
                    <a href="#"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19.05 4.91A9.816 9.816 0 0 0 12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01zm-7.01 15.24c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.264 8.264 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.183 8.183 0 0 1 2.41 5.83c.02 4.54-3.68 8.23-8.22 8.23zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43s.17-.25.25-.41c.08-.17.04-.31-.02-.43s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.07-.11-.22-.16-.47-.28z"/></svg></a>
                </div>
                <p>© {{ date('Y') }} Alena Soccer. Seluruh hak cipta dilindungi.</p>
                <p style="font-size: 12px; color: #999;">Alamat: Jl. Lapangan Futsal No. 123, Jakarta • Telp: +62 812-3456-7890</p>
            </div>
        </div>
    </div>
</body>
</html>
