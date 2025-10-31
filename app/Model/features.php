<?php
require_once __DIR__ . '/../Core/Database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}

$conn = Database::connect();
$user_id = $_SESSION['user_id'];

// ✅ Handle Profile Update BEFORE fetching user data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $contacts = trim($_POST['contacts']);

    // Fetch current user data to get existing profile image
    $currentStmt = $conn->prepare("SELECT PROFILE_IMAGE FROM users WHERE USER_ID = ?");
    $currentStmt->bind_param("i", $user_id);
    $currentStmt->execute();
    $currentUser = $currentStmt->get_result()->fetch_assoc();
    $profile_image = $currentUser['PROFILE_IMAGE']; // default = keep existing

    // Check if new image uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['profile_image']['type'], $allowedTypes)) {
            $_SESSION['error_message'] = "Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.";
        } elseif ($_FILES['profile_image']['size'] > $maxFileSize) {
            $_SESSION['error_message'] = "File too large. Maximum size is 5MB.";
        } else {
            // ✅ FIXED: Correct path relative to Controllers folder
            // From: app/Controllers/features.php
            // To: app/uploads/profile_images/
            $targetDir = __DIR__ . "/../uploads/profile_images/";
            
            // Create upload directory if it doesn't exist
            if (!file_exists($targetDir)) {
                if (!mkdir($targetDir, 0777, true)) {
                    $_SESSION['error_message'] = "Failed to create upload directory.";
                    error_log("Failed to create directory: " . $targetDir);
                }
            }

            // Generate unique filename
            $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $targetFile = $targetDir . $fileName;

            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                // Delete old profile image if it exists and is not default
                if (!empty($currentUser['PROFILE_IMAGE']) && 
                    $currentUser['PROFILE_IMAGE'] !== 'default-avatar.png' &&
                    file_exists($targetDir . $currentUser['PROFILE_IMAGE'])) {
                    unlink($targetDir . $currentUser['PROFILE_IMAGE']);
                }
                $profile_image = $fileName;
                $_SESSION['success_message'] = "Profile picture updated successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to upload image. Please try again.";
                error_log("Failed to move uploaded file to: " . $targetFile);
            }
        }
    }

    // ✅ Update user data
    $update = $conn->prepare("
        UPDATE users 
        SET FIRST_NAME=?, LAST_NAME=?, USERNAME=?, EMAIL=?, ADDRESS=?, CONTACTS=?, PROFILE_IMAGE=? 
        WHERE USER_ID=?
    ");
    $update->bind_param("sssssssi", $first_name, $last_name, $username, $email, $address, $contacts, $profile_image, $user_id);
    
    if ($update->execute()) {
        if (!isset($_SESSION['success_message'])) {
            $_SESSION['success_message'] = "Profile updated successfully!";
        }
        // Redirect to system_features.php (main page)
        header("Location: index.php?page=features");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to update profile. Please try again.";
    }
}

// Fetch user info (after potential update)
$stmt = $conn->prepare("SELECT * FROM users WHERE USER_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Check for messages to display
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>