document.addEventListener('DOMContentLoaded', () => {

    // ==========================
    //  POPUP HELPERS
    // ==========================
    const popupOverlay = document.getElementById('popupOverlay');
    const popupTitle = document.getElementById('popupTitle');
    const popupMessage = document.getElementById('popupMessage');
    const popupIcon = document.getElementById('popupIcon');
    const popupOkBtn = document.getElementById('popupOkBtn');
    const popupCloseX = document.getElementById('popupCloseX');

    function showPopup(type, title, message, btnText) {
        if (!popupOverlay) return;

        popupTitle.textContent = title || 'Message';
        popupMessage.textContent = message || '';

        popupIcon.classList.remove('popup-icon-success', 'popup-icon-error');
        popupOkBtn.classList.remove('popup-btn-success', 'popup-btn-error');

        if (type === 'error') {
            popupIcon.classList.add('popup-icon-error');
            popupOkBtn.classList.add('popup-btn-error');
            popupIcon.textContent = '✕';
            popupOkBtn.textContent = btnText || 'Try again';
        } else {
            popupIcon.classList.add('popup-icon-success');
            popupOkBtn.classList.add('popup-btn-success');
            popupIcon.textContent = '✓';
            popupOkBtn.textContent = btnText || 'Continue';
        }

        popupOverlay.classList.add('show');
    }

    function hidePopup() {
        popupOverlay?.classList.remove('show');
    }

    popupOkBtn?.addEventListener('click', hidePopup);
    popupCloseX?.addEventListener('click', hidePopup);
    popupOverlay?.addEventListener('click', (e) => {
        if (e.target === popupOverlay) hidePopup();
    });


    // ==========================
    //  ADDRESS DROPDOWNS VIA JSON / API
    // ==========================
    const provinceSelect = document.getElementById("province");
    const citySelect = document.getElementById("city");
    const barangaySelect = document.getElementById("barangay");

    // values that were previously selected (from PHP via data-selected)
    const preselectedProvince = provinceSelect?.dataset.selected || "";
    const preselectedCity = citySelect?.dataset.selected || "";
    const preselectedBarangay = barangaySelect?.dataset.selected || "";

    // in-memory cache of the address data
    let phAddressData = []; // [{ province: "Rizal", cities: [{ city: "Antipolo", barangays: ["..."] }, ...] }]

    function resetSelect(select, placeholderText) {
        if (!select) return;
        select.innerHTML = "";
        const opt = document.createElement("option");
        opt.value = "";
        opt.textContent = placeholderText;
        select.appendChild(opt);
    }

    function populateProvinces() {
        if (!provinceSelect) return;

        resetSelect(provinceSelect, "Select Province");
        resetSelect(citySelect, "Select City");
        resetSelect(barangaySelect, "Select Barangay");

        phAddressData.forEach(p => {
            const opt = document.createElement("option");
            opt.value = p.province;      // e.g. "Rizal"
            opt.textContent = p.province;
            provinceSelect.appendChild(opt);
        });

        // reselect old value if any
        if (preselectedProvince) {
            provinceSelect.value = preselectedProvince;
            // trigger change to load corresponding cities
            provinceSelect.dispatchEvent(new Event("change"));
        }
    }

    function populateCitiesFor(provinceName) {
        if (!citySelect || !barangaySelect) return;

        resetSelect(citySelect, "Select City");
        resetSelect(barangaySelect, "Select Barangay");

        const provinceObj = phAddressData.find(p => p.province === provinceName);
        if (!provinceObj) return;

        provinceObj.cities.forEach(c => {
            const opt = document.createElement("option");
            opt.value = c.city;        // e.g. "Antipolo"
            opt.textContent = c.city;
            citySelect.appendChild(opt);
        });

        if (preselectedCity) {
            citySelect.value = preselectedCity;
            citySelect.dispatchEvent(new Event("change"));
        }
    }

    function populateBarangaysFor(provinceName, cityName) {
        if (!barangaySelect) return;

        resetSelect(barangaySelect, "Select Barangay");

        const provinceObj = phAddressData.find(p => p.province === provinceName);
        if (!provinceObj) return;

        const cityObj = provinceObj.cities.find(c => c.city === cityName);
        if (!cityObj) return;

        cityObj.barangays.forEach(b => {
            const opt = document.createElement("option");
            opt.value = b;           // e.g. "San Roque"
            opt.textContent = b;
            barangaySelect.appendChild(opt);
        });

        if (preselectedBarangay) {
            barangaySelect.value = preselectedBarangay;
        }
    }

    // Load JSON from file or API (no DB table needed)
    async function loadPhilippineAddressData() {
        try {
            // 👉 change this URL to wherever you put your JSON or API
            // e.g. public/ph-json/philippines-address.json
            const res = await fetch("public/ph-json/philippines-address.json");
            if (!res.ok) throw new Error("Failed to load address data");
            phAddressData = await res.json();

            populateProvinces();
        } catch (err) {
            console.error("Error loading PH address data:", err);
        }
    }

    // Province change -> load cities
    provinceSelect?.addEventListener("change", () => {
        const selectedProvince = provinceSelect.value;
        populateCitiesFor(selectedProvince);

        if (touchedFields.has("province")) validateField("province");
        if (touchedFields.has("city")) validateField("city");
        if (touchedFields.has("barangay")) validateField("barangay");
    });

    // City change -> load barangays
    citySelect?.addEventListener("change", () => {
        const selectedProvince = provinceSelect.value;
        const selectedCity = citySelect.value;
        populateBarangaysFor(selectedProvince, selectedCity);

        if (touchedFields.has("city")) validateField("city");
        if (touchedFields.has("barangay")) validateField("barangay");
    });

    // Kick everything off
    loadPhilippineAddressData();

    // ==========================
    //  STEP STATE
    // ==========================
    let currentStep = 1;
    const totalSteps = 3;

    // ==========================
    //  ELEMENTS
    // ==========================
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

    const createAccountBtn = document.getElementById('createAccountBtn');
    const form = document.getElementById('registerForm');
    const verifyEmailBtn = document.getElementById('verifyEmailBtn');
    const nextStep1Btn = document.getElementById('nextStep1');

    // ==========================
    //  EMAIL VERIFIED STATE
    // ==========================
    const urlParams = new URLSearchParams(window.location.search);
    const verifyStatus = urlParams.get('verify');  // "success" | "failed" | null
    const bodyDataset = document.body.dataset || {};
    const sessionVerified = bodyDataset.emailVerified === '1';

    let emailVerified = false;
    const emailInput = inputs.email;
    let verifiedEmailValue = '';
    let emailLinkSent = false;           // mayroon na bang pinadalang link?
    let verifyCooldownTimer = null;      // para sa 2-minute cooldown

    if ((verifyStatus === 'success') || sessionVerified) {
        // Galing sa verification link → verified na
        emailVerified = true;
        verifiedEmailValue = emailInput ? emailInput.value : '';
        emailLinkSent = true;    // may nagamit nang link na
        // ❌ WALA nang popup dito; tahimik lang, allow Next button
    } else if (verifyStatus === 'failed') {
        showPopup(
            'error',
            'Error!',
            'The verification link is invalid or has expired. Please request a new verification email.',
            'Got it'
        );
    }


    function updateEmailButtons() {
        if (!verifyEmailBtn || !nextStep1Btn) return;

        if (emailVerified) {
            // verified na → Next enabled, Verify disabled
            nextStep1Btn.disabled = false;
            nextStep1Btn.classList.remove('btn-disabled');

            verifyEmailBtn.disabled = true;
            verifyEmailBtn.classList.add('btn-disabled');
        } else {
            // hindi pa verified → Next disabled
            nextStep1Btn.disabled = true;
            nextStep1Btn.classList.add('btn-disabled');

            // kung may pinadalang link (cooldown), huwag muna payagan mag-click
            if (emailLinkSent) {
                verifyEmailBtn.disabled = true;
                verifyEmailBtn.classList.add('btn-disabled');
            } else {
                verifyEmailBtn.disabled = false;
                verifyEmailBtn.classList.remove('btn-disabled');
            }
        }
    }


    updateEmailButtons();

    if (emailInput) {
        emailInput.addEventListener('input', () => {
            // kapag nagbago ang email → kailangan ulit mag-verify
            if (emailInput.value !== verifiedEmailValue) {
                emailVerified = false;
                verifiedEmailValue = '';
                emailLinkSent = false;           // alisin cooldown para sa bagong email
                if (verifyCooldownTimer) {
                    clearTimeout(verifyCooldownTimer);
                    verifyCooldownTimer = null;
                }
                updateEmailButtons();
            }
        });
    }

    // ==========================
    //  STEP SWITCHING
    // ==========================
    function showStep(step) {
        document.querySelectorAll('.form-step').forEach((el, idx) => {
            const s = idx + 1;
            if (s === step) {
                el.classList.remove('hidden');
                el.classList.add('active');
            } else {
                el.classList.add('hidden');
                el.classList.remove('active');
            }
        });

        currentStep = step;
        updateProgressBar();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    showStep(1); // always start on Step 1

    // ==========================
    //  VALIDATION + HELPERS
    // ==========================
    const touchedFields = new Set();

    const stepFields = {
        1: ['firstname', 'lastname', 'username', 'email'],
        2: ['street_address', 'province', 'city', 'barangay', 'postal_code', 'contact_num'],
        3: ['password', 'confirmPassword', 'termsCheckbox', 'recaptcha']
    };

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
        if (!/[!@#$%^&*(),.?\":{}|<>]/.test(value)) return "Must contain at least one special character.";
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

    function showError(field, message) {
        const errorElement = document.getElementById(field + '-error');
        if (!errorElement) return;

        const inputElement = inputs[field];

        if (field === 'termsCheckbox' || field === 'recaptcha') {
            if (message) {
                errorElement.textContent = message;
                errorElement.classList.add('show');
            } else {
                errorElement.classList.remove('show');
            }
            return;
        }

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
            const value = (field === 'termsCheckbox' || field === 'recaptcha')
                ? ''
                : (inputs[field]?.value || '');
            const message = validators[field](value);
            showError(field, message);
            return message === "";
        }
        return true;
    }

    async function checkUsernameExists(username) {
        if (!username || username.length < 3) return true;
        try {
            const response = await fetch('index.php?page=register&action=check', {
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
            const response = await fetch('index.php?page=register&action=check', {
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
        inputs.username.addEventListener('input', function () {
            if (touchedFields.has('username')) {
                debouncedUsernameCheck(this.value);
            }
        });
    }

    if (inputs.email) {
        inputs.email.addEventListener('input', function () {
            if (touchedFields.has('email')) {
                debouncedEmailCheck(this.value);
            }
        });
    }

    if (inputs.contact_num) {
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

        inputs.contact_num.addEventListener('keydown', function (e) {
            if ((e.key === 'Backspace' || e.key === 'Delete') && this.value.length <= 1) {
                e.preventDefault();
                this.value = '0';
            }
        });
    }

    if (inputs.postal_code) {
        inputs.postal_code.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }



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

    async function validateCurrentStep() {
        const fields = stepFields[currentStep];
        let isValid = true;

        fields.forEach(field => touchedFields.add(field));

        for (const field of fields) {
            if (field === 'apartment') {
                if (inputs[field] && inputs[field].value) {
                    if (!validateField(field)) isValid = false;
                }
            } else if (field === 'recaptcha' || field === 'termsCheckbox') {
                if (!validateField(field)) isValid = false;
            } else {
                if (inputs[field]) {
                    if (!validateField(field)) isValid = false;
                }
            }
        }

        if (currentStep === 1 && isValid) {
            const usernameOk = await checkUsernameExists(inputs.username.value);
            const emailOk = await checkEmailExists(inputs.email.value);
            if (!usernameOk || !emailOk) isValid = false;
        }

        return isValid;
    }

    // ==========================
    //  BUTTON HANDLERS
    // ==========================

    // VERIFY EMAIL
    verifyEmailBtn?.addEventListener('click', async () => {
        if (verifyEmailBtn.disabled) return;

        currentStep = 1;
        const isValid = await validateCurrentStep();

        if (!isValid) {
            const firstError = document.querySelector('.error-message.show');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
            return;
        }

        const fd = new FormData();
        fd.append('firstname', inputs.firstname.value.trim());
        fd.append('lastname', inputs.lastname.value.trim());
        fd.append('username', inputs.username.value.trim());
        fd.append('email', inputs.email.value.trim());

        try {
            verifyEmailBtn.disabled = true;
            verifyEmailBtn.classList.add('btn-disabled');

            const response = await fetch('index.php?page=register&action=sendVerifyEmail', {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();

            if (!data.success) {
                verifyEmailBtn.disabled = false;
                verifyEmailBtn.classList.remove('btn-disabled');

                showPopup(
                    'error',
                    'Email not sent',
                    data.message || 'We could not send the verification email. Please try again later.',
                    'OK'
                );
                return;
            }

            // ✅ Isang popup lang: "Check your email..."
            showPopup(
                'success',
                'Check your email',
                data.message || 'We sent a verification link. Open your email and click the button there.',
                'OK'
            );

            // hindi pa verified; naka-link na itong email
            emailVerified = false;
            verifiedEmailValue = inputs.email.value;
            emailLinkSent = true;
            updateEmailButtons();

            // 🔁 2-minute cooldown. Pag hindi pa rin verified after 2 mins → pwede ulit mag-send.
            if (verifyCooldownTimer) {
                clearTimeout(verifyCooldownTimer);
            }
            verifyCooldownTimer = setTimeout(() => {
                // kung hindi pa rin verified, at same pa rin ang email → enable ulit Verify button
                if (!emailVerified && emailInput && emailInput.value === verifiedEmailValue) {
                    emailLinkSent = false;
                    updateEmailButtons();
                }
            }, 120000); // 120,000 ms = 2 minutes


        } catch (err) {
            console.error('Error sending verification email:', err);
            showPopup('error', 'Error!', 'An error occurred while sending the verification email.', 'Try again');
            verifyEmailBtn.disabled = false;
            verifyEmailBtn.classList.remove('btn-disabled');
        }
    });

    // NEXT STEP 1
    nextStep1Btn?.addEventListener('click', async () => {
        if (!emailVerified) {
            showPopup(
                'error',
                'Verify your email',
                'Please verify your email first using the link we sent to your email address.',
                'OK'
            );
            return;
        }

        currentStep = 1;
        const isValid = await validateCurrentStep();

        if (!isValid) {
            const firstError = document.querySelector('.error-message.show');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
            return;
        }

        showStep(2);
    });

    document.getElementById('prevStep2')?.addEventListener('click', () => {
        showStep(1);
    });

    document.getElementById('nextStep2')?.addEventListener('click', async () => {
        currentStep = 2;
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

    // TERMS / MODALS
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

    // TERMS CHECKBOX / CREATE ACCOUNT
    if (createAccountBtn) {
        createAccountBtn.disabled = true;
    }

    if (inputs.termsCheckbox) {
        inputs.termsCheckbox.addEventListener('change', () => {
            touchedFields.add('termsCheckbox');

            if (inputs.termsCheckbox.checked) {
                createAccountBtn.disabled = false;
                showError('termsCheckbox', '');
            } else {
                createAccountBtn.disabled = true;
                showError('termsCheckbox', 'You must accept the terms and conditions.');
            }
        });

        const termsLabel = document.querySelector('label[for="termsCheckbox"]');
        if (termsLabel) {
            termsLabel.addEventListener('click', () => {
                touchedFields.add('termsCheckbox');
            });
        }
    }

    if (createAccountBtn) {
        createAccountBtn.addEventListener('click', function (e) {
            if (this.disabled) {
                e.preventDefault();
                e.stopPropagation();
                touchedFields.add('termsCheckbox');
                validateField('termsCheckbox');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            currentStep = 3;
            touchedFields.add('termsCheckbox');
            touchedFields.add('recaptcha');

            const isValid = await validateCurrentStep();

            if (!isValid) {
                const firstError = document.querySelector('.error-message.show');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } else {
                form.submit();
            }
        });
    }
    // ========== TOAST NOTIFICATION UTILITY ==========
    const toastRoot = document.getElementById('toast-root');

    function showToast(type = 'info', title = '', message = '') {
        if (!toastRoot) return;

        const icons = {
            info: 'i',
            success: '✓',
            warning: '!',
            error: '!'
        };

        const toast = document.createElement('div');
        toast.className = `toast toast--${type}`;

        toast.innerHTML = `
        <div class="toast__icon">${icons[type] || icons.info}</div>
        <div class="toast__content">
            <div class="toast__title">${title || type.charAt(0).toUpperCase() + type.slice(1)}</div>
            <div class="toast__message">${message || ''}</div>
        </div>
        <button class="toast__close" type="button" aria-label="Close">&times;</button>
    `;

        // close button
        toast.querySelector('.toast__close').addEventListener('click', () => {
            hideToast(toast);
        });

        // auto-hide after 4s
        const autoHide = setTimeout(() => {
            hideToast(toast);
        }, 4000);

        toast.addEventListener('mouseenter', () => clearTimeout(autoHide));

        toastRoot.appendChild(toast);
    }

    function hideToast(toastEl) {
        if (!toastEl) return;
        toastEl.style.animation = 'toast-fade-out 0.2s forwards';
        setTimeout(() => {
            toastEl.remove();
        }, 200);
    }

    updateProgressBar();
});
