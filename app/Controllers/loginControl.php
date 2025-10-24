<?php
// app/Controllers/loginControl.php

class LoginController {
    public function index() {
        $message = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (!empty($username) && !empty($password)) {
                $message = "Successfully logged in!";
            } else {
                $message = "Please fill in all fields.";
            }
        }

        // ✅ Correct view path
        require_once __DIR__ . '/../View/login.php';
    }
}
