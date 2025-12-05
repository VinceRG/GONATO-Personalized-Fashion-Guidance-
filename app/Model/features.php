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

$db   = new Database();
$conn = $db->connect();

$userId = $_SESSION['user_id'];

// ---------------------------------------------------------------------
// 1) HANDLE PROFILE UPDATE (including profile image upload)
// ---------------------------------------------------------------------
$successMessage = '';
$errorMessage   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    // Basic sanitization of text fields
    $firstName = trim($_POST['first_name']     ?? '');
    $lastName  = trim($_POST['last_name']      ?? '');
    $username  = trim($_POST['username']       ?? '');
    $email     = trim($_POST['email']          ?? '');
    $address   = trim($_POST['address']        ?? ''); // extra / combined address (optional)
    $contacts  = trim($_POST['contacts']       ?? '');

    // Detailed address fields
    $street    = trim($_POST['street_address'] ?? '');
    $apartment = trim($_POST['apartment']      ?? '');
    $region    = trim($_POST['region']         ?? '');
    $province  = trim($_POST['province']       ?? '');
    $city      = trim($_POST['city']           ?? '');
    $barangay  = trim($_POST['barangay']       ?? '');
    $postal    = trim($_POST['postal_code']    ?? '');

    // Simple validation (expand as needed)
    if ($firstName === '' || $lastName === '' || $username === '' || $email === '') {
        $errorMessage = "Please fill in all required fields.";
    } else {
        // Handle profile image if a new file was uploaded
        $newImageFileName = null;

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileInfo = $_FILES['profile_image'];

            // Allowed extensions
            $ext     = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed, true)) {
                $errorMessage = "Invalid image type. Please upload JPG, PNG, or WEBP.";
            } else {
                // Adjust path if your uploads folder is elsewhere
                $uploadDir = __DIR__ . '/../../uploads/profile_images/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                $newImageFileName = 'user_' . $userId . '_' . time() . '.' . $ext;
                $targetPath       = $uploadDir . $newImageFileName;

                if (!move_uploaded_file($fileInfo['tmp_name'], $targetPath)) {
                    $errorMessage     = "Failed to upload profile image.";
                    $newImageFileName = null; // don't update DB image
                }
            }
        }

        if ($errorMessage === '') {
            // ---------------------------------------------------------
            // Build UPDATE query (with or without new image)
            // ---------------------------------------------------------
            if ($newImageFileName) {
                // WITH PROFILE_IMAGE
                $sql = "UPDATE users 
                        SET FIRST_NAME     = ?, 
                            LAST_NAME      = ?, 
                            USERNAME       = ?, 
                            EMAIL          = ?, 
                            CONTACTS       = ?, 
                            REGION         = ?, 
                            STREET_ADDRESS = ?, 
                            APARTMENT      = ?, 
                            PROVINCE       = ?, 
                            CITY           = ?, 
                            BARANGAY       = ?, 
                            POSTAL_CODE    = ?, 
                            ADDRESS        = ?, 
                            PROFILE_IMAGE  = ?
                        WHERE USER_ID = ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "ssssssssssssssi",   // 14 strings + 1 int
                    $firstName,          // 1
                    $lastName,           // 2
                    $username,           // 3
                    $email,              // 4
                    $contacts,           // 5
                    $region,             // 6
                    $street,             // 7
                    $apartment,          // 8
                    $province,           // 9
                    $city,               // 10
                    $barangay,           // 11
                    $postal,             // 12
                    $address,            // 13
                    $newImageFileName,   // 14
                    $userId              // 15 (int)
                );
            } else {
                // WITHOUT PROFILE_IMAGE
                $sql = "UPDATE users 
                        SET FIRST_NAME     = ?, 
                            LAST_NAME      = ?, 
                            USERNAME       = ?, 
                            EMAIL          = ?, 
                            CONTACTS       = ?, 
                            REGION         = ?, 
                            STREET_ADDRESS = ?, 
                            APARTMENT      = ?, 
                            PROVINCE       = ?, 
                            CITY           = ?, 
                            BARANGAY       = ?, 
                            POSTAL_CODE    = ?, 
                            ADDRESS        = ?
                        WHERE USER_ID = ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "sssssssssssssi",   // 13 strings + 1 int
                    $firstName,        // 1
                    $lastName,         // 2
                    $username,         // 3
                    $email,            // 4
                    $contacts,         // 5
                    $region,           // 6
                    $street,           // 7
                    $apartment,        // 8
                    $province,         // 9
                    $city,             // 10
                    $barangay,         // 11
                    $postal,           // 12
                    $address,          // 13
                    $userId            // 14 (int)
                );
            }

            if ($stmt->execute()) {
                $successMessage = "Profile updated successfully.";
            } else {
                $errorMessage = "Failed to update profile: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // -----------------------------------------------------------------
    // Flash messages into session so the view can read them
    // -----------------------------------------------------------------
    if ($successMessage !== '') {
        $_SESSION['successMessage'] = $successMessage;
    }
    if ($errorMessage !== '') {
        $_SESSION['errorMessage'] = $errorMessage;
    }

    // Keep user account modal open after POST + reload
    $_SESSION['keep_profile_open'] = true;
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
$user   = $result->fetch_assoc();
$stmt->close();

// Calculate user initials for profile picture fallback
$userInitials = strtoupper(
    substr($user['FIRST_NAME'] ?? '', 0, 1) .
    substr($user['LAST_NAME']  ?? '', 0, 1)
);

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

// ---------------------------------------------------------------------
// 5) ORDERS / PURCHASES DATA FOR PROFILE MODAL
// ---------------------------------------------------------------------
$ordersByTab = [
    'orders'     => [],  // "Orders" sub-tab
    'to_receive' => [],  // "To Receive" sub-tab
    'history'    => [],  // "Order History" sub-tab
];

$orderItems = []; // [ORDER_ID => [items...]]

// 1) Get all orders of this user
$orderSql = "SELECT * FROM orders WHERE USER_ID = ? ORDER BY ORDER_DATE DESC";
$stmt = $conn->prepare($orderSql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$orderResult = $stmt->get_result();

while ($row = $orderResult->fetch_assoc()) {
    $status = strtolower($row['STATUS']);

    // Decide which sub-tab the order belongs to
    if (in_array($status, ['pending', 'processing', 'paid'], true)) {
        $bucket = 'orders';          // Active orders
    } elseif (in_array($status, ['shipped', 'out_for_delivery'], true)) {
        $bucket = 'to_receive';      // On the way
    } else {
        // delivered, completed, cancelled, etc.
        $bucket = 'history';
    }

    $ordersByTab[$bucket][] = $row;
}
$stmt->close();

// 2) For each order, load its items (simple N+1 approach)
$itemSql = "SELECT 
                oi.*, 
                p.PRODUCT_NAME, 
                c.COLOR_VALUE
            FROM order_items oi
            JOIN products p ON p.PRODUCT_ID = oi.PRODUCT_ID
            LEFT JOIN colors c ON c.COLOR_ID = oi.COLOR_ID
            WHERE oi.ORDER_ID = ?";

$stmtItem = $conn->prepare($itemSql);

foreach (['orders', 'to_receive', 'history'] as $tabKey) {
    foreach ($ordersByTab[$tabKey] as $order) {
        $orderId = (int)$order['ORDER_ID'];

        $stmtItem->bind_param("i", $orderId);
        $stmtItem->execute();
        $itemsResult = $stmtItem->get_result();

        while ($item = $itemsResult->fetch_assoc()) {
            $orderItems[$orderId][] = $item;
        }
    }
}

$stmtItem->close();
