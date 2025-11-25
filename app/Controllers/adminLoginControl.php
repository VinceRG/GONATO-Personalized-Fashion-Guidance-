<?php
require_once __DIR__ . '/../Model/adminfunc.php';

class AdminLoginController {
    private $conn;

    public function __construct() {
        try {
            $this->conn = Database::connect(); 

            if (!$this->conn) {
                throw new Exception("Database connection failed.");
            }
        } catch (Exception $e) {
            $this->conn = null;
            $this->dbError = $e->getMessage();
        }
    }

    public function index() {
        session_start();
        $message = '';
        $messageType = '';

        if (isset($this->dbError)) {
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
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $admin = $result->fetch_assoc();

                    if ($password === $admin['PASSWORD']) {
                        $_SESSION['admin_id'] = $admin['ADMIN_ID'];
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
            }
        }

        require_once __DIR__ . '/../View/admin_login.php';
    }
}
?>
