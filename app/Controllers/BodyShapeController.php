<?php

class BodyShapeController {
    
    public function process() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Check Authentication
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }

        // Include your Database Connection
        require_once './app/Core/Database.php';

        // 2. Define Body Shape ID Mapping (UPDATED based on your DB)
        // This maps the "String" from Python to the "ID" in your MySQL table
        $shapeMapping = [
            'Hourglass' => 1,
            'Rectangle' => 2,         // Corrected from your DB
            'Pear' => 3,              // Corrected from your DB
            'Inverted Triangle' => 4  // Corrected from your DB
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $height = $_POST['height_cm'];

            // Validate files exist
            if (empty($_FILES['front_image']['tmp_name']) || empty($_FILES['side_image']['tmp_name'])) {
                $_SESSION['errorMessage'] = "Please upload both front and side images.";
                header("Location: index.php?page=features");
                exit;
            }
            
            // Prepare files for cURL
            $frontFile = new CURLFile($_FILES['front_image']['tmp_name'], $_FILES['front_image']['type'], 'front_image');
            $sideFile = new CURLFile($_FILES['side_image']['tmp_name'], $_FILES['side_image']['type'], 'side_image');

            // 3. Send to Flask API
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/analyze");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'front_image' => $frontFile,
                'side_image' => $sideFile,
                'height_cm' => $height
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                $_SESSION['errorMessage'] = "Server Connection Error. Is the Python app.py running?";
                curl_close($ch);
                header("Location: index.php?page=features");
                exit;
            }
            curl_close($ch);

            // 4. Process Response
            $result = json_decode($response, true);

            if ($httpCode === 200 && isset($result['status']) && $result['status'] === 'success') {
                $bodyShapeName = $result['body_shape'];
                $measurements = $result['measurements'];
                
                // Get the Integer ID from our mapping
                // We use ucwords/strtolower to ensure case-insensitive matching (e.g. "rectangle" matches "Rectangle")
                // But your Python key usually sends Title Case.
                $bodyShapeId = isset($shapeMapping[$bodyShapeName]) ? $shapeMapping[$bodyShapeName] : null;

                if ($bodyShapeId) {
                    // 5. Update Database (MySQLi Syntax)
                    $db = new Database();
                    $conn = $db->connect(); 

                    // Query using '?' placeholders
                    $query = "UPDATE users SET BODY_SHAPE_ID = ? WHERE USER_ID = ?";
                    
                    $stmt = $conn->prepare($query);
                    
                    if ($stmt) {
                        // Bind parameters: "ii" means (Integer, Integer)
                        $stmt->bind_param("ii", $bodyShapeId, $_SESSION['user_id']);
                        
                        if ($stmt->execute()) {
                            $_SESSION['bodyShapeResult'] = [
                                'prediction' => ['body_shape' => $bodyShapeName],
                                'measurements' => $measurements
                            ];
                            $_SESSION['successMessage'] = "Body shape analyzed successfully: " . $bodyShapeName;
                        } else {
                            $_SESSION['errorMessage'] = "Database Error: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['errorMessage'] = "Database Prepare Error: " . $conn->error;
                    }
                } else {
                     $_SESSION['errorMessage'] = "Predicted shape '$bodyShapeName' is not valid in the database.";
                }
            } else {
                $errorMsg = isset($result['message']) ? $result['message'] : "Failed to connect to analysis server.";
                $_SESSION['errorMessage'] = "Analysis Failed: " . $errorMsg;
            }

            // Redirect back to features page
            header("Location: index.php?page=features#features");
            exit;
        }
    }
}
?>