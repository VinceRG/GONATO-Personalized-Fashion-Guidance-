<?php

require_once './app/Helpers/UploadSecurity.php';
require_once './app/Helpers/Csrf.php'; 

class BodyShapeController {

    public function process() {
        // Make sure session is active (defensive, in case index.php changes later)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Check Authentication
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        // Always clear previous flash state so this run is authoritative
        $_SESSION['successMessage']  = '';
        $_SESSION['errorMessage']    = '';
        $_SESSION['show_body_modal'] = false;

        require_once './app/Core/Database.php';

        // 2. Define Body Shape ID Mapping (based on your DB)
        $shapeMapping = [
            'Hourglass'         => 1,
            'Rectangle'         => 2,
            'Pear'              => 3,
            'Inverted Triangle' => 4
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 🔐 2.1 CSRF VALIDATION – put this BEFORE reading other POST fields
            if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
                $_SESSION['errorMessage']    = "Security check failed. Please try again.";
                $_SESSION['show_body_modal'] = false;
                header("Location: index.php?page=features#features");
                exit;
            }

            $height = $_POST['height_cm'] ?? null;

            // Basic height presence check
            if ($height === null || $height === '') {
                $_SESSION['errorMessage']    = "Please provide your height.";
                $_SESSION['show_body_modal'] = false;
                header("Location: index.php?page=features#features");
                exit;
            }

            // Validate files exist
            if (empty($_FILES['front_image']['tmp_name']) || empty($_FILES['side_image']['tmp_name'])) {
                $_SESSION['errorMessage']    = "Please upload both front and side images.";
                $_SESSION['show_body_modal'] = false;
                header("Location: index.php?page=features#features");
                exit;
            }

            // 🔐 Secure upload validation (type/size/virus)
            try {
                $frontMime = UploadSecurity::validateImageAndGetMime('front_image', 5_000_000); // 5 MB
                $sideMime  = UploadSecurity::validateImageAndGetMime('side_image',  5_000_000);
            } catch (RuntimeException $e) {
                $_SESSION['errorMessage']    = $e->getMessage();
                $_SESSION['show_body_modal'] = false;
                header("Location: index.php?page=features#features");
                exit;
            }

            // Prepare files for cURL using trusted MIME types
            $frontFile = new CURLFile(
                $_FILES['front_image']['tmp_name'],
                $frontMime,
                'front_image'
            );
            $sideFile = new CURLFile(
                $_FILES['side_image']['tmp_name'],
                $sideMime,
                'side_image'
            );

            // 3. Send to Flask API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/analyze");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'front_image' => $frontFile,
                'side_image'  => $sideFile,
                'height_cm'   => $height
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $_SESSION['errorMessage']    = "Server Connection Error. Is the Python app.py running?";
                $_SESSION['show_body_modal'] = false;
                curl_close($ch);
                header("Location: index.php?page=features#features");
                exit;
            }
            curl_close($ch);

            // 4. Process Response
            $result = json_decode($response, true);

            if ($httpCode === 200 && isset($result['status']) && $result['status'] === 'success') {
                $bodyShapeName = $result['body_shape'] ?? null;
                $measurements  = $result['measurements'] ?? [];

                $bodyShapeId = isset($shapeMapping[$bodyShapeName]) ? $shapeMapping[$bodyShapeName] : null;

                if ($bodyShapeId) {
                    $db   = new Database();
                    $conn = $db->connect();

                    $query = "UPDATE users SET BODY_SHAPE_ID = ? WHERE USER_ID = ?";

                    $stmt = $conn->prepare($query);

                    if ($stmt) {
                        $stmt->bind_param("ii", $bodyShapeId, $_SESSION['user_id']);

                        if ($stmt->execute()) {
                            $_SESSION['bodyShapeResult'] = [
                                'prediction'   => ['body_shape' => $bodyShapeName],
                                'measurements' => $measurements
                            ];

                            $_SESSION['successMessage']  = "Body shape analyzed successfully: " . $bodyShapeName;
                            $_SESSION['errorMessage']    = '';
                            $_SESSION['show_body_modal'] = true;  // 🔥 tells the view to open success modal
                        } else {
                            $_SESSION['errorMessage']    = "Database Error: " . $stmt->error;
                            $_SESSION['successMessage']  = '';
                            $_SESSION['show_body_modal'] = false;
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['errorMessage']    = "Database Prepare Error: " . $conn->error;
                        $_SESSION['successMessage']  = '';
                        $_SESSION['show_body_modal'] = false;
                    }
                } else {
                    $_SESSION['errorMessage']    = "Predicted shape '$bodyShapeName' is not valid in the database.";
                    $_SESSION['successMessage']  = '';
                    $_SESSION['show_body_modal'] = false;
                }
            } else {
                $errorMsg = isset($result['message']) ? $result['message'] : "Failed to connect to analysis server.";
                $_SESSION['errorMessage']    = "Analysis Failed: " . $errorMsg;
                $_SESSION['successMessage']  = '';
                $_SESSION['show_body_modal'] = false;
            }

            // Redirect back to features page
            header("Location: index.php?page=features#features");
            exit;
        }
    }
}
?>
