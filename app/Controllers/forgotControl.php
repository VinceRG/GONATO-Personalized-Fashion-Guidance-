<?php
// app/Controllers/forgotControl.php

require_once __DIR__ . '/../Model/forgotfunc.php';

class ForgotController {

    private $forgotModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->forgotModel = new ForgotModel();
    }

    public function index() {
        $message = '';
        $messageType = '';

        // ✅ STEP 1: SEND OTP
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && !isset($_POST['otp']) && !isset($_POST['new_password'])) {

            $email = trim($_POST['email']);

            if (empty($email)) {
                $message = "Please enter your email.";
                $messageType = "error";
            } else {
                $result = $this->forgotModel->sendResetOTP($email);

                if ($result['success']) {
                    // ✅ Save OTP & time in session
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_otp'] = $result['otp'];
                    $_SESSION['reset_otp_time'] = time();

                    // ✅ Go to OTP verification form
                    require_once __DIR__ . '/../View/forgot_verify.php';
                    return;
                } else {
                    $message = $result['message'];
                    $messageType = "error";
                }
            }
        }

        // ✅ STEP 2: VERIFY OTP
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp']) && !isset($_POST['new_password'])) {

    $enteredOtp = trim($_POST['otp']);
    $storedOtp = $_SESSION['reset_otp'] ?? null;
    $otpTimestamp = $_SESSION['reset_otp_time'] ?? 0;

    // ✅ If session expired (OTP older than 5 minutes)
    if (!$storedOtp || (time() - $otpTimestamp) > 300) {

        // Clear session
        unset($_SESSION['otp_sent']);
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_otp']);
        unset($_SESSION['reset_otp_time']);

        // Show message and return to email form
        $message = "Session expired. Please request a new OTP.";
        $messageType = "error";

        require_once __DIR__ . '/../View/forgot.php';
        return;
    }
    // ✅ OTP correct → go to password reset page
    elseif ($enteredOtp == $storedOtp) {
        require_once __DIR__ . '/../View/reset_password.php';
        return;
    } 
    else {
        $message = "Invalid OTP. Please try again.";
        $messageType = "error";
    }
}

        // ✅ STEP 3: RESET PASSWORD
elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {

    $email = $_SESSION['reset_email'] ?? null;
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // ✅ If session expired
    if (!$email) {
        unset($_SESSION['otp_sent'], $_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_otp_time']);

        $message = "Session expired. Please request a new OTP.";
        $messageType = "error";

        require_once __DIR__ . '/../View/login.php';
        return;
    }

    // ✅ Passwords must match
    if ($newPassword !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageType = "error";
    }
    else {
        $updated = $this->forgotModel->updatePassword($email, $newPassword);

        if ($updated) {
            $message = "Password updated successfully. You may now log in.";
            $messageType = "success";

            unset($_SESSION['otp_sent'], $_SESSION['reset_email'], $_SESSION['reset_otp'], $_SESSION['reset_otp_time']);
        } else {
            $message = "Failed to update password. Please try again.";
            $messageType = "error";
        }
    }
}

        // ✅ DEFAULT: Show email input page
        require_once __DIR__ . '/../View/forgot.php';
    }
}
