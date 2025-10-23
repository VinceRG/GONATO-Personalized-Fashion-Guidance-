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

let submitClicked = false;

// ✅ Validation Functions
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

// ✅ Real-time validation (after submit or user input)
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

// ✅ Auto-format contact number
inputs.contact_num.addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

// ✅ On Submit
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
