<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap">
    <title>Login - Amarelle</title>
    <link rel="stylesheet" href="public/login.css">
</head>

<body>
    <div class="header">
        <a href="index.php?page=landing" class="logo-link">
            <div class="logo">Amarelle</div>
        </a>
    </div>

    <div class="content">
        <h1>Sign In</h1>

        <?php if (!empty($message)): ?>
            <p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form id="loginForm" action="index.php?page=login" method="POST">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" placeholder="Enter your username or email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Login</button>

            <p>
                Don't have an account yet?
                <a href="index.php?page=register">Sign Up here</a>
            </p>
        </form>
    </div>

    <div class="footer">
        <p>© 2025 Amarelle. All rights reserved.</p>
    </div>
</body>
</html>
