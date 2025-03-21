<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - Alena Soccer</title>
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
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo img {
            height: 50px;
        }
        .brand-name {
            font-size: 24px;
            font-weight: bold;
            color: #9E0620;
        }
        .brand-name span {
            color: #2A2A2A;
        }
        .greeting {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        .content {
            margin-bottom: 30px;
            text-align: center;
        }
        .reset-button {
            display: inline-block;
            background-color: #9E0620;
            color: white !important;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }
        .help-text {
            margin-top: 20px;
            font-size: 14px;
            color: #64748b;
            text-align: center;
        }
        .expiry-notice {
            font-size: 13px;
            color: #64748b;
            margin-top: 15px;
            font-style: italic;
            text-align: center;
        }
        .stats-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .stats-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .stats-number {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #9E0620;
        }
        .stats-label {
            display: block;
            font-size: 14px;
            color: #64748b;
        }
        .signature {
            text-align: center;
            margin-top: 40px;
            color: #666;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Logo -->
            <div class="logo">
                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/3bc3f968d66dd0c368130525f00d42ec550c3ea8f6304c68cbb117fa6eb8dc08"
                alt="Alena Soccer Logo">
                <div class="brand-name">Alena<span>Soccer</span></div>
            </div>

            <!-- Content -->
            <div class="greeting">Reset Kata Sandi Anda</div>

            <div class="content">
                <p>Anda menerima email ini karena kami menerima permintaan reset kata sandi untuk akun Anda di Alena Soccer. Klik tombol di bawah untuk melanjutkan proses reset kata sandi.</p>

                <!-- Stats -->
                <table class="stats-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td width="48%" style="padding-right: 10px;">
                            <div class="stats-item">
                                <span class="stats-number">500+</span>
                                <span class="stats-label">Pemain Aktif</span>
                            </div>
                        </td>
                        <td width="48%" style="padding-left: 10px;">
                            <div class="stats-item">
                                <span class="stats-number">4.9</span>
                                <span class="stats-label">Rating Pengguna</span>
                            </div>
                        </td>
                    </tr>
                </table>

                <center>
                    <a href="{{ $resetLink }}" class="reset-button">
                        Reset Kata Sandi Sekarang
                    </a>
                </center>

                <div class="help-text">
                    <p>Jika Anda tidak meminta reset kata sandi, tidak perlu melakukan tindakan lebih lanjut.</p>
                </div>

                <div class="expiry-notice">
                    Tautan reset kata sandi ini akan kedaluwarsa dalam 60 menit.
                </div>
            </div>

            <!-- Signature -->
            <div class="signature">
                <p>Salam Olahraga,<br>Tim Alena Soccer</p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>Jika Anda mengalami masalah saat mengklik tombol "Reset Kata Sandi Sekarang", salin dan tempel URL di bawah ini ke browser web Anda:</p>
                <p style="word-break: break-all; color: #9E0620;">{{ $resetLink }}</p>
                <p>© {{ date('Y') }} Alena Soccer. Seluruh hak cipta dilindungi.</p>
            </div>
        </div>
    </div>
</body>
</html>
