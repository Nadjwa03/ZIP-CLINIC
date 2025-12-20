<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP - Klinik ZIP</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7fa;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f7fa; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 500px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);">
                    
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #3b82f6 0%, #6366f1 100%); padding: 40px 30px; text-align: center; border-radius: 16px 16px 0 0;">
                            <div style="width: 70px; height: 70px; background-color: #ffffff; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 32px; line-height: 70px;">🦷</span>
                            </div>
                            <h1 style="color: #ffffff; font-size: 24px; font-weight: 700; margin: 0;">Klinik ZIP</h1>
                            <p style="color: rgba(255, 255, 255, 0.9); font-size: 14px; margin: 8px 0 0;">Dental & Oral Care</p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 40px 30px;">
                            {{-- Title based on type --}}
                            @if($type === 'login')
                                <h2 style="color: #1e293b; font-size: 20px; font-weight: 600; margin: 0 0 16px; text-align: center;">
                                    Kode Login Anda
                                </h2>
                                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 24px; text-align: center;">
                                    Gunakan kode OTP di bawah ini untuk masuk ke akun Anda di Klinik ZIP.
                                </p>
                            @elseif($type === 'register')
                                <h2 style="color: #1e293b; font-size: 20px; font-weight: 600; margin: 0 0 16px; text-align: center;">
                                    Verifikasi Email Anda
                                </h2>
                                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 24px; text-align: center;">
                                    Terima kasih telah mendaftar! Gunakan kode OTP di bawah ini untuk memverifikasi email Anda.
                                </p>
                            @elseif($type === 'reset_password')
                                <h2 style="color: #1e293b; font-size: 20px; font-weight: 600; margin: 0 0 16px; text-align: center;">
                                    Reset Password
                                </h2>
                                <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin: 0 0 24px; text-align: center;">
                                    Kami menerima permintaan untuk reset password akun Anda. Gunakan kode OTP di bawah ini.
                                </p>
                            @endif

                            {{-- OTP Code Box --}}
                            <div style="background: linear-gradient(135deg, #eff6ff 0%, #eef2ff 100%); border: 2px dashed #3b82f6; border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 24px;">
                                <p style="color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px;">Kode OTP Anda</p>
                                <div style="font-size: 36px; font-weight: 700; color: #3b82f6; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                                    {{ $otp }}
                                </div>
                            </div>

                            {{-- Warning --}}
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 0 8px 8px 0; padding: 16px; margin-bottom: 24px;">
                                <p style="color: #92400e; font-size: 13px; margin: 0; line-height: 1.5;">
                                    <strong>⏰ Kode berlaku selama 5 menit.</strong><br>
                                    Jangan bagikan kode ini kepada siapapun, termasuk pihak yang mengaku dari Klinik ZIP.
                                </p>
                            </div>

                            {{-- Info --}}
                            <p style="color: #94a3b8; font-size: 13px; line-height: 1.6; margin: 0; text-align: center;">
                                Jika Anda tidak meminta kode ini, abaikan email ini atau hubungi tim support kami.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 30px; text-align: center; border-radius: 0 0 16px 16px; border-top: 1px solid #e2e8f0;">
                            <p style="color: #94a3b8; font-size: 12px; margin: 0 0 8px;">
                                © {{ date('Y') }} Klinik ZIP. All rights reserved.
                            </p>
                            <p style="color: #cbd5e1; font-size: 11px; margin: 0;">
                                Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>