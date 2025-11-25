<?php 
//controllers/featureControl.php
class FeaturesController {
    public function index() {
        require_once __DIR__ . '/../Model/features.php';
        require_once __DIR__ . '/../View/system_features.php';
    }
}

?>