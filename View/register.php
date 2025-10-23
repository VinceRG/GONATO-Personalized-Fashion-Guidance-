<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Amarelle</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap">
<link rel="stylesheet" href="public/login.css"></head>

<body>
    <div class="header">
        <a href="landing_page.html" class="logo-link">
            <div class="logo">Amarelle</div>
        </a>
    </div>

    <div class="content">
        <h1>Join Amarelle</h1>

        <?php if (!empty($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php elseif (!empty($success)): ?>
            <p style="color:green;"><?php echo $success; ?></p>
        <?php endif; ?>

        <form action="" method="POST" class="register-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" placeholder="Enter your first name" required>
                </div>

                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" placeholder="Enter your last name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" placeholder="Enter your address" required>
            </div>

            <label for="contact_num">Contact Number</label>
            <div class="contact-group">
                <span class="contact-prefix">+63</span>
                <input type="text" id="contact_num" name="contact_num" maxlength="10" placeholder="XXX XXX XXXX" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter your password" required>
                </div>
            </div>

            <button type="submit">Create Account</button>

            <p>
                Already have an account?
                <a href="login.php">Login here</a>
            </p>
        </form>
    </div>

    <div class="footer">
        <p>© 2025 Amarelle. All rights reserved.</p>
    </div>
</body>
</html>
