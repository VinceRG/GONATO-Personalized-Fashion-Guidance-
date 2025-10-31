<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Amarelle</title>
    <link rel="stylesheet" href="public/css/login.css">
</head>
<body>
    <div class="content">
        <h1>Verify OTP</h1>
        <p>Enter the 6-digit code sent to your email.</p>

        <form method="POST" action="index.php?page=forgot">
            <input type="text" name="otp" maxlength="6" required placeholder="Enter OTP">
            <button type="submit">Verify</button>
        </form>
    </div>
</body>
</html>
