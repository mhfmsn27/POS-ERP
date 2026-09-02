<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap");

        h1, h2, h3, h4, h5, h6 {
            font-family: "Inter", sans-serif;
            color: #0f172a;
            font-weight: 600;
        }

        body {
            font-family: "Inter", sans-serif;
            font-weight: 400;
            font-size: 15px;
            line-height: 1.8;
            color: #475569;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        span {
            font-size: 12px;
            color: #64748b;
        }

        a {
            text-decoration: none;
        }
    </style>
    <title>Kode Verifikasi - POSHUB ACCOUNTING</title>
</head>

<body>
    <center style="width: 100%; background-color: #f1f5f9; padding: 32px 0;">
        <!-- Email Container -->
        <div style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

            <!-- Top Accent Bar (Solid Corporate Blue) -->
            <div style="height: 5px; background-color: #1e40af;"></div>

            <!-- Header: Logo -->
            <div style="background: #0f172a; padding: 28px 36px; text-align: center;">
                <img src="{{ asset('images/logo.webp') }}" alt="POSHUB ACCOUNTING" style="max-height: 40px; width: auto;" />
                <div style="margin-top: 8px; font-family: Inter, sans-serif; font-size: 11px; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: #94a3b8;">ENTERPRISE ACCOUNTING</div>
            </div>

            <!-- Body -->
            <div style="padding: 40px 36px;">
                <h1 style="font-size: 20px; font-weight: 700; color: #0f172a; margin: 0 0 8px 0; text-align: center;">
                    Kode Verifikasi Reset Password
                </h1>
                <p style="text-align: center; color: #64748b; font-size: 14px; margin: 0 0 28px 0;">
                    Hi <strong style="color: #0f172a;">{{$user->name}}</strong>, berikut kode untuk melanjutkan proses reset password Anda:
                </p>

                <!-- OTP Code Box -->
                <div style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 28px; text-align: center; margin: 0 0 28px 0;">
                    <div style="font-size: 42px; font-weight: 800; letter-spacing: 8px; color: #1e40af; font-family: 'Courier New', monospace;">
                        {{$user->two_factor_code}}
                    </div>
                    <p style="margin: 12px 0 0 0; font-size: 12px; color: #94a3b8;">Berlaku selama <strong style="color: #ef4444;">60 menit</strong></p>
                </div>

                <p style="font-size: 13px; color: #94a3b8; text-align: center; margin: 0;">
                    Jika Anda tidak merasa melakukan permintaan reset password, abaikan email ini.
                    Akun Anda tetap aman.
                </p>
            </div>

            <!-- Footer -->
            <div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 36px; text-align: center;">
                <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                    &copy; <?=date('Y');?> <strong style="color: #475569;">POSHUB ACCOUNTING</strong> &mdash; Enterprise POS & ERP System
                </p>
                <p style="margin: 6px 0 0 0; font-size: 11px; color: #cbd5e1;">
                    Email ini dikirim secara otomatis, harap tidak membalas pesan ini.
                </p>
            </div>
        </div>
    </center>
</body>

</html>