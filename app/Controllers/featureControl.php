<?php 
//controllers/featureControl.php
class FeaturesController {
    public function index() {
        // Include model first - this processes all data and creates variables
        require_once __DIR__ . '/../Model/features.php';
        
        // Then include view - it will have access to all variables from model
        require_once __DIR__ . '/../View/system_features.php';
    }
}
?>