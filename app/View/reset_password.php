<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Reset Password</title>

    <!-- ✅ Link external CSS -->
    <link rel="stylesheet" href="public/css/login.css">

    <script>
        function validateNewPassword() {
            const newPassword = document.getElementById('new_password').value;
            const requirements = document.getElementById('password-requirements');
            const strengthContainer = document.getElementById('strength-container');
            
            if (newPassword.length > 0) {
                requirements.classList.add('show');
                strengthContainer.classList.add('show');
            } else {
                requirements.classList.remove('show');
                strengthContainer.classList.remove('show');
            }
            
            const checks = {
                length: newPassword.length >= 8,
                upper: /[A-Z]/.test(newPassword),
                lower: /[a-z]/.test(newPassword),
                number: /[0-9]/.test(newPassword),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(newPassword)
            };

            document.getElementById('req-length').className = checks.length ? 'valid' : '';
            document.getElementById('req-uppercase').className = checks.upper ? 'valid' : '';
            document.getElementById('req-lowercase').className = checks.lower ? 'valid' : '';
            document.getElementById('req-number').className = checks.number ? 'valid' : '';
            document.getElementById('req-special').className = checks.special ? 'valid' : '';

            const metCount = Object.values(checks).filter(Boolean).length;
            const percentage = (metCount / 5) * 100;
            
            const strengthBar = document.getElementById('strength-bar-fill');
            const strengthText = document.getElementById('strength-text');
            
            strengthBar.style.width = percentage + '%';
            strengthBar.className = 'strength-bar-fill';
            
            if (percentage === 0) {
                strengthText.textContent = '';
            } else if (percentage <= 40) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = 'Weak (' + Math.round(percentage) + '%)';
                strengthText.style.color = '#ff4444';
            } else if (percentage <= 60) {
                strengthBar.classList.add('strength-fair');
                strengthText.textContent = 'Fair (' + Math.round(percentage) + '%)';
                strengthText.style.color = '#ff8800';
            } else if (percentage < 100) {
                strengthBar.classList.add('strength-good');
                strengthText.textContent = 'Good (' + Math.round(percentage) + '%)';
                strengthText.style.color = '#ffbb00';
            } else {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Strong (100%)';
                strengthText.style.color = '#00cc00';
            }

            return Object.values(checks).every(Boolean);
        }

        function validatePasswordMatch() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            const matchValid = newPassword === confirmPassword && newPassword.length > 0;
            document.getElementById('req-match').className = matchValid ? 'valid' : '';
            
            return matchValid;
        }

        function handleSubmit(event) {
            event.preventDefault();
            
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            const newPasswordError = document.getElementById('new-password-error');
            const confirmPasswordError = document.getElementById('confirm-password-error');
            
            newPassword.classList.remove('error');
            confirmPassword.classList.remove('error');
            newPasswordError.classList.remove('show');
            confirmPasswordError.classList.remove('show');
            
            let isValid = true;

            if (!validateNewPassword()) {
                newPassword.classList.add('error');
                newPasswordError.textContent = 'Password must meet all requirements.';
                newPasswordError.classList.add('show');
                isValid = false;
            }
            
            if (!validatePasswordMatch()) {
                confirmPassword.classList.add('error');
                confirmPasswordError.textContent = 'Passwords do not match.';
                confirmPasswordError.classList.add('show');
                isValid = false;
            }
            
            if (isValid) {
                event.target.submit();
            }
        }

        window.addEventListener('DOMContentLoaded', function() {
            document.getElementById('new_password').addEventListener('input', function() {
                validateNewPassword();
                validatePasswordMatch();
            });

            document.getElementById('confirm_password').addEventListener('input', validatePasswordMatch);
        });
    </script>
</head>
<body>
    <div class="content">
        <h1>Reset Password</h1>

        <!-- ✅ New Success Message -->
        <div class="alert success" style="margin-bottom: 15px; background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; text-align:center;">
            ✅ You can now change your password.
        </div>

        <form method="POST" action="" onsubmit="return handleSubmit(event);">
            <label>New Password</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
            <span class="error-message" id="new-password-error"></span>

            <label>Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
            <span class="error-message" id="confirm-password-error"></span>

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

    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="modal-icon">✓</div>
            <h2>Password Changed!</h2>
            <p>Your password has been successfully reset. Redirecting you to login...</p>
        </div>
    </div>
</body>
</html>
