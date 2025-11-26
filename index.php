<?php
// index.php (root of GONATO-Personalized-Fashion-Guidance-)

session_start();



if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Clear all session data
    $_SESSION = [];
    session_unset();
    session_destroy();

    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // 🔹 Go to landing page with a "logged_out" flag
    header("Location: index.php?page=landing&logged_out=1");
    exit;
}

// =====================================================
// 4. Admin API Endpoint
// =====================================================
if (isset($_GET['api']) && $_GET['api'] === 'admin') {
    require_once './public/admin-api.php';
    exit;
}

// =====================================================
// 5. Page Routing
// =====================================================
$page = $_GET['page'] ?? 'landing';

switch ($page) {

    // LOGIN
    case 'login':
        require_once './app/Controllers/loginControl.php';
        $controller = new LoginController();
        $controller->index();
        break;

    // REGISTER
    case 'register':
        require_once './app/Controllers/registerControl.php';
        $controller = new RegisterController();
        $controller->index();
        break;

    // USER INFO
    case 'user_info':
        require_once './app/Controllers/userControl.php';
        $controller = new UserController();
        $controller->index();
        break;

    // FEATURES
    case 'features':
        require_once './app/Controllers/featureControl.php';
        $controller = new FeaturesController();
        $controller->index();
        break;

    // BODY SHAPE
    case 'process_body_shape':
        require_once './app/Controllers/BodyShapeController.php';
        $controller = new BodyShapeController();
        $controller->process();
        break;

    // COLOR ANALYSIS
    case 'process_color_analysis':
        require_once './app/Controllers/ColorAnalysisController.php';
        $controller = new ColorAnalysisController();
        $controller->process();
        break;

    // OTP
    case 'otp_verification':
        require_once './app/View/otp_verification.php';
        break;

    // FORGOT PASSWORD
    case 'forgot':
        require_once './app/Controllers/forgotControl.php';
        $controller = new ForgotController();
        $controller->index();
        break;

    // ADMIN
    case 'admin_login':
        require_once 'app/Controllers/adminLoginControl.php';
        $controller = new AdminLoginController();
        $controller->index();
        break;

    case 'admin':
        require_once 'app/View/admin.php';
        break;

    case 'policy':
        require_once 'app/View/policy.php';
        break;

    // DEFAULT
    case 'landing':
    default:
        require_once './app/View/landing.php';
        break;
}
?>
