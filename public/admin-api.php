<?php
// public/admin-api.php (or wherever you place it)

session_start();

// Check if user is admin
function checkAdminAuth() {
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_username'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

// Database connection
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Controllers/AdminController.php';

try {
    // Use your static connect method
    $db = Database::connect();
    
    // Wrap mysqli connection in PDO for AdminController
    // Create PDO connection instead
    $pdo = new PDO(
        "mysql:host=localhost;port=3307;dbname=Amarelle",
        "root",
        "root"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    $adminController = new AdminController($pdo);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Check authentication
checkAdminAuth();

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Extract the action and parameters from query string
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? null;

// Set JSON header
header('Content-Type: application/json');

// Route handler
try {
// PRODUCTS ROUTES
if ($action === 'products' && $method === 'GET') {
    $adminController->getProductsOnly();
}
elseif ($action === 'addProduct' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $adminController->addProduct($data);
}
elseif ($action === 'updateProduct' && $method === 'PUT' && $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $adminController->updateProduct(intval($id), $data);
}
elseif ($action === 'deleteProduct' && $method === 'DELETE' && $id) {
    $adminController->deleteProduct(intval($id));
}

elseif ($action === 'inventoryByProduct' && $method === 'GET') {
    $productId = $_GET['product_id'] ?? null;
    if (!$productId) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing product_id']);
        exit;
    }
    $adminController->getInventoryByProduct(intval($productId));
}
// BODY SHAPES ROUTE
elseif ($action === 'bodyShapes' && $method === 'GET') {
    $adminController->getBodyShapes();
}
// Add inventory
elseif ($action === 'addInventory' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $adminController->addInventory($data);
}

// Update inventory
elseif ($action === 'inventory' && $method === 'PUT' && $id) {
    $data = json_decode(file_get_contents('php://input'), true);
    $adminController->updateInventory(intval($id), $data);
}

// Delete inventory
elseif ($action === 'inventory' && $method === 'DELETE' && $id) {
    $adminController->deleteInventory(intval($id));
}



    
    // COLORS ROUTES
    elseif ($action === 'colors') {
        if ($method === 'GET') {
            $adminController->getColors();
        }
        elseif ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $adminController->addColor($data);
        }
    }
    
    // USERS ROUTES
    elseif ($action === 'users') {
        if ($method === 'GET' && !$id) {
            $adminController->getUsers();
        }
        elseif ($method === 'PATCH' && $id && isset($_GET['toggle-lock'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $adminController->toggleUserLock(intval($id), $data);
        }
    }
    
    // ORDERS ROUTES
    elseif ($action === 'orders') {
        if ($method === 'GET' && !$id) {
            $adminController->getOrders();
        }
        elseif ($method === 'GET' && $id) {
            $adminController->getOrderDetails(intval($id));
        }
        elseif ($method === 'PATCH' && $id && isset($_GET['status'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $adminController->updateOrderStatus(intval($id), $data);
        }
    }
    
    // 404 - Route not found
    else {
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found', 'action' => $action, 'method' => $method]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>