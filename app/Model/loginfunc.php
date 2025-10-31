<?php
require_once __DIR__ . '/../Core/Database.php';

class User {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function login($usernameOrEmail, $password) {
        $stmt = $this->conn->prepare("
            SELECT * FROM users 
            WHERE USERNAME = ? OR EMAIL = ?
        ");
        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['PASSWORD'])) {
                // Generate OTP (no email sending)
                $otp = rand(100000, 999999);

                return [
                    'success' => true,
                    'step' => 'otp_verification',
                    'user' => $user,
                    'otp' => $otp
                ];
            } else {
                return ['success' => false, 'message' => 'Incorrect password.'];
            }
        } else {
            return ['success' => false, 'message' => 'Account not found.'];
        }
    }
}
