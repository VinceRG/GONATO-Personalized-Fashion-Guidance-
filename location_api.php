<?php
// location_api.php
// Simple proxy to Nominatim (OpenStreetMap) so the browser avoids CORS/rate-limits.

header('Content-Type: application/json; charset=utf-8');

// Allow only GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$action = $_GET['action'] ?? 'search';

try {
    if ($action === 'search') {
        $query = trim($_GET['q'] ?? '');
        if ($query === '') {
            echo json_encode([]);
            exit;
        }

        $params = [
            'q'              => $query,
            'format'         => 'json',
            'addressdetails' => 1,
            'limit'          => 1,
            'countrycodes'   => 'ph'
        ];

        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query($params);
    } elseif ($action === 'reverse') {
        $lat = $_GET['lat'] ?? null;
        $lon = $_GET['lon'] ?? null;

        if ($lat === null || $lon === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing lat/lon']);
            exit;
        }

        $params = [
            'lat'            => $lat,
            'lon'            => $lon,
            'format'         => 'json',
            'addressdetails' => 1
        ];

        $url = 'https://nominatim.openstreetmap.org/reverse?' . http_build_query($params);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        exit;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Nominatim REQUIRES a valid User-Agent
    curl_setopt($ch, CURLOPT_USERAGENT, 'AmarelleApp/1.0 (amarelle2025@gmail.com)');

    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false || $code !== 200) {
        error_log("Nominatim error: HTTP $code | $err | URL: $url");
        http_response_code(500);
        echo json_encode(['error' => 'Nominatim request failed']);
        exit;
    }

    // Just relay Nominatim response
    echo $body;
} catch (Throwable $e) {
    error_log('location_api.php exception: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
    exit;
}
