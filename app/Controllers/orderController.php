<?php
// app/Controllers/OrderController.php

require_once __DIR__ . '/../Core/Database.php';

class OrderController
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect(); // mysqli
    }

    public function createFromCart()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode([
                'success' => false,
                'message' => 'User not logged in'
            ]);
            return;
        }

        $totalAmount      = isset($_POST['total_amount']) ? (float)$_POST['total_amount'] : 0;
        $shippingName     = trim($_POST['shipping_name']  ?? '');   // NEW
        $shippingPhone    = trim($_POST['shipping_phone'] ?? '');   // NEW
        $shippingAddress  = trim($_POST['shipping_address'] ?? '');

        if ($totalAmount <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid total amount'
            ]);
            return;
        }

        // Parse selected cart_item_ids (comma-separated string from JS)
        $selectedIds = [];
        if (!empty($_POST['cart_item_ids'])) {
            $parts = explode(',', $_POST['cart_item_ids']);
            foreach ($parts as $p) {
                $id = (int)trim($p);
                if ($id > 0) {
                    $selectedIds[] = $id;
                }
            }
        }

        if (empty($selectedIds)) {
            echo json_encode([
                'success' => false,
                'message' => 'No cart items selected'
            ]);
            return;
        }

        // 1) Find active cart for this user (STATUS = 0 = active)
        $sql = "SELECT CART_ID FROM cart WHERE USER_ID = ? AND STATUS = 0";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            echo json_encode([
                'success' => false,
                'message' => 'DB error: ' . $this->db->error
            ]);
            return;
        }

        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $stmt->bind_result($cartId);

        if (!$stmt->fetch()) {
            $stmt->close();
            echo json_encode([
                'success' => false,
                'message' => 'No active cart'
            ]);
            return;
        }
        $stmt->close();

        // 2) Insert order header
        $orderNumber = 'ORD-' . date('YmdHis') . '-' . $userId;

        // UPDATED: include SHIPPING_NAME + SHIPPING_PHONE
        $sql = "INSERT INTO orders 
                    (ORDER_NUMBER, USER_ID, SHIPPING_NAME, SHIPPING_PHONE, TOTAL_AMOUNT, STATUS, SHIPPING_ADDRESS)
                VALUES (?, ?, ?, ?, ?, 'pending', ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            echo json_encode([
                'success' => false,
                'message' => 'DB error: ' . $this->db->error
            ]);
            return;
        }

        // ORDER_NUMBER (s), USER_ID (i), SHIPPING_NAME (s),
        // SHIPPING_PHONE (s), TOTAL_AMOUNT (d), SHIPPING_ADDRESS (s)
        $stmt->bind_param(
            'sissds',
            $orderNumber,
            $userId,
            $shippingName,
            $shippingPhone,
            $totalAmount,
            $shippingAddress
        );
        $stmt->execute();
        $orderId = $stmt->insert_id;
        $stmt->close();

        if (!$orderId) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create order'
            ]);
            return;
        }

        // 3) Get ALL cart_items for this cart, then filter in PHP to only selected IDs
        $sql = "SELECT 
                    ci.CART_ITEM_ID,
                    ci.PRODUCT_ID,
                    ci.INVENTORY_ID,
                    ci.QUANTITY,
                    ci.PRICE,
                    i.SIZE,
                    i.COLOR_ID
                FROM cart_items ci
                LEFT JOIN inventory i ON ci.INVENTORY_ID = i.INVENTORY_ID
                WHERE ci.CART_ID = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            echo json_encode([
                'success' => false,
                'message' => 'DB error: ' . $this->db->error
            ]);
            return;
        }

        $stmt->bind_param('i', $cartId);
        $stmt->execute();
        $result = $stmt->get_result();

        $itemSql = "INSERT INTO order_items
                        (ORDER_ID, PRODUCT_ID, COLOR_ID, SIZE, QUANTITY, UNIT_PRICE)
                    VALUES (?, ?, ?, ?, ?, ?)";
        $itemStmt = $this->db->prepare($itemSql);
        if (!$itemStmt) {
            echo json_encode([
                'success' => false,
                'message' => 'DB error: ' . $this->db->error
            ]);
            return;
        }

        // Convert only the selected cart items
        while ($row = $result->fetch_assoc()) {
            $cartItemId = (int)$row['CART_ITEM_ID'];

            if (!in_array($cartItemId, $selectedIds, true)) {
                // Skip unselected items
                continue;
            }

            $productId   = (int)$row['PRODUCT_ID'];
            $inventoryId = !empty($row['INVENTORY_ID']) ? (int)$row['INVENTORY_ID'] : null;
            $qty         = (int)$row['QUANTITY'];
            $unitPrice   = (float)$row['PRICE'];
            $size        = $row['SIZE'] ?? 'M'; // ensure non-null
            $colorId     = isset($row['COLOR_ID']) ? (int)$row['COLOR_ID'] : 0;

            // ORDER_ID (i), PRODUCT_ID (i), COLOR_ID (i), SIZE (s), QUANTITY (i), UNIT_PRICE (d)
            $itemStmt->bind_param('iiisid', $orderId, $productId, $colorId, $size, $qty, $unitPrice);
            $itemStmt->execute();

            // Decrease inventory if this row is tied to a variant
            if ($inventoryId !== null) {
                $invUpd = $this->db->prepare(
                    "UPDATE inventory 
                     SET QUANTITY = GREATEST(QUANTITY - ?, 0)
                     WHERE INVENTORY_ID = ?"
                );
                if ($invUpd) {
                    $invUpd->bind_param('ii', $qty, $inventoryId);
                    $invUpd->execute();
                    $invUpd->close();
                }
            }

            // Remove this cart item
            $del = $this->db->prepare("DELETE FROM cart_items WHERE CART_ITEM_ID = ?");
            if ($del) {
                $del->bind_param('i', $cartItemId);
                $del->execute();
                $del->close();
            }
        }

        $itemStmt->close();
        $stmt->close();

        // 4) Only mark cart as checked-out if NO items remain
        $check = $this->db->prepare("SELECT COUNT(*) AS cnt FROM cart_items WHERE CART_ID = ?");
        if ($check) {
            $check->bind_param('i', $cartId);
            $check->execute();
            $res = $check->get_result();
            $row = $res->fetch_assoc();
            $check->close();

            $remaining = (int)($row['cnt'] ?? 0);

            if ($remaining === 0) {
                $updCart = $this->db->prepare("UPDATE cart SET STATUS = 1 WHERE CART_ID = ?");
                if ($updCart) {
                    $updCart->bind_param('i', $cartId);
                    $updCart->execute();
                    $updCart->close();
                }
            }
        }

        echo json_encode([
            'success'   => true,
            'order_id'  => $orderId,
            'order_num' => $orderNumber
        ]);
    }
}
