<?php
// Save this as analysis.php

// The URL where your Python Flask server (app.py) is running
define('PYTHON_API_URL', 'http://localhost:8001/seasonal_analysis');

// We will always return JSON
header('Content-Type: application/json');

// --- 1. Basic Validation ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'POST method is required.']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Image file is missing or invalid.']);
    exit;
}

// --- 2. Forward the File to the Python Server using cURL ---
try {
    $ch = curl_init();

    $file_path = $_FILES['image']['tmp_name'];
    $file_name = $_FILES['image']['name'];
    $file_mime = $_FILES['image']['type'];

    // Create a CURLFile object to send
    $cfile = new CURLFile($file_path, $file_mime, $file_name);
    $post_data = ['image' => $cfile];

    curl_setopt($ch, CURLOPT_URL, PYTHON_API_URL);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10-second connection timeout
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60-second total timeout (analysis can be slow)

    // Execute the request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);

    curl_close($ch);

    // --- 3. Handle the Response from the Python Server ---
    if ($response === false) {
        // cURL itself failed
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to connect to the Python analysis service.',
            'details' => $curl_error
        ]);
    } else {
        // cURL succeeded, so forward the Python server's response
        // This includes forwarding any errors from Python (e.g., "No face detected")
        http_response_code($http_code);
        echo $response; // $response is already a JSON string from Flask
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'An internal PHP server error occurred.',
        'details' => $e->getMessage()
    ]);
}
?>