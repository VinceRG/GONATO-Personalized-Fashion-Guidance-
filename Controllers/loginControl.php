<?php
class LoginController {
    public function index() {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // ✅ For now, no database check — just simulate login success
            if (!empty($username) && !empty($password)) {
                $message = "Successfully logged in!";
            } else {
                $message = "Please fill in all fields.";
            }
        }

        require_once './View/login.php';
    }
}
