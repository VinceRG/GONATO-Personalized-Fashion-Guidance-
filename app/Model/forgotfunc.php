<?php
require_once __DIR__ . '/../Core/Database.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ForgotModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    // ✅ STEP 1: Check Email + Send OTP
    public function sendResetOTP($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE EMAIL = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Email does not exist
        if ($result->num_rows == 0) {
            return [
                'success' => false,
                'message' => 'Email not found.'
            ];
        }

        // ✅ Create OTP
        $otp = rand(100000, 999999);

        // ✅ Save OTP to session (or DB)
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp']   = $otp;
        $_SESSION['otp_sent']    = true;
        $_SESSION['otp_time']    = time(); // Track when OTP was sent

        // ✅ Send OTP Email
        $this->sendOTPEmail($email, $otp);

        return [
            'success' => true,
            'message' => 'OTP sent',
            'otp' => $otp
        ];
    }

    // ✅ STEP 2: Update password after OTP verification
    public function updatePassword($email, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $this->conn->prepare("UPDATE users SET PASSWORD = ? WHERE EMAIL = ?");
        $stmt->bind_param("ss", $hashed, $email);

        return $stmt->execute();
    }

    // ✅ EMAIL SENDER with Beautiful Template
    private function sendOTPEmail($email, $otp) {
        require_once __DIR__ . '/../../vendor/autoload.php';

        $mail = new PHPMailer(true);

        try {
            // SMTP Settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'amarelle2025@gmail.com';       
            $mail->Password = 'hdzk sgjm jnbx kipl';         
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Sender & Receiver
            $mail->setFrom('amarelle2025@gmail.com', 'Amarelle');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password - Amarelle';
            
            // Convert OTP digits to individual characters for display
            $otpDigits = str_split($otp);
            
            $mail->Body = $this->getEmailTemplate($email, $otpDigits);

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }
    }

    // ✅ Beautiful Email Template
    private function getEmailTemplate($email, $otpDigits) {
        return '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
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
                .content {
                    padding: 50px 40px;
                    text-align: center;
                }
                h1 {
                    font-size: 28px;
                    font-weight: 600;
                    color: #1a1a1a;
                    margin-bottom: 20px;
                }
                .body-text {
                    font-size: 16px;
                    color: #666;
                    line-height: 1.6;
                    margin-bottom: 10px;
                }
                .timer-text {
                    font-size: 14px;
                    color: #999;
                    margin-bottom: 40px;
                }
                     .otp-container {
                    text-align: center;
                    margin: 40px 0;
                }
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
                .info-text {
                    font-size: 14px;
                    color: #666;
                    line-height: 1.5;
                }
                .email-highlight {
                    color: #A68763;
                    font-weight: 600;
                }
                .footer {
                    padding: 30px 40px;
                    background: #fafafa;
                    text-align: center;
                }
                .footer-text {
                    font-size: 13px;
                    color: #999;
                    line-height: 1.6;
                }
                @media only screen and (max-width: 600px) {
                    .content {
                        padding: 40px 25px;
                    }
                    .otp-digit {
                        width: 45px;
                        height: 55px;
                        font-size: 24px;
                    }
                    .otp-container {
                        gap: 8px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                
                    <div class="logo">Amarelle</div>
                </div>
                
                <div class="content">
                    <h1>Let\'s reset your password</h1>
                    
                    <p class="body-text">Use this code to reset your Amarelle password.</p>
                    <p class="timer-text">This code will expire in 2 minutes</p>
                    
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
                            This code will securely reset your password for 
                            <span class="email-highlight">' . htmlspecialchars($email) . '</span>
                        </p>
                    </div>
                </div>
                
                <div class="footer">
                    <p class="footer-text">
                        If you didn\'t request this email, you can safely ignore it.
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
?>
