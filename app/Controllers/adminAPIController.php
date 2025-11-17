<?php
// app/Controllers/AdminApiController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/adminController.php';

class AdminApiController
{
    private $adminController;

    public function __construct()
    {
        try {
            // Use PDO for AdminController
            $pdo = new PDO(
                "mysql:host=localhost;port=3307;dbname=Amarelle",
                "root",
                "root"
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            $this->adminController = new AdminController($pdo);
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
            header('Content-Type: application/json');
            echo json_encode([
                'error'   => 'Unauthorized',
                'details' => 'Admin session not found'
                // For debugging you can temporarily add: 'session' => $_SESSION
            ]);
            exit;
        }
    }

    public function handle()
    {
        $this->checkAdminAuth();

        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? '';
        $id     = $_GET['id'] ?? null;

        header('Content-Type: application/json');

        try {
            // ============= PRODUCTS ROUTES =============
            if ($action === 'products' && $method === 'GET') {
                $this->adminController->getProductsOnly();
            }
            elseif ($action === 'addProduct' && $method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $this->adminController->addProduct($data);
            }
            elseif ($action === 'updateProduct' && $method === 'PUT' && $id) {
                $data = json_decode(file_get_contents('php://input'), true);
                $this->adminController->updateProduct((int)$id, $data);
            }
            elseif ($action === 'deleteProduct' && $method === 'DELETE' && $id) {
                $this->adminController->deleteProduct((int)$id);
            }

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

            // ============= INVENTORY ROUTES =============
            elseif ($action === 'addInventory' && $method === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $this->adminController->addInventory($data);
            }
            elseif ($action === 'inventory' && $method === 'PUT' && $id) {
                $data = json_decode(file_get_contents('php://input'), true);
                $this->adminController->updateInventory((int)$id, $data);
            }
            elseif ($action === 'inventory' && $method === 'DELETE' && $id) {
                $this->adminController->deleteInventory((int)$id);
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
                } elseif ($method === 'PATCH' && $id && isset($_GET['toggle-lock'])) {
                    $data = json_decode(file_get_contents('php://input'), true);
                    $this->adminController->toggleUserLock((int)$id, $data);
                }
            }

            // ============= ORDERS ROUTES ================
            elseif ($action === 'orders') {
                if ($method === 'GET' && !$id) {
                    $this->adminController->getOrders();
                } elseif ($method === 'GET' && $id) {
                    $this->adminController->getOrderDetails((int)$id);
                } elseif ($method === 'PATCH' && $id && isset($_GET['status'])) {
                    $data = json_decode(file_get_contents('php://input'), true);
                    $this->adminController->updateOrderStatus((int)$id, $data);
                }
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
