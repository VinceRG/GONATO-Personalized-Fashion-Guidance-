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
        require_once __DIR__ . '/app/Controllers/adminApiController.php';
        $apiController = new AdminApiController();
        $apiController->handle();
        exit; // Stop execution after API response
    } elseif ($_GET['api'] === 'paymongo') {
        require_once __DIR__ . '/public/paymongo_create_intent.php';
        exit;
    }
}



// // ✅ Secure session cookie config
// $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

// session_set_cookie_params([
//     'lifetime' => 0,       // session cookie (until browser close)
//     'path'     => '/',
//     'domain'   => '',
//     'secure'   => $secure, // only over HTTPS
//     'httponly' => true,    // JS cannot read
//     'samesite' => 'Lax',
// ]);

// // session_start();


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

// Get requested page, default to 'landing'
// Handle API requests FIRST (before page routing)
if (isset($_GET['api']) && $_GET['api'] === 'admin') {
    require_once './public/admin-api.php';
    exit; // Stop execution after API response
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
    // ===== ADMIN =====
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
    case 'add_to_cart':
        require_once __DIR__ . '/app/Controllers/cartController.php';
        $controller = new CartController();

        // This will be called via fetch/AJAX as POST and return JSON
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');

            $productId   = isset($_POST['PRODUCT_ID'])   ? (int)$_POST['PRODUCT_ID']   : 0;
            $inventoryId = isset($_POST['INVENTORY_ID']) ? (int)$_POST['INVENTORY_ID'] : null;
            $price       = isset($_POST['PRICE'])        ? (float)$_POST['PRICE']      : 0;

            $controller->addToCart($productId, $inventoryId, $price);
            exit; // stop here, no view needed
        } else {
            http_response_code(405);
            echo 'Method Not Allowed';
            exit;
        }

        break;

    case 'get_cart':
        require_once __DIR__ . '/app/Controllers/cartController.php';
        $controller = new CartController();
        header('Content-Type: application/json');
        $controller->getCartItems();
        exit;
        break;
    case 'create_order':
        require_once __DIR__ . '/app/Controllers/OrderController.php';
        $controller = new OrderController();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->createFromCart();
        } else {
            echo json_encode(["success" => false, "message" => "Method not allowed"]);
        }
        exit;
        break;

    case 'get_product_variants':
        require_once __DIR__ . '/app/Controllers/cartController.php';
        $controller = new CartController();
        header('Content-Type: application/json');
        $controller->getProductVariants();
        exit;
        break;

    case 'update_cart_item':
    require_once __DIR__ . '/app/Controllers/cartController.php';
    $controller = new CartController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        $controller->updateCartItemQuantity();
        exit;
    }
    http_response_code(405);
    exit;

case 'delete_cart_item':
    require_once __DIR__ . '/app/Controllers/cartController.php';
    $controller = new CartController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        $controller->deleteCartItem();
        exit;
    }
    http_response_code(405);
    exit;
case 'create_order':
    require_once __DIR__ . '/app/Controllers/OrderController.php';
    $controller = new OrderController();
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->createFromCart();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
    }
    exit;
    break;

case 'get_cart_count':
    require_once __DIR__ . '/app/Controllers/cartController.php';
    $ctrl = new CartController();
    header('Content-Type: application/json');
    $ctrl->getCartCount();
    exit;


    // ===== DEFAULT: LANDING PAGE =====
    case 'landing':
    default:
        require_once './app/View/landing.php';
        break;
}   
?>