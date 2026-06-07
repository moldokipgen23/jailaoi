<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Artist application status</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f7;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="540" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;box-shadow:0 6px 24px rgba(0,0,0,0.06);overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#E01E75 0%,#c41a66 100%);padding:32px 30px;text-align:center;">
                            <h1 style="color:#fff;margin:0;font-size:26px;font-weight:700;">{{ $appName }}</h1>
                            <p style="color:#fff;opacity:0.9;margin:6px 0 0;font-size:13px;text-transform:uppercase;letter-spacing:1.5px;">Artist Portal</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 36px 30px;">
                            <h2 style="margin:0 0 14px;font-size:20px;color:#1a1a1a;">Hi {{ $name }},</h2>
                            <p style="margin:0 0 18px;color:#444;line-height:1.6;font-size:15px;">
                                Thank you for your interest in becoming an artist on {{ $appName }}. Unfortunately, your application for <strong>{{ $artistName }}</strong> could not be approved at this time.
                            </p>
                            @if($adminNote)
                            <div style="background:#fff5f5;border-left:4px solid #E01E75;padding:14px 18px;margin:20px 0;border-radius:6px;">
                                <p style="margin:0;color:#444;font-size:14px;line-height:1.6;">
                                    <strong>Note from admin:</strong><br>{{ $adminNote }}
                                </p>
                            </div>
                            @endif
                            <p style="margin:20px 0 0;color:#444;line-height:1.6;font-size:15px;">
                                You may re-apply with updated information at any time.
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
