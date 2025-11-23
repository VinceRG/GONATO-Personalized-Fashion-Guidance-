<?php
// app/Controllers/AdminController.php

class AdminController {
    private $db; // This is a mysqli object
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // ==================== PRODUCTS ====================
    
    /**
     * Get all products (without inventory details) - For Products Tab
     */
function getProductsOnly() {
    try {
        // Read page + pageSize from query (with defaults)
        $page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $pageSize = isset($_GET['pageSize']) ? max(1, (int)$_GET['pageSize']) : 10;
        $offset   = ($page - 1) * $pageSize;

        // 1) Total count (for pagination)
        $countQuery  = "SELECT COUNT(*) AS total FROM PRODUCTS";
        $countResult = $this->db->query($countQuery);
        $total       = 0;
        if ($countResult) {
            $row   = $countResult->fetch_assoc();
            $total = (int)$row['total'];
            $countResult->free();
        }

        // 2) Paged data
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
            ORDER BY p.PRODUCT_NAME
            LIMIT ?, ?
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ii", $offset, $pageSize);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $result->free();
        }
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode([
            'data'     => $products,
            'total'    => $total,
            'page'     => $page,
            'pageSize' => $pageSize
        ]);
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
            INSERT INTO PRODUCTS (PRODUCT_NAME, DESCRIPTION, BODY_SHAPE_ID, PRICE, IMAGE_FILE)
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
                // Adjust the path based on your project structure
                $uploadDir = __DIR__ . '/../../public/image/'; 
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
            $bodyShapeId = $data['bodyShapeSelect'] ?? 1;
            $price = $data['productPrice'] ?? 0.00;

            // --- 3. Database Update ---
            $query  = "UPDATE PRODUCTS SET PRODUCT_NAME = ?, DESCRIPTION = ?, BODY_SHAPE_ID = ?, PRICE = ?";
            $types  = "ssid";  // s,s,i,d
            $params = [$productName, $description, $bodyShapeId, $price];
            
            if ($updateImage) {
                $query .= ", IMAGE_FILE = ?";
                $types .= "s";
                $params[] = $imageFilename;
            }

            $query .= " WHERE PRODUCT_ID = ?";
            $types .= "i";
            $params[] = $productId;

            $stmt = $this->db->prepare($query);
            
            // Pass the types string and the parameter array to bind_param
            $stmt->bind_param($types, ...$params); 
            
            if (!$stmt->execute()) {
                 throw new Exception("DB Execute failed: " . $stmt->error);
            }
            
            $stmt->close();

            echo json_encode(['success' => true]);
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
        $this->db->begin_transaction(); // Start transaction
        
        try {
            // Note: Add logic here to find and delete the old image file if needed

            // Delete all inventory for this product
            $stmt = $this->db->prepare("DELETE FROM INVENTORY WHERE PRODUCT_ID = ?");
            $stmt->bind_param("i", $productId);
            if (!$stmt->execute()) throw new Exception("Inventory delete failed: " . $stmt->error);
            $stmt->close();
            
            // Delete the product
            $stmt = $this->db->prepare("DELETE FROM PRODUCTS WHERE PRODUCT_ID = ?");
            $stmt->bind_param("i", $productId);
            if (!$stmt->execute()) throw new Exception("Product delete failed: " . $stmt->error);
            $stmt->close();
            
            $this->db->commit(); // Commit transaction
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $this->db->rollback(); // Rollback on error
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete product: ' . $e->getMessage()]);
        }
    }
    
    // ==================== INVENTORY ====================
    
    /**
     * Get inventory by product - For Inventory Tab
     */
public function getInventoryByProduct($productId) {
    try {
        // Read page + pageSize from query (with defaults)
        $page     = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $pageSize = isset($_GET['pageSize']) ? max(1, (int)$_GET['pageSize']) : 10;
        $offset   = ($page - 1) * $pageSize;

        // 1) Total count for this product
        $countStmt = $this->db->prepare("
            SELECT COUNT(*) AS total 
            FROM INVENTORY 
            WHERE PRODUCT_ID = ?
        ");
        $countStmt->bind_param("i", $productId);
        $countStmt->execute();
        $countRes = $countStmt->get_result();
        $total    = 0;
        if ($countRes) {
            $row   = $countRes->fetch_assoc();
            $total = (int)$row['total'];
            $countRes->free();
        }
        $countStmt->close();

        // 2) Paged inventory rows
        $stmt = $this->db->prepare("
            SELECT 
                i.INVENTORY_ID, 
                i.SIZE, 
                i.QUANTITY, 
                i.CREATED_AT,
                c.COLOR_ID,
                c.COLOR_VALUE
            FROM INVENTORY i
            JOIN COLORS c ON i.COLOR_ID = c.COLOR_ID
            WHERE i.PRODUCT_ID = ?
            ORDER BY i.SIZE, c.COLOR_VALUE
            LIMIT ?, ?
        ");
        $stmt->bind_param("iii", $productId, $offset, $pageSize);
        
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
        echo json_encode([
            'data'     => $inventory,
            'total'    => $total,
            'page'     => $page,
            'pageSize' => $pageSize
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch inventory: ' . $e->getMessage()]);
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

            $query = "
                INSERT INTO INVENTORY (PRODUCT_ID, COLOR_ID, SIZE, QUANTITY)
                VALUES (?, ?, ?, ?)
            ";
            $stmt = $this->db->prepare($query);
            // i, i, s, i (product_id, color_id, size, quantity)
            $stmt->bind_param("iisi", $data['productId'], $data['colorId'], $data['size'], $data['quantity']);
            
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            
            $inventoryId = $this->db->insert_id;
            $stmt->close();

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'inventoryId' => $inventoryId
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
            $query = "
                UPDATE INVENTORY 
                SET COLOR_ID = ?, SIZE = ?, QUANTITY = ?
                WHERE INVENTORY_ID = ?
            ";
            $stmt = $this->db->prepare($query);
            
            // i, s, i, i (color_id, size, quantity, inventory_id)
            $stmt->bind_param("isii", 
                $data['colorId'],
                $data['size'],
                $data['quantity'],
                $inventoryId
            );
            
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $stmt->close();
            
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
            $stmt = $this->db->prepare("DELETE FROM INVENTORY WHERE INVENTORY_ID = ?");
            $stmt->bind_param("i", $inventoryId);
            
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $stmt->close();
            
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete inventory: ' . $e->getMessage()]);
        }
    }
    
    // ==================== COLORS ====================
    
    /**
     * Get all colors
     */
function getColors() {
    try {
        $query = "
            SELECT 
                c.COLOR_ID,
                c.COLOR_VALUE,
                c.SEASON_ID,
                s.SEASON_TYPE
            FROM COLORS c
            LEFT JOIN SEASONS s ON c.SEASON_ID = s.SEASON_ID
            ORDER BY c.COLOR_VALUE
        ";
        $result = $this->db->query($query);

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

    
    /**
     * Add new color
     */
    function addColor() {
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        $colorValue = trim($data['colorValue'] ?? '');
        $seasonId   = isset($data['seasonId']) ? (int)$data['seasonId'] : null;

        if ($colorValue === '' || !$seasonId) {
            throw new Exception("Color value and season are required.");
        }

        $stmt = $this->db->prepare("
            INSERT INTO COLORS (COLOR_VALUE, SEASON_ID)
            VALUES (?, ?)
        ");
        $stmt->bind_param("si", $colorValue, $seasonId);

        if (!$stmt->execute()) {
            throw new Exception("Insert failed: " . $stmt->error);
        }

        $newId = $stmt->insert_id;
        $stmt->close();

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'color_id' => $newId]);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => 'Failed to add color: ' . $e->getMessage()]);
    }
}

    
    // ==================== BODY SHAPES ====================
    
    /**
     * Get all body shapes
     */
    public function getBodyShapes() {
        try {
            $query = "SELECT BODY_SHAPE_ID, BODY_TYPE FROM BODY_SHAPES ORDER BY BODY_TYPE";
            $result = $this->db->query($query);
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

    function getSeasons() {
    try {
        $query  = "SELECT SEASON_ID, SEASON_TYPE FROM SEASONS ORDER BY SEASON_TYPE";
        $result = $this->db->query($query);
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
    
    /**
     * Get all users
     */
    public function getUsers() {
    try {
        $query = "
            SELECT 
                USER_ID,
                USERNAME,
                EMAIL,
                STATUS,
                CREATED_AT
            FROM USERS
            ORDER BY CREATED_AT DESC
        ";
        $result = $this->db->query($query);
        $users = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $row['IS_LOCKED'] = (bool)$row['STATUS']; // 0 = active, 1 = locked
                $users[] = $row;
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

    /**
     * Toggle user lock status
     */
public function toggleUserLock($userId, $data) {
    try {
        $isLocked = isset($data['isLocked']) ? (bool)$data['isLocked'] : false;
        $newStatus = $isLocked ? 1 : 0;

        // Update status AND reset failed attempts when unlocking
        $query = "
            UPDATE USERS 
            SET STATUS = ?, FAILED_ATTEMPTS = CASE WHEN ? = 0 THEN 0 ELSE FAILED_ATTEMPTS END
            WHERE USER_ID = ?
        ";
        $stmt = $this->db->prepare($query);
        
        // i, i, i (newStatus, newStatus, userId)
        $stmt->bind_param("iii", $newStatus, $newStatus, $userId); 
        
        if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
        $stmt->close();

        $message = $newStatus ? 'User account locked successfully.' : 'User account unlocked successfully.';
        echo json_encode(['success' => true, 'message' => $message]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update user status: ' . $e->getMessage()]);
    }
}
    
    // ==================== ORDERS ====================
    
    /**
     * Get all orders with summary information
     */
    public function getOrders() {
        try {
            $query = "
                SELECT 
                    o.ORDER_ID,
                    o.USER_ID,
                    u.USERNAME,
                    o.TOTAL_AMOUNT,
                    o.STATUS,
                    o.SHIPPING_REQUIRED,
                    o.CREATED_AT,
                    COUNT(oi.ORDER_ITEM_ID) as ITEM_COUNT
                FROM ORDERS o
                LEFT JOIN USERS u ON o.USER_ID = u.USER_ID
                LEFT JOIN ORDER_ITEMS oi ON o.ORDER_ID = oi.ORDER_ID
                GROUP BY o.ORDER_ID
                ORDER BY o.CREATED_AT DESC
            ";
            $result = $this->db->query($query);
            $orders = [];
            
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $row['SHIPPING_REQUIRED'] = (bool)$row['SHIPPING_REQUIRED'];
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
     * Get detailed order information including items
     */
    public function getOrderDetails($orderId) {
        try {
            // Get order details
            $stmt = $this->db->prepare("
                SELECT 
                    o.ORDER_ID,
                    o.USER_ID,
                    u.USERNAME,
                    u.EMAIL,
                    o.TOTAL_AMOUNT,
                    o.STATUS,
                    o.SHIPPING_REQUIRED,
                    o.CREATED_AT
                FROM ORDERS o
                LEFT JOIN USERS u ON o.USER_ID = u.USER_ID
                WHERE o.ORDER_ID = ?
            ");
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $order = $result->fetch_assoc();
            $result->free();
            $stmt->close();
            
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                return;
            }
            
            // Get order items
            $stmt = $this->db->prepare("
                SELECT 
                    oi.ORDER_ITEM_ID,
                    oi.QUANTITY,
                    oi.PRICE,
                    p.PRODUCT_NAME,
                    i.SIZE,
                    c.COLOR_VALUE
                FROM ORDER_ITEMS oi
                INNER JOIN INVENTORY i ON oi.INVENTORY_ID = i.INVENTORY_ID
                INNER JOIN PRODUCTS p ON i.PRODUCT_ID = p.PRODUCT_ID
                INNER JOIN COLORS c ON i.COLOR_ID = c.COLOR_ID
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
            
            // Convert boolean
            $order['SHIPPING_REQUIRED'] = (bool)$order['SHIPPING_REQUIRED'];
            
            header('Content-Type: application/json');
            echo json_encode($order);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch order details: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Update order status
     */

    
    
    public function updateOrderStatus($orderId, $data) {
        try {
            $validStatuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
            $status = $data['status'] ?? null;
            
            if (!in_array($status, $validStatuses)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid status']);
                return;
            }
            
            $stmt = $this->db->prepare("UPDATE ORDERS SET STATUS = ? WHERE ORDER_ID = ?");
            $stmt->bind_param("si", $status, $orderId);
            
            if (!$stmt->execute()) throw new Exception("Execute failed: " . $stmt->error);
            $stmt->close();
            
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update order status: ' . $e->getMessage()]);
        }
    }
}
?>