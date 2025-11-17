<?php
// index.php (root of GONATO-Personalized-Fashion-Guidance-)
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Test
// var_dump($_ENV['PAYMONGO_SECRET_KEY'] ?? 'Not set'); exit;
// Get requested page, default to 'landing'
// Handle API requests FIRST (before page routing)
if (isset($_GET['api'])) {
    if ($_GET['api'] === 'admin') {
        require_once __DIR__ . '/app/Controllers/AdminApiController.php';
        $apiController = new AdminApiController();
        $apiController->handle();
        exit; // Stop execution after API response
    } elseif ($_GET['api'] === 'paymongo') {
        require_once __DIR__ . '/public/paymongo_create_intent.php';
        exit;
    }
}

// Get the requested page from the URL, default to 'landing'
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
     case 'admin_login':
        require_once 'app/Controllers/adminLoginControl.php';
        $controller = new AdminLoginController();
        $controller->index();
        break;

    case 'admin':
        require_once 'app/View/admin.php';
        break;

    case 'landing':
    default:
        require_once './app/View/landing.php';
        break;
}
?>