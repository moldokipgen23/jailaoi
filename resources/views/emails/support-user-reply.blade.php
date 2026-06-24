<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User replied to support ticket</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f7;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="540" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;box-shadow:0 6px 24px rgba(0,0,0,0.06);overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#E01E75 0%,#c41a66 100%);padding:40px 30px;text-align:center;">
                            <h1 style="color:#fff;margin:0;font-size:28px;font-weight:700;">{{ $appName }}</h1>
                            <p style="color:#fff;opacity:0.9;margin:8px 0 0;font-size:13px;text-transform:uppercase;letter-spacing:2px;">Support Ticket Reply</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px 36px 30px;">
                            <h2 style="margin:0 0 16px;font-size:22px;color:#1a1a1a;">New reply on Ticket #{{ $ticketId }}</h2>
                            <p style="margin:0 0 16px;color:#444;line-height:1.7;font-size:15px;">
                                <strong>{{ $userName }}</strong> has replied to support ticket <strong>"{{ $subject }}"</strong>.
                            </p>
                            <div style="background:#fff8f0;border-left:4px solid #E01E75;border-radius:6px;padding:20px;margin:24px 0;">
                                <p style="margin:0 0 8px;color:#888;font-size:12px;text-transform:uppercase;letter-spacing:1px;">User's message</p>
                                <p style="margin:0;color:#333;line-height:1.7;font-size:15px;white-space:pre-wrap;">{{ $message }}</p>
                            </div>
                            <p style="margin:20px 0 0;color:#444;line-height:1.7;font-size:15px;">
                                Please log in to the admin panel to respond.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f5f5f7;padding:24px 30px;text-align:center;">
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
