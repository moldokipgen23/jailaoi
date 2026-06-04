<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $details['title'] }}</title>
</head>

<body style="margin: 0; padding: 20px; font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #e0f7ff, #f5f7fa);">

    <table align="center" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <table align="center" cellpadding="0" cellspacing="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px; background: linear-gradient(135deg, #2196f3, #1976d2); color: #ffffff; text-align: center;">
                            <h1 style="margin: 0; font-size: 24px;">{{ $details['title'] }}</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px; color: #000000;">
                            <div style="font-size: 18px; line-height: 1.7; color: #000000;">
                                <p style="margin-bottom: 20px;">
                                    Hi {{ $details['user_name'] ?? 'Guest' }},<br><br>
                                    Thank you for your package purchase on <strong>{{ App_Name(); }}</strong>! 🎉<br><br>
                                    Here are the details of your transaction:
                                </p>

                                <ul style="font-size: 16px; padding-left: 20px;">
                                    <li><strong>package:</strong> {{ $details['package_name'] ?? '-' }}</li>
                                    <li><strong>Price:</strong> {{ $details['price'] ?? '-' }}</li>
                                    <li><strong>Transaction ID:</strong> {{ $details['transaction_id'] ?? '-' }}</li>
                                    <li><strong>Date:</strong> {{ $details['date'] ?? date('Y-m-d') }}</li>
                                    <li><strong>Expiry Date:</strong> <strong style="color: red;">{{ $details['expiry_date'] ?? '-' }}</strong></li>
                                </ul>

                                <p style="margin-top: 30px;">Enjoy our App!<br><strong>{{ App_Name(); }}</strong> Team</p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px; background-color: #f8f8f8; text-align: center; font-size: 14px; color: #999;">
                            &copy; {{ date('Y') }} {{ App_Name(); }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>

</html>