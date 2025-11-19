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

// Include Database Connection
require_once __DIR__ . '/../Core/Database.php';

$db = new Database();
$conn = $db->connect();

// Fetch user data including body shape
$userId = $_SESSION['user_id'];
$query = "SELECT u.*, bs.BODY_TYPE, bs.BODY_SHAPE_ID as USER_BODY_SHAPE_ID
          FROM users u 
          LEFT JOIN body_shapes bs ON u.BODY_SHAPE_ID = bs.BODY_SHAPE_ID 
          WHERE u.USER_ID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Calculate user initials for profile picture fallback
$userInitials = strtoupper(substr($user['FIRST_NAME'], 0, 1) . substr($user['LAST_NAME'], 0, 1));

// Handle profile image path
$profileImagePath = !empty($user['PROFILE_IMAGE']) 
    ? "uploads/profile_images/" . $user['PROFILE_IMAGE'] 
    : "";

// Check if we have body shape data (either from session or database)
$hasSessionResult = isset($_SESSION['bodyShapeResult']);
$hasDbResult = !empty($user['BODY_TYPE']);

// If we have DB result but no session result, fetch the measurements from wherever you store them
// OR just use the body type from DB
if ($hasDbResult && !$hasSessionResult) {
    // You might want to store measurements in a separate table
    // For now, we'll just show the body type
    $_SESSION['bodyShapeResult'] = [
        'prediction' => ['body_shape' => $user['BODY_TYPE']],
        'measurements' => [
        ]
    ];
}

?>