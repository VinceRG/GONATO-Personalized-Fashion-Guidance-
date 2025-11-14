<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Enter Email</title>
    <link rel="stylesheet" href="public/css/login.css">
</head>
<body>
    <div class="content">
        <h1>Forgot Password</h1>

        <?php if (!empty($message)): ?>
            <div class="alert <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your registered email" required>

            <button type="submit">Send OTP</button>

            <!-- ✅ Same format as the Reset Password page -->
            <p><a href="index.php?page=login">Back to Login</a></p>
        </form>
    </div>
</body>
</html>
