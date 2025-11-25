<?php
require_once '../Model/Product.php';
require_once '../Model/Inventory.php';
require_once '../Core/Database.php';

$db = (new Database())->connect(); // $db is now a mysqli object

$upload_dir = '../uploads/product_images/'; 

/**
 * Handles the file upload process.
 * @param string $file_key The key in the $_FILES array (e.g., 'productImage').
 * @param string $upload_dir The destination directory.
 * @return string|null The unique filename on success, or null if no file uploaded/error.
 */
function handleImageUpload($file_key, $upload_dir) {
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES[$file_key];
        
        // Generate a unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $image_file_name = uniqid() . '.' . $extension;
        $destination = $upload_dir . $image_file_name;
        
        // Move the uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $image_file_name;
        } else {
            // Set an error response code and message
            http_response_code(500); 
            echo json_encode(['error' => 'Failed to save uploaded file on server.']);
            exit; 
        }
    }
    return null; // No file uploaded or error occurred
}

$action = $_POST['action'] ?? '';

switch ($action) {
    // ...
case 'addProduct':
    // --- IMAGE UPLOAD LOGIC ---
    // 💡 CHANGE: Call the function instead of duplicating the code
$image_file_name = handleImageUpload('productImage', $upload_dir);    // Note: The function handles errors and exits if upload fails.
    // --- END IMAGE UPLOAD LOGIC ---
    
    $product = new Product($db);
    $product->product_name = $_POST['productName'];
    $product->description = $_POST['productDescription'];
    $product->body_shape_id = $_POST['bodyShapeSelect'];
    $product->price = $_POST['productPrice'];
    $product->image_file = $image_file_name; 
    
    if ($product->create()) {
        $product_id = $db->insert_id; 

        $inventory = new Inventory($db);
        $inventory->product_id = $product_id;
        $inventory->color_id = $_POST['color_id'] ?? 1; 
        $inventory->size = $_POST['size'] ?? 'M'; 
        $inventory->quantity = $_POST['quantity'] ?? 0; 
        $inventory->addStock();

        echo json_encode(['success' => true]);
    } else {
        // 💡 FIX: Ensure $destination is defined for cleanup if needed
        $destination = $upload_dir . $image_file_name; 
        if ($image_file_name && file_exists($destination)) {
            unlink($destination);
        }
        // Set HTTP status code for error
        http_response_code(500); 
        echo json_encode(['error' => 'Product creation failed: ' . $db->error]);
    }
    break;

case 'updateProduct':
    $product_id = (int)($_POST['id'] ?? 0); 
    if ($product_id === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing Product ID for update.']);
        exit;
    }

    $product = new Product($db);
    $product->product_id = $product_id;
    
    // 1. Get the existing image filename
    $existing_data = $product->readSingle(); // Requires Product::readSingle()
    $old_image_file = $existing_data['IMAGE_FILE'] ?? null;

    // 2. Handle new image upload
    $image_file_name = handleImageUpload('productImage', $upload_dir); 

    // 3. Set properties for the update
    $product->product_name = $_POST['productName'];
    $product->description = $_POST['productDescription'];
    $product->body_shape_id = (int)$_POST['bodyShapeSelect'];
    $product->price = (float)$_POST['productPrice'];
    $product->image_file = $image_file_name; 

    if ($product->update()) { // Requires Product::update()
        // 4. Delete old image if a new one was successfully uploaded
        if ($image_file_name && $old_image_file) {
            $old_destination = $upload_dir . $old_image_file;
            if (file_exists($old_destination)) {
                unlink($old_destination);
            }
        }
        echo json_encode(['success' => true]);
    } else {
        // If DB update fails, delete the NEWLY uploaded file to clean up
        if ($image_file_name) {
            unlink($upload_dir . $image_file_name);
        }
        http_response_code(500);
        echo json_encode(['error' => 'Product update failed: ' . $db->error]);
    }
    break;

    case 'getInventory':
        $inventory = new Inventory($db);
        $result = $inventory->readAll(); // $result is a mysqli_result object
        
        // FIX 2: Use fetch_all(MYSQLI_ASSOC) method on the mysqli_result object
        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($data);
        } else {
            echo json_encode([]); // Return empty array if no results
        }
        break;

    case 'updateStock':
        $inventory = new Inventory($db);
        $inventory->inventory_id = $_POST['inventory_id'];
        $inventory->quantity = $_POST['quantity'];
        
        if ($inventory->updateStock()) {
            echo json_encode(['success' => true]);
        } else {
             echo json_encode(['error' => 'Stock update failed']);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>
