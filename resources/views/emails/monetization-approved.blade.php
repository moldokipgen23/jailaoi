<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monetization Approved</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f7;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="540" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;box-shadow:0 6px 24px rgba(0,0,0,0.06);overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#10b981 0%,#059669 100%);padding:32px 30px;text-align:center;">
                            <h1 style="color:#fff;margin:0;font-size:26px;font-weight:700;">{{ $appName }}</h1>
                            <p style="color:#fff;opacity:0.9;margin:6px 0 0;font-size:13px;text-transform:uppercase;letter-spacing:1.5px;">Monetization</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 36px 30px;">
                            <h2 style="margin:0 0 14px;font-size:20px;color:#1a1a1a;">Monetization Approved, {{ $name }}!</h2>
                            <p style="margin:0 0 18px;color:#444;line-height:1.6;font-size:15px;">
                                Your monetization application has been approved. You are now eligible to start earning from your content on JailaOi.
                            </p>
                            <p style="margin:0 0 18px;color:#444;line-height:1.6;font-size:15px;">
                                To withdraw your earnings, please complete KYC verification first.
                            </p>
                            <p style="text-align:center;margin:28px 0;">
                                <a href="{{ url('/user/monetization') }}" style="display:inline-block;background:#10b981;color:#fff;padding:14px 34px;border-radius:10px;text-decoration:none;font-weight:600;font-size:15px;">
                                    View Monetization
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f5f5f7;padding:18px 30px;text-align:center;color:#999;font-size:12px;">
                            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
