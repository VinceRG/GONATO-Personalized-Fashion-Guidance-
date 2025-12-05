<?php
// app/Controllers/AdminController.php
require_once __DIR__ . '/../Model/audit.php';
// config.php or at top of AdminController.php
define('CRITICAL_STOCK_THRESHOLD', 10);


class AdminController {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // ==================== PRODUCTS ====================
    
    /**
     * Get all products (without inventory details) - For Products Tab
     */
    function getProductsOnly() {
        try {
            $query = "
                SELECT 
                    p.PRODUCT_ID,
                    p.PRODUCT_NAME,
                    p.DESCRIPTION,
                    p.PRICE,
                    p.IMAGE_FILE,
                    p.BODY_SHAPE_ID,
                    bs.BODY_TYPE AS BODY_SHAPE_NAME
                FROM PRODUCTS p
                LEFT JOIN BODY_SHAPES bs ON p.BODY_SHAPE_ID = bs.BODY_SHAPE_ID
                ORDER BY p.PRODUCT_ID DESC
            ";
            $result = $this->db->query($query);

            $products = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $products[] = $row;
                }
                $result->free();
            }

            header('Content-Type: application/json');
            echo json_encode($products);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch products: ' . $e->getMessage()]);
        }
    }

    /**
     * Add a new product
     */
    public function addProduct($data) {
        $imageFilename = null;
        $uploadPath = null;
        
        try {
            // --- 1. Image File Handling ---
            $imageFile = $data['product_image_file'] ?? null;

            if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
                // Adjust the path based on your project structure relative to this controller
                $uploadDir = __DIR__ . '/../../public/image/'; 
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $ext = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
                $imageFilename = uniqid('product_', true) . '.' . $ext; 
                $uploadPath = $uploadDir . $imageFilename;

                if (!move_uploaded_file($imageFile['tmp_name'], $uploadPath)) {
                    throw new Exception("Failed to move uploaded file.");
                }
            }
            
            // --- 2. Data Preparation ---
            $productName = $data['productName'] ?? null;
            $description = $data['productDescription'] ?? ''; 
            $bodyShapeId = isset($data['bodyShapeSelect']) ? (int)$data['bodyShapeSelect'] : 1;
            $price       = isset($data['productPrice']) ? (float)$data['productPrice'] : 0.00;

            if (!$productName || $price === null) {
                throw new Exception("Product name and price are required.");
            }

            // --- 3. Database Insertion ---
            $query = "
                INSERT INTO products (PRODUCT_NAME, DESCRIPTION, BODY_SHAPE_ID, PRICE, IMAGE_FILE)
                VALUES (?, ?, ?, ?, ?)
            ";
            $stmt = $this->db->prepare($query);

            // s, s, i, d, s   (name, desc, body_shape_id, price, image_file)
            $stmt->bind_param(
                "ssids",
                $productName,
                $description,
                $bodyShapeId,
                $price,
                $imageFilename
            );

            if (!$stmt->execute()) {
                throw new Exception("DB Execute failed: " . $stmt->error);
            }

           $productId = $this->db->insert_id;
            $stmt->close();

            // 🔹 AUDIT: Add product
            if (!empty($_SESSION['admin_id'])) {
                $audit = new Audit($this->db);
                $audit->log(
                    (int)$_SESSION['admin_id'],
                    'ADD_PRODUCT',
                    "Added product: {$productName} (ID {$productId})"
                );
            }

            http_response_code(201);
            echo json_encode([
                'success'   => true,
                'productId' => $productId
            ]);

        } catch (Exception $e) {
            // Delete the file if DB insertion or file move fails
            if (isset($uploadPath) && file_exists($uploadPath)) {
                unlink($uploadPath);
            }
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add product: ' . $e->getMessage()]);
        }
    }

    /**
     * Update existing product
     */
    public function updateProduct($productId, $data) {
        $imageFilename = null;
        $uploadPath = null;
        $updateImage = false;
        
        try {
            // --- 1. Image File Handling (check if a new file was uploaded) ---
            $imageFile = $data['product_image_file'] ?? null;

            if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/image/'; 
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $ext = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
                $imageFilename = uniqid('product_', true) . '.' . $ext;
                $uploadPath = $uploadDir . $imageFilename;

                if (!move_uploaded_file($imageFile['tmp_name'], $uploadPath)) {
                    throw new Exception("Failed to move uploaded file.");
                }

                $updateImage = true;
            }

            // --- 2. Data Preparation ---
            $productName = $data['productName'] ?? null;
            $description = $data['productDescription'] ?? '';
            $bodyShapeId = isset($data['bodyShapeSelect']) ? (int)$data['bodyShapeSelect'] : 1;
            $price       = isset($data['productPrice']) ? (float)$data['productPrice'] : 0.00;

            if (!$productName || $price === null) {
                throw new Exception("Product name and price are required.");
            }

            // --- 3. Build the UPDATE query ---
            if ($updateImage) {
                $query = "
                    UPDATE PRODUCTS
                    SET PRODUCT_NAME = ?, DESCRIPTION = ?, BODY_SHAPE_ID = ?, PRICE = ?, IMAGE_FILE = ?
                    WHERE PRODUCT_ID = ?
                ";
            } else {
                $query = "
                    UPDATE PRODUCTS
                    SET PRODUCT_NAME = ?, DESCRIPTION = ?, BODY_SHAPE_ID = ?, PRICE = ?
                    WHERE PRODUCT_ID = ?
                ";
            }

            $stmt = $this->db->prepare($query);

            if ($updateImage) {
                $stmt->bind_param(
                    "ssidsi",
                    $productName,
                    $description,
                    $bodyShapeId,
                    $price,
                    $imageFilename,
                    $productId
                );
            } else {
                $stmt->bind_param(
                    "ssidi",
                    $productName,
                    $description,
                    $bodyShapeId,
                    $price,
                    $productId
                );
            }

            if (!$stmt->execute()) {
                throw new Exception("DB Execute failed: " . $stmt->error);
            }

            $stmt->close();

            http_response_code(200);
            echo json_encode([
                'success'   => true,
                'productId' => $productId
            ]);
        } catch (Exception $e) {
            if (isset($uploadPath) && file_exists($uploadPath)) {
                unlink($uploadPath);
            }
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update product: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete product and all its inventory
     */
    public function deleteProduct($productId) {
        $this->db->begin_transaction();
        
        try {
            // First delete inventory
            $stmt = $this->db->prepare("DELETE FROM inventory WHERE PRODUCT_ID = ?");
            $stmt->bind_param("i", $productId);
            if (!$stmt->execute()) throw new Exception("Inventory delete failed: " . $stmt->error);
            $stmt->close();
            
            // Then delete product
            $stmt = $this->db->prepare("DELETE FROM products WHERE PRODUCT_ID = ?");
            $stmt->bind_param("i", $productId);
            if (!$stmt->execute()) throw new Exception("Product delete failed: " . $stmt->error);
            $stmt->close();

            $this->db->commit();

            // 🔹 AUDIT: Delete product
            if (!empty($_SESSION['admin_id'])) {
                $audit = new Audit($this->db);
                $audit->log(
                    (int)$_SESSION['admin_id'],
                    'DELETE_PRODUCT',
                    "Deleted product ID: {$productId}"
                );
            }

            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            $this->db->rollback();
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete product: ' . $e->getMessage()]);
        }
    }

    // ==================== INVENTORY ====================

    /**
     * Get inventory variants for a given product - For Inventory Tab
     */
    public function getInventoryByProduct($productId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    i.INVENTORY_ID, 
                    i.SIZE, 
                    i.QUANTITY, 
                    i.CREATED_AT,
                    c.COLOR_ID,
                    c.COLOR_VALUE
                FROM inventory i
                JOIN colors c ON i.COLOR_ID = c.COLOR_ID
                WHERE i.PRODUCT_ID = ?
                ORDER BY i.SIZE, c.COLOR_VALUE
            ");
            $stmt->bind_param("i", $productId);
            
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);

            $result = $stmt->get_result();
            $inventory = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $inventory[] = $row;
                }
                $result->free();
            }
            $stmt->close();

            header('Content-Type: application/json');
            echo json_encode($inventory);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch inventory: ' . $e->getMessage()]);
        }
    }

    public function getAllInventory()
{
    try {
        $sql = "
            SELECT 
                i.INVENTORY_ID,
                i.PRODUCT_ID,
                i.COLOR_ID,
                i.SIZE,
                i.QUANTITY,
                i.CREATED_AT,
                p.PRODUCT_NAME,
                c.COLOR_VALUE
            FROM inventory i
            JOIN products p ON i.PRODUCT_ID = p.PRODUCT_ID
            JOIN colors c   ON i.COLOR_ID   = c.COLOR_ID
            ORDER BY p.PRODUCT_NAME, i.SIZE, c.COLOR_VALUE
        ";
        $result = $this->db->query($sql);
        if (!$result) {
            throw new Exception('Query failed: ' . $this->db->error);
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($rows);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to fetch inventory: ' . $e->getMessage()
        ]);
    }
}


    /**
     * Add inventory variant
     */
    public function addInventory($data) {
        try {
            if (empty($data['productId'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Product ID is required']);
                return;
            }
            if (empty($data['colorId']) || empty($data['size']) || !isset($data['quantity'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Color, size, and quantity are required']);
                return;
            }

            $productId = (int)$data['productId'];
            $colorId   = (int)$data['colorId'];
            $size      = $data['size'];
            $quantity  = (int)$data['quantity'];

            $stmt = $this->db->prepare("
                INSERT INTO inventory (PRODUCT_ID, COLOR_ID, SIZE, QUANTITY)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iisi", $productId, $colorId, $size, $quantity);

            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);

            $inventoryId = $this->db->insert_id;
            $stmt->close();

            // 🔹 AUDIT: Add inventory
            if (!empty($_SESSION['admin_id'])) {
                $audit = new Audit($this->db);
                $audit->log(
                    (int)$_SESSION['admin_id'],
                    'ADD_INVENTORY',
                    "Product {$productId}, Color {$colorId}, Size {$size}, Qty {$quantity} (Inventory ID {$inventoryId})"
                );
            }

            http_response_code(201);
            echo json_encode([
                'success'      => true,
                'inventory_id' => $inventoryId
            ]);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add inventory: ' . $e->getMessage()]);
        }
    }

    /**
     * Update inventory variant
     */
    public function updateInventory($inventoryId, $data) {
        try {
            if (empty($data['colorId']) || empty($data['size']) || !isset($data['quantity'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Color, size, and quantity are required']);
                return;
            }

            $colorId  = (int)$data['colorId'];
            $size     = $data['size'];
            $quantity = (int)$data['quantity'];

            $stmt = $this->db->prepare("
                UPDATE inventory
                SET COLOR_ID = ?, SIZE = ?, QUANTITY = ?
                WHERE INVENTORY_ID = ?
            ");
            $stmt->bind_param("isii", $colorId, $size, $quantity, $inventoryId);

            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $stmt->close();
            // 🔹 AUDIT: Update inventory
            if (!empty($_SESSION['admin_id'])) {
                $audit = new Audit($this->db);
                $audit->log(
                    (int)$_SESSION['admin_id'],
                    'UPDATE_INVENTORY',
                    "Inventory ID {$inventoryId} -> Color {$colorId}, Size {$size}, Qty {$quantity}"
                );
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update inventory: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete inventory variant
     */
    public function deleteInventory($inventoryId) {
    try {
        $stmt = $this->db->prepare("DELETE FROM inventory WHERE INVENTORY_ID = ?");
        $stmt->bind_param("i", $inventoryId);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $stmt->close();

        // 🔹 AUDIT: Delete inventory
        if (!empty($_SESSION['admin_id'])) {
            $audit = new Audit($this->db);
            $audit->log(
                (int)$_SESSION['admin_id'],
                'DELETE_INVENTORY',
                "Deleted inventory ID {$inventoryId}"
            );
        }

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to delete inventory: ' . $e->getMessage()
        ]);
    }
}

    // ==================== COLORS ====================

    public function getColors() {
        try {
            $sql = "
                SELECT 
                    c.COLOR_ID,
                    c.COLOR_VALUE,
                    c.SEASON_ID,
                    s.SEASON_TYPE
                FROM colors c
                LEFT JOIN seasons s ON c.SEASON_ID = s.SEASON_ID
                ORDER BY s.SEASON_TYPE, c.COLOR_VALUE
            ";
            $result = $this->db->query($sql);
            $colors = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $colors[] = $row;
                }
                $result->free();
            }

            header('Content-Type: application/json');
            echo json_encode($colors);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch colors: ' . $e->getMessage()]);
        }
    }

    public function addColor($data) {
        try {
            $colorValue = trim($data['colorValue'] ?? '');
            $seasonId   = isset($data['seasonId']) ? (int)$data['seasonId'] : null;

            if ($colorValue === '' || !$seasonId) {
                http_response_code(400);
                echo json_encode(['error' => 'Color value and season are required']);
                return;
            }

            $stmt = $this->db->prepare("
                INSERT INTO colors (COLOR_VALUE, SEASON_ID)
                VALUES (?, ?)
            ");
            $stmt->bind_param("si", $colorValue, $seasonId);

            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);

            $colorId = $this->db->insert_id;
            $stmt->close();
            // 🔹 AUDIT: Add color
            if (!empty($_SESSION['admin_id'])) {
                $audit = new Audit($this->db);
                $audit->log(
                    (int)$_SESSION['admin_id'],
                    'ADD_COLOR',
                    "Added color '{$colorValue}' (ID {$colorId}) for season ID {$seasonId}"
                );
            }
            http_response_code(201);
            echo json_encode([
                'success'  => true,
                'color_id' => $colorId
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add color: ' . $e->getMessage()]);
        }
    }

    // ==================== BODY SHAPES ====================

    public function getBodyShapes() {
        try {
            $sql = "SELECT BODY_SHAPE_ID, BODY_TYPE FROM body_shapes ORDER BY BODY_TYPE";
            $result = $this->db->query($sql);
            $shapes = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $shapes[] = $row;
                }
                $result->free();
            }

            header('Content-Type: application/json');
            echo json_encode($shapes);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch body shapes: ' . $e->getMessage()]);
        }
    }

    // ==================== SEASONS ====================

    public function getSeasons() {
        try {
            $sql = "SELECT SEASON_ID, SEASON_TYPE FROM seasons ORDER BY SEASON_TYPE";
            $result = $this->db->query($sql);
            $seasons = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $seasons[] = $row;
                }
                $result->free();
            }

            header('Content-Type: application/json');
            echo json_encode($seasons);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch seasons: ' . $e->getMessage()]);
        }
    }

    // ==================== USERS ====================

public function getUsers() {
    try {
        // Match your actual schema (from adminfunc.php)
        $sql = "
            SELECT 
                USER_ID,
                USERNAME,
                EMAIL,
                CREATED_AT,
                STATUS
            FROM users
            ORDER BY CREATED_AT DESC
        ";

        $result = $this->db->query($sql);
        $users = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = [
                    'USER_ID'    => $row['USER_ID'],
                    'USERNAME'   => $row['USERNAME'],
                    'EMAIL'      => $row['EMAIL'],
                    'CREATED_AT' => $row['CREATED_AT'],
                    // Convert STATUS: 0 = Active, 1 = Locked
                    'IS_LOCKED'  => ($row['STATUS'] == 1),
                ];
            }
            $result->free();
        }

        header('Content-Type: application/json');
        echo json_encode($users);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch users: ' . $e->getMessage()]);
    }
}

public function toggleUserLock($userId, $data) {
    try {
        // Frontend sends isLocked = current state.
        // Your DB uses STATUS: 0 = active, 1 = locked
        $currentlyLocked = !empty($data['isLocked']);   // true/false
        $newStatus = $currentlyLocked ? 0 : 1;          // flip it

        $stmt = $this->db->prepare(
            "UPDATE users 
             SET STATUS = ?, FAILED_ATTEMPTS = 0 
             WHERE USER_ID = ?"
        );
        $stmt->bind_param("ii", $newStatus, $userId);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $stmt->close();

        $message = $newStatus ? 'User account locked successfully.' 
                              : 'User account unlocked successfully.';
        // 🔹 AUDIT: Lock / Unlock user
        if (!empty($_SESSION['admin_id'])) {
            $audit = new Audit($this->db);
            $audit->log(
                (int)$_SESSION['admin_id'],
                $newStatus ? 'LOCK_USER' : 'UNLOCK_USER',
                "User ID {$userId}"
            );
        }
        echo json_encode([
            'success' => true,
            'message' => $message
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update user status: ' . $e->getMessage()]);
    }
}
public function getCriticalInventory() {
    // Uses the constant you already defined at the top: CRITICAL_STOCK_THRESHOLD
    $threshold = CRITICAL_STOCK_THRESHOLD; // e.g. 10

    try {
        $sql = "
            SELECT 
                i.INVENTORY_ID,
                i.PRODUCT_ID,
                i.COLOR_ID,
                i.SIZE,
                i.QUANTITY,
                p.PRODUCT_NAME,
                c.COLOR_VALUE
            FROM inventory i
            JOIN products p ON i.PRODUCT_ID = p.PRODUCT_ID
            JOIN colors c   ON i.COLOR_ID   = c.COLOR_ID
            WHERE i.QUANTITY <= ?
            ORDER BY i.UPDATED_AT DESC
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param("i", $threshold);
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'inventory_id' => (int)$row['INVENTORY_ID'],
                'product_id'   => (int)$row['PRODUCT_ID'],
                'product_name' => $row['PRODUCT_NAME'],
                'color'        => $row['COLOR_VALUE'],
                'size'         => $row['SIZE'],
                'quantity'     => (int)$row['QUANTITY'],
            ];
        }

        $stmt->close();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'count'     => count($items),
            'threshold' => $threshold,
            'items'     => $items,
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error'   => 'Failed to fetch critical inventory',
            'message' => $e->getMessage(),
        ]);
    }
}


    // ==================== ORDERS ====================

    /**
     * Get all orders for admin Orders tab
     */
    public function getOrders() {
        try {
            $sql = "
                SELECT 
                    o.ORDER_ID,
                    o.ORDER_NUMBER,
                    o.USER_ID,
                    o.TOTAL_AMOUNT,
                    o.STATUS,
                    o.SHIPPING_ADDRESS,
                    o.CREATED_AT,
                    u.USERNAME,
                    (
                        SELECT COALESCE(SUM(QUANTITY),0)
                        FROM order_items oi
                        WHERE oi.ORDER_ID = o.ORDER_ID
                    ) AS ITEM_COUNT,
                    CASE WHEN o.SHIPPING_ADDRESS IS NULL OR o.SHIPPING_ADDRESS = '' 
                        THEN 0 ELSE 1 
                    END AS SHIPPING_REQUIRED
                FROM orders o
                LEFT JOIN users u ON u.USER_ID = o.USER_ID
                ORDER BY o.CREATED_AT DESC
            ";
            $result = $this->db->query($sql);
            $orders = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $orders[] = $row;
                }
                $result->free();
            }

            header('Content-Type: application/json');
            echo json_encode($orders);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch orders: ' . $e->getMessage()]);
        }
    }

    /**
     * Get details + items for a single order
     */
    public function getOrderDetails($orderId) {
        try {
            // Main order + user info
            $stmt = $this->db->prepare("
                SELECT 
                    o.ORDER_ID,
                    o.ORDER_NUMBER,
                    o.USER_ID,
                    o.TOTAL_AMOUNT,
                    o.STATUS,
                    o.SHIPPING_ADDRESS,
                    o.CREATED_AT,
                    u.USERNAME,
                    u.EMAIL
                FROM orders o
                LEFT JOIN users u ON u.USER_ID = o.USER_ID
                WHERE o.ORDER_ID = ?
            ");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $orderRes = $stmt->get_result();
            $order = $orderRes ? $orderRes->fetch_assoc() : null;
            $stmt->close();

            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                return;
            }

            // Items
            $stmt = $this->db->prepare("
                SELECT 
                    oi.ORDER_ITEM_ID,
                    oi.ORDER_ID,
                    oi.PRODUCT_ID,
                    oi.COLOR_ID,
                    oi.SIZE,
                    oi.QUANTITY,
                    oi.UNIT_PRICE,
                    p.PRODUCT_NAME,
                    p.PRICE,
                    c.COLOR_VALUE
                FROM order_items oi
                INNER JOIN products p ON oi.PRODUCT_ID = p.PRODUCT_ID
                INNER JOIN colors c ON oi.COLOR_ID = c.COLOR_ID
                WHERE oi.ORDER_ID = ?
            ");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $order['items'] = [];
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $order['items'][] = $row;
                }
                $result->free();
            }
            $stmt->close();

            header('Content-Type: application/json');
            echo json_encode($order);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch order details: ' . $e->getMessage()]);
        }
    }

    /**
     * Update order status (optional, for future use)
     */
    public function updateOrderStatus($orderId, $data) {
        try {
            $status = $data['status'] ?? null;
            if (!$status) {
                http_response_code(400);
                echo json_encode(['error' => 'Status is required']);
                return;
            }

            $stmt = $this->db->prepare("UPDATE ORDERS SET STATUS = ? WHERE ORDER_ID = ?");
            $stmt->bind_param("si", $status, $orderId);
            
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $stmt->close();
            // 🔹 AUDIT: Order status change
            if (!empty($_SESSION['admin_id'])) {
                $audit = new Audit($this->db);
                $audit->log(
                    (int)$_SESSION['admin_id'],
                    'UPDATE_ORDER_STATUS',
                    "Order ID {$orderId} -> Status '{$status}'"
                );
            }
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update order status: ' . $e->getMessage()]);
        }
    }
}


?>
