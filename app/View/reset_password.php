<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Reset Password</title>

    <!-- External CSS -->
    <link rel="stylesheet" href="public/css/forgot.css">
    <link rel="stylesheet" href="public/css/register.css">

    <!-- Small helpers so you see errors even if not in your CSS yet -->
    <style>
        input.error {
            border: 1px solid #f87171; /* red border */
        }
        .error-message {
            display: none;
            color: #f87171;
            font-size: 0.85rem;
            margin-top: 4px;
        }
        .error-message.show {
            display: block;
        }
        button.btn-disabled,
        button[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('resetForm');

        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        const newPasswordError = document.getElementById('new-password-error');
        const confirmPasswordError = document.getElementById('confirm-password-error');

        const requirements = document.getElementById('password-requirements');
        const strengthContainer = document.getElementById('strength-container');
        const strengthBarFill = document.getElementById('strength-bar-fill');
        const strengthText = document.getElementById('strength-text');

        const reqLength   = document.getElementById('req-length');
        const reqUpper    = document.getElementById('req-uppercase');
        const reqLower    = document.getElementById('req-lowercase');
        const reqNumber   = document.getElementById('req-number');
        const reqSpecial  = document.getElementById('req-special');
        const reqMatch    = document.getElementById('req-match');

        const resetBtn = form.querySelector('button[type="submit"]');

        // Same rules as registration
        const passwordRegex =
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;

        // Enable/disable submit button based on current validity
        function updateSubmitState() {
            const pwd = newPasswordInput.value;
            const confirm = confirmPasswordInput.value;

            const pwdValid = passwordRegex.test(pwd);
            const matchValid = pwd.length > 0 && pwd === confirm;

            const canSubmit = pwdValid && matchValid;

            resetBtn.disabled = !canSubmit;
            resetBtn.classList.toggle('btn-disabled', !canSubmit);
        }

        // Password strength + requirements update
        function updatePasswordRequirements() {
            const pwd = newPasswordInput.value;

            if (pwd.length > 0) {
                requirements.classList.add('show');
                strengthContainer.classList.add('show');
            } else {
                requirements.classList.remove('show');
                strengthContainer.classList.remove('show');
            }

            const checks = {
                length:  pwd.length >= 8,
                upper:   /[A-Z]/.test(pwd),
                lower:   /[a-z]/.test(pwd),
                number:  /[0-9]/.test(pwd),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(pwd)
            };

            reqLength.classList.toggle('valid',  checks.length);
            reqUpper.classList.toggle('valid',   checks.upper);
            reqLower.classList.toggle('valid',   checks.lower);
            reqNumber.classList.toggle('valid',  checks.number);
            reqSpecial.classList.toggle('valid', checks.special);

            const metCount = Object.values(checks).filter(Boolean).length;
            const percentage = (metCount / 5) * 100;

            strengthBarFill.style.width = percentage + '%';
            strengthBarFill.className = 'strength-bar-fill';
            strengthText.textContent = '';

            if (percentage === 0) return;

            if (percentage <= 40) {
                strengthBarFill.classList.add('strength-weak');
                strengthText.textContent = 'Weak (' + Math.round(percentage) + '%)';
                strengthText.style.color = '#F97373';
            } else if (percentage <= 60) {
                strengthBarFill.classList.add('strength-fair');
                strengthText.textContent = 'Fair (' + Math.round(percentage) + '%)';
                strengthText.style.color = '#FDBA74';
            } else if (percentage < 100) {
                strengthBarFill.classList.add('strength-good');
                strengthText.textContent = 'Good (' + Math.round(percentage) + '%)';
                strengthText.style.color = '#FACC15';
            } else {
                strengthBarFill.classList.add('strength-strong');
                strengthText.textContent = 'Strong (100%)';
                strengthText.style.color = '#22C55E';
            }
        }

        function validatePasswordField() {
            const pwd = newPasswordInput.value;
            const valid = passwordRegex.test(pwd);

            newPasswordInput.classList.toggle('error', !valid);
            if (newPasswordError) {
                newPasswordError.textContent = valid ? '' : 'Password must meet all requirements.';
                newPasswordError.classList.toggle('show', !valid);
            }

            return valid;
        }

        function validateMatchField() {
            const pwd = newPasswordInput.value;
            const confirm = confirmPasswordInput.value;

            const match = pwd.length > 0 && pwd === confirm;

            confirmPasswordInput.classList.toggle('error', !match);

            if (confirmPasswordError) {
                confirmPasswordError.textContent = match ? '' : 'Passwords do not match.';
                confirmPasswordError.classList.toggle('show', !match);
            }

            if (reqMatch) {
                reqMatch.classList.toggle('valid', match);
            }

            return match;
        }

        // Live events
        newPasswordInput.addEventListener('input', function () {
            updatePasswordRequirements();
            validatePasswordField();
            validateMatchField();
            updateSubmitState();
        });

        confirmPasswordInput.addEventListener('input', function () {
            validateMatchField();
            updateSubmitState();
        });

        // Submit handler
        form.addEventListener('submit', function (e) {
            let hasError = false;

            updatePasswordRequirements();

            if (!validatePasswordField()) hasError = true;
            if (!validateMatchField()) hasError = true;

            updateSubmitState();

            if (hasError) {
                e.preventDefault();
            }
        });

        // Initial state
        updateSubmitState();
    });
    </script>
</head>
<body>
    <div class="content">
        <h1>Reset Password</h1>

        <!-- Example success text; control with PHP if needed -->
        <div class="alert success" style="margin-bottom: 15px; text-align:center;">
            You can now change your password.
        </div>

        <form method="POST" action="" id="resetForm">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password"
                   placeholder="Enter new password" required>
            <span class="error-message" id="new-password-error"></span>

            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password"
                   placeholder="Confirm new password" required>
            <!-- error span for confirm -->
            <span class="error-message" id="confirm-password-error"></span>

            <!-- Password strength -->
            <div id="strength-container" class="password-strength-container">
                <div class="strength-bar">
                    <div id="strength-bar-fill" class="strength-bar-fill"></div>
                </div>
                <div id="strength-text" class="strength-text"></div>
            </div>

            <!-- Requirements list -->
            <div class="password-requirements" id="password-requirements">
                <div id="req-length">At least 8 characters</div>
                <div id="req-uppercase">One uppercase letter</div>
                <div id="req-lowercase">One lowercase letter</div>
                <div id="req-number">One number</div>
                <div id="req-special">One special character</div>
                <div id="req-match">Passwords match</div>
            </div>

            <button type="submit">Reset Password</button>

            <p><a href="index.php?page=login">Back to Login</a></p>
        </form>
    </div>
</body>
</html>
