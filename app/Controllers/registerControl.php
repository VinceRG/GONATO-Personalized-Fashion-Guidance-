<?php
// Controller/registerControl.php
require_once __DIR__ . '/../Model/registerfunc.php';

class RegisterController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Main index method - handles both page display and form submission
     */
    public function index() {
        // Handle AJAX requests for checking username/email existence
        if ($this->isAjaxRequest()) {
            $this->handleAjaxCheck();
            return;
        }

        // Initialize variables for the view
        $error = "";
        $success = "";
        $formData = [];

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$this->isAjaxRequest()) {
            $result = $this->processRegistration($_POST);
            
            if ($result['success']) {
                $success = $result['message'];
                header('Location: index.php?page=login&registered=1');
                exit;
            } else {
                $error = $result['message'];
                $formData = $_POST;
            }
        }

        // Load the view
        require_once __DIR__ . '/../View/register.php';
    }

    /**
     * Process registration form submission
     */
    private function processRegistration($postData) {
        /*
        |--------------------------------------------------------------------------
        |  STEP 1: Validate Terms and Conditions Acceptance
        |--------------------------------------------------------------------------
        */
        if (!isset($postData['terms']) || $postData['terms'] !== 'on') {
            return [
                'success' => false,
                'message' => 'You must accept the Terms and Conditions to register.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        |  STEP 2: Validate reCAPTCHA
        |--------------------------------------------------------------------------
        */
        $recaptchaSecret = "6LeCugUsAAAAAPih7SIRz0eeTuJ19s6LJVpUcgKC"; 
        $recaptchaResponse = $postData['g-recaptcha-response'] ?? '';

        if (empty($recaptchaResponse)) {
            return [
                'success' => false,
                'message' => 'Please complete the reCAPTCHA verification.'
            ];
        }

        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}"
        );
        $captchaSuccess = json_decode($verify);

        if (!$captchaSuccess->success) {
            return [
                'success' => false,
                'message' => 'reCAPTCHA verification failed. Please try again.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        |  STEP 3: Collect and sanitize input data
        |--------------------------------------------------------------------------
        */
        $firstname = $this->sanitizeInput($postData['firstname'] ?? '');
        $lastname = $this->sanitizeInput($postData['lastname'] ?? '');
        $username = $this->sanitizeInput($postData['username'] ?? '');
        $email = $this->sanitizeInput($postData['email'] ?? '');
        $contact_num = $this->sanitizeInput($postData['contact_num'] ?? '');
        $password = $postData['password'] ?? '';
        $confirmPassword = $postData['confirmPassword'] ?? '';

        // Address fields
        $street_address = $this->sanitizeInput($postData['street_address'] ?? '');
        $apartment = $this->sanitizeInput($postData['apartment'] ?? '');
        $province = $this->sanitizeInput($postData['province'] ?? '');
        $city = $this->sanitizeInput($postData['city'] ?? '');
        $barangay = $this->sanitizeInput($postData['barangay'] ?? '');
        $postal_code = $this->sanitizeInput($postData['postal_code'] ?? '');

        $addressData = [
            'street_address' => $street_address,
            'apartment' => $apartment,
            'province' => $province,
            'city' => $city,
            'barangay' => $barangay,
            'postal_code' => $postal_code
        ];

        /*
        |--------------------------------------------------------------------------
        |  STEP 4: Validate all registration data
        |--------------------------------------------------------------------------
        */
        $validationResult = $this->validateRegistrationData(
            $firstname, $lastname, $username, $email, 
            $addressData, $contact_num, $password, $confirmPassword
        );

        if (!$validationResult['valid']) {
            return [
                'success' => false,
                'message' => $validationResult['error']
            ];
        }

        /*
        |--------------------------------------------------------------------------
        |  STEP 5: Check username/email availability
        |--------------------------------------------------------------------------
        */
        if ($this->userModel->usernameExists($username)) {
            return ['success' => false, 'message' => 'Username is already taken.'];
        }

        if ($this->userModel->emailExists($email)) {
            return ['success' => false, 'message' => 'Email is already registered.'];
        }

        /*
        |--------------------------------------------------------------------------
        |  STEP 6: Register user
        |--------------------------------------------------------------------------
        */
        try {
            $registered = $this->userModel->register(
                $firstname, $lastname, $username, 
                $email, $addressData, $contact_num, $password
            );

            if ($registered) {
                return [
                    'success' => true,
                    'message' => 'Account created successfully! You can now login.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Registration failed. Please try again.'
                ];
            }
        } catch (Exception $e) {
            error_log('Registration error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during registration. Please try again later.'
            ];
        }
    }

    /**
     * Validate all registration data server-side
     */
    private function validateRegistrationData(
        $firstname, $lastname, $username, $email, 
        $addressData, $contact_num, $password, $confirmPassword
    ) {
        // First name validation
        if (empty($firstname) || strlen($firstname) < 2 || strlen($firstname) > 50) {
            return ['valid' => false, 'error' => 'Invalid first name.'];
        }
        if (!preg_match("/^[a-zA-Z\s\-']+$/", $firstname)) {
            return ['valid' => false, 'error' => 'First name contains invalid characters.'];
        }

        // Last name validation
        if (empty($lastname) || strlen($lastname) < 2 || strlen($lastname) > 50) {
            return ['valid' => false, 'error' => 'Invalid last name.'];
        }
        if (!preg_match("/^[a-zA-Z\s\-']+$/", $lastname)) {
            return ['valid' => false, 'error' => 'Last name contains invalid characters.'];
        }

        // Username validation
        if (empty($username) || strlen($username) < 3 || strlen($username) > 20) {
            return ['valid' => false, 'error' => 'Username must be 3-20 characters.'];
        }
        if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
            return ['valid' => false, 'error' => 'Username can only contain letters, numbers, and underscores.'];
        }

        // Email validation
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'Invalid email address.'];
        }
        if (strlen($email) > 100) {
            return ['valid' => false, 'error' => 'Email is too long.'];
        }

        // Address validation
        $street_address = $addressData['street_address'];
        $province = $addressData['province'];
        $city = $addressData['city'];
        $barangay = $addressData['barangay'];
        $postal_code = $addressData['postal_code'];
        $apartment = $addressData['apartment'];

        if (empty($street_address) || strlen($street_address) < 5 || strlen($street_address) > 150) {
            return ['valid' => false, 'error' => 'Street address must be 5-150 characters.'];
        }
        if (empty($province)) {
            return ['valid' => false, 'error' => 'Province is required.'];
        }
        if (empty($city)) {
            return ['valid' => false, 'error' => 'City/Town is required.'];
        }
        if (empty($barangay)) {
            return ['valid' => false, 'error' => 'Barangay is required.'];
        }
        if (empty($postal_code)) {
            return ['valid' => false, 'error' => 'Postal code is required.'];
        }
        if (!preg_match("/^[0-9]{4,10}$/", $postal_code)) {
            return ['valid' => false, 'error' => 'Postal code must be 4-10 digits.'];
        }
        if (!empty($apartment) && strlen($apartment) > 50) {
            return ['valid' => false, 'error' => 'Apartment/Suite must not exceed 50 characters.'];
        }

        // Contact number validation
        if (!preg_match("/^[0-9]{11}$/", $contact_num)) {
            return ['valid' => false, 'error' => 'Contact number must be exactly 11 digits.'];
        }
        if (!preg_match("/^09[0-9]{9}$/", $contact_num)) {
            return ['valid' => false, 'error' => 'Invalid Philippine mobile number format (must start with 09).'];
        }

        // Password validation
        if (empty($password)) {
            return ['valid' => false, 'error' => 'Password is required.'];
        }
        if (empty($confirmPassword)) {
            return ['valid' => false, 'error' => 'Please confirm your password.'];
        }
        if (strlen($password) < 8) {
            return ['valid' => false, 'error' => 'Password must be at least 8 characters.'];
        }
        if (!preg_match("/[A-Z]/", $password)) {
            return ['valid' => false, 'error' => 'Password must contain an uppercase letter.'];
        }
        if (!preg_match("/[a-z]/", $password)) {
            return ['valid' => false, 'error' => 'Password must contain a lowercase letter.'];
        }
        if (!preg_match("/[0-9]/", $password)) {
            return ['valid' => false, 'error' => 'Password must contain a number.'];
        }
        if (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) {
            return ['valid' => false, 'error' => 'Password must contain a special character.'];
        }

        if ($password !== $confirmPassword) {
            return ['valid' => false, 'error' => 'Passwords do not match.'];
        }

        return ['valid' => true, 'error' => ''];
    }

    /**
     * AJAX username/email check
     */
    private function handleAjaxCheck() {
        header('Content-Type: application/json');
        
        $response = ['exists' => false];

        try {
            if (isset($_POST['username'])) {
                $username = $this->sanitizeInput($_POST['username']);
                if (!empty($username) && strlen($username) >= 3) {
                    $response['exists'] = $this->userModel->usernameExists($username);
                }
            } elseif (isset($_POST['email'])) {
                $email = $this->sanitizeInput($_POST['email']);
                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $response['exists'] = $this->userModel->emailExists($email);
                }
            }
        } catch (Exception $e) {
            error_log('AJAX check error: ' . $e->getMessage());
            $response['error'] = 'Error checking availability';
            http_response_code(500);
        }

        echo json_encode($response);
        exit;
    }

    /** Check if request is AJAX */
    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /** Clean input */
    private function sanitizeInput($input) {
        return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    }
}
?>