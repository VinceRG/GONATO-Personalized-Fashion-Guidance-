<?php 
// app/Controllers/featureControl.php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Model/Product.php';

class FeaturesController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. DB connection (mysqli)
        $dbObj = new Database();
        $conn  = $dbObj->connect();

        // 2. Load user + analysis helpers ($user, $profileImagePath, etc.)
        require __DIR__ . '/../Model/features.php';

        // ============================
        //   GET USER ANALYSIS RESULTS
        // ============================
        $bodyShapeResult = $_SESSION['bodyShapeResult']     ?? null;
        $colorResult     = $_SESSION['colorAnalysisResult'] ?? null;

        // These are the *names* we will use in SQL:
        // bs.BODY_TYPE (from body_shapes)  e.g. "PEAR"
        // s.SEASON_TYPE (from seasons)     e.g. "Spring"
        $bodyShape = null;
        $season    = null;

        // 2.1 BODY SHAPE
        if (!empty($bodyShapeResult['prediction']['body_shape'])) {
            // From latest body-shape analysis (e.g. "PEAR")
            $bodyShape = $bodyShapeResult['prediction']['body_shape'];
        } elseif (!empty($user['BODY_TYPE'])) {
            // If features.php already joined body_shapes for the user
            $bodyShape = $user['BODY_TYPE'];
        }

        // 2.2 SEASON / COLOR SEASON
        if (!empty($colorResult['season'])) {
            // From color analysis result (should be "Autumn", "Spring", etc.)
            $season = $colorResult['season'];
        } elseif (!empty($user['SEASON_TYPE'])) {
            // If features.php already joined seasons for the user
            $season = $user['SEASON_TYPE'];
        }

        // Optional normalisation (just in case the model returns lowercase/variants)
        if (!empty($bodyShape)) {
            $bodyShape = strtoupper(trim($bodyShape));   // to match BODY_TYPE like "PEAR"
        }

        if (!empty($season)) {
            $season = ucfirst(strtolower(trim($season))); // "spring" → "Spring"
        }

        // ===================================
        //   LOAD PRODUCTS (CATALOG + RECS)
        // ===================================
        $productModel = new Product($conn);

        // 3. Catalog (all in-stock products, using your existing method)
        $catalogProducts = [];
        $result = $productModel->readAll();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $catalogProducts[] = $row;
            }
        }

        // 4. Recommendations based on:
        //    - body_shapes.BODY_TYPE = $bodyShape (e.g. "PEAR")
        //    - seasons.SEASON_TYPE   = $season   (e.g. "Spring")
        $recommendedProducts = [];

        try {
            // This will use the JOIN:
            // products → body_shapes → inventory → colors → seasons
            // and only return in-stock products matching shape + season.
            $recResult = $productModel->readRecommended($bodyShape, $season, 8);

            if ($recResult && $recResult->num_rows > 0) {
                while ($row = $recResult->fetch_assoc()) {
                    $recommendedProducts[] = $row;
                }
            }
        } catch (Exception $e) {
            // Optional: you can log or show a flash message if needed
            // $_SESSION['errorMessage'] = $e->getMessage();
        }

        // 5. Render the view
        require __DIR__ . '/../View/system_features.php';
    }
}
