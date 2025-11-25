document.addEventListener('DOMContentLoaded', () => {

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
        confirmPassword: document.getElementById('confirmPassword'),
        termsCheckbox: document.getElementById('termsCheckbox')
    };

    // Other elements
    const createAccountBtn = document.getElementById('createAccountBtn');
    const form = document.getElementById('registerForm');

    // Track touched fields
    const touchedFields = new Set();

    // Step fields mapping
    const stepFields = {
        1: ['firstname', 'lastname', 'username', 'email'],
        2: ['street_address', 'province', 'city', 'barangay', 'postal_code', 'contact_num'],
        3: ['password', 'confirmPassword', 'termsCheckbox', 'recaptcha']
    };

    // ========== VALIDATION FUNCTIONS ==========

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
        if (requirements) {
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
        }

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

    function validateTermsCheckbox() {
        const checkbox = inputs.termsCheckbox;
        if (!checkbox || !checkbox.checked) {
            return "You must accept the terms and conditions.";
        }
        return "";
    }

    function validateRecaptcha() {
        if (typeof grecaptcha === 'undefined') {
            return "reCAPTCHA is still loading. Please wait a moment.";
        }
        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            return "Please complete the reCAPTCHA verification.";
        }
        return "";
    }

    // ========== ERROR DISPLAY ==========

    function showError(field, message) {
        const errorElement = document.getElementById(field + '-error');
        if (!errorElement) return;

        const inputElement = inputs[field];

        // Handle special fields (termsCheckbox, recaptcha)
        if (field === 'termsCheckbox' || field === 'recaptcha') {
            if (message) {
                errorElement.textContent = message;
                errorElement.classList.add('show');
            } else {
                errorElement.classList.remove('show');
            }
            return;
        }

        // Handle regular input fields
        if (!inputElement) return;
        const formGroup = inputElement.closest('.form-group');
        
        if (message) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
            formGroup?.classList.add('error');
            formGroup?.classList.remove('valid');
        } else {
            errorElement.classList.remove('show');
            formGroup?.classList.remove('error');
            if (inputElement.value || field === 'apartment') {
                formGroup?.classList.add('valid');
            }
        }
    }

    // ========== FIELD VALIDATION ==========

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
            confirmPassword: validateConfirmPassword,
            termsCheckbox: validateTermsCheckbox,
            recaptcha: validateRecaptcha
        };

        if (validators[field]) {
            const value = field === 'termsCheckbox' || field === 'recaptcha' ? '' : (inputs[field]?.value || '');
            const message = validators[field](value);
            showError(field, message);
            return message === "";
        }
        return true;
    }

    // ========== USERNAME/EMAIL EXISTENCE CHECKS ==========

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

    // ========== DEBOUNCE UTILITY ==========

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

    // ========== INPUT EVENT LISTENERS ==========

    Object.keys(inputs).forEach(field => {
        const inputElement = inputs[field];
        if (!inputElement || field === 'termsCheckbox') return;

        inputElement.addEventListener('focus', () => {
            touchedFields.add(field);
        });

        inputElement.addEventListener('blur', async () => {
            if (touchedFields.has(field)) {
                const isValid = validateField(field);
                if (field === 'username' && isValid) {
                    await checkUsernameExists(inputElement.value);
                }
                if (field === 'email' && isValid) {
                    await checkEmailExists(inputElement.value);
                }
            }
        });

        inputElement.addEventListener('input', () => {
            if (touchedFields.has(field)) {
                validateField(field);
            }
        });
    });

    // ========== DEBOUNCED CHECKS ==========

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

    if (inputs.username) {
        inputs.username.addEventListener('input', function() {
            if (touchedFields.has('username')) {
                debouncedUsernameCheck(this.value);
            }
        });
    }

    if (inputs.email) {
        inputs.email.addEventListener('input', function() {
            if (touchedFields.has('email')) {
                debouncedEmailCheck(this.value);
            }
        });
    }

    // ========== CONTACT NUMBER FORMATTING ==========

    if (inputs.contact_num) {
        inputs.contact_num.addEventListener('input', function() {
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
    }

    // ========== POSTAL CODE FORMATTING ==========

    if (inputs.postal_code) {
        inputs.postal_code.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // ========== ADDRESS DATA AND CASCADING DROPDOWNS ==========

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

    if (province) {
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
            
            // Validate after change if touched
            if (touchedFields.has('province')) {
                validateField('province');
            }
            if (touchedFields.has('city')) {
                validateField('city');
            }
            if (touchedFields.has('barangay')) {
                validateField('barangay');
            }
        });
    }

    if (city) {
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
            
            // Validate after change if touched
            if (touchedFields.has('city')) {
                validateField('city');
            }
            if (touchedFields.has('barangay')) {
                validateField('barangay');
            }
        });
    }

    // ========== STEP NAVIGATION ==========

    function updateProgressBar() {
        const progressLine = document.getElementById('progressLine');
        const progressSteps = document.querySelectorAll('.progress-step');
        if (!progressLine) return;

        const progressPercent = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressLine.style.width = progressPercent + '%';

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
        const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);
        if (currentStepElement) {
            currentStepElement.classList.add('active');
        }
        currentStep = step;
        updateProgressBar();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    async function validateCurrentStep() {
        const fields = stepFields[currentStep];
        let isValid = true;
        
        // Mark all fields in current step as touched
        fields.forEach(field => touchedFields.add(field));

        // Validate all fields
        for (const field of fields) {
            if (field === 'apartment') {
                if (inputs[field] && inputs[field].value) {
                    if (!validateField(field)) isValid = false;
                }
            } else if (field === 'recaptcha' || field === 'termsCheckbox') {
                // Force validation for these special fields
                if (!validateField(field)) isValid = false;
            } else {
                if (inputs[field]) {
                    if (!validateField(field)) isValid = false;
                }
            }
        }

        // Additional checks for step 1
        if (currentStep === 1 && isValid) {
            const usernameExists = await checkUsernameExists(inputs.username.value);
            const emailExists = await checkEmailExists(inputs.email.value);
            if (usernameExists === false || emailExists === false) {
                isValid = false;
            }
        }

        return isValid;
    }

    // ========== BUTTON EVENT LISTENERS ==========

    document.getElementById('nextStep1')?.addEventListener('click', async () => {
        const isValid = await validateCurrentStep();
        if (isValid) {
            showStep(2);
        } else {
            const firstError = document.querySelector('.error-message.show');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    });

    document.getElementById('prevStep2')?.addEventListener('click', () => {
        showStep(1);
    });

    document.getElementById('nextStep2')?.addEventListener('click', async () => {
        const isValid = await validateCurrentStep();
        if (isValid) {
            showStep(3);
        } else {
            const firstError = document.querySelector('.error-message.show');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    });

    document.getElementById('prevStep3')?.addEventListener('click', () => {
        showStep(2);
    });

    // ========== MODAL LOGIC ==========

    const termsModal = document.getElementById('termsModal');
    const privacyModal = document.getElementById('privacyModal');
    const termsLink = document.getElementById('termsLink');
    const privacyLink = document.getElementById('privacyLink');
    const closeButtons = document.querySelectorAll('.modal-close');

    function openModal(modal) {
        if (modal) modal.style.display = 'block';
    }

    function closeModal(modal) {
        if (modal) modal.style.display = 'none';
    }

    termsLink?.addEventListener('click', (e) => {
        e.preventDefault();
        openModal(termsModal);
    });

    privacyLink?.addEventListener('click', (e) => {
        e.preventDefault();
        openModal(privacyModal);
    });

    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-modal');
            closeModal(document.getElementById(modalId));
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-overlay')) {
            closeModal(e.target);
        }
    });

    // ========== TERMS CHECKBOX LOGIC ==========

    if (createAccountBtn) {
        createAccountBtn.disabled = true;
    }

    if (inputs.termsCheckbox) {
        inputs.termsCheckbox.addEventListener('change', () => {
            // Always mark as touched when user interacts
            touchedFields.add('termsCheckbox');
            
            if (inputs.termsCheckbox.checked) {
                createAccountBtn.disabled = false;
                showError('termsCheckbox', '');
            } else {
                createAccountBtn.disabled = true;
                // Show error when unchecked if field was touched
                if (touchedFields.has('termsCheckbox')) {
                    showError('termsCheckbox', 'You must accept the terms and conditions.');
                }
            }
        });
        
        // Also add click listener to label
        const termsLabel = document.querySelector('label[for="termsCheckbox"]');
        if (termsLabel) {
            termsLabel.addEventListener('click', () => {
                touchedFields.add('termsCheckbox');
            });
        }
    }

    // Add validation trigger when Create Account button is clicked but disabled
    if (createAccountBtn) {
        createAccountBtn.addEventListener('click', function(e) {
            if (this.disabled) {
                e.preventDefault();
                e.stopPropagation();
                touchedFields.add('termsCheckbox');
                validateField('termsCheckbox');
            }
        });
    }

    // ========== FORM SUBMISSION ==========

    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Ensure step 3 fields are marked as touched
            touchedFields.add('termsCheckbox');
            touchedFields.add('recaptcha');
            
            const isValid = await validateCurrentStep();
            
            if (!isValid) {
                // Find first error and scroll to it
                const firstError = document.querySelector('.error-message.show');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } else {
                // Submit the form
                this.submit();
            }
        });
    }

    // ========== INITIALIZE ==========

    updateProgressBar();
});