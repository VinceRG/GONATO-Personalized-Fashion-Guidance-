<?php
// app/Controllers/adminLoginControl.php

// Make sure Database is loaded
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Model/adminfunc.php';

class AdminLoginController {
    private $conn;
    private $dbError = null;  // define property to avoid dynamic property warnings

    public function __construct() {
        try {
            $this->conn = Database::connect();

            if (!$this->conn) {
                throw new Exception("Database connection failed.");
            }
        } catch (Exception $e) {
            $this->conn   = null;
            $this->dbError = $e->getMessage();
        }
    }

    public function index() {
        // DO NOT call session_start() here if index.php already does it

        $message = '';
        $messageType = '';

        // If DB connection failed, show error and stop
        if ($this->dbError !== null) {
            $message = $this->dbError;
            $messageType = 'error';
            require_once __DIR__ . '/../View/admin_login.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $message = "⚠️ Please fill in all fields.";
                $messageType = 'warning';
            } else {
                $stmt = $this->conn->prepare("SELECT * FROM admin WHERE USERNAME = ?");
                if (!$stmt) {
                    // handle prepare error cleanly
                    $message = "Database error: failed to prepare statement.";
                    $messageType = 'error';
                } else {
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        $admin = $result->fetch_assoc();

                        // NOTE: for now you’re using plain text passwords; later you should use password_verify()
                        if ($password === $admin['PASSWORD']) {
                            // Session must already be started in index.php
                            $_SESSION['admin_id']       = $admin['ADMIN_ID'];
                            $_SESSION['admin_username'] = $admin['USERNAME'];

                            header("Location: index.php?page=admin");
                            exit();
                        } else {
                            $message = "❌ Invalid admin password.";
                            $messageType = 'error';
                        }
                    } else {
                        $message = "⚠️ Admin not found.";
                        $messageType = 'error';
                    }

                    $stmt->close();
                }
            }
        }

        require_once __DIR__ . '/../View/admin_login.php';
    }
}
?>
