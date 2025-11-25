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
        $query = "INSERT INTO {$this->table} (PRODUCT_NAME, DESCRIPTION, BODY_SHAPE_ID, PRICE, IMAGE_FILE) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->product_name, $this->description, $this->body_shape_id, $this->price, $this->image_file]);
    }

    public function readAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY CREATED_AT DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
