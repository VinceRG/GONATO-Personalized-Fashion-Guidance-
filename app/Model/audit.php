<?php
class Audit {
    private $conn;
    private $table = "audit_trail";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function log($adminId, $action, $description = "") {
        $query = "INSERT INTO {$this->table} (ADMIN_ID, ACTION, DESCRIPTION, IP_ADDRESS)
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);
        $ip = $_SERVER['REMOTE_ADDR'];

        $stmt->bind_param("isss", $adminId, $action, $description, $ip);
        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT a.*, ad.USERNAME 
                  FROM audit_trail a
                  LEFT JOIN admin ad ON a.ADMIN_ID = ad.ADMIN_ID
                  ORDER BY a.CREATED_AT DESC";

        $result = $this->conn->query($query);
        return $result;
    }
}
