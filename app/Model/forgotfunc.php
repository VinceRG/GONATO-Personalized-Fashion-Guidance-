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

    // ✅ EMAIL SENDER
    private function sendOTPEmail($email, $otp) {
        require_once __DIR__ . '/../../vendor/autoload.php';

        $mail = new PHPMailer(true);

        try {
            // SMTP Settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'aceview18@gmail.com';       
            $mail->Password = 'uelmqlulrxbbkikx';         
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Sender & Receiver
            $mail->setFrom('aceview18@gmail.com', 'Amarelle Support');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "
                <h2>Your OTP Code</h2>
                <h1>$otp</h1>
                <p>Enter this code to reset your password.</p>
                <p>This OTP expires in 5 minutes.</p>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }
    }
}
?>
