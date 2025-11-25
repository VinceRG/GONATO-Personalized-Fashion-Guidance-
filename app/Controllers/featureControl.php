<?php 
// app/Controllers/featureControl.php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Model/Product.php';

class FeaturesController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Get mysqli connection, NOT the Database object itself
        $dbObj = new Database();
        $conn  = $dbObj->connect();   // ✅ $conn is mysqli

        // 2. Load user + analysis (defines $user, $profileImagePath, etc.)
        require __DIR__ . '/../Model/features.php';

        // 3. Use mysqli connection for Product model
        $productModel = new Product($conn);   // ✅ pass mysqli, not Database
        $result = $productModel->readAll();

        $catalogProducts = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $catalogProducts[] = $row;
            }
        }

        // 4. Render view
        require __DIR__ . '/../View/system_features.php';
    }
}
