<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap">
    <title>Login - Amarelle</title>
    <link rel="stylesheet" href="public/css/login.css">

    <!-- ✅ FONT AWESOME ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- ✅ reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        .footer p {
    padding: 2rem 3rem;
    text-align: center;
    color: #78716C;
    font-size: 0.85rem;
    font-weight: 300;
    position: relative;
    z-index: 10;
}
.g-recaptcha {
    margin: 1rem 0;
    display: flex;
    justify-content: center;
}
    </style>
</head>

<body>
    <div class="header">
        <a href="index.php?page=landing" class="logo-link">
            <div class="logo">Amarelle</div>
        </a>
    </div>

    <div class="content">
        <h1>Sign In</h1>

        <form id="loginForm" action="index.php?page=login" method="POST">

            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($remainingAttempts) && $remainingAttempts > 0 && $remainingAttempts < 3): ?>
                <div class="message warning">
                    Warning: You have <?php echo $remainingAttempts; ?> attempt(s) remaining before your account is locked.
                </div>
            <?php endif; ?>

            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username"
                   placeholder="Enter your username or email"
                   value="<?php echo htmlspecialchars($username ?? ''); ?>"
                   <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>>

                   

            <div class="form-group password-wrapper">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password">
                    
            </div>

            <div class="g-recaptcha" data-sitekey="6LeCugUsAAAAAMevrBVqSjs6AG8SsQZ8qrJGvjDZ"></div>

            <button type="submit"
                <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>>
                Login
            </button>

            <p class="forgotpass">
                <a class="forgotpass" href="index.php?page=forgot">Forgot your password?</a>
            </p>

            <p class="sigup">Don't have an account yet?
                <a href="index.php?page=register">Sign Up here</a>
            </p>

            <!-- ✅ TERMS & CONDITIONS TEXT -->
            <p class="terms-text">
                By creating your account or signing in, you agree to our<br>
                <a href="index.php?page=policy">Terms and Conditions</a> &
                <a href="index.php?page=policy">Privacy Policy</a>
            </p>

        </form>
    </div>

    <div class="footer">
        <p>© 2025 Amarelle. All rights reserved.</p>
    </div>

    

</body>
</html>
