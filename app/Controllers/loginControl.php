<?php
// app/Controllers/loginControl.php
require_once __DIR__ . '/../Core/Database.php';

class LoginController {
    public function index() {
        session_start();
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!empty($username) && !empty($password)) {
                $conn = Database::connect();

                // 1️⃣ Check if admin first
                $stmt = $conn->prepare("SELECT * FROM admin WHERE USERNAME = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $adminResult = $stmt->get_result();

                if ($adminResult->num_rows > 0) {
                    $admin = $adminResult->fetch_assoc();

                    if ($password === $admin['PASSWORD']) {
                        $_SESSION['user'] = [
                            'id' => $admin['ADMIN_ID'],
                            'username' => $admin['USERNAME'],
                            'role' => 'admin'
                        ];
                        header("Location: /AMARELLE/GONATO-Personalized-Fashion-Guidance-/?page=admin");
                        exit();
                    } else {
                        $message = "Invalid admin password.";
                    }
                } else {
                    // 2️⃣ Check regular user
                    $stmt = $conn->prepare("SELECT * FROM users WHERE USERNAME = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $userResult = $stmt->get_result();

                    if ($userResult->num_rows > 0) {
                        $user = $userResult->fetch_assoc();

                        if (password_verify($password, $user['PASSWORD'])) {
                            $_SESSION['user'] = [
                                'id' => $user['USER_ID'],
                                'username' => $user['USERNAME'],
                                'role' => 'user'
                            ];
                            header("Location: /AMARELLE/GONATO-Personalized-Fashion-Guidance-/?page=features");
                            exit();
                        } else {
                            $message = "Invalid password.";
                        }
                    } else {
                        $message = "Account not found.";
                    }
                }
            } else {
                $message = "Please fill in all fields.";
            }
        }

        // Load login view
        require_once __DIR__ . '/../View/login.php';
    }
}
