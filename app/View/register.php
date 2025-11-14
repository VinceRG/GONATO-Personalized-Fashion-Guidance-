<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Amarelle</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap">
      <!-- ✅ reCAPTCHA -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="public/css/register.css">

  
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
            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <div class="progress-line" id="progressLine"></div>
                <div class="progress-step active" data-step="1">
                    <div class="progress-step-circle">1</div>
                    <div class="progress-step-label">Personal Info</div>
                </div>
                <div class="progress-step" data-step="2">
                    <div class="progress-step-circle">2</div>
                    <div class="progress-step-label">Address & Contact</div>
                </div>
                <div class="progress-step" data-step="3">
                    <div class="progress-step-circle">3</div>
                    <div class="progress-step-label">Security</div>
                </div>
            </div>

            <!-- Step 1: Personal Information -->
            <div class="form-step active" data-step="1">
            <?php if (!empty($error)): ?>
    <div class="message error">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>
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

                <div class="form-buttons">
                    <button type="button" class="btn btn-primary btn-full" id="nextStep1">Next</button>
                </div>
            </div>

            <!-- Step 2: Address & Contact -->
            <div class="form-step" data-step="2">
                <div class="address-section">
                    <h3>Address</h3>

                    <div class="form-group">
                        <label for="street_address">Street Address / House Number</label>
                        <input type="text" id="street_address" name="street_address" placeholder="e.g., 123 Main St or Unit 4B">
                        <span class="error-message" id="street_address-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="apartment">Apartment, Suite, or Floor <span class="optional">(Optional)</span></label>
                        <input type="text" id="apartment" name="apartment" placeholder="e.g., Apt 2B, Floor 3">
                        <span class="error-message" id="apartment-error"></span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="province">Province</label>
                            <select id="province" name="province">
                                <option value="">Select Province</option>
                                <option value="Metro Manila">Metro Manila</option>
                                <option value="Cavite">Cavite</option>
                                <option value="Laguna">Laguna</option>
                                <option value="Bulacan">Bulacan</option>
                                <option value="Rizal">Rizal</option>
                            </select>
                            <span class="error-message" id="province-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="city">City / Town</label>
                            <select id="city" name="city">
                                <option value="">Select City</option>
                            </select>
                            <span class="error-message" id="city-error"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="barangay">Barangay</label>
                        <select id="barangay" name="barangay">
                            <option value="">Select Barangay</option>
                        </select>
                        <span class="error-message" id="barangay-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="postal_code">Postal / ZIP Code</label>
                        <input type="text" id="postal_code" name="postal_code" placeholder="e.g., 1101" maxlength="10">
                        <span class="error-message" id="postal_code-error"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="contact_num">Contact Number</label>
                    <input type="text" id="contact_num" name="contact_num" maxlength="11" placeholder="09XX XXX XXXX" value="0">
                    <span class="error-message" id="contact_num-error"></span>
                </div>

                <div class="form-buttons">
                    <button type="button" class="btn btn-secondary" id="prevStep2">Back</button>
                    <button type="button" class="btn btn-primary" id="nextStep2">Next</button>
                </div>
            </div>

            
            <!-- Step 3: Security -->
<div class="form-step" data-step="3">
  
    
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

    <!-- reCAPTCHA -->
    <div class="g-recaptcha" data-sitekey="6LeCugUsAAAAAMevrBVqSjs6AG8SsQZ8qrJGvjDZ"></div>

    <div class="form-buttons">
        <button type="button" class="btn btn-secondary" id="prevStep3">Back</button>
        <button type="submit" class="btn btn-primary">Create Account</button>
    </div>
</div>

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
        // Current step tracker
        let currentStep = 1;
        const totalSteps = 3;

        // Input elements
        const inputs = {
            firstname: document.getElementById('firstname'),
            lastname: document.getElementById('lastname'),
            username: document.getElementById('username'),
            email: document.getElementById('email'),
            street_address: document.getElementById('street_address'),
            apartment: document.getElementById('apartment'),
            city: document.getElementById('city'),
            province: document.getElementById('province'),
            barangay: document.getElementById('barangay'),
            postal_code: document.getElementById('postal_code'),
            contact_num: document.getElementById('contact_num'),
            password: document.getElementById('password'),
            confirmPassword: document.getElementById('confirmPassword')
        };

        // Track touched fields
        const touchedFields = new Set();

        // Step fields mapping
        const stepFields = {
            1: ['firstname', 'lastname', 'username', 'email'],
            2: ['street_address', 'province', 'city', 'barangay', 'postal_code', 'contact_num'],
            3: ['password', 'confirmPassword']
        };

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

        function validateStreetAddress(value) {
            if (!value) return "Street address is required.";
            if (value.length < 5) return "Street address must be at least 5 characters.";
            if (value.length > 150) return "Street address must not exceed 150 characters.";
            return "";
        }

        function validateApartment(value) {
            if (value && value.length > 50) return "Apartment/Suite must not exceed 50 characters.";
            return "";
        }

        function validateCity(value) {
            if (!value) return "City/Town is required.";
            return "";
        }

        function validateProvince(value) {
            if (!value) return "Province/Region is required.";
            return "";
        }

        function validateBarangay(value) {
            if (!value) return "Barangay is required.";
            return "";
        }

        function validatePostalCode(value) {
            if (!value) return "Postal code is required.";
            if (!/^[0-9]{4,10}$/.test(value)) return "Postal code must be 4-10 digits.";
            return "";
        }

        function validateContactNumber(value) {
            if (!value) return "Contact number is required.";
            if (!/^0[0-9]{10}$/.test(value)) return "Must be 11 digits starting with 0.";
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
            const formGroup = inputElement.closest('.form-group');

            if (message) {
                errorElement.textContent = message;
                errorElement.classList.add('show');
                formGroup?.classList.add('error');
                formGroup?.classList.remove('valid');
            } else {
                errorElement.classList.remove('show');
                formGroup?.classList.remove('error');
                if (inputElement.value || field === 'apartment') formGroup?.classList.add('valid');
            }
        }

        function validateField(field) {
            const validators = {
                firstname: validateFirstName,
                lastname: validateLastName,
                username: validateUsername,
                email: validateEmail,
                street_address: validateStreetAddress,
                apartment: validateApartment,
                city: validateCity,
                province: validateProvince,
                barangay: validateBarangay,
                postal_code: validatePostalCode,
                contact_num: validateContactNumber,
                password: validatePassword,
                confirmPassword: validateConfirmPassword
            };
            const message = validators[field](inputs[field].value);
            showError(field, message);
            return message === "";
        }

        // Check username existence
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
                showError('username', '');
                return true;
            } catch (error) {
                console.error('Error checking username:', error);
                return true;
            }
        }

        // Check email existence
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
                showError('email', '');
                return true;
            } catch (error) {
                console.error('Error checking email:', error);
                return true;
            }
        }

        // Debounce function
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

        // Event listeners for all inputs
        Object.keys(inputs).forEach(field => {
            inputs[field].addEventListener('focus', () => {
                touchedFields.add(field);
            });

            inputs[field].addEventListener('blur', async () => {
                if (touchedFields.has(field)) {
                    const isValid = validateField(field);
                    
                    if (field === 'username' && isValid) {
                        await checkUsernameExists(inputs[field].value);
                    }
                    if (field === 'email' && isValid) {
                        await checkEmailExists(inputs[field].value);
                    }
                }
            });

            inputs[field].addEventListener('input', () => {
                if (touchedFields.has(field)) {
                    validateField(field);
                }
            });
        });

        // Debounced checks
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

        // Contact number formatting
        inputs.contact_num.addEventListener('input', function () {
            let value = this.value.replace(/[^0-9]/g, '');
            
            if (value.length === 0) {
                this.value = '0';
            } else if (value[0] !== '0') {
                this.value = '0' + value;
            } else {
                this.value = value;
            }
        });

        inputs.contact_num.addEventListener('keydown', function(e) {
            if ((e.key === 'Backspace' || e.key === 'Delete') && this.value.length <= 1) {
                e.preventDefault();
                this.value = '0';
            }
        });

        // Postal code formatting
        inputs.postal_code.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Address data
        const addressData = {
            "Metro Manila": {
                "Quezon City": ["Commonwealth", "Fairview", "Batasan Hills", "Holy Spirit", "Bagong Silangan"],
                "Manila": ["Barangay 1", "Barangay 2", "Barangay 3", "Barangay 4", "Barangay 5"],
                "Makati": ["Bel-Air", "Poblacion", "San Lorenzo", "Guadalupe Viejo", "Olympia"],
                "Pasig": ["Rosario", "Ugong", "Manggahan", "Pinagbuhatan", "Malinao"],
                "Taguig": ["Ususan", "Bicutan", "Bagumbayan", "Napindan", "Tuktukan"]
            },
            "Cavite": {
                "Bacoor": ["Talaba", "Zapote", "Molino 1", "Molino 2", "Molino 3"],
                "Imus": ["Bayan Luma 1", "Bayan Luma 2", "Bayan Luma 3", "Medicion 1", "Medicion 2"],
                "Dasmariñas": ["San Jose", "Burol 1", "Burol 2", "Burol 3", "Paliparan"],
                "General Trias": ["Pasong Kawayan", "San Francisco", "Santa Clara", "Buenavista", "Manggahan"],
                "Kawit": ["Tabon", "Wakas", "Gahak", "Marulas", "San Sebastian"]
            },
            "Laguna": {
                "Calamba": ["Canlubang", "Real", "Lingga", "Looc", "San Cristobal"],
                "Biñan": ["Malaban", "San Antonio", "San Francisco", "San Jose", "San Vicente"],
                "Santa Rosa": ["Balibago", "Market Area", "Dila", "Tagapo", "Malusak"],
                "San Pedro": ["Chrysanthemum", "San Vicente", "Nueva", "Sampaguita", "San Lorenzo"],
                "Los Baños": ["Batong Malake", "Mayondon", "Tadlac", "Anos", "Bagong Silang"]
            },
            "Bulacan": {
                "Malolos": ["Tikay", "Mojon", "San Agustin", "Matimbo", "Santor"],
                "Meycauayan": ["Calvario", "Zamora", "Perez", "Pandayan", "Bahay Pare"],
                "Marilao": ["Abangan Norte", "Abangan Sur", "Patubig", "Saog", "Poblacion"],
                "Guiguinto": ["Malis", "Poblacion", "Tabang", "Sta. Rita", "Cutcot"],
                "Bocaue": ["Turo", "Bagumbayan", "Antipona", "Lolomboy", "Bunducan"]
            },
            "Rizal": {
                "Antipolo": ["San Roque", "Dalig", "Cupang", "Inarawan", "Dela Paz"],
                "Cainta": ["San Juan", "Sto. Domingo", "San Andres", "San Isidro", "Poblacion"],
                "Taytay": ["Dolores", "San Juan", "San Isidro", "San Antonio", "Muzon"],
                "Angono": ["San Pedro", "Bagumbayan", "Kalayaan", "San Vicente", "Sto. Niño"],
                "Binangonan": ["Guilig", "Pag-asa", "Ithan", "Rayap", "Pantok"]
            }
        };

        const province = document.getElementById("province");
        const city = document.getElementById("city");
        const barangay = document.getElementById("barangay");

        province.addEventListener("change", () => {
            city.innerHTML = '<option value="">Select City</option>';
            barangay.innerHTML = '<option value="">Select Barangay</option>';

            const selectedProvince = province.value;

            if (addressData[selectedProvince]) {
                Object.keys(addressData[selectedProvince]).forEach(c => {
                    const opt = document.createElement("option");
                    opt.value = c;
                    opt.textContent = c;
                    city.appendChild(opt);
                });
            }
        });

        city.addEventListener("change", () => {
            barangay.innerHTML = '<option value="">Select Barangay</option>';

            const selectedProvince = province.value;
            const selectedCity = city.value;

            if (addressData[selectedProvince] && addressData[selectedProvince][selectedCity]) {
                addressData[selectedProvince][selectedCity].forEach(brgy => {
                    const opt = document.createElement("option");
                    opt.value = brgy;
                    opt.textContent = brgy;
                    barangay.appendChild(opt);
                });
            }
        });

        // Step navigation functions
        function updateProgressBar() {
            const progressLine = document.getElementById('progressLine');
            const progressSteps = document.querySelectorAll('.progress-step');
            
            // Update progress line width
            const progressPercent = ((currentStep - 1) / (totalSteps - 1)) * 100;
            progressLine.style.width = progressPercent + '%';
            
            // Update step indicators
            progressSteps.forEach((step, index) => {
                const stepNum = index + 1;
                if (stepNum < currentStep) {
                    step.classList.add('completed');
                    step.classList.remove('active');
                } else if (stepNum === currentStep) {
                    step.classList.add('active');
                    step.classList.remove('completed');
                } else {
                    step.classList.remove('active', 'completed');
                }
            });
        }

        function showStep(step) {
            document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
            document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
            currentStep = step;
            updateProgressBar();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        async function validateCurrentStep() {
            const fields = stepFields[currentStep];
            let isValid = true;

            // Mark all fields in current step as touched
            fields.forEach(field => touchedFields.add(field));

            // Validate all fields in current step
            for (const field of fields) {
                if (field === 'apartment') {
                    // Only validate if user entered something
                    if (inputs[field].value) {
                        const error = validateField(field);
                        if (!error) isValid = false;
                    }
                } else {
                    const error = validateField(field);
                    if (!error) isValid = false;
                }
            }

            // Special checks for step 1
            if (currentStep === 1 && isValid) {
                const usernameExists = await checkUsernameExists(inputs.username.value);
                const emailExists = await checkEmailExists(inputs.email.value);
                
                if (usernameExists === false || emailExists === false) {
                    isValid = false;
                }
            }

            return isValid;
        }

        // Button event listeners
        document.getElementById('nextStep1').addEventListener('click', async () => {
            const isValid = await validateCurrentStep();
            if (isValid) {
                showStep(2);
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        document.getElementById('prevStep2').addEventListener('click', () => {
            showStep(1);
        });

        document.getElementById('nextStep2').addEventListener('click', async () => {
            const isValid = await validateCurrentStep();
            if (isValid) {
                showStep(3);
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        document.getElementById('prevStep3').addEventListener('click', () => {
            showStep(2);
        });

        // Form submission
        const form = document.getElementById('registerForm');
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            // Validate final step
            const isValid = await validateCurrentStep();

            if (!isValid) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // All validations passed, submit the form
                form.submit();
            }
        });

        // Initialize progress bar
        updateProgressBar();
    </script>
</body>

</html>