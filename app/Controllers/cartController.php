<?php

class CartController
{
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../Core/Database.php';
        $this->db = (new Database())->connect(); // mysqli
    }

    private function getOrCreateCart($userId) {
        $sql = "SELECT CART_ID FROM cart WHERE USER_ID = ? AND STATUS = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($cartId);

        if ($stmt->fetch()) {
            return $cartId;
        }

        $stmt->close();

        $sql = "INSERT INTO cart (USER_ID, STATUS) VALUES (?, 0)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return $stmt->insert_id;
    }

    public function addToCart($productId, $inventoryId, $price) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // accept multiple possible keys
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            echo json_encode(["success" => false, "message" => "User not logged in"]);
            return;
        }

        $cartId = $this->getOrCreateCart($userId);

        $sql = "SELECT CART_ITEM_ID, QUANTITY
                FROM cart_items
                WHERE CART_ID = ? AND PRODUCT_ID = ? AND (INVENTORY_ID <=> ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iii", $cartId, $productId, $inventoryId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $newQty = $row['QUANTITY'] + 1;
            $update = $this->db->prepare("UPDATE cart_items SET QUANTITY = ? WHERE CART_ITEM_ID = ?");
            $update->bind_param("ii", $newQty, $row['CART_ITEM_ID']);
            $update->execute();
        } else {
            $insert = $this->db->prepare("
                INSERT INTO cart_items (CART_ID, PRODUCT_ID, INVENTORY_ID, QUANTITY, PRICE)
                VALUES (?, ?, ?, 1, ?)
            ");
            $insert->bind_param("iiid", $cartId, $productId, $inventoryId, $price);
            $insert->execute();
        }

$cartCount = $this->getCartItemCount($userId);

echo json_encode([
    "success"    => true,
    "cart_count" => $cartCount
]);    }

 public function getProductVariants() {
    // Get product id from query string
    $productId = isset($_GET['PRODUCT_ID']) ? (int)$_GET['PRODUCT_ID'] : 0;

    if ($productId <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid product ID"
        ]);
        return;
    }

    // 1) Get base product info
    $sql = "SELECT PRODUCT_ID, PRODUCT_NAME, PRICE, IMAGE_FILE 
            FROM products 
            WHERE PRODUCT_ID = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    $productRow = $result->fetch_assoc();
    $stmt->close();

    if (!$productRow) {
        echo json_encode([
            "success" => false,
            "message" => "Product not found"
        ]);
        return;
    }

    $imageBasePath = 'public/image/';
    $product = [
        'id'    => (int)$productRow['PRODUCT_ID'],
        'name'  => $productRow['PRODUCT_NAME'],
        'price' => (float)$productRow['PRICE'],
        'image' => $imageBasePath . $productRow['IMAGE_FILE'],
    ];

    // 2) Get variants from inventory + colors
    $sql = "SELECT 
                i.INVENTORY_ID,
                i.SIZE,
                i.QUANTITY,
                c.COLOR_ID,
                c.COLOR_VALUE AS COLOR_NAME
            FROM inventory i
            JOIN colors c ON c.COLOR_ID = i.COLOR_ID
            WHERE i.PRODUCT_ID = ?
            ORDER BY c.COLOR_VALUE, i.SIZE";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $invResult = $stmt->get_result();

    $variants = [];
    while ($row = $invResult->fetch_assoc()) {
        $variants[] = [
            'inventory_id' => (int)$row['INVENTORY_ID'],
            'size'         => $row['SIZE'],
            'quantity'     => (int)$row['QUANTITY'],
            'color_id'     => (int)$row['COLOR_ID'],
            'color_name'   => $row['COLOR_NAME'],
        ];
    }
    $stmt->close();

    echo json_encode([
        "success"  => true,
        "product"  => $product,
        "variants" => $variants,
    ]);
}

public function getCartItems() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo json_encode([
            "success" => false,
            "message" => "User not logged in",
            "items"   => []
        ]);
        return;
    }

    // 1) Find active cart for this user
    $sql = "SELECT CART_ID 
            FROM cart 
            WHERE USER_ID = ? AND STATUS = 0";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "DB error: " . $this->db->error
        ]);
        return;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($cartId);

    if (!$stmt->fetch()) {
        // No active cart -> empty cart
        $stmt->close();
        echo json_encode([
            "success"     => true,
            "items"       => [],
            "totalAmount" => 0,
            "totalQty"    => 0
        ]);
        return;
    }

    $stmt->close();

    // 2) Get items in the cart, with product + variant info
    $sql = "SELECT 
                ci.CART_ITEM_ID,
                ci.PRODUCT_ID,
                ci.INVENTORY_ID,
                ci.QUANTITY,
                ci.PRICE,

                p.PRODUCT_NAME,
                p.IMAGE_FILE,

                i.SIZE,
                i.QUANTITY AS STOCK_LEFT,
                c.COLOR_VALUE AS COLOR_NAME
            FROM cart_items ci
            JOIN products p   ON ci.PRODUCT_ID   = p.PRODUCT_ID
            LEFT JOIN inventory i ON ci.INVENTORY_ID = i.INVENTORY_ID
            LEFT JOIN colors c    ON i.COLOR_ID      = c.COLOR_ID
            WHERE ci.CART_ID = ?";

    $stmt = $this->db->prepare($sql);
    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "DB error: " . $this->db->error
        ]);
        return;
    }

    $stmt->bind_param("i", $cartId);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    $totalAmount = 0;
    $totalQty = 0;

$imageBasePath = 'public/image/';

    while ($row = $result->fetch_assoc()) {
        $lineTotal = $row['PRICE'] * $row['QUANTITY'];
        $totalAmount += $lineTotal;
        $totalQty += $row['QUANTITY'];

        $items[] = [
            'cart_item_id' => (int)$row['CART_ITEM_ID'],
            'product_id'   => (int)$row['PRODUCT_ID'],
            'inventory_id' => (int)$row['INVENTORY_ID'],
            'name'         => $row['PRODUCT_NAME'],
            'price'        => (float)$row['PRICE'],
            'quantity'     => (int)$row['QUANTITY'],
            'line_total'   => $lineTotal,
            'image'        => $imageBasePath . $row['IMAGE_FILE'],

            // extra info for display
            'color'        => $row['COLOR_NAME'] ?? null,
            'size'         => $row['SIZE'] ?? null,
            'stock_left'   => isset($row['STOCK_LEFT']) ? (int)$row['STOCK_LEFT'] : null,
        ];
    }

    $stmt->close();

echo json_encode([
    "success"     => true,
    "items"       => $items,
    "totalAmount" => $totalAmount,
    "totalQty"    => $totalQty,
    "cart_count"  => $totalQty  // same as totalQty
]);

}
private function getCartItemCount($userId) {
    $sql = "SELECT COALESCE(SUM(ci.QUANTITY), 0) AS TOTAL_QTY
            FROM cart_items ci
            JOIN cart c ON ci.CART_ID = c.CART_ID
            WHERE c.USER_ID = ? AND c.STATUS = 0";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    return (int)($row['TOTAL_QTY'] ?? 0);
}

public function updateCartItemQuantity() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(["success" => false, "message" => "User not logged in"]);
        return;
    }

    $cartItemId = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;
    $quantity   = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

    // 🔹 Debug log so you can see what's coming in
    error_log("updateCartItemQuantity: cart_item_id={$cartItemId}, quantity={$quantity}");

    // ✅ cart_item_id must be valid
    if ($cartItemId <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid cart item"]);
        return;
    }

    // ✅ If quantity <= 0, treat as delete instead of throwing "Invalid item/quantity"
    if ($quantity <= 0) {
        $sql = "DELETE ci
                FROM cart_items ci
                JOIN cart c ON ci.CART_ID = c.CART_ID
                WHERE ci.CART_ITEM_ID = ? AND c.USER_ID = ? AND c.STATUS = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $cartItemId, $userId);
        $stmt->execute();
        $stmt->close();

        $cartCount = $this->getCartItemCount($userId);

        echo json_encode([
            "success"    => true,
            "deleted"    => true,
            "cart_count" => $cartCount,
        ]);
        return;
    }

    // Check that this cart item belongs to this user's active cart
    $sql = "SELECT ci.CART_ID, c.USER_ID, c.STATUS, i.QUANTITY AS STOCK_LEFT
            FROM cart_items ci
            JOIN cart c ON ci.CART_ID = c.CART_ID
            LEFT JOIN inventory i ON ci.INVENTORY_ID = i.INVENTORY_ID
            WHERE ci.CART_ITEM_ID = ?";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $cartItemId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if (!$row || (int)$row['USER_ID'] !== (int)$userId || (int)$row['STATUS'] !== 0) {
        echo json_encode(["success" => false, "message" => "Item not found in active cart"]);
        return;
    }

    // Clamp to stock if inventory exists
    if (!is_null($row['STOCK_LEFT'])) {
        $stockLeft = (int)$row['STOCK_LEFT'];
        if ($quantity > $stockLeft) {
            $quantity = $stockLeft;
        }
    }

    $update = $this->db->prepare("UPDATE cart_items SET QUANTITY = ? WHERE CART_ITEM_ID = ?");
    $update->bind_param("ii", $quantity, $cartItemId);
    $update->execute();
    $update->close();

    $cartCount = $this->getCartItemCount($userId);

    echo json_encode([
        "success"    => true,
        "cart_count" => $cartCount,
    ]);
}


public function deleteCartItem() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(["success" => false, "message" => "User not logged in"]);
        return;
    }

    $cartItemId = isset($_POST['cart_item_id']) ? (int)$_POST['cart_item_id'] : 0;
    if ($cartItemId <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid item"]);
        return;
    }

    // Ensure this item belongs to the current user's active cart
    $sql = "DELETE ci
            FROM cart_items ci
            JOIN cart c ON ci.CART_ID = c.CART_ID
            WHERE ci.CART_ITEM_ID = ? AND c.USER_ID = ? AND c.STATUS = 0";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("ii", $cartItemId, $userId);
    $stmt->execute();

    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected > 0) {
    $cartCount = $this->getCartItemCount($userId);
    echo json_encode([
        "success"    => true,
        "cart_count" => $cartCount
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Item not found or already removed"
    ]);
}
}
public function getCartCount() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        echo json_encode(["success" => true, "count" => 0]);
        return;
    }

    $sql = "SELECT COALESCE(SUM(ci.QUANTITY), 0) AS cnt
            FROM cart_items ci
            JOIN cart c ON ci.CART_ID = c.CART_ID
            WHERE c.USER_ID = ? AND c.STATUS = 0";

    $stmt = $this->db->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    echo json_encode([
        "success" => true,
        "count"   => (int)$result['cnt']
    ]);
}

}
