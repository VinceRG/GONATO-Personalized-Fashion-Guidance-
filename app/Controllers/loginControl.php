<?php
require_once __DIR__ . '/../Model/loginfunc.php';

class LoginController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        session_start();
        $message = '';
        $messageType = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $message = "Please fill in all fields.";
                $messageType = 'error';
            } else {
                $loginResult = $this->userModel->login($username, $password);

                if ($loginResult['success']) {
                    // Successful login → store user info in session
                    $_SESSION['user_id'] = $loginResult['user']['USER_ID'];
                    $_SESSION['username'] = $loginResult['user']['USERNAME'];
                    $_SESSION['email'] = $loginResult['user']['EMAIL'];
                    $_SESSION['first_name'] = $loginResult['user']['FIRST_NAME'];
                    $_SESSION['last_name'] = $loginResult['user']['LAST_NAME'];

                    header("Location: index.php?page=features");
                    exit;
                } else {
                    $message = $loginResult['message'];
                    $messageType = 'error';
                }
            }
        }

        require_once __DIR__ . '/../View/login.php';
    }
}
