<?php
// index.php (root of GONATO-Personalized-Fashion-Guidance-)

// Handle API requests FIRST (before page routing)
if (isset($_GET['api']) && $_GET['api'] === 'admin') {
    require_once './public/admin-api.php';
    exit; // Stop execution after API response
}

// Get the requested page from the URL, default to 'landing'
$page = $_GET['page'] ?? 'landing';

switch ($page) {
    case 'login':
        require_once './app/Controllers/loginControl.php';
        $controller = new LoginController();
        $controller->index();
        break;

    case 'register':
        require_once './app/Controllers/registerControl.php';
        $controller = new RegisterController();
        $controller->index();
        break;

    case 'user_info':
        require_once './app/Controllers/userControl.php';
        $controller = new UserController();
        $controller->index();
        break;

    case 'features':
        require_once './app/Controllers/featureControl.php';
        $controller = new FeaturesController();
        $controller->index();
        break;

    case 'admin':
        require_once './app/View/admin.php';
        break;

    case 'landing':
    default:
        require_once './app/View/landing.php';
        break;
}
?>