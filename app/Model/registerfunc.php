<?php
// GONATO-Personalized-Fashion-Guidance-/Model/User.php
require_once __DIR__ . '/../Core/Database.php';

class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function usernameExists($username) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function register($firstname, $lastname, $username, $email, $address, $contact_num, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("
            INSERT INTO users (FIRST_NAME, LAST_NAME, USERNAME , EMAIL , ADDRESS, CONTACTS, PASSWORD)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss", $firstname, $lastname, $username, $email, $address, $contact_num, $hashedPassword);
        return $stmt->execute();
    }
}
