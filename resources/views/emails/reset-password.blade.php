<!-- resources/views/emails/reset-password.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SportVue</title>
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
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
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
            color: #666;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .expiry-notice {
            font-size: 13px;
            color: #666;
            margin-top: 15px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Logo -->
            <div class="logo">
                <img src="https://cdn.builder.io/api/v1/image/assets/TEMP/3bc3f968d66dd0c368130525f00d42ec550c3ea8f6304c68cbb117fa6eb8dc08"
                alt="SportVue Logo">
                <div class="brand-name">Sport<span>Vue</span></div>
            </div>

            <!-- Content -->
            <div class="greeting">Hello!</div>

            <div class="content">
                <p>You are receiving this email because we received a password reset request for your account.</p>

                <center>
                    <a href="{{ $resetLink }}" class="reset-button">
                        Reset Password
                    </a>
                </center>

                <div class="help-text">
                    <p>If you didn't request a password reset, no further action is required.</p>
                </div>

                <div class="expiry-notice">
                    This password reset link will expire in 60 minutes.
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:</p>
                <p style="word-break: break-all; color: #9E0620;">{{ $resetLink }}</p>
                <p>© {{ date('Y') }} SportVue. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
