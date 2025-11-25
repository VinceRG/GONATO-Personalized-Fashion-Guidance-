<?php
// app/Model/features.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

require_once __DIR__ . '/../Core/Database.php';

$db  = new Database();
$conn = $db->connect();      // correct usage


$userId = $_SESSION['user_id'];

// ---------------------------------------------------------------------
// 1) HANDLE PROFILE UPDATE (including profile image upload)
// ---------------------------------------------------------------------
$successMessage = $successMessage ?? '';
$errorMessage   = $errorMessage ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    // Basic sanitization of text fields
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $address   = trim($_POST['address'] ?? '');
    $contacts  = trim($_POST['contacts'] ?? '');
    $street    = trim($_POST['street_address'] ?? '');
$apartment = trim($_POST['apartment'] ?? '');
$province  = trim($_POST['province'] ?? '');
$city      = trim($_POST['city'] ?? '');
$barangay  = trim($_POST['barangay'] ?? '');
$postal    = trim($_POST['postal_code'] ?? '');
$contacts  = trim($_POST['contacts'] ?? '');


    // Simple validation (you can expand this)
    if ($firstName === '' || $lastName === '' || $username === '' || $email === '') {
        $errorMessage = "Please fill in all required fields.";
    } else {
        // Handle profile image if a new file was uploaded
        $newImageFileName = null;

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileInfo = $_FILES['profile_image'];

            // Allowed extensions
            $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];

            if (!in_array($ext, $allowed)) {
                $errorMessage = "Invalid image type. Please upload JPG, PNG, or WEBP.";
            } else {
                // Adjust path if your uploads folder is elsewhere
                $uploadDir = __DIR__ . '/../../uploads/profile_images/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                $newImageFileName = 'user_' . $userId . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $newImageFileName;

                if (!move_uploaded_file($fileInfo['tmp_name'], $targetPath)) {
                    $errorMessage = "Failed to upload profile image.";
                    $newImageFileName = null; // don't update DB image
                }
            }
        }

        if ($errorMessage === '') {
            // Build UPDATE query (with or without new image)
            if ($newImageFileName) {
                $sql = "UPDATE users 
                        SET FIRST_NAME = ?, LAST_NAME = ?, USERNAME = ?, EMAIL = ?, ADDRESS = ?, CONTACTS = ?, PROFILE_IMAGE = ?
                        WHERE USER_ID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "sssssssi",
                    $firstName,
                    $lastName,
                    $username,
                    $email,
                    $address,
                    $contacts,
                    $newImageFileName,
                    $userId
                );
            } else {
                $sql = "UPDATE users 
                        SET FIRST_NAME = ?, LAST_NAME = ?, USERNAME = ?, EMAIL = ?, ADDRESS = ?, CONTACTS = ?
                        WHERE USER_ID = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "ssssssi",
                    $firstName,
                    $lastName,
                    $username,
                    $email,
                    $address,
                    $contacts,
                    $userId
                );
            }

            if ($stmt->execute()) {
                $successMessage = "Profile updated successfully.";
            } else {
                $errorMessage = "Failed to update profile. Please try again.";
            }
            $stmt->close();
        }
    }
}

// ---------------------------------------------------------------------
// 2) FETCH USER DATA (after any update, so we see latest values)
// ---------------------------------------------------------------------
$query = "SELECT u.*, 
          bs.BODY_TYPE, 
          bs.BODY_SHAPE_ID as USER_BODY_SHAPE_ID,
          s.SEASON_TYPE 
          FROM users u 
          LEFT JOIN body_shapes bs ON u.BODY_SHAPE_ID = bs.BODY_SHAPE_ID 
          LEFT JOIN seasons s ON u.season_id = s.SEASON_ID
          WHERE u.USER_ID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Calculate user initials for profile picture fallback
$userInitials = strtoupper(substr($user['FIRST_NAME'], 0, 1) . substr($user['LAST_NAME'], 0, 1));

// Handle profile image path (for <img src="...">)
$profileImagePath = !empty($user['PROFILE_IMAGE']) 
    ? "uploads/profile_images/" . $user['PROFILE_IMAGE'] 
    : "";

// ---------------------------------------------------------------------
// 3) BODY SHAPE SESSION SYNC
// ---------------------------------------------------------------------
$hasSessionResult = isset($_SESSION['bodyShapeResult']);
$hasDbResult      = !empty($user['BODY_TYPE']);

if ($hasDbResult && !$hasSessionResult) {
    $_SESSION['bodyShapeResult'] = [
        'prediction'   => ['body_shape' => $user['BODY_TYPE']],
        'measurements' => []
    ];
}

// ---------------------------------------------------------------------
// 4) COLOR SEASON SESSION SYNC
// ---------------------------------------------------------------------
$hasColorSession = isset($_SESSION['colorAnalysisResult']);
$hasColorDb      = !empty($user['SEASON_TYPE']);

if ($hasColorDb && !$hasColorSession) {
    $_SESSION['colorAnalysisResult'] = [
        'season'  => $user['SEASON_TYPE'],
        'palette' => []
    ];
}

?>
