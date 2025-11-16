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

            // 🔒 Check if user is locked
            if ($user['STATUS'] == 1) {
                return ['success' => false, 'message' => 'Account is locked due to multiple failed login attempts.'];
            }

            // ✅ Correct password
            if (password_verify($password, $user['PASSWORD'])) {

                // Reset failed attempts after successful login
                $reset = $this->conn->prepare("UPDATE users SET FAILED_ATTEMPTS = 0 WHERE USER_ID = ?");
                $reset->bind_param("i", $user['USER_ID']);
                $reset->execute();

                // Generate OTP (no email sending)
                $otp = rand(100000, 999999);

                return [
                    'success' => true,
                    'step' => 'otp_verification',
                    'user' => $user,
                    'otp' => $otp
                ];
            } 
            // ❌ Incorrect password
            else {
                $failedAttempts = $user['FAILED_ATTEMPTS'] + 1;

                // Update failed attempts
                $update = $this->conn->prepare("UPDATE users SET FAILED_ATTEMPTS = ? WHERE USER_ID = ?");
                $update->bind_param("ii", $failedAttempts, $user['USER_ID']);
                $update->execute();

                // Lock account after 3 failed attempts
                if ($failedAttempts >= 3) {
                    $lock = $this->conn->prepare("UPDATE users SET STATUS = 1 WHERE USER_ID = ?");
                    $lock->bind_param("i", $user['USER_ID']);
                    $lock->execute();

                    return ['success' => false, 'message' => 'Account locked after 3 failed login attempts. Please contact support.'];
                }

                return ['success' => false, 'message' => "Incorrect password. Attempt $failedAttempts of 3."];
            }
        } 
        // ❌ Account not found
        else {
            return ['success' => false, 'message' => 'Account not found.'];
        }
    }
}
