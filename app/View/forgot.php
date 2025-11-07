<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Amarelle</title>
    <link rel="stylesheet" href="public/css/login.css">

    <style>
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }
        .alert.error { background: #ffdddd; color: #a30000; }
        .alert.success { background: #ddffdd; color: #006600; }
    </style>

</head>
<body>
    <div class="content">
        <h1>Forgot Password</h1>
<?php if (!empty($message)): ?>
    <div class="alert <?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
<?php endif; ?>
        <?php 
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        ?>

        <!-- ✅ Success or Error Message -->
        <?php if (!empty($message)): ?>
            <div class="alert <?= $messageType ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['otp_sent'])): ?>
            <!-- ✅ STEP 1: Ask for email -->
            <form method="POST" action="index.php?page=forgot">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Send OTP</button>
            </form>

        <?php else: ?>
            <!-- ✅ STEP 2: Ask for new password + confirm -->

            <form method="POST" action="index.php?page=forgot">

                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Enter new password" required>

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm new password" required>

                <button type="submit">Reset Password</button>
            </form>

        <?php endif; ?>

        <p><a href="index.php?page=login">Back to login</a></p>
    </div>
</body>
</html>
