<?php
require_once __DIR__ . '/../Core/Database.php';

class Admin {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    // ==================== ADMIN LOGIN ====================
    public function login($username, $password) {
        $query = "SELECT * FROM admin WHERE USERNAME = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            // ⚠️ If passwords are not hashed, compare directly
            if ($password === $admin['PASSWORD']) {
                return ['success' => true, 'admin' => $admin];
            } else {
                return ['success' => false, 'message' => 'Incorrect password.'];
            }

            // ✅ If using password_hash(), use:
            // if (password_verify($password, $admin['PASSWORD'])) { ... }
        } else {
            return ['success' => false, 'message' => 'Admin not found.'];
        }
    }

    // ==================== FETCH USERS ====================
    public function getUsers() {
        $query = "SELECT USER_ID, USERNAME, EMAIL, CREATED_AT, STATUS FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                'USER_ID' => $row['USER_ID'],
                'USERNAME' => $row['USERNAME'],
                'EMAIL' => $row['EMAIL'],
                'CREATED_AT' => $row['CREATED_AT'],
                // Convert STATUS: 0 = Active, 1 = Locked
                'IS_LOCKED' => $row['STATUS'] == 1
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($users);
        exit;
    }

    // ==================== LOCK / UNLOCK USER ====================
    public function toggleUserLock($userId, $isLocked) {
        $newStatus = $isLocked ? 0 : 1; // 1 = lock, 0 = unlock
        $stmt = $this->conn->prepare("UPDATE users SET STATUS = ?, FAILED_ATTEMPTS = 0 WHERE USER_ID = ?");
        $stmt->bind_param("ii", $newStatus, $userId);

        if ($stmt->execute()) {
            $message = $newStatus ? 'User account locked.' : 'User account unlocked.';
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user status.']);
        }
        exit;
    }
}
