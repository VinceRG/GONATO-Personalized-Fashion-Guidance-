<?php
require_once __DIR__ . '/../Core/Database.php';

class User {
    /** @var mysqli */
    private $conn;

    public function __construct() {
        $this->conn = Database::connect(); // Make sure this returns mysqli
    }

    /**
     * Unified login: checks admin first, then users.
     *
     * Returns on success:
     *  - ['success' => true, 'role' => 'admin', 'admin' => [...]]
     *  - ['success' => true, 'role' => 'user',  'user'  => [...]]
     *
     * On failure:
     *  - ['success' => false, 'message' => '...', 'remainingAttempts' => ?, 'isLocked' => ?]
     */
    public function login($usernameOrEmail, $password) {
    // ==============================
    // 1. TRY ADMIN / STAFF FIRST
    // ==============================
    $adminStmt = $this->conn->prepare("
        SELECT ADMIN_ID, USERNAME, PASSWORD, ROLE, IS_ACTIVE
        FROM admin
        WHERE USERNAME = ?
        LIMIT 1
    ");
    if ($adminStmt) {
        $adminStmt->bind_param("s", $usernameOrEmail);
        $adminStmt->execute();
        $adminResult = $adminStmt->get_result();

        if ($adminResult && $adminResult->num_rows === 1) {
            $admin = $adminResult->fetch_assoc();

            // Check if admin/staff is active
            if ((int)$admin['IS_ACTIVE'] !== 1) {
                return [
                    'success' => false,
                    'message' => 'Admin account is disabled. Please contact the system owner.',
                    'isLocked' => false
                ];
            }

            // IMPORTANT: ideally PASSWORD is a hash from password_hash()
            $passwordMatches =
                password_verify($password, $admin['PASSWORD']) ||
                $admin['PASSWORD'] === $password; // temporary fallback if DB still has plain text

            if ($passwordMatches) {
                return [
                    'success' => true,
                    'role'    => 'admin',  // high-level role for LoginController
                    'admin'   => $admin    // contains ROLE = 'super_admin' or 'staff'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Incorrect username or password.',
                    'isLocked' => false
                ];
            }
        }
    }

        // ==================================
        // 2. FALLBACK: NORMAL USER LOGIN
        // ==================================
        $stmt = $this->conn->prepare("
            SELECT * FROM users 
            WHERE USERNAME = ? OR EMAIL = ?
            LIMIT 1
        ");
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Database error: ' . $this->conn->error
            ];
        }

        $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Account locked?
            if ($user['STATUS'] == 1) {
                return [
                    'success'  => false,
                    'message'  => 'Account is locked due to multiple failed login attempts.',
                    'isLocked' => true
                ];
            }

            if (password_verify($password, $user['PASSWORD'])) {

                // Reset failed attempts after successful login
                $reset = $this->conn->prepare("UPDATE users SET FAILED_ATTEMPTS = 0 WHERE USER_ID = ?");
                if ($reset) {
                    $reset->bind_param("i", $user['USER_ID']);
                    $reset->execute();
                }

                return [
                    'success' => true,
                    'role'    => 'user',
                    'user'    => $user
                ];

            } else {
                $failedAttempts = (int)$user['FAILED_ATTEMPTS'] + 1;

                $update = $this->conn->prepare("UPDATE users SET FAILED_ATTEMPTS = ? WHERE USER_ID = ?");
                if ($update) {
                    $update->bind_param("ii", $failedAttempts, $user['USER_ID']);
                    $update->execute();
                }

                $remainingAttempts = max(0, 3 - $failedAttempts);

                if ($failedAttempts >= 3) {
                    $lock = $this->conn->prepare("UPDATE users SET STATUS = 1 WHERE USER_ID = ?");
                    if ($lock) {
                        $lock->bind_param("i", $user['USER_ID']);
                        $lock->execute();
                    }

                    return [
                        'success'           => false,
                        'message'           => 'Account locked after 3 failed login attempts. Please contact support.',
                        'remainingAttempts' => 0,
                        'isLocked'          => true
                    ];
                }

                return [
                    'success'           => false,
                    'message'           => 'Incorrect username or password.',
                    'remainingAttempts' => $remainingAttempts,
                    'isLocked'          => false
                ];
            }
        } else {
            // No admin, no user
            return [
                'success'  => false,
                'message'  => 'Incorrect username or password.',
                'isLocked' => false
            ];
        }
    }
}
