<?php
// app/Model/adminfunc.php
require_once __DIR__ . '/../Core/Database.php';

class Admin {
    private $conn;

    public function __construct() {
        try {
            $this->conn = Database::connect();

            if (!$this->conn) {
                throw new Exception("Database connection failed.");
            }
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    // ==================== ADMIN LOGIN ====================
    public function login($username, $password) {
        $query = "SELECT * FROM admin WHERE USERNAME = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Database error: failed to prepare statement.'
            ];
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            // ⚠️ Currently using plain-text comparison
            if ($password === $admin['PASSWORD']) {
                return ['success' => true, 'admin' => $admin];
            } else {
                return ['success' => false, 'message' => 'Incorrect password.'];
            }

            // If using password_hash():
            // if (password_verify($password, $admin['PASSWORD'])) { ... }
        }

        return ['success' => false, 'message' => 'Admin not found.'];
    }

    // ==================== FETCH USERS ====================
    public function getUsers() {
        $query = "SELECT USER_ID, USERNAME, EMAIL, CREATED_AT, STATUS FROM users";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Database error: failed to prepare statement.'
            ]);
            exit;
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = [
                'USER_ID'    => $row['USER_ID'],
                'USERNAME'   => $row['USERNAME'],
                'EMAIL'      => $row['EMAIL'],
                'CREATED_AT' => $row['CREATED_AT'],
                // 0 = Active, 1 = Locked
                'IS_LOCKED'  => (int)$row['STATUS'] === 1
            ];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data'    => $users
        ]);
        exit;
    }

    // ==================== LOCK / UNLOCK USER ====================
    public function toggleUserLock($userId, $isLocked) {
        // if current state is locked (true), we unlock => newStatus = 0
        // if current state is unlocked (false), we lock => newStatus = 1
        $newStatus = $isLocked ? 0 : 1;

        $stmt = $this->conn->prepare(
            "UPDATE users SET STATUS = ?, FAILED_ATTEMPTS = 0 WHERE USER_ID = ?"
        );

        if (!$stmt) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Database error: failed to prepare statement.'
            ]);
            exit;
        }

        $stmt->bind_param("ii", $newStatus, $userId);

        if ($stmt->execute()) {
            $message = $newStatus === 1
                ? 'User account locked.'
                : 'User account unlocked.';

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update user status.'
            ]);
        }
        exit;
    }
}
