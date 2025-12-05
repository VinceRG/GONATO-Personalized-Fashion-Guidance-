<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amarelle</title>
    <link rel="icon" type="image/png" href="public/image/amarelle.png">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="public/css/register.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
  .readonly-select {
    pointer-events: none;        
    background-color: #f9fafb;
    color: #6b7280;
}
        .message {
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }
        .message.success {
            background: #e6f8ec;
            color: #166534;
            border: 1px solid #16a34a33;
        }
        .message.error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #b91c1c33;
        }

        .popup-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .popup-overlay.show {
            display: flex;
        }
        .popup-card {
            background: #f4f3ee;
            border-radius: 20px;
            max-width: 420px;
            width: 90%;
            padding: 26px 28px 22px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            text-align: center;
            position: relative;
            font-family: 'Lexend', sans-serif;
        }
        .popup-icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: #fff;
            font-size: 30px;
        }
        .popup-icon-success {
            background: linear-gradient(135deg,#1d9bf0,#0d71d5);
        }
        .popup-icon-error {
            background: linear-gradient(135deg,#f97373,#ef4444);
        }
        .popup-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 6px;
            color: #111827;
        }
        .popup-message {
            font-size: 0.9rem;
            color: #4b5563;
            margin-bottom: 18px;
        }
        .popup-close-x {
            position: absolute;
            top: 10px;
            right: 12px;
            border: none;
            background: transparent;
            font-size: 1.1rem;
            cursor: pointer;
            color: #6b7280;
        }
        .popup-btn {
            border: none;
            padding: 10px 28px;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            color: #fff;
        }
        .popup-btn-success {
            background: linear-gradient(135deg,#1d9bf0,#0d71d5);
        }
        .popup-btn-error {
            background: linear-gradient(135deg,#f97373,#ef4444);
        }

        .btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
</head>

<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $error    = $GLOBALS['error']   ?? '';
    $success  = $GLOBALS['success'] ?? '';
    $formData = $GLOBALS['formData'] ?? [];
?>

<body data-email-verified="<?= !empty($_SESSION['register_email_verified']) ? '1' : '0' ?>">
    <div id="toast-root"></div>

    <div id="popupOverlay" class="popup-overlay">
        <div class="popup-card">
            <button class="popup-close-x" id="popupCloseX">&times;</button>
            <div id="popupIcon" class="popup-icon-circle popup-icon-success">✓</div>
            <div class="popup-title" id="popupTitle">Message</div>
            <p class="popup-message" id="popupMessage">...</p>
            <button class="popup-btn popup-btn-success" id="popupOkBtn">Continue</button>
        </div>
    </div>

    <div class="header">
        <a href="index.php?page=landing" class="logo-link">
            <img src="public/image/amarelle.png" alt="Amarelle Logo" class="brand-logo" style="height: 50px; width: auto; margin-right: 10px; vertical-align: middle;">
           
        </a>
    </div>

    <div class="content">
        <h1>Join Amarelle</h1>
        <p class="page-description">Unlock the Amarelle Experience</p>

        <?php if (!empty($success)) : ?>
            <div class="message success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)) : ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="register-form" id="registerForm">
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

            <!-- STEP 1 -->
            <div class="form-step active" data-step="1">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input
                            type="text"
                            id="firstname"
                            name="firstname"
                            placeholder="Enter your first name"
                            value="<?= htmlspecialchars($formData['firstname'] ?? ($_SESSION['register_firstname'] ?? '')) ?>"
                        >
                        <span class="error-message" id="firstname-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input
                            type="text"
                            id="lastname"
                            name="lastname"
                            placeholder="Enter your last name"
                            value="<?= htmlspecialchars($formData['lastname'] ?? ($_SESSION['register_lastname'] ?? '')) ?>"
                        >
                        <span class="error-message" id="lastname-error"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Choose a username"
                            value="<?= htmlspecialchars($formData['username'] ?? ($_SESSION['register_username'] ?? '')) ?>"
                        >
                        <span class="error-message" id="username-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email address"
                            value="<?= htmlspecialchars($formData['email'] ?? ($_SESSION['register_email'] ?? '')) ?>"
                        >
                        <span class="error-message" id="email-error"></span>
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="button" class="btn btn-secondary" id="verifyEmailBtn">
                        Verify Email
                    </button>

                    <button type="button" class="btn btn-primary btn-full btn-disabled" id="nextStep1" disabled>
                        Next
                    </button>
                </div>
            </div>

            <!-- STEP 2 -->
<div class="form-step" data-step="2">
    <div class="address-section">
        <h3>Address</h3>

        

        <!-- SEARCH + MAP
        <div class="form-group">
            <label for="address_search">Search Address (OpenStreetMap)</label>
            <input
                type="text"
                id="address_search"
                placeholder="Type street, city or place..."
                autocomplete="off"
            >
            <button type="button" class="btn btn-secondary" id="addressSearchBtn" style="margin-top:8px;">
                Search on map
            </button>
            <span class="hint" style="font-size:0.8rem;">
                Tip: Type a more complete address (street + city) so we can auto-fill province, city and barangay.
            </span>
        </div>

        <div class="form-group">
            <div id="map" style="width:100%;height:260px;border-radius:12px;margin-top:8px;"></div>
        </div> -->

        <!-- Hidden: lat / lng (sent to PHP)
        <input
            type="hidden"
            id="latitude"
            name="latitude"
            value="<?= htmlspecialchars($formData['latitude'] ?? '', ENT_QUOTES) ?>"
        >
        <input
            type="hidden"
            id="longitude"
            name="longitude"
            value="<?= htmlspecialchars($formData['longitude'] ?? '', ENT_QUOTES) ?>"
        > -->

        <!-- NORMAL ADDRESS FIELDS -->
        <div class="form-group">
            <label for="street_address">Street Address / House Number</label>
            <input
                type="text"
                id="street_address"
                name="street_address"
                placeholder="e.g., 123 Main St or Unit 4B"
                value="<?= htmlspecialchars($formData['street_address'] ?? '', ENT_QUOTES) ?>"
            >
            <span class="error-message" id="street_address-error"></span>
        </div>
        
        <div class="form-group">
            <label for="contact_num">Contact Number</label>
            <input
                type="text"
                id="contact_num"
                name="contact_num"
                maxlength="11"
                placeholder="09XX XXX XXXX"
                value="<?= htmlspecialchars($formData['contact_num'] ?? '0', ENT_QUOTES) ?>"
            >
            <span class="error-message" id="contact_num-error"></span>
        </div>


    <!-- Region -->
<div class="form-group">
    <label for="regionSelect">Region</label>
    <select id="regionSelect" name="region" class="select-input">
        <option value="">Select Region</option>
        <option value="Metro Manila">Metro Manila</option>
        <option value="North Luzon">North Luzon</option>
        <option value="South Luzon">South Luzon</option>
        <option value="Visayas">Visayas</option>
        <option value="Mindanao">Mindanao</option>
    </select>
    <span class="error-message" id="region-error"></span>
</div>

<div class="form-row">
    <!-- Province -->
    <div class="form-group" style="flex:1;">
        <label for="province">Province</label>
        <select id="province" name="province" class="select-input">
            <option value="">Select Province</option>
        </select>
        <span class="error-message" id="province-error"></span>
    </div>

    <!-- City / Municipality -->
    <div class="form-group" style="flex:1;">
        <label for="city">City / Town</label>
        <select id="city" name="city" class="select-input">
            <option value="">Select City / Municipality</option>
        </select>
        <span class="error-message" id="city-error"></span>
    </div>
</div>

<!-- Barangay -->
<div class="form-group">
    <label for="barangay">Barangay</label>
    <select id="barangay" name="barangay" class="select-input">
        <option value="">Select Barangay</option>
    </select>
    <span class="error-message" id="barangay-error"></span>
</div>


        <div class="form-group">
            <label for="postal_code">Postal / ZIP Code</label>
            <input
                type="text"
                id="postal_code"
                name="postal_code"
                placeholder="e.g., 1101"
                maxlength="10"
                value="<?= htmlspecialchars($formData['postal_code'] ?? '', ENT_QUOTES) ?>"
            >
            <span class="error-message" id="postal_code-error"></span>
        </div>

    </div>

    <div class="form-buttons">
        <button type="button" class="btn btn-secondary" id="prevStep2">Back</button>
        <button type="button" class="btn btn-primary" id="nextStep2">Next</button>
    </div>
</div>


            <!-- STEP 3 -->
            <div class="form-step" data-step="3">
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Create a strong password"
                        >
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
                        <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            placeholder="Re-enter your password"
                        >
                        <span class="error-message" id="confirmPassword-error"></span>
                    </div>
                </div>

                <div class="form-group">
                    <p class="terms-text" style="justify-content: center;">
                        <input type="checkbox" id="termsCheckbox" name="terms">
                        <label for="termsCheckbox">
                            By creating your account or signing in, you agree to our
                            <a id="privacyLink">Privacy Policy</a> &
                            <a id="termsLink">Cookies and Consent</a>
                        </label>
                    </p>
                    <span class="error-message" id="termsCheckbox-error"></span>

                    <div class="g-recaptcha" data-sitekey="6LeCugUsAAAAAMevrBVqSjs6AG8SsQZ8qrJGvjDZ"></div>
                    <span class="error-message" id="recaptcha-error"></span>
                </div>

                <div class="form-buttons">
                    <button type="button" class="btn btn-secondary" id="prevStep3">Back</button>
                    <button type="submit" class="btn btn-primary" id="createAccountBtn">Create Account</button>
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

        <div id="termsModal" class="modal-overlay">
            <div class="modal-content">
    <span class="modal-close" data-modal="termsModal">&times;</span>
            <h2>Cookies and Consent</h2>
            
<p><strong>Last Updated:</strong> Nov 14, 2025</p>

<p>Welcome to Amarelle! This Cookies and Consent Policy explains how we use cookies and similar technologies on our website, located at amarelle2025.com.</p>

<p>By accessing or using this website, you consent to the use of cookies as outlined in this policy. If you do not agree with the use of cookies, you should adjust your browser settings or discontinue using the website.</p>

<p>The following terminology applies to this Cookies and Consent Policy and all Agreements: "Client", "You" and "Your" refers to you, the person accessing this website and consenting to our cookie usage. "The Company", "Ourselves", "We", "Our" and "Us", refers to Amarelle. "Party", "Parties", or "Us", refers to both the Client and ourselves.</p>

<h3>What Are Cookies?</h3>
<p>Cookies are small text files that are stored on your device when you visit a website. They help us provide essential functions, improve your browsing experience, and analyze website performance.</p>

<h3>Types of Cookies We Use</h3>
<p>We use different types of cookies for various purposes. These may include:</p>

<ul>
    <li><strong>Essential Cookies:</strong> Required for the website to function properly. These enable basic features such as navigation and access to secure areas.</li>
    <li><strong>Analytics Cookies:</strong> Help us understand how visitors interact with the website by collecting information such as pages visited and traffic sources.</li>
    <li><strong>Functional Cookies:</strong> Allow the website to remember your preferences and provide enhanced functionality.</li>
    <li><strong>Advertising Cookies:</strong (if applicable)> Used by third parties to deliver relevant advertisements and track ad performance.</li>
</ul>

<h3>Your Consent</h3>
<p>Upon first visiting our website, you may encounter a cookie consent banner. By selecting "Accept", you agree to our use of cookies. You may choose to "Reject" non-essential cookies or customize which cookies you allow.</p>
<p>You can modify or withdraw your consent at any time by accessing your cookie settings on our website or adjusting your browser’s cookie controls.</p>

<h3>Managing Cookies</h3>
<p>You can control or delete cookies through your browser settings. However, restricting certain cookies may affect the functionality and performance of the website.</p>
<p>Common browsers provide cookie management settings, including Google Chrome, Mozilla Firefox, Safari, and Microsoft Edge. Please refer to your browser's help section for detailed instructions.</p>

<h3>Third-Party Cookies</h3>
<p>Some cookies may be placed by trusted third-party services such as analytics providers, embedded content platforms, or advertising networks. These third parties may collect data according to their own privacy policies.</p>

<h3>Privacy</h3>
<p>For more information on how we handle your personal data, please refer to our Privacy Policy. Cookies may work alongside personal data to enhance your experience.</p>

<h3>Changes to This Policy</h3>
<p>We may update this Cookies and Consent Policy at any time. We encourage you to review this page regularly to stay informed of any changes. Continued use of the website signifies acceptance of the updated policy.</p>

<h3>Contact Us</h3>
<p>If you have any questions regarding our use of cookies, please contact us at amarelle2025@gmail.com.</p>

            </div>
        </div>

        <div id="privacyModal" class="modal-overlay">
            <div class="modal-content">
                <span class="modal-close" data-modal="privacyModal">&times;</span>
            <h2>Privacy Policy</h2>
            
            <p><strong>Last Updated:</strong> Nov 14, 2025</p>

            <p>Your privacy is important to us. It is Amarelle's policy to respect your privacy regarding any information we may collect from you across our website, amarelle2025.com, and other sites we own and operate.</p>
            
            <p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we’re collecting it and how it will be used.</p>

            <h3>1. Information We Collect</h3>
            
            <p>We may collect information in the following ways:</p>
            
            <h4>Information You Provide to Us:</h4>
            <p>This includes personal information you provide when you:</p>
            <ul>
                <li>Register for an account</li>
                <li>Make a purchase</li>
                <li>Sign up for our newsletter</li>
                <li>Contact us through a contact form or for customer support</li>
                <li>(e.g., Name, Email Address, Phone Number, Billing/Shipping Address, Payment Information)</li>
            </ul>

            <h4>Information We Collect Automatically:</h4>
            <p>When you access and use our website, we may automatically collect certain information about your device and usage, including:</p>
            <ul>
                <li><strong>Log and Usage Data:</strong> Service-related, diagnostic, usage, and performance information our servers automatically collect, such as your IP address, browser type, device characteristics, operating system, language preferences, and referring URLs.</li>
                <li><strong>Cookies and Tracking Technologies:</strong> We use cookies and similar tracking technologies (like web beacons and pixels) to access or store information. You can find more details about this in our "Cookies" section below.</li>
            </ul>

            <h3>2. How We Use Your Information</h3>
            <p>We use the information we collect for various purposes, including:</p>
            <ul>
                <li><strong>To provide and maintain our Service:</strong> Including to process your transactions, manage your account, and fulfill your orders.</li>
                <li><strong>To improve our Service:</strong> To understand how users interact with our website so we can improve its functionality and user experience.</li>
                <li><strong>To communicate with you:</strong> To respond to your inquiries, send you service updates, provide customer support, and (with your consent) send you marketing communications or newsletters.</li>
                <li><strong>For security and fraud prevention:</strong> To monitor and protect our website, detect security incidents, and prevent fraudulent or illegal activity.</li>
                <li><strong>For legal compliance:</strong> To comply with applicable laws, legal processes, or government requests.</li>
            </ul>

            <h3>3. Legal Basis for Processing (For EEA/UK Users)</h3>
            <p>If you are in the European Economic Area (EEA) or the UK, our legal basis for collecting and using the personal information described above will depend on the information concerned and the specific context. We will normally collect information from you only where we have your consent, where we need the information to perform a contract with you, or where the processing is in our legitimate interests and not overridden by your data protection interests or fundamental rights.</p>

            <h3>4. Data Sharing and Disclosure</h3>
            <p>We do not sell, trade, or rent your personal information to third parties. We may share your information with the following categories of third parties for the purposes described in this policy:</p>
            <ul>
                <li><strong>Service Providers:</strong> Third-party vendors who perform services on our behalf, such as payment processing, order fulfillment, web hosting, email delivery, and data analytics.</li>
                <li><strong>Legal Obligations:</strong> We may disclose your information if required to do so by law or in response to a valid legal process, such as a subpoena, court order, or government request.</li>
                <li><strong>Business Transfers:</strong> In the event of a merger, acquisition, sale of assets, or bankruptcy, your information may be transferred as part of that transaction.</li>
                <li><strong>With Your Consent:</strong> We may disclose your personal information for any other purpose with your consent.</li>
            </ul>

            <h3>5. Data Security</h3>
            <p>We take the security of your data seriously. We use commercially acceptable means (such as administrative, technical, and physical measures) to protect your personal information from loss, theft, misuse, and unauthorized access or disclosure. However, please remember that no method of transmission over the Internet or method of electronic storage is 100% secure. While we strive to protect your data, we cannot guarantee its absolute security.</p>

            <h3>6. Data Retention</h3>
            <p>We only retain collected information for as long as necessary to provide you with your requested service or to fulfill the purposes outlined in this policy. When your information is no longer required, we will either delete or anonymize it. For legal or regulatory reasons, we may be required to retain some information for longer periods.</p>

            <h3>7. Your Data Protection Rights</h3>
            <p>Depending on your location, you may have the following rights regarding your personal information:</p>
            <ul>
                <li><strong>The right to access:</strong> You can request copies of your personal data.</li>
                <li><strong>The right to rectification:</strong> You can request that we correct any information you believe is inaccurate or incomplete.</li>
                <li><strong>The right to erasure:</strong> You can request that we erase your personal data, under certain conditions.</li>
                <li><strong>The right to restrict processing:</strong> You can request that we restrict the processing of your data, under certain conditions.</li>
                <li><strong>The right to object to processing:</strong> You can object to our processing of your personal data, under certain conditions.</li>
                <li><strong>The right to data portability:</strong> You can request that we transfer the data we have collected to another organization, or directly to you.</li>
                <li><strong>The right to withdraw consent:</strong> If we are processing your data based on consent, you have the right to withdraw that consent at any time.</li>
            </ul>
            <p>To exercise any of these rights, please contact us at [Your Contact Email].</p>

            <h3>8. Cookies</h3>
            <p>We use "cookies" to collect information about you and your activity across our site. A cookie is a small piece of data that our website stores on your computer and accesses each time you visit. This helps us understand how you use our site and serve you content based on preferences you have specified. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>

            <h3>9. Children's Privacy</h3>
            <p>Our service is not directed to individuals under the age of 13 [or 16, depending on jurisdiction]. We do not knowingly collect personal information from children. If we become aware that we have collected personal data from a child without parental consent, we will take steps to remove that information from our servers.</p>

            <h3>10. Links to Other Websites</h3>
            <p>Our website may link to external sites that are not operated by us. Please be aware that we have no control over the content and practices of these sites, and cannot accept responsibility or liability for their respective privacy policies. We encourage you to review the privacy policy of any third-party site you visit.</p>

            <h3>11. Changes to This Privacy Policy</h3>
            <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date at the top. You are advised to review this Privacy Policy periodically for any changes.</p>

            </div>
        </div>

        <script src="public/js/register.js"></script>
        <script>document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registrationForm");

    form.addEventListener("submit", function (e) {
        let hasError = false;

        // Clear previous errors
        document.querySelectorAll(".error-message").forEach(el => el.innerHTML = "");

        // --- Validate Password Strength ---
        const password = document.getElementById("password").value;
        const passwordError = document.getElementById("password-error");

        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

        if (!passwordRegex.test(password)) {
            passwordError.innerHTML = 
                "Password must be at least 8 characters, include uppercase, lowercase, and a number.";
            hasError = true;
        }

        // --- Validate Terms and Conditions ---
        const termsCheckbox = document.getElementById("terms");
        const termsError = document.getElementById("terms-error");

        if (!termsCheckbox.checked) {
            termsError.innerHTML = "You must agree to the Terms and Conditions.";
            hasError = true;
        }

        // --- Validate reCAPTCHA ---
        const recaptchaResponse = grecaptcha.getResponse();
        const recaptchaError = document.getElementById("recaptcha-error");

        if (recaptchaResponse.length === 0) {
            recaptchaError.innerHTML = "Please complete the reCAPTCHA verification.";
            hasError = true;
        }

        // --- Stop form submission if any errors exist ---
        if (hasError) {
            e.preventDefault();
        }
    });
});
</script>
<script src="public/js/register.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// -------------------------
// FORM VALIDATION (step 3)
// -------------------------
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registerForm");

    if (!form) return;

    form.addEventListener("submit", function (e) {
        let hasError = false;

        // Clear previous errors
        document.querySelectorAll(".error-message").forEach(el => el.innerHTML = "");

        // --- Validate Password Strength ---
        const password = document.getElementById("password").value;
        const passwordError = document.getElementById("password-error");

        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

        if (!passwordRegex.test(password)) {
            passwordError.innerHTML =
                "Password must be at least 8 characters, include uppercase, lowercase, and a number.";
            hasError = true;
        }

        // --- Validate Terms and Conditions ---
        const termsCheckbox = document.getElementById("termsCheckbox");
        const termsError = document.getElementById("termsCheckbox-error");

        if (!termsCheckbox.checked) {
            termsError.innerHTML = "You must agree to the Terms and Conditions.";
            hasError = true;
        }

        // --- Validate reCAPTCHA ---
        const recaptchaResponse = grecaptcha.getResponse();
        const recaptchaError = document.getElementById("recaptcha-error");

        if (recaptchaResponse.length === 0) {
            recaptchaError.innerHTML = "Please complete the reCAPTCHA verification.";
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
        }
    });
});
</script>

<script>
// =======================================
//  PSGC dynamic address logic
//  App regions: Metro Manila, N/NL/South Luzon, Visayas, Mindanao
// =======================================
const PSGC_API = "https://psgc.gitlab.io/api";

const regionSelect   = document.getElementById("regionSelect");
const provinceSelect = document.getElementById("province");
const citySelect     = document.getElementById("city");
const barangaySelect = document.getElementById("barangay");

// Map your 5 "app regions" to real PSGC region codes
const APP_REGION_MAP = {
    "Metro Manila": ["130000000"],                              // NCR
    "North Luzon": ["010000000","020000000","030000000","140000000"], // Region I, II, III, CAR
    "South Luzon": ["040000000","170000000","050000000"],       // CALABARZON, MIMAROPA, Bicol
    "Visayas":     ["060000000","070000000","080000000"],       // Western, Central, Eastern Visayas
    "Mindanao":    ["090000000","100000000","110000000","120000000","160000000","150000000"] // Mindanao + BARMM
};

function resetSelect(sel, placeholder) {
    if (!sel) return;
    sel.innerHTML = "";
    const opt = document.createElement("option");
    opt.value = "";
    opt.textContent = placeholder;
    sel.appendChild(opt);
}

// ============================
//  When app Region changes
// ============================
async function handleAppRegionChange(appRegion) {
    resetSelect(provinceSelect, "Select Province");
    resetSelect(citySelect, "Select City / Municipality");
    resetSelect(barangaySelect, "Select Barangay");

    if (!appRegion) {
        provinceSelect.disabled = false;
        citySelect.disabled     = true;
        barangaySelect.disabled = true;
        return;
    }

    const regionCodes = APP_REGION_MAP[appRegion] || [];

    // Special case: Metro Manila (NCR - no provinces)
   if (appRegion === "Metro Manila") {
    resetSelect(provinceSelect, "Province");

    const opt = document.createElement("option");
    opt.value = "Metro Manila";
    opt.textContent = "Metro Manila";
    opt.selected = true;
    provinceSelect.appendChild(opt);

    provinceSelect.disabled = false;
    provinceSelect.classList.add("readonly-select");

    citySelect.disabled     = false;
    barangaySelect.disabled = true;

    await loadCitiesForNCR();
    return;
}

    // Other app regions (North/South Luzon, Visayas, Mindanao)
   // Other app regions (North/South Luzon, Visayas, Mindanao)
// temporarily disable while loading
provinceSelect.disabled = true;
provinceSelect.classList.remove("readonly-select");
citySelect.disabled     = true;
barangaySelect.disabled = true;

// Fetch provinces for each PSGC region in mapping
const allProvinces = [];
for (const rCode of regionCodes) {
    try {
        const res = await fetch(`${PSGC_API}/regions/${rCode}/provinces/`);
        if (!res.ok) continue;
        const provinces = await res.json();
        provinces.forEach(p => allProvinces.push(p));
    } catch (e) {
        console.error("Error loading provinces for region", rCode, e);
    }
}

// Sort provinces alphabetically by name
allProvinces.sort((a,b) => a.name.localeCompare(b.name));

allProvinces.forEach(p => {
    const opt = document.createElement("option");
    opt.value = p.name;        // Save province NAME in DB
    opt.textContent = p.name;
    opt.dataset.code = p.code; // PSGC code for loading cities
    provinceSelect.appendChild(opt);
});

// ✅ now enable province select (if we have items)
provinceSelect.disabled = allProvinces.length === 0 ? true : false;

}

// ============================
//  Load cities for normal provinces
// ============================
async function loadCitiesFromProvince() {
    resetSelect(citySelect, "Select City / Municipality");
    resetSelect(barangaySelect, "Select Barangay");

    const selected = provinceSelect.selectedOptions[0];
    if (!selected || !selected.dataset.code) {
        citySelect.disabled     = true;
        barangaySelect.disabled = true;
        return;
    }

    const provinceCode = selected.dataset.code;

    try {
        const res = await fetch(`${PSGC_API}/provinces/${provinceCode}/cities-municipalities/`);
        if (!res.ok) throw new Error("HTTP " + res.status);
        const cities = await res.json();

        cities.sort((a,b) => a.name.localeCompare(b.name));

        cities.forEach(c => {
            const opt = document.createElement("option");
            opt.value = c.name;        // save city NAME in DB
            opt.textContent = c.name;
            opt.dataset.code = c.code; // for barangay loading
            citySelect.appendChild(opt);
        });

        citySelect.disabled     = true ? cities.length === 0 : false;
        barangaySelect.disabled = true;
    } catch (e) {
        console.error("Error loading cities:", e);
    }
}

// ============================
//  Load cities for NCR (Metro Manila)
// ============================
async function loadCitiesForNCR() {
    resetSelect(citySelect, "Select City / Municipality");
    resetSelect(barangaySelect, "Select Barangay");

    try {
        const res = await fetch(`${PSGC_API}/regions/130000000/cities-municipalities/`);
        if (!res.ok) throw new Error("HTTP " + res.status);
        const cities = await res.json();

        cities.sort((a,b) => a.name.localeCompare(b.name));

        cities.forEach(c => {
            const opt = document.createElement("option");
            opt.value = c.name;        // city in DB
            opt.textContent = c.name;
            opt.dataset.code = c.code; // for barangays
            citySelect.appendChild(opt);
        });

        citySelect.disabled     = cities.length === 0;
        barangaySelect.disabled = true;
    } catch (e) {
        console.error("Error loading NCR cities:", e);
    }
}

// ============================
//  Load barangays by city
// ============================
async function loadBarangaysFromCity() {
    resetSelect(barangaySelect, "Select Barangay");

    const selected = citySelect.selectedOptions[0];
    if (!selected || !selected.dataset.code) {
        barangaySelect.disabled = true;
        return;
    }

    const cityCode = selected.dataset.code;

    try {
        const res = await fetch(`${PSGC_API}/cities-municipalities/${cityCode}/barangays/`);
        if (!res.ok) throw new Error("HTTP " + res.status);
        const barangays = await res.json();

        barangays.sort((a,b) => a.name.localeCompare(b.name));

        barangays.forEach(b => {
            const opt = document.createElement("option");
            opt.value = b.name;   // barangay NAME in DB
            opt.textContent = b.name;
            barangaySelect.appendChild(opt);
        });

        barangaySelect.disabled = barangays.length === 0;
    } catch (e) {
        console.error("Error loading barangays:", e);
    }
}

// ============================
//  Wire events
// ============================
document.addEventListener("DOMContentLoaded", function () {
    if (!regionSelect) return;

    // initial state
    provinceSelect.disabled = true;
    citySelect.disabled     = true;
    barangaySelect.disabled = true;

    regionSelect.addEventListener("change", function () {
        handleAppRegionChange(this.value);
    });

    provinceSelect.addEventListener("change", function () {
        loadCitiesFromProvince();
    });

    citySelect.addEventListener("change", function () {
        loadBarangaysFromCity();
    });
});
</script>

    </body>

    </html>