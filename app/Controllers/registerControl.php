<?php
// GONATO-Personalized-Fashion-Guidance-/Controllers/RegisterController.php
require_once __DIR__ . '/../Model/registerfunc.php';

class RegisterController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        // Handle AJAX requests for checking existence
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $this->handleAjaxCheck();
            return;
        }

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
                $error = "Passwords do not matc.";
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

        require_once __DIR__ . '/../View/register.php';
    }

    /**
     * Handle AJAX requests for checking username/email existence
     */
    private function handleAjaxCheck() {
        header('Content-Type: application/json');
        
        $response = ['exists' => false];

        try {
            if (isset($_POST['username'])) {
                $username = trim($_POST['username']);
                if (!empty($username)) {
                    $response['exists'] = $this->userModel->usernameExists($username);
                }
            } elseif (isset($_POST['email'])) {
                $email = trim($_POST['email']);
                if (!empty($email)) {
                    $response['exists'] = $this->userModel->emailExists($email);
                }
            }
        } catch (Exception $e) {
            $response['error'] = 'Error checking availability';
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Alternative method if you want a separate endpoint
     */
    public function checkExists() {
        $this->handleAjaxCheck();
    }
}
?>