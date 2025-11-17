<?php
session_start();
require_once __DIR__ . '/../Model/features.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit();
}

$user_id = $_SESSION['user_id'];
$upload_dir = __DIR__ . '/../uploads/body_shape/';

// Ensure upload directory exists
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$front_image = $_FILES['front_image'] ?? null;
$side_image = $_FILES['side_image'] ?? null;
$height_cm = $_POST['height_cm'] ?? null;

if (!$front_image || !$side_image || !$height_cm) {
    $_SESSION['errorMessage'] = "Please upload both images and enter height.";
    header("Location: index.php?page=features");
    exit();
}

// Save files
$front_path = $upload_dir . uniqid('front_') . "_" . basename($front_image['name']);
$side_path  = $upload_dir . uniqid('side_') . "_" . basename($side_image['name']);

move_uploaded_file($front_image['tmp_name'], $front_path);
move_uploaded_file($side_image['tmp_name'], $side_path);

// Call Python script
$height_cm = floatval($height_cm);
$command = escapeshellcmd("python3 ../ml/body_shape_analysis.py '$front_path' '$side_path' $height_cm");
$output = shell_exec($command);

// Decode JSON output
$result = json_decode($output, true);

if (!$result || $result['status'] !== 'success') {
    $_SESSION['errorMessage'] = $result['message'] ?? "Body shape analysis failed.";
} else {
    $body_shape = $result['prediction']['body_shape'];
    $measurements = json_encode($result['measurements']);

    // Save body shape to database
    updateUserBodyShape($user_id, $body_shape);

    $_SESSION['successMessage'] = "Body shape analyzed: $body_shape";
    $_SESSION['bodyShapeResult'] = $result;
}

header("Location: index.php?page=features");
exit();
