<?php
// app/Controllers/forgotControl.php
require_once __DIR__ . '/../Model/forgotfunc.php';

class ForgotController {

    private $forgotModel;
    
    const MAX_ATTEMPTS = 3;
    const OTP_EXPIRY = 120; // 2 minutes
    const BLOCK_DURATION = 120; 

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->forgotModel = new ForgotModel();
    }

    public function index() {
        $message = '';
        $messageType = '';

        // ===============================
        // STEP 1: SEND OTP
        // ===============================
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && !isset($_POST['otp']) && !isset($_POST['new_password'])) {

            $email = trim($_POST['email']);

            $recaptchaSecret = "6LeCugUsAAAAAPih7SIRz0eeTuJ19s6LJVpUcgKC"; // Replace with your secret
            $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

            if (empty($recaptchaResponse)) {
                $message = "Please verify that you are not a robot.";
                $messageType = "error";
                require_once __DIR__ . '/../View/forgot.php';
                return;
            }

            $verify = file_get_contents(
                "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}"
            );
            $captchaSuccess = json_decode($verify);

            if (!$captchaSuccess->success) {
                $message = "reCAPTCHA verification failed. Please try again.";
                $messageType = "error";
                require_once __DIR__ . '/../View/forgot.php';
                return;
            }

            if (empty($email)) {
                $message = "Please enter your email.";
                $messageType = "error";
                require_once __DIR__ . '/../View/forgot.php';
                return;
            }
            $result = $this->forgotModel->sendResetOTP($email);
            if ($result['success']) {
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_otp'] = $result['otp'];
                $_SESSION['reset_otp_time'] = time();
                $_SESSION['otp_attempts'] = 0;
                $_SESSION['otp_blocked_until'] = null;

                $message = "OTP successfully sent to your email.";
                $messageType = "success";

                require_once __DIR__ . '/../View/forgot_verify.php';
                return;
            } else {
                $message = $result['message'];
                $messageType = "error";
                require_once __DIR__ . '/../View/forgot.php';
                return;
            }
        }

        // ===============================
// STEP 2: VERIFY OTP
// ===============================
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp']) && !isset($_POST['new_password'])) {

    $enteredOtp = trim($_POST['otp']);
    $storedOtp = $_SESSION['reset_otp'] ?? null;
    $otpTimestamp = $_SESSION['reset_otp_time'] ?? 0;

    // Check if OTP exists and is not expired
    if (!$storedOtp || (time() - $otpTimestamp) > self::OTP_EXPIRY) {
        $this->clearOTPSession();
        $message = "OTP has expired. Please request a new one.";
        $messageType = "error";
        require_once __DIR__ . '/../View/forgot.php';
        return;
    }

    // Check OTP format
    if (!preg_match('/^\d{6}$/', $enteredOtp)) {
        $message = "Invalid OTP format. Enter 6 digits.";
        $messageType = "error";
        require_once __DIR__ . '/../View/forgot_verify.php';
        return;
    }

    // Verify OTP
    if ($enteredOtp == $storedOtp) {
        unset($_SESSION['reset_otp'], $_SESSION['reset_otp_time']);
        $_SESSION['otp_verified'] = true;
        require_once __DIR__ . '/../View/reset_password.php';
        return;
    } else {
        // Wrong OTP message
        $message = "Incorrect OTP. Please try again.";
        $messageType = "error";
        require_once __DIR__ . '/../View/forgot_verify.php';
        return;
    }
}
        // ===============================
        // STEP 3: RESET PASSWORD
        // ===============================
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
            $email = $_SESSION['reset_email'] ?? null;
            $otpVerified = $_SESSION['otp_verified'] ?? false;
            $newPassword = trim($_POST['new_password']);
            $confirmPassword = trim($_POST['confirm_password']);

            if (!$email || !$otpVerified) {
                $this->clearOTPSession();
                $message = "Invalid session. Please start the password reset process again.";
                $messageType = "error";
                require_once __DIR__ . '/../View/login.php';
                return;
            }

            if (empty($newPassword) || empty($confirmPassword)) {
                $message = "Please fill in all password fields.";
                $messageType = "error";
                require_once __DIR__ . '/../View/reset_password.php';
                return;
            }

            if ($newPassword !== $confirmPassword) {
                $message = "Passwords do not match.";
                $messageType = "error";
                require_once __DIR__ . '/../View/reset_password.php';
                return;
            }

            if (strlen($newPassword) < 6) {
                $message = "Password must be at least 6 characters.";
                $messageType = "error";
                require_once __DIR__ . '/../View/reset_password.php';
                return;
            }

            $updated = $this->forgotModel->updatePassword($email, $newPassword);

            if ($updated) {
                $message = "Password updated successfully! Redirecting to login...";
                $messageType = "success";
                $this->clearOTPSession();
                unset($_SESSION['otp_verified']);

                echo "<script>setTimeout(function(){ window.location.href='index.php?page=login'; },2000);</script>";
            } else {
                $message = "Failed to update password. Please try again.";
                $messageType = "error";
            }

            require_once __DIR__ . '/../View/reset_password.php';
            return;
        }

        // ===============================
        // DEFAULT: SHOW EMAIL INPUT PAGE
        // ===============================
        require_once __DIR__ . '/../View/forgot.php';
    }

    private function clearOTPSession() {
        unset($_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_otp_time']);
        unset($_SESSION['otp_attempts'], $_SESSION['otp_blocked_until'], $_SESSION['otp_verified']);
    }
}
?>
