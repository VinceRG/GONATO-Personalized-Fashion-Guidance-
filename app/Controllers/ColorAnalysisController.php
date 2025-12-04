<?php

require_once './app/Helpers/UploadSecurity.php';
require_once './app/Helpers/Csrf.php';

class ColorAnalysisController {

    public function process() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Check Authentication
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        // Always clear previous color-analysis flash state
        $_SESSION['successMessage']    = '';
        $_SESSION['errorMessage']      = '';
        $_SESSION['show_color_modal']  = false;

        require_once './app/Core/Database.php';

        // 2. MAPPING (Based on your Database)
        // 1=Autumn, 2=Summer, 3=Winter, 4=Spring
        $seasonMapping = [
            'Autumn' => 1,
            'Summer' => 2,
            'Winter' => 3,
            'Spring' => 4
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 🔐 CSRF validation FIRST
            if (!Csrf::validate($_POST['csrf_token'] ?? null)) {
                $_SESSION['errorMessage']     = "Security check failed. Please try again.";
                $_SESSION['show_color_modal'] = false;
                header("Location: index.php?page=features#features");
                exit;
            }

            if (empty($_FILES['face_image']['tmp_name'])) {
                $_SESSION['errorMessage']     = "Please upload a face image.";
                $_SESSION['show_color_modal'] = false;
                header("Location: index.php?page=features#features");
                exit;
            }

            // 🔐 Secure upload validation (type/size/virus)
            try {
                $mime = UploadSecurity::validateImageAndGetMime('face_image', 5_000_000); // 5 MB
            } catch (RuntimeException $e) {
                $_SESSION['errorMessage']     = $e->getMessage();
                $_SESSION['show_color_modal'] = false;
                header("Location: index.php?page=features#features");
                exit;
            }

            // Use the trusted MIME from validation instead of $_FILES['...']['type']
            $faceFile = new CURLFile(
                $_FILES['face_image']['tmp_name'],
                $mime,
                'face_image'
            );

            // 3. Send to Flask API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/analyze_color");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'face_image' => $faceFile
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                $_SESSION['errorMessage']     = "Server Connection Error: " . curl_error($ch);
                $_SESSION['show_color_modal'] = false;
                curl_close($ch);
                header("Location: index.php?page=features#features");
                exit;
            }
            curl_close($ch);

            // 4. Process Response
            $result = json_decode($response, true);

            if ($httpCode === 200 && isset($result['status']) && $result['status'] === 'success') {
                $seasonName = $result['season']   ?? null;
                $palette    = $result['palette']  ?? [];

                // Get ID from mapping
                $seasonId = isset($seasonMapping[$seasonName]) ? $seasonMapping[$seasonName] : null;

                if ($seasonId) {
                    $db   = new Database();
                    $conn = $db->connect();

                    // ⚠️ Make sure column name matches your table: SEASON_ID
                    $query = "UPDATE users SET SEASON_ID = ? WHERE USER_ID = ?";

                    $stmt = $conn->prepare($query);

                    if ($stmt) {
                        $stmt->bind_param("ii", $seasonId, $_SESSION['user_id']);

                        if ($stmt->execute()) {
                            $_SESSION['colorAnalysisResult'] = [
                                'season'  => $seasonName,
                                'palette' => $palette
                            ];

                            $_SESSION['successMessage']     = "Success! You are a " . $seasonName;
                            $_SESSION['errorMessage']       = '';
                            $_SESSION['show_color_modal']   = true;  // 🔥 tells view to open success modal
                        } else {
                            $_SESSION['errorMessage']       = "Database Save Error: " . $stmt->error;
                            $_SESSION['successMessage']     = '';
                            $_SESSION['show_color_modal']   = false;
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['errorMessage']       = "Database Prepare Error: " . $conn->error;
                        $_SESSION['successMessage']     = '';
                        $_SESSION['show_color_modal']   = false;
                    }
                } else {
                    $_SESSION['errorMessage']       = "Predicted season '$seasonName' ID not found.";
                    $_SESSION['successMessage']     = '';
                    $_SESSION['show_color_modal']   = false;
                }
            } else {
                $errorMsg = isset($result['message']) ? $result['message'] : "Analysis failed.";
                $_SESSION['errorMessage']       = "Error: " . $errorMsg;
                $_SESSION['successMessage']     = '';
                $_SESSION['show_color_modal']   = false;
            }

            header("Location: index.php?page=features#features");
            exit;
        }
    }
}
?>
