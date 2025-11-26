<?php

require_once './app/Helpers/UploadSecurity.php';

class ColorAnalysisController {
    
    public function process() {
      

        // 1. Check Authentication
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

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
            
            if (empty($_FILES['face_image']['tmp_name'])) {
                $_SESSION['errorMessage'] = "Please upload a face image.";
                header("Location: index.php?page=features#features");
                exit;
            }

            // 🔐 Secure upload validation (type/size/virus)
            try {
                $mime = UploadSecurity::validateImageAndGetMime('face_image', 5_000_000); // 5 MB
            } catch (RuntimeException $e) {
                $_SESSION['errorMessage'] = $e->getMessage();
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
                $_SESSION['errorMessage'] = "Server Connection Error: " . curl_error($ch);
                curl_close($ch);
               header("Location: index.php?page=features#features");

                exit;
            }
            curl_close($ch);

            // 4. Process Response
            $result = json_decode($response, true);

            if ($httpCode === 200 && isset($result['status']) && $result['status'] === 'success') {
                $seasonName = $result['season'];
                $palette    = $result['palette']; 
                
                // Get ID from mapping
                $seasonId = isset($seasonMapping[$seasonName]) ? $seasonMapping[$seasonName] : null;

                if ($seasonId) {
                    $db = new Database();
                    $conn = $db->connect(); 

                    // Use 'season_id' (lowercase)
                    $query = "UPDATE users SET season_id = ? WHERE USER_ID = ?";
                    
                    $stmt = $conn->prepare($query);
                    
                    if ($stmt) {
                        $stmt->bind_param("ii", $seasonId, $_SESSION['user_id']);
                        
                        if ($stmt->execute()) {
                            $_SESSION['colorAnalysisResult'] = [
                                'season'  => $seasonName,
                                'palette' => $palette
                            ];
                            $_SESSION['successMessage']    = "Success! You are a " . $seasonName;
                            $_SESSION['show_color_modal']  = true;   // 👈 NEW: trigger color modal one time
                        } else {
                            $_SESSION['errorMessage'] = "Database Save Error: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['errorMessage'] = "Database Prepare Error: " . $conn->error;
                    }
                } else {
                     $_SESSION['errorMessage'] = "Predicted season '$seasonName' ID not found.";
                }
            } else {
                $errorMsg = isset($result['message']) ? $result['message'] : "Analysis failed.";
                $_SESSION['errorMessage'] = "Error: " . $errorMsg;
            }

            header("Location: index.php?page=features#features");
            exit;
        }
    }
}
?>
