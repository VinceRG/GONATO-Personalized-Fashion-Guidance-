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
        
        // s = string, s = string, i = int, d = decimal, s = string
        $success = $stmt->bind_param(
            "ssids", 
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

        // CRITICAL CHECK 3: Ensure rows were actually inserted
        if ($stmt->affected_rows === 0) {
            throw new Exception("Insert reported success but affected 0 rows. Check data types or required fields.");
        }

        $stmt->close();
        return true; // Success!
    }

    public function update() {
        // Build the query dynamically based on whether a new image is provided
        $query = "UPDATE {$this->table} SET PRODUCT_NAME = ?, DESCRIPTION = ?, BODY_SHAPE_ID = ?, PRICE = ?";
        
        // s, s, i, d
        $types  = "ssid";
        $params = [
            $this->product_name,
            $this->description,
            $this->body_shape_id,
            $this->price
        ];
        
        if ($this->image_file) {
            $query  .= ", IMAGE_FILE = ?";
            $types  .= "s";
            $params[] = $this->image_file;
        }

        $query  .= " WHERE PRODUCT_ID = ?";
        $types  .= "i";
        $params[] = $this->product_id;

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed (update): " . $this->conn->error);
        }
        
        if (!$stmt->bind_param($types, ...$params)) {
            throw new Exception("Bind failed (update): " . $stmt->error);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed (update): " . $stmt->error);
        }

        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected >= 0; // true even if no changes
    }

    public function readAll() {
        $query = "
            SELECT 
                p.PRODUCT_ID,
                p.PRODUCT_NAME,
                p.DESCRIPTION,
                p.PRICE,
                p.IMAGE_FILE
            FROM products p
            WHERE EXISTS (
                SELECT 1 
                FROM inventory i
                WHERE i.PRODUCT_ID = p.PRODUCT_ID
                  AND i.QUANTITY > 0
            )
            ORDER BY p.PRODUCT_NAME ASC
        ";

        $result = $this->conn->query($query);
        return $result;
    }

    public function readSingle() {
        $query = "SELECT IMAGE_FILE FROM {$this->table} WHERE PRODUCT_ID = ? LIMIT 0,1";
        $stmt  = $this->conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Prepare failed (readSingle): " . $this->conn->error);
        }

        $stmt->bind_param("i", $this->product_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();

        $stmt->close();
        return $row;
    }

    /**
     * Recommended products based on:
     *  - body_shapes.BODY_TYPE   = $bodyShapeName (e.g. 'PEAR')
     *  - seasons.SEASON_TYPE     = $seasonName    (e.g. 'Spring')
     * and must be in stock (inventory.QUANTITY > 0).
     */
    public function readRecommended(?string $bodyShapeName, ?string $seasonName = null, int $limit = 8) {
        // Base query with joins according to your actual schema
        $query = "
            SELECT DISTINCT
                p.PRODUCT_ID,
                p.PRODUCT_NAME,
                p.DESCRIPTION,
                p.PRICE,
                p.IMAGE_FILE
            FROM {$this->table} p
            JOIN body_shapes bs
                ON p.BODY_SHAPE_ID = bs.BODY_SHAPE_ID
            JOIN inventory i
                ON i.PRODUCT_ID = p.PRODUCT_ID
               AND i.QUANTITY > 0
            JOIN colors c
                ON i.COLOR_ID = c.COLOR_ID
            JOIN seasons s
                ON c.SEASON_ID = s.SEASON_ID
            WHERE 1 = 1
        ";

        $types  = '';
        $params = [];

        // Filter by body shape name (e.g. 'PEAR')
        if (!empty($bodyShapeName)) {
            $query    .= " AND bs.BODY_TYPE = ?";
            $types    .= 's';
            $params[]  = $bodyShapeName;
        }

        // Filter by season (e.g. 'Spring')
        if (!empty($seasonName)) {
            $query    .= " AND s.SEASON_TYPE = ?";
            $types    .= 's';
            $params[]  = $seasonName;
        }

        // Always limit results
        $query    .= " ORDER BY p.PRODUCT_NAME ASC LIMIT ?";
        $types    .= 'i';
        $params[]  = $limit;

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed (readRecommended): " . $this->conn->error);
        }

        if (!$stmt->bind_param($types, ...$params)) {
            throw new Exception("Bind failed (readRecommended): " . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed (readRecommended): " . $stmt->error);
        }

        $result = $stmt->get_result();
        // don't close $stmt yet if you want to keep using $result stream,
        // but in most cases it's okay:
        // $stmt->close();

        return $result;
    }
}
?>
