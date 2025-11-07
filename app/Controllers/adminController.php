<?php
// app/Controllers/AdminController.php

class AdminController {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // ==================== PRODUCTS ====================
    
    /**
     * Get all products (without inventory details) - For Products Tab
     */
    public function getProductsOnly() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    p.PRODUCT_ID,
                    p.PRODUCT_NAME,
                    p.DESCRIPTION,
                    p.PRICE,
                    p.IMAGE_FILE,
                    p.BODY_SHAPE_ID,
                    bs.BODY_TYPE AS CATEGORY
                FROM PRODUCTS p
                LEFT JOIN BODY_SHAPES bs ON p.BODY_SHAPE_ID = bs.BODY_SHAPE_ID
                ORDER BY p.PRODUCT_NAME
            ");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($products);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch products: ' . $e->getMessage()]);
        }
    }

    /**
     * Add a new product
     */
    public function addProduct($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO PRODUCTS (PRODUCT_NAME, DESCRIPTION, BODY_SHAPE_ID, PRICE, IMAGE_FILE)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['productName'],
                $data['description'] ?? '',
                $data['bodyShapeId'] ?? 1,
                $data['price'],
                $data['imageFile'] ?? null
            ]);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'productId' => $this->db->lastInsertId()
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add product: ' . $e->getMessage()]);
        }
    }

    /**
     * Update existing product
     */
    public function updateProduct($productId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE PRODUCTS
                SET PRODUCT_NAME = ?, DESCRIPTION = ?, BODY_SHAPE_ID = ?, PRICE = ?
                WHERE PRODUCT_ID = ?
            ");
            $stmt->execute([
                $data['productName'],
                $data['description'] ?? '',
                $data['bodyShapeId'] ?? 1,
                $data['price'],
                $productId
            ]);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update product: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete product and all its inventory
     */
    public function deleteProduct($productId) {
        try {
            $this->db->beginTransaction();
            
            // Delete all inventory for this product
            $stmt = $this->db->prepare("DELETE FROM INVENTORY WHERE PRODUCT_ID = ?");
            $stmt->execute([$productId]);
            
            // Delete the product
            $stmt = $this->db->prepare("DELETE FROM PRODUCTS WHERE PRODUCT_ID = ?");
            $stmt->execute([$productId]);
            
            $this->db->commit();
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            $this->db->rollBack();
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
            ");
            $stmt->execute([$productId]);
            $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($inventory);
        } catch (PDOException $e) {
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

            $stmt = $this->db->prepare("
                INSERT INTO INVENTORY (PRODUCT_ID, COLOR_ID, SIZE, QUANTITY)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['productId'],
                $data['colorId'],
                $data['size'],
                $data['quantity']
            ]);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'inventoryId' => $this->db->lastInsertId()
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add inventory: ' . $e->getMessage()]);
        }
    }

    /**
     * Update inventory variant
     */
    public function updateInventory($inventoryId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE INVENTORY 
                SET COLOR_ID = ?, SIZE = ?, QUANTITY = ?
                WHERE INVENTORY_ID = ?
            ");
            
            $stmt->execute([
                $data['colorId'],
                $data['size'],
                $data['quantity'],
                $inventoryId
            ]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
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
            $stmt->execute([$inventoryId]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete inventory: ' . $e->getMessage()]);
        }
    }
    
    // ==================== COLORS ====================
    
    /**
     * Get all colors
     */
    public function getColors() {
        try {
            $stmt = $this->db->query("SELECT COLOR_ID, COLOR_VALUE FROM COLORS ORDER BY COLOR_VALUE");
            $colors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            header('Content-Type: application/json');
            echo json_encode($colors);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch colors: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Add new color
     */
    public function addColor($data) {
        try {
            $stmt = $this->db->prepare("INSERT INTO COLORS (COLOR_VALUE) VALUES (?)");
            $stmt->execute([$data['colorValue']]);
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'colorId' => $this->db->lastInsertId()
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add color: ' . $e->getMessage()]);
        }
    }
    
    // ==================== BODY SHAPES ====================
    
    /**
     * Get all body shapes
     */
    public function getBodyShapes() {
        try {
            $stmt = $this->db->query("SELECT BODY_SHAPE_ID, BODY_TYPE FROM BODY_SHAPES ORDER BY BODY_TYPE");
            $shapes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            header('Content-Type: application/json');
            echo json_encode($shapes);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch body shapes: ' . $e->getMessage()]);
        }
    }
    
    // ==================== USERS ====================
    
    /**
     * Get all users
     */
    public function getUsers() {
    try {
        $stmt = $this->db->query("
            SELECT 
                USER_ID,
                USERNAME,
                EMAIL,
                STATUS,
                CREATED_AT
            FROM USERS
            ORDER BY CREATED_AT DESC
        ");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Convert STATUS to boolean for frontend
        foreach ($users as &$user) {
            $user['IS_LOCKED'] = (bool)$user['STATUS']; // 0 = active, 1 = locked
        }
        
        header('Content-Type: application/json');
        echo json_encode($users);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch users: ' . $e->getMessage()]);
    }
}

    
    /**
     * Toggle user lock status
     */
public function toggleUserLock($userId, $data) {
    try {
        $stmt = $this->db->prepare("UPDATE USERS SET STATUS = ? WHERE USER_ID = ?");
        $stmt->execute([$data['isLocked'] ? 1 : 0, $userId]); // 1 = locked, 0 = active
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
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
            $stmt = $this->db->query("
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
            ");
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert SHIPPING_REQUIRED to boolean
            foreach ($orders as &$order) {
                $order['SHIPPING_REQUIRED'] = (bool)$order['SHIPPING_REQUIRED'];
            }
            
            header('Content-Type: application/json');
            echo json_encode($orders);
        } catch (PDOException $e) {
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
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
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
            $stmt->execute([$orderId]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert boolean
            $order['SHIPPING_REQUIRED'] = (bool)$order['SHIPPING_REQUIRED'];
            
            header('Content-Type: application/json');
            echo json_encode($order);
        } catch (PDOException $e) {
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
            
            if (!in_array($data['status'], $validStatuses)) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid status']);
                return;
            }
            
            $stmt = $this->db->prepare("UPDATE ORDERS SET STATUS = ? WHERE ORDER_ID = ?");
            $stmt->execute([$data['status'], $orderId]);
            
            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update order status: ' . $e->getMessage()]);
        }
    }
}
?>