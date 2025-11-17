<?php
require_once '../Model/Product.php';
require_once '../Model/Inventory.php';
require_once '../Core/Database.php';

$db = (new Database())->connect();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'addProduct':
        $product = new Product($db);
        $product->product_name = $_POST['product_name'];
        $product->description = $_POST['description'];
        $product->body_shape_id = $_POST['body_shape_id'];
        $product->price = $_POST['price'];
        $product->image_file = $_POST['image_file'];
        $product->create();

        $product_id = $db->lastInsertId();

        $inventory = new Inventory($db);
        $inventory->product_id = $product_id;
        $inventory->color_id = $_POST['color_id'];
        $inventory->size = $_POST['size'];
        $inventory->quantity = $_POST['quantity'];
        $inventory->addStock();

        echo json_encode(['success' => true]);
        break;

    case 'getInventory':
        $inventory = new Inventory($db);
        $stmt = $inventory->readAll();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
        break;

    case 'updateStock':
        $inventory = new Inventory($db);
        $inventory->inventory_id = $_POST['inventory_id'];
        $inventory->quantity = $_POST['quantity'];
        $inventory->updateStock();
        echo json_encode(['success' => true]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>
