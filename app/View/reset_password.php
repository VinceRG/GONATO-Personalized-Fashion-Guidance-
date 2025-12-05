<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Reset Password</title>

    <link rel="stylesheet" href="public/css/forgot.css">
    <link rel="stylesheet" href="public/css/register.css">

    <style>
        /* --- Password Wrapper & Toggle Styles --- */
        .password-wrapper {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }

        /* Force padding on the right to prevent text overlap with the icon */
        .password-wrapper input[type="password"],
        .password-wrapper input[type="text"] {
            padding-right: 45px !important;
            width: 100%;
            box-sizing: border-box;
        }

        /* Style the toggle button */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
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

        .password-toggle svg {
            width: 20px;
            height: 20px;
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

        // --- Update requirements + strength bar ---
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

            // reset bar + text
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

        // --- Validate password rules ---
        const passwordRegex =
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;

        function validatePasswordField() {
            const pwd = newPasswordInput.value;
            const valid = passwordRegex.test(pwd);

            newPasswordInput.classList.toggle('error', !valid);
            newPasswordError.textContent = valid ? '' : 'Password must meet all requirements.';
            newPasswordError.classList.toggle('show', !valid);

            return valid;
        }

        function validateMatchField() {
            const pwd = newPasswordInput.value;
            const confirm = confirmPasswordInput.value;

            const match = pwd.length > 0 && pwd === confirm;

            confirmPasswordInput.classList.toggle('error', !match);
            confirmPasswordError.classList.toggle('show', !match);

            reqMatch.classList.toggle('valid', match);

            return match;
        }

        // --- Live updates ---
        newPasswordInput.addEventListener('input', function () {
            updatePasswordRequirements();
            validatePasswordField();
            validateMatchField();
        });

        confirmPasswordInput.addEventListener('input', validateMatchField);

        // --- Submit handler ---
        form.addEventListener('submit', function (e) {
            let hasError = false;

            updatePasswordRequirements();

            if (!validatePasswordField()) hasError = true;
            if (!validateMatchField()) hasError = true;

            if (hasError) {
                e.preventDefault();
            }
        });

        // ----------------------------------------------------
        // --- Show/Hide Password Logic (Added) ---
        // ----------------------------------------------------
        const toggles = document.querySelectorAll('.password-toggle');

        const eyeIcon = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>`;

        const eyeSlashIcon = `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
            </svg>`;

        toggles.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.innerHTML = eyeSlashIcon;
                    } else {
                        input.type = 'password';
                        this.innerHTML = eyeIcon;
                    }
                }
            });
        });

    });
    </script>
</head>
<body>
    <div class="content">
        <h1>Reset Password</h1>

        <div class="alert success" style="margin-bottom: 15px; text-align:center;">
            You can now change your password.
        </div>

        <form method="POST" action="" id="resetForm">
            
            <div class="form-group">
                <label for="new_password">New Password</label>
                <div class="password-wrapper">
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        placeholder="Enter new password" 
                        required
                    >
                    <button type="button" class="password-toggle" data-target="new_password">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
                <span class="error-message" id="new-password-error"></span>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="password-wrapper">
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        placeholder="Confirm new password" 
                        required
                    >
                    <button type="button" class="password-toggle" data-target="confirm_password">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
                <span class="error-message" id="confirm-password-error"></span>
            </div>

            <div id="strength-container" class="password-strength-container">
                <div class="strength-bar">
                    <div id="strength-bar-fill" class="strength-bar-fill"></div>
                </div>
                <div id="strength-text" class="strength-text"></div>
            </div>

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