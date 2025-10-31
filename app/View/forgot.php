<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Amarelle</title>
    <link rel="stylesheet" href="public/css/login.css">
    <style>
        /* your existing CSS */
    </style>
</head>
<body>
    <div class="content">
        <h1>Forgot Password</h1>

        <?php 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        ?>

        <?php if (!isset($_SESSION['otp_sent'])): ?>
            <!-- STEP 1: Ask for email -->
            <p>Enter your email to receive a password reset code.</p>
            <form method="POST" action="index.php?page=forgot">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Send OTP</button>
            </form>
        <?php else: ?>
            <!-- STEP 2: Ask for OTP + new password -->
            <p>Check your email for the OTP code, then reset your password.</p>
            <form method="POST" action="index.php?page=forgot">
                <label>Enter OTP</label>
                <input type="text" name="otp" placeholder="Enter the 6-digit OTP" required>

                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Enter new password" required>

                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>

        <p><a href="index.php?page=login">Back to login</a></p>
    </div>
</body>
</html>
