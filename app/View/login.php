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
        .terms-text {
            font-size: 12px;
            text-align: center;
            color: #555;
            margin-top: 10px;
            line-height: 1.5;
        }

        h1 {
            font-family: 'Minion', 'Times New Roman', Times, serif;
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 300;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
            text-align: center;
        }

        p {
            margin-bottom: 2.5rem;
            color: grey;
        }

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
            border-color: #161414ff;
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
            margin-bottom: -1rem;
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

        /* OTP & Lock Modal Base Styles */
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

        /* --- Password Toggle Styles --- */
/* Ensure the wrapper positions the button correctly */
.password-wrapper {
    position: relative;
    width: 100%;
    display: flex;
    align-items: center;
}

/* Make space for the icon inside the input */
.password-wrapper input[type="password"],
.password-wrapper input[type="text"] {
    padding-right: 45px !important; /* Forces padding to override defaults */
    width: 100%;
    box-sizing: border-box;
}

/* Style the button */
.password-toggle {
    position: absolute;
    right: 12px;
    top: 40%;
    transform: translateY(-50%); /* Centers vertically */
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    z-index: 10;
}

.password-toggle:hover {
    color: #111827;
}

/* Icon Size */
.password-toggle svg {
    width: 20px;
    height: 20px;
}
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

<!-- ACCOUNT LOCKED MODAL -->
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
        <img src="public/image/amarelle.png" alt="Amarelle Logo" class="brand-logo" style="height: 50px; width: auto; margin-right: 10px; vertical-align: middle;">
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
