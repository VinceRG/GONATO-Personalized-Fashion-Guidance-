<?php
class Product {
    private $conn;
    private $table = "products";

    public $product_id;
    public $product_name;
    public $description;
    public $body_shape_id;
    public $price;
    public $image_file;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        // Using prepared statement for INSERT (best practice)
        $query = "INSERT INTO {$this->table} (PRODUCT_NAME, DESCRIPTION, BODY_SHAPE_ID, PRICE, IMAGE_FILE) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // CRITICAL CHECK 1: Ensure preparation succeeded
        if (!$stmt) {
             throw new Exception("Prepare failed: " . $this->conn->error);
        }
        
        // 's' for string, 's' for string, 'i' for integer, 'd' for double/decimal, 's' for string
        // **DOUBLE CHECK YOUR DATABASE SCHEMA AGAINST 'ssids'**
        $success = $stmt->bind_param("ssids", 
            $this->product_name, 
            $this->description, 
            $this->body_shape_id, 
            $this->price, 
            $this->image_file
        );

        if (!$success) {
             throw new Exception("Bind failed: " . $stmt->error);
        }
        
        $executed = $stmt->execute();

        // CRITICAL CHECK 2: Ensure execution succeeded
        if (!$executed) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // CRITICAL CHECK 3: Ensure rows were actually inserted (0 affected rows is a silent failure)
        if ($stmt->affected_rows === 0) {
            // This is the common failure point for foreign key/type mismatch
            throw new Exception("Insert reported success but affected 0 rows. Check data types or required fields.");
        }

        $stmt->close();
        return true; // Success!
    }
    public function update() {
        // Build the query dynamically based on whether a new image is provided
        $query = "UPDATE {$this->table} SET PRODUCT_NAME = ?, DESCRIPTION = ?, BODY_SHAPE_ID = ?, PRICE = ?";
        
        $types = "ssids"; // s, s, i, d
        $params = [$this->product_name, $this->description, $this->body_shape_id, $this->price];
        
        if ($this->image_file) {
            $query .= ", IMAGE_FILE = ?";
            $types .= "s";
            $params[] = $this->image_file;
        }

        $query .= " WHERE PRODUCT_ID = ?";
        $types .= "i";
        $params[] = $this->product_id;

        $stmt = $this->conn->prepare($query);
        
        // Pass the types string and the parameter array to bind_param
        $stmt->bind_param($types, ...$params); 
        
        return $stmt->execute();
    }

    public function readAll() {
        // Using mysqli->query() for simple SELECT
        $query = "SELECT * FROM {$this->table} ORDER BY CREATED_AT DESC";
        $result = $this->conn->query($query);
        
        // Returns mysqli_result object
        return $result;
    }

    public function readSingle() {
        $query = "SELECT IMAGE_FILE FROM {$this->table} WHERE PRODUCT_ID = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
}
?>