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
        /* Enhanced Message Styles */
.message {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.9rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Error Message */
.message.error {
    background-color: #FEE2E2;
    border: 1px solid #FCA5A5;
    color: #991B1B;
}

.message.error::before {
    content: "⚠";
    font-size: 1.2rem;
}

/* Warning Message */
.message.warning {
    background-color: #FEF3C7;
    border: 1px solid #FCD34D;
    color: #92400E;
}

.message.warning::before {
    content: "⚠";
    font-size: 1.2rem;
}

/* Success Message */
.message.success {
    background-color: #D1FAE5;
    border: 1px solid #6EE7B7;
    color: #065F46;
}

.message.success::before {
    content: "✓";
    font-size: 1.2rem;
}

/* Info Message */
.message.info {
    background-color: #DBEAFE;
    border: 1px solid #93C5FD;
    color: #1E40AF;
}

.message.info::before {
    content: "ℹ";
    font-size: 1.2rem;
}

/* Input Field Enhancement */
input[type="text"],
input[type="password"],
input[type="email"] {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-size: 1rem;
    font-family: 'Lexend', sans-serif;
    transition: all 0.3s ease;
    background-color: #F9FAFB;
}

input[type="text"]:focus,
input[type="password"]:focus,
input[type="email"]:focus {
    outline: none;
    border-color: #2D2D2D;
    background-color: #FFFFFF;
    box-shadow: 0 0 0 3px rgba(45, 45, 45, 0.1);
}

/* Input Error State */
input.error {
    border-color: #F87171;
    background-color: #FEF2F2;
}

input.error:focus {
    border-color: #DC2626;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

/* Disabled Input State */
input[disabled] {
    background-color: #F3F4F6;
    color: #9CA3AF;
    cursor: not-allowed;
    opacity: 0.6;
}

/* Label Styling */
label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
    font-size: 0.95rem;
}

/* Form Group Spacing */
.form-group {
    margin-bottom: 20px;
}

/* Password Wrapper */
.password-wrapper {
    position: relative;
}

/* Button Disabled State */
button[disabled] {
    background-color: #D1D5DB;
    color: #6B7280;
    cursor: not-allowed;
    opacity: 0.6;
}

button[disabled]:hover {
    background-color: #D1D5DB;
    transform: none;
}

/* OTP Modal Improvements */
.otp-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.otp-modal-content {
    background: #fff;
    padding: 35px;
    border-radius: 16px;
    width: 90%;
    max-width: 400px;
    text-align: center;
    position: relative;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.otp-modal-content h2 {
    margin-bottom: 12px;
    color: #1F2937;
    font-size: 1.5rem;
    font-weight: 600;
}

.otp-modal-content p {
    font-size: 0.9rem;
    color: #6B7280;
    line-height: 1.6;
    margin-bottom: 10px;
}

.otp-modal-content input {
    width: 100%;
    padding: 14px;
    margin-top: 20px;
    text-align: center;
    font-size: 24px;
    letter-spacing: 8px;
    border: 2px solid #E5E7EB;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.otp-modal-content input:focus {
    outline: none;
    border-color: #2D2D2D;
    box-shadow: 0 0 0 3px rgba(45, 45, 45, 0.1);
}

.otp-btn, .otp-cancel-btn {
    margin-top: 15px;
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.otp-btn {
    background: #2D2D2D;
    color: #fff;
}

.otp-btn:hover {
    background: #1a1a1a;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(45, 45, 45, 0.3);
}

.otp-cancel-btn {
    background: #F3F4F6;
    color: #374151;
}

.otp-cancel-btn:hover {
    background: #E5E7EB;
}

#otpError {
    margin-top: 15px;
    padding: 10px;
    background-color: #FEE2E2;
    border: 1px solid #FCA5A5;
    border-radius: 8px;
    color: #991B1B;
    font-size: 0.85rem;
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 480px) {
    .otp-modal-content {
        padding: 25px;
        width: 95%;
    }
    
    .message {
        font-size: 0.85rem;
        padding: 10px 14px;
    }
    
    .otp-modal-content input {
        font-size: 20px;
        letter-spacing: 6px;
    }
}

.footer {
    padding: 2rem 3rem;
    text-align: center;
    color: #78716C;
    font-size: 0.85rem;
    font-weight: 300;
    position: relative;
    z-index: 10;
    font-family: 'Lexend', sans-serif;
}
    </style>
</head>

<body>

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

            <p class="forgotpass">
                <a class="forgotpass" href="index.php?page=forgot">Forgot your password?</a>
            </p>

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
