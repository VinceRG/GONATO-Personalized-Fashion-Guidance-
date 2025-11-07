<?php
class Inventory {
    private $conn;
    private $table = "inventory";

    public $inventory_id;
    public $product_id;
    public $color_id;
    public $size;
    public $quantity;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addStock() {
        $query = "INSERT INTO {$this->table} (PRODUCT_ID, COLOR_ID, SIZE, QUANTITY) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->product_id, $this->color_id, $this->size, $this->quantity]);
    }

    public function updateStock() {
        $query = "UPDATE {$this->table} SET QUANTITY = ? WHERE INVENTORY_ID = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$this->quantity, $this->inventory_id]);
    }

    public function readAll() {
        $query = "SELECT i.*, p.PRODUCT_NAME, p.PRICE, c.COLOR_NAME
                  FROM {$this->table} i
                  JOIN products p ON i.PRODUCT_ID = p.PRODUCT_ID
                  JOIN colors c ON i.COLOR_ID = c.COLOR_ID
                  ORDER BY i.UPDATED_AT DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
