<?php
//Model/features.php
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

// Helper function to get profile image path
function getProfileImagePath($user) {
    if (!empty($user['PROFILE_IMAGE'])) {
        $imagePath = "uploads/profile_images/" . $user['PROFILE_IMAGE'];
        
        if (file_exists($imagePath)) {
            return $imagePath;
        } else {
            $altPath = "../uploads/profile_images/" . $user['PROFILE_IMAGE'];
            if (file_exists($altPath)) {
                return $altPath;
            }
        }
    }
    return "assets/default-avatar.png";
}

// Helper function to get initials
function getInitials($user) {
    $first = !empty($user['FIRST_NAME']) ? substr($user['FIRST_NAME'], 0, 1) : '';
    $last = !empty($user['LAST_NAME']) ? substr($user['LAST_NAME'], 0, 1) : '';
    return strtoupper($first . $last);
}

// Handle Profile Update BEFORE fetching user data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $contacts = trim($_POST['contacts']);

    $currentStmt = $conn->prepare("SELECT PROFILE_IMAGE FROM users WHERE USER_ID = ?");
    $currentStmt->bind_param("i", $user_id);
    $currentStmt->execute();
    $currentUser = $currentStmt->get_result()->fetch_assoc();
    $profile_image = $currentUser['PROFILE_IMAGE']; 

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($_FILES['profile_image']['type'], $allowedTypes)) {
            $_SESSION['error_message'] = "Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.";
        } elseif ($_FILES['profile_image']['size'] > $maxFileSize) {
            $_SESSION['error_message'] = "File too large. Maximum size is 5MB.";
        } else {
            $uploadDir = "uploads/profile_images/";
            $targetDir = __DIR__ . "/../../" . $uploadDir; 
            
            if (!file_exists($targetDir)) {
                if (!mkdir($targetDir, 0755, true)) {
                    $_SESSION['error_message'] = "Failed to create upload directory.";
                    error_log("Failed to create directory: " . $targetDir);
                }
            }

            $fileExtension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
                if (!empty($currentUser['PROFILE_IMAGE']) && 
                    $currentUser['PROFILE_IMAGE'] !== 'default-avatar.png' &&
                    file_exists($targetDir . $currentUser['PROFILE_IMAGE'])) {
                    unlink($targetDir . $currentUser['PROFILE_IMAGE']);
                }
                $profile_image = $fileName;
                $_SESSION['success_message'] = "Profile picture updated successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to upload image. Please check permissions.";
                error_log("Failed to move uploaded file to: " . $targetFile);
            }
        }
    }

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
        header("Location: index.php?page=features");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to update profile. Please try again.";
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE USER_ID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Prepare variables for the view
$profileImagePath = getProfileImagePath($user);
$userInitials = getInitials($user);

// Debug logging
error_log("DEBUG - User PROFILE_IMAGE from DB: " . ($user['PROFILE_IMAGE'] ?? 'EMPTY'));
error_log("DEBUG - Profile Image Path: " . $profileImagePath);
error_log("DEBUG - File exists check: " . (file_exists($profileImagePath) ? 'YES' : 'NO'));

// Add cache-busting parameter
if (!empty($user['PROFILE_IMAGE']) && $user['PROFILE_IMAGE'] !== 'default-avatar.png') {
    $profileImagePath .= '?v=' . time();
}

// Get messages
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>