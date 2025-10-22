<?php
// GONATO-Personalized-Fashion-Guidance-/Controllers/RegisterController.php
require_once __DIR__ . '/../Model/registerfunc.php';

class RegisterController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        $error = "";
        $success = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $firstname = trim($_POST['firstname']);
            $lastname = trim($_POST['lastname']);
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $address = trim($_POST['address']);
            $contact_num = trim($_POST['contact_num']);
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirmPassword'];

            // Validation
            if ($password !== $confirmPassword) {
                $error = "Passwords do not match.";
            } elseif ($this->userModel->usernameExists($username)) {
                $error = "Username already taken.";
            } elseif ($this->userModel->emailExists($email)) {
                $error = "Email already registered.";
            } else {
                if ($this->userModel->register($firstname, $lastname, $username, $email, $address, $contact_num, $password)) {
                    $success = "Account created successfully! You can now login.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        }

        // ✅ Only include once, after processing
        require_once __DIR__ . '/../View/register.php';
    }
}
