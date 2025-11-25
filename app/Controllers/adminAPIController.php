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
            // Use MYSQLI for AdminController and Models
            $mysqli = new mysqli("localhost", "root", "root", "Amarelle", 3307);
            
            if ($mysqli->connect_error) {
                // If connection fails, throw an exception
                throw new Exception("MySQLi Connection failed: " . $mysqli->connect_error);
            }
            
            // Pass the mysqli object to AdminController
            $this->adminController = new AdminController($mysqli);

        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                // Change the error message to reflect mysqli failure
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

