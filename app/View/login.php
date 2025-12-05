<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap">
    <title>Amarelle</title>
    <link rel="icon" type="image/png" href="public/image/amarelle.png">
    
    <link rel="stylesheet" href="public/css/login.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<?php
// ✅ Get remembered username from cookie, and decide what to show in the input
$rememberedUsername = $_COOKIE['remember_username'] ?? '';
$inputUsername = $username ?? $rememberedUsername ?? '';
?>

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

<div id="lockModal" class="otp-modal">
    <div class="otp-modal-content">
        <h2>Account Locked</h2>
        <p>Your account has been temporarily locked for security reasons due to multiple unsuccessful login attempts.</p>
        <p>Please contact the administrator at
            <a href="mailto:amarelle2025@gmail.com">amarelle2025@gmail.com</a>
            to regain access to your account.
        </p>
        <button id="lockOkBtn" class="otp-btn">OK</button>
    </div>
</div>

<div class="header">
    <a href="index.php?page=landing" class="logo-link">
        <img src="public/image/amarelle.png" alt="Amarelle Logo" class="brand-logo">
    </a>
</div>

<div class="content">

    <h1>Welcome to Amarelle!</h1>
    <p>Please enter your details.</p>

    <form id="loginForm" action="index.php?page=login" method="POST">

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
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

       <div class="form-group">
            <label for="password">Password</label>
            
            <div class="password-wrapper">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    <?php echo (isset($isLocked) && $isLocked) ? 'disabled' : ''; ?>
                >
                <button type="button" class="password-toggle" data-target="password">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="form-actions">
            <label class="remember-me">
                <input
                    type="checkbox"
                    name="remember_me"
                    value="1"
                    <?php echo !empty($rememberedUsername) ? 'checked' : ''; ?>
                >
                Remember me
            </label>

            <a class="forgotpass-link" href="index.php?page=forgot">Forgot password?</a>
        </div>

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

    const lockModal  = document.getElementById("lockModal");
    const lockOkBtn  = document.getElementById("lockOkBtn");

    // Open OTP modal after successful username/password (PHP sets this flag)
    <?php if (!empty($openOtpModal)): ?>
        otpModal.style.display = "flex";
    <?php endif; ?>

    // Open Account Locked modal when account is locked
    <?php if (isset($isLocked) && $isLocked): ?>
        lockModal.style.display = "flex";
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

    // Close lock modal (inputs remain disabled; user must contact admin)
    lockOkBtn.addEventListener("click", () => {
        lockModal.style.display = "none";
    });
});

// --- Password Toggle Logic ---
    const toggles = document.querySelectorAll(".password-toggle");

    const eyeIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>`;
    
    const eyeSlashIcon = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
        </svg>`;

    toggles.forEach(function (btn) {
        btn.addEventListener("click", function () {
            const targetId = btn.getAttribute("data-target");
            const input = document.getElementById(targetId);
            if (!input) return;

            if (input.type === "password") {
                input.type = "text";
                btn.innerHTML = eyeSlashIcon;
            } else {
                input.type = "password";
                btn.innerHTML = eyeIcon;
            }
        });
    });
</script>

</body>
</html>