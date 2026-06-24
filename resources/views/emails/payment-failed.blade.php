<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment failed</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f7;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f7;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="540" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;box-shadow:0 6px 24px rgba(0,0,0,0.06);overflow:hidden;">
                    <tr>
                        <td style="background:linear-gradient(135deg,#E01E75 0%,#c41a66 100%);padding:32px 30px;text-align:center;">
                            <h1 style="color:#fff;margin:0;font-size:26px;font-weight:700;">{{ $appName }}</h1>
                            <p style="color:#fff;opacity:0.9;margin:6px 0 0;font-size:13px;text-transform:uppercase;letter-spacing:1.5px;">Payment Failed</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 36px 30px;">
                            <h2 style="margin:0 0 14px;font-size:20px;color:#1a1a1a;">Hi {{ $name }},</h2>
                            <p style="margin:0 0 18px;color:#444;line-height:1.6;font-size:15px;">
                                We were unable to process your payment for the <strong>{{ $packageName }}</strong> subscription.
                            </p>
                            <div style="background:#fbe9e7;border-radius:10px;padding:20px;margin:20px 0;">
                                <table width="100%" cellpadding="6">
                                    <tr>
                                        <td style="color:#888;font-size:14px;">Package</td>
                                        <td style="text-align:right;font-size:16px;font-weight:600;color:#1a1a1a;">{{ $packageName }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#888;font-size:14px;">Amount</td>
                                        <td style="text-align:right;font-size:18px;font-weight:700;color:#1a1a1a;">{{ number_format($amount, 2) }}</td>
                                    </tr>
                                    @if($reason)
                                    <tr>
                                        <td style="color:#888;font-size:14px;">Reason</td>
                                        <td style="text-align:right;font-size:14px;color:#d32f2f;">{{ $reason }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <p style="margin:20px 0 0;color:#444;line-height:1.6;font-size:15px;">
                                Please check your payment details and try again, or contact support if you need assistance.
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
