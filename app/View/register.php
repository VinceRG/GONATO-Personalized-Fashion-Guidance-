<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Amarelle</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap">
    
    <link rel="stylesheet" href="public/css/login.css">

    <style>
       

        h1 {
            font-family: 'Minion', 'Times New Roman', Times, serif;
            font-size: clamp(2.5rem, 5vw, 3.5rem);
            font-weight: 300;
            color: #1a1a1a;
            margin-bottom: 2.5rem;
            letter-spacing: -0.02em;
            text-align: center;
        }

        form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 500px;
            border: 1px solid rgba(166, 135, 99, 0.15);
            box-shadow: 0 20px 60px rgba(166, 135, 99, 0.1);
            position: relative;
            overflow: hidden;
        }

        .register-form {
            max-width: 750px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            padding-bottom: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            padding-bottom: 1rem;
            position: relative;
        }

        .form-group label {
            margin-top: 0;
        }

        .form-group input {
            margin-bottom: 0;
        }

        .form-group.error input {
            border-color: #dc2626 !important;
            background-color: #fef2f2;
        }

        .form-group.valid input {
            border-color: #16a34a;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.4rem;
            display: none;
            font-weight: 400;
        }

        .error-message.show {
            display: block;
        }

        form::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #A68763, #D7C9AE, #A68763);
        }

        label {
            display: block;
            font-size: 0.95rem;
            font-weight: 500;
            color: #2D2D2D;
            margin-bottom: 0.6rem;
            margin-top: 1.2rem;
            letter-spacing: 0.3px;
            font-family: 'Lexend', sans-serif;
        }

        label:first-of-type {
            margin-top: 0;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 1rem 1.2rem;
            font-size: 1rem;
            font-family: 'Lexend', sans-serif;
            border: 1.5px solid rgba(166, 135, 99, 0.2);
            border-radius: 8px;
            background: #FAFAF9;
            color: #2D2D2D;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: #A68763;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(166, 135, 99, 0.1);
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder,
        input[type="email"]::placeholder {
            color: #A8A29E;
            font-size: 0.95rem;
        }

        .contact-group {
            display: flex;
            align-items: stretch;
            padding-bottom: 1rem;
        }

        .contact-prefix {
            background: #EAE0D2;
            padding: 1rem 1rem;
            border: 1.5px solid rgba(166, 135, 99, 0.2);
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #2D2D2D;
            font-weight: 500;
            font-size: 1rem;
            display: flex;
            align-items: center;
        }

        #contact_num {
            border-radius: 0 8px 8px 0;
            flex: 1;
        }

        button[type="submit"] {
            width: 100%;
            padding: 1.1rem 2rem;
            font-size: 1rem;
            font-weight: 500;
            font-family: 'Lexend', sans-serif;
            background: #2D2D2D;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.3px;
        }

        button[type="submit"]:hover {
            background: #1a1a1a;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(26, 26, 26, 0.2);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        form p {
            text-align: center;
            font-size: 0.95rem;
            color: #57534E;
            margin-top: 1.5rem;
            font-family: 'Lexend', sans-serif;
        }

        form a {
            color: #A68763;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        form a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0%;
            height: 2px;
            background: #A68763;
            transition: width 0.3s ease;
        }

        form a:hover {
            color: #8B6F47;
        }

        form a:hover::after {
            width: 100%;
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

        .password-requirements {
            margin-top: 0.5rem;
            padding: 0.75rem;
            background: #f9fafb;
            border-radius: 6px;
            font-size: 0.85rem;
            display: none;
        }

        .password-requirements.show {
            display: block;
        }

        .password-requirements div {
            margin: 0.25rem 0;
            display: flex;
            align-items: center;
        }

        .password-requirements div::before {
            content: '✗';
            margin-right: 0.5rem;
            color: #dc2626;
            font-weight: bold;
        }

        .password-requirements div.valid::before {
            content: '✓';
            color: #16a34a;
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
        <h1>Join Amarelle</h1>

        <form action="" method="POST" class="register-form" id="registerForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" placeholder="Enter your first name">
                    <span class="error-message" id="firstname-error"></span>
                </div>

                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" placeholder="Enter your last name">
                    <span class="error-message" id="lastname-error"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username">
                    <span class="error-message" id="username-error"></span>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address">
                    <span class="error-message" id="email-error"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Enter your address">
                <span class="error-message" id="address-error"></span>
            </div>

            <label for="contact_num">Contact Number</label>
            <div class="contact-group">
                <input type="text" id="contact_num" name="contact_num" maxlength="11" placeholder="XXX XXXX XXXX">
            </div>
            <span class="error-message" id="contact_num-error"></span>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a strong password">
                    <div class="password-requirements" id="password-requirements">
                        <div id="req-length">At least 8 characters</div>
                        <div id="req-uppercase">One uppercase letter</div>
                        <div id="req-lowercase">One lowercase letter</div>
                        <div id="req-number">One number</div>
                        <div id="req-special">One special character</div>
                    </div>
                    <span class="error-message" id="password-error"></span>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password">
                    <span class="error-message" id="confirmPassword-error"></span>
                </div>
            </div>

            <button type="submit">Create Account</button>

            <p>
                Already have an account?
                <a href="index.php?page=login">Login here</a>
            </p>
        </form>
    </div>

    <div class="footer">
        <p>© 2025 Amarelle. All rights reserved.</p>
    </div>

    <script>
        const form = document.getElementById('registerForm');
        const inputs = {
            firstname: document.getElementById('firstname'),
            lastname: document.getElementById('lastname'),
            username: document.getElementById('username'),
            email: document.getElementById('email'),
            address: document.getElementById('address'),
            contact_num: document.getElementById('contact_num'),
            password: document.getElementById('password'),
            confirmPassword: document.getElementById('confirmPassword')
        };

        // Track which fields have been touched (clicked/focused)
        const touchedFields = new Set();

        // Validation Functions
        function validateFirstName(value) {
            if (!value) return "First name is required.";
            if (value.length < 2) return "First name must be at least 2 characters.";
            if (value.length > 50) return "First name must not exceed 50 characters.";
            if (!/^[a-zA-Z\s\-']+$/.test(value)) return "Only letters, spaces, hyphens, and apostrophes allowed.";
            return "";
        }

        function validateLastName(value) {
            if (!value) return "Last name is required.";
            if (value.length < 2) return "Last name must be at least 2 characters.";
            if (value.length > 50) return "Last name must not exceed 50 characters.";
            if (!/^[a-zA-Z\s\-']+$/.test(value)) return "Only letters, spaces, hyphens, and apostrophes allowed.";
            return "";
        }

        function validateUsername(value) {
            if (!value) return "Username is required.";
            if (value.length < 3) return "Username must be at least 3 characters.";
            if (value.length > 20) return "Username must not exceed 20 characters.";
            if (!/^[a-zA-Z0-9_]+$/.test(value)) return "Only letters, numbers, and underscores allowed.";
            return "";
        }

        function validateEmail(value) {
            if (!value) return "Email is required.";
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) return "Invalid email format.";
            if (value.length > 100) return "Email must not exceed 100 characters.";
            return "";
        }

        function validateAddress(value) {
            if (!value) return "Address is required.";
            if (value.length < 10) return "Address must be at least 10 characters.";
            if (value.length > 200) return "Address must not exceed 200 characters.";
            return "";
        }

        function validateContactNumber(value) {
            if (!value) return "Contact number is required.";
            if (!/^[0-9]{10}$/.test(value)) return "Must be exactly 10 digits.";
            if (!/^9[0-9]{9}$/.test(value)) return "Invalid Philippine mobile number (must start with 9).";
            return "";
        }

        function validatePassword(value) {
            const requirements = document.getElementById('password-requirements');
            if (value) {
                requirements.classList.add('show');
            } else {
                requirements.classList.remove('show');
            }

            document.getElementById('req-length').className = value.length >= 8 ? 'valid' : '';
            document.getElementById('req-uppercase').className = /[A-Z]/.test(value) ? 'valid' : '';
            document.getElementById('req-lowercase').className = /[a-z]/.test(value) ? 'valid' : '';
            document.getElementById('req-number').className = /[0-9]/.test(value) ? 'valid' : '';
            document.getElementById('req-special').className = /[!@#$%^&*(),.?":{}|<>]/.test(value) ? 'valid' : '';

            if (!value) return "Password is required.";
            if (value.length < 8) return "Password must be at least 8 characters.";
            if (!/[A-Z]/.test(value)) return "Must contain at least one uppercase letter.";
            if (!/[a-z]/.test(value)) return "Must contain at least one lowercase letter.";
            if (!/[0-9]/.test(value)) return "Must contain at least one number.";
            if (!/[!@#$%^&*(),.?":{}|<>]/.test(value)) return "Must contain at least one special character.";
            return "";
        }

        function validateConfirmPassword(value) {
            if (!value) return "Please confirm your password.";
            if (value !== inputs.password.value) return "Passwords do not match.";
            return "";
        }

        function showError(field, message) {
            const errorElement = document.getElementById(field + '-error');
            const inputElement = inputs[field];
            const formGroup = inputElement.closest('.form-group') || inputElement.parentElement;

            if (message) {
                errorElement.textContent = message;
                errorElement.classList.add('show');
                formGroup?.classList.add('error');
                formGroup?.classList.remove('valid');
            } else {
                errorElement.classList.remove('show');
                formGroup?.classList.remove('error');
                if (inputElement.value) formGroup?.classList.add('valid');
            }
        }

        function validateField(field) {
            const validators = {
                firstname: validateFirstName,
                lastname: validateLastName,
                username: validateUsername,
                email: validateEmail,
                address: validateAddress,
                contact_num: validateContactNumber,
                password: validatePassword,
                confirmPassword: validateConfirmPassword
            };
            const message = validators[field](inputs[field].value);
            showError(field, message);
            return message === "";
        }

        // Check if username exists (via AJAX)
        async function checkUsernameExists(username) {
            if (!username || username.length < 3) return true;
            
            try {
                const response = await fetch('index.php?page=register', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'username=' + encodeURIComponent(username)
                });
                const data = await response.json();
                
                if (data.exists) {
                    showError('username', 'This username is already taken.');
                    return false;
                }
                // Clear error if username is available
                showError('username', '');
                return true;
            } catch (error) {
                console.error('Error checking username:', error);
                return true;
            }
        }

        // Check if email exists (via AJAX)
        async function checkEmailExists(email) {
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return true;
            
            try {
                const response = await fetch('index.php?page=register', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'email=' + encodeURIComponent(email)
                });
                const data = await response.json();
                
                if (data.exists) {
                    showError('email', 'This email is already registered.');
                    return false;
                }
                // Clear error if email is available
                showError('email', '');
                return true;
            } catch (error) {
                console.error('Error checking email:', error);
                return true;
            }
        }

        // Real-time validation on blur (when user leaves the field)
        Object.keys(inputs).forEach(field => {
            // Mark field as touched when focused
            inputs[field].addEventListener('focus', () => {
                touchedFields.add(field);
            });

            // Validate when user leaves the field (only if touched)
            inputs[field].addEventListener('blur', async () => {
                if (touchedFields.has(field)) {
                    const isValid = validateField(field);
                    
                    // Check for existing username/email only if basic validation passes
                    if (field === 'username' && isValid) {
                        await checkUsernameExists(inputs[field].value);
                    }
                    if (field === 'email' && isValid) {
                        await checkEmailExists(inputs[field].value);
                    }
                }
            });

            // Validate on input (real-time) if field has been touched
            inputs[field].addEventListener('input', () => {
                if (touchedFields.has(field)) {
                    validateField(field);
                }
            });
        });

        // Auto-format contact number
        inputs.contact_num.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Debounce function for API calls
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Debounced checks for username and email
        const debouncedUsernameCheck = debounce(async (username) => {
            const basicError = validateUsername(username);
            if (!basicError) {
                await checkUsernameExists(username);
            }
        }, 500);

        const debouncedEmailCheck = debounce(async (email) => {
            const basicError = validateEmail(email);
            if (!basicError) {
                await checkEmailExists(email);
            }
        }, 500);

        inputs.username.addEventListener('input', function() {
            if (touchedFields.has('username')) {
                debouncedUsernameCheck(this.value);
            }
        });

        inputs.email.addEventListener('input', function() {
            if (touchedFields.has('email')) {
                debouncedEmailCheck(this.value);
            }
        });

        // Form submission
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Mark all fields as touched
            Object.keys(inputs).forEach(field => touchedFields.add(field));

            let isValid = true;
            const validations = {};

            // Validate all fields
            for (const field of Object.keys(inputs)) {
                const error = validateField(field);
                if (!error) isValid = false;
            }

            // Check username and email existence
            if (isValid) {
                const usernameExists = await checkUsernameExists(inputs.username.value);
                const emailExists = await checkEmailExists(inputs.email.value);
                
                if (usernameExists === false || emailExists === false) {
                    isValid = false;
                }
            }

            if (!isValid) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                form.submit();
            }
        });
    </script>
</body>

</html>