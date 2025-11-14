

let submitClicked = false;

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
        requirements.style.display = 'block';
    } else {
        requirements.style.display = 'none';
    }

    document.getElementById('req-length').className = value.length >= 8 ? 'valid' : 'invalid';
    document.getElementById('req-uppercase').className = /[A-Z]/.test(value) ? 'valid' : 'invalid';
    document.getElementById('req-lowercase').className = /[a-z]/.test(value) ? 'valid' : 'invalid';
    document.getElementById('req-number').className = /[0-9]/.test(value) ? 'valid' : 'invalid';
    document.getElementById('req-special').className = /[!@#$%^&*(),.?":{}|<>]/.test(value) ? 'valid' : 'invalid';

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
        errorElement.style.display = 'block';
        formGroup?.classList.add('error');
        formGroup?.classList.remove('valid');
    } else {
        errorElement.style.display = 'none';
        formGroup?.classList.remove('error');
        if (inputElement.value) formGroup?.classList.add('valid');
    }
}

// Real-time validation (after submit or user input)
Object.keys(inputs).forEach(field => {
    inputs[field].addEventListener('blur', () => {
        if (submitClicked || inputs[field].value) {
            validateField(field);
        }
    });
    inputs[field].addEventListener('input', () => {
        if (submitClicked) validateField(field);
    });
});

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
}

// Auto-format contact number
inputs.contact_num.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// On Submit
form.addEventListener('submit', function (e) {
    e.preventDefault();
    submitClicked = true;

    let isValid = true;
    const validations = {
        firstname: validateFirstName(inputs.firstname.value),
        lastname: validateLastName(inputs.lastname.value),
        username: validateUsername(inputs.username.value),
        email: validateEmail(inputs.email.value),
        address: validateAddress(inputs.address.value),
        contact_num: validateContactNumber(inputs.contact_num.value),
        password: validatePassword(inputs.password.value),
        confirmPassword: validateConfirmPassword(inputs.confirmPassword.value)
    };

    for (const [field, error] of Object.entries(validations)) {
        showError(field, error);
        if (error) isValid = false;
    }

    if (!isValid) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        alert("Please correct the highlighted errors before continuing.");
    } else {
        form.submit();
    }
});

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


inputs.username.addEventListener('input', function () {
    if (touchedFields.has('username')) {
        debouncedUsernameCheck(this.value);
    }
});

inputs.email.addEventListener('input', function () {
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
    document.getElementById('req-special').className = /[!@#$%^&*(),.?":{ }|<>]/.test(value) ? 'valid' : '';

    if (!value) return "Password is required.";
    if (value.length < 8) return "Password must be at least 8 characters.";
    if (!/[A-Z]/.test(value)) return "Must contain at least one uppercase letter.";
    if (!/[a-z]/.test(value)) return "Must contain at least one lowercase letter.";
    if (!/[0-9]/.test(value)) return "Must contain at least one number.";
    if (!/[!@#$%^&*(),.?":{ }|<>]/.test(value)) return "Must contain at least one special character.";
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

inputs.username.addEventListener('input', function () {
    if (touchedFields.has('username')) {
        debouncedUsernameCheck(this.value);
    }
});

inputs.email.addEventListener('input', function () {
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