<?php
// app/Model/forgotfunc.php
require_once __DIR__ . '/../Core/Database.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ForgotModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function sendResetOTP($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE EMAIL = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $otp = rand(100000, 999999);
            $this->sendOTPEmail($email, $otp);
            return ['success' => true, 'otp' => (string)$otp];
        } else {
            return ['success' => false, 'message' => 'Email not found.'];
        }
    }

    public function updatePassword($email, $newPassword) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET PASSWORD = ? WHERE EMAIL = ?");
        $stmt->bind_param("ss", $hashed, $email);
        return $stmt->execute();
    }

    private function sendOTPEmail($email, $otp) {
        require_once __DIR__ . '/../vendor/autoload.php';
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com'; // change
            $mail->Password = 'your_app_password'; // Gmail App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Amarelle Support');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "
                <h3>Password Reset Request</h3>
                <p>Your OTP code is: <strong>$otp</strong></p>
                <p>This code expires in 5 minutes.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }
    }
}
