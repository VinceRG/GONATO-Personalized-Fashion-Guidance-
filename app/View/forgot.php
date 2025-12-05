<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
    <link rel="icon" type="image/png" href="public/image/amarelle.png">
<link rel="stylesheet" href="public/css/forgot.css">
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your registered email" autocomplete="email">

        <!-- reCAPTCHA -->
        <div class="g-recaptcha" data-sitekey="6LeCugUsAAAAAMevrBVqSjs6AG8SsQZ8qrJGvjDZ"></div>

        <button type="submit">Send OTP</button>

        <p><a href="index.php?page=login">Back to Login</a></p>
    </form>
</div>
</body>
</html>
