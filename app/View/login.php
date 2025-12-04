<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap">
    <title>Login - Amarelle</title>
    <link rel="stylesheet" href="public/css/login.css">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>


    </style>
</head>

<body>

<?php
// ✅ Get remembered username from cookie, and decide what to show in the input
$rememberedUsername = $_COOKIE['remember_username'] ?? '';
$inputUsername = $username ?? $rememberedUsername ?? '';
?>

<!-- OTP MODAL -->
<div id="otpModal" class="otp-modal">
    <div class="otp-modal-content">
        <h2>Email Verification</h2>
        <p>Please verify your identity. An OTP has been sent to your email.<br>
           Check your email and input the OTP below.</p>

        <input type="text" id="otpInput" maxlength="6" placeholder="Enter 6-digit OTP">

        <button id="verifyOtpBtn" class="otp-btn">Verify OTP</button>
        <button id="cancelOtpBtn" class="otp-cancel-btn">Cancel</button>

        <p id="otpError" style="color:red; display:none; margin-top:10px;">Invalid or expired OTP. Try again.</p>
    </div>
</div>

<div class="header">
    <a href="index.php?page=landing" class="logo-link"><img src="public/image/amarelle.png" alt="Amarelle Logo" class="brand-logo" style="height: 50px; width: auto; margin-left: 20px; vertical-align: middle;">
</a>
</div>

<div class="content">

    <h1>Welcome</h1>
    <p>Please enter your details.</p>

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
        <input
            type="text"
            id="username"
            name="username"
            placeholder="Enter your username or email"
            value="<?php echo htmlspecialchars($inputUsername); ?>"
            <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>
        >

        <div class="form-group password-wrapper">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>
            >
        </div>

        <p class="forgotpass">
            <a class="forgotpass" href="index.php?page=forgot">Forgot your password?</a>
        </p>

        <label class="remember-me">
            <input
                type="checkbox"
                name="remember_me"
                value="1"
                <?php echo !empty($rememberedUsername) ? 'checked' : ''; ?>
            >
            Remember me on this device
        </label>

        <!-- reCAPTCHA removed -->

        <button type="submit"
            <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>>
            Login
        </button>

        <p class="sigup">Don't have an account yet?
            <a href="index.php?page=register">Sign Up here</a>
        </p>

        <p class="terms-text">
            By creating your account or signing in, you agree to our<br>
            <a href="index.php?page=policy">Privacy Policy & Cookies and Consent</a>
        </p>

    </form>
</div>

<div class="footer">
    <p>© 2025 Amarelle. All rights reserved.</p>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const otpModal   = document.getElementById("otpModal");
    const otpInput   = document.getElementById("otpInput");
    const otpError   = document.getElementById("otpError");
    const verifyBtn  = document.getElementById("verifyOtpBtn");
    const cancelBtn  = document.getElementById("cancelOtpBtn");

    // Open modal after successful username/password (PHP sets this flag)
    <?php if (!empty($openOtpModal)): ?>
        otpModal.style.display = "flex";
    <?php endif; ?>

    verifyBtn.addEventListener("click", () => {
        const otp = otpInput.value.trim();

        fetch("index.php?page=login&action=verifyOtp", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "otp=" + encodeURIComponent(otp)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = "index.php?page=features";
            } else {
                otpError.style.display = "block";
                if (data.expired) {
                    otpError.textContent = "OTP expired. Please login again to get a new code.";
                } else {
                    otpError.textContent = "Invalid OTP. Please try again.";
                }
            }
        });
    });

    cancelBtn.addEventListener("click", () => {
        otpModal.style.display = "none";
        otpInput.value = "";
        otpError.style.display = "none";
    });
});
</script>

</body>
</html>
