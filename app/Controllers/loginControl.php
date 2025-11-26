<?php
require_once __DIR__ . '/../Model/loginfunc.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class LoginController {
    private $userModel;
    const OTP_EXPIRY = 120; // seconds

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $message = '';
        $messageType = '';
        $openOtpModal = false;
        $remainingAttempts = null;
        $isLocked = false;

        // ============================
        // STEP 2: AJAX OTP VERIFICATION
        // ============================
        if (isset($_GET['action']) && $_GET['action'] === 'verifyOtp') {
            header('Content-Type: application/json');

            $enteredOtp = trim($_POST['otp'] ?? '');
            $storedOtp  = $_SESSION['login_otp'] ?? null;
            $otpTime    = $_SESSION['login_otp_time'] ?? 0;

            if (!isset($_SESSION['login_otp_attempts'])) {
                $_SESSION['login_otp_attempts'] = 0;
            }

            // Too many wrong OTP attempts → expire OTP and force re-login
            if ($_SESSION['login_otp_attempts'] >= 5) {
                $this->clearOtpSession();
                unset($_SESSION['temp_user']);
                echo json_encode(['success' => false, 'expired' => true]);
                exit;
            }

            if (!$storedOtp || (time() - $otpTime) > self::OTP_EXPIRY) {
                $this->clearOtpSession();
                unset($_SESSION['temp_user']);
                echo json_encode(['success' => false, 'expired' => true]);
                exit;
            }

            if ($enteredOtp === (string)$storedOtp) {
                $user = $_SESSION['temp_user'] ?? null;

                if ($user) {
                    session_regenerate_id(true);

                    $_SESSION['user_id']    = $user['USER_ID'];
                    $_SESSION['username']   = $user['USERNAME'];
                    $_SESSION['email']      = $user['EMAIL'];
                    $_SESSION['first_name'] = $user['FIRST_NAME'];
                    $_SESSION['last_name']  = $user['LAST_NAME'];
                    $_SESSION['logged_in']  = true;

                    if (!empty($_SESSION['remember_me'])) {
                        $this->setRememberMeCookies($user);
                        unset($_SESSION['remember_me']);
                    }

                    $this->setLastLoginCookie();

                    unset($_SESSION['temp_user']);
                    $this->clearOtpSession();
                    unset($_SESSION['login_otp_attempts']);

                    echo json_encode(['success' => true]);
                    exit;
                }
            } else {
                $_SESSION['login_otp_attempts']++;
            }

            echo json_encode(['success' => false]);
            exit;
        }

        // ============================
        // STEP 1: USERNAME / PASSWORD
        // ============================
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $message = "Please fill in all fields.";
                $messageType = 'error';
            } else {
                $loginResult = $this->userModel->login($username, $password);

                if ($loginResult['success']) {
                    // =====================================
                    // ADMIN LOGIN BRANCH (NO OTP HERE)
                    // =====================================
                    if (!empty($loginResult['role']) && $loginResult['role'] === 'admin') {
                        $admin = $loginResult['admin'];

                        session_regenerate_id(true);

                        $_SESSION['admin_id']       = $admin['ADMIN_ID'];
                        $_SESSION['admin_username'] = $admin['USERNAME'];
                        $_SESSION['is_admin']       = true;

                        // ✅ Redirect to your existing admin route
                        header('Location: index.php?page=admin');
                        exit;
                    }

                    // =====================================
                    // NORMAL USER LOGIN → OTP FLOW
                    // =====================================
                    if (!empty($loginResult['role']) && $loginResult['role'] === 'user') {
                        $_SESSION['temp_user'] = $loginResult['user'];

                        $rememberMe = !empty($_POST['remember_me']);
                        $_SESSION['remember_me'] = $rememberMe;

                        // Generate OTP (session only, no DB)
                        $otp = rand(100000, 999999);
                        $_SESSION['login_otp']          = $otp;
                        $_SESSION['login_otp_time']     = time();
                        $_SESSION['login_otp_attempts'] = 0;

                        // Send OTP email
                        $email = $loginResult['user']['EMAIL'];
                        $this->sendLoginOtpEmail($email, $otp);

                        $openOtpModal = true;
                        $message = "Please verify your identity. An OTP has been sent to your email.";
                        $messageType = 'info';
                    }

                } else {
                    // LOGIN FAILED
                    $message = $loginResult['message'] ?? 'Login failed.';
                    $messageType = 'error';

                    if (isset($loginResult['remainingAttempts'])) {
                        $remainingAttempts = $loginResult['remainingAttempts'];
                    }
                    if (isset($loginResult['isLocked']) && $loginResult['isLocked'] === true) {
                        $isLocked = true;
                    }
                }
            }
        }

        require_once __DIR__ . '/../View/login.php';
    }

    private function clearOtpSession() {
        unset($_SESSION['login_otp'], $_SESSION['login_otp_time'], $_SESSION['login_otp_attempts']);
    }

    private function setRememberMeCookies(array $user) {
        $expiry = time() + (60 * 60 * 24 * 30); // 30 days
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

        setcookie(
            'remember_username',
            $user['USERNAME'],
            [
                'expires'  => $expiry,
                'path'     => '/',
                'secure'   => $secure,
                'httponly' => false,
                'samesite' => 'Lax',
            ]
        );
    }

    private function setLastLoginCookie() {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');

        setcookie(
            'last_login',
            date('Y-m-d H:i:s'),
            [
                'expires'  => time() + (60 * 60 * 24 * 30),
                'path'     => '/',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]
        );
    }

    private function sendLoginOtpEmail($email, $otp) {
        require_once __DIR__ . '/../../vendor/autoload.php';

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'amarelle2025@gmail.com';
            $mail->Password   = 'hdzk sgjm jnbx kipl'; // ⚠ move to ENV in real app
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('amarelle2025@gmail.com', 'Amarelle');
            $mail->addAddress($email);

            $otpDigits = str_split($otp);

            $mail->isHTML(true);
            $mail->Subject = 'Login Verification - Amarelle';
            $mail->Body    = $this->getLoginEmailTemplate($email, $otpDigits);

            $mail->send();
        } catch (Exception $e) {
            error_log("Login OTP Mailer Error: " . $mail->ErrorInfo);
        }
    }

    private function getLoginEmailTemplate($email, $otpDigits) {
        // Reuse your nice template, just change wording for login
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    background-color: #f5f5f5;
                    padding: 40px 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                }
                .header {
                    background: linear-gradient(135deg, #A68763 0%, #D7C9AE 100%);
                    padding: 40px 30px;
                    text-align: center;
                }
                .logo {
                    font-family: "Georgia", serif;
                    font-size: 32px;
                    font-weight: 300;
                    color: white;
                    letter-spacing: 2px;
                    margin-bottom: 10px;
                }
                .content { padding: 50px 40px; text-align: center; }
                h1 { font-size: 28px; font-weight: 600; color: #1a1a1a; margin-bottom: 20px; }
                .body-text { font-size: 16px; color: #666; line-height: 1.6; margin-bottom: 10px; }
                .timer-text { font-size: 14px; color: #999; margin-bottom: 40px; }
                .otp-container { text-align: center; margin: 40px 0; }
                .otp-digit {
                    display: inline-block;
                    width: 60px;
                    height: 70px;
                    background: #f8f8f8;
                    border: 2px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 32px;
                    font-weight: 600;
                    color: #1a1a1a;
                    line-height: 70px;
                    text-align: center;
                    margin: 0 6px;
                    vertical-align: middle;
                }
                .info-section {
                    margin-top: 40px;
                    padding: 25px;
                    background: #f9f9f9;
                    border-radius: 8px;
                }
                .info-text { font-size: 14px; color: #666; line-height: 1.5; }
                .email-highlight { color: #A68763; font-weight: 600; }
                .footer {
                    padding: 30px 40px;
                    background: #fafafa;
                    text-align: center;
                }
                .footer-text { font-size: 13px; color: #999; line-height: 1.6; }
                @media only screen and (max-width: 600px) {
                    .content { padding: 40px 25px; }
                    .otp-digit { width: 45px; height: 55px; font-size: 24px; }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="logo">Amarelle</div>
                </div>

                <div class="content">
                    <h1>Verify your login</h1>
                    <p class="body-text">Please verify your identity. Use this code to complete your sign in to Amarelle.</p>
                    <p class="timer-text">This code is valid for a short time.</p>

                    <div class="otp-container">
                        <div class="otp-digit">' . $otpDigits[0] . '</div>
                        <div class="otp-digit">' . $otpDigits[1] . '</div>
                        <div class="otp-digit">' . $otpDigits[2] . '</div>
                        <div class="otp-digit">' . $otpDigits[3] . '</div>
                        <div class="otp-digit">' . $otpDigits[4] . '</div>
                        <div class="otp-digit">' . $otpDigits[5] . '</div>
                    </div>

                    <div class="info-section">
                        <p class="info-text">
                            This code will securely verify your login for 
                            <span class="email-highlight">' . htmlspecialchars($email) . '</span>
                        </p>
                    </div>
                </div>

                <div class="footer">
                    <p class="footer-text">
                        If you didn\'t try to sign in, you can safely ignore this email.
                    </p>
                    <p class="footer-text" style="margin-top: 15px;">
                        © 2025 Amarelle. All rights reserved.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ';
    }
    
}

