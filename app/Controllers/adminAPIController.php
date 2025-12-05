<?php
// app/Controllers/AdminApiController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/adminController.php';
require_once __DIR__ . '/../Model/audit.php';

class AdminApiController
{
    private $adminController;
    private $conn;

    public function __construct()
{
    try {
        // ✅ Use your central Database class
        $mysqli = Database::connect();

        // Store connection for audit + staff functions
        $this->conn = $mysqli;

        // Pass DB to AdminController
        $this->adminController = new AdminController($mysqli);

    } catch (Exception $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => 'Database connection failed: ' . $e->getMessage()
        ]);
        exit;
    }
}

private function checkAdminAuth()
{
    if (
        empty($_SESSION['admin_id']) ||
        empty($_SESSION['admin_username'])
    ) {
        http_response_code(401);
        echo json_encode([
            'error'   => 'Unauthorized',
            'details' => 'Admin session not found'
        ]);
        exit;
    }
}

private function checkAdminRole(array $allowedRoles)
{
    $this->checkAdminAuth();

    $role = $_SESSION['admin_role'] ?? null;
    if (!in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        echo json_encode([
            'error'   => 'Forbidden',
            'details' => 'You do not have permission to access this resource.'
        ]);
        exit;
    }
}

private function handleStaff(string $method, ?string $idParam): void
{
    header('Content-Type: application/json');
    $conn = $this->conn;

    // ---------- LIST STAFF ----------
    if ($method === 'GET') {
        $sql = "SELECT ADMIN_ID, USERNAME, EMAIL, ROLE, IS_ACTIVE
                FROM admin
                ORDER BY ADMIN_ID ASC";

        $result = $conn->query($sql);

        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }

        echo json_encode($rows);
        return;
    }

    // ---------- CREATE STAFF ----------
    if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];

    $username = trim($data['username'] ?? '');
    $email    = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $role     = $data['role'] ?? 'staff';

    if ($username === '' || $password === '' || $email === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Username, email, and password are required']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email address']);
        return;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO admin (USERNAME, EMAIL, PASSWORD, ROLE, IS_ACTIVE)
        VALUES (?, ?, ?, ?, 1)
    ");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $conn->error]);
        return;
    }

    $stmt->bind_param('ssss', $username, $email, $hash, $role);

        if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(['error' => $stmt->error]);
        return;
    }

    $newAdminId = $stmt->insert_id;

    // 🔹 AUDIT: Add staff
    if (!empty($_SESSION['admin_id'])) {
        $audit = new Audit($this->conn);
        $audit->log(
            (int)$_SESSION['admin_id'],        // who did it
            'ADD_STAFF',                       // action code
            "Created staff account: {$username} (ADMIN_ID {$newAdminId}, ROLE {$role})"
        );
    }

    echo json_encode([
        'success'  => true,
        'admin_id' => $newAdminId
    ]);
    return;

}

    // ---------- UPDATE ACTIVE STATUS ----------
    if ($method === 'PATCH') {
        parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
        $id = isset($qs['id']) ? (int)$qs['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            return;
        }

        if ($id === (int)($_SESSION['admin_id'] ?? 0)) {
            http_response_code(400);
            echo json_encode(['error' => 'You cannot deactivate yourself']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $isActive = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        $stmt = $conn->prepare("UPDATE admin SET IS_ACTIVE = ? WHERE ADMIN_ID = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $conn->error]);
            return;
        }

        $stmt->bind_param('ii', $isActive, $id);

                if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['error' => $stmt->error]);
            return;
        }

        // 🔹 AUDIT: Activate / Deactivate staff
        if (!empty($_SESSION['admin_id'])) {
            $audit = new Audit($this->conn);

            $action    = $isActive ? 'ACTIVATE_STAFF' : 'DEACTIVATE_STAFF';
            $statusTxt = $isActive ? 'Active' : 'Inactive';

            $audit->log(
                (int)$_SESSION['admin_id'],          // who did it
                $action,                             // ACTIVATE_STAFF or DEACTIVATE_STAFF
                "Set staff ADMIN_ID {$id} to {$statusTxt}"
            );
        }

        echo json_encode(['success' => true]);
        return;

    }

    // ---------- DELETE STAFF ----------
    if ($method === 'DELETE') {
        parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
        $id = isset($qs['id']) ? (int)$qs['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id']);
            return;
        }

        if ($id === (int)($_SESSION['admin_id'] ?? 0)) {
            http_response_code(400);
            echo json_encode(['error' => 'You cannot delete your own account']);
            return;
        }

        $stmt = $conn->prepare("DELETE FROM admin WHERE ADMIN_ID = ?");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $conn->error]);
            return;
        }

        $stmt->bind_param('i', $id);

        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(['error' => $stmt->error]);
            return;
        }

        echo json_encode(['success' => true]);
        return;
    }

    // Method not supported
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}



public function handle()
{
    $this->checkAdminAuth();

    // Real HTTP method from server (GET/POST)
    $httpMethod = $_SERVER['REQUEST_METHOD'];

    // Optional override, e.g. &_method=PATCH
    $override = $_GET['_method'] ?? null;

    // Final method value used everywhere below
    $method = $override ? strtoupper($override) : $httpMethod;

    $action = $_GET['action'] ?? '';
    $id     = $_GET['id'] ?? null;

    header('Content-Type: application/json');

        try {
            // ============= PRODUCTS ROUTES =============
            if ($action === 'products' && $method === 'GET') {
                $this->adminController->getProductsOnly();
            }
            elseif ($action === 'addProduct' && $method === 'POST') {
                $data = $_POST; // Collects all text fields from the FormData
                $data['product_image_file'] = $_FILES['product_image'] ?? null; // Collects the file data
                $this->adminController->addProduct($data);
            }
            elseif ($action === 'updateProduct' && $method === 'POST' && $id) {
                $data = $_POST; // Collects all text fields from the FormData
                $data['product_image_file'] = $_FILES['product_image'] ?? null; // Collects the file data
                $this->adminController->updateProduct((int)$id, $data);
            }
            elseif ($action === 'deleteProduct' && $method === 'DELETE' && $id) {
                $this->adminController->deleteProduct((int)$id);
            }

            // ============= INVENTORY BY PRODUCT =========
            elseif ($action === 'inventoryByProduct' && $method === 'GET') {
                $productId = $_GET['product_id'] ?? null;
                if (!$productId) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Missing product_id']);
                    return;
                }
                $this->adminController->getInventoryByProduct((int)$productId);
            }

            // ============= BODY SHAPES ROUTE ============
            elseif ($action === 'bodyShapes' && $method === 'GET') {
                $this->adminController->getBodyShapes();
            }

            // ============= SEASONS ROUTE ================
            elseif ($action === 'seasons' && $method === 'GET') {
                $this->adminController->getSeasons();
            }

            // ============= INVENTORY ROUTES =============
            elseif ($action === 'addInventory' && $method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];

            // These keys must match what your JS sends
            $productId = (int)($data['productId'] ?? 0);
            $colorId   = (int)($data['colorId']   ?? 0);
            $size      = trim($data['size']       ?? '');

            if (!$productId || !$colorId || $size === '') {
                http_response_code(400);
                echo json_encode(['error' => 'Missing product, color or size']);
                return;
            }

            // 🔍 Check if a variant with same product + color + size already exists
            $stmt = $this->conn->prepare("
                SELECT INVENTORY_ID
                FROM inventory
                WHERE PRODUCT_ID = ? AND COLOR_ID = ? AND SIZE = ?
                LIMIT 1
            ");
            if (!$stmt) {
                http_response_code(500);
                echo json_encode(['error' => 'Database error: ' . $this->conn->error]);
                return;
            }

            $stmt->bind_param('iis', $productId, $colorId, $size);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // ❌ Duplicate variant
                http_response_code(409); // Conflict
                echo json_encode([
                    'error' => 'This variant (size, season & color) already exists for this product.'
                ]);
                $stmt->close();
                return;
            }

            $stmt->close();

            // ✅ No duplicate – continue with the normal add logic
            $this->adminController->addInventory($data);
        }

        elseif ($action === 'inventory' && $method === 'PUT' && $id) {
            $data = json_decode(file_get_contents('php://input'), true) ?? [];

            $inventoryId = (int)$id;

            // Same keys as above
            $productId = (int)($data['productId'] ?? 0);
            $colorId   = (int)($data['colorId']   ?? 0);
            $size      = trim($data['size']       ?? '');

            // Only run duplicate check if all 3 are present
            if ($productId && $colorId && $size !== '') {
                $stmt = $this->conn->prepare("
                    SELECT INVENTORY_ID
                    FROM inventory
                    WHERE PRODUCT_ID = ?
                    AND COLOR_ID   = ?
                    AND SIZE       = ?
                    AND INVENTORY_ID <> ?
                    LIMIT 1
                ");
                if (!$stmt) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Database error: ' . $this->conn->error]);
                    return;
                }

                $stmt->bind_param('iisi', $productId, $colorId, $size, $inventoryId);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows > 0) {
                    http_response_code(409); // Conflict
                    echo json_encode([
                        'error' => 'Another variant with this size, season & color already exists for this product.'
                    ]);
                    $stmt->close();
                    return;
                }

                $stmt->close();
            }

            // ✅ No duplicate – proceed with update
            $this->adminController->updateInventory($inventoryId, $data);
        }

            elseif ($action === 'inventory' && $method === 'DELETE' && $id) {
                $this->adminController->deleteInventory((int)$id);
            }
            // ============= INVENTORY ALERTS ROUTE =============
            elseif ($action === 'criticalInventory' && $method === 'GET') {
                $this->adminController->getCriticalInventory();
            }
            // ============= INVENTORY (ALL) =========
            elseif ($action === 'inventoryAll' && $method === 'GET') {
                $this->adminController->getAllInventory();
            }

            // ============= INVENTORY BY PRODUCT =========
            elseif ($action === 'inventoryByProduct' && $method === 'GET') {
                $productId = $_GET['product_id'] ?? null;
                if (!$productId) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Missing product_id']);
                    return;
                }
                $this->adminController->getInventoryByProduct((int)$productId);
            }

            // ============= COLORS ROUTES ================
            elseif ($action === 'colors') {
                if ($method === 'GET') {
                    $this->adminController->getColors();
                } elseif ($method === 'POST') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    $this->adminController->addColor($data);
                }
            }

            
            

            // ============= USERS ROUTES =================
            elseif ($action === 'users') {
                if ($method === 'GET' && !$id) {
                    $this->adminController->getUsers();
                } elseif (in_array($method, ['PATCH', 'POST']) && $id && isset($_GET['toggle-lock'])) {
                    $data = json_decode(file_get_contents('php://input'), true) ?? [];
                    $this->adminController->toggleUserLock((int)$id, $data);
                }
            }

            // ============= ORDERS ROUTES ================
            elseif ($action === 'orders') {
                if ($method === 'GET' && !$id) {
                    // List all orders for Orders tab
                    $this->adminController->getOrders();
                } elseif ($method === 'GET' && $id) {
                    // Single order details + items
                    $this->adminController->getOrderDetails((int)$id);
                } elseif ($method === 'PATCH' && $id && isset($_GET['status'])) {
                    // Optional: update order status (not yet used in admin.js)
                    $data = json_decode(file_get_contents('php://input'), true);
                    $this->adminController->updateOrderStatus((int)$id, $data);
                }
            }

            // ============= STAFF ROUTES =================
            elseif ($action === 'staff') {
                // Only super admin can manage staff/admin accounts
                $this->checkAdminRole(['super_admin']);
                $this->handleStaff($method, $id);
            }
            // ============= AUDIT ROUTES =================
            elseif ($action === 'get_audit' && $method === 'GET') {
                $audit = new Audit($this->conn);
                $logs  = $audit->getAll();

                $data = [];
                if ($logs) {
                    while ($row = $logs->fetch_assoc()) {
                        $data[] = $row;
                    }
                }

                echo json_encode([
                    'status' => 'success',
                    'data'   => $data
                ]);
            }

            // ============= 404 – NOT FOUND =============
            else {
                http_response_code(404);
                echo json_encode([
                    'error'  => 'Endpoint not found',
                    'action' => $action,
                    'method' => $method
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        }
    }
}


