<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $details['title'] }}</title>
</head>

<body>
    <div class="container">
        <h4>Hello,</h4>

        <p><strong>{{ $details['title'] }}</strong></p>

        <p>{{ $details['body'] }}</p>

        <div class="footer">
            <p>Best regards,</p>
            <p><strong>The {{ App_Name() }} Team</strong></p>
        </div>
    </div>
</body>

</html>