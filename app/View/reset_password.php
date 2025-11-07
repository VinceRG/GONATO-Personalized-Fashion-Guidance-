<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Amarelle</title>
    <link rel="stylesheet" href="public/css/login.css">
</head>
<body>
    <div class="content">
        <h1>Reset Password</h1>

        <form method="POST" action="index.php?page=forgot">
            
            <label>New Password</label>
            <input type="password" name="new_password" placeholder="Enter new password" required>

            <button type="submit">Update Password</button>

        </form>

        <p><a href="index.php?page=login">Back to login</a></p>
    </div>
</body>
</html>
