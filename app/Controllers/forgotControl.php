<?php
// app/Controllers/forgotControl.php
require_once __DIR__ . '/../Model/forgotfunc.php';

class ForgotController {
    private $userModel;

    public function __construct() {
        $this->userModel = new ForgotModel();
    }

    public function index() {
        session_start();
        $message = '';
        $messageType = '';

        // Step 1: Send OTP
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && !isset($_POST['otp'])) {
            $email = trim($_POST['email']);
            if (empty($email)) {
                $message = "Please enter your email.";
                $messageType = "error";
            } else {
                $result = $this->userModel->sendResetOTP($email);
                if ($result['success']) {
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_otp'] = $result['otp'];
                    $_SESSION['reset_otp_time'] = time();
                    require_once __DIR__ . '/../View/forgot_verify.php';
                    return;
                } else {
                    $message = $result['message'];
                    $messageType = "error";
                }
            }
        }

        // Step 2: Verify OTP
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp']) && !isset($_POST['new_password'])) {
            $enteredOtp = trim($_POST['otp']);
            $storedOtp = $_SESSION['reset_otp'] ?? null;
            $otpTime = $_SESSION['reset_otp_time'] ?? 0;

            if ($storedOtp && (time() - $otpTime) < 300) {
                if ($enteredOtp === $storedOtp) {
                    require_once __DIR__ . '/../View/reset_password.php';
                    return;
                } else {
                    $message = "Invalid OTP. Please try again.";
                    $messageType = "error";
                }
            } else {
                $message = "OTP expired. Please request a new one.";
                $messageType = "error";
                unset($_SESSION['reset_otp'], $_SESSION['reset_otp_time']);
            }
        }

        // Step 3: Update password
        elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
            $email = $_SESSION['reset_email'] ?? null;
            $newPassword = trim($_POST['new_password']);

            if ($email && $newPassword) {
                if ($this->userModel->updatePassword($email, $newPassword)) {
                    $message = "Password successfully updated. You can now log in.";
                    $messageType = "success";
                    unset($_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_otp_time']);
                } else {
                    $message = "Failed to update password.";
                    $messageType = "error";
                }
            }
        }

        require_once __DIR__ . '/../View/forgot.php';
    }
}
