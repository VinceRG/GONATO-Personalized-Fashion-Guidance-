<?php
// index.php (root of GONATO-Personalized-Fashion-Guidance-)

$page = $_GET['page'] ?? 'landing';

switch ($page) {
    case 'login':
        require_once './Controllers/LoginControl.php';
        $controller = new LoginController();
        $controller->index();
        break;

    case 'register':
        require_once './Controllers/registerControl.php';
        $controller = new RegisterController();
        $controller->index();
        break;

    case 'landing':
    default:
        require_once './View/landing.php';
        break;
}
