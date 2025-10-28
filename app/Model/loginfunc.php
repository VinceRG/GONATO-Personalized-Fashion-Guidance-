<?php
// GONATO-Personalized-Fashion-Guidance-/Model/loginfunc.php
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

    // NEW METHOD: Login function
    public function login($usernameOrEmail, $password) {
        // Check if input is email or username
        $stmt = $this->conn->prepare("
            SELECT * FROM users 
            WHERE USERNAME = ? OR EMAIL = ?
        ");
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['PASSWORD'])) {
                return [
                    'success' => true,
                    'user' => $user
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Incorrect password.'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'Account not found. Please sign up first.'
            ];
        }
    }

    public function register($firstname, $lastname, $username, $email, $address, $contact_num, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("
            INSERT INTO users (FIRST_NAME, LAST_NAME, USERNAME, EMAIL, ADDRESS, CONTACTS, PASSWORD)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss", $firstname, $lastname, $username, $email, $address, $contact_num, $hashedPassword);
        return $stmt->execute();
    }
}