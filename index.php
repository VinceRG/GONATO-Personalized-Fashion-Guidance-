<?php
// index.php (root of GONATO-Personalized-Fashion-Guidance-)

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

    case 'landing':
    default:
        require_once './app/View/landing.php';
        break;
}
