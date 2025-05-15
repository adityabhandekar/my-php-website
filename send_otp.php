<?php
session_start();
include('dbConnection.php');
// Correct the file paths according to where PHPMailer is located
require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = trim($_POST['identifier']); // This will be email or username
    
    // Find user by email or username
    $stmt = $conn->prepare("SELECT id, email FROM userinfo_tb WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $otp = rand(100000, 999999);  // 6-digit OTP
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        
        // Store OTP and expiry time
        $update = $conn->prepare("UPDATE userinfo_tb SET reset_token = ?, token_expiry = ? WHERE id = ?");
        $update->bind_param("ssi", $otp, $otp_expiry, $user['id']);
        $update->execute();

        // Send OTP via email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'eaglebeatsadiii@gmail.com';  // Your Gmail address
            $mail->Password = 'suavzltdxktlfkbd';  // Your generated App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Cleanify');
            $mail->addAddress($user['email']);

            $mail->isHTML(true);
            $mail->Subject = 'Your OTP for Password Reset';
            $mail->Body    = "Your OTP for resetting your password is <strong>$otp</strong>. It will expire in 10 minutes.";

            $mail->send();
            $_SESSION['reset_user_id'] = $user['id'];  // Store user ID for future use
            header("Location: verify_otp.php");
        } catch (Exception $e) {
            echo "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "<script>alert('User not found.'); window.location.href='forgot_password.php';</script>";
    }
}
?>
