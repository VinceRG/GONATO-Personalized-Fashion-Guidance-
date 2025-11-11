<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Verify OTP</title>
    <link rel="stylesheet" href="public/css/login.css">
    <link rel="stylesheet" href="public/css/alerts.css">
</head>
<body>
    <div class="content">
        <h1>Verify OTP</h1>

        <?php if (!empty($message)): ?>
            <div class="alert <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Enter OTP</label>
            <input type="text" name="otp" placeholder="Enter the OTP sent to your email" required>

            <button type="submit">Verify</button>

            <!-- ✅ Same placement style as all other pages -->
            <p><a href="index.php?page=forgot">Resend OTP</a></p>
        </form>
    </div>
</body>
</html>
