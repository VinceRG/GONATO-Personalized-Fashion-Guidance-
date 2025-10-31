<?php
// index.php (root of GONATO-Personalized-Fashion-Guidance-)

// Get requested page, default to 'landing'
$page = $_GET['page'] ?? 'landing';

switch ($page) {
    // ===== LOGIN PAGE + MFA =====
    case 'login':
        require_once './app/Controllers/loginControl.php';
        $controller = new LoginController();
        $controller->index();
        break;

    // ===== REGISTRATION PAGE =====
    case 'register':
        require_once './app/Controllers/registerControl.php';
        $controller = new RegisterController();
        $controller->index();
        break;

    // ===== USER INFO PAGE =====
    case 'user_info':
        require_once './app/Controllers/userControl.php';
        $controller = new UserController();
        $controller->index();
        break;

    // ===== FEATURES PAGE (after successful login) =====
    case 'features':
        require_once './app/Controllers/featureControl.php';
        $controller = new FeaturesController();
        $controller->index();
        break;

    // ===== OTP VERIFICATION (handled by LoginController internally) =====
    case 'otp_verification':
        require_once './app/View/otp_verification.php';
        break;

    // ===== FORGOT PASSWORD PAGE =====
    case 'forgot':
        require_once './app/Controllers/forgotControl.php';
        $controller = new ForgotController();
        $controller->index();
        break;

    // ===== DEFAULT: LANDING PAGE =====
    case 'landing':
    default:
        require_once './app/View/landing.php';
        break;
}
    