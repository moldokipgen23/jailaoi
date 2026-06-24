<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password changed</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f7;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="540" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;box-shadow:0 6px 24px rgba(0,0,0,0.06);overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#E01E75 0%,#c41a66 100%);padding:40px 30px;text-align:center;">
                            <h1 style="color:#fff;margin:0;font-size:28px;font-weight:700;">{{ $appName }}</h1>
                            <p style="color:#fff;opacity:0.9;margin:8px 0 0;font-size:13px;text-transform:uppercase;letter-spacing:2px;">Security Alert</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px 36px 30px;">
                            <h2 style="margin:0 0 16px;font-size:22px;color:#1a1a1a;">Hi {{ $name }},</h2>
                            <p style="margin:0 0 16px;color:#444;line-height:1.7;font-size:15px;">
                                Your <strong>{{ $appName }}</strong> account password was changed successfully.
                            </p>
                            <div style="background:#fff8e1;border-radius:10px;padding:20px;margin:24px 0;">
                                <p style="margin:0;color:#666;font-size:14px;line-height:1.6;">
                                    <strong>Didn't make this change?</strong><br>
                                    If you did not request a password reset, please contact our support team immediately to secure your account.
                                </p>
                            </div>
                            <p style="margin:20px 0 0;color:#444;line-height:1.7;font-size:15px;">
                                Stay safe,<br>
                                <strong>The {{ $appName }} Team</strong>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f5f5f7;padding:24px 30px;text-align:center;">
                            @if($socialLinks->isNotEmpty())
                            <p style="margin:0 0 12px;color:#999;font-size:12px;text-transform:uppercase;letter-spacing:1px;">Follow us</p>
                            <p style="margin:0 0 16px;">
                                @foreach($socialLinks as $link)
                                <a href="{{ $link->url }}" style="display:inline-block;background:#E01E75;color:#fff;text-decoration:none;border-radius:6px;padding:6px 14px;margin:3px;font-size:12px;font-weight:600;text-transform:capitalize;">{{ $link->name }}</a>
                                @endforeach
                            </p>
                            @endif
                            <p style="margin:0;color:#999;font-size:12px;">
                                &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
