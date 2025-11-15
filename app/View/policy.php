<?php
$terms = "
<h2>Terms and Conditions</h2>
<p>Welcome to our website. By using our services, you agree to the following terms:</p>
<ul>
<li>You must be at least 18 years old to create an account.</li>
<li>All personal information provided must be accurate and up-to-date.</li>
<li>You are responsible for maintaining the confidentiality of your account credentials.</li>
<li>Any misuse of our services may result in suspension or termination of your account.</li>
<li>We reserve the right to modify these terms at any time. Continued use constitutes acceptance of changes.</li>
</ul>
";

$privacy = "
<h2>Privacy Policy</h2>
<p>Your privacy is important to us. This privacy policy outlines how we handle your personal information:</p>
<ul>
<li>We collect personal information such as name, email, and contact number for account creation and service delivery.</li>
<li>All data is securely stored and protected against unauthorized access.</li>
<li>We do not share your personal information with third parties without your consent, except as required by law.</li>
<li>You may request access, correction, or deletion of your personal information at any time.</li>
<li>By using our website, you consent to the collection and use of information as described in this policy.</li>
</ul>
";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Terms & Privacy - Amarelle</title>
    <link rel="stylesheet" href="public/css/policy.css">
</head>
<body>
    <div class="content">
        <form>
            <h1>Our Policies</h1>

            <div class="terms-text" style="max-height: 350px; overflow-y: auto; padding-right: 10px;">
                <?php echo $terms; ?>
                <hr style="margin:20px 0;">
                <?php echo $privacy; ?>
            </div>

            <p style="text-align:center; margin-top:2rem;">
                <a href="register.php" class="back-link">Back to Sign Up</a>
            </p>
        </form>
    </div>
</body>
</html>
