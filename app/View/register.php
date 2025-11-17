    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign Up - Amarelle</title>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap">
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

                <div class="form-step active" data-step="1">
                    <?php if (!empty($error)) : ?>
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

<div class="form-group">

<p class="terms-text" style = "justify-content: center;">
        <input type="checkbox" id="termsCheckbox" name="terms">
        <label for="termsCheckbox">
            By creating your account or signing in, you agree to our 
            <a id="termsLink">Terms and Conditions</a> &
            <a id="privacyLink">Privacy Policy</a>
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
            <h2>Terms and Conditions</h2>
            
            <p><strong>Last Updated:</strong> Nov 14, 2025</p>
            
            <p>Welcome to Amarelle! These terms and conditions ("Terms") outline the rules and regulations for the use of Amarelle's Website, located at amarelle2025.com.</p>
            
            <p>By accessing this website, we assume you accept these Terms and Conditions in full. Do not continue to use Amarelle if you do not agree to all of the terms and conditions stated on this page. Your access to and use of the Service is conditioned upon your acceptance of and compliance with these Terms.</p>

            <p>The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: "Client", "You" and "Your" refers to you, the person logging on this website and compliant to the Company’s terms and conditions. "The Company", "Ourselves", "We", "Our" and "Us", refers to our Company, Amarelle. "Party", "Parties", or "Us", refers to both the Client and ourselves.</p>

            <h3>Eligibility</h3>
            <p>You must be at least 18 years of age to use this website. By using this website and by agreeing to these Terms, you warrant and represent that you are at least 18 years of age.</p>

            <h3>Intellectual Property Rights</h3>
            <p>Unless otherwise stated, Amarelle and/or its licensors own the intellectual property rights for all material on Amarelle. All intellectual property rights are reserved. This material includes, but is not limited to, the design, layout, look, appearance, text, graphics, logos, icons, and software.</p>
            <p>You may view, download for caching purposes only, and print pages from the website for your own personal, non-commercial use, subject to the restrictions set out below and elsewhere in these Terms.</p>
            
            <p>You must not:</p>
            <ul>
                <li>Republish material from Amarelle in any other media.</li>
                <li>Sell, rent, or sub-license material from Amarelle.</li>
                <li>Reproduce, duplicate, copy, or otherwise exploit material on our website for a commercial purpose.</li>
                <li>Redistribute content from Amarelle (unless content is specifically made for redistribution).</li>
                <li>Modify or create derivative works based on the website's content.</li>
            </ul>

            <h3>Acceptable Use</h3>
            <p>You agree to use our website only for lawful purposes and in a way that does not infringe the rights of, restrict, or inhibit anyone else's use and enjoyment of the website.</p>
            <p>Prohibited behavior includes:</p>
            <ul>
                <li>Using the website in any way that causes, or may cause, damage to the website or impairment of the availability or accessibility of Amarelle.</li>
                <li>Using the website in any way which is unlawful, illegal, fraudulent, or harmful, or in connection with any unlawful, illegal, fraudulent, or harmful purpose or activity.</li>
                <li>Using the website to copy, store, host, transmit, send, use, publish, or distribute any material which consists of (or is linked to) any spyware, computer virus, Trojan horse, worm, keystroke logger, rootkit, or other malicious computer software.</li>
                <li>Conducting any systematic or automated data collection activities (including without limitation scraping, data mining, data extraction, and data harvesting) on or in relation to our website without our express written consent.</li>
                <li>Using the website to transmit or send unsolicited commercial communications.</li>
                <li>Accessing or attempting to access any parts of the site that you are not authorized to access.</li>
            </ul>

            <h3>User-Generated Content</h3>
            <p>In these Terms, "your user content" means material (including without limitation text, images, audio material, video material, and audio-visual material) that you submit to our website, for whatever purpose (e.g., comments, reviews, forum posts).</p>
            <p>You grant to Amarelle a worldwide, irrevocable, non-exclusive, royalty-free license to use, reproduce, adapt, publish, translate, and distribute your user content in any existing or future media. You also grant to Amarelle the right to sub-license these rights and the right to bring an action for infringement of these rights.</p>
            <p>Your user content must not be illegal or unlawful, must not infringe any third party's legal rights, and must not be capable of giving rise to legal action whether against you or Amarelle or a third party (in each case under any applicable law). You must not submit any user content to the website that is or has ever been the subject of any threatened or actual legal proceedings or other similar complaint.</p>
            <p>Amarelle reserves the right to edit or remove any material submitted to our website, or stored on our servers, or hosted or published upon our website, at our sole discretion and without notice.</p>

            <h3>Privacy</h3>
            <p>Your use of the website is also governed by our Privacy Policy. Please review our Privacy Policy, which is incorporated into these Terms by reference, to understand our practices regarding the collection and use of your personal information.</p>

            <h3>Disclaimer of Warranties</h3>
            <p>This website is provided "as is" and "as available" without any representations or warranties, express or implied. Amarelle makes no representations or warranties in relation to this website or the information and materials provided on this website.</p>
            <p>Without prejudice to the generality of the foregoing paragraph, Amarelle does not warrant that:</p>
            <ul>
                <li>This website will be constantly available, or available at all; or</li>
                <li>The information on this website is complete, true, accurate, or non-misleading.</li>
                <li>The website is free of viruses or other harmful components.</li>
            </ul>
            <p>Nothing on this website constitutes, or is meant to constitute, advice of any kind. If you require advice in relation to any (legal, financial, or medical) matter, you should consult an appropriate professional.</p>

            <h3>Limitation of Liability</h3>
            <p>TO THE FULLEST EXTENT PERMITTED BY APPLICABLE LAW, IN NO EVENT SHALL AMARELLE, NOR ITS DIRECTORS, EMPLOYEES, PARTNERS, AGENTS, SUPPLIERS, OR AFFILIATES, BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, INCLUDING WITHOUT LIMITATION, LOSS OF PROFITS, DATA, USE, GOODWILL, OR OTHER INTANGIBLE LOSSES, RESULTING FROM:</p>
            <ul>
                <li>(i) Your access to or use of or inability to access or use the website;</li>
                <li>(ii) Any conduct or content of any third party on the website;</li>
                <li>(iii) Any content obtained from the website; and</li>
                <li>(iv) Unauthorized access, use, or alteration of your transmissions or content,</li>
            </ul>
            <p>whether based on warranty, contract, tort (including negligence), or any other legal theory, whether or not we have been informed of the possibility of such damage, and even if a remedy set forth herein is found to have failed of its essential purpose.</p>

            <h3>Indemnification</h3>
            <p>You agree to defend, indemnify, and hold harmless Amarelle and its licensee and licensors, and their employees, contractors, agents, officers, and directors, from and against any and all claims, damages, obligations, losses, liabilities, costs or debt, and expenses (including but not limited to attorney's fees), resulting from or arising out of a) your use and access of the website, or b) a breach of these Terms.</p>

            <h3>Breaches of These Terms</h3>
            <p>Without prejudice to Amarelle's other rights under these Terms, if you breach these Terms in any way, Amarelle may take such action as it deems appropriate to deal with the breach, including suspending your access to the website, prohibiting you from accessing the website, blocking computers using your IP address from accessing the website, contacting your internet service provider to request that they block your access to the website and/or bringing court proceedings against you.</p>

            <h3>Variation of Terms</h3>
            <p>Amarelle reserves the right to revise these Terms at any time as it sees fit, and by using this website, you are expected to review these Terms on a regular basis to ensure you understand all terms and conditions governing the use of this website. Your continued use of the website after any such changes constitutes your acceptance of the new Terms.</p>

            <h3>Governing Law & Jurisdiction</h3>
            <p>These Terms will be governed by and construed in accordance with the laws of Philippines, without regard to its conflict of law provisions. You agree to submit to the personal and exclusive jurisdiction of the state and federal courts located within [Your County, State] to resolve any dispute or claim arising from these Terms.</p>

            <h3>Severability</h3>
            <p>If any provision of these Terms is found to be invalid or unenforceable under applicable law, such provision shall be deleted without affecting the remaining provisions herein, which shall continue in full force and effect.</p>

            <h3>Entire Agreement</h3>
            <p>These Terms, together with our Privacy Policy, constitute the entire agreement between you and Amarelle in relation to your use of this website and supersede all previous agreements in respect of your use of this website.</p>
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
    </body>

    </html>